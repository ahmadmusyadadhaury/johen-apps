<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $recipientId,
        public int $senderId,
        public string $senderName,
        public string $body,
        public int $messageId,
        public string $url,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.user.' . $this->recipientId)];
    }

    public function broadcastAs(): string
    {
        return 'ChatMessageNotification';
    }

    public function broadcastWith(): array
    {
        return [
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'body' => $this->body,
            'message_id' => $this->messageId,
            'url' => $this->url,
        ];
    }
}
