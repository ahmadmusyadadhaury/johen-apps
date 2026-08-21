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

    public function test_overnight_checkout_double_tap_does_not_create_spurious_next_day_record(): void
    {
        $emp = $this->employee('001', '1', position: 'Host Free Fire (Malam)');

        $this->record('1', '2026-08-14 18:59:05');
        $this->record('1', '2026-08-15 01:04:17');
        $this->record('1', '2026-08-15 01:04:25');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-14');
        $this->assertNotNull($att);
        $this->assertSame('18:59:05', $att->time_in);
        $this->assertSame('01:04:17', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-15'));
    }

    public function test_same_day_checkin_double_tap_is_ignored(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-01 08:00:00');
        $this->record('1', '2026-08-01 08:00:02');
        $this->record('1', '2026-08-01 17:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-01');
        $this->assertSame('08:00:00', $att->time_in);
        $this->assertSame('17:00:00', $att->time_out);
    }

    public function test_checkout_only_then_next_day_arrival_is_split(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-17');
        $att2 = $this->attendance($emp->id, '2026-08-18');
        $this->assertNotNull($att1);
        $this->assertNotNull($att2);
        $this->assertNull($att1->time_in);
        $this->assertSame('16:06:07', $att1->time_out);
        $this->assertSame('07:58:52', $att2->time_in);
        $this->assertNull($att2->time_out);
    }

    public function test_far_afternoon_punch_is_checkout_not_checkin(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');
        $this->record('1', '2026-08-18 16:30:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-17');
        $att2 = $this->attendance($emp->id, '2026-08-18');
        $this->assertNull($att1->time_in);
        $this->assertSame('16:06:07', $att1->time_out);
        $this->assertSame('07:58:52', $att2->time_in);
        $this->assertSame('16:30:00', $att2->time_out);
    }

    public function test_overnight_checkout_before_seven_am_still_belongs_to_previous_session(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-17 22:00:00');
        $this->record('1', '2026-08-18 06:30:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-17');
        $this->assertNotNull($att);
        $this->assertSame('22:00:00', $att->time_in);
        $this->assertSame('06:30:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-18'));
    }

    public function test_rebuild_replays_checkout_only_split(): void
    {
        $emp = $this->employee('001', '1');

        $this->record('1', '2026-08-15 07:58:00');
        $this->record('1', '2026-08-15 15:57:49');
        $this->record('1', '2026-08-17 16:06:07');
        $this->record('1', '2026-08-18 07:58:52');

        $rebuilt = app(AttendanceSyncService::class)->rebuildEmployeeAttendance($emp);

        $this->assertSame(4, $rebuilt);
        $this->assertSame(3, Attendance::where('employee_id', $emp->id)->count());
        $att15 = $this->attendance($emp->id, '2026-08-15');
        $att17 = $this->attendance($emp->id, '2026-08-17');
        $att18 = $this->attendance($emp->id, '2026-08-18');
        $this->assertSame('07:58:00', $att15->time_in);
        $this->assertSame('15:57:49', $att15->time_out);
        $this->assertNull($att17->time_in);
        $this->assertSame('16:06:07', $att17->time_out);
        $this->assertSame('07:58:52', $att18->time_in);
        $this->assertNull($att18->time_out);
    }

    public function test_rebuild_replays_malam_sessions_on_checkin_date(): void
    {
        $emp = $this->employee('001', '1', position: 'Host Free Fire (Malam)');

        $this->record('1', '2026-08-14 18:59:05');
        $this->record('1', '2026-08-15 01:04:17');
        $this->record('1', '2026-08-15 01:04:25');
        $this->record('1', '2026-08-15 18:30:00');
        $this->record('1', '2026-08-16 01:10:00');

        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-14');
        $att2 = $this->attendance($emp->id, '2026-08-15');
        $this->assertSame('18:59:05', $att1->time_in);
        $this->assertSame('01:04:17', $att1->time_out);
        $this->assertSame('18:30:00', $att2->time_in);
        $this->assertSame('01:10:00', $att2->time_out);

        $rebuilt = app(AttendanceSyncService::class)->rebuildEmployeeAttendance($emp);

        $this->assertGreaterThan(0, $rebuilt);
        $this->assertSame(2, Attendance::where('employee_id', $emp->id)->count());
        $att1 = $this->attendance($emp->id, '2026-08-14');
        $att2 = $this->attendance($emp->id, '2026-08-15');
        $this->assertSame('18:59:05', $att1->time_in);
        $this->assertSame('01:04:17', $att1->time_out);
        $this->assertSame('18:30:00', $att2->time_in);
        $this->assertSame('01:10:00', $att2->time_out);
    }

    public function test_malam_label_checkout_past_midnight_stays_on_checkin_date(): void
    {
        $emp = $this->employee('001', '1', jamKerja: 'Shift Malam (19.00-24.00)');

        $this->record('1', '2026-08-21 19:05:00');
        $this->record('1', '2026-08-22 03:00:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-21');
        $this->assertNotNull($att);
        $this->assertSame('19:05:00', $att->time_in);
        $this->assertSame('03:00:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-22'));
    }

    public function test_subuh_label_attendance_belongs_to_previous_day(): void
    {
        $emp = $this->employee('001', '1', jamKerja: 'Shift Subuh (01.00-06.00)');

        $this->record('1', '2026-08-22 01:10:00');
        $this->record('1', '2026-08-22 05:50:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-21');
        $this->assertNotNull($att);
        $this->assertSame('01:10:00', $att->time_in);
        $this->assertSame('05:50:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-22'));
    }

    public function test_admin_malam_label_checkout_past_midnight_stays_on_checkin_date(): void
    {
        $emp = $this->employee('001', '1', jamKerja: 'Shift Admin Malam (19.00-06.00)');

        $this->record('1', '2026-08-21 19:10:00');
        $this->record('1', '2026-08-22 06:15:00');

        $this->assertSame(1, Attendance::where('employee_id', $emp->id)->count());
        $att = $this->attendance($emp->id, '2026-08-21');
        $this->assertNotNull($att);
        $this->assertSame('19:10:00', $att->time_in);
        $this->assertSame('06:15:00', $att->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-22'));
    }

    public function test_pagi_label_early_punch_stays_on_same_day(): void
    {
        // Shift Pagi mulai 06.00 tidak boleh dianggap shift Subuh:
        // punch sebelum jam 07.00 tetap tercatat di tanggal yang sama.
        $emp = $this->employee('001', '1', jamKerja: 'Shift Pagi (07.00-12.00)');

        $this->record('1', '2026-08-22 05:30:00');

        $att = $this->attendance($emp->id, '2026-08-22');
        $this->assertNotNull($att);
        $this->assertSame('05:30:00', $att->time_in);
        $this->assertNull($this->attendance($emp->id, '2026-08-21'));
    }

    public function test_missed_checkin_malam_checkout_punch_closes_previous_day(): void
    {
        // Host malam LUPA tap masuk 18-08. Tap pulang 00:58 19-08 harus
        // menjadi jam keluar rekap tanggal 18, bukan absen masuk tanggal 19.
        $emp = $this->employee('001', '1', Employee::SHIFT_MALAM, 'Host Free Fire (Malam)');

        $this->record('1', '2026-08-19 00:58:34');

        $att18 = $this->attendance($emp->id, '2026-08-18');
        $this->assertNotNull($att18);
        $this->assertNull($att18->time_in);
        $this->assertSame('00:58:34', $att18->time_out);
        $this->assertNull($this->attendance($emp->id, '2026-08-19'));
    }

    public function test_missed_checkin_cycle_records_night_session_on_checkin_date(): void
    {
        // Siklus penuh setelah lupa tap masuk: pulang 00:58 (rekap tgl-18),
        // masuk 18:59 tgl-19, pulang 00:34 tgl-20 menutup sesi tgl-19.
        $emp = $this->employee('001', '1', Employee::SHIFT_MALAM, 'Host Free Fire (Malam)');

        $this->record('1', '2026-08-19 00:58:34');
        $this->record('1', '2026-08-19 18:59:33');
        $this->record('1', '2026-08-20 00:34:11');

        $att18 = $this->attendance($emp->id, '2026-08-18');
        $this->assertNotNull($att18);
        $this->assertNull($att18->time_in);
        $this->assertSame('00:58:34', $att18->time_out);

        $att19 = $this->attendance($emp->id, '2026-08-19');
        $this->assertNotNull($att19);
        $this->assertSame('18:59:33', $att19->time_in);
        $this->assertSame('00:34:11', $att19->time_out);

        $this->assertNull($this->attendance($emp->id, '2026-08-20'));
    }

    public function test_missed_checkin_admin_malam_checkout_before_7am(): void
    {
        // Admin shift malam (19.00-06.00) lupa tap masuk; tap pulang 06:49
        // pagi hari berikutnya tetap jam keluar rekap tanggal sebelumnya.
        $emp = $this->employee('001', '1', Employee::SHIFT_ADMIN_MALAM, 'Admin Transaksi Johen Roblox');

        $this->record('1', '2026-08-19 06:49:10');
        $this->record('1', '2026-08-19 18:45:47');
        $this->record('1', '2026-08-20 07:30:42');

        $att18 = $this->attendance($emp->id, '2026-08-18');
        $this->assertNotNull($att18);
        $this->assertNull($att18->time_in);
        $this->assertSame('06:49:10', $att18->time_out);

        // Masuk 18:45 tgl-19 membuka sesi baru; punch 07:30 di luar jendela
        // checkout dini hari menjadi absen masuk tgl-20.
        $att19 = $this->attendance($emp->id, '2026-08-19');
        $this->assertNotNull($att19);
        $this->assertSame('18:45:47', $att19->time_in);
        $this->assertNull($att19->time_out);

        $att20 = $this->attendance($emp->id, '2026-08-20');
        $this->assertNotNull($att20);
        $this->assertSame('07:30:42', $att20->time_in);
    }

    public function test_day_shift_early_punch_is_not_redirected_as_checkout(): void
    {
        // Karyawan shift siang yang tap 06:30 tetap absen masuk hari itu,
        // bukan jam keluar rekap kemarin.
        $emp = $this->employee('001', '1', Employee::SHIFT_SIANG, 'Admin Transaksi Johen');

        $this->record('1', '2026-08-19 06:30:00');

        $this->assertNull($this->attendance($emp->id, '2026-08-18'));
        $att19 = $this->attendance($emp->id, '2026-08-19');
        $this->assertNotNull($att19);
        $this->assertSame('06:30:00', $att19->time_in);
    }
}
