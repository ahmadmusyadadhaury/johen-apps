<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceOvernightTest extends TestCase
{
    use RefreshDatabase;

    private function employee(string $nik, string $userId, ?string $jamKerja = null, ?string $position = null): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => 'Karyawan '.$nik,
            'status' => 'aktif',
            'device_user_id' => $userId,
            'jam_kerja' => $jamKerja,
            'position' => $position,
        ]);
    }

    private function record(string $userId, string $punchAt): array
    {
        return app(AttendanceSyncService::class)->recordPunch($userId, $punchAt, 'mesin');
    }

    private function attendance(int $employeeId, string $date): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)->whereDate('date', $date)->first();
    }

    public function test_checkin_and_checkout_on_same_day(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 08:00:00');
        $this->record('1', '2026-08-01 17:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertNotNull($att);
        $this->assertSame('08:00:00', $att->time_in);
        $this->assertSame('17:00:00', $att->time_out);
    }

    public function test_acceptance_overnight_checkout_belongs_to_time_in_date(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('1', '2026-08-02 02:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertNotNull($att);
        $this->assertSame('22:00:00', $att->time_in);
        $this->assertSame('02:00:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-02'));
    }

    public function test_evening_shift_checkout_after_midnight(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 20:00:00');
        $this->record('1', '2026-08-02 01:30:00');

        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertNotNull($att);
        $this->assertSame('20:00:00', $att->time_in);
        $this->assertSame('01:30:00', $att->time_out);
        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
    }

    public function test_checkin_without_checkout_leaves_time_out_null(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 08:00:00');

        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertNotNull($att);
        $this->assertSame('08:00:00', $att->time_in);
        $this->assertNull($att->time_out);
    }

    public function test_next_day_arrival_after_checkout_is_new_record(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 08:00:00');
        $this->record('1', '2026-08-01 17:00:00');
        $this->record('1', '2026-08-02 08:00:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-01');
        $att2 = $this->attendance($emp->id, '2026-08-02');
        $this->assertSame('08:00:00', $att1->time_in);
        $this->assertSame('17:00:00', $att1->time_out);
        $this->assertSame('08:00:00', $att2->time_in);
        $this->assertNull($att2->time_out);
    }

    public function test_two_consecutive_night_shifts(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('1', '2026-08-02 02:00:00');
        $this->record('1', '2026-08-02 22:00:00');
        $this->record('1', '2026-08-03 02:00:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-01');
        $att2 = $this->attendance($emp->id, '2026-08-02');
        $this->assertSame('22:00:00', $att1->time_in);
        $this->assertSame('02:00:00', $att1->time_out);
        $this->assertSame('22:00:00', $att2->time_in);
        $this->assertSame('02:00:00', $att2->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-03'));
    }

    public function test_multiple_employees_scanning_at_similar_times(): void
    {
        $a = $this->employee('001', '1');
        $b = $this->employee('002', '2');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('2', '2026-08-01 22:01:00');
        $this->record('1', '2026-08-02 02:00:00');
        $this->record('2', '2026-08-02 02:01:00');

        $attA = $this->attendance($a->id, '2026-08-01');
        $attB = $this->attendance($b->id, '2026-08-01');
        $this->assertNotNull($attA);
        $this->assertNotNull($attB);
        $this->assertSame('22:00:00', $attA->time_in);
        $this->assertSame('02:00:00', $attA->time_out);
        $this->assertSame('22:01:00', $attB->time_in);
        $this->assertSame('02:01:00', $attB->time_out);
    }

    public function test_attendance_never_cross_pairs_between_employees(): void
    {
        $a = $this->employee('001', '1');
        $b = $this->employee('002', '2');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('2', '2026-08-01 22:01:00');
        $this->record('2', '2026-08-02 02:01:00');
        $this->record('1', '2026-08-02 02:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $a->id)->count());
        $this->assertSame(1, Attendance::where('employee_id', $b->id)->count());
        $this->assertSame('22:00:00', $this->attendance($a->id, '2026-08-01')->time_in);
        $this->assertSame('02:00:00', $this->attendance($a->id, '2026-08-01')->time_out);
        $this->assertSame('22:01:00', $this->attendance($b->id, '2026-08-01')->time_in);
        $this->assertSame('02:01:00', $this->attendance($b->id, '2026-08-01')->time_out);
    }

    public function test_genuine_next_day_arrival_is_not_checkout_of_open_previous_session(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 08:00:00');
        $this->record('1', '2026-08-02 08:00:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-01');
        $att2 = $this->attendance($emp->id, '2026-08-02');
        $this->assertSame('08:00:00', $att1->time_in);
        $this->assertNull($att1->time_out);
        $this->assertSame('08:00:00', $att2->time_in);
    }

    public function test_shift_config_window_closes_night_session_within_shift_end_plus_buffer(): void
    {
        $emp = $this->employee('001', '1', jamKerja: '22:00-06:00');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('1', '2026-08-02 05:30:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertSame('22:00:00', $att->time_in);
        $this->assertSame('05:30:00', $att->time_out);
    }

    public function test_shift_config_window_treats_far_morning_scan_as_new_arrival(): void
    {
        $emp = $this->employee('001', '1', jamKerja: '22:00-06:00');

        $this->record('1', '2026-08-01 22:00:00');
        $this->record('1', '2026-08-02 08:30:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-01');
        $att2 = $this->attendance($emp->id, '2026-08-02');
        $this->assertSame('22:00:00', $att1->time_in);
        $this->assertNull($att1->time_out);
        $this->assertSame('08:30:00', $att2->time_in);
    }

    public function test_subuh_shift_attendance_belongs_to_previous_day(): void
    {
        $emp = $this->employee('001', '1', position: 'Host Johen PUBG (Subuh)');

        $this->record('1', '2026-08-15 00:24:00');
        $this->record('1', '2026-08-15 06:42:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-14');
        $this->assertNotNull($att);
        $this->assertSame('00:24:00', $att->time_in);
        $this->assertSame('06:42:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-15'));
    }

    public function test_subuh_shift_consecutive_days(): void
    {
        $emp = $this->employee('001', '1', position: 'Host Johen PUBG (Subuh)');

        $this->record('1', '2026-08-14 00:31:00');
        $this->record('1', '2026-08-14 06:25:00');
        $this->record('1', '2026-08-15 00:24:00');
        $this->record('1', '2026-08-15 06:42:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-13');
        $att2 = $this->attendance($emp->id, '2026-08-14');
        $this->assertSame('00:31:00', $att1->time_in);
        $this->assertSame('06:25:00', $att1->time_out);
        $this->assertSame('00:24:00', $att2->time_in);
        $this->assertSame('06:42:00', $att2->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-15'));
    }

    public function test_subuh_shift_employee_with_jam_kerja_also_shifts_to_previous_day(): void
    {
        $emp = $this->employee('001', '1', jamKerja: '01:00-06:00');

        $this->record('1', '2026-08-15 01:10:00');
        $this->record('1', '2026-08-15 05:50:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-14');
        $this->assertNotNull($att);
        $this->assertSame('01:10:00', $att->time_in);
        $this->assertSame('05:50:00', $att->time_out);
    }

    public function test_regular_employee_early_morning_punch_stays_on_same_day(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-15 05:00:00');

        $att = $this->attendance($emp->id, '2026-08-15');
        $this->assertNotNull($att);
        $this->assertSame('05:00:00', $att->time_in);
        $this->assertNull($this->attendance($emp->id, '2026-08-14'));
    }

    public function test_malam_position_early_morning_checkout_still_belongs_to_previous_session(): void
    {
        $emp = $this->employee('001', '1', position: 'Admin Johen PUBG (Malam)');

        $this->record('1', '2026-08-14 22:00:00');
        $this->record('1', '2026-08-15 02:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-14');
        $this->assertNotNull($att);
        $this->assertSame('22:00:00', $att->time_in);
        $this->assertSame('02:00:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-15'));
    }
}
