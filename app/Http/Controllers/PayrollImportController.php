<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPayrollRequest;
use App\Models\PayrollImport;
use App\Services\PayrollImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PayrollImportController extends Controller
{
    public function __construct(
        private PayrollImportService $importService,
    ) {}

    public function create()
    {
        return view('payroll.upload');
    }

    public function store(UploadPayrollRequest $request)
    {
        Gate::authorize('create-data');
        $file = $request->file('file');
        $periode = $request->input('bulan') . ' ' . $request->input('tahun');

        $path = $file->store('payroll-uploads', 'local');

        $import = $this->importService->import(
            Storage::disk('local')->path($path),
            $periode,
            $request->user()->id
        );

        $errors = $import->getAttribute('errors');

        if (!empty($errors)) {
            return redirect()
                ->route('payroll.preview', $import)
                ->with('warning', count($errors) . ' baris data memiliki error. Perbaiki sebelum generate.');
        }

        return redirect()
            ->route('payroll.preview', $import)
            ->with('success', 'Data payroll berhasil diupload.');
    }

    public function destroy(PayrollImport $import): RedirectResponse
    {
        Gate::authorize('delete-data');
        foreach ($import->payrollDetails as $detail) {
            if ($detail->pdf_path && Storage::disk('public')->exists($detail->pdf_path)) {
                Storage::disk('public')->delete($detail->pdf_path);
            }
        }

        $import->delete();

        return redirect()->back()->with('success', 'Data payroll berhasil dihapus.');
    }
}
