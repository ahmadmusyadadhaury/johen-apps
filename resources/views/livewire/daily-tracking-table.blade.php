<div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </div>
                <span class="badge-info">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalSold, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sold</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 text-white shadow-lg shadow-teal-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <span class="badge-success">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalView, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total View</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-red-500 text-white shadow-lg shadow-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/></svg>
                </div>
                <span class="badge-warning">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalPeak, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Peak</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-500 text-white shadow-lg shadow-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="badge-success">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalDurasi, 0, ',', '.') }} Jam</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Durasi</p>
        </div>
    </div>

    <div class="card">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Pilih Game</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih game untuk melihat detail daily tracking</p>
            </div>
        </div>

        <div class="p-6">
            @forelse($games as $game)
            @php
                $styles = match($game['divisi']) {
                    'PUBG' => ['from-blue-500 to-indigo-500', 'text-blue-600 dark:text-blue-400'],
                    'Free Fire' => ['from-orange-500 to-red-500', 'text-orange-600 dark:text-orange-400'],
                    'MLBB' => ['from-purple-500 to-fuchsia-500', 'text-purple-600 dark:text-purple-400'],
                    'E-football' => ['from-green-500 to-emerald-500', 'text-green-600 dark:text-green-400'],
                    'Valorant' => ['from-red-500 to-rose-500', 'text-red-600 dark:text-red-400'],
                    'Roblox' => ['from-cyan-500 to-teal-500', 'text-cyan-600 dark:text-cyan-400'],
                    'Monkey PUBG' => ['from-amber-500 to-orange-500', 'text-amber-600 dark:text-amber-400'],
                    'FC Mobile' => ['from-pink-500 to-rose-500', 'text-pink-600 dark:text-pink-400'],
                    default => ['from-gray-500 to-gray-600', 'text-gray-600 dark:text-gray-400'],
                };
                $iconBg = $styles[0];
                $labelColor = $styles[1];
            @endphp
            <a href="{{ route('hris.daily-tracking.game', ['divisi' => $game['divisi']]) }}" class="group relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 transition-all duration-200 hover:border-primary-300 dark:hover:border-primary-700 hover:shadow-lg hover:shadow-primary-100/50 dark:hover:shadow-primary-900/30 mb-4 last:mb-0">
                <div class="flex items-center gap-4 min-w-0">
                    <div class="relative h-14 w-14 shrink-0">
                        @if($game['photo'])
                            <img src="{{ $game['photo'] }}" alt="{{ $game['divisi'] }}" class="h-14 w-14 rounded-2xl object-cover shadow-lg border border-gray-200 dark:border-gray-600 group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br {{ $iconBg }} text-white shadow-lg group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                        @endif
                        @unless(auth()->user()->isReadOnlyWorkspace())
                        <button type="button" wire:click.stop.prevent="openUploadModal('{{ $game['divisi'] }}')" title="Ubah Foto Game"
                            class="absolute -bottom-1.5 -right-1.5 flex h-7 w-7 items-center justify-center rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 shadow-md hover:text-primary-600 hover:border-primary-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0019.5 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 10.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                        </button>
                        @endunless
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                            {{ $game['divisi'] }}
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $labelColor }} bg-gray-100 dark:bg-gray-800">{{ $game['total'] }} Data</span>
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">Pantau aktivitas harian divisi {{ $game['divisi'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-5 shrink-0">
                    <div class="text-right">
                        <p class="text-base font-bold text-gray-900 dark:text-gray-100 tabular-nums">{{ number_format($game['totalSold'], 0, ',', '.') }}</p>
                        <p class="text-[10px] font-medium text-gray-400 dark:text-gray-500">Total Sold</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800 group-hover:text-white group-hover:bg-primary-500 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </span>
                </div>
            </a>
            @empty
            <div class="flex flex-col items-center py-12 text-center">
                <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p class="font-medium">Belum ada data</p>
                <p class="text-xs mt-1">Belum ada data daily tracking untuk bawahan Anda</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL UPLOAD FOTO GAME --}}
    @if($showUploadModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closeUploadModal">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Foto Game {{ $uploadDivisi }}</h3>
                <button wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form wire:submit="saveUploadPhoto" class="p-6 space-y-4">
                <div wire:key="upload-game-photo">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Gambar <span class="text-red-500">*</span></label>
                    <input wire:model="uploadPhoto" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-all duration-200" />
                    <div wire:loading wire:target="uploadPhoto" class="text-xs text-primary-600 mt-2">Mengupload...</div>
                    @error('uploadPhoto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @if($uploadPhoto && !$errors->has('uploadPhoto'))
                    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 p-3 flex items-center justify-center">
                        <img src="{{ $uploadPhoto->temporaryUrl() }}" alt="Pratinjau" class="max-h-40 rounded-xl object-contain" />
                    </div>
                @endif
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeUploadModal" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">Batal</button>
                    <button type="submit" class="btn-primary text-xs py-2">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>