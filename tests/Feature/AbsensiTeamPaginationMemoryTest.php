<?php

namespace Tests\Feature;

use App\Livewire\AbsensiTable;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AbsensiTeamPaginationMemoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresi: kolom foto berisi gambar base64 berukuran megabyte per baris.
     * Memuat semua karyawan beserta foto saat pagination menghabiskan memori
     * (Allowed memory size exhausted). Query daftar kini memakai listSelect()
     * yang mengecualikan kolom foto dan memakai flag foto_is_base64.
     */
    public function test_team_pagination_does_not_hydrate_foto_column(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'username' => 'super',
            'email' => 'super@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $this->actingAs($user);

        // 80 karyawan aktif agar tabel terpaginasi (>10 baris per halaman).
        for ($i = 1; $i <= 80; $i++) {
            Employee::create([
                'nik' => str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'nama' => 'Karyawan '.$i,
                'status' => 'aktif',
                'jam_kerja' => Employee::SHIFT_PAGI,
                'jenis_kerja' => Employee::JENIS_KERJA_OPERASIONAL,
            ]);
        }

        $emp = Employee::where('nik', '001')->first();
        Attendance::create(['employee_id' => $emp->id, 'date' => '2026-08-18', 'time_in' => '07:05:00', 'status' => 'hadir']);

        $component = Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim')
            ->set('date', '2026-08-18');

        $component->assertViewHas('employees', function ($employees) {
            return $employees->getCollection()->every(
                fn (Employee $e) => ! array_key_exists('foto', $e->getAttributes())
                    && array_key_exists('foto_is_base64', $e->getAttributes())
            );
        });

        // Pagination penuh melalui siklus update Livewire tetap berhasil.
        $component->call('gotoPage', 2)->assertSuccessful();

        // Karyawan tanpa absensi pada hari kerjanya tetap "tidak hadir".
        $component->assertViewHas('attendances', fn ($attendances) => $attendances->has($emp->id));
    }

    public function test_employee_with_base64_foto_gets_streaming_photo_url_in_team_list(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'username' => 'super2',
            'email' => 'super2@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        $this->actingAs($user);

        Employee::create([
            'nik' => '900',
            'nama' => 'Karyawan Foto Base64',
            'status' => 'aktif',
            'foto' => 'base64:'.base64_encode(str_repeat('x', 100)),
        ]);

        Livewire::test(AbsensiTable::class)
            ->set('tab', 'tim')
            ->assertViewHas('employees', function ($employees) {
                $emp = $employees->firstWhere('nik', '900');

                return $emp !== null
                    && str_contains((string) $emp->foto_url, '/hris/employees/'.$emp->id.'/photo');
            });
    }
}
