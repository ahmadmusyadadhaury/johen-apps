<?php

namespace App\Console\Commands;

use App\Models\AttendancePunch;
use App\Services\AttendanceSyncService;
use App\Services\ZkMachine\ZkClient;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;
use Throwable;

#[Signature('attendance:listen {--once : Jalankan satu siklus koneksi lalu keluar} {--max-events=0 : Berhenti setelah N event (0 = tanpa batas)} {--dry-run : Proses tanpa menulis ke database} {--since= : Tarik log tertinggal sejak tanggal (Y-m-d), default tanggal punch terakhir di DB}')]
#[Description('Daemon realtime: menerima event tap dari mesin absensi dan mencatat ke database')]
class AttendanceListen extends Command
{
    public function handle(AttendanceSyncService $sync): int
    {
        $host = config('services.attendance_machine.host');
        $port = (int) config('services.attendance_machine.port');
        $commKey = (int) config('services.attendance_machine.comm_key');
        $timeout = (int) config('services.attendance_machine.timeout', 5);
        $maxEvents = (int) $this->option('max-events');
        $dryRun = (bool) $this->option('dry-run');

        $events = 0;
        $startedAt = now();
        $lastCatchUpAt = null;
        $catchUpEvery = 5;

        $this->info("Daemon absensi dimulai ({$host}:{$port}, comm key {$commKey}). Ctrl+C untuk berhenti.");

        while (true) {
            try {
                $client = new ZkClient($host, $port, $commKey, $timeout);

                if (!$client->connect()) {
                    $this->warn('[' . now()->format('H:i:s') . "] Koneksi gagal, coba lagi dalam 5 detik...");
                    sleep(5);
                    continue;
                }

                $this->maybeCatchUp($sync, $client, $lastCatchUpAt, $catchUpEvery, $dryRun);

                if (!$client->enableRealtime()) {
                    $this->warn('[' . now()->format('H:i:s') . "] Registrasi realtime gagal, reconnect...");
                    $client->disconnect();
                    sleep(5);
                    continue;
                }

                $this->info('[' . now()->format('H:i:s') . '] Terkoneksi. Menunggu event tap...');

                while (true) {
                    $this->maybeCatchUp($sync, $client, $lastCatchUpAt, $catchUpEvery, $dryRun);

                    $event = $client->readRealtimeEvent(5);

                    if ($event === null) {
                        if (!$client->isConnected()) {
                            throw new RuntimeException('Koneksi terputus');
                        }
                        continue;
                    }

                    $events++;
                    $this->processEvent($sync, $event, $dryRun, $client);

                    if ($maxEvents > 0 && $events >= $maxEvents) {
                        $client->disconnect();
                        $this->info("Selesai: {$events} event diproses.");

                        return self::SUCCESS;
                    }
                }
            } catch (Throwable $e) {
                $this->error('[' . now()->format('H:i:s') . '] Error: ' . $e->getMessage());
                sleep(5);
            }

            if ($this->option('once')) {
                $this->info("Selesai ({$events} event).");
                break;
            }
        }

        return self::SUCCESS;
    }

    private function maybeCatchUp(AttendanceSyncService $sync, ZkClient $client, ?Carbon &$lastCatchUpAt, int $every, bool $dryRun): void
    {
        if ($lastCatchUpAt !== null && $lastCatchUpAt->diffInSeconds(now()) < $every) {
            return;
        }

        $lastCatchUpAt = now();
        $this->catchUp($sync, $client, $this->option('since'), $dryRun);
    }

    private function catchUp(AttendanceSyncService $sync, ZkClient $client, ?string $since, bool $dryRun): void
    {
        $last = AttendancePunch::max('punch_at');
        $sinceTs = $since
            ? strtotime($since . ' 00:00:00')
            : strtotime(($last ? substr($last, 0, 10) : now()->toDateString()) . ' 00:00:00');

        $logs = $client->getAttendanceLogs();
        if ($logs === []) {
            return;
        }

        // Pairing presensi bergantung pada urutan kronologis (punch pulang dini
        // hari dipasangkan ke sesi masuk sebelumnya), jadi proses log dari yang
        // paling lama ke yang paling baru.
        usort($logs, fn ($a, $b) => strcmp($a['record_time'], $b['record_time']));

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

        if ($new > 0 || $dup > 0 || $unmatched > 0) {
            $this->line(sprintf(
                '[%s] Catch-up: %d baru, %d duplikat, %d belum termapping, %d dilewati.',
                now()->format('H:i:s'),
                $new,
                $dup,
                $unmatched,
                $skipped
            ));
        }

        if ($unmatched > 0) {
            $this->warn('Ada user mesin yang belum dipetakan ke karyawan. Jalankan `attendance:sync-users` untuk melihat daftarnya.');
        }
    }

    private function processEvent(AttendanceSyncService $sync, array $event, bool $dryRun, ZkClient $client): void
    {
        $result = $dryRun
            ? ['status' => 'dry-run', 'machine_user_id' => $event['user_id']]
            : $sync->recordPunch($event['user_id'], $event['record_time'], 'mesin');

        $label = match ($result['status']) {
            'ok' => "OK",
            'duplicate' => "DUPLIKAT",
            'unmatched' => "TIDAK TERMAPPING",
            'dry-run' => "DRY-RUN",
            default => $result['status'],
        };

        $this->line(sprintf(
            '[%s] %s | user=%s | jam=%s | state=%s',
            now()->format('H:i:s'),
            str_pad($label, 14),
            $event['user_id'],
            $event['record_time'],
            $event['state']
        ));
    }
}
