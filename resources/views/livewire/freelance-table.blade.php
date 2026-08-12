@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Freelance</h1>
        <p class="text-xs text-gray-400 mt-0.5">Data karyawan freelance</p>
    </div>
@endpush

<div>
    @if(session('message'))
    <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

    <div class="card">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Data Freelance</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar karyawan freelance</p>
            </div>
            @if(auth()->user()->isSuperAdminLike())
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
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Host</th>
                        @if(auth()->user()->isSuperAdminLike())
                        <th class="px-6 py-3">No. WhatsApp</th>
                        @endif
                        <th class="px-6 py-3 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="table-cell text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $item->nama }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $item->host_position }}</td>
                            @if(auth()->user()->isSuperAdminLike())
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                @if($item->no_whatsapp)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_whatsapp) }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413"/></svg>
                                    {{ $item->no_whatsapp }}
                                </a>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endif
                            <td class="table-cell text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus data freelance ini?" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isSuperAdminLike() ? 5 : 4 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    <p class="font-medium">Belum ada data freelance</p>
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
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Freelance</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} data freelance</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Nama *" />
                    <x-text-input type="text" wire:model="nama" class="mt-1 block w-full" placeholder="Nama freelance" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Host *" />
                    <select wire:model="host_position" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Host --</option>
                        @foreach($hostPositions as $host)
                            <option value="{{ $host }}">{{ $host }}</option>
                        @endforeach
                    </select>
                    @error('host_position') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="No. WhatsApp" />
                    <x-text-input type="text" wire:model="no_whatsapp" class="mt-1 block w-full" placeholder="08123456789" />
                    @error('no_whatsapp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
</div>
