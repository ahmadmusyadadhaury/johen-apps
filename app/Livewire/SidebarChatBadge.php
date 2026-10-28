<?php

namespace App\Livewire;

use App\Models\DirectMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SidebarChatBadge extends Component
{
    #[On('chat-unread-refresh')]
    public function refreshUnreadCount(): void
    {
        // Re-render with the latest unread count.
    }

    public function render()
    {
        $count = DirectMessage::query()
            ->where('recipient_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return view('livewire.sidebar-chat-badge', compact('count'));
    }
}
