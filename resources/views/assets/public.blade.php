<x-guest-layout>
    @push('topbar-left')
    @endpush

    <div class="w-full" x-data="assetPublicModal()" x-init="init()">
        {{-- Detail Modal --}}
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex max-h-full items-center justify-center overflow-y-auto p-4 sm:p-6">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md"></div>
            <div x-show="open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-[0.97]"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-[0.97]"
                 @click.stop
                 class="relative my-auto w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-[0_48px_120px_-32px_rgba(8,60,150,0.45)] ring-1 ring-gray-200/70 dark:bg-gray-900 dark:shadow-[0_48px_120px_-30px_rgba(2,8,23,0.8)] dark:ring-white/10">
                <div class="relative overflow-hidden bg-gradient-to-br from-primary-600 via-primary-500 to-violet-600 px-6 pb-6 pt-7 sm:px-8">
                    <div aria-hidden="true" class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-white/15 blur-3xl"></div>
                    <div aria-hidden="true" class="pointer-events-none absolute -bottom-24 -left-10 h-48 w-48 rounded-full bg-black/10 blur-3xl"></div>
                    <div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.28),transparent_55%)]"></div>

                    <div class="relative flex items-start gap-4 pr-10">
                        <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 shadow-inner backdrop-blur-md ring-1 ring-white/25">
                            <svg class="h-8 w-8 text-white drop-shadow" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/70" x-text="d.category || 'Kategori'"></p>
                            <h3 class="mt-1 truncate font-display text-2xl font-bold leading-tight text-white drop-shadow-sm" x-text="d.name || 'Detail Asset'"></h3>
                            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                <template x-if="d.code">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-2.5 py-1 font-mono text-[11px] font-semibold text-white ring-1 ring-white/25 backdrop-blur">
                                        <svg class="h-3.5 w-3.5 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-8.25h2.25M6 9.75h3m-3 3h2.25m9 0H9m3-6h2.25m-2.25 9h.01"/></svg>
                                        <span x-text="d.code"></span>
                                    </span>
                                </template>
                                <template x-if="d.condition">
                                    <span :class="conditionPill(d.condition)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>
                                        <span x-text="conditionLabels[d.condition] || d.condition"></span>
                                    </span>
                                </template>
                                <template x-if="d.status">
                                    <span :class="statusPill(d.status)" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-80"></span>
                                        <span x-text="statusLabels[d.status] || d.status"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <template x-if="barcode()">
                            <div class="relative ml-auto mt-4 hidden shrink-0 items-center gap-4 rounded-2xl bg-white/95 px-6 py-3 shadow-lg ring-1 ring-white/30 backdrop-blur sm:flex">
                                <div class="flex flex-col items-center gap-1.5">
                                    <svg class="h-14 w-full max-w-[200px]" x-cloak x-init="JsBarcode($el, barcode(), { format: 'CODE128', displayValue: false, margin: 0, background: 'transparent', lineColor: '#0f172a' })"></svg>
                                    <span class="font-mono text-[10px] font-semibold tracking-[0.22em] text-slate-600" x-text="barcode()"></span>
                                </div>
                                <template x-if="qrUrl()">
                                    <div class="flex flex-col items-center gap-1.5 border-l border-slate-200 pl-4">
                                        <canvas class="h-[88px] w-[88px]" x-cloak x-init="QRCode.toCanvas($el, qrUrl(), { width: 88, margin: 0, errorCorrectionLevel: 'M', color: { dark: '#0f172a', light: '#ffffff' } })"></canvas>
                                        <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Scan</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="max-h-[62vh] overflow-y-auto px-6 py-6 sm:px-8 sm:py-7">
                    <dl class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                        <template x-for="r in rows()" :key="r.label">
                            <div :class="r.full ? 'sm:col-span-2' : ''">
                                <dt class="flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-400 dark:text-gray-500">
                                    <span class="h-1 w-1 rounded-full bg-gradient-to-r from-primary-400 to-violet-400"></span>
                                    <span x-text="r.label"></span>
                                </dt>
                                <dd class="mt-1.5 text-sm text-gray-900 dark:text-gray-100"
                                    :class="r.mono ? 'font-mono text-[13px] tracking-tight text-gray-600 dark:text-gray-300' : ''">
                                    <template x-if="r.badge">
                                        <span :class="r.badge" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset" x-text="r.value"></span>
                                    </template>
                                    <template x-if="!r.badge && !r.rich">
                                        <span class="font-medium" x-text="r.value"></span>
                                    </template>
                                    <template x-if="!r.badge && r.rich">
                                        <blockquote class="mt-0.5 rounded-2xl border border-gray-100 bg-gray-50/70 px-6 py-3.5 text-[13px] leading-relaxed text-gray-600 dark:border-gray-800 dark:bg-gray-950/40 dark:text-gray-300" x-text="r.value"></blockquote>
                                    </template>
                                </dd>
                            </div>
                        </template>
                    </dl>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:px-8 dark:border-gray-800 dark:bg-gray-900/40">
                    <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="d.creator ? 'Dikelola oleh ' + d.creator : ''"></p>
                </div>
            </div>
        </div>

        {{-- Loading / not-found placeholder behind modal --}}
        <div class="flex min-h-screen flex-col items-center justify-center text-center" x-show="!open">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10">
                <svg class="h-8 w-8 animate-spin text-white/80" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-white/80">Memuat detail aset...</p>
        </div>
    </div>

    @push('scripts')
    <script>
        function assetPublicModal() {
            return {
                open: false,
                d: @json($detail),
                qrBase: @json(rtrim(config('app.url'), '/')),
                conditionLabels: { baik: 'Baik', rusak_ringan: 'Rusak Ringan', rusak_berat: 'Rusak Berat' },
                statusLabels: { tersedia: 'Tersedia', dipinjam: 'Dipinjam', dalam_perbaikan: 'Perbaikan', dihapuskan: 'Dihapuskan' },
                conditionPill(c) {
                    const map = {
                        baik: 'bg-emerald-400/20 text-emerald-50 ring-emerald-300/60',
                        rusak_ringan: 'bg-amber-400/20 text-amber-50 ring-amber-300/60',
                        rusak_berat: 'bg-red-400/20 text-red-50 ring-red-300/60',
                    };
                    return map[c] || 'bg-white/15 text-white ring-white/30';
                },
                statusPill(s) {
                    const map = {
                        tersedia: 'bg-emerald-400/20 text-emerald-50 ring-emerald-300/60',
                        dipinjam: 'bg-amber-400/20 text-amber-50 ring-amber-300/60',
                        dalam_perbaikan: 'bg-red-400/20 text-red-50 ring-red-300/60',
                        dihapuskan: 'bg-white/15 text-white/80 ring-white/30',
                    };
                    return map[s] || 'bg-white/15 text-white ring-white/30';
                },
                init() {
                    this.open = true;
                },
                barcode() {
                    const d = this.d || {};
                    if (!Array.isArray(d.fields)) return null;
                    const f = d.fields.find((x) => x.label === 'Barcode');
                    return f && f.value ? f.value : null;
                },
                qrUrl() {
                    const d = this.d || {};
                    if (!d.code) return null;
                    return this.qrBase + '/aset/' + encodeURIComponent(d.code);
                },
                rows() {
                    const d = this.d || {};
                    const conditionClasses = {
                        baik: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                        rusak_ringan: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                        rusak_berat: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    };
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
                        { label: 'Kondisi', value: this.conditionLabels[d.condition] || d.condition, badge: conditionClasses[d.condition] || fallbackBadge },
                        { label: 'Status', value: this.statusLabels[d.status] || d.status, badge: statusClasses[d.status] || fallbackBadge },
                        ...(Array.isArray(d.fields) ? d.fields.filter((f) => f.label !== 'Barcode').map((f) => ({
                            label: f.label,
                            value: f.value,
                        })) : []),
                        { label: 'Deskripsi', value: d.description, rich: true, full: true },
                    ].filter((r) => r.value !== null && r.value !== undefined && r.value !== '');
                },
            };
        }
    </script>
    @endpush
</x-guest-layout>
