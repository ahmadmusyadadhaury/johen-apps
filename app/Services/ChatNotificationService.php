<?php

namespace App\Services;

use App\Events\ChatMessageNotification;
use App\Models\DirectMessage;
use App\Models\ItTicketPushSubscription;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class ChatNotificationService
{
    public function notify(DirectMessage $message): void
    {
        $message->loadMissing(['sender.employee', 'recipient']);
        $senderName = $message->sender->employee?->nama ?? $message->sender->name;
        $preview = $message->body ?: ($message->attachment_mime && str_starts_with($message->attachment_mime, 'image/')
            ? 'Mengirim foto'
            : 'Mengirim dokumen: '.($message->attachment_name ?? 'lampiran'));
        $url = route('chat', ['user' => $message->sender_id]);

        try {
            ChatMessageNotification::dispatch(
                (int) $message->recipient_id,
                (int) $message->sender_id,
                $senderName,
                $preview,
                (int) $message->id,
                $url,
            );
        } catch (Throwable $exception) {
            Log::warning('Broadcast realtime chat gagal.', ['message' => $exception->getMessage()]);
        }

        $publicKey = (string) config('webpush.public_key');
        $privateKey = (string) config('webpush.private_key');
        if ($publicKey === '' || $privateKey === '') {
            return;
        }

        try {
            $subscriptions = ItTicketPushSubscription::where('user_id', $message->recipient_id)->get();
        } catch (Throwable $exception) {
            Log::warning('Daftar langganan push chat gagal dimuat.', ['message' => $exception->getMessage()]);
            return;
        }
        if ($subscriptions->isEmpty()) {
            return;
        }

        try {
            $webPush = new WebPush(['VAPID' => [
                'subject' => (string) config('webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ]]);
        } catch (Throwable $exception) {
            Log::warning('Inisialisasi push chat gagal.', ['message' => $exception->getMessage()]);
            return;
        }

        $payload = json_encode([
            'title' => 'Pesan baru dari ' . $senderName,
            'body' => mb_strimwidth($preview, 0, 160, '…'),
            'url' => $url,
            'tag' => 'chat-message-' . $message->id,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach ($subscriptions as $stored) {
            try {
                $report = $webPush->sendOneNotification(Subscription::create([
                    'endpoint' => $stored->endpoint,
                    'publicKey' => $stored->public_key,
                    'authToken' => $stored->auth_token,
                ]), $payload, ['TTL' => 86400]);

                if (!$report->isSuccess() && in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                    $stored->delete();
                }
            } catch (Throwable $exception) {
                Log::warning('Pengiriman push chat gagal.', [
                    'user_id' => $message->recipient_id,
                    'subscription_id' => $stored->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
