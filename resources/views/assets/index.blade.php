<x-app-layout :title="$isMyAssets ? 'Asset Saya' : ($selectedCategory ? ucfirst($selectedCategory) : 'Data Asset')">
    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                {{ $isMyAssets ? 'Asset Saya' : ($selectedCategory ? ucfirst($selectedCategory) : 'Data Asset') }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $isMyAssets ? 'Menampilkan aset dengan PIC atas nama Anda' : 'Kelola data asset perusahaan' }}</p>
        </div>
    @endpush

    <div class="space-y-6" x-data="assetDetailModal()">
        @php
            $startNo = $assets instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($assets->firstItem() ?: 1) : 1;
        @endphp
        @if($isSimCard)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3v-1.5m-3 0h3m-3 0v-3m3 3V0m-3 3v6m3-6v6m-3 0h3"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total SIM Card</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3v-1.5m-3 0h3m-3 0v6m3-6v6m-3 0h3"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Aktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['aktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SIM Aktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/40 dark:shadow-amber-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-3 3.187A8.998 8.998 0 109 12.5m0 0V12m0 0h.008M12 6.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning text-[10px]">Awas</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['segera_habis']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Segera Habis</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-amber-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m-3 3.15h.008M12 6.75h.008M12 12a9 9 0 100 18 9 9 0 000-18z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/40 dark:shadow-red-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Mati</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['mati']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">SIM Mati</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-red-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.617 6.468A9 9 0 005.383 17.532m14.234-11.064L3.25 10.09a1.5 1.5 0 00.538 2.28l1.31.65m16.5-2.123a1.5 1.5 0 01.538 2.28l-1.31.65"/></svg>
                </div>
            </div>
        </div>
        @elseif($isKendaraan)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Kendaraan</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Aktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['pajak_aktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pajak Aktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/40 dark:shadow-amber-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-3 3.187A8.998 8.998 0 109 12.5m0 0V12m0 0h.008M12 6.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning text-[10px]">Awas</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['segera_habis']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Segera Habis</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-amber-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m-3 3.15h.008M12 6.75h.008M12 12a9 9 0 100 15 9 9 0 000-15z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/40 dark:shadow-red-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.617 6.468A9 9 0 005.383 17.532m14.234-11.064L3.25 10.09a1.5 1.5 0 00.538 2.28l1.31.65m16.5-2.123a1.5 1.5 0 01.538 2.28l-1.31.65"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Mati</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['pajak_mati']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pajak Mati</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-red-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.617 6.468A9 9 0 005.383 17.532m14.234-11.064L3.25 10.09a1.5 1.5 0 00.538 2.28l1.31.65m16.5-2.123a1.5 1.5 0 01.538 2.28l-1.31.65m0 0l4.35 2.175a1.5 1.5 0 01-.538 2.28l-1.31.65"/></svg>
                </div>
            </div>
        </div>
        @elseif($isSosialMedia)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Akun</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Aktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['aktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Akun Aktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/40 dark:shadow-red-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4.5m0 4.5h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Tidak Aktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['tidak_aktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Akun Tidak Aktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-red-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.75 12.5a8.75 8.75 0 11-17.5 0 8.75 8.75 0 0117.5 0z"/></svg>
                </div>
            </div>
        </div>
        @elseif($isAssetMes)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Asset Mes</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM19.5 21v-2.25a4.5 4.5 0 00-4.5-4.5h-6a4.5 4.5 0 00-4.5 4.5V21"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Putra</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['putra']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Asset Mes Putra</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM19.5 21v-2.25.4.5a4.5 4.5 0 00-4.5-4.5h-6a4.5 4.5 0 00-4.5 4.5V21"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-pink-200 dark:hover:border-pink-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 text-white shadow-lg shadow-pink-500/40 dark:shadow-pink-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Putri</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['putri']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Asset Mes Putri</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-pink-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
            </div>
</div>
        @elseif($isAsetTim)
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Aset Tim</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Aktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['aktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aset Tim Aktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/40 dark:shadow-red-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Nonaktif</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['nonaktif']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aset Tim Nonaktif</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-red-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.617 6.468A9 9 0 005.383 17.532m14.234-11.064L3.25 10.09a1.5 1.5 0 00.538 2.28l1.31.65m16.5-2.123a1.5 1.5 0 01.538 2.28l-1.31.65m0 0l4.35 2.175a1.5 1.5 0 01-.538 2.28l-1.31.65"/></svg>
                </div>
            </div>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Keseluruhan</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Aset</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-blue-500">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/40 dark:shadow-emerald-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success text-[10px]">Kondisi</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['baik']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kondisi Baik</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-emerald-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    @if($isPeralatanKantor ?? false)
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-500/40 dark:shadow-violet-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 013 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                    </div>
                    <span class="badge-info text-[10px]">Nilai</span>
                    @else
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/40 dark:shadow-amber-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/></svg>
                    </div>
                    <span class="badge-warning text-[10px]">Awas</span>
                    @endif
                </div>
                @if($isPeralatanKantor ?? false)
                <p class="text-xl font-bold font-display text-gray-900 dark:text-gray-100">Rp {{ number_format($stats['total_nilai'] ?? 0, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Nilai</p>
                @else
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['perlu_diservis']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Perlu Diservis</p>
                @endif
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] {{ ($isPeralatanKantor ?? false) ? 'text-violet-500' : 'text-amber-500' }} pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-lg shadow-red-500/40 dark:shadow-red-900/50 group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-danger text-[10px]">Bahaya</span>
                </div>
                <p class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ number_format($stats['rusak']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rusak</p>
                <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.04] dark:opacity-[0.06] text-red-500 pointer-events-none">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM20.617 6.468A9 9 0 005.383 17.532m14.234-11.064L3.25 10.09a1.5 1.5 0 00.538 2.28l1.31.65m16.5-2.123a1.5 1.5 0 01.538 2.28l-1.31.65m0 0l4.35 2.175a1.5 1.5 0 01-.539 2.28l-1.31.65"/></svg>
                </div>
            </div>
        </div>
        @endif

        <div class="card overflow-hidden" x-data="{ mesTab: 'putra' }">
            <div class="flex flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Daftar Asset
                </h2>
                @if($isAssetMes)
                <div class="inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
                    <button @click="mesTab = 'putra'" :class="mesTab === 'putra' ? 'bg-white dark:bg-gray-900 shadow-sm text-blue-600' : 'text-gray-500 dark:text-gray-400'" class="rounded-lg px-4 py-1.5 text-xs font-semibold transition-all duration-200">
                        Mes Putra
                    </button>
                    <button @click="mesTab = 'putri'" :class="mesTab === 'putri' ? 'bg-white dark:bg-gray-900 shadow-sm text-pink-600' : 'text-gray-500 dark:text-gray-400'" class="rounded-lg px-4 py-1.5 text-xs font-semibold transition-all duration-200">
                        Mes Putri
                    </button>
                </div>
                @endif
                <div class="flex items-center gap-2">
                    <form method="GET" action="{{ route('assets.index') }}" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                               class="input-field w-40 text-xs pl-8 py-2">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </form>
                </div>
            </div>

            @php
                $isPeralatanKantor = $selectedCategory && strtolower(str_replace('-', ' ', $selectedCategory)) === 'peralatan kantor';
                $isSosialMedia = $selectedCategory && strtolower(str_replace('-', ' ', $selectedCategory)) === 'sosial media';
                $colspan = $isPeralatanKantor ? 9 : ($isSosialMedia ? 9 : ($selectedCategory ? 7 : 8));
            @endphp
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    @if($isPeralatanKantor)
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-10">No</th>
                            <th class="px-6 py-3">Kode Aset</th>
                            <th class="px-6 py-3">Nama Barang</th>
                            <th class="px-6 py-3 text-center">Jumlah</th>
                            <th class="px-6 py-3 text-right">Nilai (Rp)</th>
                            <th class="px-6 py-3 text-right">Harga Saat Ini</th>
                            <th class="px-6 py-3 text-center">Kondisi</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        @php $m = (array) ($a->metadata ?? []); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $a->code }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $a->name }}</td>
                            <td class="table-cell text-center">{{ $m['jumlah'] ?? '-' }}</td>
                            <td class="table-cell text-right font-medium tabular-nums text-gray-700 dark:text-gray-300">{{ isset($m['nilai']) && $m['nilai'] !== '' ? 'Rp ' . number_format((float) $m['nilai'], 0, ',', '.') : '-' }}</td>
                            <td class="table-cell text-right font-medium tabular-nums text-gray-700 dark:text-gray-300">{{ isset($m['harga_per_hari_ini']) && $m['harga_per_hari_ini'] !== '' ? 'Rp ' . number_format((float) $m['harga_per_hari_ini'], 0, ',', '.') : '-' }}</td>
                            <td class="table-cell text-center">
                                @php
                                    $pconditionLabels = ['baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat'];
                                    $pconditionClasses = ['baik' => 'badge-success', 'rusak_ringan' => 'badge-warning', 'rusak_berat' => 'badge-danger'];
                                @endphp
                                <span class="{{ $pconditionClasses[$a->condition] ?? 'badge-info' }}">{{ $pconditionLabels[$a->condition] ?? $a->condition }}</span>
                            </td>
                            <td class="table-cell text-center">
                                @php
                                    $pstatusLabels = ['tersedia' => 'Tersedia', 'dipinjam' => 'Dipinjam', 'dalam_perbaikan' => 'Perbaikan', 'dihapuskan' => 'Dihapuskan'];
                                    $pstatusClasses = ['tersedia' => 'badge-success', 'dipinjam' => 'badge-warning', 'dalam_perbaikan' => 'badge-danger', 'dihapuskan' => 'badge-info'];
                                @endphp
                                <span class="{{ $pstatusClasses[$a->status] ?? 'badge-info' }}">{{ $pstatusLabels[$a->status] ?? $a->status }}</span>
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
                            <td colspan="{{ $colspan }}" class="px-6 py-16 text-center">
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
@elseif($isSimCard)
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-12">No</th>
                            <th class="px-6 py-3">No SIM Card</th>
                            <th class="px-6 py-3">PIC</th>
                            <th class="px-6 py-3">Atasan</th>
                            <th class="px-6 py-3">Keperluan</th>
                            <th class="px-6 py-3">Masa Aktif</th>
                            <th class="px-6 py-3">Masa Tenggang</th>
                            <th class="px-6 py-3 text-center">Hari</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        @php
                            $sim = [];
                            foreach (explode(' | ', $a->description ?? '') as $part) {
                                $pair = explode(': ', $part, 2);
                                if (count($pair) === 2) {
                                    $sim[$pair[0]] = $pair[1];
                                }
                            }
                            $masaAktif = $a->purchase_date;
                            $hari = null;
                            $expired = false;
                            if ($masaAktif && $a->status === 'tersedia') {
                                $diff = $masaAktif->diffInDays(now(), false);
                                $hari = (int) $diff;
                                $expired = $hari < 0;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $a->code }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $sim['PIC'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $sim['Atasan'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $sim['Keperluan'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $masaAktif ? $masaAktif->format('d/m/Y') : '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $sim['Masa Tenggang'] ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @if($a->status !== 'tersedia')
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @elseif($expired)
                                    <span class="badge-danger">H+{{ abs($hari) }}</span>
                                @elseif($hari !== null && $hari <= 30)
                                    <span class="badge-warning">H-{{ $hari }}</span>
@elseif($hari !== null)
                                    <span class="badge-success">H-{{ $hari }}</span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <span class="{{ $a->status === 'tersedia' ? 'badge-success' : 'badge-danger' }}">{{ $a->status === 'tersedia' ? 'Aktif' : 'Mati' }}</span>
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
                            <td colspan="10" class="px-6 py-16 text-center">
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
                    @elseif($isKendaraan)
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-10">No</th>
                            <th class="px-6 py-3">Nama Kendaraan</th>
                            <th class="px-6 py-3">Nomor Polisi</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3">Merk/Tipe</th>
                            <th class="px-6 py-3 text-center">Tahun</th>
                            <th class="px-6 py-3">Warna</th>
                            <th class="px-6 py-3 whitespace-nowrap">Nomor Rangka</th>
                            <th class="px-6 py-3 whitespace-nowrap">Nomor Mesin</th>
                            <th class="px-6 py-3">Foto</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        @php $v = (array) ($a->metadata ?? []); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $v['nama_kendaraan'] ?? $a->name }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $v['plat_nomor'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $v['jenis_kendaraan'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $v['merk_tipe'] ?? '-' }}</td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $v['tahun'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $v['warna'] ?? '-' }}</td>
                            <td class="table-cell font-mono text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $v['nomor_rangka'] ?? '-' }}</td>
                            <td class="table-cell font-mono text-[11px] text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $v['nomor_mesin'] ?? '-' }}</td>
                            <td class="table-cell">
                                @if(!empty($v['foto']))
                                <button type="button" @click="showPhoto(@js($v['foto']), @js($v['nama_kendaraan'] ?? $a->name))" class="group relative inline-flex overflow-hidden rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 transition-all duration-300 hover:ring-blue-400 dark:hover:ring-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                    <img src="{{ $v['foto'] }}" alt="foto {{ $v['nama_kendaraan'] ?? $a->name }}" class="h-11 w-14 rounded-lg object-cover transition-transform duration-300 group-hover:scale-110">
                                    <span class="absolute inset-0 flex items-center justify-center bg-slate-900/0 opacity-0 transition-all duration-300 group-hover:bg-slate-900/40 group-hover:opacity-100">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </span>
                                </button>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                @if(strtolower((string) ($v['status_pajak'] ?? '')) === 'mati')
                                    <span class="badge-danger">Mati</span>
                                @else
                                    <span class="badge-success">Aktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $v['keperluan'] ?? '-' }}</td>
                            <td class="table-cell text-right">
                                <button type="button" @click="show({{ $a->id }})" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="px-6 py-16 text-center">
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
                    @elseif($isSosialMedia)
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-10">No</th>
                            <th class="px-6 py-3">Username</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3 text-center">Followers</th>
                            <th class="px-6 py-3">Platform</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3">Divisi</th>
                            <th class="px-6 py-3">PIC</th>
                            <th class="px-6 py-3 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        @php $s = (array) ($a->metadata ?? []); @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $s['username'] ?? $a->code }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $s['nama'] ?? '-' }}</td>
                            <td class="table-cell text-center tabular-nums text-gray-700 dark:text-gray-300">{{ ($s['followers'] ?? null) !== null && $s['followers'] !== '-' ? $s['followers'] : '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $s['platform'] ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @if(strtolower((string) ($s['status_akun'] ?? $s['status'] ?? '')) === 'aktif')
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $s['divisi'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $s['pic'] ?? '-' }}</td>
                            <td class="table-cell text-right">
                                <button type="button" @click="show({{ $a->id }})" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
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
                    @elseif($isAssetMes || $isAsetTim)
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-10">No</th>
                            @if($isAssetMes)
                            <th class="px-6 py-3">Nama Aset</th>
                            <th class="px-6 py-3">Kategori</th>
                            @else
                            <th class="px-6 py-3">Nama Aset</th>
                            <th class="px-6 py-3">Tim</th>
                            @endif
                            <th class="px-6 py-3 text-center">Jumlah</th>
                            <th class="px-6 py-3 whitespace-nowrap">Penanggung Jawab</th>
                            <th class="px-6 py-3">PIC</th>
                            <th class="px-6 py-3">Jabatan</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        @php
                            $m = (array) ($a->metadata ?? []);
                            if ($isAssetMes) {
                                $isPutri = str_contains(strtolower((string) ($m['kategori'] ?? $a->name)), 'putri');
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors" @if($isAssetMes) x-show="mesTab === '{{ $isPutri ? 'putri' : 'putra' }}'" @endif>
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $m['nama_aset'] ?? $a->name }}</td>
                            @if($isAssetMes)
                            <td class="table-cell">
                                @if(str_contains(strtolower((string) ($m['kategori'] ?? $a->name)), 'putri'))
                                    <span class="badge-danger">Mes Putri</span>
                                @else
                                    <span class="badge-success">Mes Putra</span>
                                @endif
                            </td>
                            @else
                            <td class="table-cell">
                                <span class="badge-info">{{ $m['tim'] ?? '-' }}</span>
                            </td>
                            @endif
                            <td class="table-cell text-center tabular-nums">{{ $m['jumlah'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $m['penanggung_jawab'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $m['pic'] ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $m['jabatan'] ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @if(strtolower((string) ($m['status'] ?? '')) === 'aktif')
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $m['keterangan'] ?? '-' }}</td>
                            <td class="table-cell text-right">
                                <button type="button" @click="show({{ $a->id }})" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="px-6 py-16 text-center">
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
                    @else
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 text-center w-12">No</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Nama Asset</th>
                            <th class="px-6 py-3">Kategori</th>
                            @if(!$selectedCategory)
                            <th class="px-6 py-3">Brand</th>
                            @endif
                            <th class="px-6 py-3 text-center">Kondisi</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-right w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($assets as $a)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $startNo + $loop->index }}</td>
                            <td class="table-cell font-mono text-xs text-gray-500 dark:text-gray-400">{{ $a->code }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $a->name }}</td>
                            <td class="table-cell">
                                <span class="badge-info">{{ $a->category?->name ?? '-' }}</span>
                            </td>
                            @if(!$selectedCategory)
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $a->brand ?? '-' }}</td>
                            @endif
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
                            <td colspan="{{ $colspan }}" class="px-6 py-16 text-center">
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
                    @endif
                </table>
            </div>
            @if(method_exists($assets, 'hasPages') && $assets->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $assets->links() }}
            </div>
            @endif
        </div>
        {{-- Detail Modal --}}
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex max-h-full items-center justify-center overflow-y-auto p-4 sm:p-6"
             @keydown.escape.window="open = false">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md" @click="open = false"></div>
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

                    <button type="button" @click="open = false"
                            class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white/90 backdrop-blur transition-all duration-300 hover:rotate-90 hover:bg-white/20 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="relative flex items-start gap-4 pr-10">
                        <div class="relative flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 shadow-inner backdrop-blur-md ring-1 ring-white/25">
                            <svg class="h-8 w-8 text-white drop-shadow" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-emerald-400 ring-4 ring-white/20"></span>
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
                            <div class="relative mt-4 flex w-fit items-center gap-4 rounded-2xl bg-white/95 px-6 py-3 shadow-lg ring-1 ring-white/30 backdrop-blur">
                                <div class="flex flex-col items-center gap-1.5">
                                    <svg class="h-14 w-full max-w-[220px]" x-cloak x-init="JsBarcode($el, barcode(), { format: 'CODE128', displayValue: false, margin: 0, background: 'transparent', lineColor: '#0f172a' })"></svg>
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
                    <div x-show="loading" class="flex flex-col items-center justify-center py-16">
                        <svg class="h-9 w-9 animate-spin text-primary-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="mt-3 text-sm font-medium text-gray-400 dark:text-gray-500">Memuat detail asetâ€¦</span>
                    </div>
                    <template x-if="!loading && rows().length">
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
                    </template>
                    <div x-show="!loading && !rows().length" class="py-12 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                            <svg class="h-7 w-7 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <p class="mt-3 text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada detail yang tersedia.</p>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:px-8 dark:border-gray-800 dark:bg-gray-900/40">
                    <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500" x-text="d.creator ? 'Dikelola oleh ' + d.creator : ''"></p>
                    <button type="button" @click="open = false"
                            class="rounded-xl bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 transition-colors hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- Photo Lightbox Modal --}}
        <div x-show="photoOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 sm:p-8"
             @keydown.escape.window="photoOpen = false">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md dark:bg-slate-950/85" @click="photoOpen = false"></div>
            <div x-show="photoOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-[0.94]"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-[0.94]"
                 @click.stop
                 class="relative flex max-h-full w-full max-w-4xl flex-col overflow-hidden rounded-3xl bg-white shadow-[0_48px_120px_-32px_rgba(8,60,150,0.45)] ring-1 ring-gray-200/70 dark:bg-slate-900 dark:shadow-[0_48px_120px_-30px_rgba(2,8,23,0.8)] dark:ring-white/10">
                <div class="flex items-center justify-between gap-4 px-6 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-violet-600 text-white shadow-lg shadow-primary-500/30">
                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 6h.008v.008H18V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5A1.5 1.5 0 0021.75 19.5V4.5A1.5 1.5 0 0020.25 3H3.75A1.5 1.5 0 002.25 4.5v15A1.5 1.5 0 003.75 21z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400 dark:text-white/50">Foto Kendaraan</p>
                            <h3 class="truncate font-display text-lg font-bold text-gray-900 dark:text-white" x-text="photoName"></h3>
                        </div>
                    </div>
                    <button type="button" @click="photoOpen = false"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-500 transition-all duration-300 hover:rotate-90 hover:bg-gray-200 hover:text-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:bg-white/10 dark:text-white/90 dark:hover:bg-white/20 dark:hover:text-white dark:focus-visible:ring-white/70">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex min-h-0 flex-1 items-center justify-center overflow-auto p-6 pt-0">
                    <img :src="photoUrl" :alt="photoName"
                         class="max-h-[72vh] max-w-full rounded-2xl object-contain shadow-xl ring-1 ring-gray-200 dark:shadow-2xl dark:ring-white/15">
                </div>
                <div class="flex items-center justify-end border-t border-gray-100 px-6 py-3 dark:border-white/10">
                    <a :href="photoUrl" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-gray-100 px-3.5 py-1.5 text-xs font-semibold text-gray-600 ring-1 ring-gray-200 transition-colors hover:bg-gray-200 hover:text-gray-800 dark:bg-white/10 dark:text-white/80 dark:ring-white/15 dark:hover:bg-white/20 dark:hover:text-white">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        Buka di tab baru
                    </a>
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
                photoOpen: false,
                photoUrl: '',
                photoName: '',
                urlTemplate: @json(route('assets.detail', ['asset' => '__ID__'])),
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
                showPhoto(url, name) {
                    this.photoUrl = url;
                    this.photoName = name || 'Foto Aset';
                    this.photoOpen = true;
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
                    return @json(rtrim(config('app.url'), '/')) + '/aset/' + encodeURIComponent(d.code);
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
                    ].filter((r) => r.value !== null && r.value !== undefined && r.value !== '');
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
