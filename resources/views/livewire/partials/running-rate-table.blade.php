<div class="card mt-4 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-50 dark:border-gray-800">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Host Performance</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pencapaian dan kebutuhan penjualan tiap host</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <select wire:model.live="periodId" class="input-field !w-44 !py-2">
                @forelse($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @empty
                    <option value="">Belum ada periode</option>
                @endforelse
            </select>
            <input type="date" wire:model.live="tanggalFilter" class="input-field !w-40 !py-2" title="Tanggal">
            @if($canManage)
                <button wire:click="openSoldModal" class="btn-primary text-xs px-4 py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Input Sold
                </button>
            @endif
        </div>
    </div>

    @if($rows->isEmpty())
        <div class="flex flex-col items-center px-6 py-14 text-center">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Belum ada target untuk periode ini</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tentukan target tiap host agar tabel Running Rate dapat tampil.</p>
            @if($canManage)
                <button wire:click="openSetupModal" class="btn-secondary text-xs mt-5">Set Up Running Rate</button>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[860px]">
                <thead>
                    <tr class="table-header">
                        <th class="px-5 py-3">Host</th>
                        <th class="px-5 py-3 text-right">Target</th>
                        <th class="px-5 py-3 text-right">Total Sold</th>
                        <th class="px-5 py-3">Achievement</th>
                        <th class="px-5 py-3 text-right">Remaining</th>
                        <th class="px-5 py-3 text-right">Daily RR</th>
                        <th class="px-5 py-3 text-right">Weekly RR</th>
                        <th class="px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary-100 to-violet-100 text-primary-700 dark:from-primary-900/40 dark:to-violet-900/40 dark:text-primary-300 text-xs font-bold">
                                        {{ strtoupper(substr($row['nama'], 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $row['nama'] }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono">{{ $row['nik'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($row['target']) }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold text-gray-700 dark:text-gray-300 tabular-nums">{{ $num($row['sold']) }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-1.5 w-24 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden shrink-0">
                                        <div class="h-full rounded-full {{ $row['achievement'] >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-primary-500 to-violet-500' }} transition-all duration-500" style="width: {{ min($row['achievement'], 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 tabular-nums">{{ $num($row['achievement'], 2) }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-medium text-gray-900 dark:text-gray-100 tabular-nums">{{ $num($row['remaining']) }}</td>
                            <td class="px-5 py-3.5 text-right text-gray-600 dark:text-gray-400 tabular-nums">{{ $num($row['rr_daily'], 2) }}</td>
                            <td class="px-5 py-3.5 text-right text-gray-600 dark:text-gray-400 tabular-nums">{{ $num($row['rr_weekly'], 2) }}</td>
                            <td class="px-5 py-3.5">
                                @if($canManage)
                                    <div class="flex items-center gap-1">
                                        <button wire:click="openEditTargetModal({{ $row['host_id'] }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            Edit
                                        </button>
                                        <button wire:click="confirmDeleteHost({{ $row['host_id'] }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <p class="font-medium">Tidak ada host yang cocok</p>
                                    <p class="text-xs mt-1">Ubah filter host atau pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>