@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pengumuman</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola informasi & pengumuman untuk karyawan</p>
    </div>
@endpush

<div>
    <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Daftar Pengumuman</h2>
                <p class="mt-0.5 text-xs text-gray-400">Kelola informasi & pengumuman untuk karyawan</p>
            </div>
            <button wire:click="openNew" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Pengumuman
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Judul</th>
                        <th class="px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Ringkasan</th>
                        <th class="px-5 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Dibuat</th>
                        <th class="px-5 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $announcement->title }}</p>
                            @if($announcement->content)
                            <p class="text-[11px] text-gray-400 mt-0.5 line-clamp-1">{{ $announcement->content }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $announcement->summary ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $announcement->created_at->isoFormat('D MMM YYYY') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="openEdit({{ $announcement->id }})" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Edit</button>
                                <button wire:click="delete({{ $announcement->id }})" wire:confirm="Hapus pengumuman ini?" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 font-medium">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12">
                            <div class="text-center">
                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38a.467.467 0 01-.502-.011 5.095 5.095 0 01-1.357-3.637m3.394-5.026a9.44 9.44 0 000 4.52M3.554 9.48l-.397.73a.72.72 0 000 .59l.397.73m7.446-5.71v-.75c0-.663.284-1.275.73-1.74 0 0 1.813-1.87 3.042-2.27.291-.094.603.06.603.366v4.133m6.659 8.677l.397-.73a.72.72 0 000-.59l-.397-.73M18.304 8.88l1.26-1.08c.33-.283.363-.795.063-1.137m-8.865 3.827a6.03 6.03 0 00-.706.74m.706-.74c.62-.24 1.29-.37 1.99-.37h1.5a4.5 4.5 0 010 9h-.75c-.705 0-1.403.03-2.09.09"/></svg>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Pengumuman</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik "Tambah Pengumuman" untuk membuat pengumuman pertama</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($announcements->hasPages())
    <div class="mt-4 px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        {{ $announcements->links() }}
    </div>
    @endif

    {{-- Modal Tambah/Edit --}}
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
             @click.stop class="relative w-full max-w-3xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Pengumuman</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} informasi pengumuman</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Judul Pengumuman *" />
                    <x-text-input type="text" wire:model="title" class="mt-1 block w-full" placeholder="Contoh: Rapat Koordinasi Bulanan" />
                    @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Ringkasan" />
                    <x-text-input type="text" wire:model="summary" class="mt-1 block w-full" placeholder="Ringkasan singkat (tampil di dashboard)" />
                    @error('summary') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Isi Pengumuman" />
                    <textarea wire:model="content" rows="10" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Detail pengumuman..."></textarea>
                    @error('content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="is_published" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500 h-4 w-4" />
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Tampilkan ke karyawan</p>
                        <p class="text-xs text-gray-400">Jika dicentang, pengumuman tampil di dashboard karyawan</p>
                    </div>
                </label>

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
