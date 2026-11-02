<?php

namespace App\Http\Controllers;

use App\Models\ItMaintenancePc;
use App\Models\ItMaintenanceSchedule;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JadwalMaintenanceController extends Controller
{
    public function photo(string $path)
    {
        abort_unless(Str::startsWith($path, 'maintenance/'), 404);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }

    public function index(Request $request)
    {
        $periode = $request->input('periode', 1);
        $periodeList = ItMaintenanceSchedule::query()
            ->distinct()
            ->orderBy('periode')
            ->pluck('periode')
            ->map(fn ($periode) => (int) $periode)
            ->values();

        if (! $periodeList->contains(1)) {
            $periodeList->prepend(1);
        }

        $periodeList->push(($periodeList->max() ?? 1) + 1);
        $periodeList = $periodeList->unique()->sort()->values();

        $pcs = ItMaintenancePc::query()
            ->where('aktif', true)
            ->whereHas('schedules', fn ($q) => $q->where('periode', $periode))
            ->with(['asset.category', 'latestCompletedSchedule', 'schedules' => function ($q) use ($periode) {
                $q->where('periode', $periode)->orderByDesc('tanggal_mulai')->limit(1);
            }])
            ->get();

        $officeAssets = Asset::query()
            ->with(['category', 'itMaintenancePc'])
            ->whereHas('category', fn ($query) => $query->where('name', 'Peralatan Kantor'))
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'category_id', 'metadata']);

        $canManage = auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt();
        $canGiveFeedback = auth()->user()->isHeadOfStore2();
        $canGiveFeedbackKoordinator = auth()->user()->isKoordinatorIt();

        return view('it.maintenance', compact('pcs', 'officeAssets', 'canManage', 'canGiveFeedback', 'canGiveFeedbackKoordinator', 'periodeList', 'periode'));
    }

    public function storeMaintenance(Request $request)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'periode' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pc = ItMaintenancePc::firstOrCreate(['nama' => $request->nama]);

        ItMaintenanceSchedule::create([
            'pc_id' => $pc->id,
            'periode' => $data['periode'],
            'catatan' => $request->catatan,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('it.maintenance', ['periode' => $data['periode']])
            ->with('success', 'Maintenance berhasil ditambahkan.');
    }

    public function complete(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $data = $request->validate([
            'foto_sebelum' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'foto_sesudah' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto_sebelum')) {
            if ($schedule->foto_sebelum) {
                Storage::disk('public')->delete($schedule->foto_sebelum);
            }
            $data['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance', 'public');
        }

        if ($request->hasFile('foto_sesudah')) {
            if ($schedule->foto_sesudah) {
                Storage::disk('public')->delete($schedule->foto_sesudah);
            }
            $data['foto_sesudah'] = $request->file('foto_sesudah')->store('maintenance', 'public');
        }

        $data['status'] = 'selesai';
        $data['tanggal_selesai'] = $schedule->tanggal_selesai?->toDateString() ?? now()->toDateString();
        $schedule->update($data);
        return back()->with('success', 'Jadwal maintenance ditandai selesai.');
    }

    public function update(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $data = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:antrean,diproses,dijeda,selesai'],
            'foto_sebelum' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'foto_sesudah' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($data['status'] === 'selesai' && empty($data['tanggal_selesai']) && ! $schedule->tanggal_selesai) {
            $data['tanggal_selesai'] = now()->toDateString();
        }

        if ($request->hasFile('foto_sebelum')) {
            if ($schedule->foto_sebelum) {
                Storage::disk('public')->delete($schedule->foto_sebelum);
            }
            $data['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance', 'public');
        }

        if ($request->hasFile('foto_sesudah')) {
            if ($schedule->foto_sesudah) {
                Storage::disk('public')->delete($schedule->foto_sesudah);
            }
            $data['foto_sesudah'] = $request->file('foto_sesudah')->store('maintenance', 'public');
        }

        $schedule->update($data);
        return back()->with('success', 'Jadwal maintenance berhasil diperbarui.');
    }

    public function destroy(ItMaintenanceSchedule $schedule)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        if ($schedule->foto_sebelum) {
            Storage::disk('public')->delete($schedule->foto_sebelum);
        }
        if ($schedule->foto_sesudah) {
            Storage::disk('public')->delete($schedule->foto_sesudah);
        }
        $schedule->delete();
        return back()->with('success', 'Jadwal maintenance berhasil dihapus.');
    }

    public function destroyPc(ItMaintenancePc $pc)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        foreach ($pc->schedules as $schedule) {
            if ($schedule->foto_sebelum) {
                Storage::disk('public')->delete($schedule->foto_sebelum);
            }
            if ($schedule->foto_sesudah) {
                Storage::disk('public')->delete($schedule->foto_sesudah);
            }
        }
        $pc->delete();
        return back()->with('success', 'PC berhasil dihapus.');
    }

    public function linkAsset(Request $request, ItMaintenancePc $pc)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $data = $request->validate([
            'asset_code' => ['nullable', 'string', 'exists:assets,code'],
        ]);

        if (! empty($data['asset_code'])) {
            abort_unless(Asset::query()
                ->where('code', $data['asset_code'])
                ->whereHas('category', fn ($query) => $query->where('name', 'Peralatan Kantor'))
                ->exists(), 422, 'Pilih aset dari kategori Peralatan Kantor.');
            abort_unless(! ItMaintenancePc::query()
                ->where('asset_code', $data['asset_code'])
                ->where('id', '!=', $pc->id)
                ->exists(), 422, 'Aset tersebut sudah dihubungkan ke PC maintenance lain.');
        }

        $pc->update(['asset_code' => $data['asset_code'] ?? null]);

        return back()->with('success', $data['asset_code'] ?? null
            ? 'PC berhasil dihubungkan ke Data Aset Johen Office.'
            : 'Hubungan PC dengan Data Aset berhasil dihapus.');
    }

    public function feedback(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless($request->user()->isHeadOfStore2(), 403);

        $request->validate([
            'feedback_atasan' => 'required|string|max:3000',
        ]);

        $schedule->update(['feedback_atasan' => $request->input('feedback_atasan')]);

        return back()->with('success', 'Feedback untuk jadwal maintenance berhasil disimpan.');
    }

    public function feedbackKoordinator(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless($request->user()->isKoordinatorIt(), 403);

        $request->validate([
            'feedback_koordinator' => 'required|string|max:3000',
        ]);

        $schedule->update(['feedback_koordinator' => $request->input('feedback_koordinator')]);

        return back()->with('success', 'Feedback koordinator untuk jadwal maintenance berhasil disimpan.');
    }
}
