@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Cuti & Izin</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola pengajuan cuti dan izin karyawan</p>
    </div>
@endpush

<div x-data="{ confirmAction: false, confirmTitle: '', confirmMessage: '', confirmHandler: null }">

    @if(auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorCreative() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isKoordinatorMlbb() || auth()->user()->isKoordinatorEfootball() || auth()->user()->isKoordinatorValorant() || auth()->user()->isKoordinatorRoblox() || auth()->user()->isKoordinatorMonkeyPubg() || auth()->user()->isHeadOfStore())
    {{-- Tab Navigation --}}
    <div class="mb-6">
        <div class="inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            <button wire:click="$set('tab', 'saya')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'saya' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                Pengajuan Saya
            </button>
            <button wire:click="$set('tab', 'tim')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'tim' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                Pengajuan Tim
            </button>
        </div>
    </div>
    @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalPengajuan }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pengajuan</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Tahunan</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $sisaCuti }}</span>
                    <span class="text-sm font-medium text-gray-400">/ {{ $jatahCuti }} hari</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sisa Cuti Tahunan</p>
                <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-green-500 transition-all duration-500" style="width: {{ $jatahCuti > 0 ? ($sisaCuti / $jatahCuti) * 100 : 0 }}%"></div>
                </div>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-warning">Izin</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalIzin }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Izin</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning">Menunggu</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $menunggu }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menunggu Persetujuan</p>
            </div>
        </div>

        <div class="card">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Pengajuan Cuti & Izin</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar pengajuan cuti dan izin karyawan</p>
                </div>
                @php $myEmployee = auth()->user()->employee; @endphp
                @if($myEmployee)
                <button wire:click="openPengajuanModal" class="btn-primary text-xs py-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Ajukan Cuti / Izin
                </button>
                @endif
            </div>
            {{-- Filter bar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <div class="flex items-center gap-3 flex-1 flex-wrap">
                    <div class="relative flex-1 min-w-[200px] max-w-xs">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama atau NIK..."
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                        >
                    </div>

                    <select wire:model.live="filterJenis" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 pr-8 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">Semua Jenis</option>
                            <option value="cuti_tahunan">Cuti Tahunan</option>
                            <option value="izin">Izin</option>
                        </select>

                    <select wire:model.live="filterStatus" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 pr-8 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 w-12 text-center">No</th>
                            <th class="px-6 py-3">Nama Karyawan</th>
                            <th class="px-6 py-3">Jabatan</th>
                            <th class="px-6 py-3">Jenis</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Durasi</th>
                            <th class="px-6 py-3">Atasan 1</th>
                            <th class="px-6 py-3">Atasan 2</th>
                            <th class="px-6 py-3">Persetujuan Atasan 1</th>
                            <th class="px-6 py-3">Persetujuan Atasan 2</th>
                            <th class="px-6 py-3">Persetujuan HR</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($leaveRequests as $lr)
                            @php
                                $isAtasan = $userEmployee && $userEmployee->id === $lr->atasan_id;
                                $isAtasan2 = $userEmployee && $userEmployee->id === $lr->atasan2_id;
                                $canApproveKoor = $isAtasan;
                                $canApproveAtasan2 = $isAtasan2;
                                $canApproveHr = $lihatSemua && !$user->isKoordinatorIt() && !$user->isKoordinatorAdmin() && !$user->isKoordinatorPubg() && !$user->isKoordinatorFf() && !$user->isKoordinatorMlbb() && !$user->isKoordinatorEfootball() && !$user->isKoordinatorValorant() && $lr->tanggal_selesai->isPast();
                                $requiresPin = $user->requiresPinApproval();
                                $isOwnRequest = $userEmployee && $userEmployee->id === $lr->employee_id;
                                $isPending = $lr->persetujuan_koor === 'menunggu' || ($lr->atasan2_id && $lr->persetujuan_atasan2 === 'menunggu') || $lr->persetujuan_hr === 'menunggu';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $leaveRequests->firstItem() + $loop->index }}</td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-2">
                                        @if($lr->employee?->foto_url)
                                            <img src="{{ $lr->employee->foto_url }}" alt="{{ $lr->employee->nama }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                                {{ strtoupper(substr($lr->employee->nama ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $lr->employee->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $lr->selectedPosition?->nama ?? $lr->employee?->position ?? '-' }}</td>
                                <td class="table-cell">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $lr->jenis === 'cuti_tahunan' ? 'Cuti Tahunan' : 'Izin' }}</span>
                                </td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($lr->tanggal_mulai)->format('d/m') }} - {{ \Carbon\Carbon::parse($lr->tanggal_selesai)->format('d/m/Y') }}
                                </td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $lr->durasi }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $lr->atasan?->nama ?? '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $lr->atasan2?->nama ?? '-' }}</td>
                                <td class="table-cell">
                                    @if($lr->persetujuan_koor === 'disetujui')
                                        <span class="badge-success">Disetujui</span>
                                    @elseif($lr->persetujuan_koor === 'ditolak')
                                        <span class="badge-danger">Ditolak</span>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span class="badge-warning">Menunggu</span>
                                            @if($canApproveKoor)
                                                @if($requiresPin)
                                                <button wire:click="setujui({{ $lr->id }}, 'persetujuan_koor')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button wire:click="tolak({{ $lr->id }}, 'persetujuan_koor')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @else
                                                <button @click="confirmAction = true; confirmTitle = 'Setujui Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?'; confirmHandler = () => $wire.setujui({{ $lr->id }}, 'persetujuan_koor')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button @click="confirmAction = true; confirmTitle = 'Tolak Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?'; confirmHandler = () => $wire.tolak({{ $lr->id }}, 'persetujuan_koor')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if(!$lr->atasan2_id)
                                        <span class="text-xs text-gray-400">-</span>
                                    @elseif($lr->persetujuan_atasan2 === 'disetujui')
                                        <span class="badge-success">Disetujui</span>
                                    @elseif($lr->persetujuan_atasan2 === 'ditolak')
                                        <span class="badge-danger">Ditolak</span>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span class="badge-warning">Menunggu</span>
                                            @if($canApproveAtasan2)
                                                @if($requiresPin)
                                                <button wire:click="setujui({{ $lr->id }}, 'persetujuan_atasan2')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button wire:click="tolak({{ $lr->id }}, 'persetujuan_atasan2')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @else
                                                <button @click="confirmAction = true; confirmTitle = 'Setujui Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?'; confirmHandler = () => $wire.setujui({{ $lr->id }}, 'persetujuan_atasan2')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button @click="confirmAction = true; confirmTitle = 'Tolak Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?'; confirmHandler = () => $wire.tolak({{ $lr->id }}, 'persetujuan_atasan2')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @if($lr->persetujuan_hr === 'disetujui')
                                        <span class="badge-success">Disetujui</span>
                                    @elseif($lr->persetujuan_hr === 'ditolak')
                                        <span class="badge-danger">Ditolak</span>
                                    @else
                                        <div class="flex items-center gap-1">
                                            <span class="badge-warning">Menunggu</span>
                                            @if($canApproveHr)
                                                @if($requiresPin)
                                                <button wire:click="setujui({{ $lr->id }}, 'persetujuan_hr')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button wire:click="tolak({{ $lr->id }}, 'persetujuan_hr')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @else
                                                <button @click="confirmAction = true; confirmTitle = 'Setujui Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menyetujui pengajuan ini?'; confirmHandler = () => $wire.setujui({{ $lr->id }}, 'persetujuan_hr')" class="p-1 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-md transition-colors" title="Setujui">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                                <button @click="confirmAction = true; confirmTitle = 'Tolak Pengajuan'; confirmMessage = 'Apakah Anda yakin ingin menolak pengajuan ini?'; confirmHandler = () => $wire.tolak({{ $lr->id }}, 'persetujuan_hr')" class="p-1 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" title="Tolak">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="table-cell">
                                    @can('delete-data')
                                    <button wire:click="confirmDelete({{ $lr->id }})"
                                            class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    @elseif($isOwnRequest && $isPending)
                                    <button wire:click="confirmDelete({{ $lr->id }})"
                                            class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada pengajuan</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada data pengajuan cuti atau izin</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leaveRequests->hasPages())
                <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>

    <template x-teleport="body">
    {{-- PENGAJUAN MODAL --}}
    <div x-data="{ open: $wire.entangle('showPengajuanModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajukan Cuti / Izin</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Isi form pengajuan di bawah ini</p>
                </div>
                <button wire:click="closePengajuanModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="submitPengajuan" class="space-y-4">
                @if(isset($userPositions) && $userPositions->count() > 1)
                <div>
                    <x-input-label value="Ajukan sebagai *" />
                    <select wire:model.live="selectedPositionId" required
                            class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">Pilih jabatan</option>
                        @foreach($userPositions as $up)
                            <option value="{{ $up->id }}">{{ $up->nama }}</option>
                        @endforeach
                    </select>
                    @if($selectedPositionId)
                        <div class="mt-2 text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                            <span class="block">Atasan 1: <strong class="text-gray-700 dark:text-gray-300">{{ $this->getSelectedPositionAtasan() ?: '-' }}</strong></span>
                            <span class="block">Atasan 2: <strong class="text-gray-700 dark:text-gray-300">{{ $this->getSelectedPositionAtasan2() ?: '-' }}</strong></span>
                        </div>
                    @endif
                    @error('selectedPositionId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div>
                    <x-input-label value="Jenis *" />
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        @if(isset($sisaCuti) && $sisaCuti > 0)
                        <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center text-sm font-medium transition-all"
                               :class="'cuti_tahunan' === $wire.pengajuanJenis ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'">
                            <input type="radio" wire:model="pengajuanJenis" value="cuti_tahunan" class="sr-only">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12"/></svg>
                            <span>Cuti Tahunan</span>
                        </label>
                        @else
                        <div class="relative flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 p-4 text-center text-sm font-medium text-gray-400 dark:text-gray-500 opacity-60 cursor-not-allowed">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12"/></svg>
                            <span>Cuti Tahunan</span>
                            <span class="text-[10px] text-red-500 font-semibold">Jatah cuti sudah habis</span>
                        </div>
                        @endif
                        <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center text-sm font-medium transition-all"
                               :class="'izin' === $wire.pengajuanJenis ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'">
                            <input type="radio" wire:model="pengajuanJenis" value="izin" class="sr-only">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Izin</span>
                        </label>
                    </div>
                    @error('pengajuanJenis') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="tgl-mulai" value="Tanggal Mulai *" />
                        <x-text-input id="tgl-mulai" wire:model="pengajuanTanggalMulai" type="date" class="mt-1 block w-full" />
                        @error('pengajuanTanggalMulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="tgl-selesai" value="Tanggal Selesai *" />
                        <x-text-input id="tgl-selesai" wire:model="pengajuanTanggalSelesai" type="date" class="mt-1 block w-full" />
                        @error('pengajuanTanggalSelesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="keterangan" value="Keterangan *" />
                    <textarea id="keterangan" wire:model="pengajuanKeterangan" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Alasan pengajuan..."></textarea>
                    @error('pengajuanKeterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closePengajuanModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
    </div>
</div>
</template>

    <template x-teleport="body">
    {{-- Confirmation Modal --}}
    <div x-show="confirmAction" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="confirmAction = false">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2" x-text="confirmTitle"></h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6" x-text="confirmMessage"></p>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="confirmAction = false" class="btn-secondary text-xs px-6">Batal</button>
                <button @click="confirmHandler(); confirmAction = false" class="btn-primary text-xs px-6">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- PIN Persetujuan Modal --}}
    <div x-data="{ open: $wire.entangle('showPinModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelPinModal()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Konfirmasi PIN Persetujuan</h3>
                <button @click="open = false; $wire.cancelPinModal()" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Masukkan PIN Persetujuan Anda untuk melanjutkan</p>

            @error('pin')
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                </div>
            @enderror

            <div class="space-y-4">
                <div>
                    <x-input-label for="pin-input" value="PIN Persetujuan" />
                    <x-text-input id="pin-input" wire:model="pin" type="password" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="mt-1 block w-full text-center text-lg tracking-widest" placeholder="******" autocomplete="off" />
                </div>

                <div>
                    <x-input-label for="catatan-input" value="Catatan (opsional)" />
                    <textarea id="catatan-input" wire:model="catatan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Tambahkan catatan..."></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.cancelPinModal()" class="btn-secondary text-xs px-6">Batal</button>
                <button wire:click="submitPinApproval" class="btn-primary text-xs px-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- No PIN Modal --}}
    <div x-data="{ open: $wire.entangle('showNoPinModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">PIN Persetujuan Belum Dibuat</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Anda harus membuat PIN Persetujuan terlebih dahulu sebelum dapat menyetujui atau menolak pengajuan.</p>
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false" class="btn-secondary text-xs px-6">Nanti Saja</button>
                <a href="{{ route('profile.edit') }}" class="btn-primary text-xs px-6 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    Buat PIN Persetujuan
                </a>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- Atasan 2 Error Modal --}}
    <div x-data="{ open: $wire.entangle('showAtasan2ErrorModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Persetujuan Tertunda</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="$wire.atasan2ErrorMessage"></p>
            <div class="flex items-center justify-center pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false" class="btn-primary text-xs px-8">Tutup</button>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- Rekomendasi Freelance Modal --}}
    <div x-data="{ open: $wire.entangle('showRekomendasiFreelanceModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.closeRekomendasiFreelanceModal()">
        <div @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center gap-3 mb-5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rekomendasi Freelance</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tersedia untuk posisi <strong class="font-semibold text-gray-700 dark:text-gray-300">{{ $rekomendasiPositionName }}</strong></p>
                </div>
                <button @click="open = false; $wire.closeRekomendasiFreelanceModal()" class="ml-auto rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2">
                @forelse($rekomendasiFreelancers as $f)
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-4 py-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 font-semibold text-xs">
                            {{ strtoupper(substr($f->nama, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $f->nama }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $f->host_position }}</p>
                        </div>
                        @if($f->no_whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $f->no_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors shrink-0">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/></svg>
                            Hubungi
                        </a>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Tidak ada freelance tersedia</p>
                @endforelse
            </div>

            <div class="flex items-center justify-end pt-5 mt-5 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.closeRekomendasiFreelanceModal()" class="btn-primary text-xs px-8">Tutup</button>
            </div>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- Delete Confirm Modal --}}
    <div x-data="{ open: $wire.entangle('showDeleteConfirmModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelDelete()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Hapus Pengajuan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.cancelDelete()" class="btn-secondary text-xs px-6">Batal</button>
                <button wire:click="executeDelete" class="btn-danger text-xs px-6 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
    </template>


</div>
