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
        $url = config('services.vehicle_api.url');

        if (! $url) {
            return collect();
        }

        $this->fromApi = false;

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

            $this->fromApi = true;

            $items = $this->extractItems($response->json());

            if ($items->isNotEmpty()) {
                Cache::put(self::CACHE_KEY, $items, self::CACHE_TTL_SECONDS);
            }

            return $items;
        } catch (\Throwable $e) {
            Log::warning('VehicleSyncService: exception', ['message' => $e->getMessage()]);
            report($e);

            return collect();
        }
    }

    private function extractItems(?array $payload): Collection
    {
        $items = $payload['data'] ?? null;

        if (! is_array($items)) {
            return collect();
        }

        return collect($items)->values();
    }

    private function normalize(array $item): array
    {
        $condition = $this->mapCondition($item['kondisi'] ?? $item['condition'] ?? null);
        $photo = $this->nullable($item['foto'] ?? $item['photo'] ?? null);
        $code = $this->nullable($item['plat_nomor'] ?? $item['code'] ?? $item['kode_aset'] ?? null);
        $id = $this->nullable($item['id'] ?? null);

        if (! $code && $id !== null) {
            $code = 'VEH-'.$id;
        }

        return [
            'code' => (string) $code,
            'name' => (string) ($item['nama_kendaraan'] ?? $item['nama_barang'] ?? $item['name'] ?? ''),
            'brand' => $this->nullable($item['merk_tipe'] ?? $item['brand'] ?? null),
            'model' => null,
            'serial_number' => $this->nullable($item['nomor_mesin'] ?? $item['nomor_rangka'] ?? null),
            'purchase_price' => $this->nullable($item['biaya_kendaraan'] ?? $item['nilai'] ?? $item['harga'] ?? null),
            'purchase_date' => $this->nullableDate($item['pajak_tahunan'] ?? $item['tanggal_pembelian'] ?? $item['purchase_date'] ?? null),
            'supplier' => null,
            'location' => $this->nullable($item['lokasi_unit'] ?? $item['ruangan'] ?? $item['location'] ?? null),
            'condition' => $condition,
            'status' => strtolower($this->nullable($item['status_pajak'] ?? $item['status'] ?? '') ?? '') === 'mati' ? 'dihapuskan' : 'tersedia',
            'description' => $this->buildDescription($item),
            'photo' => $photo,
            'metadata' => [
                'nama_kendaraan' => $this->nullable($item['nama_kendaraan'] ?? null),
                'jenis_kendaraan' => $this->nullable($item['jenis_kendaraan'] ?? null),
                'merk_tipe' => $this->nullable($item['merk_tipe'] ?? null),
                'plat_nomor' => $this->nullable($item['plat_nomor'] ?? null),
                'tahun' => $this->nullable($item['tahun'] ?? null),
                'warna' => $this->nullable($item['warna'] ?? null),
                'nomor_rangka' => $this->nullable($item['nomor_rangka'] ?? null),
                'nomor_mesin' => $this->nullable($item['nomor_mesin'] ?? null),
                'foto' => $photo,
                'pajak_tahunan' => $this->nullableDate($item['pajak_tahunan'] ?? null),
                'pajak_5_tahun' => $this->nullableDate($item['pajak_5_tahun'] ?? null),
                'kepemilikan_status' => $this->nullable($item['kepemilikan_status'] ?? null),
                'biaya_kendaraan' => $this->nullable($item['biaya_kendaraan'] ?? null),
                'pic' => $this->nullable($item['pic'] ?? null),
                'jabatan' => $this->nullable($item['jabatan'] ?? null),
                'keperluan' => $this->nullable($item['keperluan'] ?? null),
                'status_pajak' => $this->nullable($item['status_pajak'] ?? null),
            ],
        ];
    }

    private function buildDescription(array $item): ?string
    {
        $parts = [];

        if ($plat = $this->nullable($item['plat_nomor'] ?? null)) {
            $parts[] = 'Nomor Polisi: '.$plat;
        }
        if ($jenis = $this->nullable($item['jenis_kendaraan'] ?? null)) {
            $parts[] = 'Jenis: '.$jenis;
        }
        if ($merk = $this->nullable($item['merk_tipe'] ?? null)) {
            $parts[] = 'Merk/Tipe: '.$merk;
        }
        if ($tahun = $this->nullable($item['tahun'] ?? null)) {
            $parts[] = 'Tahun: '.$tahun;
        }
        if ($warna = $this->nullable($item['warna'] ?? null)) {
            $parts[] = 'Warna: '.$warna;
        }
        if ($rangka = $this->nullable($item['nomor_rangka'] ?? null)) {
            $parts[] = 'Nomor Rangka: '.$rangka;
        }
        if ($mesin = $this->nullable($item['nomor_mesin'] ?? null)) {
            $parts[] = 'Nomor Mesin: '.$mesin;
        }
        if ($status = $this->nullable($item['status_pajak'] ?? null)) {
            $parts[] = 'Status Pajak: '.$status;
        }
        if ($tahunan = $this->nullableDate($item['pajak_tahunan'] ?? null)) {
            $parts[] = 'Pajak Tahunan: '.$tahunan;
        }
        if ($tahun5 = $this->nullableDate($item['pajak_5_tahun'] ?? null)) {
            $parts[] = 'Pajak 5 Tahun: '.$tahun5;
        }
        if ($keperluan = $this->nullable($item['keperluan'] ?? null)) {
            $parts[] = 'Keterangan: '.$keperluan;
        }
        if ($pic = $this->nullable($item['pic'] ?? null)) {
            $parts[] = 'PIC: '.$pic;
        }
        if ($jabatan = $this->nullable($item['jabatan'] ?? null)) {
            $parts[] = 'Jabatan: '.$jabatan;
        }

        return $parts ? implode(' | ', $parts) : null;
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

    private function deleteOrphans(AssetCategory $category, array $sourceCodes): int
    {
        $candidates = Asset::where('category_id', $category->id)->get();

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
