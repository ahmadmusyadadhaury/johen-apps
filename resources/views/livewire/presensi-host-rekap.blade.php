@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Rekap Presensi Host Live</h1>
        <p class="text-xs text-gray-400 mt-0.5">Grid presensi 4 sesi per host per tanggal</p>
    </div>
@endpush

<div>
    <div class="card mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <x-input-label for="rekap-tanggal" value="Tanggal Hari Kerja" />
                <x-text-input id="rekap-tanggal" wire:model.live="tanggal" type="date" class="mt-1 block w-52" />
            </div>
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Hadir {{ $hadir }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500"></span> Terlambat {{ $terlambat }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span> Izin/Sakit {{ $izin }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-red-500"></span> Alpha/Kosong {{ $kosong }}
                </span>
            </div>
        </div>
        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-2">Catatan: Sesi 4 (Subuh 01.00-06.00) ikut malam sebelumnya, sehingga live dini hari tanggal X tercatat pada hari kerja X-1.</p>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 dark:border-gray-800">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                    <th class="px-4 py-3">Host</th>
                    @foreach($sessionsConfig as $sesiKey => $sesiConfig)
                        <th class="px-4 py-3 text-center">{{ $sesiConfig['label'] }}<br>
                            <span class="font-normal normal-case text-[10px] text-gray-400">{{ $sesiConfig['nama'] }} · {{ $sesiConfig['mulai'] }}-{{ $sesiConfig['selesai_display'] }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                @forelse($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $row->employee->nama }}</p>
                            <p class="text-xs text-gray-400">{{ $row->employee->nik }}</p>
                        </td>
                        @foreach($sessionsConfig as $sesiKey => $sesiConfig)
                            @php $cell = $row->cells[$sesiKey] ?? null; @endphp
                            <td class="px-4 py-3 text-center">
                                @if($cell)
                                    @if($cell->status === 'hadir')
                                        @if($cell->isTelat())
                                            <button wire:click="openModal({{ $row->employee->id }}, {{ $sesiKey }})" class="inline-flex flex-col items-center gap-0.5 rounded-xl bg-amber-100 dark:bg-amber-900/40 px-3 py-2 text-amber-700 dark:text-amber-300 hover:ring-2 hover:ring-amber-400 transition-all">
                                                <span class="text-xs font-semibold">{{ $cell->clock_in ?? '-' }}</span>
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400">Terlambat {{ $cell->late_minutes }}m</span>
                                                <span class="text-[10px] text-amber-500 dark:text-amber-500">{{ $cell->clock_out ? '→ ' . $cell->clock_out : 'masih live' }}</span>
                                            </button>
                                        @else
                                            <button wire:click="openModal({{ $row->employee->id }}, {{ $sesiKey }})" class="inline-flex flex-col items-center gap-0.5 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 px-3 py-2 text-emerald-700 dark:text-emerald-300 hover:ring-2 hover:ring-emerald-400 transition-all">
                                                <span class="text-xs font-semibold">{{ $cell->clock_in ?? '-' }}</span>
                                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400">Hadir</span>
                                                <span class="text-[10px] text-emerald-500 dark:text-emerald-500">{{ $cell->clock_out ? '→ ' . $cell->clock_out : 'masih live' }}</span>
                                            </button>
                                        @endif
                                    @else
                                        <button wire:click="openModal({{ $row->employee->id }}, {{ $sesiKey }})" class="inline-flex items-center rounded-xl bg-blue-100 dark:bg-blue-900/40 px-3 py-2 text-blue-700 dark:text-blue-300 capitalize hover:ring-2 hover:ring-blue-400 transition-all">
                                            <span class="text-xs font-semibold">{{ $cell->status }}</span>
                                        </button>
                                    @endif
                                @else
                                    <button wire:click="openModal({{ $row->employee->id }}, {{ $sesiKey }})" class="inline-flex items-center rounded-xl bg-red-50 dark:bg-red-950/40 px-3 py-2 text-red-400 dark:text-red-500 border border-dashed border-red-200 dark:border-red-900 hover:ring-2 hover:ring-red-400 transition-all">
                                        <span class="text-xs font-semibold">Alpha</span>
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">Tidak ada host terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closeModal">
        <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl" @click.stop>
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Kelola Presensi Sesi</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editNama }} · {{ $editNamaSesi }} · {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label for="edit-status" value="Status *" />
                    <select id="edit-status" wire:model="editStatus" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                        <option value="alpha">Alpha</option>
                    </select>
                    @error('editStatus') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="edit-clock_in" value="Jam Masuk" />
                        <x-text-input id="edit-clock_in" wire:model="editClockIn" type="time" class="mt-1 block w-full" />
                        @error('editClockIn') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label for="edit-clock_out" value="Jam Keluar" />
                        <x-text-input id="edit-clock_out" wire:model="editClockOut" type="time" class="mt-1 block w-full" />
                        @error('editClockOut') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @if($editSesi === 3)
                    <p class="text-[11px] text-violet-500 dark:text-violet-400">Catatan: untuk Sesi 3 (Malam), jam keluar boleh melewati tengah malam (mis. 00:46 = hari berikutnya). Tetap dihitung sebagai ABSEN PULANG pada tanggal yang sama dengan jam masuk.</p>
                @endif

                <div>
                    <x-input-label for="edit-note" value="Catatan" />
                    <x-text-input id="edit-note" wire:model="editNote" type="text" placeholder="Opsional" class="mt-1 block w-full" />
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
    @endif
</div>