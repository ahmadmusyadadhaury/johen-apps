<?php

namespace Tests\Feature;

use App\Livewire\MachineUserSyncTable;
use App\Models\Attendance;
use App\Models\AttendancePunch;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MachineUserSyncTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin']);
    }

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function employee(string $nik, string $nama): Employee
    {
        return Employee::create([
            'nik' => $nik,
            'nama' => $nama,
            'status' => 'aktif',
        ]);
    }

    private function punch(string $machineUserId, string $punchAt): AttendancePunch
    {
        return AttendancePunch::create([
            'machine_user_id' => $machineUserId,
            'punch_at' => $punchAt,
            'method' => 'mesin',
        ]);
    }

    public function test_only_super_admin_can_open_page(): void
    {
        $this->actingAs($this->admin())->get('/hris/sinkron-absen-mesin')->assertOk();
        $this->actingAs($this->staff())->get('/hris/sinkron-absen-mesin')->assertForbidden();
    }

    public function test_machine_user_list_is_displayed_with_tap_stats(): void
    {
        $this->punch('58', '2026-08-11 07:00:00');
        $this->punch('58', '2026-08-11 17:00:00');
        $this->punch('59', '2026-08-11 07:30:00');

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->assertSee('58')
            ->assertSee('59')
            ->assertSee('Belum dipetakan');
    }

    public function test_mapping_saves_device_user_id(): void
    {
        $emp = $this->employee('26030001', 'Karyawan Satu');

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('openMapModal', '58')
            ->set('selectedEmployeeId', $emp->id)
            ->call('saveMapping');

        $this->assertSame('58', $emp->fresh()->device_user_id);
    }

    public function test_mapping_is_rejected_when_machine_user_already_mapped_elsewhere(): void
    {
        $empA = $this->employee('26030001', 'Karyawan Satu');
        $empB = $this->employee('26030002', 'Karyawan Dua');

        $empA->update(['device_user_id' => '58']);

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('openMapModal', '58')
            ->set('selectedEmployeeId', $empB->id)
            ->call('saveMapping')
            ->assertHasErrors('selectedEmployeeId');

        $this->assertNull($empB->fresh()->device_user_id);
    }

    public function test_mapping_is_rejected_when_employee_already_mapped_to_another_user(): void
    {
        $emp = $this->employee('26030001', 'Karyawan Satu');
        $emp->update(['device_user_id' => '59']);

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('openMapModal', '58')
            ->set('selectedEmployeeId', $emp->id)
            ->call('saveMapping')
            ->assertHasErrors('selectedEmployeeId');

        $this->assertSame('59', $emp->fresh()->device_user_id);
    }

    public function test_unmap_clears_device_user_id(): void
    {
        $emp = $this->employee('26030001', 'Karyawan Satu');
        $emp->update(['device_user_id' => '58']);

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('unmapMapping', '58');

        $this->assertNull($emp->fresh()->device_user_id);
    }

    public function test_backfill_links_punches_and_creates_attendances(): void
    {
        $emp = $this->employee('26030001', 'Karyawan Satu');
        $emp->update(['device_user_id' => '58']);

        $p1 = $this->punch('58', '2026-08-11 07:00:00');
        $p2 = $this->punch('58', '2026-08-11 17:00:00');
        $unmatched = $this->punch('999', '2026-08-11 08:00:00');

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('backfill');

        $this->assertSame($emp->id, $p1->fresh()->employee_id);
        $this->assertSame($emp->id, $p2->fresh()->employee_id);
        $this->assertNull($unmatched->fresh()->employee_id);

        $attendance = Attendance::where('employee_id', $emp->id)->where('date', '2026-08-11')->first();
        $this->assertNotNull($attendance);
        $this->assertSame('07:00:00', $attendance->time_in);
        $this->assertSame('17:00:00', $attendance->time_out);
    }

    public function test_backfill_after_late_mapping_fixes_existing_punch(): void
    {
        $emp = $this->employee('26030001', 'Karyawan Satu');
        $punch = $this->punch('58', '2026-08-11 07:00:00');

        Livewire::actingAs($this->admin())
            ->test(MachineUserSyncTable::class)
            ->call('openMapModal', '58')
            ->set('selectedEmployeeId', $emp->id)
            ->call('saveMapping')
            ->call('backfill');

        $this->assertSame($emp->id, $punch->fresh()->employee_id);
        $this->assertNotNull(Attendance::where('employee_id', $emp->id)->first());
    }
}
