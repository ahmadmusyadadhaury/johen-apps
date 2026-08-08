<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\AsetRukoSyncService;
use App\Services\PeralatanKantorSyncService;
use App\Services\SimCardSyncService;
use App\Services\VehicleSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssetViewController extends Controller
{
    public function index(Request $request, ?string $category = null)
    {
        if ($category) {
            $lower = strtolower($category);

            if ($lower === 'asset-ruko') {
                $category = 'aset-ruko';
                $lower = 'aset-ruko';
            }

            if ($lower === 'kendaraan') {
                $this->syncKendaraan();
            } elseif ($lower === 'sim-card') {
                $this->syncSimCards();
            } elseif ($lower === 'peralatan-kantor') {
                $this->syncPeralatanKantor();
            } elseif ($lower === 'aset-ruko') {
                $this->syncAsetRuko();
            }
        }

        $categories = AssetCategory::active()->get();

        $query = Asset::with(['category', 'creator']);

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->whereRaw('LOWER(REPLACE(name, " ", "-")) LIKE ?', ['%'.strtolower($category).'%']);
            });
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $statsQuery = Asset::query();

        if ($category) {
            $statsQuery->whereHas('category', function ($q) use ($category) {
                $q->whereRaw('LOWER(REPLACE(name, " ", "-")) LIKE ?', ['%'.strtolower($category).'%']);
            });
        }

        $isSimCard = $category && strtolower(str_replace('-', ' ', $category)) === 'sim card';

        if ($isSimCard) {
            $total = (clone $statsQuery)->count();
            $aktif = (clone $statsQuery)->where('status', 'tersedia')->where(function ($q) {
                $q->whereNull('purchase_date')->orWhereDate('purchase_date', '>=', now()->toDateString());
            })->count();
            $segeraHabis = (clone $statsQuery)->where('status', 'tersedia')
                ->whereBetween('purchase_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
                ->count();
            $mati = $total - $aktif - $segeraHabis;

            $stats = [
                'total' => $total,
                'aktif' => $aktif,
                'segera_habis' => $segeraHabis,
                'mati' => max($mati, 0),
            ];
        } else {
            $stats = [
                'total' => (clone $statsQuery)->count(),
                'baik' => (clone $statsQuery)->where('condition', 'baik')->count(),
                'perlu_diservis' => (clone $statsQuery)->where('condition', 'rusak_ringan')->count(),
                'rusak' => (clone $statsQuery)->where('condition', 'rusak_berat')->count(),
            ];
        }

        $assets = $query->latest()->paginate(20);
        $selectedCategory = $category;

        $isKendaraan = $category && strtolower(str_replace('-', ' ', $category)) === 'kendaraan';

        return view('assets.index', compact('assets', 'categories', 'selectedCategory', 'stats', 'isSimCard', 'isKendaraan'));
    }

