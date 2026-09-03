@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Daily Tracking {{ $divisi }}</h1>
        <p class="text-xs text-gray-400 mt-0.5">Tracking aktivitas harian divisi {{ $divisi }}</p>
    </div>
@endpush

<x-app-layout title="Daily Tracking {{ $divisi }}">

@livewire('manager-daily-tracking-game', ['divisi' => $divisi], key('manager-daily-tracking-game-' . str_replace(' ', '-', $divisi)))

</x-app-layout>