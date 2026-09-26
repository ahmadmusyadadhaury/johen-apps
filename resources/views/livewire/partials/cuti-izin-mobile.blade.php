@php
    $currentUser = auth()->user();
@endphp

<section class="md:hidden space-y-5" aria-label="Cuti dan izin mobile">
    @if(! $currentUser->isGmCeo() && ($currentUser->isKoordinatorIt() || $currentUser->isKoordinatorCreative() || $currentUser->isKoordinatorAdmin() || $currentUser->isKoordinatorStock() || $currentUser->isKoordinatorPubg() || $currentUser->isKoordinatorFf() || $currentUser->isKoordinatorMlbb() || $currentUser->isKoordinatorEfootball() || $currentUser->isKoordinatorValorant() || $currentUser->isKoordinatorRoblox() || $currentUser->isKoordinatorMonkeyPubg() || $currentUser->isKoordinatorFcMobile() || $currentUser->isHeadOfStore() || $currentUser->isSuperAdmin()))
        <div class="grid grid-cols-2 gap-1 rounded-2xl bg-gray-100 p-1 dark:bg-gray-800" role="tablist" aria-label="Jenis pengajuan">
            <button type="button" role="tab" aria-controls="cuti-izin-mobile-list" aria-selected="{{ $tab === 'saya' ? 'true' : 'false' }}" wire:click="$set('tab', 'saya')"
                    class="flex min-h-12 items-center justify-center gap-2 rounded-xl px-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $tab === 'saya' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white/70 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.1a7.5 7.5 0 0115 0A17.9 17.9 0 0112 21.75c-2.7 0-5.25-.6-7.5-1.65z"/></svg>
                Pengajuan Saya
            </button>
            <button type="button" role="tab" aria-controls="cuti-izin-mobile-list" aria-selected="{{ $tab === 'tim' ? 'true' : 'false' }}" wire:click="$set('tab', 'tim')"
                    class="flex min-h-12 items-center justify-center gap-2 rounded-xl px-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-primary-500 {{ $tab === 'tim' ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white/70 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75a7.5 7.5 0 00-9 0m11.25-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM6.75 9.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM12 12.75a3.75 3.75 0 110-7.5 3.75 3.75 0 010 7.5z"/></svg>
                <span>Pengajuan Tim</span>
                @if($timMenungguCount > 0)
                    <span class="inline-flex min-w-5 items-center justify-center rounded-full px-1.5 py-0.5 text-[10px] font-bold {{ $tab === 'tim' ? 'bg-white/20 text-white' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-200' }}">{{ $timMenungguCount }}</span>
                @endif
            </button>
        </div>
    @endif

    <section aria-label="Daftar pengajuan cuti dan izin" class="space-y-3">
        @if($userEmployee && ! $currentUser->isGmCeo())
            <button type="button" wire:click="openPengajuanModal" class="inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/></svg>
                Ajukan Cuti / Izin / Jatah
            </button>
        @endif

        <div class="relative">
            <label for="mobile-cuti-search" class="sr-only">Cari nama karyawan atau NIK</label>
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="M16 16l4.5 4.5"/></svg>
            <input id="mobile-cuti-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIK..."
                   class="min-h-12 w-full rounded-xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:focus:ring-primary-900/50">
        </div>
        <button type="button" @click="filtersOpen = true" aria-haspopup="dialog" class="flex min-h-11 w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 text-sm font-semibold text-gray-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
            <span class="inline-flex items-center gap-2"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10m-7 6h4"/></svg>Filter</span>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $filterJenis ? ($filterJenis === 'cuti_tahunan' ? 'Cuti Tahunan' : ($filterJenis === 'jatah' ? 'Jatah Libur' : 'Izin')) : 'Semua Jenis' }} · {{ $filterStatus ? ucfirst($filterStatus) : 'Semua Status' }}</span>
        </button>

        <div id="cuti-izin-mobile-list" role="tabpanel" class="space-y-3">
            @forelse($leaveRequests as $lr)
                @php
                    $isAtasan = $userEmployee && $userEmployee->id === $lr->atasan_id;
                    $isAtasan2 = $userEmployee && $userEmployee->id === $lr->atasan2_id;
                    $canApproveKoor = $isAtasan;
                    $canApproveAtasan2 = $isAtasan2 && (! $user->isManager() || $lr->persetujuan_koor === 'disetujui');
                    $canApproveHr = $lihatSemua && ! $user->isGmCeo() && ! $user->isKoordinatorIt() && ! $user->isKoordinatorAdmin() && ! $user->isKoordinatorStock() && ! $user->isKoordinatorPubg() && ! $user->isKoordinatorFf() && ! $user->isKoordinatorMlbb() && ! $user->isKoordinatorEfootball() && ! $user->isKoordinatorValorant() && $lr->persetujuan_atasan2 === 'disetujui' && ($lr->tanggal_selesai->isPast() || $user->isSuperAdmin());
                    $requiresPin = $user->requiresPinApproval();
                    $mobileStatus = in_array('ditolak', [$lr->persetujuan_koor, $lr->persetujuan_atasan2, $lr->persetujuan_hr], true)
                        ? 'Ditolak'
                        : (in_array('menunggu', [$lr->persetujuan_koor, $lr->persetujuan_atasan2, $lr->persetujuan_hr], true) ? 'Menunggu' : 'Disetujui');
                    $mobileStatusClasses = $mobileStatus === 'Disetujui'
                        ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200'
                        : ($mobileStatus === 'Ditolak' ? 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-200' : 'bg-amber-50 text-amber-800 dark:bg-amber-900/30 dark:text-amber-200');
                    $mobileJenis = $lr->jenis === 'cuti_tahunan' ? 'Cuti Tahunan' : ($lr->jenis === 'jatah' ? 'Jatah Libur' : 'Izin');
                    $mobileEmployeeName = $lr->employee?->nama ?? '-';
                    $mobilePosition = $lr->selectedPosition?->nama ?? $lr->employee?->position ?? '-';
                @endphp
                <button type="button"
                        aria-label="Buka detail pengajuan {{ $mobileEmployeeName }}, {{ $mobileJenis }}, {{ $mobileStatus }}"
                        @click="selected = @js([
                            'id' => $lr->id,
                            'name' => $mobileEmployeeName,
                            'position' => $mobilePosition,
                            'photo' => $lr->employee?->foto_url,
                            'jenis' => $mobileJenis,
                            'start' => $lr->tanggal_mulai->isoFormat('D MMM YYYY'),
                            'end' => $lr->tanggal_selesai->isoFormat('D MMM YYYY'),
                            'duration' => $lr->durasi,
                            'reason' => $lr->keterangan ?: '-',
                            'status' => $mobileStatus,
                            'statusClasses' => $mobileStatusClasses,
                            'atasan' => $lr->atasan?->nama ?? '-',
                            'atasan2' => $lr->atasan2?->nama ?? '-',
                            'koorStatus' => ucfirst($lr->persetujuan_koor),
                            'atasan2Status' => $lr->atasan2_id ? ucfirst($lr->persetujuan_atasan2) : 'Tidak diperlukan',
                            'hrStatus' => ucfirst($lr->persetujuan_hr),
                            'approvalNote' => $lr->catatan_persetujuan ?: '-',
                            'canApproveKoor' => $canApproveKoor && $lr->persetujuan_koor === 'menunggu',
                            'canApproveAtasan2' => $canApproveAtasan2 && $lr->persetujuan_atasan2 === 'menunggu',
                            'canApproveHr' => $canApproveHr && $lr->persetujuan_hr === 'menunggu',
                            'requiresPin' => $requiresPin,
                            'canDelete' => $user->isSuperAdmin(),
                        ]); detailOpen = true"
                        class="block w-full rounded-2xl border border-gray-100 bg-white p-4 text-left shadow-sm transition hover:border-primary-200 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-700">
                    <div class="flex items-start gap-3">
                        @if($lr->employee?->foto_url)
                            <img src="{{ $lr->employee->foto_url }}" alt="" class="h-12 w-12 shrink-0 rounded-xl bg-gray-100 object-cover dark:bg-gray-800">
                        @else
                            <span aria-hidden="true" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-base font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-200">{{ strtoupper(substr($mobileEmployeeName, 0, 1)) }}</span>
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-bold text-gray-900 dark:text-gray-100">{{ $mobileEmployeeName }}</span>
                            <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">{{ $mobilePosition }}</span>
                        </span>
                        <span class="inline-flex min-h-7 shrink-0 items-center rounded-full px-2.5 text-[11px] font-semibold {{ $mobileStatusClasses }}">{{ $mobileStatus }}</span>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-800 dark:bg-primary-900/30 dark:text-primary-200">{{ $mobileJenis }}</span>
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $lr->tanggal_mulai->isoFormat('D MMM') }} – {{ $lr->tanggal_selesai->isoFormat('D MMM YYYY') }}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-3 dark:border-gray-800">
                        <span class="text-xs text-gray-500 dark:text-gray-400">Durasi <strong class="ml-1 text-gray-800 dark:text-gray-200">{{ $lr->durasi }}</strong></span>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-primary-700 dark:text-primary-300">Lihat detail <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></span>
                    </div>
                </button>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white px-5 py-10 text-center dark:border-gray-700 dark:bg-gray-900">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-300"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8m-8 4h8m-8 4h5M6 3.75h8.25L18 7.5v12.75a1.5 1.5 0 01-1.5 1.5h-9A1.5 1.5 0 016 20.25V5.25a1.5 1.5 0 011.5-1.5z"/></svg></span>
                    <h3 class="mt-3 text-sm font-bold text-gray-900 dark:text-gray-100">Belum ada pengajuan</h3>
                    <p class="mx-auto mt-1 max-w-xs text-sm text-gray-500 dark:text-gray-400">{{ $search || $filterJenis || $filterStatus ? 'Belum ada pengajuan yang sesuai dengan filter.' : 'Belum ada pengajuan cuti atau izin yang sesuai.' }}</p>
                    @if($search || $filterJenis || $filterStatus)
                        <button type="button" @click="$wire.set('search', ''); $wire.set('filterJenis', ''); $wire.set('filterStatus', '')" class="mt-4 inline-flex min-h-11 items-center justify-center rounded-xl bg-primary-50 px-4 text-sm font-semibold text-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-primary-900/30 dark:text-primary-200">Reset Filter</button>
                    @endif
                </div>
            @endforelse
        </div>
        @if($leaveRequests->hasPages())
            <div class="rounded-xl border border-gray-100 bg-white px-3 py-2 dark:border-gray-800 dark:bg-gray-900">{{ $leaveRequests->links() }}</div>
        @endif
    </section>
