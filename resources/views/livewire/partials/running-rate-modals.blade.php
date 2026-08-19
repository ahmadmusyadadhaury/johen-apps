{{-- Sold modal (create & edit) --}}
@if($showSoldModal)
<div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 bg-black/40 backdrop-blur-sm overflow-y-auto" wire:click.self="closeSoldModal">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 my-10 animate-scale-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $editSoldId ? 'Koreksi Sold' : 'Input Sold' }}</h3>
            <button wire:click="closeSoldModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form wire:submit="{{ $editSoldId ? 'updateSold' : 'saveSold' }}" class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal *</label>
                <input type="date" wire:model="soldTanggal" class="input-field">
                @error('soldTanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Host *</label>
                <select wire:model="soldHost" class="input-field">
                    <option value="">-- Pilih Host --</option>
                    @foreach($divisionHostOptions as $host)
                        <option value="{{ $host->id }}">{{ $host->nama }}</option>
                    @endforeach
                </select>
                @error('soldHost') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sold *</label>
                <input type="number" step="0.01" min="0" wire:model.live="soldValue" class="input-field" placeholder="Contoh: 2">
                @error('soldValue') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 px-4 py-3 text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                Sold dicatat sebagai data harian. Jika tanggal + host sudah memiliki data, sistem akan memperbarui nilainya.
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="closeSoldModal" class="btn-ghost">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs py-2.5">
                    <span wire:loading.remove wire:target="saveSold,updateSold">Simpan Sold</span>
                    <span wire:loading wire:target="saveSold,updateSold">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Setup period modal --}}
@if($showSetupModal)
<div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 bg-black/40 backdrop-blur-sm overflow-y-auto" wire:click.self="closeSetupModal">
    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 my-10 animate-scale-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Set Up Running Rate</h3>
            <button wire:click="closeSetupModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form wire:submit="saveSetup" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Periode *</label>
                <input type="text" wire:model="setupNama" class="input-field" placeholder="Contoh: Agustus 2026">
                @error('setupNama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai *</label>
                    <input type="date" wire:model="setupMulai" class="input-field">
                    @error('setupMulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Selesai *</label>
                    <input type="date" wire:model="setupSelesai" class="input-field">
                    @error('setupSelesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Target per Host</label>
                <div class="space-y-2">
                    @foreach($hosts as $host)
                        <div class="flex items-center justify-between gap-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 px-4 py-2.5">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $host->nama }}</p>
                                <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono">{{ $host->nik }}</p>
                            </div>
                            <input type="number" step="0.01" min="0" wire:model="setupTargets.{{ $host->id }}" class="input-field !w-24 !py-2 text-right" placeholder="0">
                        </div>
                    @endforeach
                </div>
                @error('setupTargets') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('setupTargets.*') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="closeSetupModal" class="btn-ghost">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs py-2.5">
                    <span wire:loading.remove wire:target="saveSetup">Buat Periode</span>
                    <span wire:loading wire:target="saveSetup">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Edit target modal --}}
@if($showTargetModal)
<div class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-4 bg-black/40 backdrop-blur-sm overflow-y-auto" wire:click.self="closeTargetModal">
    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 my-10 animate-scale-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Edit Data Host</h3>
            <button wire:click="closeTargetModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form wire:submit="saveTarget" class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Host</label>
                @if($editTargetHostId)
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $hostMap[$editTargetHostId]->nama ?? '—' }}</p>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 font-mono">{{ $hostMap[$editTargetHostId]->nik ?? '' }}</p>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Target *</label>
                    <input type="number" step="0.01" min="0" wire:model.live="targetValue" class="input-field" placeholder="Contoh: 15">
                    @error('targetValue') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sold · {{ $tanggalFilter ? \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d M Y') : 'Hari Ini' }} *</label>
                    <input type="number" step="0.01" min="0" wire:model.live="soldValue" class="input-field" placeholder="Contoh: 2">
                    @error('soldValue') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 px-4 py-3 text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed">
                Target memengaruhi perhitungan periode; Sold memperbarui data penjualan host untuk tanggal {{ $tanggalFilter ? \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d M Y') : 'hari ini' }}.
            </div>
            <div class="flex justify-end gap-3 pt-1">
                <button type="button" wire:click="closeTargetModal" class="btn-ghost">Batal</button>
                <button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs py-2.5">
                    <span wire:loading.remove wire:target="saveTarget">Simpan</span>
                    <span wire:loading wire:target="saveTarget">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- History modal --}}
@if($showHistoryModal)
<div class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/40 backdrop-blur-sm overflow-y-auto" wire:click.self="closeHistoryModal">
    <div class="w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 my-10 animate-scale-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Riwayat Sold</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Histori input Sold periode {{ $period?->nama }}</p>
            </div>
            <button wire:click="closeHistoryModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[560px]">
                <thead>
                    <tr class="table-header">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Host</th>
                        <th class="px-5 py-3 text-right">Sold</th>
                        <th class="px-5 py-3">Diinput Oleh</th>
                        <th class="px-5 py-3">Waktu Input</th>
                        @if($canManage)
                            <th class="px-5 py-3 text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($history as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $item->host?->nama ?? '—' }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-primary-600 dark:text-primary-400 tabular-nums">{{ $num($item->sold) }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $item->inputBy?->getRoleDisplayName() ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d M Y H:i') }}</td>
                            @if($canManage)
                                <td class="px-5 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="openEditSoldModal({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            Edit
                                        </button>
                                        <button wire:click="confirmDeleteSold({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 6 : 5 }}" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada riwayat Sold.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
            <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-800">
                {{ $history->links() }}
            </div>
        @endif
    </div>
</div>
@endif

{{-- Delete confirm --}}
@if($showDeleteConfirm)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 p-6 text-center animate-scale-in">
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-2xl bg-red-50 dark:bg-red-900/20">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">Hapus Riwayat Sold</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus data Sold ini? Perhitungan Running Rate akan diperbarui otomatis.</p>
        <div class="flex justify-center gap-3">
            <button wire:click="cancelDeleteSold" class="btn-ghost">Batal</button>
            <button wire:click="executeDeleteSold" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">Hapus</button>
        </div>
    </div>
</div>
@endif

{{-- Delete host data confirm --}}
@if($showDeleteHostConfirm)
<div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800 p-6 text-center animate-scale-in">
        <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-2xl bg-red-50 dark:bg-red-900/20">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">Hapus Data Host</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6">
            Apakah Anda yakin ingin menghapus data
            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $deleteHostId ? ($hostMap[$deleteHostId]->nama ?? 'host') : 'host' }}</span>
            pada periode {{ $period?->nama }}? Target dan seluruh riwayat Sold host akan dihapus.
        </p>
        <div class="flex justify-center gap-3">
            <button wire:click="cancelDeleteHost" class="btn-ghost">Batal</button>
            <button wire:click="executeDeleteHost" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition-colors">Hapus</button>
        </div>
    </div>
</div>
@endif