@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Seminar & Pelatihan</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Buku panduan, video, dan kegiatan pelatihan</p>
    </div>
@endpush

@php
$jsEsc = function ($s) {
    return addslashes(str_replace(["\r", "\n"], ' ', (string) $s));
};
@endphp

<div x-data="{ tab: 'manual', previewBook: null, openPdf(url, nama, deskripsi, versi) { this.previewBook = { nama: nama, deskripsi: deskripsi, versi: versi, pdfUrl: url }; } }">
    {{-- Tab Navigation --}}
    <div class="mb-6">
        <div class="flex sm:inline-flex items-center gap-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1">
            <button @click="tab = 'manual'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="tab === 'manual' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                Manual Book
            </button>
            <button @click="tab = 'video'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="tab === 'video' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Video
            </button>
            @if(auth()->user()->canJoinTraining() || auth()->user()->canManageTraining() || auth()->user()->canViewTrainingTeam())
            <button @click="tab = 'pelatihan'"
                class="flex-1 sm:flex-none flex items-center justify-center gap-2 whitespace-nowrap rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="tab === 'pelatihan' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <circle cx="5.8" cy="5" r="2.4"/>
                    <path d="M1 21v-3.2a4.7 4.7 0 0 1 4.7-4.7h1.1c.7 0 1.4.2 2 .5l3.7 2 2.1-4.3a1.4 1.4 0 0 1 2.5 1.2l-2.7 5.6a2 2 0 0 1-2.7.9l-3-1.6V21H1Z"/>
                    <rect x="13" y="2.5" width="10" height="9.5" rx="1.2"/>
                    <path d="M15.2 5.2h5.6M15.2 7.3h3.8M15.2 9.4H19" fill="none" stroke="white" stroke-linecap="round" stroke-width="1.15"/>
                    <path d="M17 12v1.6m2 0V12m-4 1.6h6m-5.5 0L13 21m5.5-7.4L21.5 21" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3"/>
                </svg>
                Pelatihan
            </button>
            @endif
        </div>
    </div>

    @if(auth()->user()->canJoinTraining() || auth()->user()->canManageTraining() || auth()->user()->canViewTrainingTeam())
    <div x-show="tab === 'pelatihan'" x-cloak>
        @livewire('training-workspace')
    </div>
    @endif
    <div x-show="tab === 'manual'">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Koleksi panduan & dokumentasi</p>
        </div>
        @if(auth()->user()->isSuperAdminLike() || auth()->user()->isGmCeo())
        <button wire:click="openNew" class="btn-primary text-xs py-2 whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Buku
        </button>
        @endif
    </div>

    {{-- Kategori Filter (Card) --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto">
        <button wire:click="$set('filterKategori', '')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterKategori === '' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300 dark:hover:border-primary-700' }}">
            Semua
        </button>
        @foreach($kategoriOptions as $opt)
        <button wire:click="$set('filterKategori', '{{ $opt }}')"
                class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterKategori === $opt ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300 dark:hover:border-primary-700' }}">
            {{ $opt }}
        </button>
        @endforeach
    </div>

    {{-- Bookshelf Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5">
        @forelse($books as $book)
        <div class="group relative rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            {{-- Thumbnail --}}
            <div class="relative aspect-[3/4] overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                @if($book->thumbnail)
                <img src="{{ route('hris.manual-book.file', [$book->id, 'thumbnail']) }}" alt="{{ $book->nama }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="flex items-center justify-center h-full">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                @endif
                {{-- Overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-4">
                    <button @click="openPdf('{{ $book->file_pdf ? route('hris.manual-book.file', ['manualBook' => $book->id, 'type' => 'pdf', 'v' => 'filename-v2']) : '' }}', '{{ $jsEsc($book->nama) }}', '{{ $jsEsc($book->deskripsi ?? '') }}', '{{ $jsEsc($book->versi ?: 'v1.0') }}')"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-semibold hover:bg-white shadow-lg transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Baca
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-4">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">{{ $book->nama }}</h3>
                    <span class="shrink-0 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">{{ $book->versi ?: 'v1.0' }}</span>
                </div>
                @if($book->kategori)
                <span class="inline-flex mt-2 items-center rounded-full bg-primary-50 dark:bg-primary-900/30 px-2 py-0.5 text-[10px] font-semibold text-primary-700 dark:text-primary-400">{{ $book->kategori }}</span>
                @endif
                @if($book->deskripsi)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $book->deskripsi }}</p>
                @endif
                @if(auth()->user()->isSuperAdminLike() || auth()->user()->isGmCeo())
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button wire:click="openEdit({{ $book->id }})" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Edit</button>
                    @if(auth()->user()->isSuperAdminLike())
                    <button wire:click="confirmDelete({{ $book->id }})" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 font-medium">Hapus</button>
                    @endif
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
    </div>

    <div x-show="tab === 'video'" x-cloak>
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Koleksi video tutorial & pelatihan</p>
            </div>
            @if(auth()->user()->isSuperAdminLike())
            <button wire:click="openVideoNew" class="btn-primary text-xs py-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Video
            </button>
            @endif
        </div>

        {{-- Kategori Filter (Card) --}}
        <div class="flex items-center gap-2 mb-6 overflow-x-auto">
            <button wire:click="$set('filterVideoKategori', '')"
                    class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterVideoKategori === '' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300 dark:hover:border-primary-700' }}">
                Semua
            </button>
            @foreach($videoKategoriOptions as $opt)
            <button wire:click="$set('filterVideoKategori', '{{ $opt }}')"
                    class="shrink-0 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all duration-200 {{ $filterVideoKategori === $opt ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-primary-300 dark:hover:border-primary-700' }}">
                {{ $opt }}
            </button>
            @endforeach
        </div>

        @if($videos->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-10 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada video pelatihan</p>
            <p class="mt-1 text-xs text-gray-400">Video akan tampil di sini setelah ditambahkan.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($videos as $video)
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="bg-black" style="aspect-ratio: 4 / 3;">
                    <iframe src="{{ $video->url }}" title="{{ $video->nama }}" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen class="w-full h-full"></iframe>
                </div>
                <div class="p-4">
                    @if($video->kategori)
                    <span class="inline-block rounded-lg bg-blue-50 dark:bg-blue-950 px-2 py-0.5 text-[11px] font-semibold text-blue-600 dark:text-blue-400 mb-2">
                        {{ $video->kategori }}
                    </span>
                    @endif
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $video->nama }}</p>
                    @if(auth()->user()->isSuperAdminLike())
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <button wire:click="openVideoEdit({{ $video->id }})" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Edit</button>
                        <button wire:click="confirmVideoDelete({{ $video->id }})" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 font-medium">Hapus</button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    {{-- PDF Preview Modal (shared) --}}
    <div x-show="previewBook !== null" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center bg-gray-900/60 backdrop-blur-sm overflow-y-auto p-0 sm:p-4 sm:pt-10"
         @click="previewBook = null">
        <div @click.stop class="relative flex h-[100dvh] w-full flex-col overflow-hidden rounded-none bg-white shadow-2xl dark:bg-gray-800 sm:my-10 sm:h-[calc(80vh+4rem)] sm:max-h-[calc(100dvh-5rem)] sm:max-w-4xl sm:rounded-2xl">
            <div class="flex shrink-0 items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-700 sm:px-6 sm:py-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="truncate text-base font-semibold text-gray-900 dark:text-gray-100" x-text="previewBook?.nama"></h3>
                        <span class="shrink-0 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300" x-text="previewBook?.versi"></span>
                    </div>
                    <p class="mt-0.5 break-words text-xs text-gray-500 dark:text-gray-400" x-text="previewBook?.deskripsi" x-show="previewBook?.deskripsi"></p>
                </div>
                <button @click="previewBook = null" class="shrink-0 rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="min-h-0 w-full flex-1 overflow-auto overscroll-contain bg-gray-100 dark:bg-gray-900">
                <iframe :src="previewBook?.pdfUrl ? previewBook.pdfUrl + '#view=FitH' : ''" title="Preview PDF manual book" class="block h-full min-h-[70vh] w-full max-w-full border-0 sm:min-h-0" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
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
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-auto">
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
                    <x-input-label value="Versi *" />
                    <x-text-input type="text" wire:model="versi" class="mt-1 block w-full" placeholder="v1.0" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan format v1.0, v1.1, v2.0, dan seterusnya.</p>
                    @error('versi') <p class="text-xs text-red-600 mt-1">Masukkan versi dengan format seperti v1.0 atau v1.1.</p> @enderror
                </div>
                <div>
                    <x-input-label value="Kategori *" />
                    <select wire:model="kategori" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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

    {{-- Delete Confirm Modal --}}
    <div x-data="{ open: $wire.entangle('showDeleteConfirmModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelDelete()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Hapus Manual Book</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Apakah Anda yakin ingin menghapus manual book ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.cancelDelete()" class="btn-secondary text-xs px-6">Batal</button>
                <button wire:click="executeDelete" class="btn-danger text-xs px-6 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- Video Modal Tambah/Edit --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showVideoModal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false; $wire.closeVideo()">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $videoEditId ? 'Edit' : 'Tambah' }} Video</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $videoEditId ? 'Perbarui' : 'Isi' }} data video pelatihan</p>
                </div>
                <button wire:click="closeVideo" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveVideo" class="space-y-4">
                <div>
                    <x-input-label value="Judul Video *" />
                    <x-text-input type="text" wire:model="videoNama" class="mt-1 block w-full" placeholder="Judul video pelatihan" />
                    @error('videoNama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Kategori" />
                    <select wire:model="videoKategori" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($videoKategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('videoKategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Link Video (YouTube) *" />
                    <x-text-input type="text" wire:model="videoUrl" class="mt-1 block w-full" placeholder="https://www.youtube.com/watch?v=..." />
                    <p class="text-xs text-gray-400 mt-1">Link YouTube biasa (watch, youtu.be, shorts) akan otomatis diubah menjadi link embed.</p>
                    @error('videoUrl') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeVideo" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $videoEditId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Video Delete Confirm Modal --}}
    <div x-data="{ open: $wire.entangle('showVideoDeleteConfirmModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.cancelVideoDelete()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Hapus Video</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Apakah Anda yakin ingin menghapus video pelatihan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex items-center justify-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.cancelVideoDelete()" class="btn-secondary text-xs px-6">Batal</button>
                <button wire:click="executeVideoDelete" class="btn-danger text-xs px-6 inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div x-data="{ open: $wire.entangle('showSuccessModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false; $wire.closeSuccessModal()">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">Berhasil</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6" x-text="$wire.successMessage"></p>
            <div class="flex items-center justify-center pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false; $wire.closeSuccessModal()" class="btn-primary text-xs px-8">Oke</button>
            </div>
        </div>
    </div>
</div>
