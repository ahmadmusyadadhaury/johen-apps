<?php

namespace App\Console\Commands;

use App\Models\AttendancePunch;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('attendance:push {--since= : Hanya kirim punch pada/ setelah tanggal (Y-m-d)} {--batch=500 : Jumlah punch per request} {--dry-run : Tampilkan payload tanpa mengirim}')]
#[Description('Kirim log absensi lokal yang belum terkirim ke endpoint cloud (API attendance/push)')]
class AttendancePush extends Command
{
    /**
     * Status HTTP yang menandakan masalah konfigurasi/permintaan — mencoba
     * ulang hanya akan menghasilkan error yang sama, jadi hentikan proses.
     */
    private const PERMANENT_FAILURES = [401, 403, 404, 405, 422];

    private const MAX_CONSECUTIVE_FAILURES = 3;

    public function handle(): int
    {
        $config = config('services.attendance_cloud');
        $url = $config['url'] ?? null;
        $token = $config['token'] ?? null;

        if (empty($config['enabled']) || empty($url) || empty($token)) {
            $this->warn('Sync absensi cloud nonaktif (set ATTENDANCE_PUSH_ENABLED=true + URL/token di .env).');

            // Scheduler memanggil command ini tiap menit — batasi log agar tidak membanjiri.
            if (! Cache::has('attendance:push:disabled_logged')) {
                Cache::put('attendance:push:disabled_logged', true, now()->addHour());
                Log::warning('attendance:push dilewati karena konfigurasi cloud belum lengkap.', [
                    'enabled' => (bool) ($config['enabled'] ?? false),
                    'has_url' => ! empty($url),
                    'has_token' => ! empty($token),
                ]);
            }

            return self::SUCCESS;
        }

        Cache::forget('attendance:push:disabled_logged');

        $batchSize = max(1, (int) $this->option('batch'));
        $dryRun = (bool) $this->option('dry-run');

        $query = AttendancePunch::whereNull('pushed_at');

        if ($since = $this->option('since')) {
            $query->where('punch_at', '>=', $since.' 00:00:00');
        }

        $total = $query->count();
        $this->info("Punch belum terkirim: {$total}");

        if ($total === 0) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $query->with('employee:id,nama,nik')
                ->take(10)
                ->get()
                ->each(fn (AttendancePunch $p) => $this->line(
                    sprintf('  %s | %s | %s | %s', $p->punch_at?->format('Y-m-d H:i:s'), $p->machine_user_id, $p->employee?->nama ?? '(belum termapping)', $p->method)
                ));

            $this->warn('DRY RUN — tidak ada data yang dikirim.');

            return self::SUCCESS;
        }

        $summary = ['new' => 0, 'duplicate' => 0, 'unmatched' => 0, 'failed_batches' => 0];
        $totalSent = 0;
        $consecutiveFailures = 0;
        $stopReason = null;

        $query->chunkById($batchSize, function ($punches) use ($url, $token, $batchSize, &$summary, &$totalSent, &$consecutiveFailures, &$stopReason) {
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
                $request = Http::timeout(60)->connectTimeout(15)
                    ->acceptJson()
                    ->withToken($token);

                if (! config('services.attendance_cloud.verify_ssl', true)) {
                    $request = $request->withoutVerifying();
                }

                $response = $request->post($url, ['punches' => $payload]);

                if ($response->successful()) {
                    $data = $response->json('processed', []);
                    $summary['new'] += $data['new'] ?? 0;
                    $summary['duplicate'] += $data['duplicate'] ?? 0;
                    $summary['unmatched'] += $data['unmatched'] ?? 0;
                    $totalSent += $punches->count();
                    $consecutiveFailures = 0;

                    AttendancePunch::whereIn('id', $punches->pluck('id'))
                        ->update(['pushed_at' => now()]);

                    return true;
                }

                $summary['failed_batches']++;
                $consecutiveFailures++;
                $body = str($response->body())->limit(500)->value();
                $message = 'attendance:push batch gagal (HTTP '.$response->status().'): '.$body;
                $this->error($message);
                Log::error($message, [
                    'batch_size' => $punches->count(),
                    'first_punch_id' => $punches->first()?->id,
                    'url' => $url,
                ]);

                if (in_array($response->status(), self::PERMANENT_FAILURES, true)) {
                    $stopReason = 'HTTP '.$response->status().' — periksa URL, token, dan versi deploy di server cloud.';

                    return false;
                }

                if ($consecutiveFailures >= self::MAX_CONSECUTIVE_FAILURES) {
                    $stopReason = $consecutiveFailures.' batch berturut-turut gagal (server tidakhealthy?).';

                    return false;
                }

                return true;
            } catch (ConnectionException|RequestException $e) {
                $summary['failed_batches']++;
                $consecutiveFailures++;
                $this->error('Exception saat mengirim batch: '.$e->getMessage());
                Log::error('attendance:push gagal menghubungi cloud.', [
                    'message' => $e->getMessage(),
                    'url' => $url,
                ]);

                if ($consecutiveFailures >= self::MAX_CONSECUTIVE_FAILURES) {
                    $stopReason = $consecutiveFailures.' batch berturut-turut gagal — '.$e->getMessage();

                    return false;
                }

                return true;
            } catch (Throwable $e) {
                $summary['failed_batches']++;
                $consecutiveFailures++;
                $this->error('Exception saat mengirim batch: '.$e->getMessage());
                Log::error('attendance:push exception tidak terduga.', [
                    'message' => $e->getMessage(),
                    'url' => $url,
                ]);

                return false;
            }
        });

        $this->table(
            ['Terkirim', 'Baru', 'Duplikat', 'Belum termapping', 'Batch gagal'],
            [[$totalSent, $summary['new'], $summary['duplicate'], $summary['unmatched'], $summary['failed_batches']]]
        );

        if ($summary['unmatched'] > 0) {
            $this->warn('Ada user mesin yang belum dipetakan ke karyawan di sisi cloud. Jalankan `attendance:sync-users` untuk melihat daftarnya.');
        }

        if ($stopReason !== null) {
            $this->error('Push dihentikan: '.$stopReason);
            Log::error('attendance:push terhenti sebelum semua punch terkirim.', [
                'reason' => $stopReason,
                'terkirim' => $totalSent,
                'sisa' => max(0, $total - $totalSent),
            ]);

            return self::FAILURE;
        }

        // Exit code non-nol bila ada batch yang gagal, meskipun proses tidak
        // dihentikan. Ini membuat kegagalan tetap terlihat oleh scheduler dan
        // monitoring, alih-alih dilaporkan "sukses" padahal data belum terkirim.
        if ($summary['failed_batches'] > 0) {
            Log::warning('attendance:push selesai dengan sebagian batch gagal.', [
                'terkirim' => $totalSent,
                'batch_gagal' => $summary['failed_batches'],
                'sisa' => max(0, $total - $totalSent),
            ]);

            return self::FAILURE;
        }

        if ($totalSent > 0) {
            Log::info('attendance:push selesai.', [
                'terkirim' => $totalSent,
                'baru' => $summary['new'],
                'duplikat' => $summary['duplicate'],
                'unmatched' => $summary['unmatched'],
            ]);
        }

        return self::SUCCESS;
    }
}
