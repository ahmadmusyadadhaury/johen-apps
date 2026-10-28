<div class="space-y-5">
    <div class="flex flex-wrap gap-2">
        <button wire:click="setTab('jadwal')" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'jadwal' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">Jadwal</button>
        @if($canManage)
            <button wire:click="setTab('riwayat')" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'riwayat' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">Riwayat Pelatihan</button>
        @endif
        @if($canJoin)
            <button wire:click="setTab('saya')" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'saya' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">Pelatihan Saya</button>
        @endif
        @if($canViewTeam)
            <button wire:click="setTab('tim')" class="rounded-xl px-4 py-2 text-sm font-semibold {{ $tab === 'tim' ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700' }}">Pelatihan Tim</button>
        @endif
    </div>

    @if($tab === 'jadwal')
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jadwal Seminar & Pelatihan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $canManage ? 'Daftar kegiatan dan draft. Pengelolaan tersedia langsung dari kartu kegiatan.' : 'Daftar kegiatan yang sudah dipublikasikan.' }}</p>
            </div>
            @if($canManage)
                <button wire:click="openCreate" class="btn-primary text-xs">Buat Kegiatan</button>
            @endif
        </div>
        @error('registration') <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-3 text-sm text-red-700 dark:text-red-300">{{ $message }}</div> @enderror
        <div wire:poll.60s.visible class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($sessions as $session)
                @php($myRegistration = $session->registrations->first())
                <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-[0_8px_28px_rgba(30,64,175,.08)] transition-shadow hover:shadow-[0_12px_34px_rgba(30,64,175,.14)] dark:border-gray-800 dark:bg-gray-900">
                    <x-training-thumbnail :title="$session->judul" :jenis="$session->jenis" :description="$session->deskripsi" :image-url="$session->thumbnail ? route('hris.training.thumbnail', $session->id) : null" />
                    <div class="p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $session->judul }}</h3>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.6"><circle cx="7" cy="6" r="3"/><path stroke-linecap="round" d="M1.8 16c.4-3 2.2-4.5 5.2-4.5 3 0 4.8 1.5 5.2 4.5M13.5 7.5h5m-2.5-2.5v5"/></svg>{{ $session->registrations_count }}{{ $session->kuota ? ' / '.$session->kuota : '' }} peserta</span>
                    </div>
                    <p class="mt-1 line-clamp-2 min-h-5 text-sm text-slate-500 dark:text-gray-400">{{ $session->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                    <dl class="mt-4 grid grid-cols-1 divide-y divide-slate-100 text-xs text-slate-500 dark:divide-gray-800 dark:text-gray-400 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                        <div class="flex items-center gap-2 py-2 sm:py-0 sm:pr-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300"><svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4.5" width="14" height="13" rx="2"/><path stroke-linecap="round" d="M6.5 2.5v4M13.5 2.5v4M3 8h14"/></svg></span><span class="min-w-0"><dt class="font-semibold text-slate-600 dark:text-gray-300">Mulai</dt><dd class="truncate">{{ $session->mulai_pada->format('d M Y, H:i') }}</dd></span></div>
                        <div class="flex items-center gap-2 py-2 sm:px-3 sm:py-0"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300"><svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4.5" width="14" height="13" rx="2"/><path stroke-linecap="round" d="M6.5 2.5v4M13.5 2.5v4M3 8h14M7 11.5h2"/></svg></span><span class="min-w-0"><dt class="font-semibold text-slate-600 dark:text-gray-300">Selesai</dt><dd class="truncate">{{ $session->selesai_pada->format('d M Y, H:i') }}</dd></span></div>
                        <div class="flex items-center gap-2 py-2 sm:py-0 sm:pl-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300"><svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.7"><path d="M16 8.2c0 4.1-6 9.3-6 9.3S4 12.3 4 8.2a6 6 0 1 1 12 0Z"/><circle cx="10" cy="8" r="2"/></svg></span><span class="min-w-0"><dt class="font-semibold text-slate-600 dark:text-gray-300">Lokasi</dt><dd class="truncate">@if($session->tautan)<a class="text-primary-600 hover:underline" href="{{ $session->tautan }}" target="_blank" rel="noopener">{{ $session->lokasi ?: 'Buka tautan' }}</a>@else{{ $session->lokasi ?: '-' }}@endif</dd></span></div>
                    </dl>
                    @php($sessionStatus = now()->greaterThanOrEqualTo($session->selesai_pada) ? 'Selesai' : (now()->greaterThanOrEqualTo($session->mulai_pada) ? 'Berlangsung' : 'Akan datang'))
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-gray-800">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $session->jenis }}</span>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold {{ $sessionStatus === 'Selesai' ? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' : ($sessionStatus === 'Berlangsung' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-cyan-50 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300') }}">{{ $sessionStatus }}</span>
                            @if($canManage && ! $session->is_published)<span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Draft</span>@endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:ml-auto">
                            @if($canManage || (int) $session->dibuat_oleh === (int) auth()->id())
                            <button wire:click="edit({{ $session->id }})" class="inline-flex items-center gap-1 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-sky-100 dark:bg-blue-900/30 dark:text-blue-300">Edit</button>
                            @if($canManage)<button wire:click="confirmDelete({{ $session->id }})" class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-300">Hapus</button>@endif
                            <button wire:click="selectSession({{ $session->id }})" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Kelola Peserta & Sertifikat</button>
                            @endif
                            @if($canJoin)
                                @if(! $employeeLinked)
                                    <span class="text-xs text-amber-700 dark:text-amber-300">Akun belum terhubung ke data pegawai</span>
                                @elseif($myRegistration)
                                    <span class="inline-flex rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $myRegistration->status === 'attended' ? 'Hadir' : ($myRegistration->status === 'absent' ? 'Tidak hadir' : 'Sudah terdaftar') }}</span>
                                @elseif(now()->greaterThan($session->selesai_pada))
                                    <span class="text-xs text-gray-500">Pendaftaran ditutup.</span>
                                @elseif($session->kuota !== null && $session->registrations_count >= $session->kuota)
                                    <span class="text-xs text-gray-500">Kuota penuh.</span>
                                @else
                                    <button wire:click="register({{ $session->id }})" wire:loading.attr="disabled" class="btn-primary text-xs">Daftar Kegiatan</button>
                                @endif
                            @endif
                        </div>
                    </div>
                    </div>
                </article>
            @empty
                <div class="lg:col-span-2 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-10 text-center text-sm text-gray-500">Belum ada jadwal kegiatan yang dipublikasikan.</div>
            @endforelse
        </div>
        <div>{{ $sessions->links() }}</div>
    @elseif($tab === 'riwayat' && $canManage)
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Pelatihan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kegiatan yang sudah selesai, diurutkan dari tanggal selesai terbaru.</p>
        </div>
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500 dark:bg-gray-800/60">
                        <tr><th class="px-4 py-3">Kegiatan</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Mulai</th><th class="px-4 py-3">Selesai</th><th class="px-4 py-3">Peserta</th><th class="px-4 py-3">Lokasi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($trainingHistory as $session)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $session->judul }}</td>
                                <td class="px-4 py-3 capitalize">{{ $session->jenis }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $session->mulai_pada->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $session->selesai_pada->format('d M Y, H:i') }}</span></td>
                                <td class="px-4 py-3">{{ $session->registrations_count }} peserta</td>
                                <td class="px-4 py-3">{{ $session->lokasi ?: ($session->tautan ? 'Online' : '-') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada kegiatan pelatihan yang selesai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-800">{{ $trainingHistory->links() }}</div>
        </div>
    @elseif($tab === 'saya')
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Pelatihan Saya</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kegiatan masuk ke riwayat ini setelah penyelenggara mencatat status Hadir.</p>
        </div>
        @if(! $employeeLinked)
            <div class="rounded-2xl bg-amber-50 dark:bg-amber-900/20 p-5 text-sm text-amber-800 dark:text-amber-200">Akun Anda belum terhubung ke data pegawai. Hubungi HR.</div>
        @else
            <div wire:poll.60s.visible class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Kegiatan</th><th class="px-4 py-3">Jenis</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Kehadiran</th><th class="px-4 py-3">Status / MOM</th><th class="px-4 py-3">Sertifikat</th></tr></thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($myRegistrations as $registration)
                                @php($sessionEnded = $registration->session && now()->greaterThanOrEqualTo($registration->session->selesai_pada))
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $registration->session?->judul ?? 'Kegiatan dihapus' }}</td>
                                    <td class="px-4 py-3">{{ $registration->session?->jenis ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $registration->session?->mulai_pada?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-4 py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Hadir</span></td>
                                    <td class="px-4 py-3">
                                        @if($sessionEnded)
                                            @if($registration->mom)
                                                <div class="space-y-1"><span class="block rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Selesai · MOM diisi</span><button wire:click="openMom({{ $registration->id }})" class="text-xs font-semibold text-primary-600 hover:underline">Lihat / Edit MOM</button></div>
                                            @else
                                                <div class="space-y-1"><span class="block rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Selesai · MOM wajib diisi</span><button wire:click="openMom({{ $registration->id }})" class="text-xs font-semibold text-primary-600 hover:underline">Isi MOM</button></div>
                                            @endif
                                        @else
                                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">Menunggu kegiatan selesai</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($registration->certificate_path)
                                            <div class="flex flex-col items-start gap-1"><a href="{{ route('training.certificate', $registration) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">Lihat sertifikat</a><a href="{{ route('training.certificate', ['registration' => $registration->id, 'download' => 1]) }}" class="text-xs font-medium text-gray-500 hover:underline">Unduh</a></div>
                                        @else
                                            <span class="text-xs text-gray-400">Belum tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Belum ada riwayat pelatihan yang diikuti.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @elseif($tab === 'tim')
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Riwayat Pelatihan Tim</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Status pendaftaran dan kehadiran pegawai di bawah struktur jabatan Anda.</p>
        </div>
        <div class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-left text-xs uppercase text-gray-500"><tr><th class="px-4 py-3">Pegawai</th><th class="px-4 py-3">Kegiatan</th><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Status</th></tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($teamRegistrations as $registration)
                            <tr><td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">{{ $registration->employee?->nama ?? '-' }}</td><td class="px-4 py-3">{{ $registration->session?->judul ?? '-' }}</td><td class="px-4 py-3">{{ $registration->session?->mulai_pada?->format('d M Y, H:i') ?? '-' }}</td><td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $registration->status === 'attended' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : ($registration->status === 'absent' ? 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300') }}">{{ $registration->status === 'attended' ? 'Hadir' : ($registration->status === 'absent' ? 'Tidak hadir' : 'Terdaftar') }}</span></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">Belum ada riwayat pendaftaran dari tim Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-800 px-4 py-3">{{ $teamRegistrations->links() }}</div>
        </div>

    @elseif($tab === 'kelola' && $canManage)
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kelola Seminar & Pelatihan</h2><p class="text-sm text-gray-500 dark:text-gray-400">Buat jadwal dan catat kehadiran peserta.</p></div>
            <button wire:click="openCreate" class="btn-primary text-xs">Buat Kegiatan</button>
        </div>

        @if($showForm)
            <form wire:submit.prevent="save" class="space-y-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <h3 class="font-bold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit Kegiatan' : 'Kegiatan Baru' }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><x-input-label value="Judul *"/><x-text-input type="text" wire:model="judul" class="mt-1 block w-full"/>@error('judul')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Jenis *"/><select wire:model="jenis" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"><option value="seminar">Seminar</option><option value="workshop">Workshop</option><option value="pelatihan">Pelatihan</option></select>@error('jenis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Mulai *"/><input type="datetime-local" wire:model="mulaiPada" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"/>@error('mulaiPada')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Selesai *"/><input type="datetime-local" wire:model="selesaiPada" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"/>@error('selesaiPada')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Lokasi"/><x-text-input type="text" wire:model="lokasi" class="mt-1 block w-full"/>@error('lokasi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Tautan online"/><x-text-input type="url" wire:model="tautan" class="mt-1 block w-full" placeholder="https://..."/>@error('tautan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div><x-input-label value="Kuota (kosongkan jika tanpa batas)"/><x-text-input type="number" min="1" wire:model="kuota" class="mt-1 block w-full"/>@error('kuota')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    <div class="flex items-center gap-2 pt-6"><input id="training-published" type="checkbox" wire:model="isPublished" class="rounded border-gray-300 text-primary-600"><label for="training-published" class="text-sm text-gray-700 dark:text-gray-300">Publikasikan agar bisa didaftari</label></div>
                    <div class="md:col-span-2"><x-input-label value="Deskripsi"/><textarea wire:model="deskripsi" rows="3" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"></textarea>@error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                </div>
                <div class="flex justify-end gap-2"><button type="button" wire:click="cancelForm" class="btn-secondary text-xs">Batal</button><button type="submit" class="btn-primary text-xs">Simpan</button></div>
            </form>
        @endif

        @if($selectedSession)
            <div class="rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
                <div class="flex items-start justify-between gap-3"><div><h3 class="font-bold text-gray-900 dark:text-gray-100">Peserta: {{ $selectedSession->judul }}</h3><p class="text-xs text-gray-500">Atur status kehadiran untuk memperbarui riwayat peserta.</p></div><button wire:click="$set('selectedSessionId', null)" class="text-sm text-gray-500 hover:text-gray-900">Tutup</button></div>
                <div class="mt-4 overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="border-b text-left text-xs uppercase text-gray-500"><th class="py-2 pr-3">Pegawai</th><th class="py-2 pr-3">Status</th><th class="py-2">Tindakan</th></tr></thead><tbody>
                    @forelse($selectedSession->registrations as $registration)
                        <tr class="border-b border-gray-100 dark:border-gray-800"><td class="py-3 pr-3">{{ $registration->employee?->nama ?? '-' }}</td><td class="py-3 pr-3">{{ $registration->status === 'attended' ? 'Hadir' : ($registration->status === 'absent' ? 'Tidak hadir' : 'Terdaftar') }}</td><td class="py-3"><div class="flex gap-2"><button wire:click="markAttendance({{ $registration->id }}, 'attended')" class="text-xs font-semibold text-blue-700">Hadir</button><button wire:click="markAttendance({{ $registration->id }}, 'absent')" class="text-xs font-semibold text-red-600">Tidak hadir</button></div></td></tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-gray-500">Belum ada peserta terdaftar.</td></tr>
                    @endforelse
                </tbody></table></div>
            </div>
        @endif

        <div class="space-y-3">
            @forelse($sessions as $session)
                <article class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
                    <div><h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $session->judul }}</h3><p class="text-xs text-gray-500">{{ $session->mulai_pada->format('d M Y, H:i') }} · {{ $session->registrations_count }} peserta · {{ $session->is_published ? 'Dipublikasikan' : 'Draft' }}</p></div>
                    <div class="flex gap-3"><button wire:click="edit({{ $session->id }})" class="text-xs font-semibold text-primary-600">Edit</button><button wire:click="selectSession({{ $session->id }})" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Kelola Peserta</button></div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 p-8 text-center text-sm text-gray-500">Belum ada kegiatan. Buat kegiatan untuk mulai mengelola pelatihan.</div>
            @endforelse
        </div>
        <div>{{ $sessions->links() }}</div>
    @endif
    @if($showForm)
        <div class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-gray-950/70 p-3 backdrop-blur-sm sm:p-6" wire:key="training-form-modal" wire:click.self="cancelForm">
            <section class="my-auto max-h-[94dvh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-5 shadow-2xl dark:border-gray-700 dark:bg-gray-900 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="training-form-title">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div><h2 id="training-form-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit Kegiatan' : 'Buat Kegiatan' }}</h2><p class="mt-1 text-sm text-gray-500">Lengkapi informasi kegiatan seminar atau pelatihan.</p></div>
                    <button type="button" wire:click="cancelForm" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tutup">&times;</button>
                </div>
                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div><x-input-label value="Judul *"/><x-text-input type="text" wire:model.live.debounce.300ms="judul" class="mt-1 block w-full"/>@error('judul')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Jenis *"/><select wire:model="jenis" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"><option value="seminar">Seminar</option><option value="workshop">Workshop</option><option value="pelatihan">Pelatihan</option></select>@error('jenis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Mulai *"/><input type="datetime-local" wire:model="mulaiPada" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"/>@error('mulaiPada')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Selesai *"/><input type="datetime-local" wire:model="selesaiPada" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"/>@error('selesaiPada')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Lokasi"/><x-text-input type="text" wire:model="lokasi" class="mt-1 block w-full"/>@error('lokasi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Tautan online"/><x-text-input type="url" wire:model="tautan" class="mt-1 block w-full" placeholder="https://..."/>@error('tautan')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <div><x-input-label value="Kuota (kosongkan jika tanpa batas)"/><x-text-input type="number" min="1" wire:model="kuota" class="mt-1 block w-full"/>@error('kuota')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                        <label class="flex items-center gap-2 self-end pb-2 text-sm text-gray-700 dark:text-gray-300"><input type="checkbox" wire:model="isPublished" class="rounded border-gray-300 text-primary-600">Publikasikan kegiatan</label>
                        <div class="sm:col-span-2">
                            <x-input-label for="training-thumbnail-upload" value="Thumbnail kegiatan (opsional)" />
                            <input id="training-thumbnail-upload" type="file" wire:model="thumbnail" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300 dark:file:bg-blue-900/30 dark:file:text-blue-300" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih gambar JPG, PNG, atau WebP hingga 4 MB. Jika dikosongkan, gambar otomatis dibuat dari judul.</p>
                            @error('thumbnail')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            <div wire:loading wire:target="thumbnail" class="mt-2 text-xs text-blue-600">Mengunggah gambar...</div>
                        </div>
                        <div class="sm:col-span-2"><x-input-label value="Deskripsi"/><textarea wire:model="deskripsi" rows="4" class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800"></textarea>@error('deskripsi')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
                    </div>
                    <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                        <p class="border-b border-gray-100 px-3 py-2 text-xs font-semibold text-gray-500 dark:border-gray-800">Preview thumbnail</p>
                        <x-training-thumbnail :title="$judul ?: 'Judul kegiatan'" :jenis="$jenis" :description="$deskripsi" :image-url="$thumbnail ? $thumbnail->temporaryUrl() : $existingThumbnailUrl" />
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 dark:border-gray-800"><button type="button" wire:click="cancelForm" class="btn-secondary text-xs">Batal</button><button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs"><span wire:loading.remove wire:target="save">{{ $editId ? 'Simpan Perubahan' : 'Buat Kegiatan' }}</span><span wire:loading wire:target="save">Menyimpan...</span></button></div>
                </form>
            </section>
        </div>
    @endif

    @if($momRegistrationId)
        <div class="fixed inset-0 z-[75] flex items-center justify-center overflow-y-auto bg-gray-950/70 p-3 backdrop-blur-sm sm:p-6" wire:key="training-mom-modal-{{ $momRegistrationId }}" wire:click.self="closeMom">
            <section class="my-auto max-h-[90dvh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-5 shadow-2xl dark:border-gray-700 dark:bg-gray-900 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="training-mom-title">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div><h2 id="training-mom-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">MOM Pelatihan</h2><p class="mt-1 text-sm text-gray-500">Tuliskan ringkasan materi, poin penting, dan tindak lanjut yang diperoleh.</p></div>
                    <button type="button" wire:click="closeMom" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tutup">&times;</button>
                </div>
                <form wire:submit.prevent="saveMom" class="space-y-4">
                    <div>
                        <x-input-label for="training-mom-text" value="Isi MOM *" />
                        <textarea id="training-mom-text" wire:model="momText" rows="9" maxlength="20000" class="mt-1 block w-full rounded-xl border-gray-200 text-sm dark:border-gray-700 dark:bg-gray-800" placeholder="1. Ringkasan kegiatan&#10;2. Materi atau hal penting yang dipelajari&#10;3. Rencana tindak lanjut"></textarea>
                        @error('momText')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 dark:border-gray-800"><button type="button" wire:click="closeMom" class="btn-secondary text-xs">Batal</button><button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs"><span wire:loading.remove wire:target="saveMom">Simpan MOM</span><span wire:loading wire:target="saveMom">Menyimpan...</span></button></div>
                </form>
            </section>
        </div>
    @endif

    @if($canManageSelectedSession && $selectedSession)
        <div class="fixed inset-0 z-[75] flex items-center justify-center overflow-y-auto bg-gray-950/70 p-3 backdrop-blur-sm sm:p-6" wire:key="training-participants-modal-{{ $selectedSession->id }}" wire:click.self="$set('selectedSessionId', null)">
            <section class="my-auto max-h-[90dvh] w-full max-w-3xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900" role="dialog" aria-modal="true" aria-labelledby="training-participants-title">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 dark:border-gray-800 sm:p-6">
                    <div><h2 id="training-participants-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">Peserta: {{ $selectedSession->judul }}</h2><p class="mt-1 text-sm text-gray-500">{{ $canManage ? 'Atur kehadiran dan berikan sertifikat kepada peserta yang hadir.' : 'Berikan sertifikat kepada peserta yang sudah tercatat hadir.' }}</p></div>
                    <button type="button" wire:click="$set('selectedSessionId', null)" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tutup">&times;</button>
                </div>
                <div class="max-h-[calc(90dvh-6rem)] overflow-auto p-5 sm:p-6">
                    <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500 dark:bg-gray-800/60"><tr><th class="px-4 py-3">Pegawai</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Kehadiran</th><th class="px-4 py-3">Sertifikat</th></tr></thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($selectedSession->registrations as $registration)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $registration->employee?->nama ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $registration->status === 'attended' ? 'Hadir' : ($registration->status === 'absent' ? 'Tidak hadir' : 'Terdaftar') }}</td>
                                        <td class="px-4 py-3">
                                            @if($canManage)<div class="flex flex-wrap gap-2"><button wire:click="markAttendance({{ $registration->id }}, 'attended')" class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">Hadir</button><button wire:click="markAttendance({{ $registration->id }}, 'absent')" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300">Tidak hadir</button></div>@else<span class="text-xs text-gray-400">Dikelola admin</span>@endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($registration->status === 'attended')
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if($registration->certificate_path)<a href="{{ route('training.certificate', $registration) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">Lihat</a>@endif
                                                    <button wire:click="openCertificateUpload({{ $registration->id }})" class="rounded-lg bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary-700 hover:bg-primary-100 dark:bg-primary-900/30 dark:text-primary-300">{{ $registration->certificate_path ? 'Ganti sertifikat' : 'Berikan sertifikat' }}</button>
                                                </div>
                                            @else<span class="text-xs text-gray-400">Tersedia setelah status Hadir</span>@endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada peserta terdaftar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    @endif

    @if($selectedCertificateRegistration)
        <div class="fixed inset-0 z-[90] flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm" wire:key="training-certificate-modal-{{ $selectedCertificateRegistration->id }}" wire:click.self="closeCertificateUpload">
            <section class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-5 shadow-2xl dark:border-gray-700 dark:bg-gray-900 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="training-certificate-title">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div><h2 id="training-certificate-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">Sertifikat peserta</h2><p class="mt-1 text-sm text-gray-500">{{ $selectedCertificateRegistration->employee?->nama }} · {{ $selectedCertificateRegistration->session?->judul }}</p></div>
                    <button type="button" wire:click="closeCertificateUpload" class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800" aria-label="Tutup">&times;</button>
                </div>
                @if($selectedCertificateRegistration->certificate_path)
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-primary-50 p-3 dark:bg-primary-900/20">
                        <div class="min-w-0"><p class="text-xs font-semibold text-primary-800 dark:text-primary-200">Sertifikat saat ini</p><p class="truncate text-xs text-primary-700 dark:text-primary-300">{{ $selectedCertificateRegistration->certificate_name }}</p></div>
                        <a href="{{ route('training.certificate', ['registration' => $selectedCertificateRegistration->id, 'download' => 1]) }}" class="shrink-0 text-xs font-semibold text-primary-700 hover:underline dark:text-primary-300">Unduh</a>
                    </div>
                @endif
                <form wire:submit.prevent="saveCertificate" class="space-y-4">
                    <div>
                        <label for="training-certificate-upload" class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $selectedCertificateRegistration->certificate_path ? 'Pilih pengganti sertifikat' : 'Pilih sertifikat' }}</label>
                        <input id="training-certificate-upload" type="file" wire:model="certificateUpload" accept="application/pdf,image/jpeg,image/png" class="mt-2 block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-primary-700 hover:file:bg-primary-100 dark:text-gray-300 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                        <p class="mt-1 text-xs text-gray-500">PDF, JPG, atau PNG. Maksimal 10 MB.</p>
                        <div wire:loading wire:target="certificateUpload" class="mt-2 text-xs text-primary-600">Mengunggah sertifikat...</div>
                        @error('certificateUpload')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 dark:border-gray-800"><button type="button" wire:click="closeCertificateUpload" class="btn-secondary text-xs">Batal</button><button type="submit" wire:loading.attr="disabled" class="btn-primary text-xs">Simpan Sertifikat</button></div>
                </form>
            </section>
        </div>
    @endif

    @if($deletingSessionId)
        <div class="fixed inset-0 z-[80] flex items-center justify-center bg-gray-950/70 p-4 backdrop-blur-sm" wire:click.self="$set('deletingSessionId', null)">
            <section class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900" role="alertdialog" aria-modal="true" aria-labelledby="training-delete-title">
                <h2 id="training-delete-title" class="text-lg font-bold text-gray-900 dark:text-gray-100">Hapus kegiatan?</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Kegiatan beserta data pendaftaran pesertanya akan dihapus dan tidak dapat dipulihkan.</p>
                <div class="mt-6 flex justify-end gap-2"><button type="button" wire:click="$set('deletingSessionId', null)" class="btn-secondary text-xs">Batal</button><button type="button" wire:click="deleteSession" wire:loading.attr="disabled" class="btn-danger text-xs">Ya, Hapus</button></div>
            </section>
        </div>
    @endif
</div>