</section>

<div x-show="filtersOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[60] flex items-end bg-gray-950/50 md:hidden" role="dialog" aria-modal="true" aria-labelledby="mobile-filter-title" @click.self="filtersOpen = false" @keydown.escape.window="filtersOpen = false">
    <section class="w-full rounded-t-3xl bg-white p-5 pb-[calc(1.25rem+env(safe-area-inset-bottom))] shadow-2xl dark:bg-gray-900">
        <div class="mx-auto mb-4 h-1 w-10 rounded-full bg-gray-300 dark:bg-gray-700"></div>
        <div class="mb-5 flex items-center justify-between">
            <div><h2 id="mobile-filter-title" class="text-base font-bold text-gray-900 dark:text-gray-100">Filter Pengajuan</h2><p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih jenis dan status pengajuan</p></div>
            <button type="button" @click="filtersOpen = false" aria-label="Tutup filter" class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:hover:bg-gray-800"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg></button>
        </div>
        <div class="space-y-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Jenis Pengajuan
                <select wire:model.live="filterJenis" class="mt-2 min-h-12 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua Jenis</option><option value="cuti_tahunan">Cuti Tahunan</option><option value="izin">Izin</option><option value="jatah">Jatah Libur</option>
                </select>
            </label>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Status
                <select wire:model.live="filterStatus" class="mt-2 min-h-12 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-900 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Semua Status</option><option value="menunggu">Menunggu</option><option value="disetujui">Disetujui</option><option value="ditolak">Ditolak</option>
                </select>
            </label>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3">
            <button type="button" @click="$wire.set('filterJenis', ''); $wire.set('filterStatus', '')" class="min-h-12 rounded-xl border border-gray-200 px-4 text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-700 dark:text-gray-200">Reset</button>
            <button type="button" @click="filtersOpen = false" class="min-h-12 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">Terapkan</button>
        </div>
    </section>
