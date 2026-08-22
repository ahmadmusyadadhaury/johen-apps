<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\AsetMesSyncService;
use App\Services\AsetRukoSyncService;
use App\Services\AsetTimSyncService;
use App\Services\PeralatanKantorSyncService;
use App\Services\SimCardSyncService;
use App\Services\SosialMediaSyncService;
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
            } elseif ($lower === 'sosial-media') {
                $this->syncSosialMedia();
            } elseif ($lower === 'asset-mes' || $lower === 'aset-mes') {
                $this->syncAsetMes();
            } elseif ($lower === 'aset-tim') {
                $this->syncAsetTim();
            }
        }

        $categories = AssetCategory::active()->get();

        $isMyAssets = $request->boolean('mine');
        $userName = $isMyAssets ? auth()->user()->name : null;

        $query = Asset::with(['category', 'creator']);

        if ($isMyAssets) {
            $query->where('metadata->pic', 'like', '%'.$userName.'%');
        }

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

        if ($isMyAssets) {
            $statsQuery->where('metadata->pic', 'like', '%'.$userName.'%');
        }

        if ($category) {
            $statsQuery->whereHas('category', function ($q) use ($category) {
                $q->whereRaw('LOWER(REPLACE(name, " ", "-")) LIKE ?', ['%'.strtolower($category).'%']);
            });
        }

        $isSimCard = $category && strtolower(str_replace('-', ' ', $category)) === 'sim card';
        $isKendaraan = $category && strtolower(str_replace('-', ' ', $category)) === 'kendaraan';
        $isSosialMedia = $category && strtolower(str_replace('-', ' ', $category)) === 'sosial media';
        $isAssetMes = $category && in_array(strtolower(str_replace('-', ' ', $category)), ['asset mes', 'aset mes'], true);
        $isAsetTim = $category && strtolower(str_replace('-', ' ', $category)) === 'aset tim';

        $kendaraanRows = $isKendaraan ? (clone $statsQuery)->get() : collect();
        $sosialRows = $isSosialMedia ? (clone $statsQuery)->get() : collect();
        $assetMesRows = $isAssetMes ? (clone $statsQuery)->get() : collect();
        $asetTimRows = $isAsetTim ? (clone $statsQuery)->get() : collect();

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
        } elseif ($isKendaraan) {
            $stats = [
                'total' => count($kendaraanRows),
                'pajak_aktif' => $this->countByStatusPajak($kendaraanRows, ['aktif', 'aktif_10tahun', 'akt']),
                'segera_habis' => $this->countByStatusPajak($kendaraanRows, ['jatuh_tempo', 'segera habis', 'segera-berakhir', 'hampir']),
                'pajak_mati' => $this->countByStatusPajak($kendaraanRows, ['mati']),
            ];
        } elseif ($isSosialMedia) {
            $aktif = 0;

            foreach ($sosialRows as $row) {
                $meta = (array) ($row->metadata ?? []);
                $status = strtolower(trim((string) ($meta['status_akun'] ?? $meta['status'] ?? $row->status)));

                if (in_array($status, ['aktif', 'tersedia', 'active', 'online', 'sukses'], true)) {
                    $aktif++;
                }
            }

            $stats = [
                'total' => count($sosialRows),
                'aktif' => $aktif,
                'tidak_aktif' => count($sosialRows) - $aktif,
            ];
        } elseif ($isAssetMes) {
            $putra = 0;
            $putri = 0;

            foreach ($assetMesRows as $row) {
                $meta = (array) ($row->metadata ?? []);
                $gender = strtolower(trim((string) ($meta['kategori'] ?? $meta['gender'] ?? $meta['tipe'] ?? $meta['jenis'] ?? $row->name)));

                if (str_contains($gender, 'putri')) {
                    $putri++;
                } elseif (str_contains($gender, 'putra') || str_contains($gender, 'laki')) {
                    $putra++;
                }
            }

            $stats = [
                'total' => count($assetMesRows),
                'putra' => $putra,
                'putri' => $putri,
            ];
        } elseif ($isAsetTim) {
            $aktif = 0;

            foreach ($asetTimRows as $row) {
                $meta = (array) ($row->metadata ?? []);
                $status = strtolower(trim((string) ($meta['status'] ?? $row->status)));

                if (in_array($status, ['aktif', 'tersedia', 'active', '1', 'true'], true)) {
                    $aktif++;
                }
            }

            $stats = [
                'total' => count($asetTimRows),
                'aktif' => $aktif,
                'nonaktif' => count($asetTimRows) - $aktif,
            ];
        } else {
            $stats = [
                'total' => (clone $statsQuery)->count(),
                'baik' => (clone $statsQuery)->where('condition', 'baik')->count(),
                'perlu_diservis' => (clone $statsQuery)->where('condition', 'rusak_ringan')->count(),
                'rusak' => (clone $statsQuery)->where('condition', 'rusak_berat')->count(),
            ];
        }

        $assets = ($isAssetMes || $isAsetTim)
            ? $query->latest()->get()
            : $query->latest()->paginate(20)->withQueryString();
        $selectedCategory = $category;

        return view('assets.index', compact('assets', 'categories', 'selectedCategory', 'stats', 'isSimCard', 'isKendaraan', 'isSosialMedia', 'isAssetMes', 'isAsetTim', 'isMyAssets'));
    }

