<x-app-layout title="Aset Digital">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Aset Digital</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola tagihan aset digital dan pembayarannya</p>
        </div>
    @endpush

    <div class="space-y-6" x-data="digitalApp()" x-init="init()">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/></svg>
                    </div>
                    <span class="badge-info">Total Aset Digital</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAset }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $totalAset }} tagihan</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Sudah Dibayar</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalLunas }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tagihan lunas</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-warning">Jatuh Tempo</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalJatuhTempo }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Belum dibayar</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-200 dark:shadow-red-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger">Terlambat</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalTerlambat }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Lewat jatuh tempo</p>
            </div>
        </div>

        {{-- Tabel: Aset Digital --}}
        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/></svg>
                    Tagihan Aset Digital
                </h2>

                <div class="flex items-center gap-2 flex-wrap">
                    <div class="relative">
                        <input type="text" x-model="search" @input.debounce="loadAssets(search, statusFilter)" placeholder="Cari..." class="input-field w-40 text-xs pl-8 py-2">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>

                    <select @change="loadAssets(search, statusFilter)" x-model="statusFilter" class="input-field text-xs py-2 w-32">
                        <option value="semua">Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="terlambat">Terlambat</option>
                    </select>

                    <a href="{{ route('digital.export') }}" class="btn-ghost p-2" title="Download Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Nama Aset</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3 text-center">Mulai</th>
                            <th class="px-4 py-3 text-center">Berakhir</th>
                            <th class="px-4 py-3">Tagihan</th>
                            <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-center">Hari</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800" id="assets-table-body">
                        @forelse($assets as $a)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $a->nama_aset }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $a->email ?? '-' }}</td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $a->mulai?->format('d/m/Y') ?? '-' }}</td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $a->berakhir?->format('d/m/Y') ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $a->tagihan }}</td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <span class="{{ $a->jatuh_tempo && $a->jatuh_tempo < now() && $a->status !== 'lunas' ? 'text-red-600 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $a->jatuh_tempo?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $a->hari ?? '-' }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($a->nominal, 0, ',', '.') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $a->pic ?? '-' }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400">{{ $a->jabatan ?? '-' }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[120px] truncate">{{ $a->keterangan ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @php
                                    $badge = match($a->status) {
                                        'lunas' => 'badge-success',
                                        'menunggu' => 'badge-warning',
                                        'terlambat' => 'badge-danger',
                                        default => 'badge-info',
                                    };
                                @endphp
                                <span class="{{ $badge }}">{{ $a->status ? ucfirst($a->status) : '-' }}</span>
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $a->tgl_bayar?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada tagihan aset digital.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end">
                <div class="flex gap-1" x-show="assetsMeta && assetsMeta.last_page > 1" x-cloak>
                    <template x-for="p in assetsMeta.last_page" :key="p">
                        <button @click="loadAssets(search, statusFilter, p)" :class="p === assetsMeta.current_page ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-3 py-1 text-xs font-semibold rounded-lg transition-all">p</button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function digitalApp() {
            return {
                assetsMeta: null,
                search: '',
                statusFilter: 'semua',
                init() {
                    this.loadAssets();
                },
                async loadAssets(search = '', status = 'semua', page = 1) {
                    try {
                        const params = new URLSearchParams({ search, status, page });
                        if (!search) params.delete('search');
                        const res = await fetch(`{{ route('digital.data') }}?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('assets-table-body');
                        if (!tbody) return;

                        this.assetsMeta = json.meta;

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="14" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l4.179 2.25m0-4.5l5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0l4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0l-5.571 3-5.571-3"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada tagihan aset digital.</p>
                                </div>
                            </td></tr>`;
                            return;
                        }

                        tbody.innerHTML = json.data.map((a, i) => {
                            const badge = a.status === 'lunas' ? 'badge-success' : a.status === 'menunggu' ? 'badge-warning' : a.status === 'terlambat' ? 'badge-danger' : 'badge-info';
                            const label = a.status ? a.status.charAt(0).toUpperCase() + a.status.slice(1) : '-';
                            const fmt = (d) => d ? new Date(d).toLocaleDateString('id-ID') : '-';
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">${a.nama_aset || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${a.email || '-'}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${fmt(a.mulai)}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${fmt(a.berakhir)}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${a.tagihan || '-'}</td>
                                <td class="table-cell text-center whitespace-nowrap">${fmt(a.jatuh_tempo)}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400">${a.hari || '-'}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp ${Number(a.nominal).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${a.pic || '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400">${a.jabatan || '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[120px] truncate">${a.keterangan || '-'}</td>
                                <td class="table-cell text-center"><span class="${badge}">${label}</span></td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${fmt(a.tgl_bayar)}</td>
                            </tr>`;
                        }).join('');
                    } catch (e) {
                        console.error('Failed to load assets:', e);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>