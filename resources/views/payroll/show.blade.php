<x-app-layout title="Detail Payroll">

    <div x-data="progressModal()" x-init="init()" class="contents">
        {{-- Progress Modal --}}
        <template x-teleport="body">
            <div x-show="processing && !showDoneModal"
                 x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                <div x-show="processing && !showDoneModal"
                     x-transition:enter="transition-all ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl">
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
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span x-text="pending + ' Pending'" x-show="pending > 0">0 Pending</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-700" x-show="failed > 0">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span x-text="failed + ' Gagal'">0 Gagal</span>
                            </span>
                        </div>
                        <p class="mt-4 text-xs text-gray-400 dark:text-gray-500" x-text="`(${sent + failed} dari ${total} karyawan)`">(0 dari 0 karyawan)</p>
                    </div>
                </div>
            </div>
        </template>

        {{-- Done Modal --}}
        <template x-teleport="body">
            <div x-show="showDoneModal"
                 x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                <div x-show="showDoneModal"
                     x-transition:enter="transition-all ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl">
                    <div class="text-center">
                        <template x-if="failed === 0">
                            <div>
                                <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-100 to-green-100 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Semua Slip Terkirim!</h3>
                            </div>
                        </template>
                        <template x-if="failed > 0">
                            <div>
                                <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Proses Selesai</h3>
                            </div>
                        </template>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Periode {{ $import->periode }}</p>

                        <div class="mt-6 bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-2 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Karyawan</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="total"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Berhasil Terkirim</span>
                                <span class="font-semibold text-emerald-600" x-text="sent"></span>
                            </div>
                            <div class="flex items-center justify-between" x-show="failed > 0">
                                <span class="text-gray-600 dark:text-gray-400">Gagal</span>
                                <span class="font-semibold text-red-600" x-text="failed"></span>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-center gap-3">
                            <button @click="window.location.reload()" class="btn-secondary">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function progressModal() {
            return {
                total: {{ $progress['total'] }},
                sent: {{ $progress['sent'] }},
                failed: {{ $progress['failed'] }},
                pending: {{ $progress['pending'] }},
                percent: {{ $progress['percent'] }},
                allDone: {{ $progress['allDone'] ? 'true' : 'false' }},
                processing: {{ session('processing') ? 'true' : 'false' }},
                showDoneModal: false,
                interval: null,
                init() {
                    if (this.allDone && this.processing) {
                        this.showDoneModal = true;
                        this.processing = false;
                        return;
                    }
                    if (!this.processing) return;
                    this.interval = setInterval(() => { this.fetchProgress(); }, 3000);
                },
                fetchProgress() {
                    fetch('{{ route('payroll.progress-json', $import) }}')
                        .then(r => r.json())
                        .then(d => {
                            this.total = d.total;
                            this.sent = d.sent;
                            this.failed = d.failed;
                            this.pending = d.pending;
                            this.percent = d.percent;
                            if (d.allDone) {
                                this.processing = false;
                                this.showDoneModal = true;
                                clearInterval(this.interval);
                            }
                        });
                }
            }
        }
    </script>

    @push('topbar-left')
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Detail Payroll</h1>
            <p class="text-xs text-gray-400 mt-0.5">Periode {{ $import->periode }}</p>
        </div>
    @endpush
    @push('topbar-right')
        <a href="{{ route('history.index') }}" class="btn-ghost">Kembali</a>
    @endpush

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Karyawan</p>
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
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($import->total_payroll, 0, ',', '.') }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">File</p>
            </div>
            <p class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $import->file_name }}</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-3 mb-2">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-cyan-500 to-blue-500 text-white shadow-lg shadow-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Upload oleh</p>
            </div>
            <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $import->uploadedBy?->name ?? 'Unknown' }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Data Karyawan</h2>
            </div>
            <span class="badge-info">{{ $import->payrollDetails->count() }} karyawan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3">NIK</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">THP</th>
                        <th class="px-6 py-3">PDF</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                    @foreach($import->payrollDetails as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 transition-colors">
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $detail->nik }}</td>
                            <td class="table-cell text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $detail->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $detail->email }}</td>
                            <td class="table-cell whitespace-nowrap">
                                @if($detail->status === 'sent')
                                    <span class="badge-success">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terkirim
                                    </span>
                                @elseif($detail->status === 'failed')
                                    <span class="badge-danger">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Gagal
                                    </span>
                                @else
                                    <span class="badge-warning">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="table-cell text-right font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">Rp {{ number_format($detail->take_home_pay, 0, ',', '.') }}</td>
                            <td class="table-cell whitespace-nowrap">
                                @if($detail->pdf_path)
                                    <a href="{{ route('payroll.download-pdf', $detail) }}" class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        Download
                                    </a>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                @if($detail->status === 'failed')
                                    <form action="{{ route('payroll.retry-failed', $detail) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                                            Generate Ulang
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>


