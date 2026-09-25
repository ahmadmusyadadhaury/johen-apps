<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollDetail;
use App\Models\PayrollImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PayrollImportService
{
    public function import(string $filePath, string $periode, int $uploadedBy): PayrollImport
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $header = array_shift($rows);
        $data = collect($rows)->filter(fn($row) => !empty(array_filter($row)));

        $errors = [];
        $validData = [];

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2;
            $rowData = [
                'nik' => trim($row[0] ?? ''),
                'nama' => trim($row[1] ?? ''),
                'divisi' => trim($row[2] ?? ''),
                'jabatan' => trim($row[3] ?? ''),
                'gaji_pokok' => $this->parseAmount($row[4] ?? 0),
                'tunjangan_jabatan' => $this->parseAmount($row[5] ?? 0),
                'tambahan_upah' => $this->parseAmount($row[6] ?? 0),
                'bonus_absensi_full' => $this->parseAmount($row[7] ?? 0),
                'pengembalian' => $this->parseAmount($row[8] ?? 0),
                'tips_pelanggan' => $this->parseAmount($row[9] ?? 0),
                'insentif_creative' => $this->parseAmount($row[10] ?? 0),
                'premi_bpjs_kesehatan' => $this->parseAmount($row[11] ?? 0),
                'tambahan_upah_sold' => $this->parseAmount($row[12] ?? 0),
                'thr' => $this->parseAmount($row[13] ?? 0),
                'thr_dibayarkan' => $this->parseAmount($row[14] ?? 0),
                'potongan_pinjaman' => $this->parseAmount($row[15] ?? 0),
                'potongan_absensi_ketidakhadiran' => $this->parseAmount($row[16] ?? 0),
                'potongan_absensi_keterlambatan' => $this->parseAmount($row[17] ?? 0),
                'potongan_bpjs_kesehatan_4' => $this->parseAmount($row[18] ?? 0),
                'potongan_bpjs_kesehatan_1' => $this->parseAmount($row[19] ?? 0),
                'pdf_password' => trim($row[20] ?? ''),
                'total_diterima_sumber' => trim($row[21] ?? ''),
            ];

            $validator = Validator::make($rowData, [
                'nik' => 'required|string',
                'nama' => 'required|string|max:255',
                'divisi' => 'nullable|string|max:255',
                'jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric|min:0',
                'tunjangan_jabatan' => 'numeric|min:0',
                'tambahan_upah' => 'numeric|min:0',
                'bonus_absensi_full' => 'numeric|min:0',
                'pengembalian' => 'numeric|min:0',
                'tips_pelanggan' => 'numeric|min:0',
                'insentif_creative' => 'numeric|min:0',
                'premi_bpjs_kesehatan' => 'numeric|min:0',
                'tambahan_upah_sold' => 'numeric|min:0',
                'thr' => 'numeric|min:0',
                'thr_dibayarkan' => 'numeric|min:0',
                'potongan_pinjaman' => 'numeric|min:0',
                'potongan_absensi_ketidakhadiran' => 'numeric|min:0',
                'potongan_absensi_keterlambatan' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_4' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_1' => 'numeric|min:0',
                'pdf_password' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'data' => $rowData,
                    'errors' => $validator->errors()->toArray(),
                ];
                continue;
            }

            $employee = Employee::where('nik', $rowData['nik'])->first();
            if (!$employee) {
                $errors[] = [
                    'row' => $rowNumber,
                    'data' => $rowData,
                    'errors' => ['nik' => ['NIK ' . $rowData['nik'] . ' tidak ditemukan di data karyawan.']],
                ];
                continue;
            }

            $rowData['employee_id'] = $employee->id;
            $rowData['email'] = $employee->email ?? $employee->user?->email ?? '';

            if ($rowData['tambahan_upah'] <= 0) {
                $rowData['tambahan_upah'] = $rowData['bonus_absensi_full'] + $rowData['pengembalian'] + $rowData['tips_pelanggan'] + $rowData['insentif_creative'];
            }

            $rowData['take_home_pay'] = $rowData['gaji_pokok'] + $rowData['tunjangan_jabatan'] + $rowData['tambahan_upah'] + $rowData['premi_bpjs_kesehatan'] + $rowData['tambahan_upah_sold'] + $rowData['thr'] - $rowData['thr_dibayarkan'] - $rowData['potongan_pinjaman'] - $rowData['potongan_absensi_ketidakhadiran'] - $rowData['potongan_absensi_keterlambatan'] - $rowData['potongan_bpjs_kesehatan_4'] - $rowData['potongan_bpjs_kesehatan_1'];

            if ($rowData['total_diterima_sumber'] !== '' && $rowData['total_diterima_sumber'] !== '-' && (float) $rowData['total_diterima_sumber'] > 0) {
                $totalSumber = $this->parseAmount($rowData['total_diterima_sumber']);
                $selisih = abs($totalSumber - $rowData['take_home_pay']);
                if ($selisih > 1) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'data' => $rowData,
                        'errors' => ['total_diterima' => ['Kolom Total Diterima (' . number_format($totalSumber, 0, ',', '.') . ') tidak sesuai dengan perhitungan sistem (' . number_format($rowData['take_home_pay'], 0, ',', '.') . ').']],
                    ];
                    continue;
                }
            }

            $validData[] = $rowData;
        }

        $payrollImport = DB::transaction(function () use ($validData, $periode, $filePath, $uploadedBy, $errors) {
            $import = PayrollImport::create([
                'periode' => $periode,
                'file_name' => 'Payroll ' . $periode,
                'total_employee' => count($validData),
                'total_payroll' => collect($validData)->sum('take_home_pay'),
                'errors' => $errors,
                'invalid_rows' => count($errors),
                'uploaded_by' => $uploadedBy,
            ]);

            foreach ($validData as $data) {
                PayrollDetail::create([
                    'payroll_import_id' => $import->id,
                    'employee_id' => $data['employee_id'],
                    'nik' => $data['nik'],
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'divisi' => $data['divisi'],
                    'jabatan' => $data['jabatan'],
                    'gaji_pokok' => $data['gaji_pokok'],
                    'tunjangan_jabatan' => $data['tunjangan_jabatan'],
                    'tambahan_upah' => $data['tambahan_upah'],
                    'bonus_absensi_full' => $data['bonus_absensi_full'],
                    'pengembalian' => $data['pengembalian'],
                    'tips_pelanggan' => $data['tips_pelanggan'],
                    'insentif_creative' => $data['insentif_creative'],
                    'premi_bpjs_kesehatan' => $data['premi_bpjs_kesehatan'],
                    'tambahan_upah_sold' => $data['tambahan_upah_sold'],
                    'thr' => $data['thr'],
                    'thr_dibayarkan' => $data['thr_dibayarkan'],
                    'potongan_pinjaman' => $data['potongan_pinjaman'],
                    'potongan_absensi_ketidakhadiran' => $data['potongan_absensi_ketidakhadiran'],
                    'potongan_absensi_keterlambatan' => $data['potongan_absensi_keterlambatan'],
                    'potongan_bpjs_kesehatan_4' => $data['potongan_bpjs_kesehatan_4'],
                    'potongan_bpjs_kesehatan_1' => $data['potongan_bpjs_kesehatan_1'],
                    'take_home_pay' => $data['take_home_pay'],
                    'pdf_password' => $data['pdf_password'],
                ]);
            }

            return $import;
        });

        return $payrollImport;
    }

    private function parseAmount(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $cleaned = trim((string) $value);
        $cleaned = str_replace(['Rp', 'RP', 'rp', ' '], '', $cleaned);

        if ($cleaned === '' || $cleaned === '-' || in_array($cleaned, ['0', '0.0', '0,0'])) {
            return 0.0;
        }

        // Titik ribuan ala Indonesia: "5.000", "500.000", "5.000.000", opsional koma desimal
        if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $cleaned)) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
            return (float) $cleaned;
        }

        // Koma desimal saja: "1,5"
        if (str_contains($cleaned, ',') && !str_contains($cleaned, '.')) {
            $cleaned = str_replace(',', '.', $cleaned);
            return (float) $cleaned;
        }

        return (float) $cleaned;
    }

    public function validate(array $data): array
    {
        $errors = [];
        $validData = [];

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2;
            $validator = Validator::make($row, [
                'nik' => 'required|string',
                'nama' => 'required|string|max:255',
                'divisi' => 'nullable|string|max:255',
                'jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric|min:0',
                'tunjangan_jabatan' => 'numeric|min:0',
                'tambahan_upah' => 'numeric|min:0',
                'bonus_absensi_full' => 'numeric|min:0',
                'pengembalian' => 'numeric|min:0',
                'tips_pelanggan' => 'numeric|min:0',
                'insentif_creative' => 'numeric|min:0',
                'premi_bpjs_kesehatan' => 'numeric|min:0',
                'tambahan_upah_sold' => 'numeric|min:0',
                'thr' => 'numeric|min:0',
                'thr_dibayarkan' => 'numeric|min:0',
                'potongan_pinjaman' => 'numeric|min:0',
                'potongan_absensi_ketidakhadiran' => 'numeric|min:0',
                'potongan_absensi_keterlambatan' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_4' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_1' => 'numeric|min:0',
                'pdf_password' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'errors' => $validator->errors()->toArray(),
                ];
            } else {
                $employee = Employee::where('nik', $row['nik'])->first();
                if (!$employee) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'data' => $row,
                        'errors' => ['nik' => ['NIK ' . $row['nik'] . ' tidak ditemukan di data karyawan.']],
                    ];
                } else {
                    $row['employee_id'] = $employee->id;
                    $row['email'] = $employee->email ?? $employee->user?->email ?? '';
                    $validData[] = $row;
                }
            }
        }

        return compact('validData', 'errors');
    }
}
