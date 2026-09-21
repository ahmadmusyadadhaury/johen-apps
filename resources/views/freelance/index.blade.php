@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Freelance</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Data karyawan freelance</p>
    </div>
@endpush

<x-app-layout title="Freelance">
    <livewire:freelance-table />
</x-app-layout>