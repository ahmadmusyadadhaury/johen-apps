<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ElectricitySetting;
use App\Models\ElectricityTokenCheck;
use App\Models\ElectricityTopup;
use App\Services\ElectricityApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElectricityApiController extends Controller
{
    public function __construct(private ElectricityApiService $api)
    {
    }

    public function stats()
    {
        $setting = ElectricitySetting::firstOrCreate(['id' => 1], ['kapasitas_kwh' => 3000]);
        $apiTopups = $this->api->topups()
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();
        $lastTopup = $apiTopups->first()
            ?? ElectricityTopup::with('creator')->latest()->first();
        $lastCheck = ElectricityTokenCheck::with('checker')->latest()->first();

        $apiChecks = $this->api->checks();
        $totalTerpakai = $apiChecks->isNotEmpty()
            ? $apiChecks->sum('terpakai')
            : ElectricityTokenCheck::sum('terpakai');

        $sisaApi = $this->api->sisaToken();
        $sisaToken = $sisaApi ?? $lastCheck?->sisa_kwh ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'setting' => $setting,
                'last_topup' => $lastTopup,
                'last_check' => $lastCheck,
                'total_terpakai' => $totalTerpakai,
                'sisa_token' => $sisaToken,
            ],
        ]);
    }

    public function topups(Request $request)
    {
        $topups = $this->api->topups();

        if ($topups->isEmpty()) {
            $topups = ElectricityTopup::with('creator')->latest()->get();
        }

        if ($filter = $request->filter) {
            $topups = $topups->filter(function ($t) use ($filter) {
                $tanggal = $t->tanggal_bayar;

                if (! $tanggal) {
                    return false;
                }

                return match ($filter) {
                    'harian' => $tanggal->isToday(),
                    'mingguan' => $tanggal->between(now()->startOfWeek(), now()->endOfWeek()),
                    default => true,
                };
            });
        }

        $topups = $topups
            ->sortByDesc(fn ($t) => $t->tanggal_bayar)
            ->values();

        $perPage = (int) ($request->per_page ?? 20);
        $total = $topups->count();
        $page = max(1, (int) $request->page);

        return response()->json([
            'success' => true,
            'data' => $topups->forPage($page, $perPage)->values()
                ->map(fn ($t) => [
                    'id' => $t->id ?? null,
                    'tanggal_bayar' => $t->tanggal_bayar ? $t->tanggal_bayar->toISOString() : null,
                    'periode' => $t->periode ?? '-',
                    'jumlah_kwh' => (float) ($t->jumlah_kwh ?? 0),
                    'nominal' => (float) ($t->nominal ?? 0),
                    'creator' => ['name' => $t->creator?->name],
                    'catatan' => $t->catatan ?? null,
                    'bukti' => $t->bukti,
                    'bukti_url' => $t->bukti_url,
                ])->all(),
            'meta' => [
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    public function storeTopup(Request $request)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'periode' => 'required|string|max:50',
            'jumlah_kwh' => 'required|numeric|min:0',
            'nominal' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
            'bukti' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $validated['created_by'] = $request->user()->id;

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('bukti-token', 'public');
        }

        $topup = ElectricityTopup::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Top up token berhasil dicatat',
            'data' => $topup->load('creator'),
        ], 201);
    }

    public function destroyTopup(ElectricityTopup $electricityTopup)
    {
        if ($electricityTopup->bukti && Storage::disk('public')->exists($electricityTopup->bukti)) {
            Storage::disk('public')->delete($electricityTopup->bukti);
        }

        $electricityTopup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat top up berhasil dihapus',
        ]);
    }

    public function checks(Request $request)
    {
        $query = ElectricityTokenCheck::with('checker');

        if ($filter = $request->filter) {
            $query = match ($filter) {
                'harian' => $query->whereDate('created_at', today()),
                'mingguan' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                default => $query,
            };
        }

        $checks = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $checks->items(),
            'meta' => [
                'current_page' => $checks->currentPage(),
                'last_page' => $checks->lastPage(),
                'total' => $checks->total(),
            ],
        ]);
    }

    public function storeCheck(Request $request)
    {
        $validated = $request->validate([
            'tanggal_check' => 'required|date',
            'sisa_kwh' => 'required|numeric|min:0',
            'terpakai' => 'required|numeric|min:0',
            'status' => 'nullable|in:normal,rendah,habis',
            'catatan' => 'nullable|string',
        ]);

        $validated['checked_by'] = $request->user()->id;

        if (!isset($validated['status'])) {
            $validated['status'] = match (true) {
                $validated['sisa_kwh'] <= 0 => 'habis',
                $validated['sisa_kwh'] <= 500 => 'rendah',
                default => 'normal',
            };
        }

        $check = ElectricityTokenCheck::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengecekan token berhasil dicatat',
            'data' => $check->load('checker'),
        ], 201);
    }

    public function destroyCheck(ElectricityTokenCheck $electricityTokenCheck)
    {
        $electricityTokenCheck->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengecekan berhasil dihapus',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'kapasitas_kwh' => 'required|numeric|min:0',
        ]);

        $setting = ElectricitySetting::firstOrCreate(
            ['id' => 1],
            ['kapasitas_kwh' => 3000]
        );

        $setting->update([
            'kapasitas_kwh' => $validated['kapasitas_kwh'],
            'updated_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan kapasitas token berhasil diupdate',
            'data' => $setting,
        ]);
    }
}
