@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Weekly Plan Report</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Laporan rencana kerja mingguan</p>
    </div>
@endpush

<x-app-layout title="Weekly Plan Report">

@livewire('weekly-plan-report-table', ['employeeId' => $employeeId ?? null])

</x-app-layout>