public function detail(Asset $asset)
    {
        return response()->json($this->detailPayload($asset));
    }

    public function publicShow(string $code)
    {
        $asset = Asset::query()
            ->with(['category', 'creator'])
            ->where('code', $code)
            ->first();

        if (! $asset) {
            abort(404);
        }

        return view('assets.public', [
            'asset' => $asset,
            'detail' => $this->detailPayload($asset),
        ]);
    }

    private function detailPayload(Asset $asset): array
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
            $response['description'] = null;
        } elseif ($asset->category?->name === 'Sosial Media') {
            $response['fields'] = $this->sosialMediaFields($asset->metadata);
            $response['condition'] = null;
            $response['status'] = null;
            $response['description'] = null;
        } elseif ($asset->category?->name === 'Asset Mes' || $asset->category?->name === 'Aset Mes') {
            $response['fields'] = $this->mesFields($asset->metadata);
            $response['condition'] = null;
            $response['description'] = null;
        } elseif ($asset->category?->name === 'Aset Tim') {
            $response['fields'] = $this->mesFields($asset->metadata);
            $response['condition'] = null;
            $response['description'] = null;
        }

        return $response;
    }

    private function sosialMediaFields(?array $metadata): array
    {
        if (! $metadata) {
            return [];
        }

        $fields = [];

        $map = [
            'Username' => 'username',
            'Nama' => 'nama',
            'Followers' => 'followers',
            'Platform' => 'platform',
            'Status' => 'status_akun',
            'Divisi' => 'divisi',
            'PIC' => 'pic',
        ];

        foreach ($map as $label => $key) {
            $value = $metadata[$key] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $fields[] = ['label' => $label, 'value' => (string) $value];
        }

        return $fields;
    }

    private function mesFields(?array $metadata): array
    {
        if (! $metadata) {
            return [];
        }

        $map = [
            'Kategori' => 'kategori',
            'Tim' => 'tim',
            'Jumlah' => 'jumlah',
            'Penanggung Jawab' => 'penanggung_jawab',
            'PIC' => 'pic',
            'Jabatan' => 'jabatan',
            'Status' => 'status',
            'Keterangan' => 'keterangan',
        ];

        $fields = [];

        foreach ($map as $label => $key) {
            $value = $metadata[$key] ?? null;

            if ($key === 'kategori' && ! isset($metadata['kategori'])) {
                continue;
            }

            if ($key === 'tim' && ! isset($metadata['tim'])) {
                continue;
            }

            if ($value === null || $value === '' || $value === '-') {
                continue;
            }

            if ($key === 'jumlah') {
                $value = (string) $value;
            }

            $fields[] = ['label' => $label, 'value' => (string) $value];
        }

        return $fields;
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

    private function countByStatusPajak($rows, array $values): int
    {
        $count = 0;

        foreach ($rows as $row) {
            $meta = (array) ($row->metadata ?? []);
            $status = strtolower(trim((string) ($meta['status_pajak'] ?? '')));

            if (in_array($status, $values, true)) {
                $count++;
            }
        }

        return $count;
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

    private function syncSosialMedia(): void
    {
        try {
            app(SosialMediaSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi sosial media gagal', ['message' => $e->getMessage()]);
        }
    }

    private function syncAsetMes(): void
    {
        try {
            app(AsetMesSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi aset mes gagal', ['message' => $e->getMessage()]);
        }
    }

    private function syncAsetTim(): void
    {
        try {
            app(AsetTimSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi aset tim gagal', ['message' => $e->getMessage()]);
        }
    }
}
