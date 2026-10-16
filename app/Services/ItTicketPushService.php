<?php

namespace App\Services;

use App\Events\ItTicketNotification;
use App\Models\ItTicket;
use App\Models\ItTicketPushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class ItTicketPushService
{
    public function sendToUsers(iterable $users, string $title, string $body, ?ItTicket $ticket = null): void
    {
        $recipients = collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->values();
        if ($recipients->isEmpty()) {
            return;
        }

        $url = route('it.tickets.index');
        $tag = 'it-ticket-update';
        if ($ticket) {
            $url .= '#ticket-' . $ticket->id;
            $tag .= '-' . $ticket->id;
        }

        try {
            ItTicketNotification::dispatch(
                $recipients->pluck('id')->all(),
                $title,
                $body,
                $url,
                $ticket?->id,
            );
        } catch (Throwable $exception) {
            Log::warning('Broadcast realtime Ticketing gagal.', ['message' => $exception->getMessage()]);
        }

        $publicKey = (string) config('webpush.public_key');
        $privateKey = (string) config('webpush.private_key');
        if ($publicKey === '' || $privateKey === '') {
            return;
        }

        try {
            $webPush = new WebPush(['VAPID' => [
                'subject' => (string) config('webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ]]);
        } catch (Throwable $exception) {
            Log::warning('Inisialisasi push Ticketing gagal.', ['message' => $exception->getMessage()]);
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'tag' => $tag,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach (ItTicketPushSubscription::whereIn('user_id', $recipients->pluck('id'))->get() as $stored) {
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
                Log::warning('Pengiriman push Ticketing gagal.', [
                    'user_id' => $stored->user_id,
                    'subscription_id' => $stored->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
