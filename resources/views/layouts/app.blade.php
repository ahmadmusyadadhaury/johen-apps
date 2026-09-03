<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Johen Sukses Abadi') }} @if($title ?? null) - {{ $title }} @endif</title>

        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:500,600,700,800&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 shadow-lg shadow-gray-200/50 dark:shadow-gray-900 transition-transform duration-300 will-change-transform lg:static lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex h-16 items-center gap-3 px-6 border-b border-gray-50 dark:border-gray-800">
                    <div x-data="logoAnimation()" class="relative flex items-center justify-center cursor-pointer select-none"
                        @mouseenter="glowIntensity = 0.5"
                        @mouseleave="glowIntensity = 0.2">
                        <div class="absolute inset-0 rounded-lg transition-all duration-1000 ease-out"
                            x-bind:style="`background: radial-gradient(circle, rgba(9,135,245,${glowIntensity}), rgba(124,58,237,${glowIntensity * 0.5}), transparent 70%); filter: blur(6px); transform: scale(${1 + glowIntensity * 0.25})`">
                        </div>
                        <div class="absolute inset-0 rounded-lg overflow-hidden">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent"
                                x-bind:style="`transform: translateX(${sweepX}%); transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1)`">
                            </div>
                        </div>
                        <div class="relative transition-transform duration-700 ease-out"
                            x-bind:style="`transform: perspective(400px) rotateY(${flipAngle}deg)`">
                            <img src="{{ asset('logo.png') }}" alt="Johen Sukses Abadi" class="h-7 w-auto relative">
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">Johen Application</span>
                        <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500 -mt-0.5">Management System</p>
                    </div>
                </div>

                @php
$activeMenu = match (true) {
    request()->routeIs('it.tickets.*', 'it.project', 'it.maintenance', 'hris.weekly-report', 'hris.weekly-report.show', 'hris.daily-tracking', 'hris.daily-tracking.game', 'hris.activity-competitor', 'hris.influencer-pengajuan') && auth()->user()->isHeadOfStore2() => 'monitoring',
    request()->routeIs('hris.weekly-report', 'hris.weekly-report.show', 'hris.daily-tracking', 'hris.daily-tracking.game', 'hris.activity-competitor', 'hris.influencer-pengajuan') && auth()->user()->isManager() && auth()->user()->isHeadOfStore() => 'monitoring',
    request()->routeIs('hris.weekly-report', 'hris.weekly-report.show', 'hris.activity-competitor') && auth()->user()->isKoordinatorGame() => '',
    request()->routeIs('hris.weekly-report', 'hris.weekly-report.show', 'hris.activity-competitor') && auth()->user()->isKoordinatorAdmin() => '',
    request()->routeIs('hris.weekly-report', 'hris.weekly-report.show', 'hris.daily-tracking', 'hris.daily-tracking.game', 'hris.activity-competitor') && auth()->user()->isKoordinatorIt() => '',
    request()->routeIs('hris.weekly-report', 'hris.weekly-report.show', 'hris.activity-competitor') && (auth()->user()->isStaffHostFf() || auth()->user()->isStaffHostMlbb()) => '',
    request()->routeIs('hris.daily-tracking-admin') => '',
    request()->routeIs('hris.daily-tracking-stock', 'hris.rekap-stok', 'hris.stok-ketersediaan', 'hris.stok-target', 'hris.weekly-report', 'hris.weekly-report.show') && (auth()->user()->isKoordinatorStock() || auth()->user()->isStaffStock()) => '',
    request()->routeIs('hris.influencer', 'hris.kalender-event', 'hris.content-plan') => '',
    request()->routeIs('hris.kontrak-kerja') && auth()->user()->isGmCeo() => 'operasional',
    request()->routeIs('hris.kontrak-kerja') && auth()->user()->isKoordinatorGame() => '',
    request()->routeIs('hris.absensi', 'hris.cuti-izin', 'hris.manual-book', 'hris.jobdesk', 'hris.weekly-report', 'hris.weekly-report.show', 'hris.daily-tracking', 'hris.daily-tracking.game', 'hris.activity-competitor', 'hris.influencer-pengajuan', 'hris.announcements', 'hris.pengumuman-saya', 'hris.pengarsipan', 'hris.birthday-wishes*', 'history.*') => 'operasional',
    request()->routeIs('hris.*') => 'sdm',
    request()->routeIs('bonus.*', 'reimbursement') => auth()->user()->isManager() ? 'operasional' : '',
    request()->routeIs('it.tickets.*') && !auth()->user()->isKoordinatorIt() && !auth()->user()->isStaffIt() && !auth()->user()->isHeadOfStore2() => 'operasional',
    request()->routeIs('meeting.*') => 'meeting',
    request()->routeIs('assets.*', 'digital-registries.*') => auth()->user()->canViewAll() && !auth()->user()->isKoordinator() ? (request()->routeIs('assets.index') ? 'sdm' : 'asset') : 'sdm',
    request()->routeIs('electricity.*', 'internet.*', 'ipl.*', 'payment-submissions.*', 'digital.*') => 'pembayaran',
    request()->routeIs('kelola-jabatan') => 'sdm',
    default => '',
};
                @endphp

                @php
