@php($complete = $filledCount === $config::indicatorCount())
<div class="mt-3">
    <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500 {{ $complete ? 'bg-emerald-500' : 'bg-emerald-400/70' }}" style="width: {{ $finalPercent ?? 0 }}%"></div>
    </div>
    <div class="mt-1.5 flex items-center justify-between text-[11px]">
        <span class="font-bold tabular-nums {{ $complete ? 'text-emerald-600' : 'text-gray-400' }}">{{ number_format($finalPercent ?? 0, 1) }}%</span>
        <span class="text-gray-400">{{ $complete ? 'Semua indikator terisi' : 'Menunggu ' . ($config::indicatorCount() - $filledCount) . ' indikator' }}</span>
    </div>
</div>

<div class="mt-4 space-y-3 px-0">
    @foreach($categoryScores as $key => $cat)
        <div>
            <div class="flex items-center justify-between text-xs">
                <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $cat['label'] }} <span class="text-gray-300 dark:text-gray-600 font-medium">· {{ $cat['weight'] }}%</span></span>
                <span class="font-bold tabular-nums {{ $cat['score'] !== null ? 'text-gray-800 dark:text-gray-100' : 'text-gray-300 dark:text-gray-600' }}">{{ $cat['score'] !== null ? number_format($cat['score'], 1) : '-' }}</span>
            </div>
            <div class="mt-1 h-1 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full rounded-full bg-emerald-400 transition-all duration-500" style="width: {{ ($cat['score'] ?? 0) / 4 * 100 }}%"></div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-800 pt-3 pb-4 px-0">
    @if(!$complete)
        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-amber-600"><span class="h-2 w-2 rounded-full bg-amber-400"></span> Belum lengkap</span>
    @elseif(($finalPercent ?? 0) >= 50.25)
        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-600"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ $scoreLabel }}</span>
    @else
        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-500"><span class="h-2 w-2 rounded-full bg-red-400"></span> {{ $scoreLabel }}</span>
    @endif
    <span class="text-[10px] text-gray-400">Lulus ≥ 2.01</span>
</div>
