@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pengumuman</h1>
        <p class="text-xs text-gray-400 mt-0.5">Informasi &amp; surat resmi dari manajemen perusahaan</p>
    </div>
@endpush

@php
    $tabs = [
        ['key' => 'semua', 'label' => 'Semua', 'count' => $statTotal],
        ['key' => 'pengumuman', 'label' => 'Pengumuman', 'count' => $statPengumuman],
        ['key' => 'surat_edaran', 'label' => 'Surat Edaran', 'count' => $statEdaran],
        ['key' => 'surat_keputusan', 'label' => 'Surat Keputusan', 'count' => $statKeputusan],
        ['key' => 'pemberitahuan', 'label' => 'Pemberitahuan', 'count' => $statPemberitahuan],
    ];

    $avatars = [
        'pengumuman' => ['bg' => 'bg-gradient-to-br from-primary-500 to-violet-500 text-white shadow-md shadow-primary-200/60', 'icon' => 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09'],
        'surat_edaran' => ['bg' => 'bg-gradient-to-br from-sky-500 to-blue-500 text-white shadow-md shadow-sky-200/60', 'icon' => 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5'],
        'surat_keputusan' => ['bg' => 'bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-md shadow-violet-200/60', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z'],
        'pemberitahuan' => ['bg' => 'bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-md shadow-amber-200/60', 'icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
    ];

    $statCards = [
        ['key' => 'pengumuman', 'label' => 'Pengumuman', 'value' => $statPengumuman, 'gradient' => 'from-primary-500 to-violet-500', 'shadow' => 'shadow-primary-200', 'badge' => 'badge-primary', 'path' => $avatars['pengumuman']['icon']],
        ['key' => 'surat_edaran', 'label' => 'Surat Edaran', 'value' => $statEdaran, 'gradient' => 'from-sky-500 to-blue-500', 'shadow' => 'shadow-sky-200', 'badge' => 'badge-info', 'path' => $avatars['surat_edaran']['icon']],
        ['key' => 'surat_keputusan', 'label' => 'Surat Keputusan', 'value' => $statKeputusan, 'gradient' => 'from-violet-500 to-purple-500', 'shadow' => 'shadow-violet-200', 'badge' => 'badge-success', 'path' => $avatars['surat_keputusan']['icon']],
        ['key' => 'pemberitahuan', 'label' => 'Pemberitahuan', 'value' => $statPemberitahuan, 'gradient' => 'from-amber-500 to-orange-500', 'shadow' => 'shadow-amber-200', 'badge' => 'badge-warning', 'path' => $avatars['pemberitahuan']['icon']],
    ];
@endphp

<div>
    {{-- Stats Overview --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        @foreach ($statCards as $sc)
            <button wire:click="$set('jenisFilter', '{{ $sc['key'] }}')" class="stat-card group text-left cursor-pointer focus:outline-none {{ $jenisFilter === $sc['key'] ? 'ring-2 ring-primary-300 dark:ring-primary-700' : '' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br {{ $sc['gradient'] }} text-white shadow-lg {{ $sc['shadow'] }} group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $sc['path'] }}"/></svg>
                    </div>
                    <span class="{{ $sc['badge'] }}">{{ $sc['value'] }}</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($sc['value'], 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $sc['label'] }}</p>
            </button>
        @endforeach
    </div>

    {{-- Main Card --}}
    <div class="card">
        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
                {{-- Search --}}
                <div class="relative sm:max-w-xs w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari judul, nomor surat atau isi..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all duration-200"
                    >
                </div>

                {{-- Jenis Filter --}}
                <div class="inline-flex items-center flex-wrap gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1 self-start sm:self-auto">
                    @foreach ($tabs as $t)
                        <button wire:click="$set('jenisFilter', '{{ $t['key'] }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200 {{ $jenisFilter === $t['key'] ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            {{ $t['label'] }}
                            <span class="rounded-md px-1.5 py-px text-[10px] font-semibold {{ $jenisFilter === $t['key'] ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300' : 'bg-gray-200/70 dark:bg-gray-900 text-gray-500 dark:text-gray-400' }}">{{ $t['count'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Inbox List --}}
        <div wire:loading.delay.class="opacity-60 pointer-events-none" wire:target="search, jenisFilter" class="transition-opacity duration-200">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($inbox as $item)
                @php $avatar = $avatars[$item['badge_key']] ?? $avatars['pengumuman']; $badge = \App\Livewire\PengumumanInbox::badge($item['badge_key']); @endphp
                <div class="group flex flex-col md:flex-row md:items-center gap-4 px-6 py-5 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                    {{-- Icon Avatar --}}
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $avatar['bg'] }} transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $avatar['icon'] }}"/></svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate max-w-full">{{ $item['title'] }}</h3>
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @if($item['type'] === 'pengumuman')
                                @if($item['is_read'])
                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-[10px] font-semibold text-gray-500 dark:text-gray-400 ring-1 ring-gray-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 text-[10px] font-semibold text-blue-700 dark:text-blue-300 ring-1 ring-blue-600/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Baru
                                    </span>
                                @endif
                            @endif
                            @if($item['event_label'])
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-violet-50 dark:bg-violet-900/30 px-2 py-0.5 text-[10px] font-semibold text-violet-700 dark:text-violet-400 ring-1 ring-violet-600/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    {{ $item['event_label'] }}
                                </span>
                            @endif
                        </div>

                        @if($item['nomor'])
                            <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500 mt-0.5">Nomor: {{ $item['nomor'] }}</p>
                        @endif

                        @if($item['description'])
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $item['date']?->locale('id')->isoFormat('D MMM Y') }}
                            </span>
                            @if($item['file_url'])
                                <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    Lampiran PDF
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        @if($item['type'] === 'arsip' && $item['file_url'])
                            <a href="{{ $item['file_url'] }}" target="_blank" rel="noopener"
                                aria-label="Buka lampiran PDF"
                                title="Buka / unduh file surat (PDF)"
                                class="inline-flex items-center gap-1.5 h-9 rounded-lg border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/30 px-3 text-[11px] font-semibold text-primary-600 dark:text-primary-300 hover:bg-primary-100 dark:hover:bg-primary-900/50 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Buka File
                            </a>
                        @elseif($item['type'] === 'pengumuman')
                            @if(! $item['is_read'])
                                <button wire:click="markRead({{ $item['id'] }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markRead({{ $item['id'] }})"
                                    aria-label="Tandai sudah dibaca"
                                    title="Tandai sudah dibaca"
                                    class="inline-flex items-center gap-1.5 h-9 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/30 px-3 text-[11px] font-semibold text-blue-600 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Tandai Dibaca
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="flex h-16 w-16 mx-auto mb-4 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-800">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                    </div>
                    @if($search !== '' || $jenisFilter !== 'semua')
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tidak ada hasil</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ada informasi yang cocok dengan pencarian atau filter.</p>
                    @else
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Informasi</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengumuman dan surat resmi dari manajemen akan tampil di sini.</p>
                    @endif
                </div>
            @endforelse
        </div>
        </div>

        @if($inbox->hasPages())
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $inbox->links() }}
        </div>
        @endif
    </div>
</div>
