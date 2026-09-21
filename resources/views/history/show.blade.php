@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Histori Payroll</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Periode {{ $import->periode }}</p>
    </div>
@endpush
@push('topbar-right')
    <a href="{{ route('history.index') }}" class="btn-ghost">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Kembali
    </a>
@endpush

<x-app-layout title="Detail Histori">


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</p>
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $import->periode }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">File</p>
            </div>
            <p class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $import->file_name }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Karyawan</p>
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $import->total_employee }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Payroll</p>
            </div>
            <p class="text-lg font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($import->total_payroll, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Data Karyawan</h2>
            </div>
            <span class="badge-info">{{ $import->payrollDetails->count() }} karyawan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Divisi</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3 text-right">THP</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3">PDF</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($import->payrollDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $detail->nik }}</td>
                            <td class="table-cell text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $detail->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $detail->divisi }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $detail->jabatan }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}</td>
                            <td class="table-cell text-center whitespace-nowrap">
                                @if($detail->status === 'sent')
                                    <span class="badge-success">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terkirim
                                    </span>
                                @elseif($detail->status === 'failed')
                                    <span class="badge-danger">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Gagal
                                    </span>
                                @else
                                    <span class="badge-warning">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="table-cell whitespace-nowrap">
                                @if($detail->pdf_path)
                                    <a href="{{ route('payroll.download-pdf', $detail) }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        Download
                                    </a>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>

