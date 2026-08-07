<?php

namespace App\Http\Controllers;

use App\Models\DigitalAssetRegistry;
use App\Services\DigitalAssetSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigitalAssetRegistryController extends Controller
{
    public function index(Request $request)
    {
        $this->syncRegistries();

        $query = DigitalAssetRegistry::query();

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $registries = $query->latest()->paginate(20);
        $stats = [
            'total' => DigitalAssetRegistry::count(),
            'aktif' => DigitalAssetRegistry::where('is_active', true)->count(),
            'nonaktif' => DigitalAssetRegistry::where('is_active', false)->count(),
            'nominal' => DigitalAssetRegistry::sum('biaya'),
        ];

        return view('digital-asset-registries.index', compact('registries', 'stats'));
    }

    private function syncRegistries(): void
    {
        try {
            app(DigitalAssetSyncService::class)->sync();
        } catch (\Throwable $e) {
            Log::warning('Sinkronisasi registri aset digital gagal', ['message' => $e->getMessage()]);
        }
    }
}
