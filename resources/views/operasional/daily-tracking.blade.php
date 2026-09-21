@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Daily Tracking</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Tracking aktivitas harian bawahan</p>
    </div>
@endpush

<x-app-layout title="Daily Tracking">

@livewire('daily-tracking-table')

</x-app-layout>