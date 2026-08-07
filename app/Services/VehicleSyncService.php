<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleSyncService
{
    private const CACHE_KEY = 'vehicle_api_payload';

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
        $url = config('services.vehicle_api.url');

        if (! $url) {
            return collect();
        }

        try {
            $request = Http::timeout(5)
                ->withToken(config('services.vehicle_api.token'))
                ->acceptJson();

            if (! config('services.vehicle_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.vehicle_api.path'));

            if (! $response->successful()) {
                Log::warning('VehicleSyncService: API gagal', [
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
            Log::warning('VehicleSyncService: exception', ['message' => $e->getMessage()]);
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

        return collect($items)
            ->filter(fn ($item) => $this->isKendaraan((array) $item))
            ->values();
    }

    private function isKendaraan(array $item): bool
    {
        $subKategori = $item['sub_kategori'] ?? $item['kategori'] ?? null;

        if (! is_string($subKategori)) {
            return false;
        }

        return strtolower(trim($subKategori)) === 'kendaraan';
    }

    private function normalize(array $item): array
    {
        $condition = $this->mapCondition($item['kondisi'] ?? $item['condition'] ?? null);

        return [
            'code' => (string) ($item['kode_aset'] ?? $item['code'] ?? ''),
            'name' => (string) ($item['nama_barang'] ?? $item['name'] ?? ''),
            'brand' => null,
            'model' => null,
            'serial_number' => $this->nullable($item['barcode'] ?? null),
            'purchase_price' => $this->nullable($item['nilai'] ?? $item['harga'] ?? null),
            'purchase_date' => $this->nullableDate($item['tanggal_pembelian'] ?? $item['purchase_date'] ?? null),
            'supplier' => null,
            'location' => $this->nullable($item['lokasi_unit'] ?? $item['ruangan'] ?? $item['location'] ?? null),
            'condition' => $condition,
            'status' => 'tersedia',
            'description' => $this->nullable($item['keterangan'] ?? $item['detail'] ?? $item['description'] ?? null),
            'photo' => $this->nullable($item['foto'] ?? $item['photo'] ?? null),
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

    private function nullableDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function ensureCategory(): ?AssetCategory
    {
        return AssetCategory::firstOrCreate(
            ['name' => 'Kendaraan'],
            ['description' => 'Data kendaraan tersinkron dari office.johengaming.store', 'is_active' => true]
        );
    }
}
