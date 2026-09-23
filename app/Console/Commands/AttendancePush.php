<?php

namespace App\Console\Commands;

use App\Models\AttendancePunch;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

#[Signature('attendance:push {--since= : Hanya kirim punch pada/ setelah tanggal (Y-m-d)} {--batch=500 : Jumlah punch per request} {--dry-run : Tampilkan payload tanpa mengirim}')]
#[Description('Kirim log absensi lokal yang belum terkirim ke endpoint cloud (API attendance/push)')]
class AttendancePush extends Command
{
    public function handle(): int
    {
        $config = config('services.attendance_cloud');
        $url = $config['url'] ?? null;
        $token = $config['token'] ?? null;

        if (empty($config['enabled']) || empty($url) || empty($token)) {
            $this->warn('Sync absensi cloud nonaktif (set ATTENDANCE_PUSH_ENABLED=true + URL/token di .env).');

            return self::SUCCESS;
        }

        $batchSize = max(1, (int) $this->option('batch'));
        $dryRun = (bool) $this->option('dry-run');

        $query = AttendancePunch::whereNull('pushed_at')->orderBy('id');

        if ($since = $this->option('since')) {
            $query->where('punch_at', '>=', $since.' 00:00:00');
        }

        $total = $query->count();
        $this->info("Punch belum terkirim: {$total}");

        if ($total === 0) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $query->with('employee:id,name,nik')
                ->take(10)
                ->get()
                ->each(fn (AttendancePunch $p) => $this->line(
                    sprintf('  %s | %s | %s | %s', $p->punch_at?->format('Y-m-d H:i:s'), $p->machine_user_id, $p->employee?->name ?? '(belum termapping)', $p->method)
                ));

            $this->warn('DRY RUN — tidak ada data yang dikirim.');

            return self::SUCCESS;
        }

        $summary = ['new' => 0, 'duplicate' => 0, 'unmatched' => 0, 'failed_batches' => 0];
        $totalSent = 0;

        $query->chunkById($batchSize, function ($punches) use ($url, $token, &$summary, &$totalSent) {
            $payload = $punches
                ->map(fn (AttendancePunch $p) => [
                    'machine_user_id' => $p->machine_user_id,
                    'punch_at' => $p->punch_at?->format('Y-m-d H:i:s'),
                    'method' => $p->method,
                    'machine_serial' => $p->machine_serial,
                ])
                ->values()
                ->all();

            try {
                $response = Http::timeout(60)
                    ->withToken($token)
                    ->post($url, ['punches' => $payload]);

                if (! $response->successful()) {
                    $this->error('Request gagal (HTTP '.$response->status().'): '.$response->body());
                    $summary['failed_batches']++;

                    return false;
                }

                $data = $response->json('processed', []);
                $summary['new'] += $data['new'] ?? 0;
                $summary['duplicate'] += $data['duplicate'] ?? 0;
                $summary['unmatched'] += $data['unmatched'] ?? 0;
                $totalSent += $punches->count();

                AttendancePunch::whereIn('id', $punches->pluck('id'))
                    ->update(['pushed_at' => now()]);
            } catch (Throwable $e) {
                $this->error('Exception saat mengirim batch: '.$e->getMessage());
                $summary['failed_batches']++;

                return false;
            }

            return true;
        });

        $this->table(
            ['Terkirim', 'Baru', 'Duplikat', 'Belum termapping', 'Batch gagal'],
            [[$totalSent, $summary['new'], $summary['duplicate'], $summary['unmatched'], $summary['failed_batches']]]
        );

        if ($summary['unmatched'] > 0) {
            $this->warn('Ada user mesin yang belum dipetakan ke karyawan di sisi cloud. Jalankan `attendance:sync-users` untuk melihat daftarnya.');
        }

        return self::SUCCESS;
    }
}
