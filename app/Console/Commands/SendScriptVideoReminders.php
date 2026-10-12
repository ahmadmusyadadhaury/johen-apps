<?php

namespace App\Console\Commands;

use App\Models\ScriptVideoPushSubscription;
use App\Models\ScriptVideoReminderSend;
use App\Models\ScriptVideoTarget;
use App\Models\ScriptVideoUpload;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class SendScriptVideoReminders extends Command
{
    protected $signature = 'script-video:send-reminders';

    protected $description = 'Kirim pengingat harian upload script video kepada koordinator yang belum mencapai target';

    public function handle(): int
    {
        $publicKey = (string) config('webpush.public_key');
        $privateKey = (string) config('webpush.private_key');

        if ($publicKey === '' || $privateKey === '') {
            $this->warn('Push dilewati: SCRIPT_VIDEO_VAPID_PUBLIC_KEY dan SCRIPT_VIDEO_VAPID_PRIVATE_KEY belum diatur.');
            Log::warning('Pengingat Script Video dilewati karena kunci VAPID belum diatur.');

            return self::SUCCESS;
        }

        $month = ScriptVideoUpload::currentBulanName();
        $year = ScriptVideoUpload::currentTahun();
        $target = ScriptVideoTarget::targetFor($month, $year);
        $today = now()->toDateString();
        $counts = ScriptVideoUpload::forPeriod($month, $year)
            ->selectRaw('user_id, COUNT(*) as jumlah')
            ->groupBy('user_id')
            ->pluck('jumlah', 'user_id');

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => (string) config('webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);
        $sent = 0;

        foreach (User::koordinator()->get() as $coordinator) {
            $uploaded = (int) ($counts[$coordinator->id] ?? 0);
            if ($uploaded >= $target || ScriptVideoReminderSend::where('user_id', $coordinator->id)->whereDate('sent_on', $today)->exists()) {
                continue;
            }

            $subscriptions = ScriptVideoPushSubscription::where('user_id', $coordinator->id)->get();
            if ($subscriptions->isEmpty()) {
                continue;
            }

            $payload = json_encode([
                'title' => 'Deadline Script Video Animator',
                'body' => "{$coordinator->name}, upload script bulan {$month} masih {$uploaded} dari target {$target}.",
                'url' => route('hris.script-video'),
                'tag' => 'script-video-deadline-' . $today,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $delivered = false;

            foreach ($subscriptions as $stored) {
                try {
                    $report = $webPush->sendOneNotification(Subscription::create([
                        'endpoint' => $stored->endpoint,
                        'publicKey' => $stored->public_key,
                        'authToken' => $stored->auth_token,
                    ]), $payload, ['TTL' => 86400]);

                    if ($report->isSuccess()) {
                        $delivered = true;
                    } elseif (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                        $stored->delete();
                    }
                } catch (Throwable $exception) {
                    Log::warning('Pengiriman push Script Video gagal.', [
                        'user_id' => $coordinator->id,
                        'subscription_id' => $stored->id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            }

            if ($delivered) {
                ScriptVideoReminderSend::firstOrCreate(['user_id' => $coordinator->id, 'sent_on' => $today]);
                $sent++;
            }
        }

        $this->info("Pengingat terkirim ke {$sent} koordinator.");

        return self::SUCCESS;
    }
}
