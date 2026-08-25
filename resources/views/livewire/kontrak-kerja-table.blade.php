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
                                    @if($ct->can_evaluate && !$ct->already_evaluated && $isAkanBerakhir)
                                        <a href="{{ route('hris.kontrak-kerja.evaluasi', $ct->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                            Evaluasi
                                        </a>
                                    @elseif($ct->can_evaluate && !$ct->already_evaluated)
                                        <span title="Evaluasi dapat diisi saat kontrak tersisa 14 hari atau kurang"
                                              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 cursor-not-allowed select-none">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Evaluasi · {{ $sisaHari }} hari lagi
                                        </span>
                                    @endif
                                    @if($ct->can_approve)
                                        <button wire:click="openPenilaian({{ $ct->id }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-violet-700 bg-violet-50 hover:bg-violet-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                            {{ $ct->already_approved ? 'Edit Approval' : 'Approval' }}
                                        </button>
                                    @endif
                                    @if($canViewDetail && !$ct->can_approve && ($ct->evaluations_count > 0 || $ct->approvals_count > 0))
                                        <button wire:click="openPenilaian({{ $ct->id }})"
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

    {{-- Modal sukses evaluasi --}}
    @if(session('eval_success'))
        <style>
            .eval-success-pop {
                animation: eval-success-pop .5s cubic-bezier(.34, 1.56, .64, 1) both;
            }
            @keyframes eval-success-pop {
                0% { transform: scale(0) rotate(-12deg); }
                60% { transform: scale(1.15) rotate(3deg); }
                100% { transform: scale(1) rotate(0deg); }
            }
        </style>
        <div x-data="{ show: false }" x-cloak x-show="show"
             x-init="setTimeout(() => show = true, 80)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             @click.self="show = false"
             class="fixed inset-0 z-[300] flex items-center justify-center p-5 bg-gray-900/50 backdrop-blur-sm">
            <div
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-6"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="w-full max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
                <div class="px-8 pt-8 pb-2 flex flex-col items-center text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-950 mb-4 eval-success-pop">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Berhasil!</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">{{ session('eval_success') }}</p>
                </div>
                <div class="px-8 py-6">
                    <button type="button" @click="show = false"
                            class="w-full py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-200 active:scale-[0.98] rounded-xl transition-all shadow-sm">
                        Selesai
                    </button>
                </div>
            </div>
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

    {{-- Modal Penilaian (Evaluasi + Approval terpadu) --}}
    @if($evaluasiDetailModal)
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
                            <h3 class="text-lg font-bold text-white">Hasil Evaluasi &amp; Approval Kontrak</h3>
                            <p class="text-xs text-white/80 mt-1 truncate">{{ $penilaianInfo['nama'] ?? '-' }} · NIK {{ $penilaianInfo['nik'] ?? '-' }}</p>
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
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $penilaianInfo['posisi'] ?? '-' }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-5 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Divisi</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $penilaianInfo['divisi'] ?? '-' }}</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 px-5 py-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Periode</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mt-1.5 leading-snug">{{ $penilaianInfo['mulai'] ?? '-' }} — {{ $penilaianInfo['berakhir'] ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Form approval terpadu --}}
                    @if($approveContractId && $hasSubmittedEval)
                    <div class="mt-6 rounded-2xl border border-violet-200 dark:border-violet-800 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-4 bg-violet-50 dark:bg-violet-950/60 border-b border-violet-100 dark:border-violet-800">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 dark:bg-violet-900 text-violet-600 dark:text-violet-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Keputusan Approval Anda</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Pilih keputusan &amp; tambahkan catatan bila perlu.</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button type="button" wire:click="$set('approveDecision', 'disetujui')"
                                        class="flex items-center gap-3 rounded-xl border px-4 py-3 text-left transition-all {{ $approveDecision === 'disetujui' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950 ring-1 ring-emerald-500' : 'border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-700' }}">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $approveDecision === 'disetujui' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </span>
                                    <span>
                                        <span class="block text-sm font-bold {{ $approveDecision === 'disetujui' ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300' }}">Disetujui</span>
                                        <span class="block text-[11px] text-gray-400">Kontrak diperpanjang</span>
                                    </span>
                                </button>
                                <button type="button" wire:click="$set('approveDecision', 'tidak_disetujui')"
                                        class="flex items-center gap-3 rounded-xl border px-4 py-3 text-left transition-all {{ $approveDecision === 'tidak_disetujui' ? 'border-red-500 bg-red-50 dark:bg-red-950 ring-1 ring-red-500' : 'border-gray-200 dark:border-gray-700 hover:border-red-300 dark:hover:border-red-700' }}">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $approveDecision === 'tidak_disetujui' ? 'bg-red-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                    <span>
                                        <span class="block text-sm font-bold {{ $approveDecision === 'tidak_disetujui' ? 'text-red-700 dark:text-red-300' : 'text-gray-700 dark:text-gray-300' }}">Tidak Disetujui</span>
                                        <span class="block text-[11px] text-gray-400">Kontrak diakhiri</span>
                                    </span>
                                </button>
                            </div>
                            @error('approveDecision')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-widest text-gray-400">Catatan (Opsional)</label>
                                <textarea wire:model="approveCatatan" rows="2"
                                          placeholder="Tambahkan catatan approval bila perlu..."
                                          class="mt-2 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-none"></textarea>
                                @error('approveCatatan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="button" wire:click="saveApproval"
                                        wire:loading.attr="disabled" wire:target="saveApproval"
                                        class="inline-flex items-center justify-center min-w-[150px] px-6 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-700 disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-semibold shadow-sm shadow-violet-600/30 transition-all">
                                    <span wire:loading.remove wire:target="saveApproval" class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                                        Simpan Approval
                                    </span>
                                    <span wire:loading wire:target="saveApproval" class="flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @elseif($approveContractId && !$hasSubmittedEval)
                    <div class="mt-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40 p-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Menunggu evaluasi dari Koordinator & HR</p>
                            <p class="text-xs text-gray-400 mt-0.5">Approval akan tersedia setelah evaluasi disubmit.</p>
                        </div>
                    </div>
                    @endif

                    {{-- Section: Hasil Evaluasi --}}
                    <div class="mt-6 mb-3 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Hasil Evaluasi</span>
                        <span class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/60 px-1.5 text-[11px] font-bold text-emerald-700 dark:text-emerald-300">{{ collect($selectedEvaluations)->where('jenis', 'evaluasi')->count() }}</span>
                    </div>

                    @if(collect($selectedEvaluations)->where('jenis', 'evaluasi')->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($selectedEvaluations as $si => $eval)
                        @continue($eval['jenis'] !== 'evaluasi')
                        <button type="button" @click="active = {{ $si }}"
                                class="group w-full flex items-center justify-between gap-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3.5 text-left transition-all hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/40 hover:shadow-sm">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 font-bold uppercase text-sm">{{ strtoupper(substr($eval['evaluator'] ?? '?', 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                        {{ $eval['evaluator'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $eval['evaluator_role'] ?: 'Penilai' }} <span class="text-gray-300 dark:text-gray-600">•</span> {{ $eval['created_at'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                @if($eval['is_new_format'])
                                    <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 px-2 py-0.5 text-[10px] font-bold text-blue-600 dark:text-blue-400">Baru</span>
                                    <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">{{ $eval['final_score'] ?? '-' }}<span class="text-[10px] font-semibold text-gray-400">/4</span></span>
                                @else
                                    <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">{{ round(($eval['kinerja'] + $eval['disiplin'] + $eval['kerjasama'] + $eval['kepatuhan'] + $eval['keterampilan']) / 5, 1) }}<span class="text-[10px] font-semibold text-gray-400">/5</span></span>
                                @endif
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-400 transition-all group-hover:bg-emerald-600 group-hover:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                                </span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-6 text-center">
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada hasil evaluasi</p>
                        <p class="text-xs text-gray-400 mt-0.5">Hasil dari koordinator/HR akan tampil di sini.</p>
                    </div>
                    @endif

                    {{-- Section: Hasil Approval --}}
                    <div class="mt-6 mb-3 flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Hasil Approval</span>
                        <span class="inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900/60 px-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-300">{{ collect($selectedEvaluations)->where('jenis', 'approval')->count() }}</span>
                    </div>

                    @if(collect($selectedEvaluations)->where('jenis', 'approval')->isNotEmpty())
                    <div class="space-y-2.5">
                        @foreach($selectedEvaluations as $si => $eval)
                        @continue($eval['jenis'] !== 'approval')
                        <button type="button" @click="active = {{ $si }}"
                                class="group w-full flex items-center justify-between gap-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3.5 text-left transition-all hover:border-emerald-300 dark:hover:border-emerald-700 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/40 hover:shadow-sm">
                            <div class="flex items-center gap-3.5 min-w-0">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-950 text-violet-600 dark:text-violet-400 font-bold uppercase text-sm">{{ strtoupper(substr($eval['evaluator'] ?? '?', 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">
                                        {{ $eval['evaluator'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $eval['evaluator_role'] ?: 'Penilai' }} <span class="text-gray-300 dark:text-gray-600">•</span> {{ $eval['created_at'] }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold {{ $eval['decision'] === 'disetujui' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300' }}">
                                {{ $eval['decision'] === 'disetujui' ? '✓ Disetujui' : '✕ Tidak Disetujui' }}
                            </span>
                        </button>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 py-6 text-center">
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada hasil approval</p>
                        <p class="text-xs text-gray-400 mt-0.5">Keputusan manager/GM akan tampil di sini.</p>
                    </div>
                    @endif
                </div>

                {{-- STEP 2: Detail isi evaluasi yang diklik --}}
                <div x-show="active !== null" x-transition.opacity.duration.200ms>
                    @foreach($selectedEvaluations as $si => $eval)
                        @php
                            $isApproval = $eval['jenis'] === 'approval';
                            $isNew = $eval['is_new_format'] ?? false;
                            $rekomendasi = $eval['rekomendasi'] ?? '';
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
                                        @if($isApproval)
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $eval['decision'] === 'disetujui' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300' : 'bg-red-100 dark:bg-red-900/60 text-red-700 dark:text-red-300' }}">
                                                {{ $eval['decision'] === 'disetujui' ? '✓ Disetujui' : '✕ Tidak Disetujui' }}
                                            </span>
                                        @else
                                            <div class="text-right">
                                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Skor Akhir</p>
                                                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $isNew ? ($eval['final_score'] ?? '-') : round(($eval['kinerja'] + $eval['disiplin'] + $eval['kerjasama'] + $eval['kepatuhan'] + $eval['keterampilan']) / 5, 1) }}<span class="text-xs font-semibold text-gray-400">/4</span></p>
                                            </div>
                                        @endif
                                        @if($eval['can_edit'] && !$isApproval)
                                            <a href="{{ route('hris.kontrak-kerja.evaluasi', $eval['contract_id']) }}"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-5 space-y-5">
                                    @if(!$isApproval && $isNew)
                                        {{-- === NEW FORMAT === --}}

                                        {{-- Submitted badge --}}
                                        @if($eval['submitted_at'])
                                            <div class="flex items-center gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 px-4 py-2.5">
                                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                                                <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Disubmit {{ $eval['submitted_at'] }}</p>
                                            </div>
                                        @endif

                                        {{-- Rekomendasi badge --}}
                                        @if($rekomendasi)
                                            <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3.5 {{ $rekomendasi === 'perpanjang' ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950' : ($rekomendasi === 'pertimbangkan' ? 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950') }}">
                                                <div>
                                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Rekomendasi Kelanjutan</p>
                                                    <p class="text-sm font-bold {{ $rekomendasi === 'perpanjang' ? 'text-emerald-700 dark:text-emerald-300' : ($rekomendasi === 'pertimbangkan' ? 'text-amber-700 dark:text-amber-300' : 'text-red-700 dark:text-red-300') }} mt-1">
                                                        {{ $rekomendasi === 'perpanjang' ? 'Perpanjang Kontrak' : ($rekomendasi === 'pertimbangkan' ? 'Perlu Pertimbangan' : 'Tidak Diperpanjang') }}
                                                    </p>
                                                </div>
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-lg {{ $rekomendasi === 'perpanjang' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400' : ($rekomendasi === 'pertimbangkan' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400') }}">{{ $rekomendasi === 'perpanjang' ? '✓' : ($rekomendasi === 'pertimbangkan' ? '?' : '✕') }}</span>
                                            </div>
                                        @endif

                                        {{-- Category breakdown --}}
                                        @php $cats = \App\Support\ContractEvaluationConfig::categories(); @endphp
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-3">Breakdown per Kategori</p>
                                            <div class="grid grid-cols-2 gap-3">
                                                @foreach($cats as $cat)
                                                    @php $catScore = $eval['cat_scores'][$cat['key']] ?? null; @endphp
                                                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30 p-3.5">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ $cat['code'] }} • {{ $cat['label'] }}</span>
                                                            <span class="text-[10px] font-bold text-gray-400">{{ $cat['weight'] }}%</span>
                                                        </div>
                                                        <p class="text-lg font-extrabold {{ $catScore !== null && $catScore >= 3 ? 'text-emerald-600 dark:text-emerald-400' : ($catScore !== null && $catScore >= 2 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">{{ $catScore !== null ? $catScore : '-' }}<span class="text-xs font-semibold text-gray-400">/4</span></p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Per-indicator list per category --}}
                                        @foreach($cats as $cat)
                                            <div>
                                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2.5">{{ $cat['code'] }}. {{ $cat['label'] }}</p>
                                                @php
                                                    $indLookup = collect($eval['indicators'])->mapWithKeys(fn ($i) => [$i['field'] => $i['value']]);
                                                @endphp
                                                <div class="space-y-2">
                                                    @foreach($cat['indicators'] as $ind)
                                                        @php $val = $indLookup[$ind['field']] ?? null; @endphp
                                                        <div class="grid grid-cols-[1fr_60px] items-center gap-3 rounded-lg bg-gray-50/50 dark:bg-gray-800/30 px-3.5 py-2.5">
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $ind['label'] }}</p>
                                                                <p class="text-[10px] text-gray-400 mt-0.5">Bobot {{ $ind['weight'] }}%</p>
                                                            </div>
                                                            @if($val !== null)
                                                                <div class="flex items-center gap-1 justify-end">
                                                                    @for($i = 0; $i < 4; $i++)
                                                                        <span class="w-4 h-4 rounded-sm {{ $val > $i ? 'bg-amber-400' : 'bg-gray-200 dark:bg-gray-700' }}"></span>
                                                                    @endfor
                                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 ml-1">{{ $val }}/4</span>
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-400 text-right">-</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Catatan --}}
                                        @if($eval['catatan_kelebihan'] || $eval['catatan_kekurangan'])
                                            <div>
                                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2.5">Catatan Evaluasi</p>
                                                <div class="space-y-2">
                                                    @if($eval['catatan_kelebihan'])
                                                        <div class="rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 px-4 py-3">
                                                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-500 mb-1">Kelebihan</p>
                                                            <p class="text-sm text-emerald-800 dark:text-emerald-200 leading-relaxed whitespace-pre-line">{{ $eval['catatan_kelebihan'] }}</p>
                                                        </div>
                                                    @endif
                                                    @if($eval['catatan_kekurangan'])
                                                        <div class="rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 px-4 py-3">
                                                            <p class="text-[10px] font-bold uppercase tracking-widest text-red-500 mb-1">Kekurangan</p>
                                                            <p class="text-sm text-red-800 dark:text-red-200 leading-relaxed whitespace-pre-line">{{ $eval['catatan_kekurangan'] }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Rekomendasi Pengembangan --}}
                                        @if($eval['rekomendasi_pengembangan'] && count($eval['rekomendasi_pengembangan']) > 0)
                                            <div>
                                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2.5">Rekomendasi Pengembangan</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($eval['rekomendasi_pengembangan'] as $tag)
                                                        <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 px-3 py-1 text-xs font-semibold text-blue-700 dark:text-blue-300">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Perpanjangan --}}
                                        @if($eval['perpanjangan_bulan'])
                                            <div class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3">
                                                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-1">Rencana Perpanjangan</p>
                                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $eval['perpanjangan_bulan'] }} bulan</p>
                                                @if($eval['perpanjangan_mulai'] && $eval['perpanjangan_berakhir'])
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $eval['perpanjangan_mulai'] }} — {{ $eval['perpanjangan_berakhir'] }}</p>
                                                @endif
                                            </div>
                                        @endif

                                    @elseif(!$isApproval)
                                        {{-- === LEGACY FORMAT === --}}
                                        @php
                                            $avg = round(($eval['kinerja'] + $eval['disiplin'] + $eval['kerjasama'] + $eval['kepatuhan'] + $eval['keterampilan']) / 5, 1);
                                            $detailCriteria = [
                                                ['label' => 'Kinerja', 'value' => $eval['kinerja'] ?? 0],
                                                ['label' => 'Disiplin', 'value' => $eval['disiplin'] ?? 0],
                                                ['label' => 'Kerjasama', 'value' => $eval['kerjasama'] ?? 0],
                                                ['label' => 'Kepatuhan', 'value' => $eval['kepatuhan'] ?? 0],
                                                ['label' => 'Keterampilan', 'value' => $eval['keterampilan'] ?? 0],
                                            ];
                                        @endphp

                                        {{-- Rekomendasi badge --}}
                                        @if($rekomendasi)
                                            <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3.5 {{ $rekomendasi === 'perpanjang' ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950' : ($rekomendasi === 'pertimbangkan' ? 'border-amber-200 bg-amber-50 dark:border-amber-800 dark:bg-amber-950' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950') }}">
                                                <div>
                                                    <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400">Rekomendasi Kelanjutan</p>
                                                    <p class="text-sm font-bold {{ $rekomendasi === 'perpanjang' ? 'text-emerald-700 dark:text-emerald-300' : ($rekomendasi === 'pertimbangkan' ? 'text-amber-700 dark:text-amber-300' : 'text-red-700 dark:text-red-300') }} mt-1">
                                                        {{ $rekomendasi === 'perpanjang' ? 'Perpanjang Kontrak' : ($rekomendasi === 'pertimbangkan' ? 'Perlu Pertimbangan' : 'Tidak Diperpanjang') }}
                                                    </p>
                                                </div>
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-lg {{ $rekomendasi === 'perpanjang' ? 'bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400' : ($rekomendasi === 'pertimbangkan' ? 'bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400') }}">{{ $rekomendasi === 'perpanjang' ? '✓' : ($rekomendasi === 'pertimbangkan' ? '?' : '✕') }}</span>
                                            </div>
                                        @endif

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
                                    @endif

                                    {{-- Approval catatan --}}
                                    @if($isApproval && $eval['catatan'])
                                        <div>
                                            <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 mb-2">Catatan Approval</p>
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
