@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Permintaan Meeting</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Daftar permintaan meeting</p>
    </div>
@endpush

<x-app-layout title="Permintaan Meeting">
    <div class="flex items-center justify-end mb-6">
        @if(auth()->user()->isStaff())
        <button x-data @click="$dispatch('open-pengajuan')" class="btn-primary text-xs py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Ajukan Meeting
        </button>
        @endif
    </div>

    <div class="card">
        @if(!auth()->user()->isStaff())
        <form method="GET" action="{{ route('meeting.permintaan') }}" class="flex items-center gap-3 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="relative flex-1 min-w-[200px] max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                       class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
            </div>
            <select name="status" onchange="this.form.submit()" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="btn-ghost text-xs py-2 px-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </form>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        @can('view-all')
                        <th class="px-6 py-3">Pemohon</th>
                        @endcan
                        <th class="px-6 py-3">Judul Meeting</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Ruangan</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Status</th>
                        @can('view-all')
                        <th class="px-6 py-3">Aksi</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $requests->firstItem() + $loop->index }}</td>
                            @can('view-all')
                            <td class="table-cell">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                        {{ strtoupper(substr($req->employee->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $req->employee->nama ?? '-' }}</span>
                                </div>
                            </td>
                            @endcan
                            <td class="table-cell">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $req->title }}</span>
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($req->date)->format('d M Y') }}
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $req->room }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($req->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($req->end_time)->format('H:i') }}
                            </td>
                            <td class="table-cell">
                                @if($req->status === 'disetujui')
                                    <span class="badge-success">Disetujui</span>
                                @elseif($req->status === 'ditolak')
                                    <span class="badge-danger">Ditolak</span>
                                @else
                                    <span class="badge-warning">Pending</span>
                                @endif
                            </td>
                            @can('view-all')
                            <td class="table-cell">
                                <div class="flex items-center gap-1">
                                    <button @click="$dispatch('open-detail', { id: {{ $req->id }} })"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                            title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </button>
                                    @if($req->status === 'pending')
                                    <form method="POST" action="{{ route('meeting.permintaan.setujui', $req) }}" class="inline" onsubmit="return confirm('Setujui permintaan ini?')">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                                class="p-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors"
                                                title="Setujui">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </button>
                                    </form>
                                    <button @click="$dispatch('open-tolak', { id: {{ $req->id }} })"
                                            class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                            title="Tolak">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum Ada Permintaan Meeting</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Belum ada data permintaan meeting</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @php
        $requestsJson = $requests->getCollection()->map(fn($r) => [
            'id' => $r->id,
            'employee_name' => $r->employee->nama ?? '-',
            'title' => $r->title,
            'date' => \Carbon\Carbon::parse($r->date)->format('d M Y'),
            'room' => $r->room,
            'start_time' => $r->start_time,
            'end_time' => $r->end_time,
            'status' => $r->status,
            'why' => $r->why ?? '-',
            'what' => $r->what ?? '-',
            'how' => $r->how ?? '-',
        ])->toJson();
    @endphp
    <div x-data="{ open: false, req: null, requests: [] }"
         x-init="requests = {{ $requestsJson }}"
         @open-detail.window="
            open = true;
            req = requests.find(r => r.id === $event.detail.id);
         "
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detail Permintaan Meeting</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="req?.title"></p>
                </div>
                <button @click="open = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-4" x-show="req">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pemohon</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="req?.employee_name"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium" x-text="req?.status === 'disetujui' ? '✓ Disetujui' : (req?.status === 'ditolak' ? '✗ Ditolak' : '◉ Pending')"
                           :class="req?.status === 'disetujui' ? 'text-emerald-600 dark:text-emerald-400' : (req?.status === 'ditolak' ? 'text-red-600 dark:text-red-400' : 'text-yellow-600 dark:text-yellow-400')">
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Judul Meeting</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="req?.title"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="req?.date"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ruangan</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="req?.room"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Waktu</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="req?.start_time + ' - ' + req?.end_time"></p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Why — Kenapa meeting ini diadakan?</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100" x-text="req?.why"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">What — Apa yang dibahas?</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100" x-text="req?.what"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">How — Bagaimana hasil yang diharapkan?</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100" x-text="req?.how"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pengajuan Modal --}}
    <div x-data="{ open: false }"
         @open-pengajuan.window="open = true"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ajukan Meeting</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Isi form permintaan meeting</p>
                </div>
                <button @click="open = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('meeting.permintaan.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="title" value="Judul Meeting *" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" placeholder="Contoh: Meeting Proyek" required />
                </div>

                <div>
                    <x-input-label for="date" value="Tanggal *" />
                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="room" value="Ruangan *" />
                    <x-text-input id="room" name="room" type="text" class="mt-1 block w-full" placeholder="Meeting Room Utama" required />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="start_time" value="Waktu Mulai *" />
                        <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="Waktu Selesai *" />
                        <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" required />
                    </div>
                </div>

                <div>
                    <x-input-label for="why" value="Why — Kenapa meeting ini diadakan?" />
                    <textarea id="why" name="why" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Alasan meeting diadakan..."></textarea>
                </div>

                <div>
                    <x-input-label for="what" value="What — Apa yang dibahas?" />
                    <textarea id="what" name="what" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Materi yang akan dibahas..."></textarea>
                </div>

                <div>
                    <x-input-label for="how" value="How — Bagaimana hasil yang diharapkan?" />
                    <textarea id="how" name="how" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Hasil yang diharapkan..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="open = false" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tolak Modal --}}
    <div x-data="{ open: false, reqId: null }"
         @open-tolak.window="open = true; reqId = $event.detail.id"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-md rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tolak Permintaan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Berikan alasan penolakan</p>
                </div>
                <button @click="open = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="'{{ route('meeting.permintaan.tolak', '') }}/' + reqId" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <x-input-label for="notes" value="Alasan Ditolak *" />
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Alasan penolakan..." required></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="open = false" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-danger text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>