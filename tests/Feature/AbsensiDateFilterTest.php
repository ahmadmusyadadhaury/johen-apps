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
}