@php
    $activeJenisTab = collect($tabs)->firstWhere('key', $jenisFilter) ?? $tabs[0];
@endphp

<section class="space-y-5 md:hidden" aria-label="Pengumuman dan surat resmi">
    <section aria-label="Ringkasan jumlah dokumen" class="-mx-4">
        <div class="mobile-summary-scroll flex snap-x snap-mandatory gap-3 overflow-x-auto px-4 pb-1">
            @foreach($statCards as $sc)
                <article class="stat-card group w-40 shrink-0 snap-start p-4 first:ml-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br {{ $sc['gradient'] }} text-white shadow-md {{ $sc['shadow'] }}" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sc['path'] }}"/></svg>
                    </div>
                    <p class="mt-3 text-2xl font-bold leading-none tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($sc['value'], 0, ',', '.') }}</p>
                    <p class="mt-1.5 text-sm font-medium leading-5 text-gray-500 dark:text-gray-400">{{ $sc['label'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="space-y-3">
        <div class="relative">
            <label for="mobile-announcement-search" class="sr-only">Cari pengumuman atau surat</label>
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="M16 16l4.5 4.5"/></svg>
            <input id="mobile-announcement-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Cari judul, nomor surat atau isi..."
                   class="min-h-12 w-full rounded-2xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-base text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-primary-900/40">
        </div>

        <button type="button" @click="filterOpen = true; draftJenisFilter = @js($jenisFilter)" aria-haspopup="dialog" aria-controls="mobile-announcement-filter"
                class="flex min-h-12 w-full items-center justify-between rounded-2xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-800 shadow-sm transition hover:border-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <span class="inline-flex items-center gap-2.5">
                <svg class="h-4 w-4 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10m-7 6h4"/></svg>
                <span>{{ $activeJenisTab['label'] }}</span>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs tabular-nums text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $activeJenisTab['count'] }}</span>
            </span>
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/></svg>
        </button>
    </div>

    <div class="flex items-center justify-between gap-3 pt-1">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Informasi terbaru</h2>
        <p class="text-xs tabular-nums text-gray-500 dark:text-gray-400">{{ number_format($inbox->total(), 0, ',', '.') }} informasi</p>
    </div>

    <div wire:loading.delay.class="opacity-60 pointer-events-none" wire:target="search, jenisFilter" class="space-y-3 transition-opacity duration-200">
        @forelse($inbox as $item)
            @php
                $avatar = $avatars[$item['badge_key']] ?? $avatars['pengumuman'];
                $badge = \App\Livewire\PengumumanInbox::badge($item['badge_key']);
                $dateLabel = $item['date']?->locale('id')->isoFormat('D MMM YYYY');
                $mobileDetail = [
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'date' => $dateLabel,
                    'eventLabel' => $item['event_label'],
                    'number' => $item['nomor'],
                    'isRead' => $item['is_read'],
                    'badgeLabel' => $badge['label'],
                    'badgeClass' => $badge['class'],
                    'icon' => $avatar['icon'],
                    'iconClass' => $avatar['bg'],
                    'fileUrl' => $item['file_url'],
                ];
            @endphp
            <article class="overflow-hidden rounded-2xl border {{ $item['type'] === 'pengumuman' && ! $item['is_read'] ? 'border-primary-200 bg-primary-50/40 dark:border-primary-800 dark:bg-primary-950/20' : 'border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900' }} shadow-sm transition hover:shadow-md">
                <button type="button" @click="selectedItem = @js($mobileDetail)" aria-label="Buka detail: {{ $item['title'] }}"
                        class="group flex min-h-11 w-full items-start gap-3 p-4 text-left active:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary-500 dark:active:bg-gray-800">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $avatar['bg'] }}" aria-hidden="true">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $avatar['icon'] }}"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block line-clamp-2 text-base font-bold leading-5 text-gray-900 dark:text-gray-100">{{ $item['title'] }}</span>
                        <span class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex min-h-6 items-center rounded-full px-2.5 text-[11px] font-semibold ring-1 {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            @if($item['type'] === 'pengumuman')
                                @if($item['is_read'])
                                    <span class="inline-flex min-h-6 items-center gap-1 rounded-full bg-gray-100 px-2.5 text-[11px] font-semibold text-gray-600 ring-1 ring-gray-500/15 dark:bg-gray-800 dark:text-gray-300">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex min-h-6 items-center gap-1.5 rounded-full bg-blue-50 px-2.5 text-[11px] font-semibold text-blue-700 ring-1 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>Belum dibaca
                                    </span>
                                @endif
                            @endif
                        </span>
                        @if($item['description'])
                            <span class="mt-2 block line-clamp-2 text-sm leading-5 text-gray-600 dark:text-gray-300">{{ $item['description'] }}</span>
                        @endif
                        <span class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/></svg>
                                {{ $dateLabel }}
                            </span>
                            @if($item['nomor'])
                                <span class="truncate">No. {{ $item['nomor'] }}</span>
                            @endif
                            @if($item['file_url'])
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    Lampiran PDF
                                </span>
                            @endif
                        </span>
                    </span>
                    <svg class="mt-3 h-4 w-4 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center dark:border-gray-700 dark:bg-gray-900">
                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300" aria-hidden="true">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73"/></svg>
                </span>
                @if($search !== '' || $jenisFilter !== 'semua')
                    <h3 class="mt-3 text-base font-bold text-gray-900 dark:text-gray-100">Tidak ada hasil</h3>
                    <p class="mx-auto mt-1 max-w-xs text-sm leading-5 text-gray-500 dark:text-gray-400">Tidak ditemukan informasi yang sesuai dengan pencarian atau filter.</p>
                    <button type="button" wire:click="$set('search', ''); $set('jenisFilter', 'semua')" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-50 px-4 text-sm font-semibold text-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:bg-primary-900/30 dark:text-primary-200">Reset Filter</button>
                @else
                    <h3 class="mt-3 text-base font-bold text-gray-900 dark:text-gray-100">Belum ada pengumuman</h3>
                    <p class="mx-auto mt-1 max-w-xs text-sm leading-5 text-gray-500 dark:text-gray-400">Belum ada informasi atau surat yang tersedia.</p>
                @endif
            </div>
        @endforelse
    </div>

    @if($inbox->hasPages())
        <div class="rounded-2xl border border-gray-100 bg-white px-3 py-2 dark:border-gray-800 dark:bg-gray-900">
            {{ $inbox->links() }}
        </div>
    @endif
</section>

<div x-show="filterOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[55] flex items-end justify-center bg-gray-950/55 md:hidden" role="dialog" aria-modal="true" aria-labelledby="mobile-announcement-filter-title" @click.self="filterOpen = false" @keydown.escape.window="filterOpen = false">
    <section id="mobile-announcement-filter" x-transition:enter="transform transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full" class="max-h-[85dvh] w-full overflow-y-auto rounded-t-[1.75rem] border border-gray-100 bg-white px-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] pt-3 shadow-2xl dark:border-gray-800 dark:bg-gray-900 sm:px-6">
        <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-gray-200 dark:bg-gray-700"></div>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 id="mobile-announcement-filter-title" class="text-lg font-bold tracking-tight text-gray-900 dark:text-gray-100">Filter Pengumuman</h2>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Pilih jenis informasi yang ingin dilihat</p>
            </div>
            <button type="button" @click="filterOpen = false" aria-label="Tutup filter" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-500 transition hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>
        <div class="space-y-2">
            @foreach($tabs as $t)
                <button type="button" @click="draftJenisFilter = @js($t['key'])" :aria-pressed="draftJenisFilter === @js($t['key'])" class="flex min-h-12 w-full items-center justify-between rounded-xl border border-gray-200 px-3.5 text-left transition focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-700" :class="draftJenisFilter === @js($t['key']) ? 'border-primary-300 bg-primary-50/70 dark:border-primary-700 dark:bg-primary-900/20' : 'bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800'">
                    <span class="inline-flex items-center gap-3">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border" :class="draftJenisFilter === @js($t['key']) ? 'border-primary-600 bg-primary-600 text-white dark:border-primary-400 dark:bg-primary-500' : 'border-gray-300 dark:border-gray-600'">
                            <svg x-show="draftJenisFilter === @js($t['key'])" x-cloak class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $t['label'] }}</span>
                    </span>
                    <span class="min-w-8 rounded-full bg-gray-100 px-2 py-1 text-center text-xs font-semibold tabular-nums text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $t['count'] }}</span>
                </button>
            @endforeach
        </div>
        <div class="mt-6 grid grid-cols-[auto_1fr] gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
            <button type="button" @click="draftJenisFilter = 'semua'" class="min-h-12 rounded-xl border border-gray-200 px-5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Reset</button>
            <button type="button" @click="$wire.set('jenisFilter', draftJenisFilter); filterOpen = false" class="min-h-12 rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900">Terapkan</button>
        </div>
    </section>
