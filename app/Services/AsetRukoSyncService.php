<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsetRukoSyncService
{
    private const CACHE_KEY = 'aset_ruko_api_payload';

    private const CACHE_TTL_SECONDS = 300;

    public function sync(): array
    {
        $items = $this->fetch();

        if ($items->isEmpty()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'source' => 'cache'];
        }

        $category = $this->ensureCategory();

        if (! $category) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0, 'source' => 'no_category'];
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

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'source' => 'api'];
    }

    public function fetch(): Collection
    {
        $url = config('services.aset_ruko_api.url');

        if (! $url) {
            return collect();
        }

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.aset_ruko_api.token'))
                ->acceptJson();

            if (! config('services.aset_ruko_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.aset_ruko_api.path'));

            if (! $response->successful()) {
                Log::warning('AsetRukoSyncService: API gagal', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return Cache::get(self::CACHE_KEY, collect());
            }

            $items = $this->extractItems($response->json());

            if ($items->isNotEmpty()) {
                Cache::put(self::CACHE_KEY, $items, self::CACHE_TTL_SECONDS);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('AsetRukoSyncService: exception', ['message' => $e->getMessage()]);
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
        $code = $id !== null ? 'RUKO-'.$id : (string) ($item['code'] ?? '');

        return [
            'code' => $code,
            'name' => (string) ($item['nama_aset'] ?? $item['name'] ?? ''),
            'brand' => null,
            'model' => null,
            'serial_number' => null,
            'purchase_price' => null,
            'purchase_date' => null,
            'supplier' => null,
            'location' => $this->nullable($item['lokasi'] ?? $item['location'] ?? null),
            'condition' => $this->mapCondition($item['kondisi'] ?? $item['condition'] ?? null),
            'status' => 'tersedia',
            'description' => $this->nullableDescription($item),
            'photo' => null,
        ];
    }

    private function mapCondition(mixed $value): string
    {
        $value = is_string($value) ? strtolower(trim($value)) : null;

        return match ($value) {
            'rusak_berat', 'rusak' => 'rusak_berat',
            'rusak_ringan', 'perlu_servis' => 'rusak_ringan',
            default => 'baik',
        };
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (string) $value;
    }

    private function nullableDescription(array $item): ?string
    {
        $parts = [];

        if (! empty($item['jumlah'])) {
            $parts[] = 'Jumlah: '.$item['jumlah'];
        }

        if (! empty($item['keterangan'])) {
            $parts[] = $item['keterangan'];
        }

        if (! empty($item['detail'])) {
            $parts[] = $item['detail'];
        }

        return $parts ? implode(' | ', $parts) : null;
    }

    private function ensureCategory(): ?AssetCategory
    {
        return AssetCategory::firstOrCreate(
            ['name' => 'Aset Ruko'],
            ['description' => 'Data aset ruko tersinkron dari office.johengaming.store', 'is_active' => true]
        );
    }
}
