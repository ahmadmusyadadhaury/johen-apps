<x-app-layout title="Pembayaran Internet">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pembayaran Internet</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola tagihan WiFi dan pengecekan usage internet</p>
        </div>
    @endpush

    <div class="space-y-6" x-data="internetApp()" x-init="init()">
        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m9 0l-2.25 2.25m4.5 0l-2.25-2.25M12 3v1.5m0 0l-2.25-2.25M12 4.5l2.25-2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-info">Total WiFi</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalWifi }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Seluruh data WiFi</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Sudah Dibayar</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $sudahDibayar }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tagihan lunas</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-warning">Jatuh Tempo</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $jatuhTempo }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dalam masa tenggang</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-200 dark:shadow-red-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger">Terlambat</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $terlambat }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Lewat masa tenggang</p>
            </div>
        </div>

        {{-- Tabel: Pembayaran Internet --}}
        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m9 0l-2.25 2.25m4.5 0l-2.25-2.25M12 3v1.5m0 0l-2.25-2.25M12 4.5l2.25-2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pembayaran Internet
                </h2>

                <div class="flex items-center gap-2 flex-wrap">
                    <div class="relative">
                        <input type="text" x-model="search" @input.debounce="loadPayments(search, statusFilter)" placeholder="Cari..." class="input-field w-40 text-xs pl-8 py-2">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>

                    <select @change="loadPayments(search, statusFilter)" x-model="statusFilter" class="input-field text-xs py-2 w-32">
                        <option value="semua">Semua Status</option>
                        <option value="lunas">Lunas</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="terlambat">Terlambat</option>
                    </select>

                    <a href="{{ route('internet.export.payments') }}" class="btn-ghost p-2" title="Download Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Nama Internet</th>
                            <th class="px-4 py-3">Provider</th>
                            <th class="px-4 py-3">PIC</th>
                            <th class="px-4 py-3">Jabatan</th>
                            <th class="px-4 py-3 text-center">Masa Tenggang</th>
                            <th class="px-4 py-3 text-center">Hari</th>
                            <th class="px-4 py-3 text-right">Biaya</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800" id="payments-table-body">
                        @forelse($payments as $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $p->nama_internet }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $p->provider }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $p->pic }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400">{{ $p->jabatan ?? '-' }}</td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <span class="{{ $p->masa_tenggang && $p->masa_tenggang < now() && $p->status !== 'lunas' ? 'text-red-600 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $p->masa_tenggang?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $p->hari ?? '-' }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($p->biaya, 0, ',', '.') }}</td>
                            <td class="table-cell text-center">
                                @php
                                    $badge = match($p->status) {
                                        'lunas' => 'badge-success',
                                        'menunggu' => 'badge-warning',
                                        'terlambat' => 'badge-danger',
                                        default => 'badge-info',
                                    };
                                @endphp
                                <span class="{{ $badge }}">{{ $p->status ? ucfirst($p->status) : '-' }}</span>
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $p->tgl_bayar?->format('d/m/Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m9 0l-2.25 2.25m4.5 0l-2.25-2.25M12 3v1.5m0 0l-2.25-2.25M12 4.5l2.25-2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Tagihan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada data pembayaran internet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end">
                <div class="flex gap-1" x-show="paymentsMeta && paymentsMeta.last_page > 1" x-cloak>
                    <template x-for="p in paymentsMeta.last_page" :key="p">
                        <button @click="loadPayments(search, statusFilter, p)" :class="p === paymentsMeta.current_page ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-3 py-1 text-xs font-semibold rounded-lg transition-all">p</button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Tabel: Pengecekan Usage Internet --}}
        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                    Pengecekan Usage Internet
                </h2>

                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        {{ now()->format('F Y') }}
                    </span>

                    <a href="{{ route('internet.export.checks', ['month' => now()->month, 'year' => now()->year]) }}" class="btn-ghost p-2" title="Download Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="px-6 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    Data pengecekan usage internet per ruangan.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Ruangan</th>
                            <th class="px-4 py-3">Hari</th>
                            <th class="px-4 py-3 text-center">Tanggal</th>
                            <th class="px-4 py-3 text-right">Penggunaan Wifi/Hari</th>
                            <th class="px-4 py-3 text-right">Penggunaan Ethernet/Hari</th>
                            <th class="px-4 py-3">Pengecek</th>
                            <th class="px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800" id="checks-table-body">
                        @forelse($checks as $c)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $c->ruangan }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $c->hari }}</td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $c->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($c->penggunaan_wifi, 1) }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($c->penggunaan_ethernet, 1) }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $c->checker?->name }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $c->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Pengecekan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lakukan pengecekan usage internet setiap hari.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end">
                <div class="flex gap-1" x-show="checkMeta && checkMeta.last_page > 1" x-cloak>
                    <template x-for="p in checkMeta.last_page" :key="p">
                        <button @click="loadChecks(p)" :class="p === checkMeta.current_page ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-3 py-1 text-xs font-semibold rounded-lg transition-all">p</button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function internetApp() {
            return {
                paymentsMeta: null,
                checkMeta: null,
                search: '',
                statusFilter: 'semua',
                init() {
                    this.loadPayments();
                    this.loadChecks();
                },
                async loadPayments(search = '', status = 'semua', page = 1) {
                    try {
                        const params = new URLSearchParams({ search, status, page });
                        if (!search) params.delete('search');
                        const res = await fetch(`{{ route('internet.payments.data') }}?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('payments-table-body');
                        if (!tbody) return;

                        this.paymentsMeta = json.meta;

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="10" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m9 0l-2.25 2.25m4.5 0l-2.25-2.25M12 3v1.5m0 0l-2.25-2.25M12 4.5l2.25-2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Tagihan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada data pembayaran internet.</p>
                                </div>
                            </td></tr>`;
                            return;
                        }

                        tbody.innerHTML = json.data.map((p, i) => {
                            const badge = p.status === 'lunas' ? 'badge-success' : p.status === 'menunggu' ? 'badge-warning' : p.status === 'terlambat' ? 'badge-danger' : 'badge-info';
                            const label = p.status ? p.status.charAt(0).toUpperCase() + p.status.slice(1) : '-';
                            const masa = p.masa_tenggang ? new Date(p.masa_tenggang).toLocaleDateString('id-ID') : '-';
                            const tglBayar = p.tgl_bayar ? new Date(p.tgl_bayar).toLocaleDateString('id-ID') : '-';
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">${p.nama_internet || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${p.provider || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${p.pic || '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400">${p.jabatan || '-'}</td>
                                <td class="table-cell text-center whitespace-nowrap">${masa}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400">${p.hari || '-'}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp ${Number(p.biaya).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-center"><span class="${badge}">${label}</span></td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${tglBayar}</td>
                            </tr>`;
                        }).join('');
                    } catch (e) {
                        console.error('Failed to load payments:', e);
                    }
                },
                async loadChecks(page = 1) {
                    try {
                        const res = await fetch(`{{ route('internet.checks.data') }}?page=${page}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('checks-table-body');
                        if (!tbody) return;

                        this.checkMeta = json.meta;

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Pengecekan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lakukan pengecekan usage internet setiap hari.</p>
                                </div>
                            </td></tr>`;
                            return;
                        }

                        tbody.innerHTML = json.data.map((c, i) => {
                            const tanggal = c.tanggal ? new Date(c.tanggal).toLocaleDateString('id-ID') : '-';
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100">${c.ruangan || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${c.hari || '-'}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${tanggal}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">${Number(c.penggunaan_wifi).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">${Number(c.penggunaan_ethernet).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${c.checker?.name || '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">${c.keterangan || '-'}</td>
                            </tr>`;
                        }).join('');
                    } catch (e) {
                        console.error('Failed to load checks:', e);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>