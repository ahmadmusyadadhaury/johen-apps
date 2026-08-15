<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:fix-malam {--dry-run : Proses tanpa menulis ke database}')]
#[Description('Perbaiki atribusi tanggal absen sesi Malam (pulang dini hari dikembalikan ke tanggal masuk sesi)')]
class AttendanceFixMalam extends Command
{
    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $employees = Employee::where('status', 'aktif')
            ->where('position', 'like', '%(Malam)%')
            ->orderBy('nik')
            ->get();

        if ($employees->isEmpty()) {
            $this->info('Tidak ada karyawan shift malam.');

            return self::SUCCESS;
        }

        $this->info('Ditemukan '.$employees->count().' karyawan shift malam.');

        foreach ($employees as $employee) {
            $summary = $this->fixEmployee($employee, $dryRun);

            $this->info('  '.$employee->nik.' '.$employee->nama
                .' (total '.$employee->attendances()->count().' catatan): '.implode(', ', $summary));
        }

        $this->info($dryRun ? 'Mode dry-run: tidak ada perubahan ditulis.' : 'Selesai. Verifikasi pada menu Presensi > Presensi Karyawan.');

        return self::SUCCESS;
    }

    /**
     * Terapkan perbaikan berulang sampai kondisi stabil (sesi yang dipisah
     * bisa memicu perbaikan beruntun pada hari-hari berikutnya).
     */
    private function fixEmployee(Employee $employee, bool $dryRun): array
    {
        $totals = ['digabung' => 0, 'dihapus' => 0, 'dipisah' => 0];

        do {
            $result = $this->scanOnce($employee, $dryRun);
            $totals['digabung'] += $result['merged'];
            $totals['dihapus'] += $result['deleted'];
            $totals['dipisah'] += $result['transformed'];
            $changed = $result['merged'] + $result['deleted'] + $result['transformed'];
        } while ($changed > 0 && ! $dryRun);

        $parts = [];

        if ($totals['digabung'] > 0) {
            $parts[] = $totals['digabung'].' sesi ditutup';
        }
        if ($totals['dihapus'] > 0) {
            $parts[] = $totals['dihapus'].' rekap dihapus';
        }
        if ($totals['dipisah'] > 0) {
            $parts[] = $totals['dipisah'].' rekap dipisah';
        }

        return $parts ?: ['tidak ada perubahan'];
    }

    /**
     * Satu pass pemindaian catatan presensi karyawan.
     *
     * Aturan 1: rekap dini hari (punch <07:00, tanpa jam keluar) yang merupakan
     *   dobel tap dari jam keluar sesi malam hari sebelumnya (jam keluar lebih
     *   awal daripada jam masuk = sesi lintas malam) dihapus.
     * Aturan 2: sesi malam yang masih terbuka (masuk >=14:00, belum keluar)
     *   ditutup oleh punch dini hari pada tanggal berikutnya. Bila rekap
     *   berikutnya itu gabungan (masuk dini hari + keluar sore), dipisah menjadi
     *   sesi baru yang dimulai pada jam keluar tersebut.
     */
    private function scanOnce(Employee $employee, bool $dryRun): array
    {
        $records = Attendance::where('employee_id', $employee->id)
            ->where('status', 'hadir')
            ->whereNotNull('time_in')
            ->orderBy('date')
            ->get()
            ->values();

        $merged = $deleted = $transformed = 0;
        $skip = [];

        for ($i = 0; $i < $records->count(); $i++) {
            $cur = $records[$i];

            if (in_array($cur->id, $skip, true)) {
                continue;
            }

            if ($this->isEarlyMorning($cur->time_in) && $cur->time_out === null && $i > 0) {
                $prev = $records[$i - 1];

                if ($prev && ! in_array($prev->id, $skip, true)
                    && $this->isNextDay($prev->date, $cur->date)
                    && $this->crossedMidnight($prev)
                    && $this->isWithinMinutes($cur->time_in, $prev->time_out, 30)) {
                    $deleted++;
                    $skip[] = $cur->id;

                    $this->line(sprintf(
                        '    [hapus] %s %s in=%s dobel tap dari keluar sesi %s',
                        $employee->nik,
                        $cur->date->toDateString(),
                        $cur->time_in,
                        $prev->date->toDateString(),
                    ));

                    if (! $dryRun) {
                        $cur->delete();
                    }

                    continue;
                }
            }

            if ($cur->time_out === null && $this->isEveningStart($cur->time_in)) {
                $next = $records[$i + 1] ?? null;

                if ($next && ! in_array($next->id, $skip, true)
                    && $this->isNextDay($cur->date, $next->date)
                    && $this->isEarlyMorning($next->time_in)) {
                    $merged++;
                    $skip[] = $cur->id;

                    $this->line(sprintf(
                        '    [tutup] %s %s out=%s diambil dari %s',
                        $employee->nik,
                        $cur->date->toDateString(),
                        $next->time_in,
                        $next->date->toDateString(),
                    ));

                    if (! $dryRun) {
                        $cur->time_out = $next->time_in;
                        $cur->save();
                    }

                    if ($next->time_out === null) {
                        $deleted++;
                        $skip[] = $next->id;

                        $this->line(sprintf(
                            '    [hapus] %s %s sesi sisa dini hari',
                            $employee->nik,
                            $next->date->toDateString(),
                        ));

                        if (! $dryRun) {
                            $next->delete();
                        }
                    } elseif ($this->isEveningStart($next->time_out)) {
                        $transformed++;
                        $skip[] = $next->id;

                        $this->line(sprintf(
                            '    [pisah] %s %s menjadi sesi baru mulai %s',
                            $employee->nik,
                            $next->date->toDateString(),
                            $next->time_out,
                        ));

                        if (! $dryRun) {
                            $next->time_in = $next->time_out;
                            $next->time_out = null;
                            $next->save();
                        }
                    }
                }
            }
        }

        return compact('merged', 'deleted', 'transformed');
    }

    private function isEarlyMorning(string $time): bool
    {
        return $time < '07:00:00';
    }

    private function isEveningStart(string $time): bool
    {
        return $time >= '14:00:00';
    }

    private function isNextDay($prevDate, $curDate): bool
    {
        return $prevDate->copy()->addDay()->toDateString() === $curDate->toDateString();
    }

    /**
     * Sesi lintas malam: jam keluar (dini hari berikutnya) lebih awal daripada
     * jam masuk (sore/malam hari).
     */
    private function crossedMidnight(Attendance $record): bool
    {
        return $record->time_out !== null && $record->time_out < $record->time_in;
    }

    private function isWithinMinutes(string $timeA, ?string $timeB, int $minutes): bool
    {
        if ($timeB === null) {
            return false;
        }

        $diff = abs(
            strtotime(date('Y-m-d').' '.$timeA)
            - strtotime(date('Y-m-d').' '.$timeB)
        );

        return $diff <= $minutes * 60;
    }
}