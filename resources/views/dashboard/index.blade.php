@push('topbar-left')
    <div>
        <h1 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
        <p class="text-xs text-gray-400 mt-0.5">Ringkasan seluruh data dan aktivitas</p>
    </div>
@endpush

<x-app-layout title="Dashboard">

@if($karyawanView ?? false)

    @include('partials.birthday-banner', [
        'birthdayEmployees' => $birthdayEmployees ?? collect(),
        'birthdayEmployee' => $birthdayEmployee ?? null,
        'birthdayWishes' => $birthdayWishes ?? collect(),
        'hideBirthdayBanner' => $hideBirthdayBanner ?? false,
        'alreadySentWish' => $alreadySentWish ?? false,
    ])

    @if($employee && $karyawanData)
    {{-- Welcome Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-violet-700 p-6 sm:p-8 mb-6">
        <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/></svg>
        </div>
        <div class="absolute -bottom-8 -left-8 w-40 h-40 rounded-full bg-white/5 blur-2xl"></div>
        <div class="relative flex items-center gap-4 sm:gap-5">
            <div class="flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white shadow-lg ring-2 ring-white/20 shrink-0">
                @if($employee->foto_url)
                    <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="w-full h-full rounded-2xl object-cover">
                @else
                    <span class="text-2xl font-bold font-display">{{ substr($employee->nama, 0, 1) }}</span>
                @endif
            </div>
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-display font-bold text-white">Selamat Datang, {{ $employee->nama }}</h1>
                <p class="text-sm text-white/80 mt-0.5">{{ $employee->positionNames() ?: '-' }} <span class="mx-1.5 text-white/40">•</span> Divisi {{ $employee->divisionNames() ?: '-' }}</p>
            </div>
        </div>
        @include('partials.birthday-fireworks', ['birthdayEmployee' => $birthdayEmployee ?? null])
        @include('partials.birthday-wishes', ['birthdayWishes' => $birthdayWishes ?? collect()])
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-sky-500 text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-primary text-[10px]">Bulan Ini</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $karyawanData['total_hadir_bulan_ini'] }}</span>
                <span class="text-sm font-medium text-gray-400">hari</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kehadiran Bulan Ini</p>
            <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.03] dark:opacity-[0.05]">
                <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="badge-success text-[10px]">Tahunan</span>
            </div>
            <div class="flex items-baseline gap-1">
                @if($karyawanData['cuti_aktif'] ?? true)
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $karyawanData['sisa_cuti'] }}</span>
                <span class="text-sm font-medium text-gray-400">/ {{ $karyawanData['jatah_cuti'] }} hari</span>
                @else
                <span class="text-2xl font-bold font-display text-gray-400 dark:text-gray-500">—</span>
                <span class="text-sm font-medium text-gray-400">belum aktif</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sisa Cuti Tahunan</p>
            @if($karyawanData['cuti_aktif'] ?? true)
            <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500 mt-1">
                Akumulasi {{ $karyawanData['terakumulasi_cuti'] }} hari &bull; Terpakai {{ $karyawanData['used_cuti'] }} hari
            </p>
            <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-green-500 transition-all duration-500" style="width: {{ $karyawanData['jatah_cuti'] > 0 ? ($karyawanData['sisa_cuti'] / $karyawanData['jatah_cuti']) * 100 : 0 }}%"></div>
            </div>
            @else
            <p class="mt-2 text-[11px] font-medium text-amber-600 dark:text-amber-400">
                Aktif setelah {{ $karyawanData['cuti_aktif_date'] ?? '1 tahun bekerja sejak kontrak pertama' }}
            </p>
            @endif
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-warning text-[10px]">Menunggu</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $karyawanData['pending_count'] }}</span>
                <span class="text-sm font-medium text-gray-400">pengajuan</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pengajuan Menunggu</p>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-500 text-white shadow-lg shadow-red-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-danger text-[10px]">Bulan Ini</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $karyawanData['total_terlambat_bulan_ini'] }}</span>
                <span class="text-sm font-medium text-gray-400">kali</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Keterlambatan Bulan Ini</p>
        </div>
    </div>

    @unless(auth()->user()->isStaff() || auth()->user()->isStaffCreative() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt() || auth()->user()->isKoordinatorCreative() || auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffAdmin() || auth()->user()->isStaffStock() || auth()->user()->isKoordinatorStock() || auth()->user()->isStaffHostMlbb() || auth()->user()->isKoordinatorMlbb() || auth()->user()->isKoordinatorEfootball() || auth()->user()->isKoordinatorValorant() || auth()->user()->isStaffHostEfootball() || auth()->user()->isStaffHostValorant())
    {{-- Quick Actions --}}
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 mb-6 shadow-sm">
        <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            <a href="{{ route('hris.absensi') }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Absen Masuk</span>
            </a>
            <a href="{{ route('hris.absensi') }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Absen Pulang</span>
            </a>
            <a href="{{ route('hris.cuti-izin') }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Ajukan Cuti</span>
            </a>
            <a href="{{ route('hris.cuti-izin') }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Ajukan Izin</span>
            </a>
            <a href="{{ route('meeting.jadwal') }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Lihat Jadwal</span>
            </a>
            <a href="{{ route('hris.employees.show', $employee->id) }}" class="group flex flex-col items-center gap-2 p-3 sm:p-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 border border-transparent hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-200">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white dark:bg-gray-700 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 group-hover:scale-110 shadow-sm transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 text-center leading-tight">Profil Saya</span>
            </a>
        </div>
    </div>
    @endunless

    @unless(auth()->user()->isStaff() || auth()->user()->isStaffCreative() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt() || auth()->user()->isKoordinatorCreative() || auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffAdmin() || auth()->user()->isStaffStock() || auth()->user()->isKoordinatorStock() || auth()->user()->isStaffHostMlbb() || auth()->user()->isKoordinatorMlbb() || auth()->user()->isKoordinatorEfootball() || auth()->user()->isKoordinatorValorant() || auth()->user()->isStaffHostEfootball() || auth()->user()->isStaffHostValorant())
    {{-- Attendance Today --}}
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Jam Kedatangan & Status Kehadiran</h3>
            <span class="text-xs text-gray-400">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
        </div>
        @if($karyawanData['attendance_today'])
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800/50 p-5 flex items-center gap-4 border border-gray-100 dark:border-gray-700">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jam Kedatangan</p>
                        <p class="text-xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $karyawanData['attendance_today']['time_in'] }}</p>
                    </div>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/10 p-5 flex items-center gap-4 border border-emerald-100 dark:border-emerald-800/30">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status Kehadiran</p>
                        @if(in_array($karyawanData['attendance_today']['status'], ['tepat waktu', 'hadir']))
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-sm font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 mt-1">Hadir</span>
                        @elseif($karyawanData['attendance_today']['status'] === 'terlambat')
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-sm font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 mt-1">Terlambat</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-sm font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 mt-1">{{ ucfirst($karyawanData['attendance_today']['status']) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Absensi Hari Ini</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Silakan lakukan absensi masuk</p>
                    </div>
                </div>
                <a href="{{ route('hris.absensi') }}" class="btn-primary text-xs py-2 px-4 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    Absen Sekarang
                </a>
            </div>
        @endif
    </div>
    @endunless

    {{-- Pengumuman + Jadwal Kerja --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        {{-- Work Schedule --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Jadwal Kerja</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Jam kerja, lokasi & supervisor Anda</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Jam Kerja</p>
                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">08:00 - 17:00</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Lokasi Kerja</p>
                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $employee->lokasi_kerja ?? 'Kantor Pusat' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-violet-50 dark:bg-violet-900/20 text-violet-600 dark:text-violet-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Supervisor</p>
                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $employee->atasan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Announcements --}}
        <div x-data="{ openAnnouncement: false, openAllAnn: false, selectedAnn: null, announcements: {{ \Illuminate\Support\Js::from($karyawanData['announcements'] ?? []) }}, viewAnn(id) { this.selectedAnn = this.announcements.find(a => a.id === id); this.openAnnouncement = true; } }" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Pengumuman</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Informasi terbaru untuk Anda</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 text-white shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                </div>
            </div>
            @if(count($karyawanData['announcements'] ?? []) > 0)
                @php
                    $ann = $karyawanData['announcements'][0];
                    // Path relatif agar fetch tetap benar walau diakses dari host/IP lain
                    $markReadUrl = parse_url(route('hris.announcements.mark-read', $ann['id']), PHP_URL_PATH);
                @endphp
                <div x-show="announcements.length > 0" x-cloak
                     @click="viewAnn({{ $ann['id'] }})"
                     class="group relative overflow-hidden rounded-xl p-4 border transition-all cursor-pointer"
                     :class="announcements[0]?.is_read ? 'bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800' : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 hover:border-blue-300 dark:hover:border-blue-700'">
                    <div class="relative flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span x-show="!announcements[0]?.is_read" class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
                                <p class="text-xs font-semibold truncate" :class="announcements[0]?.is_read ? 'text-gray-900 dark:text-gray-100' : 'text-blue-900 dark:text-blue-100'">{{ $ann['title'] }}</p>
                            </div>
                            <p class="text-[11px] mt-0.5 line-clamp-2" :class="announcements[0]?.is_read ? 'text-gray-500 dark:text-gray-400' : 'text-blue-700/80 dark:text-blue-200/80'">{{ $ann['summary'] ?? '-' }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="text-[10px] text-gray-400">{{ $ann['date'] }}</span>
                            <button x-show="!announcements[0]?.is_read" @click.stop="
                                const btn = $el; btn.disabled = true;
                                const target = announcements.find(x => x.id === {{ $ann['id'] }});
                                if (!target || target.is_read) { btn.disabled = false; return; }
                                target.is_read = true;
                                Alpine.store('successModal').show('Pengumuman ditandai sebagai sudah dibaca.');
                                fetch('{{ $markReadUrl }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                                    .catch((err) => {
                                        target.is_read = false;
                                        Alpine.store('toast').add('error', 'Gagal menandai pengumuman (' + (err && err.message ? err.message : 'jaringan') + ').');
                                    })
                                    .finally(() => { btn.disabled = false; });
                            " class="rounded-lg bg-blue-600 text-white px-3 py-1.5 text-[10px] font-semibold hover:bg-blue-700 transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                                Dibaca
                            </button>
                            <span x-show="announcements[0]?.is_read" class="rounded-lg bg-gray-100 dark:bg-gray-700/60 text-gray-500 dark:text-gray-400 px-3 py-1.5 text-[10px] font-semibold inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Sudah Dibaca
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Lihat Selengkapnya (di luar card) --}}
                <button type="button" @click="openAllAnn = true"
                        class="mt-3 w-full inline-flex items-center justify-center gap-1 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 py-2 text-[11px] font-semibold transition-colors"
                        :class="announcements[0]?.is_read ? 'text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300 dark:hover:border-primary-700' : 'text-blue-600 dark:text-blue-300 hover:bg-blue-50/60 dark:hover:bg-blue-950/40 hover:border-blue-300 dark:hover:border-blue-700'">
                    Lihat Selengkapnya
                    <span x-show="announcements.length > 1" class="tabular-nums opacity-70">(<span x-text="announcements.length"></span>)</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
            @else
                <div class="py-8 text-center">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-gray-100 dark:bg-gray-800">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada pengumuman</p>
                </div>
            @endif

            {{-- Semua Pengumuman Modal (Lihat Selengkapnya) --}}
            <div x-show="openAllAnn" x-cloak
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
                 @click="openAllAnn = false"
                 @keydown.escape.window="openAllAnn = false">
                <div @click.stop
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden flex flex-col max-h-[75vh]">
                    <div class="relative overflow-hidden bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 px-6 py-5 shrink-0">
                        <div class="relative flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-lg font-display font-bold text-white">Semua Pengumuman</h3>
                                <p class="text-xs text-white/70 mt-0.5"><span x-text="announcements.length"></span> pengumuman &middot; urut dari yang terbaru</p>
                            </div>
                            <button @click="openAllAnn = false" aria-label="Tutup" class="rounded-lg p-1.5 text-white/80 hover:text-white hover:bg-white/10 transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60">
                        <template x-for="a in announcements" :key="a.id">
                            <button type="button" @click="viewAnn(a.id); openAllAnn = false"
                                    class="w-full flex items-start gap-3 px-5 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg mt-0.5"
                                      :class="a.is_read ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' : 'bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.06 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="flex items-center gap-1.5 min-w-0">
                                        <span class="text-sm font-semibold truncate" :class="a.is_read ? 'text-gray-900 dark:text-gray-100' : 'text-blue-900 dark:text-blue-100'" x-text="a.title"></span>
                                        <span x-show="!a.is_read" class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0" title="Belum dibaca"></span>
                                    </span>
                                    <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-0.5" x-text="a.date"></span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2" x-text="a.summary || '-'"></span>
                                    <span class="block text-[10px] font-semibold mt-1.5 inline-flex items-center gap-1" :class="a.is_read ? 'text-gray-400 dark:text-gray-500' : 'text-blue-600 dark:text-blue-300'">
                                        <span x-text="a.is_read ? 'Sudah Dibaca' : 'Baca Pengumuman'"></span>
                                    </span>
                                </span>
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mt-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Detail Modal --}}
            <div x-show="openAnnouncement" x-cloak
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
                 @click="openAnnouncement = false">
                <div @click.stop
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                     class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">
                    <div class="relative overflow-hidden bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 px-6 py-5">
                        <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/></svg>
                        </div>
                        <div class="relative flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-lg font-display font-bold text-white" x-text="selectedAnn?.title"></h3>
                                <p class="text-xs text-white/70 mt-0.5" x-text="selectedAnn?.date"></p>
                            </div>
                            <button @click="openAnnouncement = false" class="rounded-lg p-1.5 text-white/80 hover:text-white hover:bg-white/10 transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="selectedAnn?.summary" x-show="selectedAnn?.summary"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-3 leading-relaxed whitespace-pre-line" x-text="selectedAnn?.content"></p>
                        <p class="text-xs text-gray-400 mt-4" x-show="!selectedAnn?.content">Tidak ada detail tambahan untuk pengumuman ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @else
    {{-- No employee linked --}}
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-12 text-center">
        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 dark:bg-amber-900/20">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Akun Belum Terhubung</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">Akun Anda belum terhubung ke data karyawan. Silakan hubungi admin untuk menghubungkan akun Anda.</p>
    </div>
    @endif

