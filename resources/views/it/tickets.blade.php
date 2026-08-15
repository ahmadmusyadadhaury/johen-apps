@php
    $statusLabels = ['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dijeda' => 'Dijeda', 'dilanjutkan' => 'Dilanjutkan', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
    $statusClasses = ['menunggu' => 'bg-blue-50 text-blue-700 ring-blue-600/15 dark:bg-blue-900/20 dark:text-blue-400', 'diproses' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400', 'dijeda' => 'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-900/20 dark:text-orange-400', 'dilanjutkan' => 'bg-violet-50 text-violet-700 ring-violet-600/15 dark:bg-violet-900/20 dark:text-violet-400', 'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-900/20 dark:text-emerald-400', 'ditolak' => 'bg-red-50 text-red-700 ring-red-600/15 dark:bg-red-900/20 dark:text-red-400'];
    $priorityLabels = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'mendesak' => 'Mendesak'];
    $priorityTextClasses = ['rendah' => 'text-gray-500 dark:text-gray-400', 'sedang' => 'text-blue-600 dark:text-blue-400', 'tinggi' => 'text-amber-600 dark:text-amber-400', 'mendesak' => 'text-red-600 dark:text-red-400'];
    $fmtDurasi = fn (int $detik) => sprintf('%02d:%02d:%02d', intdiv($detik, 3600), intdiv($detik % 3600, 60), $detik % 60);
    $countChip = fn ($key) => $key === 'semua' ? $tickets->count() : $tickets->where('status', $key)->count();
@endphp

@push('topbar-left')
    <div>
        <h1 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Ticketing IT</h1>
        <p class="mt-0.5 text-xs text-gray-400">Permintaan bantuan dan pekerjaan lintas divisi</p>
    </div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function pad(n) { return String(n).padStart(2, '0'); }
    window.fmtDurasi = function (total) {
        total = Math.max(0, Math.floor(total));
        var h = Math.floor(total / 3600);
        var m = Math.floor((total % 3600) / 60);
        var s = total % 60;
        return pad(h) + ':' + pad(m) + ':' + pad(s);
    };

    function initTimer(el) {
        el._update = function () {
            var durasi = parseInt(el.dataset.durasi || '0', 10);
            var mulai = parseInt(el.dataset.mulai || '0', 10);
            var total = durasi + (mulai ? Math.max(0, (Date.now() / 1000) - mulai) : 0);
            var target = el.querySelector('.timer-time');
            if (target) target.textContent = fmtDurasi(total);
        };
        el._update();
    }

    setInterval(function () {
        document.querySelectorAll('.timer').forEach(function (el) {
            if (!el._update) initTimer(el);
            el._update();
        });
    }, 1000);
});
</script>
@endpush

