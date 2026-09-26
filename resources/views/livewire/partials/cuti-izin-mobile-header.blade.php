@php
    $currentUser = auth()->user();
    $announcementAdmins = [...\App\Models\User::PENGUMUMAN_ADMIN_ROLES, \App\Models\User::ROLE_GM_CEO];
    $mobileAnnouncementRoute = in_array($currentUser->role, $announcementAdmins, true)
        ? route('hris.announcements')
        : (in_array($currentUser->role, \App\Models\User::PENGUMUMAN_VIEWER_ROLES, true)
            ? route('hris.pengumuman-saya')
            : null);
@endphp

<header class="md:hidden mb-6 flex items-center gap-3" aria-label="Cuti dan izin">
    <a href="{{ route('dashboard') }}" aria-label="Kembali ke dashboard"
       class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7M8 12h13"/></svg>
    </a>
    <div class="min-w-0 flex-1">
        <h1 class="truncate text-lg font-bold text-gray-900 dark:text-gray-100">Cuti &amp; Izin</h1>
        <p class="text-xs leading-5 text-gray-500 dark:text-gray-400">Kelola pengajuan cuti dan izin karyawan</p>
    </div>
    @if($mobileAnnouncementRoute)
        <a href="{{ $mobileAnnouncementRoute }}" aria-label="Buka notifikasi dan pengumuman"
           class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.31 6.022 23.848 23.848 0 005.454 1.31m5.713 0a24.255 24.255 0 01-5.713 0m5.713 0a3 3 0 11-5.713 0M12 3v1.5"/></svg>
        </a>
    @endif
</header>
