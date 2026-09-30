<div data-influencer-motion>
    @if(session('message'))
    <div class="influencer-feedback-enter mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

    @if(session('error'))
    <div class="influencer-feedback-enter mb-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4" aria-label="Ringkasan pengajuan influencer">
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
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-500 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-warning">Koordinator Creative</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['pending_creative'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menunggu Koordinator Creative</p>
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
            @if(auth()->user()->isKoordinatorCreative())
            <button wire:click="openNew" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Ajukan Influencer
            </button>
            @elseif($showKolCreateButton)
            <button type="button" wire:click="$dispatch('open-kol-influencer-request')" class="btn-primary text-xs py-2 shrink-0">
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
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Divisi</th>
                        <th class="px-6 py-3">Durasi Kontrak / Perpanjangan</th>
                        <th class="px-6 py-3">Biaya</th>
                        <th class="px-6 py-3">Koordinator Creative</th>
                        <th class="px-6 py-3">Head of Store</th>
                        <th class="px-6 py-3">General Manager</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="table-cell text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">
                                {{ $item->nama }}
                                @if($item->is_perpanjangan)<span class="ml-1 inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">Perpanjangan</span>@endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->divisi ?: '-' }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                @if($item->rekomendasi_lama_kontrak)
                                    {{ $item->rekomendasi_lama_kontrak }} bulan
                                @elseif($item->mulai_kontrak && $item->habis_kontrak)
                                    {{ $item->mulai_kontrak->isoFormat('D MMM YYYY') }} – {{ $item->habis_kontrak->isoFormat('D MMM YYYY') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="table-cell text-left text-gray-600 dark:text-gray-400">@if($item->biaya)Rp {{ number_format($item->biaya, 0, ',', '.') }}@else<span class="text-gray-400">-</span>@endif</td>
                            <td class="table-cell">
                                @if($item->approved_coordinator_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Disetujui</span>
                                @elseif($item->status === 'rejected' && !$item->approved_coordinator_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Ditolak</span>
                                @elseif($item->status === 'pending_creative')
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Menunggu</span>
                                @else
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Menunggu</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($item->approved_hos1_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Disetujui</span>
                                @elseif($item->status === 'rejected' && !$item->approved_hos1_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Ditolak</span>
                                @else
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Menunggu</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($item->approved_gm_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Disetujui</span>
                                @elseif($item->status === 'rejected' && $item->approved_hos1_by)
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Ditolak</span>
                                @else
                                <span class="influencer-status-enter inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">Menunggu</span>
                                @endif
                            </td>
                            <td class="table-cell">
                                @if($this->canApprove($item) || $this->canEditSubmission($item) || $this->canDeleteSubmission($item))
                                <div class="flex flex-wrap gap-2">
                                    @if($this->canApprove($item))
                                    <button type="button" wire:click="approve({{ $item->id }})" wire:loading.attr="disabled" wire:target="approve({{ $item->id }})" class="inline-flex min-h-10 items-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 disabled:opacity-60">
                                        <svg wire:loading wire:target="approve({{ $item->id }})" class="mr-1.5 h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Setujui
                                    </button>
                                    <button type="button" wire:click="openTolak({{ $item->id }})" wire:loading.attr="disabled" class="inline-flex min-h-10 items-center rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:bg-red-900/30 dark:text-red-300">Tolak</button>
                                    @endif
                                    @if($this->canEditSubmission($item))
                                    <button type="button" wire:click="openEdit({{ $item->id }})" class="inline-flex min-h-10 items-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-blue-900/30 dark:text-blue-300">Edit</button>
                                    @endif
                                    @if($this->canDeleteSubmission($item))
                                    <button type="button" wire:click="confirmDelete({{ $item->id }})" class="inline-flex min-h-10 items-center rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 dark:bg-red-900/30 dark:text-red-300">Hapus</button>
                                    @endif
                                </div>
                                @elseif($this->isWaitingForPreviousApproval($item))
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">Menunggu tahap sebelumnya</span>
                                @else
                                <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
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
    @teleport('body')
    <div data-influencer-motion class="influencer-modal-backdrop fixed inset-0 z-[10000] flex items-start justify-center overflow-y-auto bg-slate-950/60 p-3 sm:items-center sm:p-5">
        <div class="influencer-modal-panel isolate relative my-auto max-h-[calc(100dvh-1.5rem)] w-full max-w-lg overflow-y-auto rounded-2xl border border-gray-200 bg-white p-4 shadow-[0_24px_80px_-20px_rgba(15,23,42,0.55)] dark:border-gray-700 dark:bg-gray-900 sm:max-h-[calc(100dvh-2.5rem)] sm:p-7">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit Pengajuan Influencer' : 'Ajukan Influencer' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui data pengajuan influencer' : 'Isi data influencer baru' }}</p>
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
                        @foreach(\App\Livewire\InfluencerPengajuanTable::DIVISI_OPTIONS as $divisionOption)
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

                <div>
                    <x-input-label value="Keterangan" />
                    <textarea wire:model="keterangan" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" placeholder="Keterangan tambahan..."></textarea>
                    @error('keterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                @if(auth()->user()->canSeeBiaya())
                <div>
                    <x-input-label value="Biaya (per bulan)" />
                    <x-text-input type="number" step="0.01" min="0" wire:model="biaya" class="mt-1 block w-full" placeholder="0" />
                    @error('biaya') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="save" class="btn-primary text-xs disabled:opacity-60">
                        <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Simpan Perubahan' : 'Ajukan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endteleport
    @endif

    @if($showSuccessModal)
    @teleport('body')
    <div data-influencer-motion class="influencer-modal-backdrop fixed inset-0 z-[10001] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" role="presentation">
        <section role="alertdialog" aria-modal="true" aria-labelledby="influencer-submission-success-title" aria-describedby="influencer-submission-success-description" class="influencer-modal-panel w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl dark:bg-gray-800">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400" aria-hidden="true">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12.75 4.5 4.5L19 7.5"/></svg>
            </div>
            <h3 id="influencer-submission-success-title" class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Pengajuan influencer berhasil</h3>
            <p id="influencer-submission-success-description" class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $successMessage }}</p>
            <button type="button" wire:click="closeSuccessModal" class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 sm:w-auto">Mengerti</button>
        </section>
    </div>
    @endteleport
    @endif

    @if($tolakId)
    @teleport('body')
    <div data-influencer-motion class="influencer-modal-backdrop fixed inset-0 z-[10001] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" role="presentation">
        <section role="dialog" aria-modal="true" aria-labelledby="reject-influencer-title" class="influencer-modal-panel w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <h3 id="reject-influencer-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tolak Pengajuan Influencer</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tuliskan alasan penolakan agar pengaju memahami keputusan ini.</p>
            <label for="alasan-tolak-influencer" class="mt-4 block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan penolakan</label>
            <textarea id="alasan-tolak-influencer" wire:model="alasanTolak" rows="4" class="mt-1 block w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required></textarea>
            @error('alasanTolak') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" wire:click="batalTolak" class="btn-secondary min-h-10 text-xs">Batal</button>
                <button type="button" wire:click="reject({{ $tolakId }})" wire:loading.attr="disabled" wire:target="reject" class="inline-flex min-h-10 items-center rounded-xl bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 disabled:opacity-60">
                    <svg wire:loading wire:target="reject" class="mr-1.5 h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Konfirmasi Tolak
                </button>
            </div>
        </section>
    </div>
    @endteleport
    @endif

    @if($showDeleteConfirmation)
    @teleport('body')
    <div data-influencer-motion class="influencer-modal-backdrop fixed inset-0 z-[10001] flex items-center justify-center bg-gray-900/60 p-4 backdrop-blur-sm" role="presentation" wire:keydown.escape="cancelDeletePengajuan">
        <section role="alertdialog" aria-modal="true" aria-labelledby="delete-influencer-submission-title" aria-describedby="delete-influencer-submission-description" class="influencer-modal-panel w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <h3 id="delete-influencer-submission-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Hapus pengajuan influencer?</h3>
            <p id="delete-influencer-submission-description" class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">Riwayat pengajuan akan dihapus. Jika pengajuan kontrak sudah disetujui, data influencer, riwayat monitoring, dan jadwal pembayarannya juga akan dihapus. Menghapus pengajuan perpanjangan tidak menghapus data influencer utama.</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" wire:click="cancelDeletePengajuan" class="btn-secondary min-h-10 text-xs">Batal</button>
                <button type="button" wire:click="deletePengajuan" wire:loading.attr="disabled" wire:target="deletePengajuan" class="inline-flex min-h-10 items-center rounded-xl bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500 disabled:opacity-60">
                    <svg wire:loading wire:target="deletePengajuan" class="mr-1.5 h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Ya, hapus
                </button>
            </div>
        </section>
    </div>
    @endteleport
    @endif
</div>
