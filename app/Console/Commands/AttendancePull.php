<?php

namespace App\Console\Commands;

use App\Services\AttendanceSyncService;
use App\Services\ZkMachine\ZkClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('attendance:pull {--since= : Tarik log sejak tanggal (Y-m-d), default hari ini} {--dry-run : Proses tanpa menulis ke database}')]
#[Description('Tarik log absensi dari mesin (fallback/backfill) lalu catat ke database')]
class AttendancePull extends Command
{
    public function handle(AttendanceSyncService $sync): int
    {
        $host = config('services.attendance_machine.host');
        $port = (int) config('services.attendance_machine.port');
        $commKey = (int) config('services.attendance_machine.comm_key');
        $timeout = (int) config('services.attendance_machine.timeout', 5);
        $dryRun = (bool) $this->option('dry-run');

        $since = $this->option('since') ?: now()->toDateString();
        $sinceTs = strtotime($since . ' 00:00:00');

        $this->info("Menghubungkan ke {$host}:{$port} ...");

        $client = new ZkClient($host, $port, $commKey, $timeout);
        if (!$client->connect()) {
            $this->error('Koneksi GAGAL.');

            return self::FAILURE;
        }

        $this->info('Mendownload log absensi...');
        $logs = $client->getAttendanceLogs();
        $client->disconnect();

        $this->info('Log diterima: ' . count($logs) . ' record.');

        $new = 0;
        $dup = 0;
        $unmatched = 0;
        $skipped = 0;

        foreach ($logs as $log) {
            if (strtotime($log['record_time']) < $sinceTs) {
                $skipped++;
                continue;
            }

            $result = $dryRun
                ? ['status' => 'dry-run']
                : $sync->recordPunch($log['user_id'], $log['record_time'], 'mesin');

            match ($result['status']) {
                'ok' => $new++,
                'duplicate' => $dup++,
                'unmatched' => $unmatched++,
                default => null,
            };
        }

        $this->table(
            ['Baru', 'Duplikat', 'Belum termapping', 'Dilewati (sebelum ' . $since . ')'],
            [[$new, $dup, $unmatched, $skipped]]
        );

        if ($unmatched > 0) {
            $this->warn('Ada user mesin yang belum dipetakan ke karyawan. Jalankan `attendance:sync-users` untuk melihat daftarnya.');
        }

        return self::SUCCESS;
    }
}
