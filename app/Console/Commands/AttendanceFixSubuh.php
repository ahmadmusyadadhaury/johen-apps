<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\AttendanceSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:fix-subuh {--dry-run : Proses tanpa menulis ke database}')]
#[Description('Perbaiki atribusi tanggal absen sesi Subuh (punch 00:00-06:59 dipindah ke hari sebelumnya)')]
class AttendanceFixSubuh extends Command
{
    public function handle(AttendanceSyncService $sync): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $employees = Employee::where('position', 'like', '%(Subuh)%')
            ->where('tipe', 'karyawan_aktif')
            ->orderBy('nik')
            ->get();

        if ($employees->isEmpty()) {
            $this->info('Tidak ada karyawan shift Subuh.');

            return self::SUCCESS;
        }

        $this->info('Ditemukan '.$employees->count().' karyawan shift Subuh.');
        $rows = [];

        foreach ($employees as $employee) {
            $before = $employee->attendances()->count();

            if ($dryRun) {
                $rows[] = [$employee->nik, $employee->nama, $before, 'skipped (dry-run)'];

                continue;
            }

            $punches = $sync->rebuildEmployeeAttendance($employee);
            $after = $employee->attendances()->count();
            $rows[] = [$employee->nik, $employee->nama, $before.' -> '.$after, $punches.' punch'];

            $this->info("  {$employee->nik} {$employee->nama}: {$before} -> {$after} catatan dari {$punches} punch.");
        }

        $this->table(['NIK', 'Nama', 'Catatan Absen', 'Proses'], $rows);
        $this->info('Selesai. Verifikasi pada menu Presensi > Presensi Karyawan.');

        return self::SUCCESS;
    }
}
