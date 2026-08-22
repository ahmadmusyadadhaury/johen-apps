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
        $columns = [
            'nik', 'device_user_id', 'nama', 'email', 'no_hp',
            'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
            'pendidikan_terakhir', 'ukuran_baju', 'alamat',
            'position', 'atasan', 'atasan2', 'jenis_karyawan', 'lokasi_kerja',
            'jenis_kerja', 'jam_kerja', 'jam_masuk', 'jobdesk',
            'no_kontak_darurat1', 'hubungan_darurat1',
            'no_kontak_darurat2', 'hubungan_darurat2',
            'no_bpjs', 'status_bpjs', 'status', 'tanggal_masuk', 'tanggal_resign',
            'informasi_lowongan', 'catatan',
        ];

        $employees = Employee::query()
            ->select(array_map(fn (string $col) => "employees.{$col}", $columns))
            ->with(['divisions:id,nama'])
            ->orderBy('nik')
            ->get();

        $fields = [
            ['No', fn (Employee $e, int $i) => $i + 1],
            ['NIK', fn (Employee $e) => $e->nik],
            ['Nama', fn (Employee $e) => $e->nama],
            ['Email', fn (Employee $e) => $e->email ?? '-'],
            ['No HP', fn (Employee $e) => $e->no_hp ?? '-'],
            ['Jenis Kelamin', fn (Employee $e) => $e->jenis_kelamin === 'L' ? 'Laki-laki' : ($e->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
            ['Tempat Lahir', fn (Employee $e) => $e->tempat_lahir ?? '-'],
            ['Tanggal Lahir', fn (Employee $e) => $e->tanggal_lahir?->isoFormat('D MMM YYYY') ?? '-'],
            ['Agama', fn (Employee $e) => $e->agama ?? '-'],
            ['Pendidikan Terakhir', fn (Employee $e) => $e->pendidikan_terakhir ?? '-'],
            ['Ukuran Baju', fn (Employee $e) => $e->ukuran_baju ?? '-'],
            ['Alamat', fn (Employee $e) => $e->alamat ?? '-'],
            ['Jabatan', fn (Employee $e) => $e->position ?? '-'],
            ['Divisi', fn (Employee $e) => $e->divisionNames() ?: '-'],
            ['Atasan', fn (Employee $e) => $e->atasan ?? '-'],
            ['Atasan 2', fn (Employee $e) => $e->atasan2 ?? '-'],
            ['Jenis Karyawan', fn (Employee $e) => $e->jenis_karyawan ?? '-'],
            ['Lokasi Kerja', fn (Employee $e) => $e->lokasi_kerja ?? '-'],
            ['Jenis Kerja', fn (Employee $e) => $e->jenis_kerja ?? '-'],
            ['Jam Kerja', fn (Employee $e) => $e->jam_kerja ?? '-'],
            ['Jam Masuk', fn (Employee $e) => $e->jam_masuk ?? '-'],
            ['Jobdesk', fn (Employee $e) => $e->jobdesk ?? '-'],
            ['No Kontak Darurat 1', fn (Employee $e) => $e->no_kontak_darurat1 ?? '-'],
            ['Hubungan Darurat 1', fn (Employee $e) => $e->hubungan_darurat1 ?? '-'],
            ['No Kontak Darurat 2', fn (Employee $e) => $e->no_kontak_darurat2 ?? '-'],
            ['Hubungan Darurat 2', fn (Employee $e) => $e->hubungan_darurat2 ?? '-'],
            ['No BPJS', fn (Employee $e) => $e->no_bpjs ?? '-'],
            ['Status BPJS', fn (Employee $e) => $e->status_bpjs ?? '-'],
            ['Status', fn (Employee $e) => ucfirst($e->status)],
            ['Tanggal Masuk', fn (Employee $e) => $e->tanggal_masuk?->isoFormat('D MMM YYYY') ?? '-'],
            ['Tanggal Resign', fn (Employee $e) => $e->tanggal_resign?->isoFormat('D MMM YYYY') ?? '-'],
            ['Device User ID', fn (Employee $e) => $e->device_user_id ?? '-'],
            ['Informasi Lowongan', fn (Employee $e) => $e->informasi_lowongan ?? '-'],
            ['Catatan', fn (Employee $e) => $e->catatan ?? '-'],
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Karyawan');

        foreach ($fields as $i => [$header]) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($employees as $idx => $emp) {
            foreach ($fields as $i => [, $resolver]) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue($col . $row, $resolver($emp, $idx));
            }
            $row++;
        }

        for ($i = 1; $i <= count($fields); $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
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
        $divisions = Division::withCount('employees')->orderBy('nama')->get();

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
            $sheet->setCellValue('E' . $row, $div->employees_count);
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
        $contracts = EmployeeContract::with(['employee' => fn ($q) => $q->listSelect()])
            ->with('employee.divisions:id,nama')
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
