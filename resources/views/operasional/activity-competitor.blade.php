@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Activity Competitor</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Monitoring aktivitas kompetitor</p>
    </div>
@endpush

<x-app-layout title="Activity Competitor">

@livewire('activity-competitor-table')

</x-app-layout>