@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kontrak Kerja</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola data kontrak kerja karyawan</p>
    </div>
@endpush

<x-app-layout title="Kontrak Kerja">
    @livewire('kontrak-kerja-table')
</x-app-layout>