<x-app-layout title="Ticketing IT">
    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        @if($canManage)
            <div x-data="{ search: '', filter: 'semua', detail: null }" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <button type="button" @click="filter = 'semua'" :class="filter === 'semua' ? 'border-primary-200 ring-2 ring-primary-500/20 dark:border-primary-800' : 'hover:border-blue-200 dark:hover:border-blue-800'" class="stat-card group w-full text-left cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/></svg>
                            </div>
                            <span class="badge-info">Semua</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total Tiket Masuk</p>
                    </button>

                    <button type="button" @click="filter = 'diproses'" :class="filter === 'diproses' ? 'border-amber-300 ring-2 ring-amber-500/20 dark:border-amber-800' : 'hover:border-amber-200 dark:hover:border-amber-800'" class="stat-card group w-full text-left cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                            </div>
                            <span class="badge-warning">Aktif</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['diproses'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sedang Diproses</p>
                    </button>

                    <button type="button" @click="filter = 'dijeda'" :class="filter === 'dijeda' ? 'border-violet-300 ring-2 ring-violet-500/20 dark:border-violet-800' : 'hover:border-violet-200 dark:hover:border-violet-800'" class="stat-card group w-full text-left cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-200 dark:shadow-violet-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                            </div>
                            <span class="badge-primary">Dijeda</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['dijeda'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dijeda</p>
                    </button>

                    <button type="button" @click="filter = 'selesai'" :class="filter === 'selesai' ? 'border-emerald-300 ring-2 ring-emerald-500/20 dark:border-emerald-800' : 'hover:border-emerald-200 dark:hover:border-emerald-800'" class="stat-card group w-full text-left cursor-pointer">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="badge-success">Selesai</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['selesai'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tiket Selesai</p>
                    </button>
                </div>

                <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Semua Tiket Masuk</h2>
                            <p class="mt-0.5 text-xs text-gray-400">Kelola penugasan dan status tiket secara realtime</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="relative flex-1 sm:flex-none">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input x-model="search" type="text" placeholder="Cari tiket, pengaju, PIC..." class="w-full rounded-xl border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 sm:w-56">
                            </label>
                            <label class="relative flex-1 sm:flex-none">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                                <select x-model="filter" class="w-full appearance-none rounded-xl border-gray-200 bg-gray-50 py-2 pl-9 pr-8 text-sm font-semibold text-gray-700 focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 sm:w-44">
                                    @foreach(['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dijeda' => 'Dijeda', 'dilanjutkan' => 'Dilanjutkan', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                                        <option value="{{ $key }}">{{ $label }} ({{ $countChip($key) }})</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </label>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                            <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                    <th class="px-5 py-3">Tiket</th>
                                    <th class="px-5 py-3">Pengaju</th>
                                    <th class="px-5 py-3">Prioritas</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Waktu Pengerjaan</th>
                                    <th class="px-5 py-3">PIC</th>
                                    <th class="px-5 py-3">Feedback</th>
                                    <th class="px-5 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody" class="divide-y divide-gray-50 dark:divide-gray-800/60">
                                @forelse($tickets as $ticket)
                                    @php
                                        $sedangDikerjakan = in_array($ticket->status, ['diproses', 'dilanjutkan']) && $ticket->proses_mulai_at;
                                        $tampilDetik = $ticket->durasi_detik + ($ticket->proses_mulai_at ? max(0, $ticket->proses_mulai_at->diffInSeconds(now())) : 0);
                                        $namaPengaju = $ticket->requester->employee?->nama ?? $ticket->requester->name;
                                        $divisiPengaju = $ticket->requester->employee?->divisionNames() ?: '-';
                                        $namaAssign = $ticket->assignee?->employee?->nama ?? $ticket->assignee?->name ?? '';
                                        $cari = mb_strtolower($ticket->kode . ' ' . $ticket->judul . ' ' . $namaPengaju . ' ' . $namaAssign);
                                        $detailData = [
                                            'id' => $ticket->id,
                                            'kode' => $ticket->kode,
                                            'judul' => $ticket->judul,
                                            'deskripsi' => $ticket->deskripsi,
                                            'bukti_kendala' => $ticket->bukti_kendala ? asset('storage/' . $ticket->bukti_kendala) : '',
                                            'pengaju' => $namaPengaju,
                                            'divisi' => $divisiPengaju,
                                            'tanggal' => $ticket->created_at->format('d M Y · H:i'),
                                            'prioritas' => $priorityLabels[$ticket->prioritas],
                                            'status' => $ticket->status,
                                            'assignee_id' => $ticket->assignee_id,
                                            'can_edit' => $ticket->status !== 'selesai' && (!$ticket->assignee_id || (int) $ticket->assignee_id === (int) auth()->id()),
                                            'pic_locked' => auth()->user()->isStaffIt(),
                                            'catatan_it' => $ticket->catatan_it ?? '',
                                            'alasan_jeda' => $ticket->alasan_jeda ?? '',
                                            'feedback_atasan' => $ticket->feedback_atasan ?? '',
                                            'durasi' => (int) $ticket->durasi_detik,
                                            'mulai' => $ticket->proses_mulai_at?->timestamp ?? 0,
                                        ];
                                    @endphp
                                    <tr class="ticket-row transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40" x-show="(filter === 'semua' || $el.dataset.status === filter) && (search.trim() === '' || $el.dataset.search.includes(search.trim().toLowerCase()))" data-status="{{ $ticket->status }}" data-search="{{ $cari }}">
                                        <td class="max-w-sm px-5 py-4">
                                            <p class="text-[11px] font-bold tracking-wide text-primary-600 dark:text-primary-400">{{ $ticket->kode }}</p>
                                            <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->judul }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $namaPengaju }}</p>
                                                <p class="mt-0.5 text-[11px] text-gray-400">{{ $ticket->created_at->format('d M Y · H:i') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="whitespace-nowrap text-xs font-semibold {{ $priorityTextClasses[$ticket->prioritas] }}">{{ $priorityLabels[$ticket->prioritas] }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $statusClasses[$ticket->status] }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                {{ $statusLabels[$ticket->status] }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="timer inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold tabular-nums ring-1 {{ $sedangDikerjakan ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-gray-100 text-gray-500 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400' }}" data-durasi="{{ $ticket->durasi_detik }}" data-mulai="{{ $ticket->proses_mulai_at?->timestamp ?? 0 }}">
                                                @if($sedangDikerjakan)
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                                    </span>
                                                @else
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                                <span class="timer-time">{{ $fmtDurasi($tampilDetik) }}</span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="text-xs font-medium {{ $namaAssign ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600' }}">{{ $namaAssign ?: 'Belum ditugaskan' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($ticket->feedback_atasan)
                                                <span class="block max-w-[160px] truncate text-xs font-medium text-amber-700 dark:text-amber-400" title="{{ $ticket->feedback_atasan }}">{{ $ticket->feedback_atasan }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <button type="button" data-detail='{{ json_encode($detailData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) }}' @click="detail = JSON.parse($el.dataset.detail); detail.jeda_dipilih = false; if (!detail.assignee_id) detail.assignee_id = {{ auth()->id() }};" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-600 transition-colors hover:border-primary-300 hover:text-primary-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-primary-400">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-16 text-center">
                                            <div class="mx-auto flex max-w-xs flex-col items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/></svg>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada tiket masuk</p>
                                                <p class="text-xs text-gray-400">Tiket yang diajukan karyawan akan muncul di sini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr x-cloak x-show="(filter !== 'semua' || search.trim() !== '') && $refs.tbody.querySelectorAll('tr.ticket-row').length > 0 && Array.from($refs.tbody.querySelectorAll('tr.ticket-row')).every(r => r.style.display === 'none')">
                                    <td colspan="8" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex max-w-xs flex-col items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM13.5 10.5H21M3 10.5h.008v.008H3V10.5z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tidak ada tiket yang cocok</p>
                                            <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter status.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <template x-if="detail">
                    <div class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center">
                        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="detail = null"></div>
                        <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 dark:border-gray-800">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold tracking-wide text-primary-600 dark:text-primary-400" x-text="detail.kode"></p>
                                    <h3 class="mt-0.5 text-base font-display font-bold text-gray-900 dark:text-gray-100" x-text="detail.judul"></h3>
                                </div>
                                <button type="button" @click="detail = null" class="shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="max-h-[70vh] overflow-y-auto p-5">
                                <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pengaju</p>
                                        <p class="mt-0.5 truncate text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="detail.pengaju"></p>
                                        <p class="truncate text-[11px] text-gray-400" x-text="detail.divisi"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Diajukan</p>
                                        <p class="mt-0.5 text-xs font-medium text-gray-800 dark:text-gray-200" x-text="detail.tanggal"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Prioritas</p>
                                        <p class="mt-0.5 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="detail.prioritas"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</p>
                                        <span class="mt-0.5 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1" :class="{
                                            'bg-blue-50 text-blue-700 ring-blue-600/15 dark:bg-blue-900/20 dark:text-blue-400': detail.status === 'menunggu',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400': detail.status === 'diproses',
                                            'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-900/20 dark:text-orange-400': detail.status === 'dijeda',
                                            'bg-violet-50 text-violet-700 ring-violet-600/15 dark:bg-violet-900/20 dark:text-violet-400': detail.status === 'dilanjutkan',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/15 dark:bg-emerald-900/20 dark:text-emerald-400': detail.status === 'selesai',
                                            'bg-red-50 text-red-700 ring-red-600/15 dark:bg-red-900/20 dark:text-red-400': detail.status === 'ditolak'
                                        }" x-text="detail.status"></span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Waktu Pengerjaan</p>
                                        <span class="timer mt-0.5 inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold tabular-nums ring-1" :data-durasi="detail.durasi" :data-mulai="detail.mulai" :class="detail.status === 'diproses' || detail.status === 'dilanjutkan' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400'">
                                            <template x-if="detail.status === 'diproses' || detail.status === 'dilanjutkan'">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                                </span>
                                            </template>
                                            <template x-else>
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </template>
                                            <span class="timer-time">{{ $fmtDurasi(0) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi</p>
                                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="detail.deskripsi"></p>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50" x-show="detail.bukti_kendala">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Bukti Kendala</p>
                                    <a :href="detail.bukti_kendala" target="_blank" class="mt-2 block overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700">
                                        <img :src="detail.bukti_kendala" alt="Bukti kendala" class="max-h-56 w-full object-contain">
                                    </a>
                                </div>

                                <form x-ref="updateForm" method="POST" :action="'/it/tickets/' + detail.id" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="_method" value="PATCH">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">PIC</label>
                                        <select name="assignee_id" x-model="detail.assignee_id" :disabled="!detail.can_edit || detail.pic_locked" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-60">
                                            <option value="">Belum ditugaskan</option>
                                            @foreach($itUsers as $itUser)
                                                <option value="{{ $itUser->id }}">{{ $itUser->employee?->nama ?? $itUser->name }}</option>
                                            @endforeach
                                        </select>
                                        @if(auth()->user()->isStaffIt())
                                            <input type="hidden" name="assignee_id" :value="detail.assignee_id">
                                        @endif
                                        <p x-show="detail.pic_locked" class="mt-1 text-[10px] font-medium text-gray-400">Sebagai Staff IT, PIC tiket ini otomatis Anda.</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" x-model="detail.status" :disabled="!detail.can_edit" @change="if (detail.status === 'diproses' || detail.status === 'dilanjutkan') { if (!detail.mulai) detail.mulai = Math.floor(Date.now() / 1000); } else { detail.mulai = 0; } if (detail.status === 'dijeda') detail.jeda_dipilih = true;" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-700 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-60">
                                            @foreach($statusLabels as $value => $label)
                                                <option value="{{ $value }}" @if($value === 'menunggu') x-show="detail.status === 'menunggu'" @endif>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <p x-show="detail.can_edit && detail.status !== 'menunggu'" class="mt-1 text-[10px] font-medium text-gray-400">Setelah tiket diproses, status tidak dapat dikembalikan ke Menunggu.</p>
                                        <p x-show="!detail.can_edit" x-text="detail.status === 'selesai' ? 'Tiket sudah selesai dan tidak dapat diubah.' : 'Hanya PIC yang ditugaskan yang dapat mengubah status dan catatan.'" class="mt-1 text-[10px] font-medium text-gray-400"></p>
                                    </div>
                                    <div x-show="detail.status === 'dijeda' && detail.jeda_dipilih">
                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Alasan Jeda <span class="text-red-500">*</span></label>
                                        <textarea name="alasan_jeda" rows="2" x-model="detail.alasan_jeda" :disabled="!detail.can_edit" required placeholder="Jelaskan kenapa tiket dijeda..." class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-60"></textarea>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Catatan Tim IT</label>
                                        <textarea name="catatan_it" rows="3" x-model="detail.catatan_it" :disabled="!detail.can_edit" placeholder="Catatan untuk pengaju" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 disabled:cursor-not-allowed disabled:opacity-60"></textarea>
                                    </div>
                                </form>
                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/10" x-show="detail.feedback_atasan">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Feedback HOS 2</p>
                                    </div>
                                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="detail.feedback_atasan"></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                                @if($canDelete)
                                    <form x-ref="deleteForm" method="POST" :action="'/it/tickets/' + detail.id" class="m-0 hidden">
                                        @csrf
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                                    <button type="button" @click="if (confirm('Hapus tiket ' + detail.kode + '? Tindakan ini tidak dapat dibatalkan.')) $refs.deleteForm.submit()" class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition-colors hover:bg-red-100 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                @endif
                                <div class="ml-auto flex items-center gap-3" x-show="detail.can_edit">
                                    <button type="button" @click="detail = null" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Batal</button>
                                    <button type="button" @click="$refs.updateForm.submit()" class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @elseif($canViewOnly)
            <div x-data="{ search: '', filter: 'semua', detail: null, feedback: '' }" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="stat-card group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/></svg>
                            </div>
                            <span class="badge-info">Semua</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total Tiket Masuk</p>
                    </div>

                    <div class="stat-card group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                            </div>
                            <span class="badge-warning">Aktif</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['diproses'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sedang Diproses</p>
                    </div>

                    <div class="stat-card group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 text-white shadow-lg shadow-violet-200 dark:shadow-violet-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5"/></svg>
                            </div>
                            <span class="badge-primary">Dijeda</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['dijeda'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dijeda</p>
                    </div>

                    <div class="stat-card group">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="badge-success">Selesai</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['selesai'] }} <span class="text-sm font-medium text-gray-400">tiket</span></p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Tiket Selesai</p>
                    </div>
                </div>

                <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Monitor Tiket IT</h2>
                            <p class="mt-0.5 text-xs text-gray-400">Pantau seluruh tiket IT divisi secara realtime</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="relative flex-1 sm:flex-none">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                <input x-model="search" type="text" placeholder="Cari tiket, pengaju, PIC..." class="w-full rounded-xl border-gray-200 bg-gray-50 py-2 pl-9 pr-3 text-sm text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 sm:w-56">
                            </label>
                            <label class="relative flex-1 sm:flex-none">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                                <select x-model="filter" class="w-full appearance-none rounded-xl border-gray-200 bg-gray-50 py-2 pl-9 pr-8 text-sm font-semibold text-gray-700 focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 sm:w-44">
                                    @foreach(['semua' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dijeda' => 'Dijeda', 'dilanjutkan' => 'Dilanjutkan', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $key => $label)
                                        <option value="{{ $key }}">{{ $label }} ({{ $countChip($key) }})</option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </label>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                            <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                                <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                    <th class="px-5 py-3">Tiket</th>
                                    <th class="px-5 py-3">Pengaju</th>
                                    <th class="px-5 py-3">Prioritas</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Waktu Pengerjaan</th>
                                    <th class="px-5 py-3">PIC</th>
                                    <th class="px-5 py-3">Feedback</th>
                                    <th class="px-5 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody" class="divide-y divide-gray-50 dark:divide-gray-800/60">
                                @forelse($tickets as $ticket)
                                    @php
                                        $sedangDikerjakan = in_array($ticket->status, ['diproses', 'dilanjutkan']) && $ticket->proses_mulai_at;
                                        $tampilDetik = $ticket->durasi_detik + ($ticket->proses_mulai_at ? max(0, $ticket->proses_mulai_at->diffInSeconds(now())) : 0);
                                        $namaPengaju = $ticket->requester->employee?->nama ?? $ticket->requester->name;
                                        $divisiPengaju = $ticket->requester->employee?->divisionNames() ?: '-';
                                        $namaAssign = $ticket->assignee?->employee?->nama ?? $ticket->assignee?->name ?? '';
                                        $cari = mb_strtolower($ticket->kode . ' ' . $ticket->judul . ' ' . $namaPengaju . ' ' . $namaAssign);
                                        $detailData = [
                                            'id' => $ticket->id,
                                            'kode' => $ticket->kode,
                                            'judul' => $ticket->judul,
                                            'deskripsi' => $ticket->deskripsi,
                                            'bukti_kendala' => $ticket->bukti_kendala ? asset('storage/' . $ticket->bukti_kendala) : '',
                                            'pengaju' => $namaPengaju,
                                            'divisi' => $divisiPengaju,
                                            'tanggal' => $ticket->created_at->format('d M Y · H:i'),
                                            'prioritas' => $priorityLabels[$ticket->prioritas],
                                            'status' => $statusLabels[$ticket->status],
                                            'assignee' => $namaAssign,
                                            'catatan_it' => $ticket->catatan_it ?? '',
                                            'alasan_jeda' => $ticket->alasan_jeda ?? '',
                                            'feedback' => $ticket->feedback_atasan ?? '',
                                            'durasi' => (int) $ticket->durasi_detik,
                                            'mulai' => $ticket->proses_mulai_at?->timestamp ?? 0,
                                        ];
                                    @endphp
                                    <tr class="ticket-row transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40" x-show="(filter === 'semua' || $el.dataset.status === filter) && (search.trim() === '' || $el.dataset.search.includes(search.trim().toLowerCase()))" data-status="{{ $ticket->status }}" data-search="{{ $cari }}">
                                        <td class="max-w-sm px-5 py-4">
                                            <p class="text-[11px] font-bold tracking-wide text-primary-600 dark:text-primary-400">{{ $ticket->kode }}</p>
                                            <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->judul }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $namaPengaju }}</p>
                                                <p class="mt-0.5 text-[11px] text-gray-400">{{ $ticket->created_at->format('d M Y · H:i') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="whitespace-nowrap text-xs font-semibold {{ $priorityTextClasses[$ticket->prioritas] }}">{{ $priorityLabels[$ticket->prioritas] }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $statusClasses[$ticket->status] }}">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                {{ $statusLabels[$ticket->status] }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="timer inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold tabular-nums ring-1 {{ $sedangDikerjakan ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-gray-100 text-gray-500 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400' }}" data-durasi="{{ $ticket->durasi_detik }}" data-mulai="{{ $ticket->proses_mulai_at?->timestamp ?? 0 }}">
                                                @if($sedangDikerjakan)
                                                    <span class="relative flex h-2 w-2">
                                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                                    </span>
                                                @else
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                                <span class="timer-time">{{ $fmtDurasi($tampilDetik) }}</span>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="text-xs font-medium {{ $namaAssign ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600' }}">{{ $namaAssign ?: 'Belum ditugaskan' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($ticket->feedback_atasan)
                                                <span class="block max-w-[160px] truncate text-xs font-medium text-amber-700 dark:text-amber-400" title="{{ $ticket->feedback_atasan }}">{{ $ticket->feedback_atasan }}</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <button type="button" data-detail='{{ json_encode($detailData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP) }}' @click="detail = JSON.parse($el.dataset.detail); feedback = detail.feedback;" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-600 transition-colors hover:border-primary-300 hover:text-primary-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-primary-400">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Lihat Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-5 py-16 text-center">
                                            <div class="mx-auto flex max-w-xs flex-col items-center gap-3">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/></svg>
                                                </div>
                                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada tiket masuk</p>
                                                <p class="text-xs text-gray-400">Tiket yang diajukan karyawan akan muncul di sini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr x-cloak x-show="(filter !== 'semua' || search.trim() !== '') && $refs.tbody.querySelectorAll('tr.ticket-row').length > 0 && Array.from($refs.tbody.querySelectorAll('tr.ticket-row')).every(r => r.style.display === 'none')">
                                    <td colspan="8" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex max-w-xs flex-col items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM13.5 10.5H21M3 10.5h.008v.008H3V10.5z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Tidak ada tiket yang cocok</p>
                                            <p class="text-xs text-gray-400">Coba ubah kata kunci pencarian atau filter status.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <template x-if="detail">
                    <div class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center">
                        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="detail = null"></div>
                        <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 dark:border-gray-800">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-bold tracking-wide text-primary-600 dark:text-primary-400" x-text="detail.kode"></p>
                                    <h3 class="mt-0.5 text-base font-display font-bold text-gray-900 dark:text-gray-100" x-text="detail.judul"></h3>
                                </div>
                                <button type="button" @click="detail = null" class="shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div class="max-h-[70vh] overflow-y-auto p-5">
                                <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pengaju</p>
                                        <p class="mt-0.5 truncate text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="detail.pengaju"></p>
                                        <p class="truncate text-[11px] text-gray-400" x-text="detail.divisi"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Diajukan</p>
                                        <p class="mt-0.5 text-xs font-medium text-gray-800 dark:text-gray-200" x-text="detail.tanggal"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Prioritas</p>
                                        <p class="mt-0.5 text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="detail.prioritas"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</p>
                                        <span class="mt-0.5 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 bg-gray-100 text-gray-700 ring-gray-500/15 dark:bg-gray-800 dark:text-gray-300" x-text="detail.status"></span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">PIC</p>
                                        <p class="mt-0.5 truncate text-xs font-semibold text-gray-800 dark:text-gray-200" x-text="detail.assignee || 'Belum ditugaskan'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Waktu Pengerjaan</p>
                                        <span class="timer mt-0.5 inline-flex items-center gap-1.5 rounded-lg px-2 py-1 text-xs font-semibold tabular-nums ring-1 bg-gray-100 text-gray-500 ring-gray-500/10 dark:bg-gray-800 dark:text-gray-400" :data-durasi="detail.durasi" :data-mulai="detail.mulai">
                                            <span class="timer-time">{{ $fmtDurasi(0) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi</p>
                                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="detail.deskripsi"></p>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50" x-show="detail.bukti_kendala">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Bukti Kendala</p>
                                    <a :href="detail.bukti_kendala" target="_blank" class="mt-2 block overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700">
                                        <img :src="detail.bukti_kendala" alt="Bukti kendala" class="max-h-56 w-full object-contain">
                                    </a>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50" x-show="detail.alasan_jeda">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Alasan Jeda</p>
                                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="detail.alasan_jeda"></p>
                                </div>
                                <div class="mb-5 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Catatan Tim IT</p>
                                    <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-gray-700 dark:text-gray-300" x-text="detail.catatan_it || '-'"></p>
                                </div>

                                <div class="rounded-xl border border-primary-100 bg-primary-50/50 p-4 dark:border-primary-900/40 dark:bg-primary-900/10">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400">Feedback Anda (HOS 2)</p>
                                    </div>
                                    <form method="POST" :action="'/it/tickets/' + detail.id + '/feedback'" class="mt-2">
                                        @csrf
                                        <textarea name="feedback_atasan" rows="3" x-model="feedback" placeholder="Berikan arahan atau evaluasi untuk tim IT..." class="w-full rounded-xl border-primary-200 bg-white px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"></textarea>
                                        <div class="mt-2 flex justify-end">
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                                                Kirim Feedback
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-800/50">
                                <button type="button" @click="detail = null" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Tutup</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @else
            <div x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }}, previewFoto: null }" class="space-y-6">
            <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col gap-3 border-b border-gray-100 p-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Riwayat Pengajuan Saya</h2>
                        <p class="mt-0.5 text-xs text-gray-400">Pantau status tiket yang pernah Anda ajukan</p>
                    </div>
                    <button type="button" @click="showForm = true" class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Buat Tiket
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                        <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                            <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                <th class="px-5 py-3">Tiket</th>
                                <th class="px-5 py-3">Diajukan</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">PIC</th>
                                <th class="px-5 py-3">Catatan Tim IT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                            @forelse($tickets as $ticket)
                                <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                                    <td class="px-5 py-4">
                                        <p class="text-[11px] font-bold tracking-wide text-primary-600 dark:text-primary-400">{{ $ticket->kode }}</p>
                                        <p class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->judul }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-gray-500">{{ $ticket->created_at->format('d M Y · H:i') }}</td>
                                    <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $statusClasses[$ticket->status] }}"><span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $statusLabels[$ticket->status] }}</span></td>
                                    <td class="whitespace-nowrap px-5 py-4 text-xs font-medium text-gray-700 dark:text-gray-300">{{ $ticket->assignee?->employee?->nama ?? $ticket->assignee?->name ?? 'Belum ditugaskan' }}</td>
                                    <td class="max-w-xs px-5 py-4 text-xs text-gray-600 dark:text-gray-300">
                                        @if($ticket->status === 'dijeda' && $ticket->alasan_jeda)
                                            <p class="mb-1 text-amber-600 dark:text-amber-400"><span class="font-semibold">Alasan jeda:</span> {{ $ticket->alasan_jeda }}</p>
                                        @endif
                                        @if($ticket->catatan_it)
                                            <p>{{ $ticket->catatan_it }}</p>
                                        @elseif(!($ticket->status === 'dijeda' && $ticket->alasan_jeda))
                                            <p>-</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-16 text-center">
                                        <div class="mx-auto flex max-w-xs flex-col items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/></svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Belum ada riwayat pengajuan</p>
                                            <p class="text-xs text-gray-400">Tiket yang Anda ajukan akan tampil di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <template x-if="showForm">
                <div class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showForm = false"></div>
                    <div class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
                        <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 dark:border-gray-800">
                            <div>
                                <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Buat Tiket</h3>
                                <p class="mt-0.5 text-xs text-gray-400">Sampaikan kebutuhan IT dengan detail agar dapat ditangani lebih cepat.</p>
                            </div>
                            <button type="button" @click="showForm = false" class="shrink-0 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('it.tickets.store') }}" enctype="multipart/form-data" class="max-h-[70vh] space-y-4 overflow-y-auto p-5">
                            @csrf
                            <div>
                                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Judul permintaan</label>
                                <input name="judul" value="{{ old('judul') }}" required maxlength="150" placeholder="Contoh: Tidak bisa akses aplikasi absensi" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                @error('judul') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Kategori</label>
                                    <select name="kategori" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        @foreach(['perangkat' => 'Perangkat', 'aplikasi' => 'Aplikasi', 'akun_akses' => 'Akun & Akses', 'jaringan' => 'Jaringan', 'lainnya' => 'Lainnya'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('kategori') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Prioritas</label>
                                    <select name="prioritas" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi', 'mendesak' => 'Mendesak'] as $value => $label)
                                            <option value="{{ $value }}" @selected(old('prioritas', 'sedang') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Detail kebutuhan</label>
                                <textarea name="deskripsi" required rows="4" maxlength="3000" placeholder="Jelaskan kendala, perangkat atau aplikasi yang digunakan, dan dampaknya." class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ old('deskripsi') }}</textarea>
                                @error('deskripsi') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">Bukti Kendala <span class="text-[10px] font-medium text-gray-400">(opsional, foto JPG/PNG maks 2MB)</span></label>
                                <input type="file" name="bukti_kendala" accept="image/jpeg,image/png,image/jpg" @change="previewFoto = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="mt-1 w-full rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary-600 hover:file:bg-primary-100 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:file:bg-gray-700 dark:file:text-gray-200">
                                <div x-show="previewFoto" class="mt-2">
                                    <img :src="previewFoto" alt="Pratinjau bukti kendala" class="max-h-40 rounded-lg border border-gray-200 object-contain dark:border-gray-700">
                                </div>
                                @error('bukti_kendala') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <button type="button" @click="showForm = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-600 transition-colors hover:border-gray-300 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Batal</button>
                                <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">Kirim Tiket</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
            </div>
        @endif
    </div>
</x-app-layout>
