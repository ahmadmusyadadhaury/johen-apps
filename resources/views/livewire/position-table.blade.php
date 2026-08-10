@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jabatan</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400">Kelola struktur jabatan dan hierarki di perusahaan</p>
    </div>
@endpush

@php
$tabColors = [
    'primary' => ['border' => 'border-primary-600', 'text' => 'text-primary-700', 'dark-border' => 'dark:border-primary-400', 'dark-text' => 'dark:text-primary-400'],
    'purple' => ['border' => 'border-purple-600', 'text' => 'text-purple-700', 'dark-border' => 'dark:border-purple-400', 'dark-text' => 'dark:text-purple-400'],
    'blue' => ['border' => 'border-blue-600', 'text' => 'text-blue-700', 'dark-border' => 'dark:border-blue-400', 'dark-text' => 'dark:text-blue-400'],
    'green' => ['border' => 'border-green-600', 'text' => 'text-green-700', 'dark-border' => 'dark:border-green-400', 'dark-text' => 'dark:text-green-400'],
    'orange' => ['border' => 'border-orange-600', 'text' => 'text-orange-700', 'dark-border' => 'dark:border-orange-400', 'dark-text' => 'dark:text-orange-400'],
    'pink' => ['border' => 'border-pink-600', 'text' => 'text-pink-700', 'dark-border' => 'dark:border-pink-400', 'dark-text' => 'dark:text-pink-400'],
    'indigo' => ['border' => 'border-indigo-600', 'text' => 'text-indigo-700', 'dark-border' => 'dark:border-indigo-400', 'dark-text' => 'dark:text-indigo-400'],
    'teal' => ['border' => 'border-teal-600', 'text' => 'text-teal-700', 'dark-border' => 'dark:border-teal-400', 'dark-text' => 'dark:text-teal-400'],
    'cyan' => ['border' => 'border-cyan-600', 'text' => 'text-cyan-700', 'dark-border' => 'dark:border-cyan-400', 'dark-text' => 'dark:text-cyan-400'],
    'rose' => ['border' => 'border-rose-600', 'text' => 'text-rose-700', 'dark-border' => 'dark:border-rose-400', 'dark-text' => 'dark:text-rose-400'],
    'amber' => ['border' => 'border-amber-600', 'text' => 'text-amber-700', 'dark-border' => 'dark:border-amber-400', 'dark-text' => 'dark:text-amber-400'],
    'lime' => ['border' => 'border-lime-600', 'text' => 'text-lime-700', 'dark-border' => 'dark:border-lime-400', 'dark-text' => 'dark:text-lime-400'],
    'emerald' => ['border' => 'border-emerald-600', 'text' => 'text-emerald-700', 'dark-border' => 'dark:border-emerald-400', 'dark-text' => 'dark:text-emerald-400'],
];

$badgeColors = [
    'primary' => 'bg-primary-50 text-primary-700 ring-primary-700/10',
    'purple' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
    'blue' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
    'green' => 'bg-green-50 text-green-700 ring-green-700/10',
    'orange' => 'bg-orange-50 text-orange-700 ring-orange-700/10',
    'pink' => 'bg-pink-50 text-pink-700 ring-pink-700/10',
    'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    'teal' => 'bg-teal-50 text-teal-700 ring-teal-700/10',
    'cyan' => 'bg-cyan-50 text-cyan-700 ring-cyan-700/10',
    'rose' => 'bg-rose-50 text-rose-700 ring-rose-700/10',
    'amber' => 'bg-amber-50 text-amber-700 ring-amber-700/10',
    'lime' => 'bg-lime-50 text-lime-700 ring-lime-700/10',
    'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
];

$hoverTabColors = [
    'primary' => 'hover:border-primary-300 hover:text-primary-600 dark:hover:border-primary-500',
    'purple' => 'hover:border-purple-300 hover:text-purple-600 dark:hover:border-purple-500',
    'blue' => 'hover:border-blue-300 hover:text-blue-600 dark:hover:border-blue-500',
    'green' => 'hover:border-green-300 hover:text-green-600 dark:hover:border-green-500',
    'orange' => 'hover:border-orange-300 hover:text-orange-600 dark:hover:border-orange-500',
    'pink' => 'hover:border-pink-300 hover:text-pink-600 dark:hover:border-pink-500',
    'indigo' => 'hover:border-indigo-300 hover:text-indigo-600 dark:hover:border-indigo-500',
    'teal' => 'hover:border-teal-300 hover:text-teal-600 dark:hover:border-teal-500',
    'cyan' => 'hover:border-cyan-300 hover:text-cyan-600 dark:hover:border-cyan-500',
    'rose' => 'hover:border-rose-300 hover:text-rose-600 dark:hover:border-rose-500',
    'amber' => 'hover:border-amber-300 hover:text-amber-600 dark:hover:border-amber-500',
    'lime' => 'hover:border-lime-300 hover:text-lime-600 dark:hover:border-lime-500',
    'emerald' => 'hover:border-emerald-300 hover:text-emerald-600 dark:hover:border-emerald-500',
];
@endphp

