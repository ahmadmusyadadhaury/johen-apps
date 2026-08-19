<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeContract;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function employees()
    {
        $employees = Employee::with('divisions')->orderBy('nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Karyawan');

        $headers = ['No', 'NIK', 'Nama', 'Email', 'No HP', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Jabatan', 'Divisi', 'Status', 'Tanggal Masuk'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
            $sheet->getStyle(chr(65 + $i) . '1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($employees as $idx => $emp) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $emp->nik);
            $sheet->setCellValue('C' . $row, $emp->nama);
            $sheet->setCellValue('D' . $row, $emp->email ?? '-');
            $sheet->setCellValue('E' . $row, $emp->no_hp ?? '-');
            $sheet->setCellValue('F' . $row, $emp->jenis_kelamin === 'L' ? 'Laki-laki' : ($emp->jenis_kelamin === 'P' ? 'Perempuan' : '-'));
            $sheet->setCellValue('G' . $row, $emp->tempat_lahir ?? '-');
            $sheet->setCellValue('H' . $row, $emp->tanggal_lahir?->isoFormat('D MMM YYYY') ?? '-');
            $sheet->setCellValue('I' . $row, $emp->position ?? '-');
            $sheet->setCellValue('J' . $row, $emp->divisionNames() ?: '-');
            $sheet->setCellValue('K' . $row, ucfirst($emp->status));
            $sheet->setCellValue('L' . $row, $emp->tanggal_masuk?->isoFormat('D MMM YYYY') ?? '-');
            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="karyawan.xlsx"',
        ]);
    }

    public function divisions()
    {
        $divisions = Division::with('employees')->orderBy('nama')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Divisi');

        $headers = ['No', 'Nama Divisi', 'Koordinator', 'Deskripsi', 'Jumlah Karyawan', 'Status'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
            $sheet->getStyle(chr(65 + $i) . '1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($divisions as $idx => $div) {
            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $div->nama);
            $sheet->setCellValue('C' . $row, $div->koordinator ?? '-');
            $sheet->setCellValue('D' . $row, $div->deskripsi ?? '-');
            $sheet->setCellValue('E' . $row, $div->employees->count());
            $sheet->setCellValue('F' . $row, $div->is_active ? 'Aktif' : 'Nonaktif');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="divisi.xlsx"',
        ]);
    }

    public function kontrakKerja()
    {
        $contracts = EmployeeContract::with('employee.divisions')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kontrak Kerja');

        $headers = ['No', 'Nama Karyawan', 'NIK', 'Jabatan', 'Divisi', 'Jenis Kontrak', 'Tanggal Mulai', 'Tanggal Berakhir', 'Sisa Hari', 'Status'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i) . '1', $h);
            $sheet->getStyle(chr(65 + $i) . '1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($contracts as $idx => $ct) {
            $sisaHari = now()->startOfDay()->diffInDays($ct->tanggal_berakhir, false);
            $isAkanBerakhir = $sisaHari <= 14 && $sisaHari >= 0 && $ct->status === 'berlaku';
            $statusLabel = $ct->status === 'selesai' ? 'Selesai' : ($isAkanBerakhir ? 'Akan Berakhir' : 'Aktif');

            $sheet->setCellValue('A' . $row, $idx + 1);
            $sheet->setCellValue('B' . $row, $ct->employee->nama);
            $sheet->setCellValue('C' . $row, $ct->employee->nik);
            $sheet->setCellValue('D' . $row, $ct->employee->position ?? '-');
            $sheet->setCellValue('E' . $row, $ct->employee->divisionNames() ?: '-');
            $sheet->setCellValue('F' . $row, $ct->jenis_kontrak);
            $sheet->setCellValue('G' . $row, $ct->tanggal_mulai->isoFormat('D MMM YYYY'));
            $sheet->setCellValue('H' . $row, $ct->tanggal_berakhir->isoFormat('D MMM YYYY'));
            $sheet->setCellValue('I' . $row, $sisaHari < 0 ? '-' : $sisaHari . ' hari');
            $sheet->setCellValue('J' . $row, $statusLabel);
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="kontrak-kerja.xlsx"',
        ]);
    }
}
