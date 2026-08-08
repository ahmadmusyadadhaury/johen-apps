<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsetTimSyncService
{
    private const CACHE_KEY = 'aset_tim_api_payload';

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
        $url = config('services.aset_tim_api.url');

        if (! $url) {
            return collect();
        }

        $this->fromApi = false;

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.aset_tim_api.token'))
                ->acceptJson();

            if (! config('services.aset_tim_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.aset_tim_api.path'));

            if (! $response->successful()) {
                Log::warning('AsetTimSyncService: API gagal', [
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
            Log::warning('AsetTimSyncService: exception', ['message' => $e->getMessage()]);
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
        $code = $id !== null ? 'TIM-'.$id : (string) ($item['code'] ?? '');

        return [
            'code' => (string) $code,
            'name' => (string) ($item['nama_aset'] ?? $item['name'] ?? ''),
            'brand' => null,
            'model' => null,
            'serial_number' => null,
            'purchase_price' => null,
            'purchase_date' => null,
            'supplier' => null,
            'location' => $this->nullable($item['tim'] ?? null),
            'condition' => 'baik',
            'status' => $this->isAktif($item['is_active'] ?? null) ? 'tersedia' : 'dihapuskan',
            'description' => $this->nullable($item['keterangan'] ?? null),
            'photo' => null,
            'metadata' => $this->extractMetadata($item),
        ];
    }

    private function extractMetadata(array $item): array
    {
        return [
            'nama_aset' => $this->nullable($item['nama_aset'] ?? $item['name'] ?? null),
            'tim' => $this->nullable($item['tim'] ?? null),
            'jumlah' => $this->nullable($item['jumlah'] ?? null),
            'penanggung_jawab' => $this->nullable($item['penanggung_jawab_nama'] ?? $item['penanggung_jawab'] ?? null),
            'pic' => $this->nullable($item['pic'] ?? null),
            'jabatan' => $this->nullable($item['jabatan'] ?? null),
            'status' => $this->isAktif($item['is_active'] ?? null) ? 'aktif' : 'nonaktif',
            'keterangan' => $this->nullable($item['keterangan'] ?? null),
            'is_active' => $this->isAktif($item['is_active'] ?? null),
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
            ['name' => 'Aset Tim'],
            ['description' => 'Data aset tim tersinkron dari office.johengaming.store', 'is_active' => true]
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