$isDivisionView = false;
$activeDivisionMenu = null;

$divisionViewUser = auth()->check() && auth()->user()->canViewAll() && !auth()->user()->isKoordinator();

if ($divisionViewUser) {
    $divisionViewId = null;
    if (request()->routeIs('dashboard.division')) {
        $divisionViewRoute = request()->route('division');
        $divisionViewId = $divisionViewRoute instanceof \App\Models\Division ? $divisionViewRoute->id : (int) $divisionViewRoute;
    } elseif (session('division_menu')) {
        $divisionViewId = (int) session('division_menu');
    }

    if ($divisionViewId) {
        $divisionViewRecord = \App\Models\Division::find($divisionViewId);
        if ($divisionViewRecord) {
            $divisionViewMenu = \App\Support\DivisionMenu::for($divisionViewRecord->nama);
            if ($divisionViewMenu && (request()->routeIs('dashboard.division') || \App\Support\DivisionMenu::matchesAny($divisionViewMenu, request()))) {
                $isDivisionView = true;
                $activeDivisionMenu = $divisionViewMenu;
            }
        }
    }
}
                @endphp

                <nav x-data="{ openMenu: @js($activeMenu) }" class="flex-1 overflow-y-auto p-4 space-y-1">
                    @if($isDivisionView)
                        @include('layouts.partials.division-sidebar', ['menu' => $activeDivisionMenu])
                    @else
                    <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Menu</p>

                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <x-slot:icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        </x-slot>
                        Dashboard
                    </x-nav-link>

                    @if(auth()->user()->isManager())
                    <x-nav-link href="{{ route('hris.kontrak-kerja') }}" :active="request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi')">
                        <x-slot:icon>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                        </x-slot>
                        Penilaian
                    </x-nav-link>
                    @endif

                    @can('view-all')
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'sdm' ? null : 'sdm'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'sdm' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                SDM
                                <livewire:sidebar-position-note-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'sdm' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'sdm'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('hris.employees.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.employees.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Karyawan
                            </a>
                            <a href="{{ route('hris.divisions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.divisions.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Divisi
                            </a>
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isGmCeo() || auth()->user()->isStaffHr())
                            <a href="{{ route('kelola-jabatan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('kelola-jabatan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jabatan
                            </a>
                            @endif
                            @unless(auth()->user()->isAnyKoordinator() || auth()->user()->isManager())
                            <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Kontrak Kerja
                            </a>
                            @endunless
                            <a href="{{ route('hris.freelance') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.freelance') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Freelance
                            </a>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.struktur-organisasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.struktur-organisasi') }}" class="flex-1">Struktur Organisasi</a>
                                <livewire:sidebar-position-note-badge />
                            </div>
                            @if(auth()->user()->employee)
                            <a href="{{ route('hris.informasi-saya') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.informasi-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Informasi saya
                            </a>
                            @endif
                            <a href="{{ route('assets.index', ['mine' => 1]) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.index') && request()->boolean('mine') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Asset Saya
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'sdm' ? null : 'sdm'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'sdm' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                SDM
                                <livewire:sidebar-position-note-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'sdm' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'sdm'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.struktur-organisasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.struktur-organisasi') }}" class="flex-1">Struktur Organisasi</a>
                                <livewire:sidebar-position-note-badge />
                            </div>
                            @if(auth()->user()->employee)
                            <a href="{{ route('hris.informasi-saya') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.informasi-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Informasi saya
                            </a>
                            @endif
                            <a href="{{ route('assets.index', ['mine' => 1]) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.index') && request()->boolean('mine') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Asset Saya
                            </a>
                        </div>
                    </div>
                    @endcan



                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'operasional' ? null : 'operasional'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'operasional' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                Operasional
                                <livewire:sidebar-operasional-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'operasional' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'operasional'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('hris.absensi') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.absensi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Presensi
                            </a>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.cuti-izin') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.cuti-izin') }}" class="flex-1">Cuti dan Izin</a>
                                <livewire:sidebar-leave-badge />
                            </div>
                            <a href="{{ route('hris.jobdesk') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.jobdesk') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jobdesk saya
                            </a>
                            @if(!auth()->user()->isKoordinatorIt() && !auth()->user()->isStaffIt() && !auth()->user()->isHeadOfStore())
                            <a href="{{ route('it.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.tickets.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Ticketing IT
                            </a>
                            @endif
                            @if(auth()->user()->isStaff() || auth()->user()->isStaffAdmin() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isKoordinatorRoblox() || auth()->user()->isKoordinatorMonkeyPubg() || auth()->user()->isStaffIt() || auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffHostRoblox() || auth()->user()->isStaffHostMonkeyPubg() || auth()->user()->isSuperAdminLike())
                            <a href="{{ route('hris.manual-book') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.manual-book') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Manual Book
                            </a>
                            @endif
                            @if((auth()->user()->isKoordinator() || auth()->user()->isManager() || auth()->user()->isKoordinatorCreative()) && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.weekly-report') }}" class="flex-1">Weekly Plan Report</a>
                                <livewire:sidebar-feedback-badge type="weekly" />
                            </div>
                            @endif
                            @if((auth()->user()->isKoordinator() || auth()->user()->isManager()) && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.activity-competitor') }}" class="flex-1">Activity Competitor</a>
                                <livewire:sidebar-feedback-badge type="activity" />
                            </div>
                            @endif
                            @if(auth()->user()->isManager() && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.daily-tracking', 'hris.daily-tracking.game') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.daily-tracking') }}" class="flex-1">Daily Tracking</a>
                                <livewire:sidebar-daily-tracking-badge />
                            </div>
                            @endif
                            @if(auth()->user()->isManager() && !auth()->user()->isHeadOfStore2())
                            <a href="{{ route('reimbursement') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('reimbursement') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Reimbursement
                            </a>
                            @endif
                            @if(auth()->user()->isSuperAdminLike())
                            <a href="{{ route('hris.announcements') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.announcements', 'hris.pengumuman-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengumuman
                            </a>
                            <a href="{{ route('hris.birthday-wishes') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.birthday-wishes*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Ucapan Ulang Tahun
                            </a>
                            @elseif(auth()->user()->canViewPengumuman())
                            <a href="{{ route('hris.pengumuman-saya') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.pengumuman-saya', 'hris.announcements') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengumuman
                            </a>
                            @endif
                            @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('hris.pengarsipan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.pengarsipan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengarsipan
                            </a>
                            @endif
                            @if(auth()->user()->isGmCeo())
                            <a href="{{ route('history.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('history.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Slip Gaji
                            </a>
                            <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Penilaian
                            </a>
                            @endif
                            @if((auth()->user()->isGmCeo() || auth()->user()->isManager() || auth()->user()->isKoordinatorCreative() || (auth()->user()->isStaffCreative() && str_starts_with(auth()->user()->employee?->mainPosition()?->nama ?? '', 'Admin KOL')) || auth()->user()->isKoordinatorAdmin() || auth()->user()->isStaffAdmin()) && !auth()->user()->isHeadOfStore())
                            <a href="{{ route('hris.influencer-pengajuan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.influencer-pengajuan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengajuan Influencer
                            </a>
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->isKoordinatorCreative() || auth()->user()->isStaffCreative())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Creative</p>

                        <a href="{{ route('hris.influencer') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.influencer') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Influencer
                        </a>
                        <a href="{{ route('hris.kalender-event') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kalender-event') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            Kalender Event
                        </a>
                        <a href="{{ route('hris.content-plan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.content-plan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                            Content Plan
                        </a>
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorAdmin() || auth()->user()->isStaffAdmin())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Admin</p>
                        <a href="{{ route('hris.daily-tracking-admin') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.daily-tracking-admin') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                            Daily Tracking
                        </a>
                        @if(auth()->user()->isKoordinatorAdmin())
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorIt() || auth()->user()->isStaffIt())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi IT</p>
                        <a href="{{ route('it.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.tickets.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 7.5h15v5.25a2.25 2.25 0 010 4.5v2.25h-15v-2.25a2.25 2.25 0 010-4.5V7.5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v1.5m0 2.25v1.5"/></svg>
                            <span class="flex-1">Ticketing IT</span>
                            @if(!auth()->user()->isHeadOfStore2() && $itTicketMenungguCount > 0)
                                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold tabular-nums leading-none text-white shadow-sm">{{ $itTicketMenungguCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('it.project') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.project') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                            Project IT
                        </a>
                        <a href="{{ route('it.maintenance') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.maintenance') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            Jadwal Maintenance
                        </a>
                        @if(!auth()->user()->isStaffIt())
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif

                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorStock() || auth()->user()->isStaffStock())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Stock</p>
                        <a href="{{ route('hris.daily-tracking-stock') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.daily-tracking-stock') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                            Daily Tracking
                        </a>
                        <a href="{{ route('hris.rekap-stok') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.rekap-stok') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            Rekap Stok
                        </a>
                        <a href="{{ route('hris.stok-ketersediaan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.stok-ketersediaan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            Ketersediaan Stok
                        </a>
                        <a href="{{ route('hris.stok-target') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.stok-target') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            Target Stok
                        </a>
                        @if(!auth()->user()->isStaffStock())
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isManager() && auth()->user()->isHeadOfStore())
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'monitoring' ? null : 'monitoring'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'monitoring' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex flex-1 min-w-0 items-center justify-start gap-3 text-left whitespace-nowrap">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                Monitoring Koor
                                <livewire:sidebar-monitoring-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'monitoring' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'monitoring'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            @if(auth()->user()->isHeadOfStore2())
                            <a href="{{ route('it.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.tickets.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Ticketing IT
                            </a>
                            <a href="{{ route('it.project') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.project') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Project IT
                            </a>
                            <a href="{{ route('it.maintenance') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.maintenance') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jadwal Maintenance
                            </a>
                            @endif
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.weekly-report') }}" class="flex-1">Weekly Plan Report</a>
                                <livewire:sidebar-feedback-badge type="weekly" />
                            </div>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.daily-tracking', 'hris.daily-tracking.game') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.daily-tracking') }}" class="flex-1">Daily Tracking</a>
                                <livewire:sidebar-daily-tracking-badge />
                            </div>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.activity-competitor') }}" class="flex-1">Activity Competitor</a>
                                <livewire:sidebar-feedback-badge type="activity" />
                            </div>
                            <a href="{{ route('hris.influencer-pengajuan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.influencer-pengajuan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengajuan Influencer
                            </a>
                        </div>
                    </div>
                    @endif

                    @if(!auth()->user()->isKoordinatorFf() && !auth()->user()->isStaffHostFf() && (auth()->user()->isKoordinatorPubg() || auth()->user()->isStaffHostPubg()))
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Johen PUBG</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.daily-tracking') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking') }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorFf() || auth()->user()->isStaffHostFf())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi FF</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.daily-tracking') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking') }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
@if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorMlbb() || auth()->user()->isStaffHostMlbb())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi MLBB</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ (request()->routeIs('pubg.daily-tracking') && request()->input('divisi') === 'mlbb') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking', ['divisi' => 'mlbb']) }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->hasDivisionPosition('Koordinator FC Mobile'))
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi FC Mobile</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ (request()->routeIs('pubg.daily-tracking') && request()->query('divisi') === 'FC Mobile') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking', ['divisi' => 'FC Mobile']) }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        <a href="{{ route('pubg.running-rate', ['divisi' => 'FC Mobile']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ (request()->routeIs('pubg.running-rate') && request()->query('divisi') === 'FC Mobile') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 15.75V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorEfootball() || auth()->user()->isStaffHostEfootball())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi E-football</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.daily-tracking') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking') }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
@if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorValorant() || auth()->user()->isStaffHostValorant())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Valorant</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ (request()->routeIs('pubg.daily-tracking') && request()->input('divisi') === 'valorant') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking', ['divisi' => 'valorant']) }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
@if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorRoblox() || auth()->user()->isStaffHostRoblox())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Roblox</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.daily-tracking') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking') }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
@if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isKoordinatorMonkeyPubg() || auth()->user()->isStaffHostMonkeyPubg())
                    <div class="mt-4">
                        <p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">Divisi Monkey PUBG</p>
                        <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.daily-tracking') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <a href="{{ route('pubg.daily-tracking') }}" class="flex flex-1 items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                Daily Tracking
                            </a>
                            <livewire:sidebar-daily-tracking-badge />
                        </div>
                        @if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('pubg.running-rate') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('pubg.running-rate') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h4.5l1.5-4.5 3 9 1.5-4.5h4.5"/></svg>
                            Running Rate
                        </a>
                        <a href="{{ route('hris.weekly-report') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            Weekly Plan Report
                        </a>
                        @endif
