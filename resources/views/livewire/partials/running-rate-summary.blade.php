{{-- Summary cards --}}
<div class="grid grid-cols-12 gap-5 mt-2">
    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <span class="badge-info">hari</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $summary['remaining_working_days'] }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sisa Hari Kerja</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200 dark:shadow-primary-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="badge-primary">periode</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['total_target']) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Target</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </div>
            <span class="badge-success">terjual</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['total_sold']) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sold</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-200 dark:shadow-violet-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/></svg>
            </div>
            <span class="badge-secondary">tim</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['achievement'], 2) }}%</p>
        <div class="mt-2.5 h-1.5 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
            <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-purple-500 transition-all duration-500" style="width: {{ min($summary['achievement'], 100) }}%"></div>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">Achievement Tim</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
            </div>
            <span class="badge-warning">sisa</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['remaining']) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Remaining Target</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white shadow-lg shadow-teal-200 dark:shadow-teal-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
            </div>
            <span class="badge-info">sold/hari</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['rr_daily'], 2) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">RR Harian Tim</p>
    </div>

    <div class="stat-card group col-span-12 sm:col-span-6 lg:col-span-4 xl:col-span-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-200 dark:shadow-rose-900/30 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
            </div>
            <span class="badge-warning">sold/minggu</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($summary['rr_weekly'], 2) }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">RR Mingguan Tim</p>
    </div>
</div>