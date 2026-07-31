@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Presensi</h1>
        <p class="text-xs text-gray-400 mt-0.5">Rekap kehadiran karyawan harian</p>
    </div>
@endpush

<div>
    @if(auth()->user()->isKoordinator() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorCreative() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isKoordinatorMlbb() || auth()->user()->isKoordinatorEfootball() || auth()->user()->isKoordinatorValorant() || auth()->user()->isKoordinatorRoblox() || auth()->user()->isKoordinatorMonkeyPubg() || auth()->user()->isHeadOfStore())
    {{-- Tab Navigation --}}
    <div class="mb-6">
        <div class="inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            <button wire:click="$set('tab', 'saya')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'saya' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                Presensi Saya
            </button>
            <button wire:click="$set('tab', 'tim')"
                class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'tim' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                Presensi Tim
            </button>
        </div>
    </div>
    @endif

    @if($karyawanView || isset($koordinatorView))
        {{-- Karyawan Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAbsensi }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Absensi Saya</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Hadir</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $tepatWaktu }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tepat Waktu</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning">Telat</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $terlambat }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Terlambat</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalHadir }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Hadir</p>
            </div>

        </div>

        <div class="card">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Riwayat Absensi Saya</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $employee?->nama ?? '-' }}</p>
                </div>
                @if($attendanceHariIni)
                    <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Sudah Absen Hari Ini
                    </span>
                @else
                    <button wire:click="openAbsenModal" class="btn-primary text-xs py-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Absen Hari Ini
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="table-header">
                            <th class="px-6 py-3 w-12 text-center">No</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Jam Masuk</th>
                            <th class="px-6 py-3">Jam Keluar</th>
                            <th class="px-6 py-3">Durasi Kerja</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($riwayat as $att)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $riwayat->firstItem() + $loop->index }}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $att->duration ?? '-' }}</td>
                                <td class="table-cell">
                                    @php $ds = $att->display_status; @endphp
                                    @if($ds === 'tepat waktu')
                                        <span class="badge-success">Tepat Waktu</span>
                                    @elseif($ds === 'terlambat')
                                        <span class="badge-warning">Terlambat</span>
                                    @elseif($ds === 'tidak hadir')
                                        <span class="badge-danger">Tidak Hadir</span>
                                    @elseif($ds === 'izin')
                                        <span class="badge-info">Izin</span>
                                    @elseif($ds === 'sakit')
                                        <span class="badge-warning">Sakit</span>
                                    @elseif($ds === 'cuti')
                                        <span class="badge-info">Cuti</span>
                                    @else
                                        <span class="badge-secondary">{{ $ds }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada riwayat absensi</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lakukan absensi untuk memulai</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($riwayat->hasPages())
                <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                    {{ $riwayat->links() }}
                </div>
            @endif
        </div>

        <template x-teleport="body">
        {{-- ABSEN MODAL --}}
        <div x-data="{ open: $wire.entangle('showAbsenModal') }"
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
             @click="open = false">
            <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Absen Hari Ini</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d M Y') }}</p>
                    </div>
                    <button wire:click="closeAbsenModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitAbsen" class="space-y-4">
                    <div>
                        <x-input-label value="Status Kehadiran *" />
                        <div class="mt-2 grid grid-cols-3 gap-3">
                            <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center text-sm font-medium transition-all"
                                   :class="'hadir' === $wire.absenStatus ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="radio" wire:model="absenStatus" value="hadir" class="sr-only">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Hadir</span>
                            </label>
                            <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center text-sm font-medium transition-all"
                                   :class="'izin' === $wire.absenStatus ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="radio" wire:model="absenStatus" value="izin" class="sr-only">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                <span>Izin</span>
                            </label>
                            <label class="relative flex cursor-pointer flex-col items-center gap-2 rounded-xl border-2 p-4 text-center text-sm font-medium transition-all"
                                   :class="'sakit' === $wire.absenStatus ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-500'">
                                <input type="radio" wire:model="absenStatus" value="sakit" class="sr-only">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                                <span>Sakit</span>
                            </label>
                        </div>
                        @error('absenStatus') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="closeAbsenModal" class="btn-secondary text-xs">Batal</button>
                        <button type="submit" class="btn-primary text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Kirim
                        </button>
                    </div>
                </form>
        </div>
    </div>
    </template>
    @else
        {{-- Admin/Direksi Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalKaryawan }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Karyawan</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Hadir</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $hadir }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tepat Waktu</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning">Telat</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $terlambat }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Terlambat</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalHadir }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Hadir</p>
            </div>
        </div>

        {{-- Card wrapper for table --}}
        <div class="card">
            {{-- Filter bar --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
                <div class="flex items-center gap-3 flex-1 flex-wrap">
                    <div class="relative flex-1 max-w-xs">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama atau NIK..."
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                        >
                    </div>

                    <input
                        type="date"
                        wire:model.live="date"
                        class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                    >
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
                            <th class="px-6 py-3">Jam Masuk</th>
                            <th class="px-6 py-3">Jam Keluar</th>
                            <th class="px-6 py-3">Durasi Kerja</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($employees as $emp)
                            @php
                                $att = $attendances->get($emp->id);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $employees->firstItem() + $loop->index }}</td>
                                <td class="table-cell">
                                    <div class="flex items-center gap-2">
                                        @if($emp->foto_url)
                                            <img src="{{ $emp->foto_url }}" alt="{{ $emp->nama }}" class="w-8 h-8 rounded-lg object-contain bg-gray-50">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                                {{ strtoupper(substr($emp->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $emp->nama }}</span>
                                    </div>
                                </td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $emp->position ?? '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $att?->time_in ? \Carbon\Carbon::parse($att->time_in)->format('H:i') : '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $att?->time_out ? \Carbon\Carbon::parse($att->time_out)->format('H:i') : '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $att?->duration ?? '-' }}</td>
                                <td class="table-cell">
                                    @php
                                        $ds = $att?->display_status ?? 'tidak hadir';
                                    @endphp
                                    @if($ds === 'tepat waktu')
                                        <span class="badge-success">Tepat Waktu</span>
                                    @elseif($ds === 'terlambat')
                                        <span class="badge-warning">Terlambat</span>
                                    @elseif($ds === 'tidak hadir')
                                        <span class="badge-danger">Tidak Hadir</span>
                                    @elseif($ds === 'izin')
                                        <span class="badge-info">Izin</span>
                                    @elseif($ds === 'sakit')
                                        <span class="badge-warning">Sakit</span>
                                    @elseif($ds === 'cuti')
                                        <span class="badge-info">Cuti</span>
                                    @else
                                        <span class="badge-secondary">{{ $ds }}</span>
                                    @endif
                                </td>
                                <td class="table-cell text-center">
                                    <div x-data="{ open: false }" class="relative inline-block">
                                        <button @click="open = !open" @click.outside="open = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                        </button>
                                        <div x-show="open" x-cloak
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-100"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             @click.outside="open = false" class="absolute right-0 z-50 mt-1 w-44 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 py-1.5">
                                            <a href="{{ route('hris.employees.show', $emp) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Detail Karyawan
                                            </a>
                                        </div>
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
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada data absensi</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tidak ada catatan absensi untuk tanggal ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->hasPages())
                <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
