@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kontrak Kerja</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola kontrak kerja karyawan</p>
    </div>
@endpush

<div>
    {{-- Alert akan berakhir --}}
    @if($akanBerakhir > 0)
        @php $isUrgentAlert = $urgent > 0; @endphp
        <div class="mb-5 rounded-xl border px-5 py-3.5 flex items-start gap-3 {{ $isUrgentAlert ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }}">
            <svg class="w-5 h-5 mt-0.5 shrink-0 {{ $isUrgentAlert ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <div class="flex-1">
                <p class="text-sm font-semibold {{ $isUrgentAlert ? 'text-red-800' : 'text-amber-800' }}">Ada <span class="underline">{{ $akanBerakhir }}</span> kontrak yang akan berakhir dalam 14 hari</p>
                <p class="text-xs {{ $isUrgentAlert ? 'text-red-700' : 'text-amber-700' }} mt-0.5">Segera perbarui kontrak melalui menu Riwayat Kontrak di halaman detail karyawan.</p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAktif }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Aktif</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Akan Berakhir</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $akanBerakhir }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dalam 14 Hari</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <span class="badge-info">Selesai</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSelesai }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Selesai</p>
        </div>
    </div>

    {{-- Card wrapper for table --}}
    <div class="card">
        {{-- Filter bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama atau NIK..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                    >
                </div>

                <a href="{{ route('hris.export.kontrak-kerja') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Export Excel
                </a>
            </div>


        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">Nama Karyawan</th>
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Jabatan</th>
                        <th class="px-6 py-3">Divisi</th>
                        <th class="px-6 py-3">Sisa Hari</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($contracts as $ct)
                        @php
                            $sisaHari = now()->startOfDay()->diffInDays($ct->tanggal_berakhir, false);
                            $isAkanBerakhir = $sisaHari <= 14 && $sisaHari >= 0;
                            $isUrgent = $sisaHari < 3 && $sisaHari >= 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $contracts->firstItem() + $loop->index }}</td>
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    @if($ct->employee->foto_url)
                                        <img src="{{ $ct->employee->foto_url }}" alt="{{ $ct->employee->nama }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50">
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                            {{ strtoupper(substr($ct->employee->nama, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ct->employee->nama }}</span>
                                </div>
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $ct->employee->nik }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $ct->employee->position ?? '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $ct->employee->divisionNames() ?: '-' }}</td>
                            <td class="table-cell font-mono {{ $isUrgent ? 'text-red-600 font-semibold' : ($isAkanBerakhir ? 'text-amber-600 font-semibold' : 'text-gray-600 dark:text-gray-400') }}">
                                @if($sisaHari < 0)
                                    <span class="text-red-500">-</span>
                                @elseif($sisaHari === 0)
                                    <span class="text-red-500">Hari ini</span>
                                @else
                                    {{ $sisaHari }} hari
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($sisaHari < 0)
                                    <span class="badge-danger">Kedaluwarsa</span>
                                @elseif($isUrgent)
                                    <span class="badge-danger">Segera Berakhir</span>
                                @elseif($isAkanBerakhir)
                                    <span class="badge-warning">Akan Berakhir</span>
                                @else
                                    <span class="badge-success">Aktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if($ct->can_evaluate && !$ct->already_evaluated)
                                        <button wire:click="openEvaluasi({{ $ct->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                            Evaluasi
                                        </button>
                                    @endif
                                    @if($canViewDetail && $ct->evaluations_count > 0)
                                        <button wire:click="openDetail({{ $ct->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Lihat Detail
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
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tidak ada kontrak aktif</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada kontrak kerja yang sedang berlaku</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

    {{-- Flash sukses --}}
    @if(session('eval_success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
             x-transition.opacity.duration.300ms
             class="fixed bottom-6 right-6 z-[300] flex items-center gap-3 rounded-xl bg-emerald-600 text-white pl-4 pr-5 py-3 shadow-xl shadow-emerald-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            <p class="text-sm font-semibold">{{ session('eval_success') }}</p>
        </div>
    @endif

    {{-- Flash error --}}
    @if(session('eval_error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
             x-transition.opacity.duration.300ms
             class="fixed bottom-6 right-6 z-[300] flex items-center gap-3 rounded-xl bg-red-600 text-white pl-4 pr-5 py-3 shadow-xl shadow-red-200">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <p class="text-sm font-semibold">{{ session('eval_error') }}</p>
        </div>
    @endif

    {{-- Modal Evaluasi (input) --}}
    @if($evaluasiModal)
    <div x-data x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         wire:click.self="$set('evaluasiModal', false)">
        <div x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-green-600 px-7 pt-5 pb-8 shrink-0 relative">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_0%,rgba(255,255,255,0.18),transparent_55%)] pointer-events-none"></div>
                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 shrink-0 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Evaluasi Kontrak</h3>
                            <p class="text-xs text-white/80 mt-1 truncate">{{ $evalContractInfo['nama'] ?? '-' }} · NIK {{ $evalContractInfo['nik'] ?? '-' }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('evaluasiModal', false)" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/30 text-white hover:bg-white/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-7 pt-10 pb-6 -mt-4 space-y-5">
                {{-- Ringkasan kontrak --}}
                <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Posisi</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ $evalContractInfo['posisi'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Divisi</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ $evalContractInfo['divisi'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Mulai</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ $evalContractInfo['mulai'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">Berakhir</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-0.5">{{ $evalContractInfo['berakhir'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Kriteria penilaian --}}
                @php
                    $criteria = [
                        'evalKinerja' => ['Kinerja', 'Hasil kerja dan pencapaian target'],
                        'evalDisiplin' => ['Disiplin', 'Kepatuhan terhadap jam kerja dan peraturan'],
                        'evalKerjasama' => ['Kerjasama', 'Kolaborasi dengan tim dan rekan kerja'],
                        'evalKepatuhan' => ['Kepatuhan', 'Kepatuhan terhadap SOP perusahaan'],
                        'evalKeterampilan' => ['Keterampilan', 'Kemampuan teknis dan penguasaan tugas'],
                    ];
                @endphp
                @foreach($criteria as $key => [$label, $desc])
                    <div class="space-y-1.5">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $label }}</p>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">{{ $desc }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="$set('{{ $key }}', {{ $i }})"
                                        class="p-0.5 transition-transform hover:scale-110" title="{{ $i }} dari 5">
                                    <svg class="w-7 h-7 {{ $this->{$key} !== null && $this->{$key} >= $i ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                </button>
                            @endfor
                        </div>
                    </div>
                @endforeach

                {{-- Rekomendasi --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Rekomendasi Kelanjutan <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2 rounded-xl bg-gray-100 dark:bg-gray-800 p-1.5">
                        <button type="button" wire:click="$set('evalRekomendasi', 'perpanjang')"
                                class="rounded-lg py-2 text-sm font-semibold transition-all {{ $evalRekomendasi === 'perpanjang' ? 'bg-white dark:bg-gray-900 text-emerald-600 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            Perpanjang
                        </button>
                        <button type="button" wire:click="$set('evalRekomendasi', 'pertimbangkan')"
                                class="rounded-lg py-2 text-sm font-semibold transition-all {{ $evalRekomendasi === 'pertimbangkan' ? 'bg-white dark:bg-gray-900 text-amber-600 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            Pertimbangkan
                        </button>
                        <button type="button" wire:click="$set('evalRekomendasi', 'tidak_perpanjang')"
                                class="rounded-lg py-2 text-sm font-semibold transition-all {{ $evalRekomendasi === 'tidak_perpanjang' ? 'bg-white dark:bg-gray-900 text-red-600 shadow-sm ring-1 ring-gray-200 dark:ring-gray-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            Tidak Perpanjang
                        </button>
                    </div>
                    @error('evalRekomendasi') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Catatan --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Catatan Evaluasi</label>
                    <textarea wire:model="evalCatatan" rows="4"
                              placeholder="Tulis catatan penilaian, kelebihan, kekurangan, dan pertimbangan kelanjutan kontrak..."
                              class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 outline-none hover:border-gray-300 dark:hover:border-gray-500 focus:border-emerald-500 focus:shadow-[0_0_0_3px_rgba(16,185,129,0.25)] transition-all"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 px-7 py-4 border-t border-gray-100 dark:border-gray-700 shrink-0">
                <button type="button" wire:click="$set('evaluasiModal', false)"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Batal
                </button>
                <button type="button" wire:click="saveEvaluasi"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    Simpan Evaluasi
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Detail Evaluasi --}}
    @if($evaluasiDetailModal && count($selectedEvaluations) > 0)
    @php $evalSummary = $selectedEvaluations[0]; @endphp
    <div x-data="{ active: null }" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm"
         wire:click.self="$set('evaluasiDetailModal', false)">
        <div x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="w-full max-w-3xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 via-emerald-500 to-green-600 px-7 pt-5 pb-8 shrink-0 relative">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_0%,rgba(255,255,255,0.18),transparent_55%)] pointer-events-none"></div>
                <div class="flex items-start justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 shrink-0 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Detail Evaluasi Kontrak</h3>
                            <p class="text-xs text-white/80 mt-1 truncate">{{ $evalSummary['employee'] }} · NIK {{ $evalSummary['nik'] }}</p>
                        </div>
                    </div>
                    <button wire:click="$set('evaluasiDetailModal', false)" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15 ring-1 ring-white/30 text-white hover:bg-white/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-7 pt-10 pb-6 -mt-4">

                {{-- STEP 1: Daftar atasan yang memberikan evaluasi --}}
                <div x-show="active === null" x-transition.opacity.duration.200ms>
                    {{-- Info kontrak --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-px rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700">
                        <div class="bg-white dark:bg-gray-900 px-5 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Posisi</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $evalSummary['posisi'] }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-5 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Divisi</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $evalSummary['divisi'] }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-5 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Periode</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $evalSummary['mulai'] }} — {{ $evalSummary['berakhir'] }}</p>
                        </div>
                    </div>

                    {{-- Judul daftar penilai --}}
                    <div class="mt-6 mb-3 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Atasan yang memberikan evaluasi</span>
                        <span class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/60 px-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300">{{ count($selectedEvaluations) }}</span>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($selectedEvaluations as $si => $eval)
                        <button type="button" @click="active = {{ $si }}"
                                class="group w-full flex items-center justify-between gap-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3.5 text-left transition-all hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/40 hover:shadow-sm">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-bold uppercase text-sm">{{ strtoupper(substr($eval['evaluator'] ?? '?', 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ $eval['evaluator'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $eval['evaluator_role'] ?: 'Penilai' }} <span class="text-gray-300 dark:text-gray-600">•</span> {{ $eval['created_at'] }}</p>
                                </div>
                            </div>
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 transition-all group-hover:bg-emerald-600 group-hover:text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- STEP 2: Detail isi evaluasi yang diklik --}}
                <div x-show="active !== null" x-transition.opacity.duration.200ms>
                    @foreach($selectedEvaluations as $si => $eval)
                        @php
                            $avg = round(($eval['kinerja'] + $eval['disiplin'] + $eval['kerjasama'] + $eval['kepatuhan'] + $eval['keterampilan']) / 5, 1);
                            $rekomendasi = $eval['rekomendasi'] ?? '';
                            $detailCriteria = [
                                ['label' => 'Kinerja', 'value' => $eval['kinerja'] ?? 0],
                                ['label' => 'Disiplin', 'value' => $eval['disiplin'] ?? 0],
                                ['label' => 'Kerjasama', 'value' => $eval['kerjasama'] ?? 0],
                                ['label' => 'Kepatuhan', 'value' => $eval['kepatuhan'] ?? 0],
                                ['label' => 'Keterampilan', 'value' => $eval['keterampilan'] ?? 0],
                            ];
                        @endphp
                        <div x-show="active === {{ $si }}" class="space-y-5">
                            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/40">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-bold uppercase text-sm">{{ strtoupper(substr($eval['evaluator'] ?? '?', 0, 1)) }}</span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ $eval['evaluator'] }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $eval['evaluator_role'] ?: 'Penilai' }} <span class="text-gray-300 dark:text-gray-600">•</span> {{ $eval['created_at'] }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 shrink-0">
                                        <div class="text-right">
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Rata-rata</p>
                                            <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $avg }}<span class="text-xs font-semibold text-gray-400">/5</span></p>
                                        </div>
                                        @if($eval['can_edit'])
                                            <button type="button" wire:click="openEvaluasi({{ $eval['contract_id'] }})"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                Edit
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-5 space-y-5">
                                    {{-- Rekomendasi badge --}}
                                    <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3.5 {{ $rekomendasi === 'perpanjang' ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950' : ($rekomendasi === 'pertimbangkan' ? 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950') }}">
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Rekomendasi Kelanjutan</p>
                                            <p class="text-sm font-bold {{ $rekomendasi === 'perpanjang' ? 'text-emerald-700 dark:text-emerald-300' : ($rekomendasi === 'pertimbangkan' ? 'text-amber-700 dark:text-amber-300' : 'text-red-700 dark:text-red-300') }} mt-1">
                                                {{ $rekomendasi === 'perpanjang' ? 'Perpanjang Kontrak' : ($rekomendasi === 'pertimbangkan' ? 'Perlu Pertimbangan' : 'Tidak Diperpanjang') }}
                                            </p>
                                        </div>
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-lg {{ $rekomendasi === 'perpanjang' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400' : ($rekomendasi === 'pertimbangkan' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400') }}">{{ $rekomendasi === 'perpanjang' ? '✓' : ($rekomendasi === 'pertimbangkan' ? '?' : '✕') }}</span>
                                    </div>

                                    {{-- Kriteria --}}
                                    <div>
                                        <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-3">Komponen Penilaian</p>
                                        <div class="space-y-3">
                                            @foreach($detailCriteria as $c)
                                                <div class="grid grid-cols-[120px_1fr_48px] items-center gap-4">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $c['label'] }}</p>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <svg class="w-5 h-5 {{ $c['value'] >= $i ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700' }}" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                                        @endfor
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-800 dark:text-gray-200 text-right">{{ $c['value'] }}/5</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Catatan --}}
                                    @if($eval['catatan'])
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2">Catatan Evaluasi</p>
                                            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 p-4">
                                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $eval['catatan'] }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-between gap-2.5 px-7 py-4 border-t border-gray-100 dark:border-gray-700 shrink-0">
                <button type="button" @click="active = null" x-show="active !== null" x-transition
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                    Kembali
                </button>
                <button type="button" wire:click="$set('evaluasiDetailModal', false)"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
