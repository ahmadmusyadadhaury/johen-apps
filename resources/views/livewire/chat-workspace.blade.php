<div wire:poll.5s.visible="refreshChat" x-on:chat-message-sent.window="$nextTick(() => { const list = document.getElementById('chat-message-list'); if (list) list.scrollTop = list.scrollHeight })" class="-mx-4 -my-4 flex h-[calc(100dvh-7rem)] min-h-[26rem] overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:-mx-6 sm:-my-6 lg:-mx-8">
    <aside class="{{ $selectedUser ? 'hidden md:flex' : 'flex' }} w-full shrink-0 flex-col border-r border-gray-200 dark:border-gray-800 md:w-80 lg:w-96">
        <div class="flex items-center justify-between bg-primary-700 px-5 py-4 text-white">
            <div>
                <h1 class="text-lg font-bold">Chat Karyawan</h1>
                <p class="text-xs text-primary-100">{{ $contacts->count() }} karyawan</p>
            </div>
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15" title="Pesan pribadi">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-9.75 8.25 2.77-3.323A8.964 8.964 0 013 12a9 9 0 119 9c-1.858 0-3.584-.563-5.015-1.527L3.75 20.25z"/></svg>
            </span>
        </div>

        <div wire:ignore data-chat-push-notice data-config-url="{{ route('it.tickets.push-config') }}" data-subscribe-url="{{ route('it.tickets.push-subscription') }}" class="flex items-center justify-between gap-2 border-b border-primary-100 bg-primary-50/70 px-3 py-2.5 dark:border-primary-900/50 dark:bg-primary-950/20">
            <div class="min-w-0">
                <p class="text-xs font-semibold text-primary-900 dark:text-primary-200">Notifikasi pesan</p>
                <p data-chat-push-status class="mt-0.5 text-[10px] leading-4 text-primary-700 dark:text-primary-300">Aktifkan agar pesan baru muncul di browser.</p>
            </div>
            <button type="button" data-chat-push class="shrink-0 rounded-lg bg-primary-600 px-3 py-2 text-[10px] font-semibold text-white hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-60">Memeriksa...</button>
        </div>

        @if($onlineContacts->isNotEmpty())
            <div class="border-b border-gray-100 bg-white px-3 py-2 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-1.5 flex items-center justify-between px-0.5">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sedang aktif</p>
                    <span class="text-[10px] font-semibold text-primary-600 dark:text-primary-400">{{ $onlineContacts->count() }}</span>
                </div>
                <div class="flex snap-x snap-mandatory gap-2 overflow-x-auto px-1 py-1 pr-2 [scrollbar-width:thin]" aria-label="Karyawan yang sedang aktif">
                    @foreach($onlineContacts as $activeContact)
                        @php $activeName = $activeContact->employee?->nama ?? $activeContact->name; @endphp
                        <button type="button" wire:key="chat-active-{{ $activeContact->id }}" @if($activeContact->id === auth()->id()) disabled title="Anda sedang aktif" @else wire:click="selectUser({{ $activeContact->id }})" title="Chat dengan {{ $activeName }}" @endif class="flex w-[52px] shrink-0 snap-start flex-col items-center gap-1 rounded-lg p-0.5 transition hover:bg-primary-50 disabled:cursor-default dark:hover:bg-primary-950/40 {{ $selectedUserId === $activeContact->id ? 'bg-primary-50 dark:bg-primary-950/40' : '' }}">
                            <span class="relative flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-xs font-bold text-primary-800 ring-2 ring-primary-500 ring-offset-1 ring-offset-white dark:bg-primary-900 dark:text-primary-200 dark:ring-offset-gray-900">
                                @if($activeContact->employee?->foto_url)
                                    <img src="{{ $activeContact->employee->foto_url }}" alt="" class="h-full w-full object-cover">
                                @else
                                    {{ mb_strtoupper(mb_substr($activeName, 0, 1)) }}
                                @endif
                                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-primary-500 dark:border-gray-900"></span>
                            </span>
                            <span class="w-full truncate text-center text-[9px] leading-3 text-gray-700 dark:text-gray-300">{{ $activeContact->id === auth()->id() ? 'Anda' : $activeName }}</span>
                            <span class="text-[8px] font-semibold leading-3 text-primary-600 dark:text-primary-400">Aktif</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-b border-gray-100 p-3 dark:border-gray-800">
            <label class="flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2.5 dark:bg-gray-800">
                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-4-4"/></svg>
                <input wire:model.live.debounce.250ms="search" type="search" placeholder="Cari karyawan" class="w-full border-0 bg-transparent p-0 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-0 dark:text-gray-100">
            </label>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse($contacts as $contact)
                @php
                    $lastMessage = $latestMessages->get($contact->id);
                    $isOnline = $contact->last_seen_at && $contact->last_seen_at->greaterThan($onlineThreshold);
                @endphp
                <button type="button" wire:key="chat-contact-{{ $contact->id }}" wire:click="selectUser({{ $contact->id }})" class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-left transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/70 {{ $selectedUserId === $contact->id ? 'bg-primary-50/70 dark:bg-primary-950/20' : '' }}">
                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-100 font-semibold text-primary-800 dark:bg-primary-900/50 dark:text-primary-200">
                        @if($contact->employee?->foto_url)
                            <img src="{{ $contact->employee->foto_url }}" alt="" class="h-full w-full object-cover">
                        @else
                            {{ mb_strtoupper(mb_substr($contact->employee?->nama ?? $contact->name, 0, 1)) }}
                        @endif
                        <span class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white dark:border-gray-900 {{ $isOnline ? 'bg-primary-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <span class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $contact->employee?->nama ?? $contact->name }}</span>
                            @if($lastMessage)<time class="shrink-0 text-[10px] text-gray-400">{{ $lastMessage->created_at->format('H:i') }}</time>@endif
                        </div>
                        <div class="mt-1 flex items-center justify-between gap-2">
                            <span class="truncate text-xs {{ $isOnline ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">
                                @if($isOnline) Aktif @elseif($contact->last_seen_at) Terakhir aktif {{ $contact->last_seen_at->diffForHumans() }} @else Belum aktif @endif
                                @if($lastMessage) <span class="text-gray-400">· {{ $lastMessage->sender_id === auth()->id() ? 'Anda: ' : '' }}{{ $lastMessage->body }}</span>@endif
                            </span>
                            @if(($unreadCounts[$contact->id] ?? 0) > 0)
                                <span class="inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-primary-600 px-1.5 text-[10px] font-bold text-white">{{ $unreadCounts[$contact->id] }}</span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada karyawan yang cocok.</div>
            @endforelse
        </div>
    </aside>

    <section class="{{ $selectedUser ? 'flex' : 'hidden md:flex' }} min-w-0 flex-1 flex-col bg-[#efeae2] dark:bg-gray-950">
        @if($selectedUser)
            @php $selectedOnline = $selectedUser->last_seen_at && $selectedUser->last_seen_at->greaterThan($onlineThreshold); @endphp
            <header class="flex items-center gap-3 border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <button type="button" wire:click="$set('selectedUserId', null)" class="rounded-full p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 md:hidden" aria-label="Kembali ke daftar karyawan">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                </button>
                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-primary-100 font-semibold text-primary-800 dark:bg-primary-900/50 dark:text-primary-200">
                    @if($selectedUser->employee?->foto_url)<img src="{{ $selectedUser->employee->foto_url }}" alt="" class="h-full w-full object-cover">@else{{ mb_strtoupper(mb_substr($selectedUser->employee?->nama ?? $selectedUser->name, 0, 1)) }}@endif
                </div>
                <div class="min-w-0">
                    <h2 class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedUser->employee?->nama ?? $selectedUser->name }}</h2>
                    <p class="text-xs {{ $selectedOnline ? 'text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $selectedOnline ? 'Aktif sekarang' : ($selectedUser->last_seen_at ? 'Terakhir aktif '.$selectedUser->last_seen_at->diffForHumans() : 'Belum aktif') }}</p>
                </div>
                <div class="ml-auto flex items-center gap-1">
                    <button type="button" data-chat-call-start="audio" @disabled(!$selectedOnline) title="Telepon" aria-label="Telepon" class="rounded-full p-2 text-primary-600 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-primary-950/40"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25A2.25 2.25 0 0021.75 19.5v-1.372c0-.516-.351-.966-.852-1.087l-4.423-1.105a1.125 1.125 0 00-1.173.417l-.97 1.293a1.125 1.125 0 01-1.21.39 12.035 12.035 0 01-7.158-7.158 1.125 1.125 0 01.39-1.21l1.293-.97a1.125 1.125 0 00.417-1.173L6.96 3.102a1.125 1.125 0 00-1.087-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg></button>
                    <button type="button" data-chat-call-start="video" @disabled(!$selectedOnline) title="Video call" aria-label="Video call" class="rounded-full p-2 text-primary-600 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-primary-950/40"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5 20.47 7.9a.75.75 0 011.11.66v6.88a.75.75 0 01-1.11.66l-4.72-2.6M3.75 6.75h10.5c.83 0 1.5.67 1.5 1.5v7.5c0 .83-.67 1.5-1.5 1.5H3.75c-.83 0-1.5-.67-1.5-1.5v-7.5c0-.83.67-1.5 1.5-1.5z"/></svg></button>
                </div>
            </header>

            <div wire:ignore wire:key="chat-call-panel-{{ $selectedUser->id }}" data-chat-call-panel data-recipient-id="{{ $selectedUser->id }}" data-peer-name="{{ $selectedUser->employee?->nama ?? $selectedUser->name }}" data-signal-url="{{ route('chat.signals', $selectedUser->id) }}" class="hidden border-b border-primary-100 bg-primary-50 px-4 py-3 dark:border-primary-900 dark:bg-primary-950/40">
                <div class="flex flex-wrap items-center gap-3">
                    <p data-chat-call-status class="min-w-0 flex-1 text-sm font-medium text-primary-800 dark:text-primary-200">Panggilan</p>
                    <button type="button" data-chat-call-accept class="hidden rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white">Angkat</button>
                    <button type="button" data-chat-call-reject class="hidden rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200">Tolak</button>
                    <button type="button" data-chat-call-hangup class="hidden rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white">Akhiri</button>
                </div>
                <div data-chat-call-media class="mt-3 hidden gap-3">
                    <video data-chat-local-video autoplay muted playsinline class="hidden max-h-40 rounded-lg bg-black"></video>
                    <video data-chat-remote-video autoplay playsinline class="hidden max-h-64 flex-1 rounded-lg bg-black"></video>
                    <audio data-chat-remote-audio autoplay></audio>
                </div>
            </div>

            <div id="chat-message-list" x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight })" class="min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-5 sm:px-8">
                @forelse($messages as $chatMessage)
                    @php $mine = $chatMessage->sender_id === auth()->id(); @endphp
                    <div wire:key="chat-message-{{ $chatMessage->id }}" class="group flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-xl px-3 py-2 shadow-sm sm:max-w-[70%] {{ $mine ? 'rounded-tr-sm bg-primary-100 text-primary-950 dark:bg-primary-900/60 dark:text-gray-100' : 'rounded-tl-sm bg-white text-gray-800 dark:bg-gray-800 dark:text-gray-100' }}">
                            @if($chatMessage->attachment_path)
                                @php $attachmentUrl = route('chat.attachments.show', $chatMessage); $isImage = str_starts_with((string) $chatMessage->attachment_mime, 'image/'); @endphp
                                @if($isImage)
                                    <a href="{{ $attachmentUrl }}" target="_blank" rel="noopener" class="mb-2 block"><img src="{{ $attachmentUrl }}" alt="{{ $chatMessage->attachment_name }}" loading="lazy" class="max-h-72 max-w-full rounded-lg object-contain"></a>
                                @else
                                    <a href="{{ $attachmentUrl }}" class="mb-2 flex items-center gap-2 rounded-lg bg-black/5 px-3 py-2 text-sm text-primary-700 hover:bg-black/10 dark:bg-white/5 dark:text-primary-300 dark:hover:bg-white/10"><svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25A3.75 3.75 0 004.5 6v12A3.75 3.75 0 008.25 21.75h7.5a3.75 3.75 0 003.75-3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25V6a2.25 2.25 0 002.25 2.25H18"/></svg><span class="min-w-0"><span class="block break-all font-medium">{{ $chatMessage->attachment_name }}</span><span class="text-xs text-gray-500">{{ number_format(($chatMessage->attachment_size ?? 0) / 1024, 0) }} KB · Unduh</span></span></a>
                                @endif
                            @endif
                            @if($chatMessage->body !== '')<p class="whitespace-pre-wrap break-words text-sm">{{ $chatMessage->body }}</p>@endif
                            <div class="mt-1 flex items-center justify-end gap-1 text-[10px] text-gray-500 dark:text-gray-400">
                                <time>{{ $chatMessage->created_at->format('H:i') }}</time>
                                @if($mine)<button type="button" wire:click="deleteMessage({{ $chatMessage->id }})" wire:confirm="Hapus pesan ini?" title="Hapus pesan" aria-label="Hapus pesan" class="ml-1 rounded px-1 py-0.5 text-[9px] font-semibold text-gray-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40">Hapus</button>@endif
                                @if($mine)<span aria-label="{{ $chatMessage->read_at ? 'Dibaca' : 'Terkirim' }}" class="{{ $chatMessage->read_at ? 'text-sky-600' : '' }}">{{ $chatMessage->read_at ? '✓✓' : '✓' }}</span>@endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="mx-auto mt-12 max-w-sm rounded-xl bg-[#fff8c5]/90 px-4 py-3 text-center text-xs leading-5 text-amber-900 shadow-sm dark:bg-amber-950/70 dark:text-amber-200">Pesan pribadi hanya dapat dilihat oleh Anda dan {{ $selectedUser->employee?->nama ?? $selectedUser->name }}. Mulai percakapan dengan mengirim pesan.</div>
                @endforelse
            </div>

            @if($attachment)
                @php
                    $pendingMime = $attachment->getMimeType() ?: 'application/octet-stream';
                    $pendingIsImage = str_starts_with($pendingMime, 'image/');
                    $pendingIsPdf = $pendingMime === 'application/pdf';
                    $pendingPreviewUrl = ($pendingIsImage || $pendingIsPdf) ? $attachment->temporaryUrl() : null;
                @endphp
                <div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-start gap-3">
                        @if($pendingIsImage)
                            <img src="{{ $pendingPreviewUrl }}" alt="Pratinjau {{ $attachment->getClientOriginalName() }}" class="h-20 w-20 shrink-0 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-700 dark:bg-primary-950/50 dark:text-primary-300"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 0010.125 2.25H8.25A3.75 3.75 0 004.5 6v12A3.75 3.75 0 008.25 21.75h7.5a3.75 3.75 0 003.75-3.75z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25V6a2.25 2.25 0 002.25 2.25H18"/></svg></span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-primary-700 dark:text-primary-300">Pratinjau lampiran</p>
                            <p class="mt-0.5 break-all text-sm font-medium text-gray-800 dark:text-gray-100">{{ $attachment->getClientOriginalName() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($attachment->getClientOriginalExtension()) }} · {{ number_format($attachment->getSize() / 1024, 0) }} KB</p>
                            @if($pendingIsPdf)<a href="{{ $pendingPreviewUrl }}" target="_blank" rel="noopener" class="mt-1 inline-block text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">Buka pratinjau PDF</a>@elseif(!$pendingIsImage)<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dokumen siap dikirim. Pratinjau isi tersedia setelah diunduh.</p>@endif
                        </div>
                        <button type="button" wire:click="$set('attachment', null)" title="Hapus lampiran" class="shrink-0 rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">Hapus</button>
                    </div>
                    @if($pendingIsPdf)<iframe src="{{ $pendingPreviewUrl }}" title="Pratinjau PDF" class="mt-3 hidden h-56 w-full rounded-lg border border-gray-200 sm:block dark:border-gray-700"></iframe>@endif
                </div>
            @endif
            @error('attachment')<p class="bg-white px-5 pt-2 text-xs text-red-600 dark:bg-gray-900">{{ $message }}</p>@enderror
            <form wire:submit="sendMessage" class="flex items-end gap-2 border-t border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900 sm:px-5">
                <label class="inline-flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" title="Lampirkan foto atau dokumen"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.125 4.125 0 01-5.834-5.834l10.607-10.607a2.75 2.75 0 013.89 3.89L8.737 16.49a1.375 1.375 0 01-1.944-1.945l9.546-9.546"/></svg><input type="file" wire:model="attachment" accept="image/jpeg,image/png,image/gif,image/webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip" class="hidden"></label>
                <textarea wire:model="message" rows="1" wire:keydown.enter.prevent="$wire.sendMessage()" placeholder="Ketik pesan" class="max-h-32 min-h-11 flex-1 resize-y rounded-2xl border-0 bg-gray-100 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-100"></textarea>
                <button type="submit" wire:loading.attr="disabled" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-600 text-white transition hover:bg-primary-700 disabled:opacity-50" aria-label="Kirim pesan">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13"/></svg>
                </button>
            </form>
            @error('message')<p class="bg-white px-5 pb-2 text-xs text-red-600 dark:bg-gray-900">{{ $message }}</p>@enderror
        @else
            <div class="m-auto max-w-md px-8 text-center">
                <div class="mx-auto mb-5 flex h-24 w-24 items-center justify-center rounded-full bg-primary-100 text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                    <svg class="h-11 w-11" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-9.75 8.25 2.77-3.323A8.964 8.964 0 013 12a9 9 0 119 9c-1.858 0-3.584-.563-5.015-1.527L3.75 20.25z"/></svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Chat Karyawan</h2>
                <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">Pilih nama karyawan untuk mulai mengirim pesan pribadi.</p>
            </div>
        @endif
    </section>
</div>
