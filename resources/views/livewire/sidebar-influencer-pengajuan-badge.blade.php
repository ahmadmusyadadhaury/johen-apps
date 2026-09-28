<span wire:poll.30s class="inline-flex shrink-0 items-center" aria-live="polite" aria-atomic="true" aria-label="{{ $total }} pengajuan influencer menunggu tindakan">
    @if($total > 0)
        <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold leading-none text-white shadow-sm">{{ $total > 99 ? '99+' : $total }}</span>
    @endif
</span>
