@php
    $dailyTrackingLabel = strtolower(trim($divisi)) === 'pubg' ? 'Johen PUBG' : $divisi;
    $isJohenPubg = strtolower(trim($divisi)) === 'pubg';
@endphp

@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Daily Tracking {{ $dailyTrackingLabel }}</h1>
        @unless($isJohenPubg)
            <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Tracking aktivitas harian divisi {{ $dailyTrackingLabel }}</p>
        @endunless
    </div>
@endpush

<x-app-layout title="Daily Tracking {{ $dailyTrackingLabel }}">

@livewire('manager-daily-tracking-game', ['divisi' => $divisi], key('manager-daily-tracking-game-' . str_replace(' ', '-', $divisi)))

</x-app-layout>
