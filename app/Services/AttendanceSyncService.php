<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSyncService
{
    public function recordPunch(string $machineUserId, string $punchAt, string $method = 'finger', ?string $machineSerial = null, ?array $raw = null): array
    {
        $punchAt = Carbon::parse($punchAt);
        $employee = Employee::where('device_user_id', $machineUserId)->first();

        $duplicate = AttendancePunch::where('machine_user_id', $machineUserId)
            ->where('punch_at', $punchAt->format('Y-m-d H:i:s'))
            ->first();

        if ($duplicate) {
            if ($duplicate->employee_id === null && $employee) {
                $duplicate->employee_id = $employee->id;
                $duplicate->save();
                $this->applyToAttendance($employee, $punchAt, $method);

                return ['status' => 'ok', 'employee_id' => $employee->id, 'machine_user_id' => $machineUserId];
            }

            return ['status' => 'duplicate', 'machine_user_id' => $machineUserId];
        }

        AttendancePunch::create([
            'machine_user_id' => $machineUserId,
            'employee_id' => $employee?->id,
            'punch_at' => $punchAt,
            'method' => $method,
            'machine_serial' => $machineSerial,
            'raw_data' => $raw,
        ]);

        if (! $employee) {
            return ['status' => 'unmatched', 'machine_user_id' => $machineUserId];
        }

        $this->applyToAttendance($employee, $punchAt, $method);

        return ['status' => 'ok', 'employee_id' => $employee->id, 'machine_user_id' => $machineUserId];
    }

    public function backfillForUser(string $machineUserId): array
    {
        $employee = Employee::where('device_user_id', $machineUserId)->first();
        if (! $employee) {
            return ['processed' => 0, 'unmatched' => 1];
        }

        $punches = AttendancePunch::where('machine_user_id', $machineUserId)
            ->whereNull('employee_id')
            ->get();

        foreach ($punches as $punch) {
            $this->recordPunch(
                $punch->machine_user_id,
                $punch->punch_at->format('Y-m-d H:i:s'),
                $punch->method,
                $punch->machine_serial,
                $punch->raw_data,
            );
        }

        return ['processed' => $punches->count(), 'unmatched' => 0];
    }

    private function applyToAttendance(Employee $employee, Carbon $punchAt, string $method): void
    {
        $date = $punchAt->toDateString();
        $time = $punchAt->format('H:i:s');

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $date)
            ->first();

        // Dobel tap: punch kedua dalam waktu berdekatan (mis. 00:46:21 / 00:46:23)
        // akan membuat durasi 0j 0m. Abaikan punch yang masih dalam rentang waktu itu.
        if ($attendance && $this->isDoubleTap($attendance, $time)) {
            return;
        }

        // Shift Malam (datang siang, pulang lewat tengah malam): punch dini hari
        // (00:00 - 06:00) adalah clock-out hari sebelumnya, bukan clock-in tanggal punch.
        if ($this->isMalamShift($employee, $punchAt) && $time >= '00:00:00' && $time < '06:00:00') {
            $prevDate = $punchAt->copy()->subDay()->toDateString();
            $prev = Attendance::where('employee_id', $employee->id)
                ->where('date', $prevDate)
                ->first();

            if ($prev && $prev->status === 'hadir') {
                if ($prev->time_in === null) {
                    $prev->time_in = $time;
                } elseif ($prev->time_out === null) {
                    $prev->time_out = $time;
                }

                $prev->method = $method;
                $prev->save();

                return;
            }
        }

        if (! $attendance) {
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $date,
                'time_in' => $time,
                'time_out' => null,
                'status' => 'hadir',
                'method' => $method,
            ]);

            return;
        }

        if ($attendance->status !== 'hadir') {
            return;
        }

        if ($attendance->time_in === null) {
            $attendance->time_in = $time;
        } elseif ($time < $attendance->time_in) {
            $attendance->time_in = $time;
        } elseif ($attendance->time_out === null) {
            $attendance->time_out = $time;
        } elseif ($time > $attendance->time_out) {
            $attendance->time_out = $time;
        }

        $attendance->method = $method;
        $attendance->save();
    }

    private function isMalamShift(Employee $employee, Carbon $punchAt): bool
    {
        $position = (string) ($employee->position ?? '');
        if (str_contains($position, '(Malam)')) {
            return true;
        }

        $jamKerja = $employee->shiftOn($punchAt->toDateString())['jam_kerja'];
        $jamKerja = (string) ($jamKerja ?? '');
        if (preg_match('/^\s*(\d{1,2})[.:](\d{2})\s*[-–—]\s*(\d{1,2})[.:](\d{2})\s*$/', $jamKerja, $m)) {
            $start = (int) $m[1] * 60 + (int) $m[2];
            $end = (int) $m[3] * 60 + (int) $m[4];

            if ($start >= 10 * 60 && $start <= 18 * 60 && $end >= 22 * 60) {
                return true;
            }
        }

        return false;
    }

    private function isDoubleTap(Attendance $attendance, string $time): bool
    {
        if ($attendance->time_in === null || $attendance->time_out !== null) {
            return false;
        }

        $in = strtotime($attendance->time_in);
        $punch = strtotime($time);

        if ($in === false || $punch === false) {
            return false;
        }

        return abs($punch - $in) < 90;
    }
}
