<span wire:poll.20s.visible class="ml-auto inline-flex shrink-0" aria-live="polite" aria-atomic="true" aria-label="{{ $count }} pesan chat belum dibaca">
    @if($count > 0)
        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[10px] font-bold leading-none text-white">{{ $count > 99 ? '99+' : $count }}</span>
    @endif
</span>



