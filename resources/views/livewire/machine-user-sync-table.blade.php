<div>
    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-4 md:mb-6">
        <div class="stat-card group p-4 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl md:h-12 md:w-12 bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <span class="badge-info">Total</span>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-gray-100 md:text-2xl">{{ number_format($totalIds, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:text-sm">User ID Mesin</p>
        </div>

        <div class="stat-card group p-4 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl md:h-12 md:w-12 bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success text-[10px]">Terpetakan</span>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-gray-100 md:text-2xl">{{ number_format($mappedIds, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:text-sm">User Terpetakan Karyawan</p>
        </div>

        <div class="stat-card group p-4 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl md:h-12 md:w-12 bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-warning">Belum</span>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-gray-100 md:text-2xl">{{ number_format($totalIds - $mappedIds, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:text-sm">Belum Terpetakan</p>
        </div>

        <div class="stat-card group p-4 md:p-5">
            <div class="flex items-center justify-between mb-2 md:mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl md:h-12 md:w-12 bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Pending</span>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-gray-100 md:text-2xl">{{ number_format($pendingPunches, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:text-sm">Punch Belum Diproses</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">dari {{ number_format($totalPunches, 0, ',', '.') }} total punch</p>
        </div>
    </div>

    <div class="card">
        {{-- Filter bar --}}
        <div class="flex flex-col gap-3 border-b border-gray-50 px-4 py-4 dark:border-gray-800 lg:flex-row lg:items-center lg:justify-between lg:gap-4 lg:px-6">
            <div class="flex w-full flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:gap-3 lg:w-auto">
                <div class="relative w-full min-w-0 flex-1 sm:max-w-xs">
                    <label for="sinkron-search" class="sr-only">Cari User ID mesin</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        id="sinkron-search"
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari User ID..."
                        class="w-full min-h-11 rounded-xl border border-gray-200 bg-white pl-9 pr-3 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200 sm:text-xs"
                    >
                </div>

                <div class="w-full sm:w-auto">
                    <label for="sinkron-status" class="sr-only">Filter status mapping</label>
                    <select id="sinkron-status" wire:model.live="filterStatus" class="w-full min-h-11 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200 sm:text-xs">
                        <option value="">Semua Status</option>
                        <option value="unmapped">Belum Terpetakan</option>
                        <option value="mapped">Sudah Terpetakan</option>
                    </select>
                </div>
            </div>

            <div class="flex w-full flex-col gap-2 sm:flex-row sm:justify-end lg:ml-auto lg:w-auto lg:shrink-0">
                <button type="button" wire:click="syncMachineUsers" wire:loading.attr="disabled" class="btn-secondary min-h-11 w-full justify-center text-xs sm:w-auto">
                    <svg wire:loading wire:target="syncMachineUsers" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    <span wire:loading.remove wire:target="syncMachineUsers">Tarik Nama dari Mesin</span>
                    <span wire:loading wire:target="syncMachineUsers">Menarik...</span>
                </button>

                <button type="button" wire:click="backfill" wire:confirm="Proses semua punch yang belum terpetakan ke absensi? Proses ini bisa memakan waktu beberapa detik." wire:loading.attr="disabled" class="btn-primary min-h-11 w-full justify-center text-xs sm:w-auto">
                    <svg wire:loading wire:target="backfill" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    <span wire:loading.remove wire:target="backfill">Proses Backfill</span>
                    <span wire:loading wire:target="backfill">Memproses...</span>
                </button>
            </div>
        </div>

        {{-- Mobile: kartu User ID mesin --}}
        <div class="divide-y divide-gray-100 md:hidden dark:divide-gray-800">
            @forelse($machineUsers as $m)
                <article class="px-4 py-3.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $m->machine_user_id }}</p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $m->machine_name ?? '-' }}</p>
                        </div>
                        @if($m->employee_id)
                            <span class="badge-success shrink-0">Terpetakan</span>
                        @else
                            <span class="badge-warning shrink-0">Belum dipetakan</span>
                        @endif
                    </div>

                    <dl class="mt-3 grid grid-cols-3 gap-2">
                        <div class="rounded-xl bg-gray-50 px-2 py-2 text-center dark:bg-gray-800/70">
                            <dt class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Total Tap</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($m->total_taps, 0, ',', '.') }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-2 py-2 text-center dark:bg-gray-800/70">
                            <dt class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Tap Pertama</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($m->pertama)->format('d M H:i') }}</dd>
                        </div>
                        <div class="rounded-xl bg-gray-50 px-2 py-2 text-center dark:bg-gray-800/70">
                            <dt class="text-[11px] font-medium text-gray-500 dark:text-gray-400">Tap Terakhir</dt>
                            <dd class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($m->terakhir)->format('d M H:i') }}</dd>
                        </div>
                    </dl>

                    @if($m->employee_id)
                        <p class="mt-2.5 text-xs text-gray-600 dark:text-gray-300">
                            {{ $m->employee_nama }} <span class="font-mono text-[11px] text-gray-400">({{ $m->employee_nik }})</span>
                        </p>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if(!$m->employee_id)
                            <button type="button" wire:click="openMapModal('{{ $m->machine_user_id }}')"
                                class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 rounded-xl border border-primary-200 bg-primary-50 px-3 py-2 text-xs font-semibold text-primary-700 transition-colors hover:bg-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-primary-800 dark:bg-primary-900/30 dark:text-primary-300 dark:hover:bg-primary-900/50">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Petakan
                            </button>
                        @endif
                        @if($m->employee_id)
                            <button type="button" wire:click="openUnmapModal('{{ $m->machine_user_id }}')"
                                class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Lepas Mapping
                            </button>
                        @endif
                        @if(auth()->user()->isSuperAdminLike())
                            <button type="button" wire:click="openDeleteModal('{{ $m->machine_user_id }}')"
                                class="inline-flex min-h-11 flex-1 items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition-colors hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-300 dark:hover:bg-red-950/50">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Hapus
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="flex flex-col items-center justify-center px-4 py-12 text-center">
                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900">
                        <svg class="h-7 w-7 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07zM12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tidak ada data User ID mesin</h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tarik data mesin absen terlebih dahulu</p>
                </div>
            @endforelse
        </div>

        {{-- Table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">User ID Mesin</th>
                        <th class="px-6 py-3">Nama di Mesin</th>
                        <th class="px-6 py-3 text-center">Jumlah Tap</th>
                        <th class="px-6 py-3">Tap Pertama</th>
                        <th class="px-6 py-3">Tap Terakhir</th>
                        <th class="px-6 py-3">Mapping Karyawan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($machineUsers as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $machineUsers->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $m->machine_user_id }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                {{ $m->machine_name ?? '-' }}
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ number_format($m->total_taps, 0, ',', '.') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->pertama)->format('d M Y H:i') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->terakhir)->format('d M Y H:i') }}</td>
                            <td class="table-cell">
                                @if($m->employee_id)
                                    <div class="flex items-center gap-2">
                                        <span class="badge-success">Terpetakan</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $m->employee_nama }}</span>
                                        <span class="text-[11px] text-gray-400 font-mono">({{ $m->employee_nik }})</span>
                                    </div>
                                @else
                                    <span class="badge-warning">Belum dipetakan</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    @if(!$m->employee_id)
                                    <button type="button" wire:click="openMapModal('{{ $m->machine_user_id }}')" class="inline-flex min-h-11 items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Map
                                    </button>
                                    @endif
                                    @if(auth()->user()->isSuperAdminLike())
                                    <button type="button" wire:click="openDeleteModal('{{ $m->machine_user_id }}')" class="inline-flex min-h-11 items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="Hapus User ID">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                    @endif
                                    @if($m->employee_id)
                                    <button type="button" wire:click="openUnmapModal('{{ $m->machine_user_id }}')" class="inline-flex min-h-11 items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Unmap
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tidak ada data User ID mesin</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tarik data mesin absen terlebih dahulu</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($machineUsers->hasPages())
            <div class="border-t border-gray-50 px-4 py-3 dark:border-gray-800 sm:px-6">
                {{ $machineUsers->links() }}
            </div>
        @endif
    </div>

    {{-- MAP MODAL --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showMapModal') }"
         x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end justify-center bg-gray-900/60 backdrop-blur-sm sm:items-center sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="sinkron-map-title"
         @keydown.escape.window="open = false"
         @click="open = false">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative max-h-[90dvh] w-full max-w-lg overflow-y-auto rounded-t-3xl bg-white p-6 shadow-2xl dark:bg-gray-800 sm:my-8 sm:rounded-2xl sm:p-8">
            <div class="mb-6 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 id="sinkron-map-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Petakan User ID
                        @if($mapMachineUserId)<span class="font-mono text-primary-600 dark:text-primary-400">{{ $mapMachineUserId }}</span>@endif
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cari karyawan berdasarkan NIK atau nama</p>
                </div>
                <button type="button" wire:click="closeMapModal" aria-label="Tutup pemetaan user ID"
                    class="-mr-1 flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveMapping" class="space-y-4">
                <div>
                    <x-input-label for="map-search" value="Cari Karyawan" />
                    <div class="relative mt-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input id="map-search" type="text" wire:model.live.debounce.200ms="mapSearch" placeholder="Ketik NIK atau nama..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" />
                    </div>
                </div>

                <div>
                    <x-input-label for="map-employee" value="Karyawan" />
                    <select id="map-employee" wire:model="selectedEmployeeId" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih karyawan --</option>
                        @foreach($mapEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                        @endforeach
                    </select>
                    @error('selectedEmployeeId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @if($mapSearch && $mapEmployees->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">Tidak ada karyawan yang cocok dengan pencarian.</p>
                    @endif
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
                    <button type="button" wire:click="closeMapModal" class="btn-secondary min-h-11 w-full justify-center text-xs sm:w-auto">Batal</button>
                    <button type="submit" class="btn-primary min-h-11 w-full justify-center text-xs sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan Mapping
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- UNMAP MODAL --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showUnmapModal') }"
         x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end justify-center bg-gray-900/60 backdrop-blur-sm sm:items-center sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="sinkron-unmap-title"
         @keydown.escape.window="open = false"
         @click="open = false">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative max-h-[90dvh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-6 shadow-2xl dark:bg-gray-800 sm:my-8 sm:rounded-2xl sm:p-8">
            <div class="mb-6 flex items-start justify-between gap-3">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 id="sinkron-unmap-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Lepas Mapping</h3>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Konfirmasi tindakan Anda</p>
                    </div>
                </div>
                <button type="button" wire:click="closeUnmapModal" aria-label="Tutup konfirmasi lepas mapping"
                    class="-mr-1 flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                Mapping User ID <span class="font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $unmapMachineUserId }}</span>
                akan dilepas dari <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $unmapEmployeeName }}</span>. Punch mesin tetap tersimpan dan tidak akan terhubung ke karyawan lagi.
            </p>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
                <button type="button" wire:click="closeUnmapModal" class="btn-secondary min-h-11 w-full justify-center text-xs sm:w-auto">Batal</button>
                <button type="button" wire:click="confirmUnmap" class="inline-flex min-h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-gray-600 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Ya, Lepas Mapping
                </button>
            </div>
        </div>
    </div>
    </template>

    {{-- DELETE MODAL --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showDeleteModal') }"
         x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end justify-center bg-gray-900/60 backdrop-blur-sm sm:items-center sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="sinkron-delete-title"
         @keydown.escape.window="open = false"
         @click="open = false">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative max-h-[90dvh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-6 shadow-2xl dark:bg-gray-800 sm:my-8 sm:rounded-2xl sm:p-8">
            <div class="mb-6 flex items-start justify-between gap-3">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-50 dark:bg-red-950/40">
                        <svg class="w-6 h-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </div>
                    <div>
                        <h3 id="sinkron-delete-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Hapus User ID Mesin</h3>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <button type="button" wire:click="closeDeleteModal" aria-label="Tutup konfirmasi hapus user ID"
                    class="-mr-1 flex min-h-11 min-w-11 shrink-0 items-center justify-center rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                User ID mesin <span class="font-mono font-semibold text-red-600 dark:text-red-400">{{ $deleteMachineUserId }}</span>
                @if($deleteMachineName && $deleteMachineName !== $deleteMachineUserId)
                    ({{ $deleteMachineName }})
                @endif
                beserta seluruh punch-nya akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:items-center sm:justify-end dark:border-gray-700">
                <button type="button" wire:click="closeDeleteModal" class="btn-secondary min-h-11 w-full justify-center text-xs sm:w-auto">Batal</button>
                <button type="button" wire:click="confirmDelete" class="inline-flex min-h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-red-600 px-4 py-2 text-xs font-medium text-white transition-colors hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Ya, Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    </template>

    {{-- SUCCESS MODAL --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showSuccessModal') }"
         x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] flex items-end justify-center bg-gray-900/60 backdrop-blur-sm sm:items-center sm:p-4"
         role="dialog" aria-modal="true" aria-labelledby="sinkron-success-title"
         @keydown.escape.window="open = false"
         @click="open = false">
        <div @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative max-h-[90dvh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white p-6 shadow-2xl dark:bg-gray-800 sm:my-8 sm:rounded-2xl sm:p-8">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-4">
                <svg x-show="open"
                     x-transition:enter="transition-all ease-out duration-500 delay-150"
                     x-transition:enter-start="opacity-0 scale-50"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 id="sinkron-success-title" class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $successTitle }}</h3>
            <p class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ $successMessage }}</p>
            <div class="flex items-center justify-center border-t border-gray-100 pt-4 dark:border-gray-700">
                <button type="button" wire:click="closeSuccessModal" class="btn-primary min-h-11 w-full justify-center text-xs sm:w-auto">Tutup</button>
            </div>
        </div>
    </div>
    </template>
</div>
