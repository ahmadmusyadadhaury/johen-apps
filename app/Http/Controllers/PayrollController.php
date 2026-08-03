<?php

namespace App\Http\Controllers;

use App\Models\PayrollDetail;
use App\Models\PayrollImport;
use App\Services\PdfGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PayrollController extends Controller
{
    private const BATCH_SIZE = 8;

    public function processBatch(PayrollImport $import, PdfGenerationService $pdfService): JsonResponse
    {
        set_time_limit(120);

        $details = $import->payrollDetails()
            ->where('status', 'pending')
            ->limit(self::BATCH_SIZE)
            ->get();

        foreach ($details as $detail) {
            try {
                $path = $pdfService->generate($detail, $import->periode, $detail->pdf_password);
                $detail->update([
                    'pdf_path' => $path,
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                $detail->update(['status' => 'failed']);
            }
        }

        $progress = $this->getProgress($import);

        if ($progress['allDone']) {
            session()->flash('success', 'Slip gaji berhasil digenerate dan tersedia di menu Riwayat Payroll masing-masing karyawan.');
        }

        return response()->json($progress);
    }

    public function retryFailed(PayrollDetail $detail, PdfGenerationService $pdfService): RedirectResponse
    {
        if ($detail->status !== 'failed') {
            return redirect()->back()->with('error', 'Slip ini tidak berstatus gagal.');
        }

        try {
            $path = $pdfService->generate($detail, $detail->payrollImport->periode, $detail->pdf_password);
            $detail->update([
                'pdf_path' => $path,
                'status' => 'sent',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Generate ulang gagal: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Slip gaji berhasil digenerate ulang.');
    }

    public function show(PayrollImport $import)
    {
        $import->load(['payrollDetails.emailLog', 'uploadedBy']);

        $progress = $this->getProgress($import);

        return view('payroll.show', compact('import', 'progress'));
    }

    public function progressJson(PayrollImport $import)
    {
        return response()->json($this->getProgress($import));
    }

    private function getProgress(PayrollImport $import): array
    {
        $total = $import->total_employee;
        $sent = $import->payrollDetails()->where('status', 'sent')->count();
        $failed = $import->payrollDetails()->where('status', 'failed')->count();
        $pending = $total - $sent - $failed;
        $percent = $total > 0 ? (int) round(($sent + $failed) / $total * 100) : 0;

        return [
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'percent' => $percent,
            'allDone' => $total > 0 && ($sent + $failed) >= $total,
        ];
    }

    public function downloadPdf(PayrollDetail $detail)
    {
        if (!$detail->pdf_path || !Storage::disk('public')->exists($detail->pdf_path)) {
            return redirect()->back()->with('error', 'File PDF tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $detail->pdf_path,
            sprintf('Slip_%s_%s.pdf', $detail->nik, $detail->payrollImport->periode)
        );
    }
}
