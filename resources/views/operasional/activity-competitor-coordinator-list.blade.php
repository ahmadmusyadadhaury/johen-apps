@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Activity Competitor</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Pilih koordinator untuk melihat aktivitas kompetitor</p>
    </div>
@endpush

<x-app-layout title="Activity Competitor">
    @livewire('activity-competitor-coordinator-list')
</x-app-layout>
