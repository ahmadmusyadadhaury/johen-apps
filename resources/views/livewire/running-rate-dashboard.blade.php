@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Running Rate</h1>
        <p class="text-xs text-gray-400 mt-0.5">Monitor pencapaian target dan kebutuhan penjualan host live {{ $divisi }}.</p>
    </div>
@endpush

@php
    $num = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
@endphp

<div>
    {{-- Global loading bar --}}
    <div class="fixed top-0 left-0 right-0 z-[120] h-0.5 overflow-hidden pointer-events-none">
        <div wire:loading.flex class="h-full w-full bg-gradient-to-r from-primary-600 via-violet-500 to-primary-600 animate-shimmer"></div>
    </div>

    @if(!$period)
        {{-- Empty state: belum ada periode --}}
        <div class="card flex flex-col items-center justify-center px-6 py-16 text-center mt-6">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-50 to-violet-50 text-primary-500 dark:from-primary-950/40 dark:to-violet-950/40 dark:text-primary-400 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm-9 3v1m9-3v1"/></svg>
            </div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum ada periode Running Rate</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 max-w-md">Buat periode Running Rate dan tentukan target tiap host untuk mulai memantau pencapaian penjualan host live {{ $divisi }}.</p>
            @if($canManage)
                <button wire:click="openSetupModal" class="btn-primary mt-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Set Up Running Rate
                </button>
            @endif
        </div>
    @else
        <div wire:loading.class="opacity-60 pointer-events-none transition-opacity duration-150">
            @include('livewire.partials.running-rate-summary', ['num' => $num])
            @include('livewire.partials.running-rate-table', ['num' => $num])
        </div>
    @endif

    @include('livewire.partials.running-rate-modals', ['num' => $num])
</div>