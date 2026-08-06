<div>
    @if(session('message'))
    <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

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

    <div class="mb-5 grid grid-cols-1 sm:grid-cols-4 gap-4">
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
            @if(auth()->user()->isKoordinatorCreative())
            <a href="{{ route('hris.influencer-pengajuan') }}" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Ajukan Data
            </a>
            @else
            <button wire:click="openNew" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Data
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 text-center w-12">No</th>
                        <th class="px-6 py-3">No. Kontrak</th>
                        <th class="px-6 py-3">Nama Influencer</th>
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
                                    @if(!auth()->user()->isReadOnlyWorkspace())
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus data influencer ini?" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                    @endif
                                    @if(auth()->user()->canSeeBiaya() && !auth()->user()->isReadOnlyWorkspace())
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
                            <td colspan="{{ auth()->user()->canSeeBiaya() ? 10 : 8 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
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

    {{-- Modal --}}
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
             @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Influencer</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} data influencer</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="No. Kontrak *" />
                    <x-text-input type="text" wire:model="no_kontrak" class="mt-1 block w-full" placeholder="Contoh: SPK/2026/001" />
                    @error('no_kontrak') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

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
                    <x-input-label value="Biaya" />
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
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Payment Modal --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pembayaran Influencer</h3>
                    @php $inf = $paymentInfluencerId ? \App\Models\Influencer::find($paymentInfluencerId) : null; @endphp
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $inf?->nama }} · Rp {{ $inf ? number_format($inf->biaya, 0, ',', '.') : 0 }}/bulan</p>
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
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Jumlah</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($paymentRecords as $p)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $p->bulan_ke }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $p->tanggal_jatuh_tempo->isoFormat('D MMMM YYYY') }}</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-gray-100 font-medium">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'lunas')
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Lunas</span>
                                @else
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($p->status === 'pending')
                                <button wire:click="markAsPaid({{ $p->id }})" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors">
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
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
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
</div>
