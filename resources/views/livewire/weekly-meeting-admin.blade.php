@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Weekly Meeting</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Kelola rapat mingguan dan absensi QR Code</p>
    </div>
@endpush

<div class="space-y-4">
    {{-- Create Meeting Button / Back to List --}}
    @if($mode === 'list')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Daftar Rapat Mingguan</h2>
        </div>
        <button wire:click="openCreateModal" class="btn-primary text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Buat Rapat Baru
        </button>
    </div>
    @elseif($mode === 'create')
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $selectedMeetingId ? 'Edit Rapat' : 'Buat Rapat Baru' }}</h2>
        </div>
        <button wire:click="closeModal" class="btn-secondary text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Kembali
        </button>
    </div>
    @elseif($mode === 'attendance')
    <div class="flex items-center justify-between" wire:poll.30s="regenerateQrAuto">
        <div class="flex items-center gap-2">
            <button wire:click="backToList" class="btn-secondary text-xs">Kembali ke Daftar</button>
        </div>
    </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($mode === 'create')
    <div class="card">
        <div class="p-6">
            <form wire:submit.prevent="{{ $selectedMeetingId ? 'updateMeeting' : 'createMeeting' }}" class="space-y-4 max-w-2xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label for="title" value="Judul Rapat *" />
                        <x-text-input id="title" wire:model="title" type="text" class="mt-1 block w-full" placeholder="Contoh: Rapat Mingguan Divisi Creative" />
                        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-input-label for="meeting_date" value="Tanggal Rapat *" />
                        <x-text-input id="meeting_date" wire:model="meeting_date" type="date" class="mt-1 block w-full" />
                        @error('meeting_date') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-input-label for="start_time" value="Jam Mulai" />
                        <x-text-input id="start_time" wire:model="start_time" type="time" class="mt-1 block w-full" />
                        @error('start_time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-input-label for="end_time" value="Jam Selesai" />
                        <x-text-input id="end_time" wire:model="end_time" type="time" class="mt-1 block w-full" />
                        @error('end_time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="location" value="Lokasi" />
                        <x-text-input id="location" wire:model="location" type="text" class="mt-1 block w-full" placeholder="Contoh: Ruang Rapat Lt. 2 / Zoom Meeting" />
                        @error('location') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="description" value="Deskripsi / Agenda" />
                        <textarea id="description" wire:model="description" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Agenda rapat, catatan, atau informasi tambahan..."></textarea>
                        @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $selectedMeetingId ? 'Update Rapat' : 'Buat Rapat' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Attendance View --}}
    @if($mode === 'attendance' && $selectedMeetingId)
    <div class="card flex flex-col" wire:poll.3s="refreshAttendanceData" style="height: calc(100vh - 175px);">
        <div class="p-6 flex flex-col min-h-0">
            <div class="mb-6">
                {{-- QR Code Display --}}
                @php
                    $meeting = $meetings->firstWhere('id', $selectedMeetingId);
                    $qrCode = $meeting?->qr_code ?? '';
                @endphp
                @if($qrCode)
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-600">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-4 text-center">QR Code untuk Absen (Bagikan ke Peserta)</h4>
                    <div class="flex flex-col items-center gap-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=280x280&data={{ urlencode($qrCode) }}" alt="QR Code" class="w-64 h-64 bg-white p-2 rounded-lg border border-gray-200 dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Peserta memindai QR ini menggunakan kamera HP di menu <strong>Operasional > Weekly Meeting</strong></p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Attendance List --}}
            <div id="attendance-table-scroll" class="flex-1 min-h-0 overflow-auto overscroll-contain">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="table-header">
                            <th class="px-6 py-3 w-12 text-center">No</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">NIK</th>
                            <th class="px-6 py-3">Divisi</th>
                            <th class="px-6 py-3">Waktu Absen</th>
                            <th class="px-6 py-3">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($attendance as $index => $att)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $att->employee->nama }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $att->employee->nik }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $att->employee->divisionNames() ?: '-' }}</td>
                                <td class="table-cell text-gray-600 dark:text-gray-400">{{ $att->attended_at->format('H:i:s') }}</td>
                                <td class="table-cell">
                                    @if($att->device_location)
                                        <span class="badge-secondary inline-flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            {{ $att->device_location }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Lokasi tidak terdeteksi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada absensi</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Bagikan QR code di atas ke peserta rapat</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- List View --}}
    @if($mode === 'list')
    <div class="card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">Judul</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Lokasi</th>
                        <th class="px-6 py-3 text-center">Absen</th>
                        <th class="px-6 py-3 text-center">QR</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($meetings as $index => $meeting)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $meetings->firstItem() + $index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $meeting->title }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">{{ $meeting->meeting_date->format('d F Y') }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                @if($meeting->start_time && $meeting->end_time)
                                    {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $meeting->location ?: '-' }}</td>
                            <td class="table-cell text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold">
                                    {{ $meeting->attendances_count ?? $meeting->attendances->count() }}
                                </span>
                            </td>
                            <td class="table-cell text-center">
                                @if($meeting->qr_code)
                                    <span class="badge-success">Ready</span>
                                @else
                                    <span class="badge-secondary">Belum</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                @if($meeting->is_active)
                                    <span class="badge-success">Aktif</span>
                                @else
                                    <span class="badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="viewAttendance({{ $meeting->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors" title="Lihat Absensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                    <button wire:click="editMeeting({{ $meeting->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-yellow-600 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $meeting->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada rapat mingguan</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Buat rapat mingguan pertama untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($meetings->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $meetings->links() }}
            </div>
        @endif
    </div>
    @endif

    <x:confirm-delete-modal title="Hapus Rapat" message="Apakah Anda yakin ingin menghapus rapat mingguan ini? Data absensi juga akan terhapus." />
</div>

@push('scripts')
<script>
// Saat ada baris absensi baru ditambahkan (data terbaru di bawah), gulir
// otomatis ke bawah agar baris yang baru masuk selalu terlihat.
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.added', ({ el }) => {
        if (el.tagName === 'TR') {
            const wrap = document.getElementById('attendance-table-scroll');
            if (wrap) wrap.scrollTop = wrap.scrollHeight;
        }
    });
});
</script>
@endpush