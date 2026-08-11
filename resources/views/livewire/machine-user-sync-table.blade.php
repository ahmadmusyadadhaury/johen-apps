@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Sinkron Absen Mesin</h1>
        <p class="text-xs text-gray-400 mt-0.5">Petakan User ID mesin absen ke karyawan (NIK)</p>
    </div>
@endpush

<div>
    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
        <div class="card p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">User ID Mesin</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($totalIds, 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Terpetakan</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($mappedIds, 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Belum Terpetakan</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($totalIds - $mappedIds, 0, ',', '.') }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Punch Belum Diproses</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($pendingPunches, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">dari {{ number_format($totalPunches, 0, ',', '.') }} total punch</p>
        </div>
    </div>

    <div class="card">
        {{-- Filter bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari User ID..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                    >
                </div>

                <select wire:model.live="filterStatus" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    <option value="">Semua Status</option>
                    <option value="unmapped">Belum Terpetakan</option>
                    <option value="mapped">Sudah Terpetakan</option>
                </select>
            </div>

            <button wire:click="syncMachineUsers" wire:loading.attr="disabled" class="btn-secondary text-xs py-2 shrink-0">
                <svg wire:loading wire:target="syncMachineUsers" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                <span wire:loading.remove wire:target="syncMachineUsers">Tarik Nama dari Mesin</span>
                <span wire:loading wire:target="syncMachineUsers">Menarik...</span>
            </button>

            <button wire:click="backfill" wire:confirm="Proses semua punch yang belum terpetakan ke absensi? Proses ini bisa memakan waktu beberapa detik." wire:loading.attr="disabled" class="btn-primary text-xs py-2 shrink-0">
                <svg wire:loading wire:target="backfill" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                <span wire:loading.remove wire:target="backfill">Proses Backfill</span>
                <span wire:loading wire:target="backfill">Memproses...</span>
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">User ID Mesin</th>
                        <th class="px-6 py-3">Nama di Mesin</th>
                        <th class="px-6 py-3 text-center">Jumlah Tap</th>
                        <th class="px-6 py-3">Tap Pertama</th>
                        <th class="px-6 py-3">Tap Terakhir</th>
                        <th class="px-6 py-3">Mapping Karyawan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($machineUsers as $m)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $machineUsers->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $m->machine_user_id }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                {{ $m->machine_name ?? '-' }}
                            </td>
                            <td class="table-cell text-center text-gray-600 dark:text-gray-400">{{ number_format($m->total_taps, 0, ',', '.') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->pertama)->format('d M Y H:i') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($m->terakhir)->format('d M Y H:i') }}</td>
                            <td class="table-cell">
                                @if($m->employee_id)
                                    <div class="flex items-center gap-2">
                                        <span class="badge-success">Terpetakan</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $m->employee_nama }}</span>
                                        <span class="text-[11px] text-gray-400 font-mono">({{ $m->employee_nik }})</span>
                                    </div>
                                @else
                                    <span class="badge-warning">Belum dipetakan</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openMapModal('{{ $m->machine_user_id }}')" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $m->employee_id ? 'Ganti' : 'Map' }}
                                    </button>
                                    @if($m->employee_id)
                                    <button wire:click="unmapMapping('{{ $m->machine_user_id }}')" wire:confirm="Lepas mapping User ID {{ $m->machine_user_id }} dari {{ $m->employee_nama }}?" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Unmap
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tidak ada data User ID mesin</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tarik data mesin absen terlebih dahulu</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($machineUsers->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $machineUsers->links() }}
            </div>
        @endif
    </div>

    {{-- MAP MODAL --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showMapModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Petakan User ID
                        @if($mapMachineUserId)<span class="font-mono text-primary-600 dark:text-primary-400">{{ $mapMachineUserId }}</span>@endif
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cari karyawan berdasarkan NIK atau nama</p>
                </div>
                <button wire:click="closeMapModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveMapping" class="space-y-4">
                <div>
                    <x-input-label for="map-search" value="Cari Karyawan" />
                    <div class="relative mt-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input id="map-search" type="text" wire:model.live.debounce.200ms="mapSearch" placeholder="Ketik NIK atau nama..." class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" />
                    </div>
                </div>

                <div>
                    <x-input-label for="map-employee" value="Karyawan" />
                    <select id="map-employee" wire:model="selectedEmployeeId" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Pilih karyawan --</option>
                        @foreach($mapEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                        @endforeach
                    </select>
                    @error('selectedEmployeeId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @if($mapSearch && $mapEmployees->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">Tidak ada karyawan yang cocok dengan pencarian.</p>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeMapModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan Mapping
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>
