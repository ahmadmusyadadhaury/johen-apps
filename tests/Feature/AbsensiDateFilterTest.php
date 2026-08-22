<?php

namespace Tests\Feature;

use App\Livewire\AbsensiTable;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AbsensiDateFilterTest extends TestCase
{
    use RefreshDatabase;

    private function employee(string $nik): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => 'Karyawan '.$nik,
            'status' => 'aktif',
        ]);
    }

    private function superAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'username' => 'super',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
    }

    public function test_date_filter_filters_attendance_for_super_admin_employee_tab(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp14 = $this->employee('001');
        $emp15 = $this->employee('002');

        Attendance::create(['employee_id' => $emp14->id, 'date' => '2026-08-14', 'time_in' => '08:00:00', 'time_out' => '17:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp15->id, 'date' => '2026-08-15', 'time_in' => '08:00:00', 'time_out' => '17:00:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim')
            ->set('date', '2026-08-14');

        $component->assertViewHas('attendances', function ($attendances) use ($emp14, $emp15) {
            return $attendances->has($emp14->id) && ! $attendances->has($emp15->id);
        });
    }

    public function test_date_filter_changes_data_on_livewire_update(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp = $this->employee('001');

        Attendance::create(['employee_id' => $emp->id, 'date' => '2026-08-14', 'time_in' => '08:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp->id, 'date' => '2026-08-15', 'time_in' => '09:30:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim');

        $component->set('date', '2026-08-14');
        $component->assertViewHas('attendances', function ($attendances) use ($emp) {
            $att = $attendances->get($emp->id);

            return $att && $att->time_in === '08:00:00';
        });

        $component->set('date', '2026-08-15');
        $component->assertViewHas('attendances', function ($attendances) use ($emp) {
            $att = $attendances->get($emp->id);

            return $att && $att->time_in === '09:30:00';
        });
    }

    public function test_date_filter_input_is_present_in_super_admin_employee_view(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp = $this->employee('001');
        Attendance::create(['employee_id' => $emp->id, 'date' => '2026-08-14', 'time_in' => '08:00:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim');

        $html = $component->html();

        $this->assertStringContainsString('wire:model.live="date"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*type="date"[^>]*>/', $html);
    }

    public function test_cuti_attendance_shown_in_team_view(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp = $this->employee('001');

        Attendance::create(['employee_id' => $emp->id, 'date' => '2026-08-14', 'status' => 'cuti', 'method' => 'manual']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim')
            ->set('date', '2026-08-14');

        $component->assertViewHas('attendances', function ($attendances) use ($emp) {
            $att = $attendances->get($emp->id);

            return $att && $att->status === 'cuti' && $att->display_status === 'cuti';
        });
    }

    public function test_tab_switch_resets_pagination_page(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim')
            ->set('paginators.page', 3);

        $component->set('tab', 'saya');

        $component->assertSet('paginators.page', 1);
    }

    public function test_query_string_restores_date_and_tab_on_mount(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $component = Livewire::withQueryParams(['date' => '2026-08-14', 'tab' => 'tim'])
            ->test(AbsensiTable::class);

        $component->assertSet('date', '2026-08-14');
        $component->assertSet('tab', 'tim');
    }

    public function test_own_view_shows_only_current_payroll_period(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp = $this->employee('010');
        $user->employee_id = $emp->id;
        $user->save();

        // Periode gaji berjalan: tanggal 26 bulan sebelumnya s.d. tanggal 25
        // bulan ini. Dua record di dalam periode, dua di luar.
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->subMonthNoOverflow()->day(27)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->day(25)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->subMonthNoOverflow()->day(25)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->day(26)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'saya');

        $component->assertViewHas('riwayat', function ($riwayat) use ($emp) {
            return $riwayat->getCollection()->every(fn ($a) => $a->employee_id === $emp->id)
                && $riwayat->total() === 2
                && $riwayat->contains(fn ($a) => $a->date->toDateString() === now()->subMonthNoOverflow()->day(27)->toDateString())
                && $riwayat->contains(fn ($a) => $a->date->toDateString() === now()->day(25)->toDateString());
        });

        $component->assertViewHas('periodeLabel');
    }

    public function test_own_view_period_filter_selects_previous_month_cycle(): void
    {
        $user = $this->superAdmin();
        $this->actingAs($user);

        $emp = $this->employee('011');
        $user->employee_id = $emp->id;
        $user->save();

        // Periode bulan lalu: tgl 26 dua bulan lalu s.d. tgl 25 bulan lalu.
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->subMonthsNoOverflow(2)->day(27)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->subMonthNoOverflow()->day(24)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);
        // Di luar periode bulan lalu (masuk periode berjalan).
        Attendance::create(['employee_id' => $emp->id, 'date' => now()->subMonthNoOverflow()->day(26)->toDateString(), 'time_in' => '19:00:00', 'time_out' => '23:00:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'saya')
            ->set('periode', now()->subMonthNoOverflow()->format('Y-m'));

        $component->assertViewHas('riwayat', function ($riwayat) {
            return $riwayat->total() === 2
                && $riwayat->contains(fn ($a) => $a->date->toDateString() === now()->subMonthsNoOverflow(2)->day(27)->toDateString())
                && $riwayat->contains(fn ($a) => $a->date->toDateString() === now()->subMonthNoOverflow()->day(24)->toDateString());
        });
    }
}