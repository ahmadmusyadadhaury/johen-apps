@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Ucapan Ulang Tahun</h1>
        <p class="text-xs text-gray-400 mt-0.5">Semua ucapan untuk {{ $employee->nama }}</p>
    </div>
@endpush

<div>
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="shrink-0">
                    @if($employee->foto_url)
                        <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="w-14 h-14 rounded-xl object-contain bg-gray-50 dark:bg-gray-800">
                    @else
                        <div class="flex w-14 h-14 items-center justify-center rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 font-display text-xl font-bold">
                            {{ strtoupper(substr($employee->nama, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">{{ $employee->nama }}</h2>
                    <p class="mt-0.5 text-xs text-gray-400">{{ $employee->position ?: '-' }}</p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-2">
                        @if($employee->tanggal_lahir)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 text-primary-700 ring-1 ring-primary-600/20 px-2.5 py-0.5 text-xs font-medium dark:bg-primary-950/40 dark:text-primary-400 dark:ring-primary-500/30">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                            {{ $employee->tanggal_lahir->isoFormat('D MMMM') }}
                        </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 px-2.5 py-0.5 text-xs font-semibold dark:bg-emerald-950/40 dark:text-emerald-400 dark:ring-emerald-500/30">
                            {{ $employee->birthday_wishes_count }} Ucapan
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('hris.birthday-wishes') }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali
            </a>
        </div>
    </section>

    <section class="mt-4 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Ucapan</h3>
            @if(count($availableYears) > 0)
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500 dark:text-gray-400">Tahun:</label>
                <select wire:model.live="selectedYear" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Pengirim</th>
                        <th class="px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Ucapan</th>
                        <th class="px-5 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Tanggal</th>
                        <th class="px-5 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($wishes as $wish)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="shrink-0">
                                    @if($wish->user?->employee?->foto_url)
                                        <img src="{{ $wish->user->employee->foto_url }}" alt="{{ $wish->user?->employee?->nama }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50 dark:bg-gray-800">
                                    @else
                                        <div class="flex w-8 h-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400 text-xs font-bold">
                                            {{ strtoupper(substr($wish->user?->employee?->nama ?? $wish->user?->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $wish->user?->employee?->nama ?? $wish->user?->name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $wish->user?->getRoleDisplayName() ?: '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300 max-w-xl">"{{ $wish->message }}"</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 text-right">{{ $wish->created_at->isoFormat('D MMM YYYY') }}</p>
                            <p class="text-[11px] text-gray-400 text-right mt-0.5">{{ $wish->created_at->isoFormat('HH:mm') }}</p>
                        </td>
                        <td class="px-5 py-4 text-center whitespace-nowrap">
                            <button wire:click="delete({{ $wish->id }})" wire:confirm="Hapus ucapan dari {{ $wish->user?->employee?->nama ?? $wish->user?->name }}?" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12">
                            <div class="text-center">
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Ucapan</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum ada ucapan untuk {{ $employee->nama }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
