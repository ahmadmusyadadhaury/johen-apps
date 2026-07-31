@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Daily Tracking {{ $divisi }}</h1>
        <p class="text-xs text-gray-400 mt-0.5">Tracking harian pemain</p>
    </div>
@endpush

<div x-data="{ showFotoModal: false, fotoModalUrl: '', fotoModalLabel: '' }">
    @if(auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffHostMlbb() || auth()->user()->isStaffHostEfootball() || auth()->user()->isStaffHostValorant() || auth()->user()->isStaffHostRoblox() || auth()->user()->isStaffHostMonkeyPubg() || auth()->user()->isKoordinatorGame())
    <div x-data="{ modal: null }" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div @click="modal = 'sold'" class="stat-card group cursor-pointer">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </div>
                <span class="badge-info">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalSold, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sold</p>
            <div class="mt-2 flex items-center gap-1 text-[11px] font-medium text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <div @click="modal = 'view'" class="stat-card group cursor-pointer">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white shadow-lg shadow-teal-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <span class="badge-success">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalView, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total View</p>
            <div class="mt-2 flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <div @click="modal = 'peak'" class="stat-card group cursor-pointer">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-red-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <span class="badge-warning">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalPeak, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Peak</p>
            <div class="mt-2 flex items-center gap-1 text-[11px] font-medium text-amber-600 dark:text-amber-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        <div @click="modal = 'durasi'" class="stat-card group cursor-pointer">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalDurasi, 0, ',', '.') }} Jam</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Durasi</p>
            <div class="mt-2 flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>

        {{-- Detail Modal Sold --}}
        <div x-show="modal === 'sold'" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="modal = null">
            <div x-show="modal === 'sold'" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Total Sold per Host</h3>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @forelse($soldBreakdown as $d)
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $d->nama }}</span>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ number_format($d->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Detail Modal View --}}
        <div x-show="modal === 'view'" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="modal = null">
            <div x-show="modal === 'view'" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Total View per Host</h3>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @forelse($viewBreakdown as $d)
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $d->nama }}</span>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($d->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Detail Modal Peak --}}
        <div x-show="modal === 'peak'" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="modal = null">
            <div x-show="modal === 'peak'" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Total Peak per Host</h3>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @forelse($peakBreakdown as $d)
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $d->nama }}</span>
                        <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">{{ number_format($d->total, 0, ',', '.') }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Detail Modal Durasi --}}
        <div x-show="modal === 'durasi'" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="modal = null">
            <div x-show="modal === 'durasi'" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Total Durasi per Host</h3>
                    <button @click="modal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    @forelse($durasiBreakdown as $d)
                    <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-900">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $d->nama }}</span>
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($d->total, 0, ',', '.') }} Jam</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Daily Tracking {{ $divisi }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Data daily tracking divisi {{ $divisi }}</p>
            </div>
            <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Data
            </button>
        </div>

        {{-- Filter --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIK atau Nama..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
            </div>
            <select wire:model.live="bulan" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-3 pr-8 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                    @php $val = now()->format('Y') . '-' . str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                    <option value="{{ $val }}">{{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}</option>
                @endforeach
            </select>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 text-center border-r border-gray-200 dark:border-gray-600">Tanggal</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Sesi</th>
                        <th class="px-6 py-3 text-right">Sold</th>
                        <th class="px-6 py-3 text-right">View</th>
                        <th class="px-6 py-3 text-right">Peak View</th>
                        <th class="px-6 py-3 text-center">Durasi Live</th>
                        <th class="px-6 py-3">Catatan</th>
                        <th class="px-6 py-3 text-center">Bukti Stats</th>
                        <th class="px-6 py-3 text-center">Bukti Live</th>
                        <th class="px-6 py-3 text-amber-700 dark:text-amber-400">Feedback Atasan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($groupedItems as $date => $dateItems)
                        @foreach($dateItems as $i => $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            @if($loop->first)
                            <td class="table-cell text-gray-700 dark:text-gray-300 font-medium text-center border-r border-gray-200 dark:border-gray-600" rowspan="{{ $dateItems->count() }}">{{ $item->tanggal->format('d/m/Y') }}</td>
                            @endif
                            <td class="table-cell font-mono text-xs text-gray-600 dark:text-gray-400">{{ $item->nik }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $item->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->sesi ?? '-' }}</td>
                            <td class="table-cell text-right font-mono text-sm text-gray-700 dark:text-gray-300">{{ number_format($item->ach_sold, 0) }}</td>
                            <td class="table-cell text-right font-mono text-sm text-gray-700 dark:text-gray-300">{{ number_format($item->ach_view, 0) }}</td>
                            <td class="table-cell text-right font-mono text-sm text-gray-700 dark:text-gray-300">{{ number_format($item->peak_view, 0) }}</td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ $item->durasi ? number_format($item->durasi, 0) . ' Jam' : '-' }}</td>
                            <td class="table-cell text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $item->catatan ?? '-' }}</td>
                            <td class="table-cell text-center">
                                @if($item->foto_bukti_stats)
                                    <button @click="fotoModalUrl = '/storage/{{ $item->foto_bukti_stats }}'; fotoModalLabel = 'Bukti Stats'; showFotoModal = true" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors" title="Bukti Stats">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        Stats
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                @if($item->foto_bukti_live)
                                    <button @click="fotoModalUrl = '/storage/{{ $item->foto_bukti_live }}'; fotoModalLabel = 'Bukti Live'; showFotoModal = true" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors" title="Bukti Live">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        Live
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-sm text-amber-700 dark:text-amber-400 max-w-xs truncate">
                                @if($item->feedback_atasan)
                                    {{ $item->feedback_atasan }}
                                @else
                                    <span class="text-gray-400 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                @php
                                    $statusBadge = match($item->status) {
                                        'pending' => 'badge-warning',
                                        'disetujui' => 'badge-success',
                                        'ditolak' => 'badge-danger',
                                        default => 'badge-info',
                                    };
                                    $statusLabel = match($item->status) {
                                        'pending' => 'Menunggu',
                                        'disetujui' => 'Disetujui',
                                        'ditolak' => 'Ditolak',
                                        default => $item->status,
                                    };
                                @endphp
                                <span class="{{ $statusBadge }}">{{ $statusLabel }}</span>
                            </td>
                            <td class="table-cell text-center">
                                @php
                                    $isKoord = auth()->user()->isKoordinatorGame();
                                    $isStaff = auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffHostMlbb() || auth()->user()->isStaffHostEfootball() || auth()->user()->isStaffHostValorant() || auth()->user()->isStaffHostRoblox() || auth()->user()->isStaffHostMonkeyPubg();
                                    $canEdit = $isKoord || ($isStaff && $item->status === 'pending');
                                @endphp
                                @if($isKoord && $item->status === 'pending')
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="setujui({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            Setujui
                                        </button>
                                        <button wire:click="tolak({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </button>
                                    </div>
                                @elseif($canEdit)
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="openEditModal({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="14" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                                    <p class="font-medium">Belum ada data</p>
                                    <p class="text-xs mt-1">Klik "Tambah Data" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 dark:border-gray-800">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Create Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
        <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Tambah Data {{ $divisi }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan *</label>
                        <select wire:model.live="nik" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->nik }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                            @endforeach
                        </select>
                        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nama *</label>
                        <input type="text" wire:model="nama" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100" readonly>
                        @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sesi *</label>
                        <select wire:model="sesi" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Sesi --</option>
                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>
                            <option value="Subuh">Subuh</option>
                        </select>
                        @error('sesi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal *</label>
                    <input type="date" wire:model="tanggal" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                    @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sold *</label>
                        <input type="number" step="0.01" min="0" wire:model.live="ach_sold" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('ach_sold') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">View *</label>
                        <input type="number" step="0.01" min="0" wire:model="ach_view" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('ach_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Peak View *</label>
                        <input type="number" step="0.01" min="0" wire:model="peak_view" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('peak_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi *</label>
                        <input type="number" wire:model="durasi" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('durasi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea wire:model="catatan" rows="2" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div wire:key="create-foto-bukti-stats">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Bukti Stats</label>
                        <input wire:model="foto_bukti_stats" type="file" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_stats" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_stats') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div wire:key="create-foto-bukti-live">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Bukti Live</label>
                        <input wire:model="foto_bukti_live" type="file" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_live" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_live') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn-primary text-xs py-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
        <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Edit Data {{ $divisi }}</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="update" class="p-6 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan *</label>
                        <select wire:model.live="nik" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->nik }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                            @endforeach
                        </select>
                        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nama *</label>
                        <input type="text" wire:model="nama" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100" readonly>
                        @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sesi *</label>
                        <select wire:model="sesi" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Sesi --</option>
                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>
                            <option value="Subuh">Subuh</option>
                        </select>
                        @error('sesi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal *</label>
                    <input type="date" wire:model="tanggal" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                    @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sold *</label>
                        <input type="number" step="0.01" min="0" wire:model.live="ach_sold" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('ach_sold') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">View *</label>
                        <input type="number" step="0.01" min="0" wire:model="ach_view" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('ach_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Peak View *</label>
                        <input type="number" step="0.01" min="0" wire:model="peak_view" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('peak_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi *</label>
                        <input type="number" wire:model="durasi" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none">
                        @error('durasi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea wire:model="catatan" rows="2" class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Foto Saat Ini</label>
                    <div class="flex items-center gap-3">
                        @if($fotoBuktiStatsPath)
                            <button @click="fotoModalUrl = '/storage/{{ $fotoBuktiStatsPath }}'; fotoModalLabel = 'Bukti Stats'; showFotoModal = true" class="text-xs text-primary-600 hover:underline cursor-pointer bg-transparent border-none p-0">Lihat Bukti Stats</button>
                        @endif
                        @if($fotoBuktiLivePath)
                            <button @click="fotoModalUrl = '/storage/{{ $fotoBuktiLivePath }}'; fotoModalLabel = 'Bukti Live'; showFotoModal = true" class="text-xs text-emerald-600 hover:underline cursor-pointer bg-transparent border-none p-0">Lihat Bukti Live</button>
                        @endif
                        @if(!$fotoBuktiStatsPath && !$fotoBuktiLivePath)
                            <span class="text-xs text-gray-400">Tidak ada foto</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div wire:key="edit-foto-bukti-stats">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ganti Bukti Stats</label>
                        <input wire:model="foto_bukti_stats" type="file" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_stats" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_stats') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div wire:key="edit-foto-bukti-live">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ganti Bukti Live</label>
                        <input wire:model="foto_bukti_live" type="file" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_live" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_live') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn-primary text-xs py-2">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 p-6 text-center">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-2xl bg-red-50 dark:bg-red-900/20">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">Hapus Data</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus data ini?</p>
            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Batal</button>
                <button wire:click="executeDelete" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">Hapus</button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL FOTO --}}
    <div x-show="showFotoModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/60 backdrop-blur-sm"
         @click="showFotoModal = false">
        <div x-show="showFotoModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Foto</h3>
                <button @click="showFotoModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3" x-text="fotoModalLabel"></p>
                <img :src="fotoModalUrl" :alt="fotoModalLabel" class="max-w-full max-h-[70vh] w-auto h-auto object-contain mx-auto rounded-xl border border-gray-200 dark:border-gray-600">
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="showFotoModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
