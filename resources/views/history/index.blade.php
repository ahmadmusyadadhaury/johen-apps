@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Payroll</h1>
        <p class="text-xs text-gray-400 mt-0.5">Semua data payroll yang pernah diupload</p>
    </div>
@endpush

<x-app-layout title="Riwayat Payroll">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Seluruh Data</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($stats['total_payroll'], 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Payroll</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <span class="badge-warning">Rekap</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_employees']) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Karyawan</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="badge-warning">Upload</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_periode'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Periode</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-500 text-white shadow-lg shadow-red-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
                <span class="badge-danger">Terkirim</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['email_sent'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Email Slip Terkirim</p>
        </div>
    </div>

    @if($imports->count() > 0)
        <div class="card overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <div class="relative">
                    <select onchange="window.location = '?year=' + this.value"
                            class="appearance-none rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-3 pr-8 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200 cursor-pointer">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                @can('create-data')
                <a href="{{ route('payroll.upload') }}" class="btn-primary text-xs py-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Upload Baru
                </a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 w-12 text-center">No</th>
                            <th class="px-6 py-3">Periode</th>
                            <th class="px-6 py-3">Nama File</th>
                            <th class="px-6 py-3 text-center">Karyawan</th>
                            <th class="px-6 py-3 text-right">Total Payroll</th>
                            <th class="px-6 py-3">Upload</th>
                            <th class="px-6 py-3">Oleh</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($imports as $import)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $imports->firstItem() + $loop->index }}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $import->periode }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 max-w-[200px] truncate" title="{{ $import->file_name }}">{{ $import->file_name }}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $import->payroll_details_count }}</td>
                                <td class="table-cell text-right font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($import->total_payroll, 0, ',', '.') }}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400">{{ $import->created_at->format('d M Y H:i') }}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400">{{ $import->uploadedBy?->name ?? 'Unknown' }}</td>
                                <td class="table-cell text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1">
                                        <a href="{{ route('history.show', $import) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Lihat Detail
                                        </a>

                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" @click.outside="open = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                            </button>
                                            @can('delete-data')
                                            <div x-show="open" x-cloak
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100 scale-100"
                                                 x-transition:leave-end="opacity-0 scale-95"
                                                 @click.outside="open = false" class="absolute right-0 z-50 mt-1 w-40 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 py-1.5">
                                                <form method="POST" action="{{ route('payroll.destroy', $import) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" @click.prevent="$store.confirmModal.show('Hapus Payroll', 'Yakin ingin menghapus payroll {{ $import->periode }}?', () => $el.closest('form').submit())" class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $imports->links() }}
        </div>
    @else
        <div class="card">
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-5">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Belum ada riwayat payroll</h3>
                @can('create-data')
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload payroll pertama Anda untuk memulai</p>
                <a href="{{ route('payroll.upload') }}" class="btn-primary mt-5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Upload Payroll Pertama
                </a>
                @endcan
            </div>
        </div>
    @endif

</x-app-layout>
