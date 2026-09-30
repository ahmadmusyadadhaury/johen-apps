<div data-influencer-motion>
    @php
        $canEditInfluencers = !auth()->user()->isReadOnlyWorkspace() && !auth()->user()->isHeadOfStore();
        $canCreateInfluencers = $canEditInfluencers && !auth()->user()->isKoordinatorCreative();
        $canManageInfluencerPayments = $canEditInfluencers && (auth()->user()->canSeeBiaya() || auth()->user()->isKoordinatorCreative());
        $isCoordinatorCreative = auth()->user()->isKoordinatorCreative();
    @endphp

    @if(session('message'))
    <div class="influencer-feedback-enter mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

    @if($showRequestTabs)
    <div class="mb-5 grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1 dark:bg-gray-800 sm:inline-flex" role="tablist" aria-label="Menu influencer">
        <button type="button" role="tab" aria-controls="influencer-monitoring-panel" aria-selected="{{ $activeTab === 'monitoring' ? 'true' : 'false' }}" wire:click="switchTab('monitoring')"
                class="flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:px-4 sm:text-sm {{ $activeTab === 'monitoring' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-gray-100' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            Monitoring Influencer
        </button>
        <button type="button" role="tab" aria-controls="influencer-submission-panel" aria-selected="{{ $activeTab === 'pengajuan' ? 'true' : 'false' }}" wire:click="switchTab('pengajuan')"
                class="flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-xs font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 sm:px-4 sm:text-sm {{ $activeTab === 'pengajuan' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-gray-100' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            Pengajuan Influencer
            @if($pendingActionCount > 0)
            <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold leading-none text-white shadow-sm" aria-label="{{ $pendingActionCount }} pengajuan menunggu persetujuan">{{ $pendingActionCount > 99 ? '99+' : $pendingActionCount }}</span>
            @endif
        </button>
    </div>
    @endif

    @if(!$showRequestTabs || $activeTab === 'monitoring' || ($isKolSubmitter && $showModal))
    <section id="influencer-monitoring-panel" role="tabpanel" aria-label="Monitoring Influencer" class="influencer-feedback-enter">

    @if($isKolSubmitter || $isCoordinatorCreative)
    <div class="card">
        <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Monitoring Influencer</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $isKolSubmitter ? 'Daftar pengajuan Anda yang sudah disetujui dan masuk tahap monitoring.' : 'Daftar influencer yang sudah masuk tahap monitoring.' }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead><tr class="table-header"><th class="px-5 py-3 text-center">No</th><th class="px-5 py-3">Nama Influencer</th><th class="px-5 py-3">Divisi</th><th class="px-5 py-3">Kontrak Mulai</th><th class="px-5 py-3">Kontrak Selesai</th><th class="px-5 py-3">Biaya</th><th class="px-5 py-3">Link Sosmed</th><th class="px-5 py-3 text-center">Aksi</th></tr></thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @if($isKolSubmitter)
                    @forelse($kolApprovedSubmissions as $submission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="table-cell text-center text-gray-500">{{ $kolApprovedSubmissions->firstItem() + $loop->index }}</td>
                        <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $submission->nama }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $submission->divisi ?: '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $submission->influencer?->mulai_kontrak?->isoFormat('D MMM YYYY') ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $submission->influencer?->habis_kontrak?->isoFormat('D MMM YYYY') ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $submission->biaya ? 'Rp '.number_format($submission->biaya, 0, ',', '.') : '-' }}</td>
                        <td class="table-cell">@if($submission->link_sosmed)<a href="{{ $submission->link_sosmed }}" target="_blank" rel="noopener noreferrer" class="break-all text-primary-600 hover:underline dark:text-primary-400">{{ $submission->link_sosmed }}</a>@else<span class="text-gray-400">-</span>@endif</td>
                        <td class="table-cell text-center"><button type="button" wire:click="openMonitoring({{ $submission->influencer_id }})" class="inline-flex min-h-10 items-center rounded-lg bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-700">Monitoring</button></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada pengajuan yang disetujui. Pengajuan akan muncul di sini setelah melewati seluruh tahap persetujuan.</td></tr>
                    @endforelse
                    @else
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="table-cell text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                        <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $item->nama }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->divisi ?: '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->mulai_kontrak?->isoFormat('D MMM YYYY') ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->habis_kontrak?->isoFormat('D MMM YYYY') ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->biaya ? 'Rp '.number_format($item->biaya, 0, ',', '.') : '-' }}</td>
                        <td class="table-cell">@if($item->link_sosmed)<a href="{{ $item->link_sosmed }}" target="_blank" rel="noopener noreferrer" class="break-all text-primary-600 hover:underline dark:text-primary-400">{{ $item->link_sosmed }}</a>@else<span class="text-gray-400">-</span>@endif</td>
                        <td class="table-cell text-center"><button type="button" wire:click="openMonitoring({{ $item->id }})" class="inline-flex min-h-10 items-center rounded-lg bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-700">Monitoring</button></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada data influencer.</td></tr>
                    @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        @if($isKolSubmitter && $kolApprovedSubmissions->hasPages())<div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $kolApprovedSubmissions->links() }}</div>@elseif($isCoordinatorCreative && $items->hasPages())<div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800">{{ $items->links() }}</div>@endif
    </div>
    @else

    @if($upcomingPayments->count() > 0)
    <div class="mb-5 rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 px-5 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-rose-700 dark:text-rose-400">{{ $upcomingPayments->count() }} pembayaran akan jatuh tempo dalam 7 hari</p>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach($upcomingPayments as $up)
                    <span class="text-xs text-rose-600 dark:text-rose-500 bg-rose-100 dark:bg-rose-900/30 px-2 py-0.5 rounded-full">{{ $up->influencer->nama }} · Rp {{ number_format($up->jumlah, 0, ',', '.') }} · {{ $up->tanggal_jatuh_tempo->isoFormat('D MMM') }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4" aria-label="Ringkasan kontrak influencer">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $aktifCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Aktif</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">Akan Berakhir</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $segeraHabisCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Segera Habis</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-rose-500 text-white shadow-lg shadow-red-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-danger">Tidak Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $tidakAktifCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kontrak Tidak Aktif</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125V9M7.5 12h9M12 15h-1.5m0 0H9m1.5 0V9m-6 3h6m-6 3h6m-3-6h.008v.008H12V12z"/></svg>
                </div>
                <span class="badge-{{ $upcomingPayments->count() > 0 ? 'warning' : 'success' }}">{{ $upcomingPayments->count() }}</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $upcomingPayments->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pembayaran H-7</p>
        </div>
    </div>

    <div class="card">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Data Influencer</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar influencer creative</p>
            </div>
            @if($canCreateInfluencers)
            <button wire:click="openNew" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Data
            </button>
            @endif
        </div>

        <div class="space-y-3 p-3 sm:hidden" role="list" aria-label="Daftar influencer">
            @forelse($items as $item)
                @php
                    $daysRemaining = now()->startOfDay()->diffInDays($item->habis_kontrak, false);
                    $totalPayments = $item->payments->count();
                    $paidPayments = $item->payments->where('status', 'lunas')->count();
                    $paymentPercent = $totalPayments > 0 ? round($paidPayments / $totalPayments * 100) : 0;
                    $overduePayments = $item->payments->where('status', 'pending')->where('tanggal_jatuh_tempo', '<', now())->count();
                @endphp
                <article role="listitem" class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="break-words text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->nama }}</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No. Kontrak: {{ $item->no_kontrak ?: '-' }}</p>
                        </div>
                        @if($daysRemaining <= 0)
                            <span class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">Habis</span>
                        @elseif($daysRemaining <= 7)
                            <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Segera habis</span>
                        @else
                            <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Aktif</span>
                        @endif
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-3 border-t border-gray-100 pt-3 text-xs dark:border-gray-800">
                        <div><dt class="text-gray-500 dark:text-gray-400">Divisi</dt><dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $item->divisi ?: '-' }}</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Mulai kontrak</dt><dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $item->mulai_kontrak->isoFormat('D MMM YYYY') }}</dd></div>
                        <div><dt class="text-gray-500 dark:text-gray-400">Habis kontrak</dt><dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $item->habis_kontrak->isoFormat('D MMM YYYY') }}</dd></div>
                        <div class="col-span-2"><dt class="text-gray-500 dark:text-gray-400">Keterangan</dt><dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $item->keterangan ?: '-' }}</dd></div>
                        @if(auth()->user()->canSeeBiaya())
                        <div><dt class="text-gray-500 dark:text-gray-400">Biaya</dt><dd class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $item->biaya ? 'Rp '.number_format($item->biaya, 0, ',', '.') : '-' }}</dd></div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Pembayaran</dt>
                            <dd class="mt-1 flex items-center gap-2">
                                <span class="font-medium {{ $overduePayments > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-200' }}">{{ $paidPayments }}/{{ $totalPayments }} lunas{{ $overduePayments > 0 ? ', '.$overduePayments.' terlambat' : '' }}</span>
                                <span class="sr-only">{{ $paymentPercent }} persen</span>
                            </dd>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700" role="progressbar" aria-label="Pembayaran lunas" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $paymentPercent }}"><div class="h-full rounded-full {{ $paymentPercent === 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $paymentPercent }}%"></div></div>
                        </div>
                        @endif
                        <div class="col-span-2 min-w-0"><dt class="text-gray-500 dark:text-gray-400">Link media sosial</dt><dd class="mt-1">@if($item->link_sosmed)<a href="{{ $item->link_sosmed }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 max-w-full items-center gap-1 break-all font-medium text-primary-600 underline decoration-primary-300 underline-offset-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:text-primary-400" aria-label="Buka media sosial {{ $item->nama }} di tab baru">{{ $item->link_sosmed }}</a>@else<span class="text-gray-500 dark:text-gray-400">Belum ada</span>@endif</dd></div>
                    </dl>

                    @if($canEditInfluencers || $canViewMonitoring)
                    <div class="mt-3 flex flex-wrap gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                        @if($canViewMonitoring)<button type="button" wire:click="openMonitoring({{ $item->id }})" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">Monitoring influencer</button>@endif
                        @if($canEditInfluencers)
                        <button type="button" wire:click="openEdit({{ $item->id }})" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-primary-200 px-3 py-2 text-xs font-semibold text-primary-700 hover:bg-primary-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-primary-800 dark:text-primary-300 dark:hover:bg-primary-900/20">Edit</button>
                        <button type="button" wire:click="delete({{ $item->id }})" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:border-red-900 dark:text-red-400 dark:hover:bg-red-900/20">Hapus</button>
                        @endif
                        @if($canManageInfluencerPayments)
                        <button type="button" wire:click="openPaymentModal({{ $item->id }})" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-violet-600 px-3 py-2 text-xs font-semibold text-white hover:bg-violet-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2">Kelola pembayaran</button>
                        @endif
                    </div>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">Belum ada data influencer.</div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 text-center w-12">No</th>
                        <th class="px-6 py-3">No. Kontrak</th>
                        <th class="px-6 py-3">Nama Influencer</th>
                        <th class="px-6 py-3">Divisi</th>
                        <th class="px-6 py-3">Keterangan</th>
                        <th class="px-6 py-3">Mulai Kontrak</th>
                        <th class="px-6 py-3">Habis Kontrak</th>
                        <th class="px-6 py-3">Status</th>
                        @if(auth()->user()->canSeeBiaya())
                        <th class="px-6 py-3">Biaya</th>
                        <th class="px-6 py-3">Pembayaran</th>
                        @endif
                        <th class="px-6 py-3">Link Sosmed</th>
                        <th class="px-6 py-3 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="table-cell text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $item->no_kontrak ?: '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->divisi ?: '-' }}</td>
                            <td class="table-cell max-w-xs text-gray-600 dark:text-gray-400">{{ $item->keterangan ?: '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->mulai_kontrak->isoFormat('D MMMM YYYY') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->habis_kontrak->isoFormat('D MMMM YYYY') }}</td>
                            <td class="table-cell">
                                @php
                                    $sisa = now()->startOfDay()->diffInDays($item->habis_kontrak, false);
                                @endphp
                                @if($sisa <= 0)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Habis</span>
                                @elseif($sisa <= 7)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Segera Habis</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Aktif</span>
                                @endif
                            </td>
                            @if(auth()->user()->canSeeBiaya())
                            @php
                                $totalPay = $item->payments->count();
                                $paidPay = $item->payments->where('status', 'lunas')->count();
                                $pct = $totalPay > 0 ? round($paidPay / $totalPay * 100) : 0;
                                $overdue = $item->payments->where('status', 'pending')->where('tanggal_jatuh_tempo', '<', now())->count();
                            @endphp
                            <td class="table-cell text-right text-gray-600 dark:text-gray-400">@if($item->biaya)Rp {{ number_format($item->biaya, 0, ',', '.') }}@else<span class="text-gray-400">-</span>@endif</td>
                            <td class="table-cell min-w-[140px]">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $paidPay === $totalPay && $totalPay > 0 ? 'bg-emerald-500' : ($pct > 0 ? 'bg-amber-500' : 'bg-gray-300 dark:bg-gray-600') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium whitespace-nowrap {{ $overdue > 0 ? 'text-red-600 dark:text-red-400' : ($paidPay === $totalPay && $totalPay > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400') }}">
                                        {{ $paidPay }}/{{ $totalPay }}
                                        @if($overdue > 0)<span class="ml-0.5 inline-block w-1.5 h-1.5 rounded-full bg-red-500"></span>@endif
                                    </span>
                                </div>
                            </td>
                            @endif
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                @if($item->link_sosmed)
                                <a href="{{ $item->link_sosmed }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                                    {{ $item->link_sosmed }}
                                </a>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($canViewMonitoring)
                                    <button wire:click="openMonitoring({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-900/30 transition-colors">Monitoring</button>
                                    @endif
                                    @if($canEditInfluencers)
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                    @endif
                                    @if($canManageInfluencerPayments)
                                    <button wire:click="openPaymentModal({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125V9M7.5 12h9M12 15h-1.5m0 0H9m1.5 0V9m-6 3h6m-6 3h6m-3-6h.008v.008H12V12z"/></svg>
                                        Bayar
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canSeeBiaya() ? 12 : 10 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <p class="font-medium">Belum ada data influencer</p>
                                    <p class="text-xs mt-1">Klik "Tambah Data" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-gray-50 dark:border-gray-800">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    @endif

    {{-- Modal --}}
    @if($showModal)
    @teleport('body')
    <div data-influencer-motion class="influencer-modal-backdrop fixed inset-0 z-[10000] flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center sm:p-5">
        <div role="dialog" aria-modal="true" aria-labelledby="influencer-form-title" class="influencer-modal-panel isolate relative my-auto max-h-[calc(100dvh-1.5rem)] w-full max-w-lg overflow-y-auto rounded-2xl border border-gray-200 bg-white p-4 shadow-[0_24px_80px_-20px_rgba(15,23,42,0.55)] dark:border-gray-700 dark:bg-gray-900 sm:max-h-[calc(100dvh-2.5rem)] sm:p-7">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 id="influencer-form-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit Influencer' : 'Ajukan Influencer' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui data influencer' : 'Isi data influencer baru' }}</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Nama Influencer *" />
                    <x-text-input type="text" wire:model="nama" class="mt-1 block w-full" placeholder="Nama influencer" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Divisi *" />
                    <select wire:model="divisi" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">Pilih divisi</option>
                        @foreach(\App\Livewire\InfluencerTable::DIVISI_OPTIONS as $divisionOption)
                        <option value="{{ $divisionOption }}">{{ $divisionOption }}</option>
                        @endforeach
                    </select>
                    @error('divisi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Rekomendasi Lama Kontrak *" />
                    <div class="mt-1 flex items-center gap-2">
                        <x-text-input type="number" min="1" max="60" step="1" wire:model="rekomendasiLamaKontrak" class="block w-full" placeholder="Contoh: 6" />
                        <span class="shrink-0 text-sm text-gray-500 dark:text-gray-400">bulan</span>
                    </div>
                    @error('rekomendasiLamaKontrak') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                @if(auth()->user()->canSeeBiaya())
                <div>
                    <x-input-label value="Biaya (per bulan)" />
                    <x-text-input type="number" step="0.01" min="0" wire:model="biaya" class="mt-1 block w-full" placeholder="0" />
                    @error('biaya') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <x-input-label value="Keterangan" />
                    <textarea wire:model="keterangan" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" placeholder="Keterangan tambahan..."></textarea>
                    @error('keterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Link Sosmed" />
                    <x-text-input type="url" wire:model="link_sosmed" class="mt-1 block w-full" placeholder="https://instagram.com/..." />
                    @error('link_sosmed') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-primary text-xs disabled:opacity-60">
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Ajukan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endteleport
    @endif

    {{-- Monitoring Influencer Modal --}}
    @if($showMonitoringModal)
    @php $monitoringInfluencer = \App\Models\Influencer::find($monitoringInfluencerId); @endphp
    @if($isKolSubmitter)
    <style>body:has(.kol-monitoring-modal-backdrop) { overflow: hidden !important; }</style>
    @endif
    @teleport('body')
    <div class="influencer-modal-backdrop fixed inset-0 z-[2147483000] isolate flex justify-center overscroll-contain bg-gray-900/60 p-2 backdrop-blur-sm {{ $isKolSubmitter ? 'kol-monitoring-modal-backdrop items-center overflow-hidden' : 'items-start overflow-y-auto sm:items-center sm:p-4' }}" style="position: fixed; inset: 0;" wire:keydown.escape="closeMonitoring">
        <section role="dialog" aria-modal="true" aria-labelledby="influencer-monitoring-title" style="{{ $isKolSubmitter ? 'max-height: calc(100vh - 1rem); max-height: calc(100dvh - 1rem);' : 'max-height: calc(100vh - 2rem); max-height: calc(100dvh - 2rem);' }}" class="influencer-modal-panel {{ $isKolSubmitter ? 'my-0 flex min-h-0 flex-col overflow-hidden' : 'my-2 overflow-y-auto sm:my-4' }} w-full max-w-4xl overscroll-contain rounded-2xl bg-white p-3 shadow-2xl dark:bg-gray-800 sm:p-5">
            <div class="flex {{ $isKolSubmitter ? 'shrink-0' : '' }} items-start justify-between gap-4 border-b border-gray-100 pb-4 dark:border-gray-700">
                <div>
                    <h3 id="influencer-monitoring-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Monitoring Influencer</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $monitoringInfluencer?->nama }} · {{ $monitoringInfluencer?->divisi }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    @if($isKolSubmitter && $monitoringModalView === 'detail')
                    <button type="button" wire:click="showMonitoringMonths" class="btn-secondary text-xs">Kembali ke daftar bulan</button>
                    @endif
                    <button type="button" wire:click="closeMonitoring" class="rounded-xl p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200" aria-label="Tutup monitoring">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            @if($isKolSubmitter && $monitoringModalView === 'months')
            @php
                $currentMonthKey = now()->format('Y-m');
                $hasCurrentMonthMonitoring = $monitoringHistory->contains(fn ($record) => $record->period_month->format('Y-m') === $currentMonthKey);
            @endphp
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden overscroll-contain pt-5">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Pilih Bulan Monitoring</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih bulan untuk melihat atau mengisi detail monitoring.</p>
                    </div>
                    <button type="button" wire:click="addMonitoringForCurrentMonth" class="btn-primary shrink-0 text-xs">Tambah Monitoring</button>
                </div>
                <div class="space-y-2">
                    @unless($hasCurrentMonthMonitoring)
                    <button type="button" wire:click="selectMonitoringMonth('{{ $currentMonthKey }}')" class="flex w-full items-center justify-between gap-3 rounded-xl border border-primary-200 bg-primary-50/60 px-4 py-3 text-left transition-colors hover:bg-primary-50 dark:border-primary-900/50 dark:bg-primary-950/20 dark:hover:bg-primary-950/40">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ now()->isoFormat('MMMM YYYY') }}</span>
                            <span class="mt-1 block text-xs text-primary-700 dark:text-primary-300">Bulan berjalan · belum diisi</span>
                        </span>
                        <span class="text-xs font-semibold text-primary-700 dark:text-primary-300">Isi monitoring</span>
                    </button>
                    @endunless
                    @forelse($monitoringHistory as $record)
                    <button type="button" wire:click="selectMonitoringMonth('{{ $record->period_month->format('Y-m') }}')" class="flex w-full items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-left transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800">
                        <span>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $record->period_month->isoFormat('MMMM YYYY') }}</span>
                            <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">{{ $record->period_month->format('Y-m') === $currentMonthKey ? 'Bulan berjalan · sudah diisi' : 'Monitoring sudah diisi' }}</span>
                        </span>
                        <span class="text-xs font-semibold text-sky-700 dark:text-sky-300">Lihat detail</span>
                    </button>
                    @empty
                    @endforelse
                </div>
            </div>
            @else
            @php
                $monitoringRows = $isKolSubmitter && $monitoringReadOnly
                    ? $monitoringHistory->filter(fn ($record) => $record->period_month->format('Y-m') === $monitoringMonth)
                    : $monitoringHistory;
                $monitoringDetailNotes = $isKolSubmitter ? $monitoringNotes : ($monitoringHistory->first()?->notes ?? '');
                $monitoringDetailBenefits = $isKolSubmitter ? $monitoringBenefits : ($monitoringHistory->first()?->benefits ?? '');
            @endphp
            <div class="{{ $isKolSubmitter ? 'min-h-0 flex-1 overflow-y-auto overflow-x-hidden overscroll-contain' : '' }}">
            <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
                <div>
                    @if($isKolSubmitter && !$monitoringReadOnly)
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Input Monitoring Bulanan</h4>
                    <form id="kol-monitoring-form" wire:submit.prevent="saveMonitoring" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <x-input-label value="Bulan Monitoring *" />
                            <x-text-input type="month" wire:model.live="monitoringMonth" class="mt-1 block w-full" />
                            @error('monitoringMonth')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-input-label value="Followers *" />
                            <x-text-input type="number" min="0" step="1" wire:model="monitoringFollowers" class="mt-1 block w-full" placeholder="Contoh: 1700000" />
                            @error('monitoringFollowers')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-input-label value="Viewers 1 Bulan Terakhir *" />
                            <x-text-input type="number" min="0" step="1" wire:model="monitoringViewers" class="mt-1 block w-full" placeholder="Contoh: 1400000" />
                            @error('monitoringViewers')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-input-label value="Durasi 1 Bulan Terakhir (jam) *" />
                            <x-text-input type="number" min="0" step="0.01" wire:model="monitoringDuration" class="mt-1 block w-full" placeholder="162" />
                            @error('monitoringDuration')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-input-label value="Target Durasi (jam) *" />
                            <x-text-input type="number" min="0" step="0.01" wire:model="monitoringTargetDuration" class="mt-1 block w-full" placeholder="130" />
                            @error('monitoringTargetDuration')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label value="Keterangan" />
                            <textarea wire:model="monitoringNotes" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" placeholder="Catatan monitoring"></textarea>
                            @error('monitoringNotes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label value="Benefit" />
                            <textarea wire:model="monitoringBenefits" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" placeholder="Masukkan benefit kerja sama influencer"></textarea>
                            @error('monitoringBenefits')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </form>
                    @endif

                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Riwayat Monitoring</h4>
                        <div class="mt-3 {{ $isKolSubmitter ? 'overflow-x-hidden' : 'overflow-x-auto' }} rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="w-full {{ $isKolSubmitter ? 'table-fixed' : '' }} text-left text-xs">
                                <thead class="bg-gray-50 text-gray-500 dark:bg-gray-900 dark:text-gray-400"><tr><th class="px-3 py-2.5">Bulan</th><th class="px-3 py-2.5">Followers</th><th class="px-3 py-2.5">Viewers</th><th class="px-3 py-2.5">Durasi</th><th class="px-3 py-2.5">Target</th></tr></thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse($monitoringRows as $record)
                                    <tr class="text-gray-700 dark:text-gray-300"><td class="px-3 py-2.5">{{ $record->period_month->isoFormat('MMM YYYY') }}</td><td class="px-3 py-2.5">{{ $this->formatAudienceCount($record->followers) }}</td><td class="px-3 py-2.5">{{ $this->formatAudienceCount($record->viewers_last_month) }}</td><td class="px-3 py-2.5">{{ number_format((float) $record->duration_hours, 0, ',', '.') }} jam</td><td class="px-3 py-2.5">{{ number_format((float) $record->target_duration_hours, 0, ',', '.') }} jam</td></tr>
                                    @empty
                                    <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">{{ $monitoringReadOnly ? 'Belum ada data monitoring pada bulan ini.' : 'Belum ada riwayat monitoring.' }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <aside class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900 sm:p-5">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Keterangan &amp; Benefit</h4>
                    @if($monitoringDetailNotes)
                    <div class="mt-4">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Keterangan</p>
                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $monitoringDetailNotes }}</p>
                    </div>
                    @endif
                    <div class="mt-4 {{ $monitoringDetailNotes ? 'border-t border-gray-200 pt-4 dark:border-gray-700' : '' }}">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Benefit</p>
                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $monitoringDetailBenefits ?: 'Belum ada benefit yang diinput.' }}</p>
                    </div>
                </aside>
            </div>
            </div>
            @if($isKolSubmitter && !$monitoringReadOnly)
            <footer class="flex shrink-0 justify-end gap-2 border-t border-gray-100 pt-3 dark:border-gray-700">
                <button type="button" wire:click="closeMonitoring" class="btn-secondary text-xs">Tutup</button>
                <button type="submit" form="kol-monitoring-form" wire:loading.attr="disabled" wire:target="saveMonitoring" class="btn-primary text-xs">Simpan Monitoring</button>
            </footer>
            @endif
            @endif
        </section>
    </div>
    @endteleport
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="influencer-modal-backdrop fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-gray-900/60 p-3 backdrop-blur-sm sm:p-4">
        <div role="dialog" aria-modal="true" aria-labelledby="influencer-payment-title" class="influencer-modal-panel relative my-auto max-h-[calc(100dvh-1.5rem)] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-4 shadow-2xl dark:bg-gray-800 sm:max-h-[calc(100dvh-2rem)] sm:p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 id="influencer-payment-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pembayaran Influencer</h3>
                    @php $inf = $paymentInfluencerId ? \App\Models\Influencer::find($paymentInfluencerId) : null; @endphp
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $inf?->nama }}@if(auth()->user()->canSeeBiaya()) · Rp {{ $inf ? number_format($inf->biaya, 0, ',', '.') : 0 }}/bulan @endif</p>
                </div>
                <button wire:click="closePaymentModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Bulan Ke</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Jatuh Tempo</th>
                            @if(auth()->user()->canSeeBiaya())
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Jumlah</th>
                            @endif
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($paymentRecords as $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $p->bulan_ke }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $p->tanggal_jatuh_tempo->isoFormat('D MMMM YYYY') }}</td>
                            @if(auth()->user()->canSeeBiaya())
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100 font-medium">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            @endif
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'lunas')
                                <span class="influencer-status-enter inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Lunas</span>
                                @else
                                <span class="influencer-status-enter inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'pending')
                                <button wire:click="markAsPaid({{ $p->id }})" wire:loading.attr="disabled" wire:target="markAsPaid({{ $p->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors disabled:opacity-60">
                                    <svg wire:loading wire:target="markAsPaid({{ $p->id }})" class="h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Lunas
                                </button>
                                @else
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canSeeBiaya() ? 5 : 4 }}" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-8 h-8 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125V9M7.5 12h9M12 15h-1.5m0 0H9m1.5 0V9m-6 3h6m-6 3h6m-3-6h.008v.008H12V12z"/></svg>
                                    <p class="font-medium">Belum ada data pembayaran</p>
                                    <p class="text-xs mt-1">Data akan muncul saat influencer dibuat</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteConfirmation)
    <div class="influencer-modal-backdrop fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" role="presentation" wire:keydown.escape="cancelDelete">
        <section role="alertdialog" aria-modal="true" aria-labelledby="delete-influencer-title" aria-describedby="delete-influencer-description" class="influencer-modal-panel w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl dark:bg-gray-800 sm:p-6">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3h.008v.008H12v-.008zM10.29 3.86 1.82 18.5A1.7 1.7 0 003.3 21h17.4a1.7 1.7 0 001.48-2.5L13.71 3.86a1.98 1.98 0 00-3.42 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <h3 id="delete-influencer-title" class="text-base font-semibold text-gray-900 dark:text-gray-100">Hapus data influencer?</h3>
                    <p id="delete-influencer-description" class="mt-1 break-words text-sm leading-6 text-gray-600 dark:text-gray-300">Data <span class="font-semibold">{{ $deleteInfluencerName }}</span> dan riwayat pembayarannya akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" wire:click="cancelDelete" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">Batal</button>
                <button type="button" wire:click="deleteConfirmed" wire:loading.attr="disabled" wire:target="deleteConfirmed" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-60">
                    <svg wire:loading wire:target="deleteConfirmed" class="mr-1.5 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Ya, hapus
                </button>
            </div>
        </section>
    </div>
    @endif
    </section>
    @else
    <section id="influencer-submission-panel" role="tabpanel" aria-label="Pengajuan Influencer" class="influencer-feedback-enter">
        @livewire('influencer-pengajuan-table', ['showKolCreateButton' => $isKolSubmitter], key('influencer-submission-tab'))
    </section>
    @endif
</div>
