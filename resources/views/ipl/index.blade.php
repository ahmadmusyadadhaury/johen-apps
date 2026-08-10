<x-app-layout title="IPL Ruko">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">IPL Ruko</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola tagihan IPL Ruko</p>
        </div>
    @endpush

    <div class="space-y-6" x-data="iplApp()" x-init="init()">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    </div>
                    <span class="badge-info">Total Tagihan</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalTagihan }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $totalTagihan }} tagihan</p>
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
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalMenunggu }}</p>
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

        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                    Pembayaran IPL Ruko
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

                    <a href="{{ route('ipl.export') }}" class="btn-ghost p-2" title="Download Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3">Tagihan</th>
                            <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-center">Hari</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800" id="payments-table-body">
                        @forelse($payments as $index => $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $p->periode }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $p->tagihan ?? '-' }}</td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <span class="{{ $p->jatuh_tempo && $p->jatuh_tempo < now() && $p->status !== 'lunas' ? 'text-red-600 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $p->jatuh_tempo?->format('d/m/Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $p->hari ?? '-' }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
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
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada tagihan IPL Ruko.</p>
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

    </div>

    @push('scripts')
    <script>
        function iplApp() {
            return {
                paymentsMeta: null,
                search: '',
                statusFilter: 'semua',
                init() {
                    this.loadPayments();
                },
                async loadPayments(search = '', status = 'semua', page = 1) {
                    try {
                        const params = new URLSearchParams({ search, status, page });
                        if (!search) params.delete('search');
                        const res = await fetch(`{{ route('ipl.data') }}?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('payments-table-body');
                        if (!tbody) return;

                        this.paymentsMeta = json.meta;

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada tagihan IPL Ruko.</p>
                                </div>
                            </td></tr>`;
                            return;
                        }

                        tbody.innerHTML = json.data.map((p, i) => {
                            const badge = p.status === 'lunas' ? 'badge-success' : p.status === 'menunggu' ? 'badge-warning' : p.status === 'terlambat' ? 'badge-danger' : 'badge-info';
                            const label = p.status ? p.status.charAt(0).toUpperCase() + p.status.slice(1) : '-';
                            const fmt = (d) => d ? new Date(d).toLocaleDateString('id-ID') : '-';
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">${p.periode || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${p.tagihan || '-'}</td>
                                <td class="table-cell text-center whitespace-nowrap">${fmt(p.jatuh_tempo)}</td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400">${p.hari || '-'}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp ${Number(p.nominal).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-center"><span class="${badge}">${label}</span></td>
                                <td class="table-cell text-center text-gray-600 dark:text-gray-400 whitespace-nowrap">${fmt(p.tgl_bayar)}</td>
                            </tr>`;
                        }).join('');
                    } catch (e) {
                        console.error('Failed to load payments:', e);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>