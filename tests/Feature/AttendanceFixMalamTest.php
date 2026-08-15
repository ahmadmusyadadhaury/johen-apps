<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AttendanceFixMalamTest extends TestCase
{
    use RefreshDatabase;

    private function employee(string $nik, ?string $position = null): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => 'Karyawan '.$nik,
            'status' => 'aktif',
            'position' => $position,
        ]);
    }

    private function attendance(int $employeeId, string $date, ?string $in, ?string $out): void
    {
        Attendance::create([
            'employee_id' => $employeeId,
            'date' => $date,
            'time_in' => $in,
            'time_out' => $out,
            'status' => 'hadir',
        ]);
    }

    private function findAttendance(int $employeeId, string $date): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)->whereDate('date', $date)->first();
    }

    public function test_open_evening_session_is_closed_by_next_day_early_morning_checkout(): void
    {
        $emp = $this->employee('001', 'Host Monkey PUBG (Malam)');

        // Sesi lama hasil kode sebelumnya: masuk malam tanpa jam keluar,
        // lalu rekap gabungan (pulang dini hari + masuk sore) keesokan harinya.
        $this->attendance($emp->id, '2026-08-13', '22:55:05', null);
        $this->attendance($emp->id, '2026-08-14', '06:28:24', '18:48:04');
        $this->attendance($emp->id, '2026-08-15', '06:25:55', null);

        Artisan::call('attendance:fix-malam');

        $att13 = $this->findAttendance($emp->id, '2026-08-13');
        $att14 = $this->findAttendance($emp->id, '2026-08-14');

        $this->assertNotNull($att13);
        $this->assertSame('22:55:05', $att13->time_in);
        $this->assertSame('06:28:24', $att13->time_out);

        $this->assertNotNull($att14);
        $this->assertSame('18:48:04', $att14->time_in);
        $this->assertSame('06:25:55', $att14->time_out);

        $this->assertNull($this->findAttendance($emp->id, '2026-08-15'));
    }

    public function test_spurious_next_day_double_tap_record_is_deleted(): void
    {
        $emp = $this->employee('001', 'Koordinator Valorant & Host MLBB (Malam)');

        $this->attendance($emp->id, '2026-08-14', '13:41:18', '00:44:52');
        $this->attendance($emp->id, '2026-08-15', '00:44:55', null);

        Artisan::call('attendance:fix-malam');

        $att14 = $this->findAttendance($emp->id, '2026-08-14');
        $this->assertNotNull($att14);
        $this->assertSame('13:41:18', $att14->time_in);
        $this->assertSame('00:44:52', $att14->time_out);
        $this->assertNull($this->findAttendance($emp->id, '2026-08-15'));
    }

    public function test_combined_record_is_split_into_new_evening_session(): void
    {
        $emp = $this->employee('001', 'Host E-football (Malam)');

        $this->attendance($emp->id, '2026-08-13', '22:55:07', null);
        $this->attendance($emp->id, '2026-08-14', '06:28:30', '18:47:52');
        $this->attendance($emp->id, '2026-08-15', '01:12:30', '07:20:53');

        Artisan::call('attendance:fix-malam');

        $att13 = $this->findAttendance($emp->id, '2026-08-13');
        $att14 = $this->findAttendance($emp->id, '2026-08-14');

        $this->assertSame('22:55:07', $att13->time_in);
        $this->assertSame('06:28:30', $att13->time_out);
        $this->assertSame('18:47:52', $att14->time_in);
        $this->assertSame('01:12:30', $att14->time_out);
    }

    public function test_non_malam_employee_is_untouched(): void
    {
        $emp = $this->employee('001', null);

        $this->attendance($emp->id, '2026-08-13', '22:55:07', null);
        $this->attendance($emp->id, '2026-08-14', '06:28:30', '18:47:52');

        Artisan::call('attendance:fix-malam');

        $att13 = $this->findAttendance($emp->id, '2026-08-13');
        $att14 = $this->findAttendance($emp->id, '2026-08-14');

        $this->assertSame('22:55:07', $att13->time_in);
        $this->assertNull($att13->time_out);
        $this->assertSame('06:28:30', $att14->time_in);
        $this->assertSame('18:47:52', $att14->time_out);
    }

    public function test_dry_run_does_not_write(): void
    {
        $emp = $this->employee('001', 'Host Monkey PUBG (Malam)');

        $this->attendance($emp->id, '2026-08-13', '22:55:05', null);
        $this->attendance($emp->id, '2026-08-14', '06:28:24', '18:48:04');

        Artisan::call('attendance:fix-malam', ['--dry-run' => true]);

        $att13 = $this->findAttendance($emp->id, '2026-08-13');
        $att14 = $this->findAttendance($emp->id, '2026-08-14');

        $this->assertSame('22:55:05', $att13->time_in);
        $this->assertNull($att13->time_out);
        $this->assertSame('06:28:24', $att14->time_in);
        $this->assertSame('18:48:04', $att14->time_out);
    }
}