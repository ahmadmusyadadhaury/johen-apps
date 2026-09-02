@php
    $value = ${$indicator['field']};
    $sel = [
        0 => 'border-red-500 bg-red-500 text-white shadow-md shadow-red-500/25 scale-[1.05]',
        1 => 'border-orange-500 bg-orange-500 text-white shadow-md shadow-orange-500/25 scale-[1.05]',
        2 => 'border-amber-500 bg-amber-500 text-white shadow-md shadow-amber-500/25 scale-[1.05]',
        3 => 'border-blue-500 bg-blue-500 text-white shadow-md shadow-blue-500/25 scale-[1.05]',
        4 => 'border-emerald-500 bg-emerald-500 text-white shadow-md shadow-emerald-500/25 scale-[1.05]',
    ];
    $lblCls = [
        0 => 'text-red-500 dark:text-red-400',
        1 => 'text-orange-500 dark:text-orange-400',
        2 => 'text-amber-600 dark:text-amber-400',
        3 => 'text-blue-600 dark:text-blue-400',
        4 => 'text-emerald-600 dark:text-emerald-400',
    ];
    $words = ['Kurang', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik'];
    $chosenLabel = $value !== null
        ? (isset($indicator['scale']) ? $indicator['scale'][$value] : $words[$value])
        : null;
@endphp
<div id="card-{{ $indicator['field'] }}" class="eval-card scroll-mt-[200px] rounded-2xl bg-white dark:bg-gray-900 border transition-all duration-200 {{ $value !== null ? 'border-emerald-200 dark:border-emerald-800/60 ring-1 ring-emerald-100 dark:ring-emerald-900/40' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
    <div class="p-4 sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0 flex-1">
                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 leading-snug">{{ $indicator['label'] }}</h4>
                <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $indicator['desc'] }}</p>
            </div>
            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold {{ $value !== null ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500' }}">
                {{ $indicator['weight'] }}%
            </span>
        </div>

        <div class="mt-4 flex flex-col gap-2.5 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex items-center gap-1.5" wire:loading.class="opacity-60">
                @for($s = 0; $s <= 4; $s++)
                    <button type="button"
                            wire:click="$set('{{ $indicator['field'] }}', {{ $s }})"
                            aria-label="Skor {{ $s }}"
                            class="relative flex h-10 w-10 items-center justify-center rounded-xl border-2 text-sm font-extrabold transition-all duration-150 {{ $value === $s ? $sel[$s] : 'border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 hover:border-gray-300 dark:hover:border-gray-500 hover:text-gray-600 dark:hover:text-gray-300' }}">
                        {{ $s }}
                    </button>
                @endfor
            </div>
            <div class="shrink-0 sm:text-right">
                <p class="text-[11px] font-bold uppercase tracking-widest {{ $value !== null ? $lblCls[$value] : 'text-gray-300 dark:text-gray-600' }}">{{ $chosenLabel ?? 'Belum dinilai' }}</p>
                <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-0.5">Skala 0 – 4</p>
            </div>
        </div>
    </div>
</div>