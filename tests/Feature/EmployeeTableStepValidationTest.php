<?php

namespace Tests\Feature;

use App\Livewire\EmployeeTable;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EmployeeTableStepValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'johen_apps']);

        $admin = User::firstOrCreate(
            ['username' => 'testadmin'],
            [
                'name' => 'Test Admin',
                'email' => 'testadmin@johen.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $this->actingAs($admin);
    }

    public function test_next_step_blocks_advance_when_current_step_empty(): void
    {
        $component = Livewire::test(EmployeeTable::class)
            ->set('step', 1)
            ->set('nik_ktp', '')
            ->set('nama', '')
            ->call('nextStep');

        $component->assertHasErrors([
            'nik_ktp' => 'required',
            'nama' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'status_pernikahan' => 'required',
            'ukuran_baju' => 'required',
            'agama' => 'required',
            'pendidikan_terakhir' => 'required',
            'provinsi' => 'required',
            'kota' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos' => 'required',
            'alamat' => 'required',
        ]);

        // Step must not advance
        $component->assertSet('step', 1);
    }

    public function test_step_2_requires_positions_divisions_and_fields(): void
    {
        $component = Livewire::test(EmployeeTable::class)
            ->set('step', 2)
            ->set('nik', '')
            ->set('division_ids', [])
            ->set('position_ids', [])
            ->set('atasan', '')
            ->set('atasan2', '')
            ->set('tanggal_masuk', '')
            ->set('jenis_karyawan', '')
            ->set('lokasi_kerja', '')
            ->set('jenis_kerja', '')
            ->set('jam_kerja', '')
            ->set('jobdesk', '')
            ->call('nextStep');

        $component->assertHasErrors([
            'nik' => 'required',
            'division_ids' => 'required',
            'position_ids' => 'required',
            'main_position_id' => 'required',
            'atasan' => 'required',
            'tanggal_masuk' => 'required',
            'jenis_karyawan' => 'required',
            'lokasi_kerja' => 'required',
            'jenis_kerja' => 'required',
            'jam_kerja' => 'required',
            'jobdesk' => 'required',
        ]);

        $component->assertSet('step', 2);
    }

    public function test_step_2_allows_empty_atasan_2(): void
    {
        $employee = Employee::first();
        $division = Division::first();
        $position = Position::first();

        if (! $employee || ! $division || ! $position) {
            $this->markTestSkipped('Butuh data karyawan/divisi/jabatan untuk uji validasi.');
        }

        $component = Livewire::test(EmployeeTable::class)
            ->set('step', 2)
            ->set('nik', 'NIK-'.uniqid())
            ->set('division_ids', [$division->id])
            ->set('position_ids', [$position->id])
            ->set('main_position_id', $position->id)
            ->set('atasan', $employee->nama)
            ->set('atasan2', '')
            ->set('tanggal_masuk', now()->toDateString())
            ->set('jenis_karyawan', 'tetap')
            ->set('lokasi_kerja', 'Summarecon')
            ->set('jenis_kerja', 'Office')
            ->set('jam_kerja', 'Shift')
            ->set('jobdesk', 'Pekerjaan contoh')
            ->call('nextStep');

        // Atasan 2 boleh kosong: tidak boleh error pada atasan2, langkah tetap maju.
        $component->assertHasNoErrors(['atasan2']);
        $component->assertSet('step', 3);
    }
}
