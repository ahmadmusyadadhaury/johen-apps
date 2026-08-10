<x-app-layout title="Pembayaran Listrik">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pembayaran Listrik</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola token listrik dan pengecekan</p>
        </div>
    @endpush

    @push('topbar-right')
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">Kapasitas: {{ number_format($setting->kapasitas_kwh, 0, ',', '.') }} KWH/bulan</span>
            <button @click="$dispatch('open-modal', { name: 'settings-modal' })" class="btn-ghost p-1.5" title="Pengaturan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
        </div>
    @endpush

    <div class="space-y-6" x-data="electricityApp()" x-init="init()">
        {{-- Card: Top Up Terakhir --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-info">Top Up Terakhir</span>
                </div>
                @if($lastTopup)
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($lastTopup->jumlah_kwh, 0, ',', '.') }} <span class="text-sm font-medium text-gray-400">KWH</span></p>
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Rp {{ number_format($lastTopup->nominal, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $lastTopup->tanggal_bayar->format('d M Y') }} &middot; {{ $lastTopup->creator?->name }}</p>
                @else
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada top up</p>
                @endif
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                    </div>
                    <span class="badge-warning">Terpakai</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalTerpakai, 0, ',', '.') }} <span class="text-sm font-medium text-gray-400">KWH</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total pemakaian tercatat</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Sisa Token</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($sisaToken, 0, ',', '.') }} <span class="text-sm font-medium text-gray-400">KWH</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dari {{ number_format($setting->kapasitas_kwh, 0, ',', '.') }} KWH/bulan</p>
            </div>
        </div>

        {{-- Tabel: Riwayat Top Up Token --}}
        <div class="card overflow-hidden" x-data="{ topupFilter: 'bulanan', topups: [], topupMeta: null }">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Riwayat Top Up Token
                </h2>

                <div class="flex items-center gap-2">
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1" x-data="{ tab: 'bulanan' }">
                        <button @click="tab='harian'; loadTopups('harian')" :class="tab==='harian' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Harian</button>
                        <button @click="tab='mingguan'; loadTopups('mingguan')" :class="tab==='mingguan' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Mingguan</button>
                        <button @click="tab='bulanan'; loadTopups('bulanan')" :class="tab==='bulanan' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Bulanan</button>
                    </div>

                    <a href="{{ route('electricity.export.topups') }}" class="btn-ghost p-2" title="Export Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Tanggal Bayar</th>
                            <th class="px-4 py-3">Periode</th>
                            <th class="px-4 py-3 text-right">Jumlah KWH</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">Oleh</th>
                            <th class="px-4 py-3">Bukti</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800" id="topup-table-body">
                        @forelse($topups as $t)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $t->tanggal_bayar->format('d/m/Y H:i') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $t->periode }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($t->jumlah_kwh, 0, ',', '.') }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $t->creator?->name }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                @if($t->bukti_url)
                                    <a href="{{ $t->bukti_url }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:underline text-xs font-medium" title="Lihat bukti">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $t->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Top Up</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada riwayat pembelian token listrik.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="topupMeta ? 'Total: ' + topupMeta.total + ' data' : ''"></p>
                <div class="flex gap-1" x-show="topupMeta && topupMeta.last_page > 1" x-cloak>
                    <template x-for="p in topupMeta.last_page" :key="p">
                        <button @click="loadTopups(tab, p)" :class="p === topupMeta.current_page ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100'" class="px-3 py-1 text-xs font-semibold rounded-lg transition-all">p</button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Tabel: Pengecekan Token Listrik --}}
        <div class="card overflow-hidden" x-data="{ checkFilter: 'bulanan' }">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                    Pengecekan Token Listrik
                </h2>

                <div class="flex items-center gap-2">
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 rounded-xl p-1" x-data="{ tab: 'bulanan' }">
                        <button @click="tab='harian'; loadChecks('harian')" :class="tab==='harian' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Harian</button>
                        <button @click="tab='mingguan'; loadChecks('mingguan')" :class="tab==='mingguan' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Mingguan</button>
                        <button @click="tab='bulanan'; loadChecks('bulanan')" :class="tab==='bulanan' ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700'" class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all">Bulanan</button>
                    </div>

                    <a href="{{ route('electricity.export.checks') }}" class="btn-ghost p-2" title="Export Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Tanggal Check</th>
                            <th class="px-4 py-3 text-right">Sisa KWH</th>
                            <th class="px-4 py-3 text-right">Terpakai</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3">Pengecek</th>
                            <th class="px-4 py-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($checks as $c)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $c->tanggal_check->format('d/m/Y') }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">{{ number_format($c->sisa_kwh, 0, ',', '.') }}</td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">{{ $c->terpakai !== null ? number_format($c->terpakai, 0, ',', '.') : '-' }}</td>
                            <td class="table-cell text-center">
                                @php
                                    $badgeClass = match($c->status) {
                                        'normal', 'aman' => 'badge-success',
                                        'rendah' => 'badge-warning',
                                        'habis' => 'badge-danger',
                                        default => 'badge-info',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ $c->status }}</span>
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $c->checker?->name }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $c->catatan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Pengecekan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lakukan pengecekan sisa KWH token setiap minggu.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: Pengaturan Kapasitas --}}
    <x-modal name="settings-modal" maxWidth="sm">
        <form method="POST" action="{{ route('electricity.update.settings') }}" class="p-6">
            @csrf @method('PUT')
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pengaturan Kapasitas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Ubah kapasitas token per bulan</p>
                </div>
                <button type="button" @click="$dispatch('close-modal', { name: 'settings-modal' })" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <x-input-label for="kapasitas">Kapasitas KWH / Bulan</x-input-label>
                <x-text-input id="kapasitas" name="kapasitas_kwh" type="number" step="0.01" min="0" class="w-full mt-1" value="{{ $setting->kapasitas_kwh }}" required />
                <x-input-error :messages="$errors->get('kapasitas_kwh')" />
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="button" @click="$dispatch('close-modal', { name: 'settings-modal' })" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function electricityApp() {
            return {
                init() {
                    this.loadTopups('bulanan');
                    this.loadChecks('bulanan');
                },
                async loadTopups(filter, page = 1) {
                    try {
                        const res = await fetch(`{{ route('electricity.topups.data') }}?filter=${filter}&page=${page}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('topup-table-body');

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Top Up</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada riwayat pembelian token listrik.</p>
                                </div>
                            </td></tr>`;
                            return;
                        }

                        this.topupMeta = json.meta;
                        tbody.innerHTML = json.data.map((t, i) => {
                            const date = new Date(t.tanggal_bayar).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">${date}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${t.periode}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">${Number(t.jumlah_kwh).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">Rp ${Number(t.nominal).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${t.creator?.name || '-'}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${t.bukti_url ? `<a href="${t.bukti_url}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:underline text-xs font-medium" title="Lihat bukti"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>Lihat</a>` : '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">${t.catatan || '-'}</td>
                            </tr>`;
                        }).join('');
                    } catch (e) {
                        console.error('Failed to load topups:', e);
                    }
                },
                async loadChecks(filter) {
                    try {
                        const res = await fetch(`{{ route('electricity.checks.data') }}?filter=${filter}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        const json = await res.json();
                        const tbody = document.getElementById('checks-table-body');
                        if (!tbody) return;

                        if (json.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-16 text-center">...</td></tr>`;
                            return;
                        }

                        tbody.innerHTML = json.data.map((c, i) => {
                            const badge = c.status === 'normal' || c.status === 'aman' ? 'badge-success' : c.status === 'rendah' ? 'badge-warning' : 'badge-danger';
                            const terpakai = c.terpakai != null ? Number(c.terpakai).toLocaleString('id-ID') : '-';
                            return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">${(json.meta.current_page - 1) * json.meta.per_page + i + 1}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">${new Date(c.tanggal_check).toLocaleDateString('id-ID')}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">${Number(c.sisa_kwh).toLocaleString('id-ID')}</td>
                                <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100">${terpakai}</td>
                                <td class="table-cell text-center"><span class="${badge}">${c.status}</span></td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">${c.checker?.name || '-'}</td>
                                <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">${c.catatan || '-'}</td>
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
