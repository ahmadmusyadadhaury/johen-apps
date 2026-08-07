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

        $assets = $query->latest()->paginate(20);
        $selectedCategory = $category;

        return view('assets.index', compact('assets', 'categories', 'selectedCategory'));
    }

    public function detail(Asset $asset)
    {
        $asset->load(['category', 'creator']);

        return response()->json([
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
            'photo' => $asset->photo,
            'creator' => $asset->creator?->name,
        ]);
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
