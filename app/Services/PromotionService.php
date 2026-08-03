<?php

namespace App\Services;

use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\PositionHistory;
use App\Models\Promotion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PromotionService
{
    public function apply(array $data, Employee $employee): Promotion
    {
        return DB::transaction(function () use ($data, $employee) {
            $posisiLama = $employee->position;
            $divisiLama = $employee->divisionNames() ?: null;
            $atasanLama = $employee->atasan;

            $newDivisionIds = array_map('intval', $data['division_ids'] ?? []);
            if (empty($newDivisionIds)) {
                $newDivisionIds = $employee->divisions->pluck('id')->toArray();
            }
            $divisiBaru = Division::whereIn('id', $newDivisionIds)
                ->orderBy('nama')
                ->pluck('nama')
                ->implode(' & ');
            $divisiBaru = $divisiBaru ?: $divisiLama;

            $promotion = Promotion::create([
                'employee_id' => $employee->id,
                'nomor_surat' => $data['nomor_surat'] ?? null,
                'posisi_lama' => $posisiLama ?? '—',
                'posisi_baru' => $data['posisi_baru'],
                'divisi_lama' => $divisiLama,
                'divisi_baru' => $divisiBaru,
                'atasan_lama' => $atasanLama,
                'atasan_baru' => $data['atasan_baru'] ?? $atasanLama,
                'tanggal_efektif' => $data['tanggal_efektif'],
                'jenis' => $data['jenis'],
                'alasan' => $data['alasan'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $employee->update([
                'position' => $data['posisi_baru'],
                'atasan' => $data['atasan_baru'] ?? $atasanLama,
            ]);

            $employee->divisions()->sync($newDivisionIds);

            $activeHistory = $employee->positionHistories()
                ->where('status', 'Aktif')
                ->latest('mulai')
                ->first();

            if ($activeHistory) {
                $activeHistory->update([
                    'selesai' => $data['tanggal_efektif'],
                    'status' => 'Selesai',
                ]);
            }

            PositionHistory::create([
                'employee_id' => $employee->id,
                'jabatan' => $data['posisi_baru'],
                'divisi' => $divisiBaru ?? '—',
                'atasan' => $data['atasan_baru'] ?? $atasanLama,
                'mulai' => $data['tanggal_efektif'],
                'status' => 'Aktif',
            ]);

            $activeContract = $employee->contracts()
                ->where('status', 'berlaku')
                ->latest('tanggal_mulai')
                ->first();

            if ($activeContract) {
                $activeContract->update(['status' => 'selesai']);

                EmployeeContract::create([
                    'employee_id' => $employee->id,
                    'jenis_kontrak' => $activeContract->jenis_kontrak,
                    'posisi' => $data['posisi_baru'],
                    'atasan' => $data['atasan_baru'] ?? $atasanLama,
                    'tanggal_mulai' => $data['tanggal_efektif'],
                    'tanggal_berakhir' => $activeContract->tanggal_berakhir,
                    'status' => 'berlaku',
                    'keterangan' => 'Addendum promosi: ' . $posisiLama . ' → ' . $data['posisi_baru'],
                    'is_addendum' => true,
                ]);
            }

            $pdfPath = $this->generatePdf($promotion, $employee);

            $promotion->update(['pdf_path' => $pdfPath]);

            return $promotion;
        });
    }

    public function generatePdf(Promotion $promotion, ?Employee $employee = null): string
    {
        $employee ??= $promotion->employee;

        $jenisLabel = [
            'promosi' => 'PROMOSI JABATAN',
            'demosi' => 'DEMOSI JABATAN',
            'mutasi' => 'MUTASI JABATAN',
        ];

        $pdf = Pdf::loadView('pdf.surat-promosi', [
            'promotion' => $promotion,
            'employee' => $employee,
            'jenisLabel' => $jenisLabel[$promotion->jenis] ?? strtoupper($promotion->jenis),
        ]);

        $filename = sprintf(
            'promosi_%s_%s.pdf',
            $employee->nik,
            $promotion->tanggal_efektif->format('Ymd')
        );

        $path = "promotions/{$employee->id}/{$filename}";
        $fullPath = Storage::disk('public')->path($path);
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $pdf->save($fullPath);

        return $path;
    }

    public function rollback(Promotion $promotion): void
    {
        $employee = $promotion->employee;

        DB::transaction(function () use ($promotion, $employee) {
            $divisiNamesLama = $promotion->divisi_lama
                ? array_filter(array_map('trim', explode(' & ', $promotion->divisi_lama)))
                : [];

            $divisiIdsLama = $divisiNamesLama
                ? Division::whereIn('nama', $divisiNamesLama)->pluck('id')->toArray()
                : [];

            $employee->update([
                'position' => $promotion->posisi_lama,
                'atasan' => $promotion->atasan_lama,
            ]);

            $employee->divisions()->sync($divisiIdsLama);

            $newHistory = $employee->positionHistories()
                ->where('jabatan', $promotion->posisi_baru)
                ->where('mulai', $promotion->tanggal_efektif)
                ->where('status', 'Aktif')
                ->first();

            if ($newHistory) {
                $newHistory->delete();
            }

            $oldHistory = $employee->positionHistories()
                ->where('jabatan', $promotion->posisi_lama)
                ->where('selesai', $promotion->tanggal_efektif)
                ->where('status', 'Selesai')
                ->first();

            if ($oldHistory) {
                $oldHistory->update([
                    'selesai' => null,
                    'status' => 'Aktif',
                ]);
            }

            $addendumContract = $employee->contracts()
                ->where('is_addendum', true)
                ->where('posisi', $promotion->posisi_baru)
                ->where('tanggal_mulai', $promotion->tanggal_efektif)
                ->where('status', 'berlaku')
                ->first();

            if ($addendumContract) {
                $addendumContract->delete();
            }

            $oldContract = $employee->contracts()
                ->where('posisi', $promotion->posisi_lama)
                ->where('status', 'selesai')
                ->latest('tanggal_mulai')
                ->first();

            if ($oldContract) {
                $oldContract->update(['status' => 'berlaku']);
            }

            if ($promotion->pdf_path && Storage::disk('public')->exists($promotion->pdf_path)) {
                Storage::disk('public')->delete($promotion->pdf_path);
            }

            $promotion->delete();
        });
    }
}
