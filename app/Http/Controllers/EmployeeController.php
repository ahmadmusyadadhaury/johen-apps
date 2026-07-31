<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\EmployeeDocument;
use App\Models\Position;
use App\Models\PositionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Employee::count(),
            'aktif' => Employee::where('status', 'aktif')->count(),
            'divisi' => Division::count(),
        ];

        return view('employees.index', compact('stats'));
    }

    public function creative()
    {
        $division = Division::firstOrCreate(['nama' => 'Creative']);
        return redirect()->route('hris.employees.index', ['division' => $division->id]);
    }

    public function create()
    {
        return redirect()->route('hris.employees.index');
    }

    public function store(StoreEmployeeRequest $request)
    {
        Gate::authorize('create-data');
        Employee::create($request->validated());

        return redirect()->route('hris.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['division', 'documents', 'contracts', 'positionHistories', 'payrollDetails.payrollImport', 'promotions', 'positions']);
        $employee->setRelation('contracts', $employee->contracts->sortByDesc('tanggal_mulai')->values());

        $payrollDetails = $employee->payrollDetails()
            ->with('payrollImport')
            ->get()
            ->sortByDesc(fn($d) => $d->payrollImport?->periode ?? '')
            ->values()
            ->map(fn($d) => [
                'id' => $d->id,
                'periode' => $d->payrollImport?->periode ?? '-',
                'gaji_pokok' => (float) $d->gaji_pokok,
                'tambahan_upah' => (float) $d->tambahan_upah,
                'bonus' => (float) $d->bonus,
                'thr' => (float) $d->thr,
                'apresiasi' => (float) $d->apresiasi,
                'tunjangan_jabatan' => (float) $d->tunjangan_jabatan,
                'thr_dibayarkan' => (float) $d->thr_dibayarkan,
                'potongan_pinjaman' => (float) $d->potongan_pinjaman,
                'potongan_absensi' => (float) $d->potongan_absensi,
                'take_home_pay' => (float) $d->take_home_pay,
                'status' => $d->status,
            ]);

        $stats = [
            'gaji_pokok' => $payrollDetails->sum('gaji_pokok'),
            'total_tunjangan' => $payrollDetails->sum(fn($d) => $d['tambahan_upah'] + $d['bonus'] + $d['thr'] + $d['apresiasi'] + $d['tunjangan_jabatan']),
            'total_potongan' => $payrollDetails->sum(fn($d) => $d['thr_dibayarkan'] + $d['potongan_pinjaman'] + $d['potongan_absensi']),
            'gaji_bersih' => $payrollDetails->sum('take_home_pay'),
        ];

        $statusClasses = [
            'aktif' => 'bg-emerald-50 text-emerald-700',
            'nonaktif' => 'bg-amber-50 text-amber-700',
            'resign' => 'bg-red-50 text-red-700',
        ];

        $divisions = Division::orderBy('nama')->get();
        $jenisDokumenList = ['KTP', 'KK', 'NPWP', 'Ijazah', 'Sertifikat', 'Kontrak', 'SK', 'Lainnya'];
        $allPositions = Position::where('is_active', true)->orderBy('nama')->get();

        return view('employees.show', compact('employee', 'divisions', 'jenisDokumenList', 'payrollDetails', 'stats', 'statusClasses', 'allPositions'));
    }

    public function edit(Employee $employee)
    {
        return redirect()->route('hris.employees.index');
    }

    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('update-data');
        }
        $employee->update($request->validated());

        // Sync positions from pivot
        if ($request->has('position_ids')) {
            $positionIds = $request->input('position_ids', []);
            $mainPositionId = $request->input('main_position_id');
            $syncData = [];
            foreach ($positionIds as $pid) {
                $syncData[$pid] = ['is_main' => $pid == $mainPositionId];
            }
            $employee->positions()->sync($syncData);
        }

        if ($request->input('_redirect') === 'show') {
            return redirect()->route('hris.employees.show', $employee)
                ->with('success', 'Data karyawan berhasil diperbarui.');
        }

        return redirect()->route('hris.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request, Employee $employee)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('update-data');
        }
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $file = $request->file('foto');
        $contents = base64_encode(file_get_contents($file->getRealPath()));

        $employee->update(['foto' => 'base64:' . $contents]);

        return redirect()->route('hris.employees.show', $employee)
            ->with('success', 'Foto berhasil diperbarui.');
    }

    public function showPhoto(Employee $employee)
    {
        if (!$employee->foto || !str_starts_with($employee->foto, 'base64:')) {
            abort(404);
        }

        $imageData = base64_decode(str_replace('base64:', '', $employee->foto));
        $f = finfo_open();
        $mimeType = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
        finfo_close($f);

        return response($imageData, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    public function storeDocument(Request $request, Employee $employee)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('create-data');
        }
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'jenis_dokumen' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $filename = $employee->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('documents', $filename, 'public');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'nama_dokumen' => $request->nama_dokumen,
            'jenis_dokumen' => $request->jenis_dokumen,
            'file' => $filename,
            'keterangan' => $request->keterangan,
        ]);

        return redirect(route('hris.employees.show', $employee) . '#dokumen')
            ->with('doc_success', 'Dokumen berhasil ditambahkan.');
    }

    public function downloadDocument(Employee $employee, EmployeeDocument $document)
    {
        $filePath = 'documents/' . $document->file;

        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->route('hris.employees.show', $employee)
                ->with('error', 'File dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download($filePath, $document->nama_dokumen . '.' . pathinfo($document->file, PATHINFO_EXTENSION));
    }

    public function destroyDocument(Employee $employee, EmployeeDocument $document)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('delete-data');
        }
        $filePath = 'documents/' . $document->file;

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $document->delete();

        return redirect(route('hris.employees.show', $employee) . '#dokumen')
            ->with('doc_success', 'Dokumen berhasil dihapus.');
    }

    public function storeContract(Request $request, Employee $employee)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('create-data');
        }
        $request->validate([
            'jenis_kontrak' => 'required|string|max:100',
            'posisi' => 'required|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string|max:500',
        ]);

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'jenis_kontrak' => $request->jenis_kontrak,
            'posisi' => $request->posisi,
            'atasan' => $request->atasan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status' => 'berlaku',
            'keterangan' => $request->keterangan,
        ]);

        return redirect(route('hris.employees.show', $employee) . '#kontrak')
            ->with('contract_success', 'Kontrak berhasil ditambahkan.');
    }

    public function getContract(Employee $employee, EmployeeContract $contract)
    {
        return response()->json($contract);
    }

    public function destroyContract(Employee $employee, EmployeeContract $contract)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('delete-data');
        }
        $contract->delete();

        return redirect(route('hris.employees.show', $employee) . '#kontrak')
            ->with('contract_success', 'Kontrak berhasil dihapus.');
    }

    public function storePositionHistory(Request $request, Employee $employee)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('create-data');
        }
        $request->validate([
            'jabatan' => 'required|string|max:255',
            'divisi' => 'required|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'mulai' => 'required|date',
            'selesai' => 'nullable|date',
            'status' => 'required|in:Aktif,Selesai',
        ]);

        PositionHistory::create([
            'employee_id' => $employee->id,
            'jabatan' => $request->jabatan,
            'divisi' => $request->divisi,
            'atasan' => $request->atasan,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
            'status' => $request->status,
        ]);

        return redirect(route('hris.employees.show', $employee) . '#jabatan')
            ->with('position_success', 'Riwayat jabatan berhasil ditambahkan.');
    }

    public function destroyPositionHistory(Employee $employee, PositionHistory $positionHistory)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('delete-data');
        }
        $positionHistory->delete();

        return redirect(route('hris.employees.show', $employee) . '#jabatan')
            ->with('position_success', 'Riwayat jabatan berhasil dihapus.');
    }

    public function updateContract(Request $request, Employee $employee, EmployeeContract $contract)
    {
        if (auth()->user()->employee_id !== $employee->id) {
            Gate::authorize('update-data');
        }
        $request->validate([
            'jenis_kontrak' => 'required|string|max:100',
            'posisi' => 'required|string|max:255',
            'atasan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $contract->update(['status' => 'selesai']);

        EmployeeContract::create([
            'employee_id' => $employee->id,
            'jenis_kontrak' => $request->jenis_kontrak,
            'posisi' => $request->posisi,
            'atasan' => $request->atasan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'status' => 'berlaku',
            'keterangan' => $request->keterangan,
        ]);

        return redirect(route('hris.employees.show', $employee) . '#kontrak')
            ->with('contract_success', 'Kontrak berhasil diperbarui. Kontrak lama otomatis ditandai selesai.');
    }

    public function destroy(Employee $employee)
    {
        Gate::authorize('delete-data');
        $employee->delete();

        return redirect()->route('hris.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
