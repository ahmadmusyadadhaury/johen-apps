<?php

namespace Tests\Feature;

use App\Livewire\EmployeeTable;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class EmployeeJobdeskAutofillTest extends TestCase
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

    public function test_satu_jabatan_terisi_judul_dan_deskripsi(): void
    {
        $pos = $this->createPosition('Staff Gudang', 'Mengelola stok dan barang masuk.');

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$pos->id]);

        $expected = strtoupper($pos->nama).PHP_EOL.'Mengelola stok dan barang masuk.';
        $this->assertSame($expected, $component->get('jobdesk'));
    }

    public function test_multi_jabatan_dengan_main_posisi_utama_paling_atas(): void
    {
        $posA = $this->createPosition('a_petugas', 'Deskripsi A');
        $posB = $this->createPosition('b_driver', 'Deskripsi B');

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$posA->id, $posB->id])
            ->set('main_position_id', (string) $posB->id);

        $expected = strtoupper($posB->nama).PHP_EOL.'Deskripsi B'.PHP_EOL.PHP_EOL.strtoupper($posA->nama).PHP_EOL.'Deskripsi A';
        $this->assertSame($expected, $component->get('jobdesk'));
    }

    public function test_multi_jabatan_tanpa_main_posisi_diurutkan_alfabetis(): void
    {
        $posA = $this->createPosition('b_petugas', 'Deskripsi B');
        $posC = $this->createPosition('a_driver', 'Deskripsi A');

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$posA->id, $posC->id]);

        // Tanpa main: urut alfabetis berdasarkan nama (a_driver dulu, lalu b_petugas).
        $expected = strtoupper($posC->nama).PHP_EOL.'Deskripsi A'.PHP_EOL.PHP_EOL.strtoupper($posA->nama).PHP_EOL.'Deskripsi B';
        $this->assertSame($expected, $component->get('jobdesk'));
    }

    public function test_deskripsi_kosong_tampil_tanda_strip(): void
    {
        $pos = $this->createPosition('Staff Admin', null);

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$pos->id]);

        $expected = strtoupper($pos->nama).PHP_EOL.'-';
        $this->assertSame($expected, $component->get('jobdesk'));
    }

    public function test_tidak_ada_jabatan_yang_dipilih_jobdesk_kosong(): void
    {
        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [])
            ->set('main_position_id', '');

        $this->assertSame('', $component->get('jobdesk'));
    }

    public function test_perubahan_main_posisi_memindahkan_utama_ke_paling_atas(): void
    {
        $posA = $this->createPosition('a_petugas', 'Deskripsi A');
        $posB = $this->createPosition('b_driver', 'Deskripsi B');

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$posA->id, $posB->id])
            ->set('main_position_id', (string) $posA->id);

        $expectedA = strtoupper($posA->nama).PHP_EOL.'Deskripsi A'.PHP_EOL.PHP_EOL.strtoupper($posB->nama).PHP_EOL.'Deskripsi B';
        $this->assertSame($expectedA, $component->get('jobdesk'), 'Sebelum ganti main, A harus di atas.');

        // Ganti main ke B → B pindah ke atas.
        $component->set('main_position_id', (string) $posB->id);
        $expectedB = strtoupper($posB->nama).PHP_EOL.'Deskripsi B'.PHP_EOL.PHP_EOL.strtoupper($posA->nama).PHP_EOL.'Deskripsi A';
        $this->assertSame($expectedB, $component->get('jobdesk'), 'Setelah ganti main, B harus di atas.');
    }

    public function test_menghapus_salah_satu_jabatan_menghilang_dari_jobdesk(): void
    {
        $posA = $this->createPosition('a_petugas', 'Deskripsi A');
        $posB = $this->createPosition('b_driver', 'Deskripsi B');

        $component = Livewire::test(EmployeeTable::class)
            ->set('position_ids', [$posA->id, $posB->id])
            ->set('main_position_id', (string) $posB->id);

        // Hapus A → hanya B yang tersisa.
        $component->set('position_ids', [$posB->id]);
        $expected = strtoupper($posB->nama).PHP_EOL.'Deskripsi B';
        $this->assertSame($expected, $component->get('jobdesk'));
    }

    public function test_edit_modal_menghasilkan_jobdesk_sesuai_jabatan_tersimpan(): void
    {
        $posA = $this->createPosition('a_petugas', 'Deskripsi A');
        $posB = $this->createPosition('b_driver', 'Deskripsi B');

        $employee = Employee::create([
            'nik' => 'AUTO-'.uniqid(),
            'nama' => 'Karyawan Auto',
            'jobdesk' => null,
        ]);
        $employee->positions()->attach([
            $posA->id => ['is_main' => false],
            $posB->id => ['is_main' => true],
        ]);

        $component = Livewire::test(EmployeeTable::class)
            ->call('openEditModal', $employee->id);

        // B adalah main → paling atas, lalu A.
        $expected = strtoupper($posB->nama).PHP_EOL.'Deskripsi B'.PHP_EOL.PHP_EOL.strtoupper($posA->nama).PHP_EOL.'Deskripsi A';
        $this->assertSame($expected, $component->get('jobdesk'));

        $employee->delete();
    }

    private function createPosition(string $nama, ?string $deskripsi): Position
    {
        return Position::create([
            'nama' => $nama.'-'.uniqid(),
            'deskripsi' => $deskripsi,
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Position::where('deskripsi', 'like', 'Deskripsi%')->delete();
        Position::where('deskripsi', 'Mengelola stok%')->delete();
        Position::where('deskripsi', null)->delete();
        Employee::where('nik', 'like', 'AUTO-%')->delete();
        parent::tearDown();
    }
}