@if(auth()->user()->isKoordinatorGame())
                        <a href="{{ route('hris.activity-competitor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            Activity Competitor
                        </a>
                        @endif
                        @if(auth()->user()->canEvaluateContract() || auth()->user()->canApproveContract())
                        <a href="{{ route('hris.kontrak-kerja') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                            Penilaian
                        </a>
                        @endif
                    </div>
                    @endif

                    @can('view-all')
                    @unless(auth()->user()->isKoordinator())
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'meeting' ? null : 'meeting'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'meeting' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                Meeting
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'meeting' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'meeting'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('meeting.jadwal') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('meeting.jadwal') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jadwal Meeting
                            </a>
                        </div>
                    </div>
                    @endunless
                    @endcan

                    @can('view-all')
                    @unless(auth()->user()->isKoordinator())
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'asset' ? null : 'asset'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'asset' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Data Asset
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'asset' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'asset'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('assets.category', 'kendaraan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/kendaraan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Kendaraan
                            </a>
                            <a href="{{ route('digital-registries.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('digital-registries.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Digital
                            </a>
                            <a href="{{ route('assets.category', 'sim-card') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/sim-card') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                SIM Card
                            </a>
                            <a href="{{ route('assets.category', 'peralatan-kantor') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/peralatan-kantor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Peralatan Kantor
                            </a>
                            <a href="{{ route('assets.category', 'aset-ruko') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/aset-ruko') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Asset Ruko
                            </a>
                            <a href="{{ route('assets.category', 'sosial-media') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/sosial-media') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Sosial Media
                            </a>
                            <a href="{{ route('assets.category', 'aset-mes') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/aset-mes') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Aset Mes
                            </a>
                            <a href="{{ route('assets.category', 'aset-tim') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.*') && request()->is('assets/aset-tim') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Aset Tim
                            </a>
                        </div>
                    </div>
                    @endunless

                    @unless(auth()->user()->isKoordinator())
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'pembayaran' ? null : 'pembayaran'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'pembayaran' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                Pembayaran
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'pembayaran' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'pembayaran'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('electricity.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('electricity.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Listrik
                            </a>
                            <a href="{{ route('internet.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('internet.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Internet
                            </a>
                            <a href="{{ route('digital.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('digital.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Digital
                            </a>
                            <a href="{{ route('ipl.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('ipl.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                IPL Ruko
                            </a>
                        </div>
                    </div>
                    @endunless
                    @endcan

                    @if(auth()->user()->isSuperAdmin())
                    <div class="mt-3">
                        <a href="{{ route('kelola-akun') }}" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 {{ request()->routeIs('kelola-akun') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            Kelola Akun
                        </a>
                    </div>
                    @endif

                    @endif
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 dark:border-gray-800 px-4 py-3">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        Keluar
                    </button>
                </form>
            </aside>

            <div class="flex flex-1 flex-col overflow-hidden">
                <header class="flex h-16 items-center justify-between border-b border-gray-100 dark:border-gray-800 bg-white/80 dark:bg-gray-950/80 backdrop-blur-lg px-4 lg:px-6 sticky top-0 z-30">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="rounded-xl p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden transition-colors shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </button>

                        @stack('topbar-left')
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @stack('topbar-right')

                        <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-xl px-1.5 py-1.5 bg-white dark:bg-gray-800 shadow-sm">
                            <button @click="toggleTheme()" x-data="themeToggle()" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all">
                                <svg x-show="!isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                                <svg x-show="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                            </button>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2.5 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-xs shadow-sm shrink-0 overflow-hidden">
                                    @php $fotoUrl = Auth::user()->employee?->foto_url; @endphp
                                    @if($fotoUrl)
                                        <img src="{{ $fotoUrl }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 leading-tight">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 leading-tight">{{ Auth::user()->employee?->position ?? Auth::user()->email }}</p>
                                </div>
                                <svg class="w-3 h-3 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-2"
                                 @click="open = false" class="absolute right-0 top-full mt-1.5 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1.5 z-50">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Pengaturan
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-4 lg:p-8">
                    <div class="w-full space-y-6 animate-fade-in">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        {{-- Floating Toast Container (top-right) --}}
        <div x-data class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none" style="max-height: calc(100vh - 2rem); overflow-y: auto;">
            <div class="pointer-events-auto space-y-3">
                @include('components.toast')
            </div>
        </div>

        {{-- Success Modal (centered) --}}
        <div x-data
             x-show="$store.successModal.open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
             @click="$store.successModal.hide()">
            <div x-show="$store.successModal.open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Berhasil!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="$store.successModal.message"></p>
                <div class="flex items-center justify-center pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button @click="$store.successModal.hide()" class="btn-primary text-xs px-8">Tutup</button>
                </div>
            </div>
        </div>

        {{-- Confirm Delete Modal (global, non-Livewire) --}}
        <div x-data
             x-show="$store.confirmModal.open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
             @click="$store.confirmModal.cancel()">
            <div x-show="$store.confirmModal.open"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.stop
                 class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2" x-text="$store.confirmModal.title"></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="$store.confirmModal.message"></p>
                <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button @click="$store.confirmModal.cancel()" class="btn-secondary text-xs px-6">Batal</button>
                    <button @click="$store.confirmModal.confirm()" class="btn-danger text-xs px-6 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        {{-- Idle timeout form (RTO): auto logout after inactivity --}}
        <form id="idle-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>

        @livewireScripts
        @stack('scripts')
        <script>
            function idleTimeout() {
                const TIMEOUT_MS = 15 * 60 * 1000;
                const idleForm = document.getElementById('idle-logout-form');
                if (!idleForm) return;

                let timer;
                const reset = () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => idleForm.submit(), TIMEOUT_MS);
                };

                ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'wheel', 'click'].forEach(evt => {
                    window.addEventListener(evt, reset, { passive: true });
                });

                reset();
            }
            document.addEventListener('DOMContentLoaded', idleTimeout);
        </script>
        <script>
            function themeToggle() {
                return {
                    isDark: document.documentElement.classList.contains('dark'),
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        document.documentElement.classList.toggle('dark', this.isDark);
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    }
                }
            }

            function logoAnimation() {
                return {
                    glowIntensity: 0.2,
                    flipAngle: 0,
                    sweepX: -150,
                    init() {
                        this.startSweep();
                        this.startFlipCycle();
                    },
                    startSweep() {
                        setInterval(() => {
                            this.sweepX = 150;
                            this.$nextTick(() => {
                                setTimeout(() => { this.sweepX = -150; }, 100);
                            });
                        }, 4000);
                    },
                    startFlipCycle() {
                        setInterval(() => {
                            this.flipAngle = 360;
                            this.$nextTick(() => {
                                setTimeout(() => { this.flipAngle = 0; }, 800);
                            });
                        }, 6000);
                    }
                }
            }

            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', ({ type, message }) => {
                    if (type === 'success') {
                        Alpine.store('successModal').show(message);
                    } else {
                        Alpine.store('toast').add(type, message);
                    }
                });
            });

            @if (session('success'))
            (function(){function w(){if(typeof Alpine!=='undefined'&&Alpine.store('successModal')){Alpine.store('successModal').show('{{ addslashes(session('success')) }}');}else{setTimeout(w,50)}}w()})();
            @endif
            @if (session('error'))
            (function(){function w(){if(typeof Alpine!=='undefined'&&Alpine.store('toast')){Alpine.store('toast').error('{{ addslashes(session('error')) }}');}else{setTimeout(w,50)}}w()})();
            @endif
            @if (session('warning'))
            (function(){function w(){if(typeof Alpine!=='undefined'&&Alpine.store('toast')){Alpine.store('toast').warning('{{ addslashes(session('warning')) }}');}else{setTimeout(w,50)}}w()})();
            @endif
        </script>
    </body>
</html>