</div>

<div x-show="detailOpen" x-cloak x-transition.opacity class="fixed inset-0 z-40 overflow-y-auto bg-gray-50 dark:bg-gray-950 md:hidden" role="dialog" aria-modal="true" aria-labelledby="mobile-detail-title" @keydown.escape.window="detailOpen = false">
    <div class="mx-auto min-h-full max-w-xl px-4 pb-8 pt-4">
        <header class="sticky top-0 z-10 -mx-4 mb-5 flex items-center gap-3 border-b border-gray-100 bg-gray-50/95 px-4 py-2 backdrop-blur dark:border-gray-800 dark:bg-gray-950/95">
            <button type="button" @click="detailOpen = false" aria-label="Kembali ke daftar pengajuan" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-200 dark:hover:bg-gray-800"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>
            <h2 id="mobile-detail-title" class="text-base font-bold text-gray-900 dark:text-gray-100">Detail Pengajuan</h2>
        </header>
        <div class="space-y-4" x-show="selected">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status Pengajuan</p>
                <span class="mt-3 inline-flex min-h-8 items-center rounded-full px-3 text-sm font-bold" :class="selected.statusClasses" x-text="selected.status"></span>
            </section>
            <section class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <template x-if="selected.photo"><img :src="selected.photo" :alt="'Foto ' + selected.name" class="h-16 w-16 rounded-2xl bg-gray-100 object-cover dark:bg-gray-800"></template>
                <template x-if="!selected.photo"><span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-100 text-xl font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-200" x-text="selected.name.charAt(0).toUpperCase()"></span></template>
                <div class="min-w-0"><p class="break-words text-base font-bold text-gray-900 dark:text-gray-100" x-text="selected.name"></p><p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="selected.position"></p></div>
            </section>
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-bold text-gray-900 dark:text-gray-100">Informasi Pengajuan</h3>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Jenis</dt><dd class="text-right font-semibold text-gray-900 dark:text-gray-100" x-text="selected.jenis"></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Tanggal</dt><dd class="text-right font-semibold text-gray-900 dark:text-gray-100"><span x-text="selected.start"></span><span> – </span><span x-text="selected.end"></span></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Durasi</dt><dd class="text-right font-semibold text-gray-900 dark:text-gray-100" x-text="selected.duration"></dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Alasan</dt><dd class="mt-1 whitespace-pre-line font-medium text-gray-900 dark:text-gray-100" x-text="selected.reason"></dd></div>
                </dl>
            </section>
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-bold text-gray-900 dark:text-gray-100">Atasan &amp; Persetujuan</h3>
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Atasan 1</dt><dd class="text-right font-medium text-gray-900 dark:text-gray-100" x-text="selected.atasan"></dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Atasan 2</dt><dd class="text-right font-medium text-gray-900 dark:text-gray-100" x-text="selected.atasan2"></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Persetujuan Atasan 1</dt><dd class="font-semibold text-gray-900 dark:text-gray-100" x-text="selected.koorStatus"></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Persetujuan Atasan 2</dt><dd class="font-semibold text-gray-900 dark:text-gray-100" x-text="selected.atasan2Status"></dd></div>
                    <div class="flex items-center justify-between gap-4"><dt class="text-gray-500 dark:text-gray-400">Persetujuan HR</dt><dd class="font-semibold text-gray-900 dark:text-gray-100" x-text="selected.hrStatus"></dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">Catatan Persetujuan</dt><dd class="mt-1 whitespace-pre-line font-medium text-gray-900 dark:text-gray-100" x-text="selected.approvalNote"></dd></div>
                </dl>
            </section>
            <section x-show="selected.canApproveKoor || selected.canApproveAtasan2 || selected.canApproveHr || selected.canDelete" class="grid grid-cols-2 gap-3 pb-4">
                <template x-for="level in [{key:'canApproveKoor', value:'persetujuan_koor'}, {key:'canApproveAtasan2', value:'persetujuan_atasan2'}, {key:'canApproveHr', value:'persetujuan_hr'}]" :key="level.value">
                    <div x-show="selected[level.key]" class="col-span-2 grid grid-cols-2 gap-3">
                        <button type="button" @click="if (selected.requiresPin) { $wire.tolak(selected.id, level.value) } else { confirmAction = true; confirmTitle = 'Tolak Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?'; confirmHandler = () => $wire.tolak(selected.id, level.value) }" class="min-h-12 rounded-xl border border-red-200 bg-white px-3 text-sm font-bold text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-900 dark:bg-gray-900 dark:text-red-300">Tolak</button>
                        <button type="button" @click="if (selected.requiresPin) { $wire.setujui(selected.id, level.value) } else { confirmAction = true; confirmTitle = 'Setujui Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?'; confirmHandler = () => $wire.setujui(selected.id, level.value) }" class="min-h-12 rounded-xl bg-emerald-600 px-3 text-sm font-bold text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">Setujui</button>
                    </div>
                </template>
                <button x-show="selected.canDelete" type="button" @click="$wire.confirmDelete(selected.id)" class="col-span-2 min-h-12 rounded-xl border border-red-200 px-4 text-sm font-bold text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 dark:border-red-900 dark:text-red-300">Hapus Pengajuan</button>
            </section>
        </div>
    </div>
</div>
