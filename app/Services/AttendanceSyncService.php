<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSyncService
{
    public function recordPunch(string $machineUserId, string $punchAt, string $method = 'finger', ?string $machineSerial = null, array $raw = null): array
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

        if (!$employee) {
            return ['status' => 'unmatched', 'machine_user_id' => $machineUserId];
        }

        $this->applyToAttendance($employee, $punchAt, $method);

        return ['status' => 'ok', 'employee_id' => $employee->id, 'machine_user_id' => $machineUserId];
    }

    public function backfillForUser(string $machineUserId): array
    {
        $employee = Employee::where('device_user_id', $machineUserId)->first();
        if (!$employee) {
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

        if (!$attendance) {
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
}
