<span wire:poll.30s>
    @if($count > 0)
        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold tabular-nums leading-none text-white shadow-sm">{{ $count }}</span>
    @endif
</span>