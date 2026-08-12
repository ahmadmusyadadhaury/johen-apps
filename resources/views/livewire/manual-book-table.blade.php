@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Manual Book</h1>
        <p class="text-xs text-gray-400 mt-0.5">Panduan & dokumentasi</p>
    </div>
@endpush

<div x-data="{ previewBook: null }">
    @if(session('message'))
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Koleksi panduan & dokumentasi</p>
        </div>
        @if(auth()->user()->isSuperAdminLike())
        <button wire:click="openNew" class="btn-primary text-xs py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Buku
        </button>
        @endif
    </div>

    {{-- Bookshelf Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($books as $book)
        <div class="group relative rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            {{-- Thumbnail --}}
            <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                @if($book->thumbnail)
                <img src="{{ Storage::url($book->thumbnail) }}" alt="{{ $book->nama }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="flex items-center justify-center h-full">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                @endif
                {{-- Overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                    <button @click="previewBook = { nama: '{{ $book->nama }}', deskripsi: '{{ $book->deskripsi ?? '' }}', pdfUrl: '{{ $book->file_pdf ? Storage::url($book->file_pdf) : '' }}' }"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-semibold hover:bg-white shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Baca
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">{{ $book->nama }}</h3>
                @if($book->deskripsi)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $book->deskripsi }}</p>
                @endif
                @if(auth()->user()->isSuperAdminLike())
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="openEdit({{ $book->id }})" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Edit</button>
                    <button wire:click="delete({{ $book->id }})" wire:confirm="Hapus manual book ini?" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 font-medium">Hapus</button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-12 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Manual Book</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Belum ada buku panduan yang tersedia</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($books->hasPages())
    <div class="mt-4 px-4 py-3 rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm">
        {{ $books->links() }}
    </div>
    @endif

    {{-- PDF Preview Modal (shared) --}}
    <div x-show="previewBook !== null" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="previewBook = null">
        <div @click.stop class="relative w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-800 shadow-2xl my-10">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100" x-text="previewBook?.nama"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="previewBook?.deskripsi" x-show="previewBook?.deskripsi"></p>
                </div>
                <button @click="previewBook = null" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <iframe :src="previewBook?.pdfUrl" class="w-full h-[80vh] rounded-b-2xl" frameborder="0"></iframe>
        </div>
    </div>

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
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Manual Book</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} data buku panduan</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label value="Nama Manual Book *" />
                    <x-text-input type="text" wire:model="nama" class="mt-1 block w-full" placeholder="Judul buku panduan" />
                    @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Deskripsi" />
                    <textarea wire:model="deskripsi" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Deskripsi singkat..."></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Thumbnail Buku" />
                        <input type="file" wire:model="thumbnail" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/20 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/30 transition-colors cursor-pointer" />
                        @error('thumbnail') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        @if($thumbnail_preview)
                        <img src="{{ $thumbnail_preview }}" class="mt-2 h-20 rounded-lg object-cover border border-gray-100 dark:border-gray-700">
                        @endif
                        @if($thumbnail)
                        <img src="{{ $thumbnail->temporaryUrl() }}" class="mt-2 h-20 rounded-lg object-cover border border-gray-100 dark:border-gray-700">
                        @endif
                    </div>
                    <div>
                        <x-input-label value="File PDF *" />
                        <input type="file" wire:model="file_pdf" accept=".pdf,application/pdf"
                               class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 dark:file:bg-primary-900/20 file:text-primary-700 dark:file:text-primary-400 hover:file:bg-primary-100 dark:hover:file:bg-primary-900/30 transition-colors cursor-pointer" />
                        @error('file_pdf') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
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
