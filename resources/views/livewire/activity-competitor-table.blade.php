<div>
    @if(session('message'))
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola data aktivitas kompetitor</p>
        </div>
        @unless(auth()->user()->isManager() || auth()->user()->isReadOnlyWorkspace())
        <div class="flex items-center gap-2">
            <button wire:click="openSwot" class="btn-primary text-xs py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah SWOT
            </button>
            <button wire:click="openMonitoring" class="btn-secondary text-xs py-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Monitoring
            </button>
        </div>
        @endunless
    </div>

    @if(auth()->user()->isManager())
        {{-- MANAGER VIEW: Summary Cards --}}
        <div class="space-y-4">
            @forelse($items as $item)
            @php
                $emp = $item->employee;
                $canFeedback = isset($canGiveFeedbackMap[$emp?->id]);
            @endphp
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 text-gray-600 dark:text-gray-300 font-bold text-sm">
                                {{ strtoupper(substr($emp?->nama ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $emp?->nama ?? '-' }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $item->jenis === 'swot' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' }}">
                                        {{ ucfirst($item->jenis) }}
                                    </span>
                                    @if($item->nama_competitor)
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 truncate">• {{ $item->nama_competitor }}</span>
                                    @endif
                                    @if($item->tanggal_analysis)
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500">• {{ $item->tanggal_analysis->isoFormat('D MMM YYYY') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button wire:click="viewDetail({{ $item->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 text-xs font-semibold hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                    @if($item->feedback_atasan)
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Feedback</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $item->feedback_atasan }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-12 text-center">
                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum ada data dari bawahan</p>
            </div>
            @endforelse
        </div>

        @if($items->hasPages())
        <div class="mt-4 px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
            {{ $items->links() }}
        </div>
        @endif

        {{-- MANAGER: Detail Modal --}}
        <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
             x-data="{ open: false }"
             x-init="$watch('$wire.showDetail', value => open = value)"
             x-show="open" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.stop class="relative w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
                @php $detail = $items->firstWhere('id', $detailId); @endphp
                @if($detail)
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Activity Competitor</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $detail->employee?->nama }} • <span class="capitalize">{{ $detail->jenis }}</span></p>
                    </div>
                    <button wire:click="closeDetail" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tanggal Analysis</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $detail->tanggal_analysis?->isoFormat('D MMM YYYY') ?? '-' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Competitor</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $detail->nama_competitor ?? '-' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Platform</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5 capitalize">{{ $detail->platform ?? '-' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Kategori Produk</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $detail->kategori_produk ? str_replace('_', ' ', $detail->kategori_produk) : '-' }}</p>
                        </div>
                    </div>

                    @if($detail->link)
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Link</p>
                        <a href="{{ $detail->link }}" target="_blank" class="text-sm font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:underline mt-0.5 block truncate">{{ $detail->link }}</a>
                    </div>
                    @endif

                    @if($detail->jenis === 'swot')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @if($detail->strength)
                        <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30">
                            <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-1">Strength</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $detail->strength }}</p>
                        </div>
                        @endif
                        @if($detail->weakness)
                        <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/30">
                            <p class="text-[10px] font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-1">Weakness</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $detail->weakness }}</p>
                        </div>
                        @endif
                        @if($detail->opportunity)
                        <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30">
                            <p class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-1">Opportunity</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $detail->opportunity }}</p>
                        </div>
                        @endif
                        @if($detail->threat)
                        <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30">
                            <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Threat</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $detail->threat }}</p>
                        </div>
                        @endif
                    </div>

                    @if($detail->kesimpulan)
                    <div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700">
                        <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kesimpulan</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $detail->kesimpulan }}</p>
                    </div>
                    @endif
                    @endif

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                        @php $isFeedbackAllowed = !auth()->user()->isReadOnlyWorkspace() && isset($canGiveFeedbackMap[$detail->employee?->id]); @endphp
                        @if($isFeedbackAllowed)
                            @if($detail->feedback_atasan)
                            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30">
                                <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Feedback Anda</p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $detail->feedback_atasan }}</p>
                            </div>
                            @else
                            <button wire:click="openFeedback({{ $detail->id }})" class="w-full py-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/10 border border-dashed border-amber-200 dark:border-amber-800/50 text-amber-600 dark:text-amber-400 text-sm font-semibold hover:bg-amber-100 dark:hover:bg-amber-900/20 transition-colors">
                                + Beri Feedback
                            </button>
                            @endif
                        @elseif($detail->feedback_atasan)
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Feedback</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $detail->feedback_atasan }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

    @else
        {{-- KOORDINATOR VIEW: Detailed Table --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">Nama</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">Jenis</th>
                            <th colspan="5" class="px-4 py-3 text-center text-xs font-semibold text-primary-700 dark:text-primary-400 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">Detail Analisis</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">Feedback Atasan</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                        <tr class="bg-gray-50/50 dark:bg-gray-800/50 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Tgl Analysis</th>
                            <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Competitor</th>
                            <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Platform</th>
                            <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Kategori</th>
                            <th class="px-4 py-2.5">Link</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($items as $item)
                        @php
                            $emp = $item->employee;
                            $canFeedback = isset($canGiveFeedbackMap[$emp?->id]);
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 dark:bg-gray-900 transition-colors">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $emp?->nama ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize border-r border-gray-100 dark:border-gray-700">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $item->jenis === 'swot' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' }}">
                                    {{ $item->jenis }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap border-r border-gray-100 dark:border-gray-700">{{ $item->tanggal_analysis?->isoFormat('D MMM YYYY') ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-[150px] truncate border-r border-gray-100 dark:border-gray-700">{{ $item->nama_competitor ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize border-r border-gray-100 dark:border-gray-700">{{ $item->platform ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize border-r border-gray-100 dark:border-gray-700">{{ $item->kategori_produk ? str_replace('_', ' ', $item->kategori_produk) : '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-[120px] truncate">
                                @if($item->link)
                                <a href="{{ $item->link }}" target="_blank" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:underline">{{ $item->link }}</a>
                                @else
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate border-r border-gray-100 dark:border-gray-700">
                                @if($canFeedback)
                                    @if($item->feedback_atasan)
                                        {{ $item->feedback_atasan }}
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 italic">-</span>
                                    @endif
                                @else
                                    @if($item->feedback_atasan)
                                        {{ $item->feedback_atasan }}
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 italic">-</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(!auth()->user()->isReadOnlyWorkspace() && $emp && auth()->user()->employee && $emp->id === auth()->user()->employee->id)
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus data ini?" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @else
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                        </tr>
                        @if($item->jenis === 'swot' && ($item->strength || $item->weakness || $item->opportunity || $item->threat || $item->kesimpulan))
                        <tr class="bg-gray-50/30 dark:bg-gray-800/30">
                            <td colspan="9" class="px-4 py-3">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @if($item->strength)
                                    <div class="p-2.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30">
                                        <p class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-0.5">Strength</p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300">{{ $item->strength }}</p>
                                    </div>
                                    @endif
                                    @if($item->weakness)
                                    <div class="p-2.5 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800/30">
                                        <p class="text-[10px] font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-0.5">Weakness</p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300">{{ $item->weakness }}</p>
                                    </div>
                                    @endif
                                    @if($item->opportunity)
                                    <div class="p-2.5 rounded-lg bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30">
                                        <p class="text-[10px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-0.5">Opportunity</p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300">{{ $item->opportunity }}</p>
                                    </div>
                                    @endif
                                    @if($item->threat)
                                    <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/30">
                                        <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-0.5">Threat</p>
                                        <p class="text-xs text-gray-700 dark:text-gray-300">{{ $item->threat }}</p>
                                    </div>
                                    @endif
                                </div>
                                @if($item->kesimpulan)
                                <div class="mt-2 p-2.5 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <p class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-0.5">Kesimpulan</p>
                                    <p class="text-xs text-gray-700 dark:text-gray-300">{{ $item->kesimpulan }}</p>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Data</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik "Tambah SWOT" atau "Tambah Monitoring" untuk mengisi data</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($items->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    @endif

    {{-- Modal Tambah Data --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showModal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Activity Competitor</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Isi data aktivitas kompetitor</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Jenis:</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $jenis === 'swot' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' }}">
                        {{ ucfirst($jenis) }}
                    </span>
                </div>

                @if($jenis === 'swot')
                <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Data Analisis SWOT</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Tanggal Analysis *" />
                            <x-text-input type="date" wire:model="tanggal_analysis" class="mt-1 block w-full" />
                            @error('tanggal_analysis') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Nama Competitor *" />
                            <x-text-input type="text" wire:model="nama_competitor" class="mt-1 block w-full" placeholder="Nama kompetitor" />
                            @error('nama_competitor') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Platform *" />
                            <select wire:model="platform" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih platform</option>
                                <option value="instagram">Instagram</option>
                                <option value="tiktok">TikTok</option>
                            </select>
                            @error('platform') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Kategori Produk *" />
                            <select wire:model="kategori_produk" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih kategori</option>
                                <option value="top_up_game">Top Up Game</option>
                                <option value="jual_beli_akun_game">Jual Beli Akun Game</option>
                            </select>
                            @error('kategori_produk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <x-input-label value="Link Website / Sosmed" />
                            <x-text-input type="url" wire:model="link" class="mt-1 block w-full" placeholder="https://..." />
                            @error('link') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label value="Strength *" />
                            <textarea wire:model="strength" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Kekuatan kompetitor..."></textarea>
                            @error('strength') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Weakness *" />
                            <textarea wire:model="weakness" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Kelemahan kompetitor..."></textarea>
                            @error('weakness') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Opportunity *" />
                            <textarea wire:model="opportunity" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Peluang..."></textarea>
                            @error('opportunity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label value="Threat *" />
                            <textarea wire:model="threat" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Ancaman..."></textarea>
                            @error('threat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label value="Kesimpulan *" />
                        <textarea wire:model="kesimpulan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Kesimpulan analisis..."></textarea>
                        @error('kesimpulan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Feedback Atasan --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showFeedbackModal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Feedback Atasan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Berikan feedback untuk data ini</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveFeedback" class="space-y-4">
                <div>
                    <x-input-label value="Feedback *" />
                    <textarea wire:model="feedback_atasan" rows="4" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Tulis feedback Anda..."></textarea>
                    @error('feedback_atasan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Sukses Feedback --}}
    <div wire:ignore.self class="fixed inset-0 z-[250] flex items-center justify-center p-5 bg-gray-900/60 backdrop-blur-sm"
         x-data="{ open: false }"
         x-init="$watch('$wire.showSuccess', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 text-center shadow-2xl my-10">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Feedback Berhasil</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Feedback Anda sudah disimpan.</p>
            <div class="mt-6">
                <button wire:click="$set('showSuccess', false)" class="btn-primary text-xs w-full justify-center">Tutup</button>
            </div>
        </div>
    </div>
</div>
