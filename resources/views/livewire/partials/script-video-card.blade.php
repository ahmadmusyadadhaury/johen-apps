@props(['upload', 'manageable' => true, 'canToggle' => false])
<div x-data="{ scriptPreview: null }" class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-200 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700 {{ !$upload->konten_dibuat ? '!bg-gray-100 dark:!bg-gray-950 grayscale opacity-75' : '' }}">
    {{-- Cover / Thumbnail --}}
    <div class="relative aspect-[4/3] shrink-0 overflow-hidden bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-600">
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,.6) 0, transparent 40%), radial-gradient(circle at 80% 70%, rgba(255,255,255,.5) 0, transparent 45%);"></div>
        <div class="relative flex h-full flex-col items-center justify-center gap-2 px-4">
            <svg class="w-12 h-12 text-white/90" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            <span class="max-w-full truncate text-[11px] font-semibold text-white/90">Konten Script</span>
        </div>
        <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-bold text-blue-700 backdrop-blur-sm shadow-sm">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            M{{ $upload->minggu }}
        </span>
        @if($upload->user)
        <span class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-black/25 px-2 py-0.5 text-[10px] font-semibold text-white backdrop-blur-sm">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            {{ $upload->user->name }}
        </span>
        @endif
        {{-- Overlay on hover --}}
        <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 group-hover:opacity-100">
            <button type="button"
               @click="scriptPreview = { url: @js(route('hris.script-video.file', [$upload->id, 'inline'])), downloadUrl: @js(route('hris.script-video.file', [$upload->id, 'download'])), title: @js($upload->judul), fileName: @js($upload->file_name) }"
               class="inline-flex translate-y-2 items-center gap-1.5 rounded-xl bg-white/90 px-4 py-2 text-xs font-bold text-gray-900 shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white group-hover:translate-y-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12 18.25 18.75 12 18.75 2.25 12 2.25 12z"/><circle cx="12" cy="12" r="2.25"/></svg>
                Buka File
            </button>
        </div>
    </div>

    {{-- Info --}}
    <div class="flex flex-1 flex-col p-4">
        <div class="mb-1 flex items-center justify-between gap-2">
            <div class="flex items-center gap-1.5 min-w-0">
                @if($upload->kategori_game)
                <span class="inline-flex shrink-0 items-center rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-semibold text-violet-600 ring-1 ring-inset ring-violet-600/10 dark:bg-violet-900/30 dark:text-violet-400 dark:ring-violet-400/20">{{ $upload->kategori_game }}</span>
                @endif
                <span class="inline-flex shrink-0 items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-600 ring-1 ring-inset ring-blue-600/10 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/20">{{ $upload->bulan }} {{ $upload->tahun }}</span>
            </div>
            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-400 dark:text-gray-500">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                {{ $upload->created_at->format('d M Y') }}
            </span>
        </div>
        <h3 class="text-sm font-bold leading-snug text-gray-900 dark:text-gray-100 line-clamp-2">{{ $upload->judul }}</h3>
        <p class="mt-0.5 text-[11px] font-medium text-gray-400 dark:text-gray-500 truncate">{{ $upload->file_name }}</p>
        @if($upload->keterangan)
        <p class="mt-2 text-xs leading-relaxed text-gray-500 dark:text-gray-400 line-clamp-2 flex-1">{{ $upload->keterangan }}</p>
        @else
        <div class="flex-1"></div>
        @endif

        <div class="mt-4 flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
            <div class="flex items-center gap-1">
                <a href="{{ route('hris.script-video.file', [$upload->id, 'download']) }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:text-emerald-400 dark:hover:bg-emerald-900/20 transition-colors" title="Download">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </a>
            </div>
            <div class="flex items-center gap-1">
                @if($manageable ?? true)
                <button wire:click="openEdit({{ $upload->id }})"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                </button>
                <button wire:click="confirmDelete({{ $upload->id }})"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 transition-colors" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                </button>
                @endif
                <button type="button"
                        @if(!($canToggle ?? false)) disabled @endif
                        wire:click="requestToggleKontenDibuat({{ $upload->id }})"
                        class="group relative inline-flex h-6 w-11 shrink-0 items-center rounded-full p-0.5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 {{ $upload->konten_dibuat ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }} {{ ($canToggle ?? false) ? 'cursor-pointer hover:opacity-90' : 'cursor-default' }}"
                        title="{{ $upload->konten_dibuat ? 'Konten sudah dibuat' : 'Konten belum dibuat' }}" aria-pressed="{{ $upload->konten_dibuat ? 'true' : 'false' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-200 {{ $upload->konten_dibuat ? 'translate-x-0' : 'translate-x-5' }}"></span>
                </button>
            </div>
        </div>
    </div>

    <template x-teleport="body">
    <div x-show="scriptPreview !== null" x-cloak
         class="fixed inset-0 z-[100] flex items-start justify-center overflow-y-auto bg-gray-900/60 p-3 pt-6 backdrop-blur-sm sm:p-6 sm:pt-10"
         @click="scriptPreview = null" @keydown.escape.window="scriptPreview = null">
        <div @click.stop class="relative my-auto flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
            <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-4 py-3 dark:border-gray-700 sm:px-6">
                <div class="min-w-0">
                    <h3 class="truncate text-base font-semibold text-gray-900 dark:text-gray-100" x-text="scriptPreview?.title"></h3>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="scriptPreview?.fileName"></p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <a x-bind:href="scriptPreview?.downloadUrl" class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">Unduh</a>
                    <button type="button" @click="scriptPreview = null" aria-label="Tutup preview" class="rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <iframe x-bind:src="scriptPreview?.url" class="min-h-[65vh] w-full flex-1 bg-gray-50 dark:bg-gray-900 sm:min-h-[75vh]" title="Preview script"></iframe>
        </div>
    </div>
</template>
</div>

