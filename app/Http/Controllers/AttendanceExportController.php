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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AttendanceExportController extends Controller
{
    public function export(Request $request)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $filters = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'period' => ['nullable', 'in:week,month'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $period = $filters['period'] ?? 'week';
        $start = Carbon::createFromFormat('Y-m-d', $filters['date'])->startOfDay();
        $end = $period === 'month'
            ? $start->copy()->endOfMonth()->startOfDay()
            : $start->copy()->addDays(6)->startOfDay();
        $dates = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $dates[] = $day->copy();
        }

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
        // The export no longer uses employee relations here. Eager-loading them
        // also loads employee photos (stored as base64) repeatedly for every
        // attendance row, which can exceed PHP's memory limit on monthly exports.
        $attendanceByDay = Attendance::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->keyBy(fn (Attendance $attendance) => $attendance->employee_id.'|'.$attendance->date->toDateString());

        // For each work date, include the configured early-morning checkout window.
        // The preceding date owns these overnight punches, matching the current daily export.
        $overnightHour = (int) config('attendance.overnight_latest_checkout_hour', 7);
        $punches = AttendancePunch::query()
            ->whereIn('employee_id', $employeeIds)
            ->where('punch_at', '>=', $start)
            ->where('punch_at', '<', $end->copy()->addDay()->setTime($overnightHour, 0))
            ->orderBy('punch_at')
            ->get()
            ->groupBy(function (AttendancePunch $punch) use ($overnightHour) {
                $workDate = $punch->punch_at->copy();
                if ((int) $workDate->format('G') < $overnightHour) {
                    $workDate->subDay();
                }

                return $punch->employee_id.'|'.$workDate->toDateString();
            });

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Presensi');
        $lastColumn = Coordinate::stringFromColumnIndex(2 + count($dates) * 2);

        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'No.ID');
        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        foreach ($dates as $index => $day) {
            $firstColumn = Coordinate::stringFromColumnIndex(3 + $index * 2);
            $secondColumn = Coordinate::stringFromColumnIndex(4 + $index * 2);
            $sheet->setCellValue($firstColumn.'1', $day->format('d/m/Y'));
            $sheet->mergeCells($firstColumn.'1:'.$secondColumn.'1');
            $sheet->setCellValue($firstColumn.'2', 'In');
            $sheet->setCellValue($secondColumn.'2', 'Out');
        }

        $row = 3;
        $duplicateWindow = (int) config('attendance.tap_duplicate_window_seconds', 180);
        foreach ($employees as $employee) {
            $sheet->setCellValue('A'.$row, $employee->nama ?? '-');
            $sheet->setCellValueExplicit('B'.$row, (string) ($employee->nik ?? '-'), DataType::TYPE_STRING);

            foreach ($dates as $index => $day) {
                $key = $employee->id.'|'.$day->toDateString();
                $attendance = $attendanceByDay->get($key);
                $dayPunches = $punches->get($key, collect());

                // Collapse rapid repeated scans, then alternate arrival/departure as in the daily export.
                $times = [];
                $previousPunchAt = null;
                foreach ($dayPunches as $punch) {
                    $timestamp = $punch->punch_at->getTimestamp();
                    if ($previousPunchAt !== null && $timestamp - $previousPunchAt < $duplicateWindow) {
                        $previousPunchAt = $timestamp;
                        continue;
                    }
                    $times[] = $punch->punch_at->format('H:i');
                    $previousPunchAt = $timestamp;
                }

                $startsWithCheckout = $attendance && ! $attendance->time_in && $attendance->time_out;
                $inTimes = [];
                $outTimes = [];
                foreach ($times as $tapIndex => $time) {
                    $isCheckIn = ($tapIndex % 2 === 0) !== (bool) $startsWithCheckout;
                    if ($isCheckIn) {
                        $inTimes[] = $time;
                    } else {
                        $outTimes[] = $time;
                    }
                }

                // The screenshot layout has one value per In/Out cell: first arrival and last departure.
                $in = $inTimes[0] ?? ($attendance?->time_in ? Carbon::parse($attendance->time_in)->format('H:i') : '-');
                $out = $outTimes ? end($outTimes) : ($attendance?->time_out ? Carbon::parse($attendance->time_out)->format('H:i') : '-');
                $inColumn = Coordinate::stringFromColumnIndex(3 + $index * 2);
                $outColumn = Coordinate::stringFromColumnIndex(4 + $index * 2);
                $sheet->setCellValue($inColumn.$row, $in);
                $sheet->setCellValue($outColumn.$row, $out);
            }
            $row++;
        }

        $headerRange = 'A1:'.$lastColumn.'2';
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF263648');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3:B'.max(3, $row - 1))->getFont()->setBold(true);
        $sheet->getStyle('A3:'.$lastColumn.max(3, $row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFD7E0E8');
        $sheet->getStyle('C3:'.$lastColumn.max(3, $row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        for ($index = 0; $index < count($dates); $index++) {
            $inColumn = Coordinate::stringFromColumnIndex(3 + $index * 2);
            $outColumn = Coordinate::stringFromColumnIndex(4 + $index * 2);
            $sheet->getStyle($inColumn.'3:'.$inColumn.max(3, $row - 1))->getFont()->getColor()->setARGB('FF47735D');
            $sheet->getStyle($outColumn.'3:'.$outColumn.max(3, $row - 1))->getFont()->getColor()->setARGB('FFB46A6A');
        }
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(23);
        $sheet->getColumnDimension('A')->setWidth(36);
        $sheet->getColumnDimension('B')->setWidth(10);
        for ($column = 3; $column <= 2 + count($dates) * 2; $column++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setWidth(10);
        }
        $sheet->freezePane('C3');

        $filename = 'presensi_'.$period.'_'.$start->format('Ymd').'_sampai_'.$end->format('Ymd').'.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'presensi_');
        (new Xlsx($spreadsheet))->save($temp);

        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }
}