@else
    {{-- Admin / Direksi Dashboard --}}

    @include('partials.birthday-banner', [
        'birthdayEmployees' => $birthdayEmployees ?? collect(),
        'birthdayEmployee' => $birthdayEmployee ?? null,
        'birthdayWishes' => $birthdayWishes ?? collect(),
        'hideBirthdayBanner' => $hideBirthdayBanner ?? false,
        'alreadySentWish' => $alreadySentWish ?? false,
    ])

    {{-- Welcome Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-violet-700 p-6 sm:p-8 mb-6">
        <div class="absolute top-0 right-0 w-96 h-96 opacity-5">
            <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/><circle cx="180" cy="50" r="30" fill="white"/><circle cx="30" cy="160" r="20" fill="white"/></svg>
        </div>
        <div class="relative">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white shadow-lg ring-2 ring-white/20 shrink-0">
                    @if($employee && $employee->foto_url)
                        <img src="{{ $employee->foto_url }}" alt="{{ $employee->nama }}" class="w-full h-full rounded-2xl object-cover">
                    @else
                        <span class="text-2xl font-bold font-display">{{ $employee ? substr($employee->nama, 0, 1) : substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-display font-bold text-white">Selamat Datang, {{ $employee->nama ?? auth()->user()->name }}</h1>
                    <p class="text-sm text-white/80 mt-0.5">
                        @if($employee && $employee->positionNames())
                            {{ $employee->positionNames() }}
                        @else
                            {{ ucfirst(auth()->user()->role) }}
                        @endif
                        <span class="mx-1.5 text-white/40">•</span>
                        @if($employee && $employee->divisionNames())
                            Divisi {{ $employee->divisionNames() }}
                        @else
                            {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                    <span class="text-xs font-semibold text-white/80">Total Karyawan</span>
                    <span class="text-sm font-bold text-white">{{ $stats['total_employees'] }}</span>
                </div>
                <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                    <span class="text-xs font-semibold text-white/80">Divisi</span>
                    <span class="text-sm font-bold text-white">{{ $stats['total_divisions'] }}</span>
                </div>
                @if(!auth()->user()->isStaffHr())
                <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                    <span class="text-xs font-semibold text-white/80">Pengajuan Menunggu</span>
                    <span class="text-sm font-bold text-white">{{ $pendingLeaveCount }}</span>
                </div>
                @endif
                <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                    <span class="text-xs font-semibold text-white/80">Total Meeting</span>
                    <span class="text-sm font-bold text-white">{{ $meetingStats['total_meetings'] }}</span>
                </div>
            </div>
            @include('partials.birthday-fireworks', ['birthdayEmployee' => $birthdayEmployee ?? null])
            @include('partials.birthday-wishes', ['birthdayWishes' => $birthdayWishes ?? collect()])
        </div>
    </div>

    @if(auth()->user()->isKoordinator() && isset($koordinatorStats) && $koordinatorStats)
    {{-- Personal Stats Cards for Koordinator --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-sky-500 text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-primary text-[10px]">Bulan Ini</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $koordinatorStats['total_hadir_bulan_ini'] }}</span>
                <span class="text-sm font-medium text-gray-400">hari</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kehadiran Bulan Ini</p>
            <div class="absolute bottom-0 right-0 w-20 h-20 opacity-[0.03] dark:opacity-[0.05]">
                <svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <span class="badge-success text-[10px]">Tahunan</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $koordinatorStats['sisa_cuti'] }}</span>
                <span class="text-sm font-medium text-gray-400">/ {{ $koordinatorStats['jatah_cuti'] }} hari</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sisa Cuti Tahunan</p>
            <p class="text-[11px] font-medium text-gray-400 dark:text-gray-500 mt-1">
                Akumulasi {{ $koordinatorStats['terakumulasi_cuti'] }} hari &bull; Terpakai {{ $koordinatorStats['used_cuti'] }} hari
            </p>
            <div class="mt-2 w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-green-500 transition-all duration-500" style="width: {{ $koordinatorStats['jatah_cuti'] > 0 ? ($koordinatorStats['sisa_cuti'] / $koordinatorStats['jatah_cuti']) * 100 : 0 }}%"></div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-warning text-[10px]">Menunggu</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $koordinatorStats['pending_count'] }}</span>
                <span class="text-sm font-medium text-gray-400">pengajuan</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pengajuan Menunggu</p>
        </div>

        <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-red-200 dark:hover:border-red-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-500 text-white shadow-lg shadow-red-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-danger text-[10px]">Bulan Ini</span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold font-display text-gray-900 dark:text-gray-100">{{ $koordinatorStats['total_terlambat_bulan_ini'] }}</span>
                <span class="text-sm font-medium text-gray-400">kali</span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Keterlambatan Bulan Ini</p>
        </div>
    </div>
    @endif

    {{-- Ringkasan Menu --}}
    <div x-data="{ openDivisiModal: false, openMeetingModal: false, openAssetModal: false }">
    <div class="grid {{ auth()->user()->isSuperAdmin() ? 'grid-cols-2' : 'grid-cols-1' }} md:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt())
        <div @click="openDivisiModal = true" class="group cursor-pointer rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold font-display text-gray-900 dark:text-gray-100">Data SDM</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Karyawan & Divisi</p>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $stats['total_employees'] }}</span>
                    <span class="text-xs text-gray-400">Karyawan</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $stats['total_divisions'] }}</span>
                    <span class="text-xs text-gray-400">Divisi</span>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-[11px] font-medium text-primary-600 dark:text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        @endunless

        @unless(auth()->user()->isSuperAdmin() || auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt() || (auth()->user()->isManager() && auth()->user()->isHeadOfStore()))
        <a href="{{ route('history.index') }}" class="group rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold font-display text-gray-900 dark:text-gray-100">Keuangan</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Payroll, Bonus & Insentif</p>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">Rp {{ number_format($stats['total_payroll'], 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-gray-400 mt-0.5">Total Payroll</p>
            <div class="mt-2 flex items-center gap-1 text-[11px] font-medium text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endunless

        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt())
        <div @click="openMeetingModal = true" class="group cursor-pointer rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-teal-200 dark:hover:border-teal-800 transition-all duration-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-cyan-500 text-white shadow-md group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold font-display text-gray-900 dark:text-gray-100">Meeting</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Semua</p>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $meetingStats['total_meetings'] }}</span>
                <span class="text-xs text-gray-400">Meeting</span>
            </div>
            <div class="mt-3 flex items-center gap-1 text-[11px] font-medium text-teal-600 dark:text-teal-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>Lihat Detail</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </div>
        </div>
        @endunless

        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt())
        @if(auth()->user()->isGmCeo() || auth()->user()->isSuperAdmin() || auth()->user()->isManager())
        <div @click="openAssetModal = true" class="group cursor-pointer rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 dark:hover:border-blue-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="badge-primary">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_assets'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data Aset</p>
        </div>
        @if(!auth()->user()->isStaffHr())
        <a href="{{ route('hris.cuti-izin') }}" class="group rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 dark:hover:border-amber-800 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Menunggu</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingLeaveCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengajuan Menunggu</p>
        </a>
        @endif
        @else
        @if(!auth()->user()->isStaffHr())
        <a href="{{ route('hris.cuti-izin') }}" class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Menunggu</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingLeaveCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengajuan Menunggu</p>
        </a>
        @endif
        @endif
        @endunless

        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt() || auth()->user()->isGmCeo() || auth()->user()->isSuperAdmin() || auth()->user()->isManager())
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-violet-500 text-white shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold font-display text-gray-900 dark:text-gray-100">Data Asset</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Kendaraan, Digital, SIM Card</p>
                </div>
            </div>
            <div class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 px-2.5 py-1">
                <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400">Segera hadir</span>
            </div>
        </div>
        @endunless
    </div>

    {{-- DIVISI MODAL --}}
    <div x-show="openDivisiModal" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="openDivisiModal = false">
        <div x-show="openDivisiModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Pilih Divisi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Klik divisi untuk melihat menu divisi</p>
                </div>
                <button @click="openDivisiModal = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <a href="{{ route('hris.employees.index') }}"
                   class="w-full flex items-center justify-between p-4 rounded-xl border-2 border-primary-100 dark:border-primary-900/50 bg-primary-50/50 dark:bg-primary-900/10 hover:border-primary-300 dark:hover:border-primary-700 transition-all group">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Semua Karyawan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Seluruh divisi</p>
                    </div>
                    <span class="text-lg font-bold font-display text-primary-600 dark:text-primary-400">{{ $stats['total_employees'] }}</span>
                </a>
                <div class="my-1 flex items-center gap-3 text-[11px] font-semibold text-gray-400 dark:text-gray-500">
                    <span class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
                    <span>Pilih Divisi</span>
                    <span class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($divisionStats as $ds)
                <a href="{{ (auth()->user()->isManager() || auth()->user()->isSuperAdmin()) ? route('hris.employees.index', ['division' => $ds['id']]) : route('dashboard.division', $ds['id']) }}"
                   class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800 hover:bg-primary-50/30 dark:hover:bg-primary-900/5 transition-all group">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $ds['nama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $ds['total'] }} karyawan</p>
                    </div>
                    <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $ds['total'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- DATA ASET MODAL --}}
    <div x-show="openAssetModal" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="openAssetModal = false">
        <div x-show="openAssetModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Data Aset per Kategori</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah seluruh aset</p>
                </div>
                <button @click="openAssetModal = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex items-center justify-between p-4 rounded-xl border-2 border-blue-100 dark:border-blue-900/50 bg-blue-50/50 dark:bg-blue-900/10">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Semua Kategori</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total seluruh aset</p>
                    </div>
                    <span class="text-lg font-bold font-display text-blue-600 dark:text-blue-400">{{ $stats['total_assets'] }}</span>
                </div>
                @foreach($assetStats as $as)
                @php $assetSlug = strtolower(str_replace(' ', '-', $as['nama'])); @endphp
                <a href="{{ route('assets.category', $assetSlug === 'asset-ruko' ? 'aset-ruko' : $assetSlug) }}"
                   class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 hover:bg-blue-50/30 dark:hover:bg-blue-900/5 transition-all group">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $as['nama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $as['total'] }} aset</p>
                    </div>
                    <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $as['total'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MEETING MODAL --}}
    <div x-show="openMeetingModal" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="openMeetingModal = false">
        <div x-show="openMeetingModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Meeting per Divisi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total seluruh meeting</p>
                </div>
                <button @click="openMeetingModal = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex items-center justify-between p-4 rounded-xl border-2 border-teal-100 dark:border-teal-900/50 bg-teal-50/50 dark:bg-teal-900/10">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">Semua Divisi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total meeting</p>
                    </div>
                    <span class="text-lg font-bold font-display text-teal-600 dark:text-teal-400">{{ $meetingStats['total_meetings'] }}</span>
                </div>
                @foreach($meetingStats['per_division'] as $md)
                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $md['nama'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $md['total'] }} meeting</p>
                    </div>
                    <span class="text-lg font-bold font-display text-gray-900 dark:text-gray-100">{{ $md['total'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    </div>

    {{-- 2x2 Grid: Kontrak, Reimbursement, Cuti, Pembayaran --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt() || auth()->user()->isGmCeo())
        {{-- Daily Tracking yang perlu direview (manager) --}}
        @if(auth()->user()->isManager())
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Daily Tracking</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Yang perlu direview</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-red-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @if($managerReviewStats['daily_tracking']['count'] > 0)
                    <div class="space-y-2.5">
                        @foreach($managerReviewStats['daily_tracking']['items'] as $dt)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $dt['employee'] }}</p>
                                    <p class="text-[11px] text-rose-600 dark:text-rose-400">{{ $dt['subtitle'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $managerReviewStats['daily_tracking']['count'] }} perlu direview</span>
                        <a href="{{ route('hris.daily-tracking') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Selengkapnya &rarr;</a>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full py-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Semua daily tracking sudah direview</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @else
        {{-- Kontrak Akan Berakhir --}}
        <div x-data="{ openKontrakModal: false }" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Kontrak Akan Berakhir</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">14 hari ke depan</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-rose-500 to-red-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @if($expiringContractCount > 0)
                    @php $previewContracts = array_slice($expiringContracts, 0, 2); @endphp
                    <div class="space-y-2.5">
                        @foreach($previewContracts as $ec)
                            @php $isUrgent = $ec['days_remaining'] < 3; @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $isUrgent ? 'bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30' : 'bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30' }}">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $ec['employee'] }}</p>
                                    <p class="text-[11px] {{ $isUrgent ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">{{ $ec['posisi'] }} • Berakhir {{ $ec['tanggal_berakhir'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $expiringContractCount }} kontrak akan berakhir</span>
                        @if($expiringContractCount > 2)
                        <button @click="openKontrakModal = true" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Semua &rarr;</button>
                        @else
                        <a href="{{ route('hris.kontrak-kerja') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Selengkapnya &rarr;</a>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-center h-full py-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Semua kontrak masih berlaku</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- KONTRAK MODAL --}}
            <div x-show="openKontrakModal" x-cloak
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
                 @click="openKontrakModal = false">
                <div x-show="openKontrakModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Kontrak Akan Berakhir</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $expiringContractCount }} kontrak berakhir dalam 14 hari ke depan</p>
                        </div>
                        <button @click="openKontrakModal = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto pr-1 space-y-2.5">
                        @foreach($expiringContracts as $ec)
                            @php $isUrgent = $ec['days_remaining'] < 3; @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl {{ $isUrgent ? 'bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30' : 'bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30' }}">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $ec['employee'] }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $ec['posisi'] }} • Berakhir {{ $ec['tanggal_berakhir'] }}</p>
                                </div>
                                <span class="shrink-0 ml-3 text-[10px] font-bold px-2 py-1 rounded-lg {{ $isUrgent ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                    {{ $ec['days_remaining'] }} hari lagi
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $expiringContractCount }} kontrak akan berakhir</span>
                        <a href="{{ route('hris.kontrak-kerja') }}" class="inline-flex items-center gap-1 rounded-lg bg-white border border-primary-200 px-3 py-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 hover:bg-primary-50 transition-colors">Buka Menu Kontrak &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endunless

        @unless(auth()->user()->isGmCeo() || auth()->user()->isStaffHr())
        {{-- Pengajuan Cuti & Izin --}}
        <div x-data="{ openCutiModal: false }" class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Pengajuan Cuti & Izin</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Perlu persetujuan</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @if($pendingLeaveCount > 0)
                    @php $previewLeaves = array_slice($pendingLeaveRequests, 0, 2); @endphp
                    <div class="space-y-2.5">
                        @foreach($previewLeaves as $pl)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $pl['employee'] }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $pl['jenis'] }} • {{ $pl['tanggal'] }}</p>
                                </div>
                                <a href="{{ route('hris.cuti-izin') }}" class="shrink-0 text-[10px] font-semibold text-amber-600 hover:text-amber-700 hover:underline ml-2">Proses</a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $pendingLeaveCount }} pengajuan menunggu</span>
                        @if($pendingLeaveCount > 2)
                        <button @click="openCutiModal = true" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Semua &rarr;</button>
                        @else
                        <a href="{{ route('hris.cuti-izin') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Selengkapnya &rarr;</a>
                        @endif
                    </div>
                @else
                    <div class="flex items-center justify-center h-full py-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Semua sudah diproses</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- CUTI & IZIN MODAL --}}
            <div x-show="openCutiModal" x-cloak
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
                 @click="openCutiModal = false">
                <div x-show="openCutiModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Pengajuan Cuti & Izin</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $pendingLeaveCount }} pengajuan menunggu persetujuan</p>
                        </div>
                        <button @click="openCutiModal = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto pr-1 space-y-2.5">
                        @foreach($pendingLeaveRequests as $pl)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $pl['employee'] }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $pl['jenis'] }} • {{ $pl['tanggal'] }}</p>
                                </div>
                                <a href="{{ route('hris.cuti-izin') }}" class="shrink-0 ml-3 text-[10px] font-bold px-2 py-1 rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">Proses</a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $pendingLeaveCount }} pengajuan menunggu</span>
                        <a href="{{ route('hris.cuti-izin') }}" class="inline-flex items-center gap-1 rounded-lg bg-white border border-primary-200 px-3 py-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 hover:bg-primary-50 transition-colors">Buka Menu Cuti & Izin &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        @endunless

        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt())
        {{-- Weekly Plan Report yang perlu direview (manager) --}}
        @if(auth()->user()->isManager())
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Weekly Plan Report</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Yang perlu direview</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @if($managerReviewStats['weekly_report']['count'] > 0)
                    <div class="space-y-2.5">
                        @foreach($managerReviewStats['weekly_report']['items'] as $wr)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $wr['employee'] }}</p>
                                    <p class="text-[11px] text-amber-600 dark:text-amber-400">{{ $wr['subtitle'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $managerReviewStats['weekly_report']['count'] }} perlu direview</span>
                        <a href="{{ route('hris.weekly-report') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Selengkapnya &rarr;</a>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full py-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Semua weekly plan report sudah direview</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @else
        {{-- Pembayaran Mendatang --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Pembayaran Mendatang</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Tagihan & langganan</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @php
                    $internetDays = 5;
                    $isUrgent = $internetDays <= 3;
                    $internetWarnClass = $isUrgent ? 'bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-900/30' : 'bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30';
                @endphp
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 rounded-xl {{ $internetWarnClass }}">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg {{ $isUrgent ? 'bg-rose-100 dark:bg-rose-900/20' : 'bg-amber-100 dark:bg-amber-900/20' }}">
                                <svg class="w-4 h-4 {{ $isUrgent ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-900 dark:text-gray-100">Internet</p>
                                <p class="text-[11px] {{ $isUrgent ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">Mendekati masa tenggang — {{ $internetDays }} hari lagi</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <span class="text-xs font-semibold {{ $isUrgent ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400' }}">1 pembayaran perlu diperhatikan</span>
                    <span class="text-xs font-semibold text-gray-400 cursor-default">Lihat Selengkapnya &rarr;</span>
                </div>
            </div>
        </div>
        @endif
        @endunless

        @unless(auth()->user()->isKoordinator() || auth()->user()->isStaff() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isStaffIt())
        {{-- Activity Competitor yang perlu direview (manager) --}}
        @if(auth()->user()->isManager())
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Activity Competitor</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Yang perlu direview</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                @if($managerReviewStats['activity_competitor']['count'] > 0)
                    <div class="space-y-2.5">
                        @foreach($managerReviewStats['activity_competitor']['items'] as $ac)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-cyan-50 dark:bg-cyan-900/10 border border-cyan-100 dark:border-cyan-900/30">
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $ac['employee'] }}</p>
                                    <p class="text-[11px] text-cyan-600 dark:text-cyan-400">{{ $ac['subtitle'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400">{{ $managerReviewStats['activity_competitor']['count'] }} perlu direview</span>
                        <a href="{{ route('hris.activity-competitor') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 hover:underline">Lihat Selengkapnya &rarr;</a>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full py-6">
                        <div class="text-center">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Semua activity competitor sudah direview</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @else
        {{-- Pengajuan Reimbursement --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">Reimbursement</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Pengajuan dana yang perlu diproses</p>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex-1">
                <div class="flex items-center justify-center h-full py-6">
                    <div class="text-center">
                        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada pengajuan reimbursement</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endunless
    </div>

@endif

{{-- Popup Pengumuman ------------------------------------------------------ --}}
{{-- Diambil dari data pengumuman tayang (is_published = true) di menu
     Pengumuman. "Aktif" = is_published = true (draft tidak tampil).
     Dismiss per-pengumuman via sessionStorage: announcement_popup_dismissed_{id}.
     Dipasang via x-teleport ke <body> agar lepas dari parent bertipe
     overflow/transform/stacking-context sehingga backdrop fixed inset-0
     menutupi seluruh viewport termasuk header/navbar/sidebar. --}}
<template x-teleport="body">
<div
    x-data="announcementPopup({{ \Illuminate\Support\Js::from($announcements ?? []) }})"
    x-init="init()"
    x-show="open"
    x-cloak
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
    @keydown.escape.window="close()">
    <div @click.stop
         x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
         class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">
        <div class="relative overflow-hidden bg-gradient-to-br from-cyan-500 via-blue-500 to-indigo-600 px-6 py-5">
            <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/></svg>
            </div>
            <div class="relative flex items-start justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-lg font-display font-bold text-white">Pengumuman</h3>
                        <p class="text-xs text-white/70 mt-0.5">Informasi terbaru untuk Anda</p>
                    </div>
                </div>
                <button type="button" @click="close()" aria-label="Tutup" class="rounded-lg p-1.5 text-white/80 hover:text-white hover:bg-white/10 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="px-6 py-5">
            <template x-if="selected">
                <div>
                    <p class="text-sm font-display font-bold text-gray-900 dark:text-gray-100" x-text="selected.title"></p>
                    <p class="text-xs mt-3 leading-relaxed text-gray-600 dark:text-gray-300 whitespace-pre-line line-clamp-6" x-text="selected.summary || selected.content || '-'"></p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-4 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        <span x-text="selected.date"></span>
                    </p>
                </div>
            </template>
            <template x-if="!selected">
                <p class="text-sm text-center text-gray-400 py-6">Tidak ada pengumuman aktif untuk ditampilkan.</p>
            </template>
        </div>

        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            <label class="inline-flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 cursor-pointer select-none">
                <input type="checkbox" x-model="checked" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                Jangan tampilkan lagi
            </label>
            <button type="button" @click="close()" class="rounded-lg border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 px-4 py-2 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
</template>

<script>
    function announcementPopup(announcements) {
        return {
            announcements: Array.isArray(announcements) ? announcements : [],
            selected: null,
            checked: false,
            open: false,
            storageKey(id) {
                return 'announcement_popup_dismissed_' + id;
            },
            init() {
                let candidate = null;
                for (const a of this.announcements) {
                    // Lewati pengumuman yang sudah di-"jangan tampilkan lagi" selama session ini.
                    if (sessionStorage.getItem(this.storageKey(a.id)) === 'true') {
                        continue;
                    }
                    // Ambil yang paling relevan/terbaru (list sudah diurut terbaru).
                    candidate = a;
                    break;
                }
                if (candidate) {
                    this.selected = candidate;
                    this.open = true;
                }
            },
            close() {
                // Menutup popup. Jika checkbox "Jangan tampilkan lagi" dicentang,
                // tandai dismiss di sessionStorage (per-pengumuman) agar tidak
                // muncul lagi selama sesi ini; jika tidak dicentang, popup tidak
                // menandai apapun sehingga muncul lagi saat refresh.
                // Preferensi sementara di browser, tidak mengubah status DB.
                if (this.selected && this.checked) {
                    sessionStorage.setItem(this.storageKey(this.selected.id), 'true');
                }
                this.open = false;
            },
        };
    }
</script>

</x-app-layout>