</div>

<div x-show="selectedItem !== null" x-cloak x-transition.opacity class="fixed inset-0 z-[55] bg-gray-50 dark:bg-gray-950 md:hidden" role="dialog" aria-modal="true" aria-labelledby="mobile-announcement-detail-title" @keydown.escape.window="selectedItem = null">
    <div class="flex h-full flex-col safe-t safe-b">
        <header class="flex min-h-14 shrink-0 items-center gap-3 border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900">
            <button type="button" @click="selectedItem = null" aria-label="Kembali ke daftar pengumuman" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-gray-700 transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:text-gray-200 dark:hover:bg-gray-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7M8 12h13"/></svg>
            </button>
            <h1 id="mobile-announcement-detail-title" class="min-w-0 flex-1 truncate text-base font-bold text-gray-900 dark:text-gray-100">Detail Pengumuman</h1>
        </header>

        <main class="flex-1 overflow-y-auto px-4 py-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))]">
            <article class="mx-auto max-w-xl space-y-5">
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl" :class="selectedItem?.iconClass" aria-hidden="true">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" :d="selectedItem?.icon"/></svg>
                    </div>
                    <h2 class="mt-4 break-words text-xl font-bold leading-7 text-gray-900 dark:text-gray-100" x-text="selectedItem?.title"></h2>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="inline-flex min-h-7 items-center rounded-full px-3 text-xs font-semibold ring-1" :class="selectedItem?.badgeClass" x-text="selectedItem?.badgeLabel"></span>
                        <template x-if="selectedItem?.type === 'pengumuman'">
                            <span class="inline-flex min-h-7 items-center gap-1.5 rounded-full px-3 text-xs font-semibold ring-1" :class="selectedItem?.isRead ? 'bg-gray-100 text-gray-600 ring-gray-500/15 dark:bg-gray-800 dark:text-gray-300' : 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-200'">
                                <svg x-show="selectedItem?.isRead" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span x-text="selectedItem?.isRead ? 'Dibaca' : 'Belum dibaca'"></span>
                            </span>
                        </template>
                    </div>
                </div>

                <dl class="grid grid-cols-1 gap-3 rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="selectedItem?.date"></dd>
                    </div>
                    <div x-show="selectedItem?.number" x-cloak>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nomor surat</dt>
                        <dd class="mt-1 break-all text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="selectedItem?.number"></dd>
                    </div>
                    <div x-show="selectedItem?.eventLabel" x-cloak>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Jadwal acara</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="selectedItem?.eventLabel"></dd>
                    </div>
                </dl>

                <section x-show="selectedItem?.description" x-cloak class="rounded-2xl border border-gray-100 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="selectedItem?.type === 'pengumuman' ? 'Isi pengumuman' : 'Keterangan surat'"></h3>
                    <p class="mt-3 whitespace-pre-wrap break-words text-sm leading-6 text-gray-700 dark:text-gray-300" x-text="selectedItem?.description"></p>
                </section>

                <section x-show="selectedItem?.fileUrl" x-cloak class="rounded-2xl border border-gray-100 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="mb-3 text-sm font-bold text-gray-900 dark:text-gray-100">Lampiran</h3>
                    <button type="button" @click="pdfUrl = selectedItem.fileUrl" class="flex min-h-14 w-full items-center gap-3 rounded-xl border border-gray-200 p-3 text-left transition hover:border-primary-300 hover:bg-primary-50/50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-700 dark:hover:border-primary-700 dark:hover:bg-primary-900/10">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300" aria-hidden="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">Dokumen PDF</span>
                            <span class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">Ketuk untuk membuka file</span>
                        </span>
                        <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </section>

                <button type="button" x-show="selectedItem?.type === 'pengumuman' && !selectedItem?.isRead" @click="$wire.markRead(selectedItem.id); selectedItem.isRead = true" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-950">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Tandai sudah dibaca
                </button>
            </article>
        </main>
    </div>
</div>
