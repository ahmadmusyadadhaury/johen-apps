@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Rekap Stok</h1>
        <p class="text-xs text-gray-400 mt-0.5">Rekap stok masuk & keluar</p>
    </div>
@endpush

<div>
    <div class="mb-6">
        <div class="inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            <button wire:click="switchTab('masuk')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'masuk' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Stok Masuk
            </button>
            <button wire:click="switchTab('keluar')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'keluar' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                Stok Keluar
            </button>
        </div>
    </div>

    @if($tab === 'masuk')
        <livewire:stok-masuk-table wire:key="tab-masuk" />
    @else
        <livewire:stok-keluar-table wire:key="tab-keluar" />
    @endif
</div>
