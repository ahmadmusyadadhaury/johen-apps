<x-app-layout title="Registri Aset Digital">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Registri Aset Digital</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Data langganan & akun digital tersinkron dari server office</p>
        </div>
    @endpush

    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 010-5.304m5.304 0a3.75 3.75 0 010 5.304m-7.425 2.121a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12z"/></svg>
                    </div>
                    <span class="badge-info">Total Aset</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Semua aset digital</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Aktif</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['aktif'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Masa berlaku masih aktif</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-200 dark:shadow-red-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger">Nonaktif</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['nonaktif'] }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Masa berlaku sudah lewat</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-warning">Total Biaya</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($stats['nominal'], 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total biaya per tahun</p>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 010-5.304m5.304 0a3.75 3.75 0 010 5.304m-7.425 2.121a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12z"/></svg>
                    Daftar Aset Digital
                </h2>
                <form method="GET" action="{{ route('digital-registries.index') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aset, email, PIC..."
                           class="input-field w-48 text-xs pl-8 py-2">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Nama Aset</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Masa Berlaku</th>
                            <th class="px-4 py-3 text-right">Biaya</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($registries as $r)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $r->nama_aset }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400">{{ $r->email ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                {{ $r->mulai ? $r->mulai->format('d/m/Y') : '-' }} <span class="text-gray-400">s/d</span> {{ $r->berakhir ? $r->berakhir->format('d/m/Y') : '-' }}
                            </td>
                            <td class="table-cell text-right font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format((float) $r->biaya, 0, ',', '.') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $r->pic ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $r->jabatan ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @if($r->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 010-5.304m5.304 0a3.75 3.75 0 010 5.304m-7.425 2.121a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Aset Digital</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Data belum tersinkron atau tidak ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($registries->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $registries->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
