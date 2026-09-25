<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\PayrollDetail;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function payroll(Request $request)
    {
        $year = $request->integer('year', now()->year);

        $fields = [
            ['Periode', fn (PayrollDetail $d) => $d->payrollImport?->periode],
            ['NIK', fn (PayrollDetail $d) => $d->nik],
            ['Nama', fn (PayrollDetail $d) => $d->nama],
            ['Divisi', fn (PayrollDetail $d) => $d->divisi ?? '-'],
            ['Jabatan', fn (PayrollDetail $d) => $d->jabatan],
            ['Gaji Pokok', fn (PayrollDetail $d) => (float) $d->gaji_pokok],
            ['Tunjangan Jabatan', fn (PayrollDetail $d) => (float) $d->tunjangan_jabatan],
            ['Tambahan Upah (Bonus Absensi, Pengembalian, Tips Pelanggan, Insentif Creative, Resepsionist, IT)', fn (PayrollDetail $d) => (float) $d->tambahan_upah],
            ['Bonus Absensi Full 1 Bulan', fn (PayrollDetail $d) => (float) $d->bonus_absensi_full],
            ['Pengembalian', fn (PayrollDetail $d) => (float) $d->pengembalian],
            ['Tips Pelanggan', fn (PayrollDetail $d) => (float) $d->tips_pelanggan],
            ['Insentif View / Sold Creative; Content Creator, Video Editor & Resepsionist', fn (PayrollDetail $d) => (float) $d->insentif_creative],
            ['Premi BPJS Kesehatan (4%)', fn (PayrollDetail $d) => (float) $d->premi_bpjs_kesehatan],
            ['Tambahan Upah (Bonus Sold, View, dll)', fn (PayrollDetail $d) => (float) $d->tambahan_upah_sold],
            ['THR', fn (PayrollDetail $d) => (float) $d->thr],
            ['THR Dibayarkan', fn (PayrollDetail $d) => (float) $d->thr_dibayarkan],
            ['Potongan Pinjaman', fn (PayrollDetail $d) => (float) $d->potongan_pinjaman],
            ['Potongan Absensi (Ketidakhadiran)', fn (PayrollDetail $d) => (float) $d->potongan_absensi_ketidakhadiran],
            ['Potongan Absensi (Keterlambatan)', fn (PayrollDetail $d) => (float) $d->potongan_absensi_keterlambatan],
            ['Potongan BPJS Kesehatan (4%) - Tanggungan Perusahaan', fn (PayrollDetail $d) => (float) $d->potongan_bpjs_kesehatan_4],
            ['Potongan BPJS Kesehatan (1%) - Tanggungan Karyawan', fn (PayrollDetail $d) => (float) $d->potongan_bpjs_kesehatan_1],
            ['Password PDF', fn (PayrollDetail $d) => $d->pdf_password],
            ['Total Diterima', fn (PayrollDetail $d) => (float) $d->take_home_pay],
        ];

        $details = PayrollDetail::with('payrollImport:id,periode')
            ->whereHas('payrollImport', fn ($q) => $q->where('periode', 'LIKE', "%{$year}"))
            ->get()
            ->sortBy([
                fn ($d) => $d->payrollImport?->periode ?? '',
                fn ($d) => $d->nik,
            ])
            ->values();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Slip Gaji');

        foreach ($fields as $i => [$header]) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }

        $row = 2;
        foreach ($details as $detail) {
            foreach ($fields as $i => [, $resolver]) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue($col . $row, $resolver($detail));
            }
            $row++;
        }

        foreach ($fields as $i => [$header]) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="slip-gaji-' . $year . '.xlsx"',
        ]);
    }

    public function employees()
    {
        $columns = [
            'nik', 'nik_ktp', 'device_user_id', 'nama', 'email', 'no_hp',
            'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama',
            'pendidikan_terakhir', 'asal_sekolah', 'ukuran_baju', 'alamat',
            'provinsi', 'kota', 'kecamatan', 'kelurahan', 'rt_rw', 'kode_pos',
            'position', 'atasan', 'atasan2', 'jenis_karyawan', 'lokasi_kerja',
            'jenis_kerja', 'jam_kerja', 'jobdesk',
            'no_kontak_darurat1', 'hubungan_darurat1',
            'no_kontak_darurat2', 'hubungan_darurat2',
            'no_bpjs', 'status_bpjs', 'tipe', 'status_pernikahan', 'tanggal_masuk',
            'informasi_lowongan',
        ];

        $employees = Employee::query()
            ->select(array_map(fn (string $col) => "employees.{$col}", $columns))
            ->with(['divisions:id,nama'])
            ->orderBy('nik')
            ->get();

        $fields = [
            ['No', fn (Employee $e, int $i) => $i + 1],
            ['NIK KTP', fn (Employee $e) => $e->nik_ktp ?? '-'],
            ['Nama Lengkap', fn (Employee $e) => $e->nama],
            ['Tempat Lahir', fn (Employee $e) => $e->tempat_lahir ?? '-'],
            ['Tanggal Lahir', fn (Employee $e) => $e->tanggal_lahir?->isoFormat('D MMM YYYY') ?? '-'],
            ['Jenis Kelamin', fn (Employee $e) => $e->jenis_kelamin === 'L' ? 'Laki-laki' : ($e->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
            ['Tipe Karyawan', fn (Employee $e) => Employee::TIPE_OPTIONS[$e->tipe] ?? ucfirst($e->tipe ?? '') ?: '-'],
            ['Ukuran Baju', fn (Employee $e) => $e->ukuran_baju ?? '-'],
            ['Agama', fn (Employee $e) => $e->agama ?? '-'],
            ['Pendidikan Terakhir', fn (Employee $e) => $e->pendidikan_terakhir ?? '-'],
            ['Asal Sekolah', fn (Employee $e) => $e->asal_sekolah ?? '-'],
            ['Status Pernikahan', fn (Employee $e) => $e->status_pernikahan ? ucfirst($e->status_pernikahan) : '-'],
            ['Provinsi', fn (Employee $e) => $e->provinsi ? ucwords(strtolower($e->provinsi)) : '-'],
            ['Kota/Kabupaten', fn (Employee $e) => $e->kota ? ucwords(strtolower($e->kota)) : '-'],
            ['Kecamatan', fn (Employee $e) => $e->kecamatan ? ucwords(strtolower($e->kecamatan)) : '-'],
            ['Kelurahan/Desa', fn (Employee $e) => $e->kelurahan ? ucwords(strtolower($e->kelurahan)) : '-'],
            ['RT / RW', fn (Employee $e) => $e->rt_rw ?? '-'],
            ['Kode Pos', fn (Employee $e) => $e->kode_pos ?? '-'],
            ['Alamat Lengkap', fn (Employee $e) => $e->alamat ?? '-'],
            ['NIP (Nomor Induk Pegawai)', fn (Employee $e) => $e->nik],
            ['Divisi', fn (Employee $e) => $e->divisionNames() ?: '-'],
            ['Jabatan', fn (Employee $e) => $e->position ?? '-'],
            ['Atasan 1', fn (Employee $e) => $e->atasan ?? '-'],
            ['Atasan 2', fn (Employee $e) => $e->atasan2 ?? '-'],
            ['Tanggal Bergabung', fn (Employee $e) => $e->tanggal_masuk?->isoFormat('D MMM YYYY') ?? '-'],
            ['Jenis Karyawan', fn (Employee $e) => $e->jenis_karyawan ?? '-'],
            ['Lokasi Kerja', fn (Employee $e) => $e->lokasi_kerja ?? '-'],
            ['Jenis Kerja', fn (Employee $e) => $e->jenis_kerja ?? '-'],
            ['Jam Kerja', fn (Employee $e) => $e->jam_kerja ?? '-'],
            ['Jobdesk', fn (Employee $e) => $e->jobdesk ?? '-'],
            ['No. Telepon', fn (Employee $e) => $e->no_hp ?? '-'],
            ['Email', fn (Employee $e) => $e->email ?? '-'],
            ['Informasi Lowongan', fn (Employee $e) => $e->informasi_lowongan ?? '-'],
            ['No. Kontak Darurat 1', fn (Employee $e) => $e->no_kontak_darurat1 ?? '-'],
            ['Hubungan', fn (Employee $e) => $e->hubungan_darurat1 ?? '-'],
            ['No. Kontak Darurat 2', fn (Employee $e) => $e->no_kontak_darurat2 ?? '-'],
            ['Hubungan', fn (Employee $e) => $e->hubungan_darurat2 ?? '-'],
            ['No. BPJS', fn (Employee $e) => $e->no_bpjs ?? '-'],
            ['Status BPJS', fn (Employee $e) => $e->status_bpjs ?? '-'],
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
