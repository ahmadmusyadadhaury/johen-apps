@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kontrak Kerja</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola kontrak kerja karyawan</p>
    </div>
@endpush

<div>
    {{-- Alert akan berakhir --}}
    @if($akanBerakhir > 0)
        @php $isUrgentAlert = $urgent > 0; @endphp
        <div class="mb-5 rounded-xl border px-5 py-3.5 flex items-start gap-3 {{ $isUrgentAlert ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }}">
            <svg class="w-5 h-5 mt-0.5 shrink-0 {{ $isUrgentAlert ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div class="flex-1">
                <p class="text-sm font-semibold {{ $isUrgentAlert ? 'text-red-800' : 'text-amber-800' }}">Ada <span class="underline">{{ $akanBerakhir }}</span> kontrak yang akan berakhir dalam 7 hari</p>
                <p class="text-xs {{ $isUrgentAlert ? 'text-red-700' : 'text-amber-700' }} mt-0.5">Segera perbarui kontrak melalui menu Riwayat Kontrak di halaman detail karyawan.</p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAktif }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Aktif</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Akan Berakhir</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $akanBerakhir }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dalam 7 Hari</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <span class="badge-info">Selesai</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSelesai }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Selesai</p>
        </div>
    </div>

    {{-- Card wrapper for table --}}
    <div class="card">
        {{-- Filter bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama atau NIK..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                    >
                </div>

                <a href="{{ route('hris.export.kontrak-kerja') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Export Excel
                </a>
            </div>


        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">Nama Karyawan</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3">Divisi</th>
                        <th class="px-6 py-3">Sisa Hari</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($contracts as $ct)
                        @php
                            $sisaHari = now()->startOfDay()->diffInDays($ct->tanggal_berakhir, false);
                            $isAkanBerakhir = $sisaHari <= 7 && $sisaHari >= 0;
                            $isUrgent = $sisaHari < 3 && $sisaHari >= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $contracts->firstItem() + $loop->index }}</td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    @if($ct->employee->foto_url)
                                        <img src="{{ $ct->employee->foto_url }}" alt="{{ $ct->employee->nama }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50">
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                            {{ strtoupper(substr($ct->employee->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ct->employee->nama }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $ct->employee->nik }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $ct->employee->position ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $ct->employee->division->nama ?? '-' }}</td>
                            <td class="table-cell font-mono {{ $isUrgent ? 'text-red-600 font-semibold' : ($isAkanBerakhir ? 'text-amber-600 font-semibold' : 'text-gray-600 dark:text-gray-400') }}">
                                @if($sisaHari < 0)
                                    <span class="text-red-500">-</span>
                                @elseif($sisaHari === 0)
                                    <span class="text-red-500">Hari ini</span>
                                @else
                                    {{ $sisaHari }} hari
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($sisaHari < 0)
                                    <span class="badge-danger">Kedaluwarsa</span>
                                @elseif($isUrgent)
                                    <span class="badge-danger">Segera Berakhir</span>
                                @elseif($isAkanBerakhir)
                                    <span class="badge-warning">Akan Berakhir</span>
                                @else
                                    <span class="badge-success">Aktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <a href="{{ route('hris.employees.show', $ct->employee) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tidak ada kontrak aktif</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada kontrak kerja yang sedang berlaku</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>
