@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Absensi</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Rekap kehadiran karyawan harian</p>
    </div>
@endpush

<x-app-layout title="Absensi">
    @livewire('absensi-table')
</x-app-layout>