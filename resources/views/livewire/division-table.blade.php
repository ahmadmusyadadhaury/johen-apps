<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
        <div class="flex items-center gap-3 flex-1">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama divisi..."
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                >
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('hris.export.divisions') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Export Excel
            </a>
            @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
            <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Divisi
            </button>
            @endif
        </div>
    </div>

    {{-- Filter Status (Tag Card) --}}
    <div class="flex flex-wrap items-center gap-2 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
        <button wire:click="$set('filterStatus', '')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterStatus === '' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300 dark:hover:border-primary-700' }}">
            Semua
        </button>
        <button wire:click="$set('filterStatus', 'aktif')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterStatus === 'aktif' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-emerald-300 dark:hover:border-emerald-700' }}">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Aktif
        </button>
        <button wire:click="$set('filterStatus', 'nonaktif')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterStatus === 'nonaktif' ? 'bg-red-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-red-300 dark:hover:border-red-700' }}">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-red-500"></span>
            Nonaktif
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="table-header">
                    <th class="px-6 py-3 w-12 text-center">No</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('nama')">
                        <div class="flex items-center gap-1">
                            Nama Divisi
                            @if($sortField === 'nama')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M19.5 8.25l-7.5 7.5-7.5-7.5' : 'M4.5 15.75l7.5-7.5 7.5 7.5' }}"/></svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3">Koordinator</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3 text-center">Karyawan</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($divisions as $div)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                        <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $divisions->firstItem() + $loop->index }}</td>
                        <td class="table-cell">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $div->nama }}</span>
                        </td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $div->koordinator ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $div->deskripsi ?? '-' }}</td>
                        <td class="table-cell text-center">
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">{{ $div->employees_count }} Orang</span>
                        </td>
                        <td class="table-cell text-center">
                            @if(auth()->user()->canUpdateData() && ! auth()->user()->isGmCeo())
                            <button wire:click="toggleActive({{ $div->id }})" class="inline-flex items-center gap-1.5">
                            @endif
                                @if($div->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Nonaktif</span>
                                @endif
                            @if(auth()->user()->canUpdateData() && ! auth()->user()->isGmCeo())
                            </button>
                            @endif
                        </td>
                        <td class="table-cell text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEditModal({{ $div->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lihat Detail
                                </button>

                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.outside="open = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         @click.outside="open = false" class="absolute right-0 z-50 mt-1 w-40 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 py-1.5">
                                @if(auth()->user()->canUpdateData() && ! auth()->user()->isGmCeo())
                                <button wire:click="openEditModal({{ $div->id }})" @click="open = false" class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    Edit
                                </button>
                                @endif
                                @if(auth()->user()->canDeleteData() && ! auth()->user()->isGmCeo())
                                <button wire:click="confirmDelete({{ $div->id }})" @click="open = false" class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Hapus
                                </button>
                                @endif

    <x:confirm-delete-modal title="Hapus Divisi" message="Apakah Anda yakin ingin menghapus divisi ini? Tindakan ini tidak dapat dibatalkan." />
</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada data divisi</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambah divisi pertama untuk memulai</p>
                                @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
                                <button wire:click="openCreateModal" class="btn-primary mt-4 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Tambah Divisi
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($divisions->hasPages())
        <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
            {{ $divisions->links() }}
        </div>
    @endif

    <template x-teleport="body">
    {{-- ============ CREATE MODAL ============ --}}
    <div x-data="{ open: $wire.entangle('showCreateModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Divisi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Buat divisi baru</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label for="create-nama" value="Nama Divisi *" />
                    <x-text-input id="create-nama" wire:model="nama" type="text" class="mt-1 block w-full" placeholder="Teknologi Informasi" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-koordinator" value="Koordinator" />
                    <x-text-input id="create-koordinator" wire:model="koordinator" type="text" class="mt-1 block w-full" placeholder="Nama koordinator" />
                </div>

                <div>
                    <x-input-label for="create-deskripsi" value="Deskripsi" />
                    <textarea id="create-deskripsi" wire:model="deskripsi" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Deskripsi divisi"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="create-is_active" wire:model="is_active" class="rounded-lg border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                    <label for="create-is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</label>
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
    {{-- ============ EDIT MODAL ============ --}}
    <div x-data="{ open: $wire.entangle('showEditModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Divisi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data divisi</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <x-input-label for="edit-nama" value="Nama Divisi *" />
                    <x-text-input id="edit-nama" wire:model="nama" type="text" class="mt-1 block w-full" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-koordinator" value="Koordinator" />
                    <x-text-input id="edit-koordinator" wire:model="koordinator" type="text" class="mt-1 block w-full" />
                </div>

                <div>
                    <x-input-label for="edit-deskripsi" value="Deskripsi" />
                    <textarea id="edit-deskripsi" wire:model="deskripsi" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="edit-is_active" wire:model="is_active" class="rounded-lg border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                    <label for="edit-is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif</label>
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
</div>
