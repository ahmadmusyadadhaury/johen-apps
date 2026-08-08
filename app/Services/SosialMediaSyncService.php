<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SosialMediaSyncService
{
    private const CACHE_KEY = 'sosial_media_api_payload';

    private const CACHE_TTL_SECONDS = 300;

    private bool $fromApi = false;

    public function sync(): array
    {
        $items = $this->fetch();

        $category = $this->ensureCategory();

        if (! $category) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'deleted' => 0, 'source' => 'no_category'];
        }

        if ($this->fromApi) {
            $sourceCodes = [];

            foreach ($items as $item) {
                $code = $this->normalize((array) $item)['code'] ?? null;

                if (! empty($code)) {
                    $sourceCodes[] = (string) $code;
                }
            }

            $deleted = $this->deleteOrphans($category, $sourceCodes);
        } else {
            $deleted = 0;
        }

        if ($items->isEmpty()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'deleted' => $deleted, 'source' => $this->fromApi ? 'api' : 'cache'];
        }

        $createdBy = auth()->id()
            ?? User::where('role', User::ROLE_SUPER_ADMIN)->value('id')
            ?? User::orderBy('id')->value('id');

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $data = $this->normalize((array) $item);

            if (empty($data['code'])) {
                $skipped++;

                continue;
            }

            $asset = Asset::where('code', $data['code'])->first();

            if ($asset) {
                $asset->update($data);
                $updated++;
            } else {
                Asset::create(array_merge($data, [
                    'category_id' => $category->id,
                    'created_by' => $createdBy,
                ]));
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'deleted' => $deleted, 'source' => 'api'];
    }

    public function fetch(): Collection
    {
        $url = config('services.sosial_media_api.url');

        if (! $url) {
            return collect();
        }

        $this->fromApi = false;

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.sosial_media_api.token'))
                ->acceptJson();

            if (! config('services.sosial_media_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.sosial_media_api.path'));

            if (! $response->successful()) {
                Log::warning('SosialMediaSyncService: API gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return Cache::get(self::CACHE_KEY, collect());
            }

            $this->fromApi = true;

            $items = $this->extractItems($response->json());

            if ($items->isNotEmpty()) {
                Cache::put(self::CACHE_KEY, $items, self::CACHE_TTL_SECONDS);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('SosialMediaSyncService: exception', ['message' => $e->getMessage()]);
            report($e);

            return Cache::get(self::CACHE_KEY, collect());
        }
    }

    private function extractItems(?array $payload): Collection
    {
        if (! is_array($payload)) {
            return collect();
        }

        $items = $payload['data'] ?? null;

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalize(array $item): array
    {
        $id = $item['id'] ?? null;
        $username = $this->nullable($item['username'] ?? null);
        $code = $username ?: ($id !== null ? 'SOSMED-'.$id : (string) ($item['code'] ?? ''));

        return [
            'code' => (string) $code,
            'name' => (string) ($item['nama'] ?? $item['name'] ?? $item['username'] ?? ''),
            'brand' => null,
            'model' => null,
            'serial_number' => null,
            'purchase_price' => null,
            'purchase_date' => null,
            'supplier' => null,
            'location' => $this->nullable($item['divisi'] ?? null),
            'condition' => 'baik',
            'status' => $this->isAktif($item['status'] ?? null) ? 'tersedia' : 'dihapuskan',
            'description' => null,
            'photo' => null,
            'metadata' => $this->extractMetadata($item),
        ];
    }

    private function extractMetadata(array $item): array
    {
        $status = $this->nullable($item['status'] ?? null);
        $aktif = $this->isAktif($status);

        return [
            'username' => $this->nullable($item['username'] ?? null),
            'nama' => $this->nullable($item['nama'] ?? $item['name'] ?? null),
            'followers' => $this->nullable($item['followers'] ?? null),
            'platform' => $this->nullable($item['platform'] ?? null),
            'status' => $status,
            'status_akun' => $aktif ? 'aktif' : 'tidak_aktif',
            'divisi' => $this->nullable($item['divisi'] ?? null),
            'pic' => $this->nullable($item['pic'] ?? null),
        ];
    }

    private function isAktif(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['aktif', 'active', 'tersedia', '1', 'true', 'on', 'yes', 'success'], true);
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function ensureCategory(): ?AssetCategory
    {
        return AssetCategory::firstOrCreate(
            ['name' => 'Sosial Media'],
            ['description' => 'Akun sosial media tersinkron dari office.johengaming.store', 'is_active' => true]
        );
    }

    private function deleteOrphans(AssetCategory $category, array $sourceCodes): int
    {
        $candidates = Asset::where('category_id', $category->id)
            ->whereNotNull('metadata')
            ->get();

        $deleted = 0;

        foreach ($candidates as $asset) {
            if (! in_array((string) $asset->code, $sourceCodes, true)) {
                $asset->delete();
                $deleted++;
            }
        }

        return $deleted;
    }
}