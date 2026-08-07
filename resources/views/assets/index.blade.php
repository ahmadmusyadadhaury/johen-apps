<x-app-layout :title="$selectedCategory ? ucfirst($selectedCategory) : 'Data Asset'">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                {{ $selectedCategory ? ucfirst($selectedCategory) : 'Data Asset' }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Kelola data asset perusahaan</p>
        </div>
    @endpush

    <div class="space-y-6" x-data="assetDetailModal()">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('assets.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ !$selectedCategory ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Semua
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('assets.category', strtolower($cat->name)) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 {{ $selectedCategory && strtolower($cat->name) === strtolower($selectedCategory) ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Daftar Asset
                </h2>
                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('assets.index') }}" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                               class="input-field w-40 text-xs pl-8 py-2">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-4 py-3 text-center w-12">No</th>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Nama Asset</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Brand</th>
                            <th class="px-4 py-3 text-center">Kondisi</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400">{{ $a->code }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $a->name }}</td>
                            <td class="table-cell">
                                <span class="badge-info">{{ $a->category?->name ?? '-' }}</span>
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $a->brand ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @php
                                    $conditionLabels = ['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat'];
                                    $conditionClasses = ['baik' => 'badge-success', 'rusak_ringan' => 'badge-warning', 'rusak_berat' => 'badge-danger'];
                                @endphp
                                <span class="{{ $conditionClasses[$a->condition] ?? 'badge-info' }}">{{ $conditionLabels[$a->condition] ?? $a->condition }}</span>
                            </td>
                            <td class="table-cell text-center">
                                @php
                                    $statusLabels = ['tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'dalam_perbaikan' => 'Perbaikan', 'dihapuskan' => 'Dihapuskan'];
                                    $statusClasses = ['tersedia' => 'badge-success', 'dipinjam' => 'badge-warning', 'dalam_perbaikan' => 'badge-danger', 'dihapuskan' => 'badge-info'];
                                @endphp
                                <span class="{{ $statusClasses[$a->status] ?? 'badge-info' }}">{{ $statusLabels[$a->status] ?? $a->status }}</span>
                            </td>
                            <td class="table-cell text-right">
                                <button type="button" @click="show({{ $a->id }})" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Asset</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada data asset di kategori ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $assets->links() }}
            </div>
            @endif
        </div>
        {{-- Detail Modal --}}
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
             @click.self="open = false">
            <div x-show="open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 shadow-2xl my-10">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 truncate" x-text="d.name || 'Detail Asset'"></h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="d.code ? 'Kode Aset: ' + d.code : ''"></p>
                    </div>
                    <button type="button" @click="open = false" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 max-h-[70vh] overflow-y-auto">
                    <div x-show="loading" class="flex items-center justify-center py-16">
                        <svg class="animate-spin w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">Memuat detail...</span>
                    </div>
                    <template x-if="!loading && rows().length">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                            <template x-for="r in rows()" :key="r.label">
                                <div :class="r.full ? 'sm:col-span-2' : ''">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide" x-text="r.label"></dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100" :class="r.mono ? 'font-mono text-xs' : ''">
                                        <template x-if="r.badge">
                                            <span :class="r.badge" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" x-text="r.value"></span>
                                        </template>
                                        <template x-if="!r.badge">
                                            <span x-text="r.value"></span>
                                        </template>
                                    </dd>
                                </div>
                            </template>
                        </dl>
                    </template>
                    <div x-show="!loading && !rows().length" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada detail yang tersedia.</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function assetDetailModal() {
            return {
                open: false,
                loading: false,
                d: {},
                urlTemplate: @json(route('assets.detail', ['asset' => '__ID__'])),
                async show(id) {
                    this.loading = true;
                    this.open = true;
                    this.d = {};
                    try {
                        const res = await fetch(this.urlTemplate.replace('__ID__', id), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) throw new Error('Gagal memuat detail');
                        this.d = await res.json();
                    } catch (e) {
                        this.d = { name: 'Gagal memuat detail asset', description: e.message || e.toString() };
                    } finally {
                        this.loading = false;
                    }
                },
                rows() {
                    const d = this.d || {};
                    const conditionLabels = { baik: 'Baik', rusak_ringan: 'Rusak Ringan', rusak_berat: 'Rusak Berat' };
                    const conditionClasses = {
                        baik: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        rusak_ringan: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        rusak_berat: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    };
                    const statusLabels = { tersedia: 'Tersedia', dipinjam: 'Dipinjam', dalam_perbaikan: 'Perbaikan', dihapuskan: 'Dihapuskan' };
                    const statusClasses = {
                        tersedia: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        dipinjam: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        dalam_perbaikan: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                        dihapuskan: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                    };
                    const fallbackBadge = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
                    const rupiah = (v) => {
                        if (v === null || v === undefined || v === '') return null;
                        const n = Number(v);
                        return isNaN(n) ? null : 'Rp ' + n.toLocaleString('id-ID');
                    };
                    return [
                        { label: 'Kategori', value: d.category },
                        { label: 'Brand', value: d.brand },
                        { label: 'Model', value: d.model },
                        { label: 'Nomor Serial', value: d.serial_number, mono: true },
                        { label: 'Nilai Aset', value: rupiah(d.purchase_price) },
                        { label: 'Tanggal Pembelian', value: d.purchase_date },
                        { label: 'Lokasi', value: d.location },
                        { label: 'Supplier', value: d.supplier },
                        { label: 'Dibuat Oleh', value: d.creator },
                        { label: 'Kondisi', value: conditionLabels[d.condition] || d.condition, badge: conditionClasses[d.condition] || fallbackBadge },
                        { label: 'Status', value: statusLabels[d.status] || d.status, badge: statusClasses[d.status] || fallbackBadge },
                        { label: 'Deskripsi', value: d.description, full: true },
                    ].filter((r) => r.value !== null && r.value !== undefined && r.value !== '');
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
