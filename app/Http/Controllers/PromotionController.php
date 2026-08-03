<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PromotionController extends Controller
{
    public function __construct(
        protected PromotionService $promotionService
    ) {}

    public function store(Request $request, Employee $employee)
    {
        Gate::authorize('create-data');
        $validated = $request->validate([
            'nomor_surat' => 'nullable|string|max:100',
            'posisi_baru' => 'required|string|max:255',
            'division_ids' => 'nullable|array',
            'division_ids.*' => 'exists:divisions,id',
            'atasan_baru' => 'nullable|string|max:255',
            'tanggal_efektif' => 'required|date',
            'jenis' => 'required|in:promosi,demosi,mutasi',
            'alasan' => 'nullable|string|max:500',
        ]);

        $promotion = $this->promotionService->apply($validated, $employee);

        return redirect(route('hris.employees.show', $employee) . '#jabatan')
            ->with('promotion_success', 'Promosi berhasil diterapkan. Surat promosi telah dibuat.');
    }

    public function downloadPdf(Employee $employee, Promotion $promotion)
    {
        if ($promotion->employee_id !== $employee->id) {
            abort(404);
        }

        if (!$promotion->pdf_path || !Storage::disk('public')->exists($promotion->pdf_path)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan.');
        }

        $filename = 'Surat_Promosi_' . $employee->nik . '.pdf';

        return Storage::disk('public')->download($promotion->pdf_path, $filename);
    }

    public function destroy(Employee $employee, Promotion $promotion)
    {
        Gate::authorize('delete-data');
        if ($promotion->employee_id !== $employee->id) {
            abort(404);
        }

        $this->promotionService->rollback($promotion);

        return redirect(route('hris.employees.show', $employee) . '#jabatan')
            ->with('promotion_success', 'Promosi berhasil dibatalkan. Data dikembalikan ke posisi semula.');
    }
}
