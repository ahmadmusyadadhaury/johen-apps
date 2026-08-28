@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Weekly Plan Report</h1>
        <p class="text-xs text-gray-400 mt-0.5">Pilih koordinator untuk melihat laporan</p>
    </div>
@endpush

<x-app-layout title="Weekly Plan Report">

@livewire('weekly-report-coordinator-list')

</x-app-layout>
