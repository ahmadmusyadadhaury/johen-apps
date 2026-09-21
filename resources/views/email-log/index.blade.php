@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Log Pengiriman Email</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Periode {{ $import->periode }}</p>
    </div>
@endpush
@push('topbar-right')
    <form action="{{ route('payroll.email-retry-all', $import) }}" method="POST">
        @csrf
        <button type="submit" class="btn-danger">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
            Retry Semua Gagal
        </button>
    </form>
    <a href="{{ route('payroll.show', $import) }}" class="btn-ghost">Kembali</a>
@endpush

<x-app-layout title="Log Email">


    @livewire('email-log-monitor', ['import' => $import])

</x-app-layout>

