<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItTicketNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $recipientIds,
        public string $title,
        public string $body,
        public string $url,
        public ?int $ticketId = null,
    ) {
        $this->recipientIds = array_values(array_unique(array_map('intval', $recipientIds)));
    }

    public function broadcastOn(): array
    {
        return array_map(
            fn (int $userId) => new PrivateChannel('it-ticket.user.' . $userId),
            $this->recipientIds,
        );
    }

    public function broadcastAs(): string
    {
        return 'ItTicketNotification';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'ticket_id' => $this->ticketId,
        ];
    }
}
