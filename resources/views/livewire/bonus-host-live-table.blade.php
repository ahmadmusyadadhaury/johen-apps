<div x-data="{ showFotoModal: false, fotoModalUrl: '', fotoModalLabel: '' }">
    <div class="flex flex-col gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIK, nama, atau sesi..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                </div>
            </div>
            <div class="flex items-center gap-2">
                @can('create-data')
                <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Data
                </button>
                @endcan
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div>
                <select wire:model.live="bulan" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    <option value="">Semua Bulan</option>
                    @php
                        $now = now();
                    @endphp
                    @for($i = 0; $i < 12; $i++)
                        @php
                            $date = $now->copy()->subMonths($i);
                            $val = $date->format('Y-m');
                        @endphp
                        <option value="{{ $val }}">{{ $date->locale('id')->isoFormat('MMMM Y') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <select wire:model.live="divisiFilter" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->nama }}">{{ $div->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="table-header">
                    <th class="px-6 py-3 w-12 text-center">No</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('tanggal')">
                        <div class="flex items-center gap-1">Tanggal @if($sortField === 'tanggal')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M19.5 8.25l-7.5 7.5-7.5-7.5' : 'M4.5 15.75l7.5-7.5 7.5 7.5' }}"/></svg>@endif</div>
                    </th>
                    <th class="px-6 py-3">Jam</th>
                    <th class="px-6 py-3">NIK</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Sesi</th>
                    <th class="px-6 py-3 text-right">Sold</th>
                    <th class="px-6 py-3 text-right">View</th>
                    <th class="px-6 py-3 text-right">Peak</th>
                    <th class="px-6 py-3">Catatan</th>
                    <th class="px-6 py-3 text-center">Foto</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                        <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $items->firstItem() + $loop->index }}</td>
                        <td class="table-cell">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="table-cell whitespace-nowrap">
                            @if($item->jam_mulai && $item->jam_selesai)
                                {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                            @elseif($item->jam_mulai)
                                {{ substr($item->jam_mulai, 0, 5) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="table-cell font-medium">{{ $item->nik }}</td>
                        <td class="table-cell">{{ $item->nama }}</td>
                        <td class="table-cell">{{ $item->sesi }}</td>
                        <td class="table-cell text-right">{{ number_format($item->ach_sold, 0, ',', '.') }}</td>
                        <td class="table-cell text-right">{{ number_format($item->ach_view, 0, ',', '.') }}</td>
                        <td class="table-cell text-right">{{ number_format($item->peak_view, 0, ',', '.') }}</td>
                        <td class="table-cell max-w-[120px] truncate text-gray-500">{{ $item->catatan ?? '-' }}</td>
                        <td class="table-cell text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if($item->foto_statistik)
                                    <button @click="fotoModalUrl = '{{ Storage::disk('public')->url($item->foto_statistik) }}'; fotoModalLabel = 'Foto Statistik'; showFotoModal = true" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors" title="Foto Statistik">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        Statistik
                                    </button>
                                @endif
                                @if($item->foto_bukti_live)
                                    <button @click="fotoModalUrl = '{{ Storage::disk('public')->url($item->foto_bukti_live) }}'; fotoModalLabel = 'Foto Bukti Live'; showFotoModal = true" class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors" title="Foto Bukti Live">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        Bukti Live
                                    </button>
                                @endif
                                @if(!$item->foto_statistik && !$item->foto_bukti_live)
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="table-cell text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEditModal({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Detail
                                </button>
                                @can('delete-data')
                                <button wire:click="confirmDelete({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Hapus
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada data Host Live</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambah data pertama untuk memulai</p>
                                @can('create-data')
                                <button wire:click="openCreateModal" class="btn-primary mt-4 text-xs">Tambah Data</button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
        <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
            {{ $items->links() }}
        </div>
    @endif

    <template x-teleport="body">
    {{-- CREATE MODAL --}}
    <div x-data="{ open: $wire.entangle('showCreateModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="open = false">
        <div @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Data Host Live</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Masukkan data bonus host live</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="create-tanggal" value="Tanggal *" />
                        <x-text-input id="create-tanggal" wire:model="tanggal" type="date" class="mt-1 block w-full" />
                        @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="create-sesi" value="Sesi *" />
                        <select id="create-sesi" wire:model="sesi" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Sesi --</option>
                            <option value="Pagi">Sesi 1 - Pagi (07.00 - 12.00)</option>
                            <option value="Siang">Sesi 2 - Siang (13.00 - 18.00)</option>
                            <option value="Malam">Sesi 3 - Malam (19.00 - 24.00)</option>
                            <option value="Subuh">Sesi 4 - Subuh (01.00 - 06.00)</option>
                        </select>
                        @error('sesi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="create-nik" value="NIK Karyawan *" />
                    <select id="create-nik" wire:model="nik" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih NIK --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->nik }}">{{ $emp->nik }} - {{ $emp->nama }}</option>
                        @endforeach
                    </select>
                    @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="create-nama" value="Nama" />
                        <x-text-input id="create-nama" wire:model="nama" type="text" class="mt-1 block w-full" readonly />
                    </div>
                    <div>
                        <x-input-label for="create-jabatan" value="Jabatan" />
                        <x-text-input id="create-jabatan" wire:model="jabatan" type="text" class="mt-1 block w-full" readonly />
                    </div>
                    <div>
                        <x-input-label for="create-divisi" value="Divisi" />
                        <x-text-input id="create-divisi" wire:model="divisi" type="text" class="mt-1 block w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="create-jam_mulai" value="Jam Mulai" />
                        <x-text-input id="create-jam_mulai" wire:model="jam_mulai" type="time" class="mt-1 block w-full" />
                        @error('jam_mulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="create-jam_selesai" value="Jam Selesai" />
                        <x-text-input id="create-jam_selesai" wire:model="jam_selesai" type="time" class="mt-1 block w-full" />
                        @error('jam_selesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="create-ach_sold" value="Sold *" />
                        <x-text-input id="create-ach_sold" wire:model="ach_sold" type="number" step="0.01" class="mt-1 block w-full" placeholder="0" />
                        @error('ach_sold') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="create-ach_view" value="View *" />
                        <x-text-input id="create-ach_view" wire:model="ach_view" type="number" step="0.01" class="mt-1 block w-full" placeholder="0" />
                        @error('ach_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="create-peak_view" value="Peak *" />
                        <x-text-input id="create-peak_view" wire:model="peak_view" type="number" step="0.01" class="mt-1 block w-full" placeholder="0" />
                        @error('peak_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="create-catatan" value="Catatan" />
                    <textarea id="create-catatan" wire:model="catatan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Catatan tambahan..."></textarea>
                    @error('catatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div wire:key="create-foto-statistik">
                        <x-input-label for="create-foto_statistik" value="Foto Statistik" />
                        <input id="create-foto_statistik" wire:model="foto_statistik" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_statistik" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_statistik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div wire:key="create-foto-bukti-live">
                        <x-input-label for="create-foto_bukti_live" value="Foto Bukti Live" />
                        <input id="create-foto_bukti_live" wire:model="foto_bukti_live" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_live" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_live') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- EDIT MODAL --}}
    <div x-data="{ open: $wire.entangle('showEditModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto" @click="open = false">
        <div @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Data Host Live</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data bonus host live</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="update" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-tanggal" value="Tanggal *" />
                        <x-text-input id="edit-tanggal" wire:model="tanggal" type="date" class="mt-1 block w-full" />
                        @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="edit-sesi" value="Sesi *" />
                        <select id="edit-sesi" wire:model="sesi" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Sesi --</option>
                            <option value="Pagi">Sesi 1 - Pagi (07.00 - 12.00)</option>
                            <option value="Siang">Sesi 2 - Siang (13.00 - 18.00)</option>
                            <option value="Malam">Sesi 3 - Malam (19.00 - 24.00)</option>
                            <option value="Subuh">Sesi 4 - Subuh (01.00 - 06.00)</option>
                        </select>
                        @error('sesi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="edit-nik" value="NIK Karyawan *" />
                    <select id="edit-nik" wire:model="nik" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih NIK --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->nik }}">{{ $emp->nik }} - {{ $emp->nama }}</option>
                        @endforeach
                    </select>
                    @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="edit-nama" value="Nama" />
                        <x-text-input id="edit-nama" wire:model="nama" type="text" class="mt-1 block w-full" readonly />
                    </div>
                    <div>
                        <x-input-label for="edit-jabatan" value="Jabatan" />
                        <x-text-input id="edit-jabatan" wire:model="jabatan" type="text" class="mt-1 block w-full" readonly />
                    </div>
                    <div>
                        <x-input-label for="edit-divisi" value="Divisi" />
                        <x-text-input id="edit-divisi" wire:model="divisi" type="text" class="mt-1 block w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-jam_mulai" value="Jam Mulai" />
                        <x-text-input id="edit-jam_mulai" wire:model="jam_mulai" type="time" class="mt-1 block w-full" />
                        @error('jam_mulai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="edit-jam_selesai" value="Jam Selesai" />
                        <x-text-input id="edit-jam_selesai" wire:model="jam_selesai" type="time" class="mt-1 block w-full" />
                        @error('jam_selesai') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="edit-ach_sold" value="Sold *" />
                        <x-text-input id="edit-ach_sold" wire:model="ach_sold" type="number" step="0.01" class="mt-1 block w-full" />
                        @error('ach_sold') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="edit-ach_view" value="View *" />
                        <x-text-input id="edit-ach_view" wire:model="ach_view" type="number" step="0.01" class="mt-1 block w-full" />
                        @error('ach_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="edit-peak_view" value="Peak *" />
                        <x-text-input id="edit-peak_view" wire:model="peak_view" type="number" step="0.01" class="mt-1 block w-full" />
                        @error('peak_view') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label for="edit-catatan" value="Catatan" />
                    <textarea id="edit-catatan" wire:model="catatan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"></textarea>
                    @error('catatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div wire:key="edit-foto-statistik">
                        <x-input-label for="edit-foto_statistik" value="Foto Statistik" />
                        @if($fotoStatistikPath)
                            <div class="mb-2">
                                <button @click="fotoModalUrl = '{{ Storage::disk('public')->url($fotoStatistikPath) }}'; fotoModalLabel = 'Foto Statistik'; showFotoModal = true" class="text-xs text-primary-600 hover:underline cursor-pointer bg-transparent border-none p-0">Lihat foto statistik saat ini</button>
                            </div>
                        @endif
                        <input id="edit-foto_statistik" wire:model="foto_statistik" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_statistik" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_statistik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div wire:key="edit-foto-bukti-live">
                        <x-input-label for="edit-foto_bukti_live" value="Foto Bukti Live" />
                        @if($fotoBuktiLivePath)
                            <div class="mb-2">
                                <button @click="fotoModalUrl = '{{ Storage::disk('public')->url($fotoBuktiLivePath) }}'; fotoModalLabel = 'Foto Bukti Live'; showFotoModal = true" class="text-xs text-emerald-600 hover:underline cursor-pointer bg-transparent border-none p-0">Lihat foto bukti live saat ini</button>
                            </div>
                        @endif
                        <input id="edit-foto_bukti_live" wire:model="foto_bukti_live" type="file" accept="image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                        <div wire:loading wire:target="foto_bukti_live" class="text-xs text-primary-600 mt-1">Mengupload...</div>
                        @error('foto_bukti_live') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- MODAL FOTO --}}
    <div x-show="showFotoModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/60 backdrop-blur-sm"
         @click="showFotoModal = false">
        <div x-show="showFotoModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Foto Host Live</h3>
                <button @click="showFotoModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3" x-text="fotoModalLabel"></p>
                <img :src="fotoModalUrl" :alt="fotoModalLabel" class="max-w-full max-h-[70vh] w-auto h-auto object-contain mx-auto rounded-xl border border-gray-200 dark:border-gray-600">
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="showFotoModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </template>

    <x:confirm-delete-modal title="Hapus Data" message="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan." />
</div>
