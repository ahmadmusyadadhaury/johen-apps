@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Bonus &amp; Insentif</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola data bonus dan insentif karyawan</p>
    </div>
@endpush

<x-app-layout title="Bonus & Insentif">

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z"/></svg>
                </div>
                <span class="badge-info">Host Live</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['host_live']) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Data Host Live</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                </div>
                <span class="badge-success">Transaksi</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['admin_transaksi']) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Data Admin Transaksi</p>
        </div>

        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
                </div>
                <span class="badge-warning">Creative</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['creative']) }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Data Creative</p>
        </div>
    </div>

    <div x-data="{ tab: 'host-live' }" class="card overflow-hidden">
        <div class="flex border-b border-gray-100 dark:border-gray-800 px-6">
            <button @click="tab = 'host-live'" :class="tab === 'host-live' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-4 py-3 text-sm font-medium transition-colors">
                Host Live
            </button>
            <button @click="tab = 'admin-transaksi'" :class="tab === 'admin-transaksi' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-4 py-3 text-sm font-medium transition-colors">
                Admin Transaksi
            </button>
            <button @click="tab = 'creative'" :class="tab === 'creative' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-4 py-3 text-sm font-medium transition-colors">
                Divisi Creative
            </button>
        </div>

        <div x-show="tab === 'host-live'">
            @livewire('bonus-host-live-table')
        </div>

        <div x-show="tab === 'admin-transaksi'" x-cloak>
            @livewire('bonus-admin-transaction-table')
        </div>

        <div x-show="tab === 'creative'" x-cloak>
            @livewire('bonus-creative-table')
        </div>
    </div>

</x-app-layout>
