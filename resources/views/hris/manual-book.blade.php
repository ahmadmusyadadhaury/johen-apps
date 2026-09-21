@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Manual Book</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Panduan & dokumentasi</p>
    </div>
@endpush

<x-app-layout title="Manual Book">

@livewire('manual-book-table')

</x-app-layout>