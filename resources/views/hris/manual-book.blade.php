@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Pelatihan</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Panduan & video pelatihan</p>
    </div>
@endpush

@php
    // Tambahkan video baru dengan menambah entry di array ini.
    // 'url' berupa link embed (YouTube: https://www.youtube.com/embed/XXXX).
    $videos = [
        // ['title' => 'Cara Login Aplikasi', 'kategori' => 'Operasional', 'url' => 'https://www.youtube.com/embed/XXXX'],
    ];
@endphp

<x-app-layout title="Pelatihan">

<div x-data="{ tab: 'manual' }">
    <div class="mb-6">
        <div class="flex sm:inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            <button @click="tab = 'manual'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="tab === 'manual' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                Manual Book
            </button>
            <button @click="tab = 'video'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="tab === 'video' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Video
            </button>
        </div>
    </div>

    <div x-show="tab === 'manual'">
        @livewire('manual-book-table')
    </div>

    <div x-show="tab === 'video'" x-cloak>
        @if(empty($videos))
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-10 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada video pelatihan</p>
            <p class="mt-1 text-xs text-gray-400">Video akan tampil di sini setelah ditambahkan.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($videos as $video)
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="aspect-video bg-black">
                    <iframe src="{{ $video['url'] }}" title="{{ $video['title'] }}" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen class="w-full h-full"></iframe>
                </div>
                <div class="p-4">
                    @if(!empty($video['kategori']))
                    <span class="inline-block rounded-lg bg-blue-50 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-semibold text-blue-600 dark:text-blue-400 mb-2">
                        {{ $video['kategori'] }}
                    </span>
                    @endif
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $video['title'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

</x-app-layout>
