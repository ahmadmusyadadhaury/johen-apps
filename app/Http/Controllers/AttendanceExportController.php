<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendanceExportController extends Controller
{
    public function export(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $filters = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $filters['date'])->startOfDay();
        $employees = Employee::query()
            ->where('tipe', Employee::TIPE_KARYAWAN_AKTIF)
            ->listSelect()
            ->when($filters['search'] ?? '', function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nik', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('CAST(nik AS UNSIGNED) ASC')
            ->get();

        $employeeIds = $employees->pluck('id');
        $attendances = Attendance::with('employee')
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('date', $date->toDateString())
            ->get()
            ->keyBy('employee_id');

        // Ikuti jendela detail presensi: punch sebelum pukul 07.00 esok hari
        // masih dapat menjadi checkout untuk tanggal kerja yang dipilih.
        $punchesByEmployee = AttendancePunch::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('punch_at', '>=', $date)
            ->where('punch_at', '<', $date->copy()->addDay()->setTime(
                (int) config('attendance.overnight_latest_checkout_hour', 7), 0
            ))
            ->orderBy('punch_at')
            ->get()
            ->groupBy('employee_id');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Presensi');

        $headers = ['NIP', 'Nama Pegawai', 'Jabatan', 'Jam Masuk', 'Jam Keluar', 'Durasi Kerja', 'Status'];
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1).'1', $header);
        }

        $row = 2;
        $duplicateWindow = (int) config('attendance.tap_duplicate_window_seconds', 180);

        foreach ($employees as $employee) {
            $attendance = $attendances->get($employee->id);
            $punches = $punchesByEmployee->get($employee->id, collect());

            // Satu cluster tap berulang dihitung sebagai satu kejadian.
            $times = [];
            $previousPunchAt = null;
            foreach ($punches as $punch) {
                $punchTimestamp = $punch->punch_at->getTimestamp();
                if ($previousPunchAt !== null && $punchTimestamp - $previousPunchAt < $duplicateWindow) {
                    $previousPunchAt = $punchTimestamp;
                    continue;
                }

                $times[] = $punch->punch_at->format('H:i');
                $previousPunchAt = $punchTimestamp;
            }

            $inTimes = [];
            $outTimes = [];
            $startsWithCheckout = $attendance && ! $attendance->time_in && $attendance->time_out;
            foreach ($times as $index => $time) {
                $isCheckIn = ($index % 2 === 0) !== (bool) $startsWithCheckout;
                if ($isCheckIn) {
                    $inTimes[] = (count($inTimes) + 1).'. '.$time;
                } else {
                    $outTimes[] = (count($outTimes) + 1).'. '.$time;
                }
            }

            // Bila punch mentah belum tersedia, tetap tampilkan ringkasan
            // presensi yang tersimpan.
            $jamMasuk = $inTimes ? implode("\n", $inTimes) : ($attendance?->time_in ? Carbon::parse($attendance->time_in)->format('H:i') : '-');
            $jamKeluar = $outTimes ? implode("\n", $outTimes) : ($attendance?->time_out ? Carbon::parse($attendance->time_out)->format('H:i') : '-');
            $status = $attendance?->displayStatusForViewer(true)
                ?? ($employee->isWeeklyDayOff($date) ? 'libur' : 'tidak hadir');

            $sheet->setCellValueExplicit('A'.$row, (string) ($employee->nik ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue('B'.$row, $employee->nama ?? '-');
            $sheet->setCellValue('C'.$row, $employee->position ?? '-');
            $sheet->setCellValue('D'.$row, $jamMasuk);
            $sheet->setCellValue('E'.$row, $jamKeluar);
            $sheet->setCellValue('F'.$row, $attendance?->duration ?? '-');
            $sheet->setCellValue('G'.$row, $status);
            $row++;
        }

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFEFF6FF');
        if ($row > 2) {
            $sheet->getStyle('D2:E'.($row - 1))->getAlignment()->setWrapText(true)->setVertical('top');
            $sheet->getAutoFilter()->setRange('A1:G'.($row - 1));
        }
        foreach (['A' => 16, 'B' => 30, 'C' => 28, 'D' => 18, 'E' => 18, 'F' => 16, 'G' => 18] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->freezePane('A2');

        $filename = 'presensi_'.$date->format('Ymd').'.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'presensi_');
        (new Xlsx($spreadsheet))->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
