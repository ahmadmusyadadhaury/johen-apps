<?php

namespace App\Livewire;

use App\Models\DirectMessage;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWorkspace extends Component
{
    use WithFileUploads;

    public ?int $selectedUserId = null;
    public string $search = '';
    public string $message = '';
    public $attachment = null;

    protected $queryString = ['selectedUserId' => ['as' => 'user']];

    public function mount(): void
    {
        $this->touchPresence();
        $requestedUserId = request()->integer('user');
        if ($requestedUserId && $this->contacts()->contains('id', $requestedUserId)) {
            $this->selectedUserId = $requestedUserId;
            $this->markConversationRead();
        }
    }

    public function selectUser(int $userId): void
    {
        abort_unless($this->contacts()->contains('id', $userId), 404);
        $this->selectedUserId = $userId;
        $this->markConversationRead();
    }

    public function sendMessage(): void
    {
        $validated = $this->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,zip'],
            'selectedUserId' => ['required', 'integer', 'exists:users,id'],
        ]);

        abort_unless($this->contacts()->contains('id', (int) $validated['selectedUserId']), 404);

        $body = trim((string) ($validated['message'] ?? ''));
        if ($body === '' && !$this->attachment) {
            $this->addError('message', 'Pesan tidak boleh kosong.');
            return;
        }

        $attachmentData = [];
        if ($this->attachment) {
            // Simpan metadata sebelum store(): disk temporary dan disk local
            // sama, sehingga Livewire memindahkan (bukan menyalin) file tmp.
            $attachmentName = $this->attachment->getClientOriginalName();
            $attachmentMime = $this->attachment->getMimeType() ?: 'application/octet-stream';
            $attachmentSize = $this->attachment->getSize();
            $attachmentPath = $this->attachment->store('chat-attachments/'.auth()->id(), 'local');

            $attachmentData = [
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'attachment_mime' => $attachmentMime,
                'attachment_size' => $attachmentSize,
            ];
        }

        $directMessage = DirectMessage::create([
            'sender_id' => auth()->id(),
            'recipient_id' => (int) $validated['selectedUserId'],
            'body' => $body,
            ...$attachmentData,
        ]);

        $this->reset('message', 'attachment');
        $this->resetValidation();
        $this->touchPresence();
        $this->dispatch('chat-message-sent');
        app(\App\Services\ChatNotificationService::class)->notify($directMessage);
    }

    public function deleteMessage(int $messageId): void
    {
        $directMessage = DirectMessage::query()
            ->whereKey($messageId)
            ->where('sender_id', auth()->id())
            ->where('recipient_id', $this->selectedUserId)
            ->firstOrFail();

        $attachmentPath = $directMessage->attachment_path;
        $directMessage->delete();

        if ($attachmentPath) {
            Storage::disk('local')->delete($attachmentPath);
        }

        $this->dispatch('chat-message-sent');
        $this->dispatch('chat-unread-refresh');
    }

    public function refreshChat(): void
    {
        $this->touchPresence();
        $this->markConversationRead();
    }

    #[On('chat-message-received')]
    public function handleIncomingMessage(): void
    {
        $this->touchPresence();
        $this->markConversationRead();
    }

    private function touchPresence(): void
    {
        User::whereKey(auth()->id())
            ->where(fn ($query) => $query->whereNull('last_seen_at')->orWhere('last_seen_at', '<', now()->subSeconds(25)))
            ->update(['last_seen_at' => now()]);
    }

    private function contacts()
    {
        return User::query()
            ->with('employee:id,nama,tipe,foto')
            ->where('id', '!=', auth()->id())
            ->whereHas('employee', fn ($query) => $query->where('tipe', Employee::TIPE_KARYAWAN_AKTIF))
            ->orderBy('name')
            ->get();
    }

    private function markConversationRead(): void
    {
        if (!$this->selectedUserId) return;

        $updated = DirectMessage::query()
            ->where('sender_id', $this->selectedUserId)
            ->where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            $this->dispatch('chat-unread-refresh');
        }
    }

    public function render()
    {
        $allContacts = $this->contacts();
        $onlineThreshold = now()->subMinutes(2);
        $usersById = $allContacts->keyBy('id');

        $latestMessages = DirectMessage::query()
            ->where(fn ($query) => $query->where('sender_id', auth()->id())->orWhere('recipient_id', auth()->id()))
            ->latest('id')
            ->get()
            ->unique(fn (DirectMessage $message) => $message->sender_id === auth()->id() ? $message->recipient_id : $message->sender_id)
            ->keyBy(fn (DirectMessage $message) => $message->sender_id === auth()->id() ? $message->recipient_id : $message->sender_id);

        $unreadCounts = DirectMessage::query()
            ->where('recipient_id', auth()->id())
            ->whereNull('read_at')
            ->select('sender_id', DB::raw('COUNT(*) as total'))
            ->groupBy('sender_id')
            ->pluck('total', 'sender_id');

        $matchesSearch = fn (User $user) => !$this->search
            || str_contains(mb_strtolower($user->employee?->nama ?? $user->name), mb_strtolower($this->search));

        $onlineContacts = $allContacts
            ->filter(fn (User $user) => $matchesSearch($user) && $user->last_seen_at?->greaterThan($onlineThreshold))
            ->sortBy(fn (User $user) => mb_strtolower($user->employee?->nama ?? $user->name))
            ->values();

        $currentUser = auth()->user()->loadMissing('employee');
        $onlineContacts->prepend($currentUser);

        $contacts = $allContacts
            ->filter($matchesSearch)
            ->sortByDesc(fn (User $user) => $latestMessages->get($user->id)?->id ?? 0)
            ->sortByDesc(fn (User $user) => $user->last_seen_at?->greaterThan($onlineThreshold) ?? false)
            ->values();

        $selectedUser = $usersById->get($this->selectedUserId);
        $messages = collect();
        if ($selectedUser) {
            $messages = DirectMessage::query()
                ->where(fn ($query) => $query->where('sender_id', auth()->id())->where('recipient_id', $selectedUser->id))
                ->orWhere(fn ($query) => $query->where('sender_id', $selectedUser->id)->where('recipient_id', auth()->id()))
                ->with('sender:id,name')
                ->latest('id')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();
        }

        return view('livewire.chat-workspace', compact('contacts', 'onlineContacts', 'selectedUser', 'messages', 'latestMessages', 'unreadCounts', 'onlineThreshold'))
            ->layout('layouts.app', ['title' => 'Chat']);
    }
}
