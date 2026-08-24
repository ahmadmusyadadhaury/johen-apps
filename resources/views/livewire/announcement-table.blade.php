@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pengumuman</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola informasi & pengumuman untuk karyawan</p>
    </div>
@endpush

<div>
    {{-- Stats Overview --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/></svg>
                </div>
                <span class="badge-info">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statTotal }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pengumuman</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.06 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                </div>
                <span class="badge-success">Tayang</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statPublished }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dipublikasikan</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <span class="badge-warning">Draft</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statDraft }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum Tayang</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="badge-info">Terbaca</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $statReads }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pembacaan</p>
        </div>
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
                        placeholder="Cari judul atau isi pengumuman..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all duration-200"
                    >
                </div>

                {{-- Status Filter --}}
                <div class="inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1 self-start sm:self-auto">
                    @php $tabs = [['key' => 'semua', 'label' => 'Semua', 'count' => $statTotal], ['key' => 'publish', 'label' => 'Tayang', 'count' => $statPublished], ['key' => 'draft', 'label' => 'Draft', 'count' => $statDraft]]; @endphp
                    @foreach($tabs as $t)
                        <button wire:click="$set('statusFilter', '{{ $t['key'] }}')"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium transition-all duration-200 {{ $statusFilter === $t['key'] ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                            {{ $t['label'] }}
                            <span class="rounded-md px-1.5 py-px text-[10px] font-semibold {{ $statusFilter === $t['key'] ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300' : 'bg-gray-200/70 dark:bg-gray-900 text-gray-500 dark:text-gray-400' }}">{{ $t['count'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <button wire:click="openNew" class="btn-primary shrink-0 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Buat Pengumuman
            </button>
        </div>

        {{-- Announcement List --}}
        <div wire:loading.delay.class="opacity-60 pointer-events-none" wire:target="search, statusFilter" class="transition-opacity duration-200">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($announcements as $announcement)
                @php
                    $pct = $audience > 0 ? min(100, (int) round($announcement->readers_count * 100 / $audience)) : 0;
                    $eventLabel = null;
                    if ($announcement->event_date) {
                        $eventLabel = \Carbon\Carbon::parse($announcement->event_date)->locale('id')->isoFormat('D MMM YYYY');
                        if ($announcement->event_time) {
                            $eventLabel .= ' · '.substr($announcement->event_time, 0, 5);
                        }
                    }
                @endphp
                <div class="group flex flex-col md:flex-row md:items-center gap-4 px-6 py-5 hover:bg-gray-50/80 dark:hover:bg-gray-800/40 transition-colors">
                    {{-- Icon Avatar --}}
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $announcement->is_published ? 'bg-gradient-to-br from-primary-500 to-violet-500 text-white shadow-md shadow-primary-200/60' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }} transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.06 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate max-w-full">{{ $announcement->title }}</h3>
                            @if($announcement->is_published)
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Tayang
                                </span>
                            @else
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-900/30 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:text-amber-400 ring-1 ring-amber-600/20">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Draft
                                </span>
                            @endif
                            @if($eventLabel)
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-violet-50 dark:bg-violet-900/30 px-2 py-0.5 text-[10px] font-semibold text-violet-700 dark:text-violet-400 ring-1 ring-violet-600/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    {{ $eventLabel }}
                                </span>
                            @endif
                        </div>

                        @if($announcement->summary || $announcement->content)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ $announcement->summary ?? $announcement->content }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Dibuat {{ $announcement->created_at->locale('id')->isoFormat('D MMM Y') }}
                            </span>
                            <span class="inline-flex items-center gap-2 text-[11px] text-gray-400 dark:text-gray-500" title="{{ $announcement->readers_count }} dari {{ $audience }} pengguna telah membaca">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                {{ $announcement->readers_count }}/{{ $audience }} dibaca
                                <span class="hidden sm:inline-block h-1.5 w-24 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <span class="block h-full rounded-full bg-gradient-to-r from-primary-400 to-violet-500 transition-all duration-500" style="width: {{ $pct }}%"></span>
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <button wire:click="togglePublish({{ $announcement->id }})"
                            aria-label="{{ $announcement->is_published ? 'Sembunyikan dari dashboard' : 'Tayangkan di dashboard' }}"
                            title="{{ $announcement->is_published ? 'Sembunyikan dari dashboard' : 'Tayangkan di dashboard' }}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border transition-all duration-200 {{ $announcement->is_published ? 'border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30' : 'border-gray-200 dark:border-gray-700 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-600 dark:hover:text-gray-300' }}">
                            @if($announcement->is_published)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @endif
                        </button>
                        <button wire:click="openEdit({{ $announcement->id }})"
                            aria-label="Edit pengumuman"
                            title="Edit"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-200 dark:hover:border-primary-800 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                        </button>
                        <button wire:click="confirmDelete({{ $announcement->id }})"
                            aria-label="Hapus pengumuman"
                            title="Hapus"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="flex h-16 w-16 mx-auto mb-4 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-800">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.06 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                    </div>
                    @if($search !== '' || $statusFilter !== 'semua')
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Tidak ada hasil</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ada pengumuman yang cocok dengan pencarian atau filter.</p>
                    @else
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Pengumuman</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-5">Buat pengumuman pertama untuk mengabarkan seluruh karyawan.</p>
                        <button wire:click="openNew" class="btn-primary text-xs mx-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Buat Pengumuman
                        </button>
                    @endif
                </div>
            @endforelse
        </div>
        </div>

        @if($announcements->hasPages())
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>

    {{-- ============ CREATE / EDIT MODAL ============ --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showModal') }"
         x-show="open"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false; $wire.close()"
         @keydown.escape.window="open = false; $wire.close()">
        <div @click.stop
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 shadow-2xl my-10 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 flex flex-col">

            {{-- Modal Header --}}
            <div class="flex items-center gap-4 px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-violet-500 text-white shadow-md shadow-primary-200/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.06 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit Pengumuman' : 'Buat Pengumuman Baru' }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $editId ? 'Perbarui informasi pengumuman yang sudah ada' : 'Informasi akan tampil di dashboard karyawan' }}</p>
                </div>
                <button wire:click="close" aria-label="Tutup" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="flex flex-col max-h-[72vh] overflow-hidden">
                <div class="px-6 py-5 space-y-5 overflow-y-auto">
                <div>
                    <x-input-label value="Judul Pengumuman *" />
                    <x-text-input type="text" wire:model="title" class="mt-1.5 block w-full" placeholder="Contoh: Rapat Koordinasi Bulanan" />
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Ringkasan" />
                    <x-text-input type="text" wire:model="summary" class="mt-1.5 block w-full" placeholder="Ringkasan singkat, tampil sebagai preview di dashboard" />
                    <p class="mt-1 text-[11px] flex items-center justify-between text-gray-400 dark:text-gray-500">
                        <span>Opsional — jika kosong, potongan isi pertama yang ditampilkan.</span>
                        <span class="tabular-nums {{ strlen($summary) > 255 ? 'text-red-500 font-semibold' : '' }}">{{ strlen($summary) }}/255</span>
                    </p>
                    @error('summary') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Tanggal Acara" />
                        <input type="date" wire:model="event_date" class="mt-1.5 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all duration-200 dark:[color-scheme:dark]" />
                        @error('event_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Waktu Acara" />
                        <input type="time" wire:model="event_time" class="mt-1.5 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all duration-200 dark:[color-scheme:dark]" />
                        @error('event_time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label value="Isi Pengumuman" />
                    <textarea wire:model="content" rows="7" class="mt-1.5 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all duration-200" placeholder="Tulis detail lengkap pengumuman..."></textarea>
                    @error('content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Publish Toggle --}}
                <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/40 px-4 py-3.5">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Tayangkan ke Karyawan</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Nonaktifkan untuk menyimpan sebagai draft tanpa menampilkan di dashboard.</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" wire:model="is_published" class="peer sr-only" />
                        <div class="h-6 w-11 rounded-full bg-gray-300 dark:bg-gray-600 transition-colors duration-200 peer-checked:bg-primary-600 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-transform after:duration-200 peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
                </div>

                {{-- Modal Footer --}}
                <div class="shrink-0 flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/40">
                <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-primary text-xs">
                    <span wire:loading.remove wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Simpan Perubahan' : 'Terbitkan Pengumuman' }}
                    </span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- ============ DELETE CONFIRM MODAL ============ --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showDeleteConfirmModal') }"
         x-show="open"
         x-cloak
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false; $wire.cancelDelete()"
         @keydown.escape.window="open = false; $wire.cancelDelete()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Hapus Pengumuman?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-1">Pengumuman</p>
            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 text-center mb-6 line-clamp-2">&ldquo;{{ $deleteTitle }}&rdquo;</p>
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.cancelDelete()" class="btn-secondary text-xs px-6">Batal</button>
                <button wire:click="executeDelete" wire:loading.attr="disabled" wire:target="executeDelete" class="btn-danger text-xs px-6 inline-flex items-center gap-2">
                    <span wire:loading.remove wire:target="executeDelete" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Ya, Hapus
                    </span>
                    <span wire:loading wire:target="executeDelete" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Menghapus...
                    </span>
                </button>
            </div>
        </div>
    </div>
    </template>
</div>
