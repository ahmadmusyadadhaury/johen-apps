@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jadwal Maintenance</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola jadwal maintenance PC kantor</p>
    </div>
@endpush

<x-app-layout title="Jadwal Maintenance">
    <div x-data="{
        showAddSchedule: false,
        showAddPc: false,
        deleteTarget: null,
        deleteType: '',
        formPcId: '',
        formJenis: '',
        formJadwal: '',
        formCatatan: '',
        formPcNama: ''
    }" class="space-y-6">

        @php
            $totalPc = $pcs->count();
            $maintenanceBulanIni = \App\Models\ItMaintenanceSchedule::whereMonth('jadwal', now()->month)->whereYear('jadwal', now()->year)->count();
            $selesaiBulanIni = \App\Models\ItMaintenanceSchedule::whereMonth('jadwal', now()->month)->whereYear('jadwal', now()->year)->where('status', 'selesai')->count();
            $belumBulanIni = $maintenanceBulanIni - $selesaiBulanIni;
        @endphp

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalPc }} <span class="text-sm font-medium text-gray-400">PC</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total PC Aktif</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.1-5.1m0 0L11.42 4.97m-5.1 5.1H21M3 3v18"/></svg>
                    </div>
                    <span class="badge-warning">Menunggu</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $belumBulanIni }} <span class="text-sm font-medium text-gray-400">jadwal</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maintenance Bulan Ini</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Selesai</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $selesaiBulanIni }} <span class="text-sm font-medium text-gray-400">jadwal</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Selesai Bulan Ini</p>
            </div>
        </div>

        {{-- Table --}}
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 p-5 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Daftar PC</h2>
                    <p class="mt-0.5 text-xs text-gray-400">Jadwal maintenance setiap PC</p>
                </div>
                <div class="flex gap-2">
                    <button @click="showAddPc = true" class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah PC
                    </button>
                    <button @click="showAddSchedule = true; formPcId = ''; formJenis = ''; formJadwal = '{{ now()->format('Y-m-d') }}'; formCatatan = ''" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah Jadwal
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Nama PC</th>
                            <th class="px-5 py-3">Jadwal Terakhir</th>
                            <th class="px-5 py-3">Jenis</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Keterangan</th>
                            <th class="px-5 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                        @forelse($pcs as $index => $pc)
                            @php
                                $latest = $pc->schedules->first();
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-5 py-3.5 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">{{ $pc->nama }}</td>
                                @if($latest)
                                    @php
                                        $isOverdue = $latest->status === 'belum' && $latest->jadwal->isPast();
                                    @endphp
                                    <td class="px-5 py-3.5 {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                        {{ $latest->jadwal->format('d M Y') }}
                                        @if($isOverdue)
                                            <span class="ml-1 text-[10px] font-bold uppercase">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-700 dark:text-gray-300">{{ $latest->jenis }}</td>
                                    <td class="px-5 py-3.5">
                                        @if($latest->status === 'selesai')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Selesai</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Belum</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $latest->catatan ?? '-' }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            @if($latest->status === 'belum')
                                            <form action="{{ route('it.maintenance.complete', $latest->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-lg p-1.5 text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20" title="Tandai Selesai">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </button>
                                            </form>
                                            @endif
                                            <button @click="deleteTarget = {{ $latest->id }}; deleteType = 'schedule'" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td class="px-5 py-3.5 text-gray-400 italic" colspan="4">Belum ada jadwal</td>
                                    <td class="px-5 py-3.5">
                                        <button @click="showAddSchedule = true; formPcId = '{{ $pc->id }}'; formJenis = ''; formJadwal = '{{ now()->format('Y-m-d') }}'; formCatatan = ''" class="rounded-lg p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Tambah Jadwal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                    Belum ada PC terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Modal Tambah Jadwal --}}
        <template x-if="showAddSchedule">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showAddSchedule = false"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tambah Jadwal Maintenance</h3>
                    <form action="{{ route('it.maintenance.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">PC</label>
                            <select name="pc_id" x-model="formPcId" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <option value="">Pilih PC</option>
                                @foreach($pcs as $pc)
                                    <option value="{{ $pc->id }}">{{ $pc->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis Maintenance</label>
                            <input type="text" name="jenis" x-model="formJenis" placeholder="Contoh: Bersihin PC, Repasta, dll" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jadwal</label>
                            <input type="date" name="jadwal" x-model="formJadwal" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan <span class="text-gray-400">(opsional)</span></label>
                            <input type="text" name="catatan" x-model="formCatatan" placeholder="Keterangan tambahan" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showAddSchedule = false" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modal Tambah PC --}}
        <template x-if="showAddPc">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showAddPc = false"></div>
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tambah PC</h3>
                    <form action="{{ route('it.maintenance.pc.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama PC</label>
                            <input type="text" name="nama" x-model="formPcNama" placeholder="Contoh: PC 31" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showAddPc = false" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modal Konfirmasi Hapus --}}
        <template x-if="deleteTarget">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="deleteTarget = null"></div>
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Hapus Jadwal?</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Jadwal maintenance ini akan dihapus secara permanen.</p>
                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="deleteTarget = null" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                        <form :action="'{{ url('it/maintenance') }}/' + deleteTarget" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>
</x-app-layout>
