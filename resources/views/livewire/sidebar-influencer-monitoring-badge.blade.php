<span wire:poll.30s class="inline-flex shrink-0 items-center" aria-live="polite" aria-atomic="true" aria-label="{{ $count }} influencer belum diisi monitoring bulan ini">
    @if($count > 0)
        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-bold tabular-nums leading-none text-white shadow-sm">{{ $count > 99 ? '99+' : $count }}</span>
    @endif
</span>
