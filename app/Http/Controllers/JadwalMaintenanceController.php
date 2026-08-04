<?php

namespace App\Http\Controllers;

use App\Models\ItMaintenancePc;
use App\Models\ItMaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JadwalMaintenanceController extends Controller
{
    public function index()
    {
        $pcs = ItMaintenancePc::with(['schedules' => function ($q) {
            $q->orderByDesc('urutan')->limit(1);
        }])->where('aktif', true)->get();

        $canManage = auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt();
        $canGiveFeedback = auth()->user()->isHeadOfStore2();

        return view('it.maintenance', compact('pcs', 'canManage', 'canGiveFeedback'));
    }

    public function storeMaintenance(Request $request)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        $pc = ItMaintenancePc::firstOrCreate(['nama' => $request->nama]);

        ItMaintenanceSchedule::create([
            'pc_id' => $pc->id,
            'urutan' => $request->urutan,
            'catatan' => $request->catatan,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Maintenance berhasil ditambahkan.');
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
        $schedule->update($data);
        return back()->with('success', 'Jadwal maintenance ditandai selesai.');
    }

    public function update(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt(), 403);

        $data = $request->validate([
            'urutan' => ['required', 'integer', 'min:1'],
            'tanggal' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:antrean,diproses,selesai'],
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

    public function feedback(Request $request, ItMaintenanceSchedule $schedule)
    {
        abort_unless($request->user()->isHeadOfStore2(), 403);

        $request->validate([
            'feedback_atasan' => 'required|string|max:3000',
        ]);

        $schedule->update(['feedback_atasan' => $request->input('feedback_atasan')]);

        return back()->with('success', 'Feedback untuk jadwal maintenance berhasil disimpan.');
    }
}
