@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Struktur Organisasi</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Bagan hierarki jabatan perusahaan</p>
    </div>
@endpush

<x-app-layout title="Struktur Organisasi">
    @livewire('struktur-organisasi')
</x-app-layout>