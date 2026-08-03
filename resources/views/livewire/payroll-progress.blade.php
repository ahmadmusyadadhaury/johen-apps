<div wire:poll.3s="poll">
    @if($showProgress)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-100 to-violet-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mengirim Slip Gaji</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mohon tunggu, jangan tutup halaman ini</p>

                    <div class="mt-6 mb-2">
                        <span class="text-5xl font-extrabold text-primary-600">{{ $percent }}%</span>
                    </div>
                    <div class="text-[10px] text-gray-300 dark:text-gray-600">last poll: {{ $lastPoll }}</div>

                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-primary-500 to-violet-500 h-3 rounded-full shadow-sm shadow-primary-200"
                             style="width: {{ $percent }}%"></div>
                    </div>

                    <div class="flex items-center justify-center gap-6 mt-4">
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $sent }} Terkirim
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-700">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            {{ $pending }} Pending
                        </span>
                        @if($failed > 0)
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-700">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                {{ $failed }} Gagal
                            </span>
                        @endif
                    </div>
                    <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">({{ $sent + $failed }} dari {{ $total }} karyawan)</p>
                </div>
            </div>
        </div>
    @endif

    @if($showDone)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl">
                <div class="text-center">
                    @if($failed === 0)
                        <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-100 to-green-100 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Semua Slip Terkirim!</h3>
                    @else
                        <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Proses Selesai</h3>
                    @endif
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Periode {{ $import->periode }}</p>

                    <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total Karyawan</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $total }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Berhasil Terkirim</span>
                            <span class="font-semibold text-emerald-600">{{ $sent }}</span>
                        </div>
                        @if($failed > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Gagal</span>
                                <span class="font-semibold text-red-600">{{ $failed }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-center gap-3">
                        <a href="{{ route('payroll.show', $import) }}" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            Lihat Detail
                        </a>
                        <button wire:click="close" class="btn-secondary">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>


