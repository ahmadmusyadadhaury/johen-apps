@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Ucapan Ulang Tahun</h1>
        <p class="text-xs text-gray-400 mt-0.5">Pilih karyawan untuk melihat detail ucapan</p>
    </div>
@endpush

<div>
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Daftar Karyawan</h2>
                <p class="mt-0.5 text-xs text-gray-400">Karyawan yang menerima ucapan ulang tahun</p>
            </div>
            <div class="relative w-full sm:w-64">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / jabatan..." class="input-field pl-9 py-2 text-xs">
            </div>
        </div>

        <div class="p-5">
            @forelse($employees as $emp)
            <a href="{{ route('hris.birthday-wishes.detail', $emp) }}" class="group flex items-center gap-4 rounded-xl border border-gray-100 dark:border-gray-800 p-4 transition-colors hover:border-primary-200 hover:bg-primary-50/40 dark:hover:border-primary-800 dark:hover:bg-primary-950/20">
                <div class="shrink-0">
                    @if($emp->foto_url)
                        <img src="{{ $emp->foto_url }}" alt="{{ $emp->nama }}" class="w-12 h-12 rounded-xl object-contain bg-gray-50 dark:bg-gray-800">
                    @else
                        <div class="flex w-12 h-12 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 font-display text-lg font-bold">
                            {{ strtoupper(substr($emp->nama, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $emp->nama }}</p>
                    <p class="mt-0.5 truncate text-xs text-gray-400">{{ $emp->position ?: '-' }}</p>
                    <p class="mt-1 text-[11px] text-gray-400">
                        @if($emp->tanggal_lahir)
                            Ulang Tahun {{ $emp->tanggal_lahir->isoFormat('D MMM') }}
                        @endif
                        @if($emp->last_wish_at)
                            · Terakhir {{ \Illuminate\Support\Carbon::parse($emp->last_wish_at)->isoFormat('D MMM YYYY') }}
                        @endif
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 text-primary-700 ring-1 ring-primary-600/20 px-2.5 py-1 text-xs font-semibold dark:bg-primary-950/40 dark:text-primary-400 dark:ring-primary-500/30">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                        {{ $emp->birthday_wishes_count }} Ucapan
                    </span>
                    <svg class="w-4 h-4 text-gray-300 transition-transform group-hover:translate-x-0.5 group-hover:text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>
            @empty
            <div class="py-12 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Ucapan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $search ? 'Tidak ditemukan hasil untuk "' . $search . '"' : 'Belum ada karyawan yang menerima ucapan ulang tahun' }}</p>
            </div>
            @endforelse
        </div>
    </section>

    @if($employees->hasPages())
    <div class="mt-4 px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        {{ $employees->links() }}
    </div>
    @endif
</div>
