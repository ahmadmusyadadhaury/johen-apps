@push('topbar-left')
    <div>
        <h1 class="truncate text-base font-bold text-gray-900 dark:text-gray-100 sm:text-lg">Dashboard</h1>
        <p class="mt-0.5 hidden text-xs text-gray-400 sm:block">{{ $office->name }}</p>
    </div>
@endpush

<x-app-layout title="Dashboard {{ $office->name }}">
    <section class="flex min-h-[60vh] items-center justify-center">
        <div class="w-full max-w-xl rounded-3xl border border-gray-100 bg-white p-8 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-12">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-300">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3.75h1.5m-1.5 3.75h1.5m4.5-7.5h1.5m-1.5 3.75h1.5m-1.5 3.75h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
            </div>

            <h2 class="mt-5 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $office->name }} masih kosong</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500 dark:text-gray-400">
                Belum ada divisi, jabatan, atau karyawan untuk kantor ini. Data Kantor Summarecon Bandung tidak ditampilkan pada konteks ini.
            </p>

            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminMaster())
                <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('hris.divisions.index') }}" class="btn-primary justify-center text-xs">Buat Divisi Pertama</a>
                    <a href="{{ route('kelola-jabatan') }}" class="btn-secondary justify-center text-xs">Kelola Jabatan</a>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
