@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Script Video Animator</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">{{ $isRekap ? 'Rekap script semua koordinator' : ($isVideoAnimator ? 'Semua script koordinator &mdash; ' . $bulan . ' ' . $tahun : 'Unggah script video animator per konten &mdash; target ' . $target . ' konten / bulan') }}</p>
    </div>
@endpush

@php
    $isComplete = $isRekap ? $rekapStats['belum'] === 0 : $jumlah >= $target;
    $barPercent = $isRekap
        ? ($rekapStats['total'] > 0 ? round($rekapStats['tercapai'] / $rekapStats['total'] * 100) : 0)
        : $progress;
@endphp

<div x-data="{ pushSuccessOpen: false }" x-on:script-video-push-activated.window="pushSuccessOpen = true" class="space-y-6">
    @if(!empty($weeklySummary))
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Target Mingguan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Quota dinamis: sisa target dibagi minggu tersisa</p>
            </div>
            <span class="inline-flex shrink-0 items-center rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold text-blue-600 ring-1 ring-inset ring-blue-600/10 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/20">Minggu Berjalan: M{{ $mingguBerjalan }}</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach(range(1, 4) as $m)
            @php
                $w = $weeklySummary[$m];
                $pct = $w['quota'] > 0 ? min(100, round($w['terisi'] / $w['quota'] * 100)) : 0;
            @endphp
            <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 p-4 {{ $m === $mingguBerjalan ? 'ring-2 ring-blue-500/20 border-blue-200 dark:border-blue-800' : '' }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">Minggu {{ $m }}</span>
                    @if($m === $mingguBerjalan)
                    <span class="badge-primary">Berjalan</span>
                    @endif
                </div>
                <div class="flex items-end justify-between mb-1">
                    <span class="text-2xl font-bold tabular-nums text-gray-900 dark:text-gray-100">{{ $w['terisi'] }}</span>
                    <span class="text-[11px] text-gray-400">/ {{ $w['quota'] }}</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden mb-2">
                    <div class="h-full rounded-full {{ $w['sisa'] === 0 ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $pct }}%"></div>
                </div>
                <p class="text-[11px] {{ $w['sisa'] === 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ $w['sisa'] === 0 ? 'Tercapai' : 'Sisa ' . $w['sisa'] . ' konten' }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!$isRekap && !$isVideoAnimator && auth()->user()?->isAnyKoordinator())
    @if($jumlah < $target)
    <div x-data="{ open: false }" x-init="try { let key = 'script-video-reminder-{{ now()->toDateString() }}'; if (localStorage.getItem(key) !== '1') { open = true; localStorage.setItem(key, '1'); } } catch (_) { open = true; }" x-show="open" x-cloak class="fixed z-[10000] top-4 left-1/2 -translate-x-1/2 w-[min(560px,calc(100vw-2rem))] rounded-2xl border border-amber-200 bg-white p-4 shadow-2xl dark:border-amber-800 dark:bg-gray-900">
        <div class="flex items-start gap-3">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Deadline upload Script Video Animator</p>
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">{{ $jumlah }} dari {{ $target }} script bulan {{ $bulan }} {{ $tahun }} sudah diunggah. Selesaikan sisa target hari ini.</p>
                <button type="button" wire:click="openNew" @click="open = false" class="mt-3 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">Upload script</button>
            </div>
            <button type="button" @click="open = false" aria-label="Tutup pengingat" class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">&times;</button>
        </div>
    </div>
    @endif
    <div data-script-video-push-notice class="flex flex-col gap-2 rounded-xl border border-blue-100 bg-blue-50/70 p-3 sm:flex-row sm:items-center sm:justify-between dark:border-blue-900/50 dark:bg-blue-950/20">
        <div>
            <p data-script-video-push-description class="text-xs text-blue-900 dark:text-blue-200">Aktifkan notifikasi agar pengingat deadline tampil di perangkat ini setiap hari pukul 09.00.</p>
            <p data-script-video-push-status aria-live="polite" class="mt-1 text-xs font-semibold text-blue-700 dark:text-blue-300">Memeriksa status notifikasi pada browser ini...</p>
        </div>
        <button type="button" data-script-video-push data-config-url="{{ route('hris.script-video.push-config') }}" data-subscribe-url="{{ route('hris.script-video.push-subscription') }}" disabled class="shrink-0 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60">Aktifkan notifikasi</button>
    </div>
    @endif

    <div x-cloak x-show="pushSuccessOpen" x-transition.opacity class="fixed inset-0 z-[11000] flex items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="push-success-title" @click.self="pushSuccessOpen = false" @keydown.escape.window="pushSuccessOpen = false">
        <div x-show="pushSuccessOpen" x-transition class="w-full max-w-md rounded-2xl border border-green-200 bg-white p-6 shadow-2xl dark:border-green-900 dark:bg-gray-900">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-3xl text-green-700 dark:bg-green-950 dark:text-green-300" aria-hidden="true">&#10003;</div>
            <h2 id="push-success-title" class="mt-4 text-center text-lg font-bold text-gray-900 dark:text-gray-100">Notifikasi berhasil diaktifkan</h2>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">Pengingat deadline Script Video Animator akan dikirim ke perangkat ini setiap hari pukul 09.00 selama target bulan ini belum tercapai.</p>
            <button type="button" @click="pushSuccessOpen = false" class="mt-6 w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Mengerti</button>
        </div>
    </div>

    @if($isRekap && !$detailUserId)
        @php
            $koordinatorBelumUpload = $rekap->filter(fn ($koordinator) => $koordinator->jumlah === 0);
        @endphp
        @if($koordinatorBelumUpload->isNotEmpty())
        <div wire:poll.30s role="status" class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/30">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-amber-900 dark:text-amber-200">Monitoring script: {{ $koordinatorBelumUpload->count() }} leader belum upload pada {{ $bulan }} {{ $tahun }}</p>
                <p class="mt-1 text-xs leading-relaxed text-amber-800 dark:text-amber-300">{{ $koordinatorBelumUpload->pluck('name')->join(', ') }}</p>
            </div>
        </div>
        @endif
    @endif

    {{-- Toolbar: Periode + Search + Aksi --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <x-input-label value="Bulan" />
                <select wire:model.live="bulan"
                        class="mt-1 block w-40 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    @foreach(\App\Models\ScriptVideoUpload::BULAN_LIST as $namaBulan)
                    <option value="{{ $namaBulan }}">{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label value="Tahun" />
                <select wire:model.live="tahun"
                        class="mt-1 block w-28 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    @foreach(range(now()->year - 2, now()->year + 1) as $tahunList)
                    <option value="{{ $tahunList }}">{{ $tahunList }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-1 lg:flex-none">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ $isRekap ? 'Cari nama koordinator...' : ($isVideoAnimator ? 'Cari judul / nama koordinator...' : 'Cari judul / keterangan...') }}"
                       class="w-full sm:w-64 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900/30 outline-none transition-all">
            </div>

            @unless($isRekap || $isVideoAnimator)
            <button wire:click="openNew" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span class="hidden sm:inline">Upload Script</span>
                <span class="sm:hidden">Upload</span>
            </button>
            @endunless
        </div>
    </div>

    {{-- Filter Kategori Game --}}
    @unless($isRekap)
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <button wire:click="$set('filterGame', '')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterGame === null ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-blue-300 dark:hover:border-blue-700' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
            Semua Game
        </button>
        @foreach(\App\Models\ScriptVideoUpload::GAME_CATEGORIES as $game)
        <button wire:click="$set('filterGame', '{{ $game }}')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterGame === $game ? 'bg-blue-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-blue-300 dark:hover:border-blue-700' }}">
            {{ $game }}
        </button>
        @endforeach
    </div>
    @endunless

    @if($isRekap && !$detailUserId)
    {{-- Rekap Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="badge-info">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $rekapStats['total'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Koordinator</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </div>
                <span class="badge-success">Tercapai</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $rekapStats['tercapai'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sudah mencapai target</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Belum</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $rekapStats['belum'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum capai target</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="badge-primary">Target</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $target }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konten / {{ $bulan }} {{ $tahun }}</p>
        </div>
    </div>

    {{-- Pengaturan Target --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Pengaturan Target</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Atur jumlah konten script wajib untuk periode {{ $bulan }} {{ $tahun }}</p>
            </div>
            <div class="flex items-end gap-3">
                <div>
                    <x-input-label value="Target Konten" />
                    <x-text-input type="number" wire:model.live="targetInput" min="1" max="1000" class="mt-1 block w-32" />
                </div>
                <button type="button" wire:click="saveTarget" class="btn-primary text-xs">Simpan Target</button>
            </div>
            @error('targetInput') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
    @endif

    @if($isVideoAnimator)
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 7.5a2.25 2.25 0 116.75 0m-6.75 0A2.25 2.25 0 005.625 7.5zm0 0h-2.25m6.75 0a2.25 2.25 0 100-4.5m0 4.5v1.5m6.75 0a2.25 2.25 0 116.75 0m-6.75 0A2.25 2.25 0 0018.375 7.5zm0 0H20.625M19.875 15a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zM12 15a2.625 2.625 0 105.25 0A2.625 2.625 0 0012 15z"/></svg>
                </div>
                <span class="badge-info">Total Script</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $jumlah }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dari semua koordinator</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="badge-success">Koordinator Mengirim</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $contributorCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sudah upload script</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="badge-primary">Periode</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $bulan }} {{ $tahun }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Script yang akan dieksekusi</p>
        </div>
    </div>
    @else
    {{-- Progress Card --}}
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center justify-between gap-4 mb-3">
            <div>
                @if($isRekap)
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Progress Rekap Koordinator</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $rekapStats['tercapai'] }} dari {{ $rekapStats['total'] }} koordinator sudah mencapai {{ $target }} konten</p>
                @else
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Progress Bulan Ini</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $jumlah }} dari {{ $target }} konten sudah diunggah</p>
                @endif
            </div>
            <span class="{{ $isComplete ? 'badge-success' : 'badge-warning' }}">
                {{ $isComplete ? 'Tercapai' : 'Dalam Proses' }}
            </span>
        </div>
        <div class="w-full h-2.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500 {{ $isComplete ? 'bg-emerald-500' : 'bg-blue-600' }}"
                 style="width: {{ $barPercent }}%"></div>
        </div>
    </div>
    @endif

    @if($isRekap)
        @if($detailUserId)
            {{-- Detail Upload Koordinator --}}
            <div class="flex items-center gap-3">
                <button wire:click="closeDetail" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Kembali ke Rekap
                </button>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $detailUser?->name ?? 'Data tidak ditemukan' }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $jumlah }} / {{ $target }} konten &middot; {{ $bulan }} {{ $tahun }}</p>
                </div>
            </div>

            @if($detailUser)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($uploads as $upload)
                @include('livewire.partials.script-video-card', ['upload' => $upload, 'manageable' => true, 'canToggle' => $isVideoAnimator || auth()->id() === $upload->user_id])
                @empty
                <div class="col-span-full">
                    <div class="flex flex-col items-center justify-center py-16 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 mb-4">
                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Script</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-center max-w-xs">Koordinator ini belum mengunggah script untuk periode {{ $bulan }} {{ $tahun }}</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if($uploads->hasPages())
            <div class="flex justify-center">
                <div class="px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                    {{ $uploads->links() }}
                </div>
            </div>
            @endif
            @endif
        @else
            {{-- Rekap Tabel --}}
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden dark:border-gray-800 dark:bg-gray-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Nama Koordinator</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Konten</th>
                                <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">M1</th>
                                <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">M2</th>
                                <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">M3</th>
                                <th class="px-5 py-3 text-center text-[11px] font-semibold uppercase tracking-wider text-gray-400">M4</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 w-64">Progress</th>
                                <th class="px-5 py-3 text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</th>
                                <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($rekap as $koordinator)
                            @php
                                $persen = $target > 0 ? min(100, round(($koordinator->jumlah / $target) * 100)) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-[11px] font-bold text-white uppercase">
                                            {{ \Illuminate\Support\Str::of($koordinator->name)->substr(0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $koordinator->name }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $koordinator->getRoleDisplayName() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                    {{ $koordinator->jumlah }} / {{ $target }}
                                </td>
                                @foreach(range(1, 4) as $m)
                                <td class="px-5 py-4">
                                    <div class="text-center text-sm font-semibold tabular-nums {{ $koordinator->weeklySummary[$m]['sisa'] === 0 ? 'text-emerald-600' : ($m === $mingguBerjalan ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-300') }}">
                                        {{ $koordinator->weeklySummary[$m]['terisi'] }}
                                    </div>
                                </td>
                                @endforeach
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                            <div class="h-full rounded-full transition-all {{ $koordinator->tercapai ? 'bg-emerald-500' : 'bg-blue-600' }}" style="width: {{ $persen }}%"></div>
                                        </div>
                                        <span class="text-xs tabular-nums text-gray-400 w-10 text-right">{{ $persen }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($koordinator->tercapai)
                                    <span class="badge-success">Tercapai</span>
                                    @else
                                    <span class="badge-warning">Belum</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button wire:click="openDetail({{ $koordinator->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-5 py-10 text-center text-sm text-gray-400">Tidak ada koordinator yang cocok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        {{-- Upload Koordinator --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($uploads as $upload)
            @include('livewire.partials.script-video-card', ['upload' => $upload, 'manageable' => !$isVideoAnimator, 'canToggle' => $isVideoAnimator || auth()->id() === $upload->user_id])
            @empty
            <div class="col-span-full">
                <div class="flex flex-col items-center justify-center py-16 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Script</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-center max-w-xs">
                        {{ $search ? 'Tidak ditemukan hasil untuk "' . $search . '"' : ($isVideoAnimator ? 'Belum ada script terkumpul untuk periode ini' : 'Klik "Upload Script" untuk mengunggah script konten pertama bulan ini') }}
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        @if($uploads->hasPages())
        <div class="flex justify-center">
            <div class="px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
                {{ $uploads->links() }}
            </div>
        </div>
        @endif
    @endif

    {{-- Modal Upload / Edit --}}
    <div x-data="{ open: $wire.entangle('showModal') }"
         x-show="open"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false; $wire.close()"
         @keydown.escape.window="open = false; $wire.close()">
        <div @click.stop
             x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Upload' }} Script Video Animator</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Periode {{ $bulan }} {{ $tahun }} &middot; target {{ $target }} konten</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Judul Konten *" />
                    <x-text-input type="text" wire:model.live="judul" class="mt-1 block w-full" placeholder="Contoh: Cara Setting Emulator MLBB" />
                    @error('judul') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Kategori Game" />
                    <select wire:model.live="kategoriGame"
                            class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Kategori Game --</option>
                        @foreach(\App\Models\ScriptVideoUpload::GAME_CATEGORIES as $kategori)
                        <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategoriGame') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Bulan *" />
                        <select wire:model.live="bulan"
                                class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            @foreach(\App\Models\ScriptVideoUpload::BULAN_LIST as $namaBulan)
                            <option value="{{ $namaBulan }}">{{ $namaBulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Tahun *" />
                        <select wire:model.live="tahun"
                                class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            @foreach(range(now()->year - 2, now()->year + 1) as $tahunList)
                            <option value="{{ $tahunList }}">{{ $tahunList }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <x-input-label value="File Script * (PDF, DOC, DOCX, JPG, PNG)" />
                    <input type="file" wire:model="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/20 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/30 transition-colors cursor-pointer" />
                    @error('file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="file" class="text-xs text-blue-600 mt-1.5">Mengunggah file...</div>
                    @if($editId)
                    <p class="text-xs text-gray-400 mt-1.5">Kosongkan jika tidak mengganti file.</p>
                    @endif
                </div>

                <div>
                    <x-input-label value="Keterangan" />
                    <textarea wire:model.live="keterangan" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Keterangan singkat script..."></textarea>
                    @error('keterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Reactivate Script Confirmation Modal --}}
    <div x-data="{ open: $wire.entangle('showActivateContentModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-[121] flex items-center justify-center overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelActivateContent()"
         @keydown.escape.window="open = false; $wire.cancelActivateContent()">
        <div @click.stop class="relative my-10 w-full max-w-sm rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 dark:bg-blue-900/30">
                <svg class="h-7 w-7 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 12.75l6 6 9-13.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-gray-100">Aktifkan Kembali Konten?</h3>
            <p class="mb-1 text-center text-sm font-medium text-gray-700 dark:text-gray-300">{{ $activateContentTitle }}</p>
            <p class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">Apakah Anda ingin mengaktifkan kembali konten ini?</p>
            <div class="flex items-center justify-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                <button type="button" @click="open = false; $wire.cancelActivateContent()" class="btn-secondary px-6 text-xs">Batal</button>
                <button type="button" wire:click="confirmActivateContent" class="btn-primary inline-flex items-center gap-2 px-6 text-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Ya, Aktifkan
                </button>
            </div>
        </div>
    </div>
    {{-- Delete Script Confirmation Modal --}}
    <div x-data="{ open: $wire.entangle('showDeleteConfirmModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-[120] flex items-center justify-center overflow-y-auto bg-gray-900/60 p-4 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelDelete()"
         @keydown.escape.window="open = false; $wire.cancelDelete()">
        <div @click.stop class="relative my-10 w-full max-w-sm rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30">
                <svg class="h-7 w-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-gray-100">Hapus Script Konten</h3>
            <p class="mb-1 text-center text-sm font-medium text-gray-700 dark:text-gray-300">{{ $deleteConfirmTitle }}</p>
            <p class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">Script dan file yang tersimpan akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex items-center justify-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                <button type="button" @click="open = false; $wire.cancelDelete()" class="btn-secondary px-6 text-xs">Batal</button>
                <button type="button" wire:click="executeDelete" class="btn-danger inline-flex items-center gap-2 px-6 text-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
