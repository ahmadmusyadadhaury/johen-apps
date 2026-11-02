<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $emailChanged = $request->user()->isDirty('email');
        if ($emailChanged) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($emailChanged && $request->user()->email) {
            try {
                $request->user()->sendEmailVerificationNotification();
            } catch (\Throwable $exception) {
                report($exception);

                return Redirect::route('profile.edit')
                    ->with('status', 'profile-updated')
                    ->with('email_verification_error', 'Email tersimpan, tetapi tautan verifikasi gagal dikirim. Silakan kirim ulang.');
            }

            return Redirect::route('profile.edit')->with('status', 'verification-link-sent');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function downloadPayrollTemplate()
    {
        $user = auth()->user();
        abort_unless($user && ($user->isAdminMaster() || $user->isSuperAdmin()), 403);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Payroll');

        $headers = [
            'NIK', 'Nama', 'Divisi', 'Jabatan', 'Gaji Pokok', 'Tunjangan Jabatan',
            'Tambahan Upah', 'Bonus Absensi Full', 'Pengembalian', 'Tips Pelanggan',
            'Insentif Creative', 'Premi BPJS Kesehatan', 'Tambahan Upah Sold', 'THR',
            'THR Dibayarkan', 'Potongan Pinjaman', 'Potongan Absensi Ketidakhadiran',
            'Potongan Absensi Keterlambatan', 'Potongan BPJS Kesehatan 4%',
            'Potongan BPJS Kesehatan 1%', 'Password PDF', 'Total Diterima',
        ];

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column.'1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:V1');
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('U:U')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('E:T')->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('V:V')->getNumberFormat()->setFormatCode('#,##0.00');

        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Petunjuk');
        $instructions = [
            'PETUNJUK TEMPLATE PAYROLL',
            'Isi data mulai baris kedua pada sheet Data Payroll. Jangan mengubah atau mengurutkan ulang kolom.',
            'NIK harus sama dengan NIK karyawan yang terdaftar di aplikasi.',
            'Nama, Jabatan, dan Gaji Pokok wajib diisi. Nilai gaji dan tunjangan diisi angka tanpa awalan Rp.',
            'Kolom Total Diterima digunakan untuk mencocokkan perhitungan sistem. Kosongkan jika tidak digunakan.',
            'Password PDF opsional, maksimal 50 karakter, dan digunakan untuk membuka file slip gaji.',
            'Simpan workbook dalam format .xlsx sebelum mengunggah melalui menu Payroll.',
        ];
        foreach ($instructions as $index => $instruction) {
            $guide->setCellValue('A'.($index + 1), $instruction);
        }
        $guide->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('1D4ED8');
        $guide->getColumnDimension('A')->setWidth(110);
        $guide->getStyle('A1:A7')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        foreach (range(2, 7) as $row) {
            $guide->getRowDimension($row)->setRowHeight(30);
        }
        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 'Template_Payroll.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function pin(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updatePin(PinUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasPin()) {
            if (!password_verify($request->current_pin, $user->pin_hash)) {
                return back()->withErrors(['current_pin' => 'PIN saat ini tidak sesuai.'])->withInput();
            }
        }

        $isNew = !$user->hasPin();
        $user->pin_hash = bcrypt($request->pin);
        $user->save();

        $message = $isNew ? 'PIN Persetujuan berhasil dibuat.' : 'PIN Persetujuan berhasil diperbarui.';

        return Redirect::route('profile.edit')->with('pin_success', $message);
    }

}
