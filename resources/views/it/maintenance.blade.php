@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jadwal Maintenance</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola jadwal maintenance PC kantor</p>
    </div>
@endpush

<x-app-layout title="Jadwal Maintenance">
    <div x-data="{
        showAddMaintenance: false,
        deleteTarget: null,
        deleteType: '',
        formNama: '',
        formCatatan: '',
        formPeriode: '{{ $periode }}',
        editTarget: null,
        editPreviewSebelum: null,
        editPreviewSesudah: null,
        fotoViewer: null,
        showFeedbackModal: false,
        feedbackData: null,
        editingKoordinator: false,
        editingAtasan: false,
        feedbackKoordinatorText: '',
        feedbackAtasanText: ''
    }" class="space-y-6">

        @php
            $totalPc = $pcs->count();
            $countAntrean = \App\Models\ItMaintenanceSchedule::where('status', 'antrean')->where('periode', $periode)->count();
            $countDiproses = \App\Models\ItMaintenanceSchedule::where('status', 'diproses')->where('periode', $periode)->count();
            $countSelesai = \App\Models\ItMaintenanceSchedule::where('status', 'selesai')->where('periode', $periode)->count();
        @endphp

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
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
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
                    </div>
                    <span class="badge-warning">Antrean</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $countAntrean }} <span class="text-sm font-medium text-gray-400">PC</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dalam Antrean</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-primary">Diproses</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $countDiproses }} <span class="text-sm font-medium text-gray-400">PC</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sedang Diproses</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Selesai</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $countSelesai }} <span class="text-sm font-medium text-gray-400">PC</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Maintenance Selesai</p>
            </div>
        </div>

        {{-- Table --}}
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 p-5 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Daftar PC</h2>
                    <p class="mt-0.5 text-xs text-gray-400">Jadwal maintenance setiap PC</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-gray-500 dark:text-gray-400">Periode</label>
                        <select x-model="window.location.href = '{{ route('it.maintenance') }}?periode=' + $event.target.value" class="rounded-lg border-gray-200 bg-gray-50 text-xs font-semibold focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            @foreach($periodeList as $p)
                                <option value="{{ $p }}" {{ $p == $periode ? 'selected' : '' }}>Periode {{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($canManage)
                    <button @click="showAddMaintenance = true; formNama = ''; formCatatan = ''; formPeriode = '{{ $periode }}'" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tambah Maintenance
                    </button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Nama PC</th>
                            <th class="px-5 py-3">Tanggal Mulai</th>
                            <th class="px-5 py-3">Tanggal Selesai</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Sebelum</th>
                            <th class="px-5 py-3">Sesudah</th>
                            <th class="px-5 py-3">Keterangan</th>
                            @if($canGiveFeedbackKoordinator || $canGiveFeedback || $canManage)
                            <th class="px-5 py-3">Feedback</th>
                            @endif
                            @if($canManage)
                            <th class="px-5 py-3">Aksi</th>
                            @endif
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
                                    <td class="px-5 py-3.5">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $latest->tanggal_mulai?->format('d M Y') ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $latest->tanggal_selesai?->format('d M Y') ?? '-' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($latest->status === 'selesai')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Selesai</span>
                                        @elseif($latest->status === 'diproses')
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Diproses</span>
                                        @elseif($latest->status === 'dijeda')
                                            <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-semibold text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">Dijeda</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Dalam Antrean</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($latest->foto_sebelum)
                                            <button type="button" @click="fotoViewer = { src: '{{ asset('storage/' . $latest->foto_sebelum) }}', label: 'Sebelum Maintenance' }" class="rounded-lg border border-gray-200 p-1 hover:border-blue-400 dark:border-gray-700" title="Lihat foto sebelum">
                                                <img src="{{ asset('storage/' . $latest->foto_sebelum) }}" alt="Sebelum" class="h-9 w-9 rounded-md object-cover">
                                            </button>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($latest->foto_sesudah)
                                            <button type="button" @click="fotoViewer = { src: '{{ asset('storage/' . $latest->foto_sesudah) }}', label: 'Sesudah Maintenance' }" class="rounded-lg border border-gray-200 p-1 hover:border-emerald-400 dark:border-gray-700" title="Lihat foto sesudah">
                                                <img src="{{ asset('storage/' . $latest->foto_sesudah) }}" alt="Sesudah" class="h-9 w-9 rounded-md object-cover">
                                            </button>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $latest->catatan ?? '-' }}</td>
                                    @if($canGiveFeedbackKoordinator || $canGiveFeedback || $canManage)
                                    <td class="px-5 py-3.5">
                                        <button type="button" @click="feedbackData = { id: {{ $latest->id }}, nama: '{{ addslashes($pc->nama) }}', feedback_koordinator: {{ json_encode($latest->feedback_koordinator ?? '') }}, feedback_atasan: {{ json_encode($latest->feedback_atasan ?? '') }} }; feedbackKoordinatorText = feedbackData.feedback_koordinator; feedbackAtasanText = feedbackData.feedback_atasan; editingKoordinator = false; editingAtasan = false; showFeedbackModal = true" class="inline-flex items-center gap-1.5 rounded-lg border {{ ($latest->feedback_koordinator || $latest->feedback_atasan) ? 'border-violet-200 bg-violet-50 text-violet-600 dark:border-violet-800 dark:bg-violet-900/20 dark:text-violet-400' : 'border-gray-200 text-gray-600 hover:border-violet-300 hover:text-violet-600 dark:border-gray-700 dark:text-gray-300 dark:hover:text-violet-400' }} px-2.5 py-1 text-xs font-semibold transition-colors">
                                            @if($latest->feedback_koordinator || $latest->feedback_atasan)
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Lihat Feedback
                                            @else
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                                Feedback
                                            @endif
                                        </button>
                                    </td>
                                    @endif
                                    @if($canManage)
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <button type="button" data-edit='{{ json_encode(['id' => $latest->id, 'nama' => $pc->nama, 'tanggal_mulai' => $latest->tanggal_mulai?->format('Y-m-d') ?? '', 'tanggal_selesai' => $latest->tanggal_selesai?->format('Y-m-d') ?? '', 'catatan' => $latest->catatan ?? '', 'status' => $latest->status, 'foto_sebelum' => $latest->foto_sebelum ? asset('storage/' . $latest->foto_sebelum) : '', 'foto_sesudah' => $latest->foto_sesudah ? asset('storage/' . $latest->foto_sesudah) : ''], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) }}' @click="editTarget = JSON.parse($el.dataset.edit); editPreviewSebelum = null; editPreviewSesudah = null" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-600 transition-colors hover:border-blue-400 hover:text-blue-600 dark:border-gray-700 dark:text-gray-300 dark:hover:text-blue-400">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                                Edit
                                            </button>
                                            <button type="button" @click="deleteTarget = {{ $pc->id }}; deleteType = 'pc'" class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-900/20">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                    @endif
                                @else
                                    <td class="px-5 py-3.5 text-gray-400 italic" colspan="{{ 8 + (($canGiveFeedbackKoordinator || $canGiveFeedback || $canManage) ? 1 : 0) + ($canManage ? 1 : 0) }}">Belum ada jadwal</td>
                                    @if($canManage)
                                    <td class="px-5 py-3.5">
                                        <button @click="showAddMaintenance = true; formNama = '{{ $pc->nama }}'; formCatatan = ''; formPeriode = '{{ $periode }}'" class="rounded-lg p-1.5 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Tambah Maintenance">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </button>
                                    </td>
                                    @endif
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 8 + (($canGiveFeedbackKoordinator || $canGiveFeedback || $canManage) ? 1 : 0) + ($canManage ? 1 : 0) }}" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                    Belum ada PC terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Modal Tambah Maintenance --}}
        <template x-if="showAddMaintenance">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showAddMaintenance = false"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tambah Maintenance</h3>
                    <form action="{{ route('it.maintenance.store') }}" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama PC</label>
                            <input type="text" name="nama" x-model="formNama" placeholder="Contoh: PC 31" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode</label>
                            <input type="number" name="periode" x-model="formPeriode" min="1" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan <span class="text-gray-400">(opsional)</span></label>
                            <input type="text" name="catatan" x-model="formCatatan" placeholder="Keterangan tambahan" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showAddMaintenance = false" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modal Edit Jadwal Maintenance --}}
        <template x-if="editTarget">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="editTarget = null"></div>
                <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Edit Jadwal Maintenance</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ubah jadwal dan unggah foto sebelum &amp; sesudah untuk <span class="font-semibold text-gray-700 dark:text-gray-200" x-text="editTarget.nama"></span></p>
                    <form :action="'/it/maintenance/' + editTarget.id" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" x-model="editTarget.tanggal_mulai" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" x-model="editTarget.tanggal_selesai" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" x-model="editTarget.status" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    <option value="antrean">Dalam Antrean</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="dijeda">Dijeda</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan <span class="text-gray-400">(opsional)</span></label>
                                <input type="text" name="catatan" x-model="editTarget.catatan" placeholder="Keterangan tambahan" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Sebelum <span class="text-gray-400">(opsional, JPG/PNG maks 2MB)</span></label>
                            <input type="file" name="foto_sebelum" accept="image/jpeg,image/png,image/jpg,image/webp" @change="editPreviewSebelum = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-600 hover:file:bg-blue-100 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:file:bg-gray-700 dark:file:text-gray-200">
                            <div class="mt-2 flex items-center gap-3">
                                <template x-if="editTarget.foto_sebelum">
                                    <img :src="editTarget.foto_sebelum" alt="Foto sebelum saat ini" class="h-20 w-20 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                                </template>
                                <img x-show="editPreviewSebelum" :src="editPreviewSebelum" alt="Pratinjau sebelum" class="h-20 w-20 rounded-lg border border-blue-300 object-cover dark:border-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Sesudah <span class="text-gray-400">(opsional, JPG/PNG maks 2MB)</span></label>
                            <input type="file" name="foto_sesudah" accept="image/jpeg,image/png,image/jpg,image/webp" @change="editPreviewSesudah = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-600 hover:file:bg-emerald-100 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:file:bg-gray-700 dark:file:text-gray-200">
                            <div class="mt-2 flex items-center gap-3">
                                <template x-if="editTarget.foto_sesudah">
                                    <img :src="editTarget.foto_sesudah" alt="Foto sesudah saat ini" class="h-20 w-20 rounded-lg border border-gray-200 object-cover dark:border-gray-700">
                                </template>
                                <img x-show="editPreviewSesudah" :src="editPreviewSesudah" alt="Pratinjau sesudah" class="h-20 w-20 rounded-lg border border-emerald-300 object-cover dark:border-gray-700">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="editTarget = null" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modal Lihat Foto --}}
        <template x-if="fotoViewer">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/70" @click="fotoViewer = null"></div>
                <div class="relative w-full max-w-2xl rounded-2xl bg-white p-5 shadow-xl dark:bg-gray-900" @click.stop>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100" x-text="fotoViewer.label"></h3>
                        <button type="button" @click="fotoViewer = null" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <img :src="fotoViewer.src" :alt="fotoViewer.label" class="max-h-[70vh] w-full rounded-xl object-contain bg-gray-100 dark:bg-gray-800">
                </div>
            </div>
        </template>

        {{-- Modal Konfirmasi Hapus --}}
        <template x-if="deleteTarget">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="deleteTarget = null"></div>
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Hapus Data PC?</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Seluruh data PC beserta jadwal maintenance akan dihapus secara permanen.</p>
                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="deleteTarget = null" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                        <form :action="'{{ url('it/maintenance/pc') }}/' + deleteTarget" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        {{-- Modal Feedback (Koordinator + Atasan) --}}
        <template x-if="showFeedbackModal && feedbackData">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showFeedbackModal = false"></div>
                <div class="relative w-full max-w-lg max-h-[85vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Feedback</h3>
                        <button type="button" @click="showFeedbackModal = false" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Feedback untuk <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="feedbackData?.nama"></span>
                    </p>

                    {{-- Feedback Koordinator --}}
                    <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/50 p-4 dark:border-blue-900/50 dark:bg-blue-900/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <span class="text-sm font-bold text-blue-700 dark:text-blue-400">Koordinator IT</span>
                            </div>
                            @if($canGiveFeedbackKoordinator)
                            <button type="button" @click="editingKoordinator = !editingKoordinator" class="rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors" :class="editingKoordinator ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300' : 'text-blue-600 hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-900/30'">
                                <span x-text="editingKoordinator ? 'Batal' : 'Edit'"></span>
                            </button>
                            @endif
                        </div>
                        <template x-if="editingKoordinator">
                            <form :action="'{{ url('it/maintenance') }}/' + feedbackData?.id + '/feedback-koordinator'" method="POST" class="mt-3">
                                @csrf
                                <textarea name="feedback_koordinator" x-model="feedbackKoordinatorText" rows="3" placeholder="Catatan atau evaluasi dari koordinator IT..." class="block w-full rounded-xl border-blue-200 bg-white text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-blue-800 dark:bg-gray-800 dark:text-gray-200"></textarea>
                                <div class="flex justify-end gap-2 mt-3">
                                    <button type="button" @click="editingKoordinator = false" class="rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">Batal</button>
                                    <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition">Simpan</button>
                                </div>
                            </form>
                        </template>
                        <template x-if="!editingKoordinator">
                            <div class="mt-2">
                                <template x-if="feedbackData?.feedback_koordinator">
                                    <p class="text-sm text-blue-800 dark:text-blue-300 whitespace-pre-line" x-text="feedbackData?.feedback_koordinator"></p>
                                </template>
                                <template x-if="!feedbackData?.feedback_koordinator">
                                    <p class="text-xs text-blue-400 italic">Belum ada feedback</p>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Feedback Atasan --}}
                    <div class="mt-3 rounded-xl border border-amber-100 bg-amber-50/50 p-4 dark:border-amber-900/50 dark:bg-amber-900/10">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                </span>
                                <span class="text-sm font-bold text-amber-700 dark:text-amber-400">Head of Store</span>
                            </div>
                            @if($canGiveFeedback)
                            <button type="button" @click="editingAtasan = !editingAtasan" class="rounded-lg px-2.5 py-1 text-xs font-semibold transition-colors" :class="editingAtasan ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'text-amber-600 hover:bg-amber-100 dark:text-amber-400 dark:hover:bg-amber-900/30'">
                                <span x-text="editingAtasan ? 'Batal' : 'Edit'"></span>
                            </button>
                            @endif
                        </div>
                        <template x-if="editingAtasan">
                            <form :action="'{{ url('it/maintenance') }}/' + feedbackData?.id + '/feedback'" method="POST" class="mt-3">
                                @csrf
                                <textarea name="feedback_atasan" x-model="feedbackAtasanText" rows="3" placeholder="Arahan atau catatan untuk tim IT..." class="block w-full rounded-xl border-amber-200 bg-white text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-amber-800 dark:bg-gray-800 dark:text-gray-200"></textarea>
                                <div class="flex justify-end gap-2 mt-3">
                                    <button type="button" @click="editingAtasan = false" class="rounded-lg px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">Batal</button>
                                    <button type="submit" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 transition">Simpan</button>
                                </div>
                            </form>
                        </template>
                        <template x-if="!editingAtasan">
                            <div class="mt-2">
                                <template x-if="feedbackData?.feedback_atasan">
                                    <p class="text-sm text-amber-800 dark:text-amber-300 whitespace-pre-line" x-text="feedbackData?.feedback_atasan"></p>
                                </template>
                                <template x-if="!feedbackData?.feedback_atasan">
                                    <p class="text-xs text-amber-400 italic">Belum ada feedback</p>
                                </template>
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </template>

    </div>
</x-app-layout>
