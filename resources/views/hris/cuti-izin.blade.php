@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Cuti & Izin</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola pengajuan cuti dan izin karyawan</p>
    </div>
@endpush

<x-app-layout title="Cuti & Izin">
    @livewire('cuti-izin-table')
</x-app-layout>