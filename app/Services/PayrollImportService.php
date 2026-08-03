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
                'gaji_pokok' => (float) ($row[4] ?? 0),
                'tambahan_upah' => (float) ($row[5] ?? 0),
                'bonus' => (float) ($row[6] ?? 0),
                'thr' => (float) ($row[7] ?? 0),
                'apresiasi' => (float) ($row[8] ?? 0),
                'tunjangan_jabatan' => (float) ($row[9] ?? 0),
                'premi_bpjs_kesehatan' => (float) ($row[10] ?? 0),
                'thr_dibayarkan' => (float) ($row[11] ?? 0),
                'potongan_pinjaman' => (float) ($row[12] ?? 0),
                'potongan_absensi' => (float) ($row[13] ?? 0),
                'potongan_bpjs_kesehatan_4' => (float) ($row[14] ?? 0),
                'potongan_bpjs_kesehatan_1' => (float) ($row[15] ?? 0),
                'pdf_password' => trim($row[16] ?? ''),
            ];

            $validator = Validator::make($rowData, [
                'nik' => 'required|string',
                'nama' => 'required|string|max:255',
                'divisi' => 'nullable|string|max:255',
                'jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric|min:0',
                'tambahan_upah' => 'numeric|min:0',
                'bonus' => 'numeric|min:0',
                'thr' => 'numeric|min:0',
                'apresiasi' => 'numeric|min:0',
                'tunjangan_jabatan' => 'numeric|min:0',
                'premi_bpjs_kesehatan' => 'numeric|min:0',
                'thr_dibayarkan' => 'numeric|min:0',
                'potongan_pinjaman' => 'numeric|min:0',
                'potongan_absensi' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_4' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_1' => 'numeric|min:0',
                'pdf_password' => 'required|string|min:1|max:50',
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
            $rowData['take_home_pay'] = $rowData['gaji_pokok'] + $rowData['tambahan_upah'] + $rowData['bonus'] + $rowData['thr'] + $rowData['apresiasi'] + $rowData['tunjangan_jabatan'] + $rowData['premi_bpjs_kesehatan'] - $rowData['thr_dibayarkan'] - $rowData['potongan_pinjaman'] - $rowData['potongan_absensi'] - $rowData['potongan_bpjs_kesehatan_4'] - $rowData['potongan_bpjs_kesehatan_1'];
            $validData[] = $rowData;
        }

        $payrollImport = DB::transaction(function () use ($validData, $periode, $filePath, $uploadedBy, $errors) {
            $import = PayrollImport::create([
                'periode' => $periode,
                'file_name' => 'Payroll ' . $periode,
                'total_employee' => count($validData),
                'total_payroll' => collect($validData)->sum('take_home_pay'),
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
                    'tambahan_upah' => $data['tambahan_upah'],
                    'bonus' => $data['bonus'],
                    'thr' => $data['thr'],
                    'apresiasi' => $data['apresiasi'],
                    'tunjangan_jabatan' => $data['tunjangan_jabatan'],
                    'premi_bpjs_kesehatan' => $data['premi_bpjs_kesehatan'],
                    'thr_dibayarkan' => $data['thr_dibayarkan'],
                    'potongan_pinjaman' => $data['potongan_pinjaman'],
                    'potongan_absensi' => $data['potongan_absensi'],
                    'potongan_bpjs_kesehatan_4' => $data['potongan_bpjs_kesehatan_4'],
                    'potongan_bpjs_kesehatan_1' => $data['potongan_bpjs_kesehatan_1'],
                    'take_home_pay' => $data['take_home_pay'],
                    'pdf_password' => $data['pdf_password'],
                ]);
            }

            return $import;
        });

        $payrollImport->setAttribute('errors', $errors);
        $payrollImport->setAttribute('invalid_rows', count($errors));

        return $payrollImport;
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
                'tambahan_upah' => 'numeric|min:0',
                'bonus' => 'numeric|min:0',
                'thr' => 'numeric|min:0',
                'apresiasi' => 'numeric|min:0',
                'tunjangan_jabatan' => 'numeric|min:0',
                'premi_bpjs_kesehatan' => 'numeric|min:0',
                'thr_dibayarkan' => 'numeric|min:0',
                'potongan_pinjaman' => 'numeric|min:0',
                'potongan_absensi' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_4' => 'numeric|min:0',
                'potongan_bpjs_kesehatan_1' => 'numeric|min:0',
                'pdf_password' => 'required|string|min:1|max:50',
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