<div>
    <div class="card">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
        <div class="flex items-center gap-3 flex-1">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama jabatan..."
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                >
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
            <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Jabatan
            </button>
            @endif
        </div>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700">
        <div class="flex overflow-x-auto px-6 gap-1">
            <button wire:click="selectDivision(null)"
                class="whitespace-nowrap px-4 py-3 text-xs font-medium border-b-2 transition-colors duration-200 {{ is_null($selectedDivision) ? 'border-primary-600 text-primary-700 dark:text-primary-400 dark:border-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300' }}">
                Semua Divisi
            </button>
            @foreach($divisions as $div)
                @php $tc = $tabColors[$div->color]; $hc = $hoverTabColors[$div->color]; @endphp
                <button wire:click="selectDivision({{ $div->id }})"
                    class="whitespace-nowrap px-4 py-3 text-xs font-medium border-b-2 transition-colors duration-200 {{ $selectedDivision === $div->id ? "{$tc['border']} {$tc['text']} {$tc['dark-border']} {$tc['dark-text']}" : "border-transparent text-gray-500 dark:text-gray-400 {$hc}" }}">
                    {{ $div->nama }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="table-header">
                    <th class="px-6 py-3 w-12 text-center">No</th>
                    <th class="px-6 py-3">Nama Jabatan</th>
                    <th class="px-6 py-3">Divisi</th>
                    <th class="px-6 py-3">Atasan</th>
                    <th class="px-6 py-3">Bawahan</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    @unless(auth()->user()->isGmCeo())
                    <th class="px-6 py-3 text-center">Aksi</th>
                    @endunless
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($positions as $pos)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                        <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $loop->iteration }}</td>
                        <td class="table-cell">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $pos->nama }}</span>
                        </td>
                        <td class="table-cell">
                            @if($pos->division)
                                @php $bc = $badgeColors[$colorMap[$pos->division_id] ?? 'primary'] @endphp
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $bc }}">
                                    {{ $pos->division->nama }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $pos->parent->nama ?? '-' }}</td>
                        <td class="table-cell">
                            @if($pos->children->count() > 0)
                                <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                    {{ $pos->children->count() }} Jabatan
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="table-cell text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $pos->deskripsi ?? '-' }}</td>
                        <td class="table-cell text-center">
                            @if(auth()->user()->isGmCeo())
                                @if($pos->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Nonaktif</span>
                                @endif
                            @else
                            <button wire:click="toggleActive({{ $pos->id }})" class="inline-flex items-center gap-1.5">
                                @if($pos->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-danger">Nonaktif</span>
                                @endif
                            </button>
                            @endif
                        </td>
                        @unless(auth()->user()->isGmCeo())
                        <td class="table-cell text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEditModal({{ $pos->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    Edit
                                </button>
                                <button wire:click="confirmDelete({{ $pos->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                        @endunless
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isGmCeo() ? 7 : 8 }}" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada data jabatan</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambah jabatan pertama untuk memulai</p>
                                @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
                                <button wire:click="openCreateModal" class="btn-primary mt-4 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Tambah Jabatan
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse

                <x:confirm-delete-modal title="Hapus Jabatan" message="Apakah Anda yakin ingin menghapus jabatan ini? Tindakan ini tidak dapat dibatalkan." />
            </tbody>
        </table>
    </div>
    </div>

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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Jabatan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Buat jabatan baru</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label for="create-nama" value="Nama Jabatan *" />
                    <x-text-input id="create-nama" wire:model="nama" type="text" class="mt-1 block w-full" placeholder="Nama jabatan" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-division_id" value="Divisi" />
                    <select id="create-division_id" wire:model="division_id"
                        class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->nama }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-parent_id" value="Atasan (Superior)" />
                    <select id="create-parent_id" wire:model="parent_id"
                        class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Tidak Punya Atasan (Root) --</option>
                        @foreach($parentOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->nama }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-deskripsi" value="Deskripsi" />
                    <textarea id="create-deskripsi" wire:model="deskripsi" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Deskripsi jabatan"></textarea>
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Jabatan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data jabatan</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <x-input-label for="edit-nama" value="Nama Jabatan *" />
                    <x-text-input id="edit-nama" wire:model="nama" type="text" class="mt-1 block w-full" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-division_id" value="Divisi" />
                    <select id="edit-division_id" wire:model="division_id"
                        class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->nama }}</option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-parent_id" value="Atasan (Superior)" />
                    <select id="edit-parent_id" wire:model="parent_id"
                        class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Tidak Punya Atasan (Root) --</option>
                        @foreach($parentOptions as $opt)
                            <option value="{{ $opt->id }}">{{ $opt->nama }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-deskripsi" value="Deskripsi" />
                    <textarea id="edit-deskripsi" wire:model="deskripsi" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="edit-is_active" wire:model="is_active" class="rounded-lg border-gray-200 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
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
