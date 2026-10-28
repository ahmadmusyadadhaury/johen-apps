<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatCallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $recipientId,
        public int $senderId,
        public string $senderName,
        public string $type,
        public string $callId,
        public string $mode,
        public array $payload = [],
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.user.' . $this->recipientId)];
    }

    public function broadcastAs(): string
    {
        return 'ChatCallSignal';
    }

    public function broadcastWith(): array
    {
        return [
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'type' => $this->type,
            'call_id' => $this->callId,
            'mode' => $this->mode,
            'payload' => $this->payload,
        ];
    }
}
