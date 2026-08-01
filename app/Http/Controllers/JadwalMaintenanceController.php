<?php

namespace App\Http\Controllers;

use App\Models\ItMaintenancePc;
use App\Models\ItMaintenanceSchedule;
use Illuminate\Http\Request;

class JadwalMaintenanceController extends Controller
{
    public function index()
    {
        $pcs = ItMaintenancePc::with(['schedules' => function ($q) {
            $q->latest('jadwal')->limit(1);
        }])->where('aktif', true)->get();

        return view('it.maintenance', compact('pcs'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'pc_id' => 'required|exists:it_maintenance_pcs,id',
            'jenis' => 'required|string|max:255',
            'jadwal' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ]);

        ItMaintenanceSchedule::create([
            'pc_id' => $request->pc_id,
            'jenis' => $request->jenis,
            'jadwal' => $request->jadwal,
            'catatan' => $request->catatan,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Jadwal maintenance berhasil ditambahkan.');
    }

    public function complete(ItMaintenanceSchedule $schedule)
    {
        $schedule->update(['status' => 'selesai']);
        return back()->with('success', 'Jadwal maintenance ditandai selesai.');
    }

    public function destroy(ItMaintenanceSchedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal maintenance berhasil dihapus.');
    }

    public function storePc(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:it_maintenance_pcs,nama',
        ]);

        ItMaintenancePc::create(['nama' => $request->nama]);

        return back()->with('success', 'PC berhasil ditambahkan.');
    }

    public function destroyPc(ItMaintenancePc $pc)
    {
        $pc->delete();
        return back()->with('success', 'PC berhasil dihapus.');
    }
}
