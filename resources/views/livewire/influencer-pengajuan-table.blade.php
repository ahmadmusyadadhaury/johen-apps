<div>
    @if(session('message'))
    <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <div class="mb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Semua</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pengajuan</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-500 text-white shadow-lg shadow-sky-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-warning">HOS 1</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['pending_hos1'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menunggu Atasan 1</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <span class="badge-warning">GM/CEO</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['pending_gm'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menunggu GM / CEO</p>
        </div>
        <div class="stat-card group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-500 text-white shadow-lg shadow-violet-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                </div>
                <span class="badge-success">Disetujui</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['approved'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pengajuan Disetujui</p>
        </div>
    </div>

    <div class="card">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Pengajuan Influencer</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar pengajuan influencer baru</p>
            </div>
            @if(auth()->user()->isKoordinatorCreative() || auth()->user()->isSuperAdmin())
            <button wire:click="openNew" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Ajukan Influencer
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 text-center w-12">No</th>
                        <th class="px-6 py-3">Pengaju</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Mulai</th>
                        <th class="px-6 py-3">Habis</th>
                        <th class="px-6 py-3">Biaya</th>
                        <th class="px-6 py-3">Persetujuan Atasan 1</th>
                        <th class="px-6 py-3">Persetujuan Atasan 2</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="table-cell text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->pengaju->name ?? $item->pengaju->username }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $item->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->mulai_kontrak->isoFormat('D MMM YYYY') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->habis_kontrak->isoFormat('D MMM YYYY') }}</td>
                            <td class="table-cell text-right text-gray-600 dark:text-gray-400">@if($item->biaya)Rp {{ number_format($item->biaya, 0, ',', '.') }}@else<span class="text-gray-400">-</span>@endif</td>
                            <td class="table-cell">
                                @if($item->approved_hos1_by)
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $item->approverHos1->name ?? $item->approverHos1->username }}</span>
                                @elseif($item->status === 'rejected' && !$item->approved_hos1_by)
                                <span class="text-xs text-red-600 dark:text-red-400">Ditolak</span>
                                @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($item->approved_gm_by)
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $item->approverGm->name ?? $item->approverGm->username }}</span>
                                @elseif($item->status === 'rejected' && $item->approved_hos1_by)
                                <span class="text-xs text-red-600 dark:text-red-400">Ditolak</span>
                                @elseif($item->status === 'approved' && !$item->approved_gm_by)
                                <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <p class="font-medium">Belum ada pengajuan</p>
                                    <p class="text-xs mt-1">Pengajuan influencer baru akan muncul di sini</p>
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

    {{-- Form Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajukan Influencer</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Isi data influencer baru</p>
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

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Mulai Kontrak *" />
                        <x-text-input type="date" wire:model="mulai_kontrak" class="mt-1 block w-full" />
                        @error('mulai_kontrak') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Habis Kontrak *" />
                        <x-text-input type="date" wire:model="habis_kontrak" class="mt-1 block w-full" />
                        @error('habis_kontrak') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if(auth()->user()->canSeeBiaya())
                <div>
                    <x-input-label value="Biaya (per bulan)" />
                    <x-text-input type="number" step="0.01" min="0" wire:model="biaya" class="mt-1 block w-full" placeholder="0" />
                    @error('biaya') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div>
                    <x-input-label value="Link Sosmed" />
                    <x-text-input type="url" wire:model="link_sosmed" class="mt-1 block w-full" placeholder="https://instagram.com/..." />
                    @error('link_sosmed') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Ajukan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
