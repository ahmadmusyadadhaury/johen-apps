<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AttendanceFixCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function employee(string $nik, string $userId): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => 'Karyawan '.$nik,
            'status' => 'aktif',
            'device_user_id' => $userId,
        ]);
    }

    private function record(string $userId, string $punchAt): void
    {
        app(\App\Services\AttendanceSyncService::class)->recordPunch($userId, $punchAt, 'mesin');
    }

    private function findAttendance(int $employeeId, string $date): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)->whereDate('date', $date)->first();
    }

    public function test_command_splits_mis_paired_checkout_only_records(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');

        // Simulasikan data lama yang sudah salah (sebelum perbaikan logika).
        Attendance::where('employee_id', $emp->id)->delete();
        Attendance::create([
            'employee_id' => $emp->id,
            'date' => '2026-08-17',
            'time_in' => '16:06:07',
            'time_out' => '07:58:52',
            'status' => 'hadir',
            'method' => 'mesin',
        ]);

        Artisan::call('attendance:fix-checkout');

        $att17 = $this->findAttendance($emp->id, '2026-08-17');
        $att18 = $this->findAttendance($emp->id, '2026-08-18');
        $this->assertNotNull($att17);
        $this->assertNotNull($att18);
        $this->assertNull($att17->time_in);
        $this->assertSame('16:06:07', $att17->time_out);
        $this->assertSame('07:58:52', $att18->time_in);
        $this->assertNull($att18->time_out);
    }

    public function test_command_preserves_manual_non_hadir_records(): void
    {
        $emp = $this->employee('001', '1');

        Attendance::create([
            'employee_id' => $emp->id,
            'date' => '2026-08-20',
            'time_in' => null,
            'time_out' => null,
            'status' => 'izin',
            'method' => 'manual',
        ]);

        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');

        Artisan::call('attendance:fix-checkout');

        $izin = $this->findAttendance($emp->id, '2026-08-20');
        $this->assertNotNull($izin);
        $this->assertSame('izin', $izin->status);

        $att17 = $this->findAttendance($emp->id, '2026-08-17');
        $att18 = $this->findAttendance($emp->id, '2026-08-18');
        $this->assertNull($att17->time_in);
        $this->assertSame('16:06:07', $att17->time_out);
        $this->assertSame('07:58:52', $att18->time_in);
    }

    public function test_dry_run_does_not_write(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');

        Attendance::where('employee_id', $emp->id)->delete();
        Attendance::create([
            'employee_id' => $emp->id,
            'date' => '2026-08-17',
            'time_in' => '16:06:07',
            'time_out' => '07:58:52',
            'status' => 'hadir',
            'method' => 'mesin',
        ]);

        Artisan::call('attendance:fix-checkout', ['--dry-run' => true]);

        $att17 = $this->findAttendance($emp->id, '2026-08-17');
        $this->assertSame('16:06:07', $att17->time_in);
        $this->assertSame('07:58:52', $att17->time_out);
        $this->assertNull($this->findAttendance($emp->id, '2026-08-18'));
    }
}
