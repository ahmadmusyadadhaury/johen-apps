@push('topbar-left')
        <div>
            <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Kalender Event</h1>
            <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola kegiatan event</p>
        </div>
    @endpush

<div>

    <div class="mb-4 flex flex-col gap-3 sm:mb-6 sm:flex-row sm:items-center sm:justify-end sm:gap-2">
        <div class="flex items-center justify-between gap-2 sm:justify-start">
            <button wire:click="prevMonth" aria-label="Bulan sebelumnya" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-800">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h2 aria-live="polite" class="min-w-0 flex-1 text-center text-sm font-semibold text-gray-900 dark:text-gray-100 sm:min-w-[140px] sm:flex-none sm:text-base">{{ $monthName }}</h2>
            <button wire:click="nextMonth" aria-label="Bulan berikutnya" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-800">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <button wire:click="goToday" class="min-h-11 rounded-xl border border-gray-200 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Hari Ini</button>
        </div>
@unless(auth()->user()->isReadOnlyWorkspace())
        <button wire:click="openNew" class="inline-flex min-h-11 w-full items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-3 py-2 text-xs font-medium text-white shadow-sm hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Event
        </button>
        @endunless
    </div>

    <div class="card p-2 sm:p-5">
        @php
            $dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        @endphp

        <div role="grid" aria-label="Kalender event bulan {{ $monthName }}" class="grid grid-cols-7 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
            @foreach($dayLabels as $label)
                <div role="columnheader" class="border-b border-gray-200 bg-gray-50 px-1 py-2 text-center text-[10px] font-semibold text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:px-3 sm:text-xs"><span class="sm:hidden" aria-hidden="true">{{ mb_substr($label, 0, 1) }}</span><span class="hidden sm:inline">{{ $label }}</span><span class="sr-only sm:hidden">{{ ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][$loop->index] }}</span></div>
            @endforeach

            @foreach($days as $date)
                @php
                    $dateKey = $date->format('Y-m-d');
                    $isToday = $date->isToday();
                    $isCurrentMonth = $date->month === $currentMonth;
                    $isSelected = $dateKey === $selectedDate;
                    $dayEvents = $events[$dateKey] ?? collect();
                @endphp
                <button type="button" role="gridcell" wire:click="selectDate('{{ $dateKey }}')" aria-label="{{ $date->translatedFormat('l, d F Y') }}{{ $dayEvents->count() ? ', '.$dayEvents->count().' event' : ', tidak ada event' }}" aria-pressed="{{ $isSelected ? 'true' : 'false' }}" class="min-h-[58px] w-full border-b border-r border-gray-100 px-1.5 py-1 text-left align-top focus-visible:z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-500 dark:border-gray-800 sm:min-h-[108px] sm:px-2 sm:py-1.5 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-950' }} {{ $isToday ? 'ring-2 ring-blue-500 ring-inset' : '' }} {{ $isSelected ? 'bg-blue-50 dark:bg-blue-900/20' : '' }} hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <span class="mb-1 flex items-center justify-between text-[11px] font-medium sm:text-xs">
                        <span class="{{ $isToday ? 'text-blue-600' : ($isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600') }}">{{ $date->day }}</span>
                        @if($dayEvents->count() > 0)
                        <span class="rounded-full bg-blue-50 px-1.5 py-0.5 text-[9px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $dayEvents->count() }}</span>
                        @endif
                    </span>
                    <div class="hidden space-y-0.5 sm:block">
                        @foreach($dayEvents->take(3) as $event)
                            <div class="text-[10px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 truncate font-medium">
                                @if($event->waktu_mulai) {{ $event->waktu_mulai }}@if($event->waktu_selesai)-{{ $event->waktu_selesai }}@endif @endif {{ $event->kegiatan }}
                            </div>
                        @endforeach
                        @if($dayEvents->count() > 3)
                            <p class="text-[10px] text-gray-400 pl-1">+{{ $dayEvents->count() - 3 }} lainnya</p>
                        @endif
                    </div>
                    @if($dayEvents->count() > 0)<span aria-hidden="true" class="mt-1 flex justify-center gap-0.5 sm:hidden">@foreach($dayEvents->take(3) as $event)<span class="h-1 w-1 rounded-full bg-blue-600"></span>@endforeach</span>@endif
                </button>
            @endforeach
        </div>

    </div>

    {{-- Detail Modal --}}
    <div x-data="{ open: false }"
         x-init="$watch('$wire.showDetailModal', value => open = value)"
         x-show="open" x-cloak
         role="presentation" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/60 p-2 backdrop-blur-sm sm:p-4"
         @click="closeDetailModal">
        <div @click.stop role="dialog" aria-modal="true" aria-labelledby="event-day-title" class="relative my-auto w-full max-w-lg rounded-2xl bg-white p-4 shadow-2xl dark:bg-gray-800 sm:my-10 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 id="event-day-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') : '' }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar event</p>
                </div>
                <button wire:click="closeDetailModal" aria-label="Tutup daftar event" class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-gray-400 hover:bg-gray-100 hover:text-gray-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3">
                @forelse($selectedEvents as $event)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-gray-100 dark:border-gray-800">
                    <div class="flex-shrink-0 w-1 h-10 rounded-full bg-blue-500"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $event->kegiatan }}
                            @if($event->waktu_mulai) <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">· {{ $event->waktu_mulai }}@if($event->waktu_selesai)-{{ $event->waktu_selesai }}@endif</span> @endif
                        </p>
                        @if($event->keterangan)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $event->keterangan }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        @unless(auth()->user()->isReadOnlyWorkspace())
                        <button wire:click="openEdit({{ $event->id }})" @click="open = false" class="p-1.5 rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        </button>
                        <button wire:click="delete({{ $event->id }})" wire:confirm="Hapus event ini?" class="p-1.5 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        </button>
                        @endunless
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Belum Ada Event</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada event di tanggal ini</p>
                </div>
                @endforelse
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button wire:click="openNew" @click="open = false" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-xs font-medium text-white shadow-sm hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Event
                </button>
            </div>
        </div>
    </div>

    {{-- Event Modal --}}
    <div x-data="{ open: false }"
         x-init="$watch('$wire.showForm', value => open = value)"
         x-show="open" x-cloak
         role="presentation" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/60 p-2 backdrop-blur-sm sm:p-4"
         @click="open = false">
        <div @click.stop role="dialog" aria-modal="true" aria-labelledby="event-form-title" class="relative my-auto w-full max-w-lg rounded-2xl bg-white p-4 shadow-2xl dark:bg-gray-800 sm:my-10 sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 id="event-form-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Event</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') : '' }}
                    </p>
                </div>
                <button wire:click="closeForm" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Kegiatan *" />
                    <x-text-input type="text" wire:model="kegiatan" class="mt-1 block w-full" placeholder="Nama kegiatan" />
                    @error('kegiatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label value="Mulai" />
                        <x-text-input type="time" wire:model="waktuMulai" class="mt-1 block w-full" />
                        @error('waktuMulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Selesai" />
                        <x-text-input type="time" wire:model="waktuSelesai" class="mt-1 block w-full" />
                        @error('waktuSelesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <x-input-label value="Keterangan" />
                    <textarea wire:model="keterangan" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Keterangan tambahan..."></textarea>
                    @error('keterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeForm" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
