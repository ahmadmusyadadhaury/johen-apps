<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JenisKerjaWorkdayTest extends TestCase
{
    use RefreshDatabase;

    private function employee(?string $jenisKerja): Employee
    {
        return Employee::create([
            'nik' => 'JK001',
            'nama' => 'Karyawan Jenis Kerja',
            'status' => 'aktif',
            'jenis_kerja' => $jenisKerja,
        ]);
    }

    public function test_office_employee_is_off_on_sundays_only(): void
    {
        $emp = $this->employee('Office');

        // 23 Agustus 2026 = Minggu, 22 Agustus 2026 = Sabtu.
        $this->assertTrue($emp->isWeeklyDayOff(\Carbon\Carbon::parse('2026-08-23')));
        $this->assertFalse($emp->isWeeklyDayOff(\Carbon\Carbon::parse('2026-08-22')));
        $this->assertTrue($emp->isWorkday(\Carbon\Carbon::parse('2026-08-22')));
        $this->assertFalse($emp->isWorkday(\Carbon\Carbon::parse('2026-08-23')));
    }

    public function test_operasional_employee_has_no_weekly_day_off(): void
    {
        $emp = $this->employee('Operasional');

        $this->assertFalse($emp->isWeeklyDayOff(\Carbon\Carbon::parse('2026-08-23'))); // Minggu
        $this->assertTrue($emp->isWorkday(\Carbon\Carbon::parse('2026-08-23')));
    }

    public function test_without_jenis_kerja_there_is_no_weekly_day_off(): void
    {
        $emp = $this->employee(null);

        $this->assertFalse($emp->isWeeklyDayOff(\Carbon\Carbon::parse('2026-08-23')));
    }
}
