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

class PeralatanKantorSyncService
{
    private const CACHE_KEY = 'peralatan_kantor_api_payload';

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
        $url = config('services.peralatan_kantor_api.url');

        if (! $url) {
            return collect();
        }

        $this->fromApi = false;

        try {
            $request = Http::timeout(15)
                ->withToken(config('services.peralatan_kantor_api.token'))
                ->acceptJson();

            if (! config('services.peralatan_kantor_api.verify_ssl')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url.config('services.peralatan_kantor_api.path'));

            if (! $response->successful()) {
                Log::warning('PeralatanKantorSyncService: API gagal', [
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
            Log::warning('PeralatanKantorSyncService: exception', ['message' => $e->getMessage()]);
            report($e);

            return collect();
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
            ->filter(fn ($item) => $this->isPeralatanKantor((array) $item))
            ->values();
    }

    private function isPeralatanKantor(array $item): bool
    {
        $subKategori = $item['sub_kategori'] ?? $item['kategori'] ?? null;

        if (! is_string($subKategori)) {
            return false;
        }

        return strtolower(trim($subKategori)) === 'peralatan kantor';
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
            'metadata' => $this->extractMetadata($item),
        ];
    }

    private function extractMetadata(array $item): array
    {
        return [
            'nama_barang' => $this->nullable($item['nama_barang'] ?? $item['name'] ?? null),
            'kode_aset' => $this->nullable($item['kode_aset'] ?? $item['code'] ?? null),
            'barcode' => $this->nullable($item['barcode'] ?? null),
            'jumlah' => $this->nullable($item['jumlah'] ?? null),
            'detail' => $this->nullable($item['detail'] ?? null),
            'keterangan' => $this->nullable($item['keterangan'] ?? null),
            'lokasi_unit' => $this->nullable($item['lokasi_unit'] ?? null),
            'ruangan' => $this->nullable($item['ruangan'] ?? null),
            'pengadaan_tahun' => $this->nullable($item['pengadaan_tahun'] ?? null),
            'tanggal_pembelian' => $this->nullableDate($item['tanggal_pembelian'] ?? null),
            'kategori_nilai' => $this->nullable($item['kategori_nilai'] ?? null),
            'kategori_ukuran' => $this->nullable($item['kategori_ukuran'] ?? null),
            'sub_kategori' => $this->nullable($item['sub_kategori'] ?? null),
            'milik' => $this->nullable($item['milik'] ?? null),
            'nilai' => $this->nullable($item['nilai'] ?? null),
            'waktu_pakai_per_hari' => $this->nullable($item['waktu_pakai_per_hari'] ?? null),
            'estimasi_waktu_barang' => $this->nullable($item['estimasi_waktu_barang'] ?? null),
            'pengurangan_harga_per_hari' => $this->nullable($item['pengurangan_harga_per_hari'] ?? null),
            'harga_per_hari_ini' => $this->nullable($item['harga_per_hari_ini'] ?? null),
            'nilai_sekarang' => $this->nullable($item['nilai_sekarang'] ?? null),
            'pic' => $this->nullable($item['pic'] ?? null),
            'jabatan' => $this->nullable($item['jabatan'] ?? null),
            'atasan' => $this->nullable($item['atasan'] ?? null),
            'jabatan_atasan' => $this->nullable($item['jabatan_atasan'] ?? null),
            'foto' => $this->nullable($item['foto'] ?? null),
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
            ['name' => 'Peralatan Kantor'],
            ['description' => 'Data peralatan kantor tersinkron dari office.johengaming.store', 'is_active' => true]
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
