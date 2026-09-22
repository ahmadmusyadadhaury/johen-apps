@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Influencer</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola data influencer</p>
    </div>
@endpush

<x-app-layout title="Influencer">
    @livewire('influencer-table')
</x-app-layout>