public function detail(Asset $asset)
    {
        $asset->load(['category', 'creator']);

        $response = [
            'id' => $asset->id,
            'code' => $asset->code,
            'name' => $asset->name,
            'category' => $asset->category?->name,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'purchase_price' => $asset->purchase_price,
            'purchase_date' => $asset->purchase_date?->format('d/m/Y'),
            'supplier' => $asset->supplier,
            'location' => $asset->location,
            'condition' => $asset->condition,
            'status' => $asset->status,
            'description' => $asset->description,
            'creator' => $asset->creator?->name,
        ];

        if ($asset->category?->name === 'SIM Card') {
            $response['fields'] = $this->structuredSimFields($asset->description);
            $response['description'] = null;
        } elseif ($asset->category?->name === 'Peralatan Kantor') {
            $response['fields'] = $this->peralatanKantorFields($asset->metadata);
            $response['description'] = null;
        } elseif ($asset->category?->name === 'Kendaraan') {
            $response['fields'] = $this->vehicleFields($asset->metadata);
            $response['photo'] = $asset->photo;
            $response['description'] = $asset->description;
        }

        return response()->json($response);
    }

    private function vehicleFields(?array $metadata): array
    {
        if (! $metadata) {
            return [];
        }

        $photo = $metadata['foto'] ?? null;

        $map = [
            'Nomor Polisi' => 'plat_nomor',
            'Jenis' => 'jenis_kendaraan',
            'Merk/Tipe' => 'merk_tipe',
            'Tahun' => 'tahun',
            'Warna' => 'warna',
            'Nomor Rangka' => 'nomor_rangka',
            'Nomor Mesin' => 'nomor_mesin',
            'Status Pajak' => 'status_pajak',
            'Pajak Tahunan' => 'pajak_tahunan',
            'Pajak 5 Tahun' => 'pajak_5_tahun',
            'Kepemilikan' => 'kepemilikan_status',
            'Biaya Kendaraan' => 'biaya_kendaraan',
            'Keterangan' => 'keperluan',
            'PIC' => 'pic',
            'Jabatan' => 'jabatan',
        ];

        $fields = [];

        foreach ($map as $label => $key) {
            $value = $metadata[$key] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            if ($key === 'biaya_kendaraan') {
                $value = 'Rp ' . number_format((float) $value, 0, ',', '.');
            }

            $fields[] = ['label' => $label, 'value' => (string) $value];
        }

        return $fields;
    }

    private function peralatanKantorFields(?array $metadata): array
    {
        if (! $metadata) {
            return [];
        }

        $map = [
            'Jumlah' => 'jumlah',
            'Detail' => 'detail',
            'Keterangan' => 'keterangan',
            'Lokasi Unit' => 'lokasi_unit',
            'Ruangan' => 'ruangan',
            'Pengadaan (Tahun)' => 'pengadaan_tahun',
            'Kategori Nilai' => 'kategori_nilai',
            'Kategori Ukuran' => 'kategori_ukuran',
            'Sub Kategori' => 'sub_kategori',
            'Milik' => 'milik',
            'Barcode' => 'barcode',
            'Waktu Pakai/Hari' => 'waktu_pakai_per_hari',
            'Estimasi Waktu (hari)' => 'estimasi_waktu_barang',
            'Penggunaan/hari' => 'pengurangan_harga_per_hari',
            'Harga Saat Ini' => 'nilai_sekarang',
            'PIC' => 'pic',
            'Jabatan PIC' => 'jabatan',
            'Atasan' => 'atasan',
            'Jabatan Atasan' => 'jabatan_atasan',
        ];

        $rupiahFields = ['pengurangan_harga_per_hari', 'nilai_sekarang'];

        $fields = [];

        foreach ($map as $label => $key) {
            $value = $metadata[$key] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($key, $rupiahFields, true)) {
                $value = 'Rp ' . number_format((float) $value, 0, ',', '.');
            }

            $fields[] = ['label' => $label, 'value' => (string) $value];
        }

        return $fields;
    }

    private function structuredSimFields(?string $description): array
    {
        if (! $description) {
            return [];
        }

        $fields = [];

        foreach (explode(' | ', $description) as $part) {
            $pair = explode(': ', $part, 2);

            if (count($pair) === 2) {
                $fields[] = ['label' => $pair[0], 'value' => $pair[1]];
            }
        }

        return $fields;
    }

    private function syncKendaraan(): void
    {
        try {
            app(VehicleSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi kendaraan gagal', ['message' => $e->getMessage()]);
        }
    }

    private function syncSimCards(): void
    {
        try {
            app(SimCardSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi SIM card gagal', ['message' => $e->getMessage()]);
        }
    }

    private function syncPeralatanKantor(): void
    {
        try {
            app(PeralatanKantorSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi peralatan kantor gagal', ['message' => $e->getMessage()]);
        }
    }

    private function syncAsetRuko(): void
    {
        try {
            app(AsetRukoSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi aset ruko gagal', ['message' => $e->getMessage()]);
        }
    }
}
