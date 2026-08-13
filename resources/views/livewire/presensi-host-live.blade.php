@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Presensi Host Live</h1>
        <p class="text-xs text-gray-400 mt-0.5">Check-in &amp; check-out per sesi siaran</p>
    </div>
@endpush

<div wire:poll.5s>
    @if(!$employee)
        <div class="card">
            <p class="text-sm text-gray-500 dark:text-gray-400">Akun Anda tidak terhubung ke data karyawan. Hubungi admin HR untuk sinkronisasi data.</p>
        </div>
    @else
        {{-- Statistik --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    </div>
                    <span class="badge-info">Sesi</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sesi Presensi</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Hadir</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['hadir'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sesi Hadir</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    </div>
                    <span class="badge-warning">Telat</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['terlambat'] }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sesi Terlambat</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            {{-- Kartu Sesi Saat Ini --}}
            <div class="card lg:col-span-1 overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Sesi Saat Ini</h2>
                    <span class="text-[10px] font-medium text-gray-400">{{ now()->format('d M Y H:i') }}</span>
                </div>

                @php
                    $config = \App\Models\AttendanceSession::sessionConfig($sesi);
                @endphp

                <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-4">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Terdeteksi otomatis dari jam</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $config['label'] ?? 'Sesi ' . $sesi }} <span class="text-sm font-medium text-gray-400">({{ $config['nama'] ?? '-' }})</span></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $config['mulai'] ?? '-' }} - {{ $config['selesai_display'] ?? $config['selesai'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tanggal tercatat: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</span></p>

                    <div class="mt-4">
                        <button type="button" wire:click="mulaiSesi" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                            Mulai Sesi {{ $sesi }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sesi Aktif (belum check-out) --}}
            <div class="card lg:col-span-2">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Sesi Sedang Berjalan</h2>

                @forelse($activeSessions as $session)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-xl border border-emerald-100 dark:border-emerald-900/40 bg-emerald-50/50 dark:bg-emerald-950/20 p-4 mb-3">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-500 text-white shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $session->namaSesi() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Check-in {{ $session->clock_in }} · {{ $session->tanggal->format('d M Y') }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="selesaiSesi({{ $session->id }})" class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Selesai Sesi
                        </button>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-6 text-center">
                        <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada sesi yang sedang berjalan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat Presensi Sesi --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Presensi Sesi</h2>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span> Tepat Waktu
                    <span class="ml-2 inline-flex h-2 w-2 rounded-full bg-amber-500"></span> Terlambat
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 dark:border-gray-800">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Sesi</th>
                            <th class="px-4 py-3">Check-in</th>
                            <th class="px-4 py-3">Check-out</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Telat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                        @forelse($mySessions as $session)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $session->tanggal->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">
                                        {{ $session->namaSesi() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $session->clock_in ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $session->clock_out ?? '-' }}
                                    @if($session->clock_out && $session->sesi === 3 && (int) substr($session->clock_out, 0, 2) < 7)
                                        <span class="ml-1 text-[10px] font-medium text-violet-500 dark:text-violet-400">(besok, tetap pulang)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($session->status === 'hadir')
                                        <span class="inline-flex items-center gap-1 rounded-full {{ $session->isTelat() ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' : 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300' }} px-2.5 py-0.5 text-xs font-medium">
                                            {{ $session->isTelat() ? 'Terlambat' : 'Tepat Waktu' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:text-gray-300 capitalize">{{ $session->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $session->late_minutes ? $session->late_minutes . ' mnt' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada presensi sesi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>