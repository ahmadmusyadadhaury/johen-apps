@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Preview Data Payroll</h1>
        <p class="text-xs text-gray-400 mt-0.5">Periksa data sebelum melanjutkan ke generate slip gaji</p>
    </div>
@endpush

<x-app-layout title="Preview Payroll">


    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-center w-full overflow-x-auto pb-1">
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 text-white text-sm font-bold shadow-lg shadow-primary-200">1</div>
                <span class="text-[10px] font-semibold text-gray-900 dark:text-gray-100">Upload</span>
            </div>
            <div class="flex-1 flex items-center mx-2"><div class="w-full h-1 rounded-full bg-gradient-to-r from-primary-600 to-violet-600"></div></div>
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-primary-600 to-violet-600 text-white text-sm font-bold shadow-lg shadow-primary-200">2</div>
                <span class="text-[10px] font-semibold text-primary-600">Validasi</span>
            </div>
            <div class="flex-1 flex items-center mx-2"><div class="w-full h-1 rounded-full bg-gray-200"></div></div>
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 text-gray-400 dark:text-gray-500 text-sm font-bold">3</div>
                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Generate PDF</span>
            </div>
            <div class="flex-1 flex items-center mx-2"><div class="w-full h-1 rounded-full bg-gray-200"></div></div>
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 text-gray-400 dark:text-gray-500 text-sm font-bold">4</div>
                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Slip Tersedia</span>
            </div>
            <div class="flex-1 flex items-center mx-2"><div class="w-full h-1 rounded-full bg-gray-200"></div></div>
            <div class="flex flex-col items-center gap-1 shrink-0">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 text-gray-400 dark:text-gray-500 text-sm font-bold">5</div>
                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Selesai</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Karyawan</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $import->total_employee }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Payroll</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalPayroll, 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $import->periode }}</p>
        </div>
    </div>

    @livewire('payroll-preview-table', ['import' => $import])

    <div class="flex items-center justify-between pt-2">
        <div>
            <a href="{{ route('payroll.upload') }}" class="btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                Upload Ulang
            </a>
        </div>
        <div x-data="generateFlow()">
            <button @click="open = true" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate Slip Gaji
            </button>

            <div x-show="processing" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
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
                            <span class="text-5xl font-extrabold text-primary-600" x-text="percent + '%'">0%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-500 to-violet-500 h-3 rounded-full transition-all duration-700 ease-out shadow-sm shadow-primary-200"
                                 x-bind:style="'width: ' + percent + '%'"></div>
                        </div>

                        <div class="flex items-center justify-center gap-6 mt-4">
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span x-text="sent + ' Terkirim'">0 Terkirim</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-700" x-show="pending > 0">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span x-text="pending + ' Pending'">0 Pending</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-700" x-show="failed > 0">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span x-text="failed + ' Gagal'">0 Gagal</span>
                            </span>
                        </div>
                        <p class="mt-4 text-xs text-gray-400 dark:text-gray-500" x-text="`(${sent + failed} dari ${total} karyawan)`"></p>
                    </div>
                </div>
            </div>

            <div x-show="open && !processing" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="open && !processing" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="open = false"></div>
                <div x-show="open && !processing" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition-all ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl">
                    <div class="text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-100 to-violet-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Konfirmasi Generate</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Anda akan membuat <strong class="text-gray-900 dark:text-gray-100">{{ $import->total_employee }} slip gaji</strong> untuk periode <strong class="text-gray-900 dark:text-gray-100">{{ $import->periode }}</strong>. Slip gaji akan tersedia di menu Riwayat Payroll masing-masing karyawan.</p>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button @click="open = false" class="btn-secondary">Batal</button>
                        <button @click="start()" class="btn-primary" :disabled="processing">
                            Generate Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generateFlow() {
            return {
                open: false,
                processing: false,
                total: {{ $import->total_employee }},
                sent: 0,
                failed: 0,
                pending: {{ $import->total_employee }},
                percent: 0,
                async start() {
                    this.open = false;
                    this.processing = true;
                    await this.processUntilDone();
                },
                async processUntilDone() {
                    while (true) {
                        let d;
                        try {
                            const resp = await fetch('{{ route('payroll.process-batch', $import) }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                },
                            });
                            d = await resp.json();
                        } catch (e) {
                            await new Promise(r => setTimeout(r, 1500));
                            continue;
                        }
                        this.sent = d.sent;
                        this.failed = d.failed;
                        this.pending = d.pending;
                        this.percent = d.percent;
                        if (d.allDone) {
                            window.location.href = '{{ route('history.index') }}';
                            return;
                        }
                        await new Promise(r => setTimeout(r, 600));
                    }
                }
            }
        }
    </script>

</x-app-layout>


