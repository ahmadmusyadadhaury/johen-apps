@php
    $canGive = $canGiveNotesByPosition[$cardNode['id']] ?? false;
@endphp
<div class="cursor-pointer w-44 sm:w-56 lg:w-64 p-2.5 sm:p-3 lg:p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm text-center hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-md hover:scale-105 active:scale-95 transition-all duration-200">
    <div class="flex items-center justify-center mb-1">
        @if($cardEmployee && $cardEmployee->foto_url)
            <img src="{{ $cardEmployee->foto_url }}" alt="{{ $cardEmployee->nama }}"
                 class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 rounded object-cover bg-gray-50 dark:bg-gray-700 shrink-0 shadow-sm">
        @else
            <div class="flex h-6 w-6 sm:h-7 sm:w-7 lg:h-8 lg:w-8 items-center justify-center rounded text-white font-bold text-[11px] sm:text-[12px] lg:text-[13px] shrink-0 shadow-sm
                {{ $level == 1 ? 'bg-emerald-500' : ($level == 2 ? 'bg-blue-500' : 'bg-purple-500') }}">
                {{ strtoupper(substr($cardEmployee ? $cardEmployee->nama : $cardNode['nama'], 0, 1)) }}
            </div>
        @endif
    </div>
    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 leading-snug">{{ $cardNode['nama'] }}</p>
    @if($cardEmployee)
        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 truncate">{{ $cardEmployee->nama }}</p>
    @else
        <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1 italic">Kosong</p>
    @endif

    {{-- Note buttons --}}
    <div class="flex items-center justify-center gap-1.5 sm:gap-2 mt-2 sm:mt-2.5">
        @if($canGive)
            <button wire:click.stop="openNoteModal({{ $cardNode['id'] }}, 'history')"
                    class="flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-[9px] sm:text-[10px] font-medium transition-colors"
                    title="Tambah evaluasi">
                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span class="hidden sm:inline">Evaluasi</span>
            </button>
            <button wire:click.stop="openNoteModal({{ $cardNode['id'] }}, 'history')"
                    class="flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-[9px] sm:text-[10px] font-medium transition-colors"
                    title="Riwayat evaluasi">
                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="hidden sm:inline">Riwayat Evaluasi</span>
            </button>
        @endif
        @if($cardNode['id'] == $myPositionId && !$canGive)
            <button wire:click.stop="openNoteModal({{ $cardNode['id'] }}, 'history')"
                    class="flex items-center gap-1 px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 hover:border-primary-400 dark:hover:border-primary-500 text-[9px] sm:text-[10px] font-medium transition-colors">
                Lihat Evaluasi
            </button>
        @endif
    </div>
</div>
