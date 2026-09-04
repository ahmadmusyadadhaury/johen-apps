<?php

namespace Tests\Feature;

use App\Livewire\BirthdayWishTable;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class BirthdayUpcomingTest extends TestCase
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

        // Bersihkan data karyawan uji dari run sebelumnya agar deterministik.
        Employee::where('nik', 'like', 'TST-%')->delete();
    }

    private function makeEmployee(string $nama, ?Carbon $tanggalLahir, string $tipe = 'karyawan_aktif'): Employee
    {
        return Employee::create([
            'nik' => 'TST-'.strtoupper(substr(md5($nama.microtime()), 0, 8)),
            'nama' => $nama,
            'tanggal_lahir' => $tanggalLahir,
            'tipe' => $tipe,
        ]);
    }

    public function test_upcoming_birthdays_lists_only_active_employees_within_next_7_days(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 4, 10, 0, 0));

        $today = Carbon::today();
        $iniHari = $this->makeEmployee('Ultah Hari Ini', $today);
        $h3 = $this->makeEmployee('Ultah H-3', $today->copy()->addDays(3));
        $h7 = $this->makeEmployee('Ultah H-7', $today->copy()->addDays(7));
        $h8 = $this->makeEmployee('Ultah H-8', $today->copy()->addDays(8));
        $nonaktif = $this->makeEmployee('Ultah Nonaktif', $today->copy()->addDays(2), 'mantan_karyawan');

        $component = Livewire::test(BirthdayWishTable::class);
        $upcoming = $component->viewData('upcomingBirthdays');
        $names = $upcoming->pluck('nama')->all();

        $this->assertEqualsCanonicalizing(['Ultah Hari Ini', 'Ultah H-3', 'Ultah H-7'], $names);
        $this->assertNotContains('Ultah H-8', $names);
        $this->assertNotContains('Ultah Nonaktif', $names);

        Carbon::setTestNow();
    }

    public function test_upcoming_birthdays_handles_cross_year_boundary(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 12, 28, 10, 0, 0));
        $today = Carbon::today();

        $tahunBaru = $this->makeEmployee('Ultah 1 Januari', Carbon::create(2000, 1, 1));
        $masihTahunIni = $this->makeEmployee('Ultah 30 Desember', Carbon::create(1990, 12, 30));

        $component = Livewire::test(BirthdayWishTable::class);
        $upcoming = $component->viewData('upcomingBirthdays');
        $names = $upcoming->pluck('nama')->all();

        $this->assertEqualsCanonicalizing(['Ultah 1 Januari', 'Ultah 30 Desember'], $names);

        Carbon::setTestNow();
    }
}
