<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceSyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('attendance:fix-checkout {--dry-run : Proses tanpa menulis ke database} {--employee= : NIK karyawan tertentu (opsional)}')]
#[Description('Perbaiki absen yang hanya tap pulang: rekap "absen pulang saja" dan kedatangan hari berikutnya dipisah ke tanggalnya masing-masing')]
class AttendanceFixCheckout extends Command
{
    public function handle(AttendanceSyncService $sync): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $employeeFilter = $this->option('employee');

        $query = Employee::query()->where('status', 'aktif')->orderBy('nik')->listSelect();
        if ($employeeFilter) {
            $query->where('nik', $employeeFilter);
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            $this->error($employeeFilter
                ? "Karyawan dengan NIK {$employeeFilter} tidak ditemukan."
                : 'Tidak ada karyawan aktif.');

            return self::FAILURE;
        }

        $affected = $employees->filter(fn (Employee $employee) => $sync->hasCheckoutOnlyMisPairing($employee));

        if ($affected->isEmpty()) {
            $this->info('Tidak ada karyawan dengan data "tap pulang saja" yang perlu diperbaiki.');

            return self::SUCCESS;
        }

        $this->info('Ditemukan '.$affected->count().' dari '.$employees->count()
            .' karyawan dengan data "tap pulang saja"'
            .($dryRun ? ' (mode dry-run, tidak ada perubahan ditulis).' : '.'));

        $changed = 0;

        DB::beginTransaction();

        try {
            foreach ($affected as $employee) {
                $before = $this->snapshot($employee);
                $totalPunch = $sync->rebuildEmployeeAttendance($employee, preserveManual: true);
                $after = $this->snapshot($employee);

                $diff = $this->diff($before, $after);

                if (empty($diff)) {
                    continue;
                }

                $changed++;

                $this->line('  '.$employee->nik.' '.$employee->nama
                    .' ('.$before->count().' -> '.$after->count().' catatan, dari '.$totalPunch.' punch):');

                foreach ($diff as $row) {
                    $this->line('    '.$row);
                }
            }
        } finally {
            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        }

        if ($dryRun) {
            $this->info('Dry-run selesai: '.$changed.' karyawan akan berubah. Jalankan tanpa --dry-run untuk menerapkan.');
        } else {
            $this->info('Selesai. '.$changed.' karyawan diperbarui. Verifikasi pada menu Presensi > Presensi Karyawan.');
        }

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Support\Collection<string, string>
     */
    private function snapshot(Employee $employee): \Illuminate\Support\Collection
    {
        return Attendance::where('employee_id', $employee->id)
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($a) => [
                $a->date->toDateString() => sprintf(
                    '%s|%s|%s',
                    $a->time_in ?? '-',
                    $a->time_out ?? '-',
                    $a->status,
                ),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private function diff(\Illuminate\Support\Collection $before, \Illuminate\Support\Collection $after): array
    {
        $rows = [];
        $dates = collect($before->keys())->merge($after->keys())->unique()->sort();

        foreach ($dates as $date) {
            $old = $before->get($date);
            $new = $after->get($date);

            if ($old === $new) {
                continue;
            }

            // Abaikan perubahan sepele (selisih beberapa detik dari dobel tap).
            if ($this->isTrivialChange($old, $new)) {
                continue;
            }

            $fmt = function (?string $v): string {
                if ($v === null) {
                    return '-';
                }
                [$in, $out, $status] = explode('|', $v);

                return $in.' -> '.$out.' ('.$status.')';
            };

            $rows[] = sprintf('[%s] %s => %s', $date, $fmt($old), $fmt($new));
        }

        return $rows;
    }

    private function isTrivialChange(?string $old, ?string $new): bool
    {
        if ($old === null || $new === null) {
            return false;
        }

        [$oldIn, $oldOut, $oldStatus] = explode('|', $old);
        [$newIn, $newOut, $newStatus] = explode('|', $new);

        if ($oldStatus !== $newStatus) {
            return false;
        }

        if (in_array('-', [$oldIn, $newIn, $oldOut, $newOut], true)) {
            return false;
        }

        $minute = function (string $t): int {
            $parts = explode(':', $t);

            return ((int) ($parts[0] ?? 0) * 60) + (int) ($parts[1] ?? 0);
        };

        return abs($minute($oldIn) - $minute($newIn)) <= 1
            && abs($minute($oldOut) - $minute($newOut)) <= 1;
    }
}