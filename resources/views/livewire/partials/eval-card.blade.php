@php $value = ${$indicator['field']}; @endphp
<div id="card-{{ $indicator['field'] }}" class="eval-card scroll-mt-[200px] rounded-2xl bg-white dark:bg-gray-900 border transition-all duration-200 {{ $value !== null ? 'border-emerald-200 dark:border-emerald-800/60 ring-1 ring-emerald-100 dark:ring-emerald-900/40' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
    <div class="p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 leading-snug">{{ $indicator['label'] }}</h4>
                <p class="text-xs text-gray-400 mt-1 leading-relaxed">{{ $indicator['desc'] }}</p>
            </div>
            <span class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-bold {{ $value !== null ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500' }}">
                {{ $indicator['weight'] }}%
            </span>
        </div>

        <div class="mt-4">
            <p class="text-[11px] font-semibold uppercase tracking-widest text-gray-400 mb-2.5">Pilih penilaian</p>
            <div class="grid grid-cols-5 gap-2" wire:loading.class="opacity-60">
                @for($s = 0; $s <= 4; $s++)
                    <button type="button"
                            wire:click="$set('{{ $indicator['field'] }}', {{ $s }})"
                            class="relative flex flex-col items-center justify-center gap-0.5 rounded-xl border-2 py-3 text-sm font-bold transition-all duration-150 {{ $value === $s ? 'border-emerald-500 bg-emerald-500 text-white shadow-md shadow-emerald-500/25 scale-[1.04]' : 'border-gray-150 dark:border-gray-750 text-gray-500 dark:text-gray-400 hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 hover:text-emerald-600 dark:hover:text-emerald-400' }}">
                        <span class="text-base leading-none">{{ $s }}</span>
                        @if($value === $s)
                            <svg class="w-3 h-3 text-white/80" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                        @endif
                    </button>
                @endfor
            </div>

            <div class="mt-2.5 flex items-center justify-between text-[11px]">
                <span class="{{ ($value ?? 5) <= 1 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">0 Kurang</span>
                @if(isset($indicator['scale']) && $value !== null && isset($indicator['scale'][$value]))
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $indicator['scale'][$value] }}</span>
                @endif
                <span class="{{ ($value ?? -1) >= 4 ? 'text-emerald-600 font-semibold' : 'text-gray-400' }}">4 Sangat Tinggi</span>
            </div>
        </div>
    </div>
</div>
