<div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600 text-sm font-bold text-gray-700 dark:text-gray-200">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Pergantian Shift
        </div>
        @if($canManage)
            <button wire:click="openCreate" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 text-xs font-semibold transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Shift
            </button>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                    <th class="px-5 py-3">Berlaku Mulai</th>
                    <th class="px-5 py-3">Jam Kerja</th>
                    <th class="px-5 py-3">Jam Masuk (acuan telat)</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($histories as $history)
                    <tr class="border-b border-gray-50 dark:border-gray-800 last:border-0">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $history->effective_date->isoFormat('D MMMM Y') }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $history->jam_kerja ?? '-' }}</td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $history->jam_masuk ? substr($history->jam_masuk, 0, 5) : '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($canManage)
                            <div class="inline-flex items-center gap-1">
                                <button wire:click="openEdit({{ $history->id }})" class="rounded-lg p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                </button>
                                <button wire:click="delete({{ $history->id }})" wire:confirm="Hapus catatan shift ini?" class="rounded-lg p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <p class="text-xs text-gray-400 dark:text-gray-500">Belum ada catatan pergantian shift.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($canManage)
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editing ? 'Edit Jadwal Shift' : 'Tambah Pergantian Shift' }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->nama }}</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label for="shift-effective_date" value="Berlaku Mulai" />
                    <x-text-input id="shift-effective_date" wire:model="effective_date" type="date" class="mt-1 block w-full" />
                    @error('effective_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label for="shift-jam_kerja" value="Jam Kerja" />
                    <x-text-input id="shift-jam_kerja" wire:model="jam_kerja" type="text" placeholder="Contoh: 08.00-17.00" class="mt-1 block w-full" />
                    @error('jam_kerja') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label for="shift-jam_masuk" value="Jam Masuk (acuan telat)" />
                    <x-text-input id="shift-jam_masuk" wire:model="jam_masuk" type="time" class="mt-1 block w-full" />
                    @error('jam_masuk') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700 mt-6">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs mr-2">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
    @endif
</div>