<div>
    <div class="flex items-center justify-between px-6 py-3 bg-gray-100 dark:bg-gray-800 rounded-xl">
        @php
            $tipeCounts = [
                'karyawan_aktif' => \App\Models\Employee::where('tipe', 'karyawan_aktif')->count(),
                'calon_karyawan' => \App\Models\Employee::where('tipe', 'calon_karyawan')->count(),
                'mantan_karyawan' => \App\Models\Employee::where('tipe', 'mantan_karyawan')->count(),
            ];
        @endphp
        <button wire:click="$set('filterStatus', 'karyawan_aktif')"
            class="flex-1 justify-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $filterStatus === 'karyawan_aktif' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-600 hover:text-emerald-600 dark:text-white dark:hover:text-emerald-300' }}">
            <span class="inline-flex items-center gap-1">
                @if($filterStatus === 'karyawan_aktif')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                @endif
                Karyawan Aktif
                <span class="ml-0.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $filterStatus === 'karyawan_aktif' ? 'bg-emerald-600 text-emerald-100' : 'bg-gray-300 text-gray-700 dark:bg-white/20 dark:text-white' }}">{{ $tipeCounts['karyawan_aktif'] }}</span>
            </span>
        </button>
        <button wire:click="$set('filterStatus', 'calon_karyawan')"
            class="flex-1 justify-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $filterStatus === 'calon_karyawan' ? 'bg-blue-500 text-white shadow-sm' : 'text-gray-600 hover:text-blue-600 dark:text-white dark:hover:text-blue-300' }}">
            <span class="inline-flex items-center gap-1">
                @if($filterStatus === 'calon_karyawan')
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                @endif
                Calon Karyawan
                <span class="ml-0.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $filterStatus === 'calon_karyawan' ? 'bg-blue-600 text-blue-100' : 'bg-gray-300 text-gray-700 dark:bg-white/20 dark:text-white' }}">{{ $tipeCounts['calon_karyawan'] }}</span>
            </span>
        </button>
        <button wire:click="$set('filterStatus', 'mantan_karyawan')"
            class="flex-1 justify-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 {{ $filterStatus === 'mantan_karyawan' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 hover:text-red-600 dark:text-white dark:hover:text-red-300' }}">
            <span class="inline-flex items-center gap-1">
                Mantan Karyawan
                <span class="ml-0.5 px-1.5 py-0.5 rounded-full text-[10px] {{ $filterStatus === 'mantan_karyawan' ? 'bg-red-600 text-red-100' : 'bg-gray-300 text-gray-700 dark:bg-white/20 dark:text-white' }}">{{ $tipeCounts['mantan_karyawan'] }}</span>
            </span>
        </button>
    </div>

    <div class="card overflow-hidden mt-5">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
        <div class="flex items-center gap-3 flex-1">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari NIK, nama, email..."
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                >
            </div>

            <select wire:model.live="filterDivision" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                    <option value="{{ $div->id }}">{{ $div->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('hris.export.employees') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Export Excel
            </a>
            @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
            <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Karyawan
            </button>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="table-header">
                    <th class="px-6 py-3 w-12 text-center">No</th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('nik')">
                        <div class="flex items-center gap-1">
                            NIK
                            @if($sortField === 'nik')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M19.5 8.25l-7.5 7.5-7.5-7.5' : 'M4.5 15.75l7.5-7.5 7.5 7.5' }}"/></svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('nama')">
                        <div class="flex items-center gap-1">
                            Nama
                            @if($sortField === 'nama')
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M19.5 8.25l-7.5 7.5-7.5-7.5' : 'M4.5 15.75l7.5-7.5 7.5 7.5' }}"/></svg>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3">Jabatan</th>
                    <th class="px-6 py-3">Divisi</th>
                    <th class="px-6 py-3">Tanggal Masuk</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                        <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $employees->firstItem() + $loop->index }}</td>
                        <td class="table-cell font-mono font-medium text-gray-900 dark:text-gray-100">{{ $emp->nik }}</td>
                        <td class="table-cell">
                            <div class="flex items-center gap-2">
                                @if($emp->foto_url)
                                    <img src="{{ $emp->foto_url }}" alt="{{ $emp->nama }}" class="w-10 h-10 rounded-lg object-contain bg-gray-50">
                                @else
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 font-semibold text-xs">
                                        {{ strtoupper(substr($emp->nama, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $emp->nama }}</span>
                            </div>
                        </td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $emp->position ?? '-' }}</td>
                        <td class="table-cell text-gray-600 dark:text-gray-400">{{ $emp->divisionNames() ?: '-' }}</td>
                        <td class="table-cell text-gray-500 dark:text-gray-400">{{ $emp->tanggal_masuk?->format('d M Y') ?? '-' }}</td>
                        <td class="table-cell">
                            @if($emp->tipe == 'karyawan_aktif')
                                <span class="badge-success">Karyawan Aktif</span>
                            @elseif($emp->tipe == 'calon_karyawan')
                                <span class="badge-info">Calon Karyawan</span>
                            @else
                                <span class="badge-danger">Mantan Karyawan</span>
                            @endif
                        </td>
                        <td class="table-cell text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('hris.employees.show', $emp) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lihat Detail
                                </a>

                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" @click.outside="open = false" class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         @click.outside="open = false" class="absolute right-0 z-50 mt-1 w-40 rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 py-1.5">
                                        @if(auth()->user()->canUpdateData() && ! auth()->user()->isGmCeo())
                                        <button wire:click="openEditModal({{ $emp->id }})" @click="open = false" class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            Edit
                                        </button>
                                        @endif
                                        @if(auth()->user()->canDeleteData() && ! auth()->user()->isGmCeo())
                                        <button wire:click="confirmDelete({{ $emp->id }})" @click="open = false" class="flex w-full items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Hapus
                                        </button>
                                        @endif
                                    </div>
                                </div>
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
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada data karyawan</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambah karyawan pertama untuk memulai</p>
                                @if(auth()->user()->canCreateData() && ! auth()->user()->isGmCeo())
                                <button wire:click="openCreateModal" class="btn-primary mt-4 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    Tambah Karyawan
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employees->hasPages())
        <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
            {{ $employees->links() }}
        </div>
    @endif
    </div>

    {{-- ============ CREATE MODAL ============ --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showCreateModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-8">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Karyawan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Lengkapi data karyawan baru</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- STEP INDICATOR --}}
            <div class="mb-5">
                <div class="flex items-center justify-center gap-0">
                    @foreach ([1 => 'Informasi Pribadi', 2 => 'Data Pekerjaan', 3 => 'Kontak & Darurat'] as $num => $label)
                        <div class="flex items-center">
                            <button type="button" wire:click="goToStep({{ $num }})" class="flex flex-col items-center gap-1.5 group">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition-all duration-200
                                    {{ $step == $num ? 'bg-primary-600 text-white ring-4 ring-primary-100 dark:ring-primary-900/50' : '' }}
                                    {{ $step > $num ? 'bg-emerald-500 text-white' : '' }}
                                    {{ $step < $num ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' : '' }}">
                                    @if($step > $num)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    @else
                                        {{ $num }}
                                    @endif
                                </div>
                                <span class="text-[10px] font-medium whitespace-nowrap
                                    {{ $step == $num ? 'text-primary-600 dark:text-primary-400' : '' }}
                                    {{ $step > $num ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                                    {{ $step < $num ? 'text-gray-400 dark:text-gray-500' : '' }}">{{ $label }}</span>
                            </button>
                            @if($num < 3)
                                <div class="w-16 sm:w-24 h-px mx-2 mb-5
                                    {{ $step > $num ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- FORM --}}
            <form wire:submit.prevent="confirmPreview">

                {{-- STEP 1: Informasi Pribadi --}}
                @if($step == 1)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="create-nik_ktp" value="NIK KTP *" />
                            <x-text-input id="create-nik_ktp" wire:model="nik_ktp" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('nik_ktp') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="Masukan NIK KTP" />
                            <x-input-error :messages="$errors->get('nik_ktp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-nama" value="Nama Lengkap *" />
                            <x-text-input id="create-nama" wire:model="nama" type="text" class="mt-1 block w-full {{ $errors->has('nama') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="Masukan nama karyawan" />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="create-tempat_lahir" wire:model="tempat_lahir" type="text" class="mt-1 block w-full {{ $errors->has('tempat_lahir') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="Bandung" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="create-tanggal_lahir" wire:model="tanggal_lahir" type="date" class="mt-1 block w-full {{ $errors->has('tanggal_lahir') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-jenis_kelamin" value="Jenis Kelamin" />
                            <select id="create-jenis_kelamin" wire:model="jenis_kelamin" class="mt-1 block w-full rounded-xl border @error('jenis_kelamin') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-tipe" value="Tipe Karyawan *" />
                            <select id="create-tipe" wire:model="tipe" class="mt-1 block w-full rounded-xl border @error('tipe') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="karyawan_aktif">Karyawan Aktif</option>
                                <option value="calon_karyawan">Calon Karyawan</option>
                                <option value="mantan_karyawan">Mantan Karyawan</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipe')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-ukuran_baju" value="Ukuran Baju" />
                            <select id="create-ukuran_baju" wire:model="ukuran_baju" class="mt-1 block w-full rounded-xl border @error('ukuran_baju') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                            <x-input-error :messages="$errors->get('ukuran_baju')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-agama" value="Agama" />
                            <select id="create-agama" wire:model="agama" class="mt-1 block w-full rounded-xl border @error('agama') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="islam">Islam</option>
                                <option value="katolik">Katolik</option>
                                <option value="protestan">Protestan</option>
                                <option value="hindu">Hindu</option>
                                <option value="budha">Budha</option>
                            </select>
                            <x-input-error :messages="$errors->get('agama')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-pendidikan_terakhir" value="Pendidikan Terakhir" />
                            <select id="create-pendidikan_terakhir" wire:model="pendidikan_terakhir" class="mt-1 block w-full rounded-xl border @error('pendidikan_terakhir') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="sekolah dasar (SD)">Sekolah Dasar (SD)</option>
                                <option value="sekolah menengah pertama (SMP)">Sekolah Menengah Pertama (SMP)</option>
                                <option value="sekolah menengah atas (SMA)">Sekolah Menengah Atas (SMA) Sederajat</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                            <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-asal_sekolah" value="Asal Sekolah" />
                            <textarea id="create-asal_sekolah" wire:model="asal_sekolah" rows="1" class="mt-1 block w-full rounded-xl border @error('asal_sekolah') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Masukan Asal Sekolah"></textarea>
                            <x-input-error :messages="$errors->get('asal_sekolah')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-status_pernikahan" value="Status Pernikahan" />
                            <select id="create-status_pernikahan" wire:model="status_pernikahan" class="mt-1 block w-full rounded-xl border @error('status_pernikahan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                @foreach(\App\Models\Employee::STATUS_PERKAWINAN_OPTIONS as $nilai => $label)
                                    <option value="{{ $nilai }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_pernikahan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-provinsi" value="Provinsi" />
                            <select id="create-provinsi" wire:model.live="provinsi" class="mt-1 block w-full rounded-xl border @error('provinsi') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinceList as $pid => $pname)
                                    <option value="{{ $pid }}">{{ ucwords(strtolower($pname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-kota" value="Kota/Kabupaten" />
                            <select id="create-kota" wire:model.live="kota" class="mt-1 block w-full rounded-xl border @error('kota') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                @foreach($cityList as $cid => $cname)
                                    <option value="{{ $cid }}">{{ ucwords(strtolower($cname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kota')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-kecamatan" value="Kecamatan" />
                            <select id="create-kecamatan" wire:model.live="kecamatan" class="mt-1 block w-full rounded-xl border @error('kecamatan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($districtList as $did => $dname)
                                    <option value="{{ $did }}">{{ ucwords(strtolower($dname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-kelurahan" value="Kelurahan/Desa" />
                            <select id="create-kelurahan" wire:model.live="kelurahan" class="mt-1 block w-full rounded-xl border @error('kelurahan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                                @foreach($villageList as $vid => $vname)
                                    <option value="{{ $vid }}">{{ ucwords(strtolower($vname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kelurahan')" class="mt-2" />
                        </div>
                        <div>
                            <div class="flex flex-col sm:flex-row gap-2 items-end">
                                <div class="flex-1">
                                    <x-input-label for="create-rt" value="RT / RW" />
                                    <div class="mt-1 flex items-center gap-0">
                                        <input id="create-rt" type="text" maxlength="3" inputmode="numeric" placeholder="001"
                                            class="rt-rw-input input-field !w-16 !rounded-r-none !text-center !px-2 {{ $errors->has('rt_rw') ? '!border-red-400 !focus:border-red-400 !focus:ring-red-100' : '' }}"
                                            data-target="create-rw" data-group="create" />
                                        <span class="input-field !w-auto !rounded-none !px-2 !border-l-0 !border-r-0 cursor-default select-none">/</span>
                                        <input id="create-rw" type="text" maxlength="3" inputmode="numeric" placeholder="002"
                                            class="rt-rw-input input-field !w-16 !rounded-l-none !text-center !px-2 {{ $errors->has('rt_rw') ? '!border-red-400 !focus:border-red-400 !focus:ring-red-100' : '' }}"
                                            data-group="create" />
                                    </div>
                                    <input type="hidden" wire:model="rt_rw" id="create-rt_rw" />
                                    <x-input-error :messages="$errors->get('rt_rw')" class="mt-2" />
                                </div>
                                <div class="w-full sm:w-[300px]">
                                    <x-input-label for="create-kode_pos" value="Kode Pos" />
                                    <x-text-input id="create-kode_pos" wire:model="kode_pos" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('kode_pos') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="40134" />
                                    <x-input-error :messages="$errors->get('kode_pos')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="create-alamat" value="Alamat Lengkap" />
                            <textarea id="create-alamat" wire:model="alamat" rows="2" class="mt-1 block w-full rounded-xl border @error('alamat') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Jl. Contoh No. 123"></textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>
                    </div>
                </div>
                @endif

                {{-- STEP 2: Data Pekerjaan --}}
                @if($step == 2)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="create-nik" value="NIP (Nomor Induk Pegawai) *" />
                            <x-text-input id="create-nik" wire:model="nik" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('nik') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="Masukan NIP" />
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                        </div>
                        <div x-data="{ open: false }" class="relative">
                            <x-input-label value="Divisi" />
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full mt-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                                <span>{{ count($division_ids) > 0 ? count($division_ids) . ' divisi dipilih' : 'Pilih divisi' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                @foreach($divisions as $div)
                                    <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors {{ in_array($div->id, $division_ids) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <input type="checkbox" value="{{ $div->id }}" wire:model="division_ids"
                                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $div->nama }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @error('division_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ open: false }" class="relative">
                            <x-input-label value="Jabatan" />
                            <input type="hidden" wire:model="position">
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full mt-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                                <span>{{ count($position_ids) > 0 ? count($position_ids) . ' jabatan dipilih' : 'Pilih jabatan' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                @foreach($allPositions as $pos)
                                    <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors {{ in_array($pos->id, $position_ids) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <input type="checkbox" value="{{ $pos->id }}" wire:model.live="position_ids"
                                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $pos->nama }}</span>
                                        <input type="radio" value="{{ $pos->id }}" wire:model.live="main_position_id"
                                               class="text-primary-600 focus:ring-primary-500">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">Utama</span>
                                    </label>
                                @endforeach
                            </div>
                        @error('position_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        @error('main_position_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label for="create-atasan" value="Atasan 1" />
                            <select id="create-atasan" wire:model="atasan" class="mt-1 block w-full rounded-xl border @error('atasan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih atasan --</option>
                                @foreach(\App\Models\Employee::ATASAN_OPTIONS as $namaAtasan)
                                    <option value="{{ $namaAtasan }}">{{ $namaAtasan }}</option>
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('atasan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-atasan2" value="Atasan 2" />
                            <select id="create-atasan2" wire:model="atasan2" class="mt-1 block w-full rounded-xl border @error('atasan2') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih atasan 2 --</option>
                                @foreach(\App\Models\Employee::ATASAN_OPTIONS as $namaAtasan)
                                    <option value="{{ $namaAtasan }}">{{ $namaAtasan }}</option>
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('atasan2')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-tanggal_masuk" value="Tanggal Bergabung" />
                            <x-text-input id="create-tanggal_masuk" wire:model="tanggal_masuk" type="date" class="mt-1 block w-full {{ $errors->has('tanggal_masuk') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-jenis_karyawan" value="Jenis Karyawan" />
                            <select id="create-jenis_karyawan" wire:model="jenis_karyawan" class="mt-1 block w-full rounded-xl border @error('jenis_karyawan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="magang">Magang</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_karyawan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-lokasi_kerja" value="Lokasi Kerja" />
                            <select id="create-lokasi_kerja" wire:model="lokasi_kerja" class="mt-1 block w-full rounded-xl border @error('lokasi_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih lokasi kerja</option>
                                <option value="Summarecon">Summarecon</option>
                                <option value="Baleendah">Baleendah</option>
                            </select>
                            <x-input-error :messages="$errors->get('lokasi_kerja')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-jenis_kerja" value="Jenis Kerja" />
                            <select id="create-jenis_kerja" wire:model="jenis_kerja" class="mt-1 block w-full rounded-xl border @error('jenis_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih jenis kerja</option>
                                @foreach(\App\Models\Employee::JENIS_KERJA_OPTIONS as $jenis => $ket)
                                    <option value="{{ $jenis }}">{{ $jenis }} — {{ $ket }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kerja')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-jam_kerja" value="Jam Kerja" />
                            <select id="create-jam_kerja" wire:model.live="jam_kerja" class="mt-1 block w-full rounded-xl border @error('jam_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih jam kerja</option>
                                @foreach(\App\Models\Employee::SHIFT_OPTIONS as $label => $mulai)
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jam_kerja')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-jam_masuk" value="Jam Masuk" />
                            <x-text-input id="create-jam_masuk" wire:model="jam_masuk" type="time" class="mt-1 block w-full {{ $errors->has('jam_masuk') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('jam_masuk')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-tanggal_resign" value="Tanggal Resign" />
                            <x-text-input id="create-tanggal_resign" wire:model="tanggal_resign" type="date" class="mt-1 block w-full {{ $errors->has('tanggal_resign') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tanggal_resign')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="create-jobdesk" value="Jobdesk" />
                            <textarea id="create-jobdesk" wire:model="jobdesk" rows="3" class="mt-1 block w-full rounded-xl border @error('jobdesk') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Deskripsi jobdesk..."></textarea>
                            <x-input-error :messages="$errors->get('jobdesk')" class="mt-2" />
                        </div>
                    </div>
                </div>
                @endif

                {{-- STEP 3: Kontak & Darurat --}}
                @if($step == 3)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
<x-input-label for="create-no_hp" value="No. Telepon" />
                           <x-text-input id="create-no_hp" wire:model="no_hp" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_hp') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="08123456789" />
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-email" value="Email" />
                            <x-text-input id="create-email" wire:model="email" type="email" class="mt-1 block w-full {{ $errors->has('email') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="john@company.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-informasi_lowongan" value="Informasi Lowongan" />
                            <select id="create-informasi_lowongan" wire:model="informasi_lowongan" class="mt-1 block w-full rounded-xl border @error('informasi_lowongan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="kita lulus">Kita Lulus</option>
                                <option value="loker bandung">Loker Bandung</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="kerabat">Kerabat</option>
                            </select>
                            <x-input-error :messages="$errors->get('informasi_lowongan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-device_user_id" value="Device User ID" />
                            <x-text-input id="create-device_user_id" wire:model="device_user_id" type="text" class="mt-1 block w-full {{ $errors->has('device_user_id') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="001" />
                            <x-input-error :messages="$errors->get('device_user_id')" class="mt-2" />
                        </div>
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kontak Darurat</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
<x-input-label for="create-no_kontak_darurat1" value="No. Kontak Darurat 1" />
                           <x-text-input id="create-no_kontak_darurat1" wire:model="no_kontak_darurat1" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_kontak_darurat1') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="08xxxxxxxxxx" />
                            <x-input-error :messages="$errors->get('no_kontak_darurat1')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-hubungan_darurat1" value="Hubungan" />
                            <select id="create-hubungan_darurat1" wire:model="hubungan_darurat1" class="mt-1 block w-full rounded-xl border @error('hubungan_darurat1') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="saudara">Saudara</option>
                                <option value="teman">Teman</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('hubungan_darurat1')" class="mt-2" />
                        </div>
                        <div>
<x-input-label for="create-no_kontak_darurat2" value="No. Kontak Darurat 2" />
                           <x-text-input id="create-no_kontak_darurat2" wire:model="no_kontak_darurat2" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_kontak_darurat2') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="08xxxxxxxxxx" />
                            <x-input-error :messages="$errors->get('no_kontak_darurat2')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-hubungan_darurat2" value="Hubungan" />
                            <select id="create-hubungan_darurat2" wire:model="hubungan_darurat2" class="mt-1 block w-full rounded-xl border @error('hubungan_darurat2') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="saudara">Saudara</option>
                                <option value="teman">Teman</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('hubungan_darurat2')" class="mt-2" />
                        </div>
                        <div>
<x-input-label for="create-no_bpjs" value="No. BPJS" />
                           <x-text-input id="create-no_bpjs" wire:model="no_bpjs" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_bpjs') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="0001234567890" />
                            <x-input-error :messages="$errors->get('no_bpjs')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="create-status_bpjs" value="Status BPJS" />
                            <select id="create-status_bpjs" wire:model="status_bpjs" class="mt-1 block w-full rounded-xl border @error('status_bpjs') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('status_bpjs')" class="mt-2" />
                        </div>
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700">
                    <div>
                        <x-input-label for="create-catatan" value="Catatan" />
                        <textarea id="create-catatan" wire:model="catatan" rows="2" class="mt-1 block w-full rounded-xl border @error('catatan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Catatan tambahan..."></textarea>
                        <x-input-error :messages="$errors->get('catatan')" class="mt-2" />
                    </div>
                </div>
                @endif

                {{-- FOOTER NAVIGATION --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <div>
                        @if($step > 1)
                            <button type="button" wire:click="prevStep" class="btn-secondary text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                Sebelumnya
                            </button>
                        @else
                            <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                        @endif
                    </div>
                    <div>
                        @if($step < 3)
                            <button type="button" wire:click="nextStep" class="btn-primary text-xs">
                                Selanjutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        @else
                            <button type="submit" class="btn-primary text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/></svg>
                                Preview
                            </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
    </template>

    {{-- ============ EDIT MODAL ============ --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showEditModal') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl my-8">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Karyawan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data karyawan</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- STEP INDICATOR --}}
            <div class="mb-5">
                <div class="flex items-center justify-center gap-0">
                    @foreach ([1 => 'Informasi Pribadi', 2 => 'Data Pekerjaan', 3 => 'Kontak & Darurat'] as $num => $label)
                        <div class="flex items-center">
                            <button type="button" wire:click="goToStep({{ $num }})" class="flex flex-col items-center gap-1.5 group">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold transition-all duration-200
                                    {{ $step == $num ? 'bg-primary-600 text-white ring-4 ring-primary-100 dark:ring-primary-900/50' : '' }}
                                    {{ $step > $num ? 'bg-emerald-500 text-white' : '' }}
                                    {{ $step < $num ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' : '' }}">
                                    @if($step > $num)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    @else
                                        {{ $num }}
                                    @endif
                                </div>
                                <span class="text-[10px] font-medium whitespace-nowrap
                                    {{ $step == $num ? 'text-primary-600 dark:text-primary-400' : '' }}
                                    {{ $step > $num ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                                    {{ $step < $num ? 'text-gray-400 dark:text-gray-500' : '' }}">{{ $label }}</span>
                            </button>
                            @if($num < 3)
                                <div class="w-16 sm:w-24 h-px mx-2 mb-5
                                    {{ $step > $num ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <form wire:submit.prevent="confirmPreview">

                {{-- STEP 1: Informasi Pribadi --}}
                @if($step == 1)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="edit-nik_ktp" value="NIK KTP *" />
                            <x-text-input id="edit-nik_ktp" wire:model="nik_ktp" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('nik_ktp') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('nik_ktp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-nama" value="Nama Lengkap *" />
                            <x-text-input id="edit-nama" wire:model="nama" type="text" class="mt-1 block w-full {{ $errors->has('nama') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-tempat_lahir" value="Tempat Lahir" />
                            <x-text-input id="edit-tempat_lahir" wire:model="tempat_lahir" type="text" class="mt-1 block w-full {{ $errors->has('tempat_lahir') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-tanggal_lahir" value="Tanggal Lahir" />
                            <x-text-input id="edit-tanggal_lahir" wire:model="tanggal_lahir" type="date" class="mt-1 block w-full {{ $errors->has('tanggal_lahir') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-jenis_kelamin" value="Jenis Kelamin" />
                            <select id="edit-jenis_kelamin" wire:model="jenis_kelamin" class="mt-1 block w-full rounded-xl border @error('jenis_kelamin') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-tipe" value="Tipe Karyawan *" />
                            <select id="edit-tipe" wire:model="tipe" class="mt-1 block w-full rounded-xl border @error('tipe') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="karyawan_aktif">Karyawan Aktif</option>
                                <option value="calon_karyawan">Calon Karyawan</option>
                                <option value="mantan_karyawan">Mantan Karyawan</option>
                            </select>
                            <x-input-error :messages="$errors->get('tipe')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-ukuran_baju" value="Ukuran Baju" />
                            <select id="edit-ukuran_baju" wire:model="ukuran_baju" class="mt-1 block w-full rounded-xl border @error('ukuran_baju') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                            <x-input-error :messages="$errors->get('ukuran_baju')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-agama" value="Agama" />
                            <select id="edit-agama" wire:model="agama" class="mt-1 block w-full rounded-xl border @error('agama') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="islam">Islam</option>
                                <option value="katolik">Katolik</option>
                                <option value="protestan">Protestan</option>
                                <option value="hindu">Hindu</option>
                                <option value="budha">Budha</option>
                            </select>
                            <x-input-error :messages="$errors->get('agama')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-pendidikan_terakhir" value="Pendidikan Terakhir" />
                            <select id="edit-pendidikan_terakhir" wire:model="pendidikan_terakhir" class="mt-1 block w-full rounded-xl border @error('pendidikan_terakhir') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="sekolah dasar (SD)">Sekolah Dasar (SD)</option>
                                <option value="sekolah menengah pertama (SMP)">Sekolah Menengah Pertama (SMP)</option>
                                <option value="sekolah menengah atas (SMA)">Sekolah Menengah Atas (SMA) Sederajat</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                            <x-input-error :messages="$errors->get('pendidikan_terakhir')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-asal_sekolah" value="Asal Sekolah" />
                            <textarea id="edit-asal_sekolah" wire:model="asal_sekolah" rows="1" class="mt-1 block w-full rounded-xl border @error('asal_sekolah') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Masukan Asal Sekolah"></textarea>
                            <x-input-error :messages="$errors->get('asal_sekolah')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-status_pernikahan" value="Status Pernikahan" />
                            <select id="edit-status_pernikahan" wire:model="status_pernikahan" class="mt-1 block w-full rounded-xl border @error('status_pernikahan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                @foreach(\App\Models\Employee::STATUS_PERKAWINAN_OPTIONS as $nilai => $label)
                                    <option value="{{ $nilai }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_pernikahan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-provinsi" value="Provinsi" />
                            <select id="edit-provinsi" wire:model.live="provinsi" class="mt-1 block w-full rounded-xl border @error('provinsi') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinceList as $pid => $pname)
                                    <option value="{{ $pid }}">{{ ucwords(strtolower($pname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('provinsi')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-kota" value="Kota/Kabupaten" />
                            <select id="edit-kota" wire:model.live="kota" class="mt-1 block w-full rounded-xl border @error('kota') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                @foreach($cityList as $cid => $cname)
                                    <option value="{{ $cid }}">{{ ucwords(strtolower($cname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kota')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-kecamatan" value="Kecamatan" />
                            <select id="edit-kecamatan" wire:model.live="kecamatan" class="mt-1 block w-full rounded-xl border @error('kecamatan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach($districtList as $did => $dname)
                                    <option value="{{ $did }}">{{ ucwords(strtolower($dname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kecamatan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-kelurahan" value="Kelurahan/Desa" />
                            <select id="edit-kelurahan" wire:model.live="kelurahan" class="mt-1 block w-full rounded-xl border @error('kelurahan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih Kelurahan/Desa --</option>
                                @foreach($villageList as $vid => $vname)
                                    <option value="{{ $vid }}">{{ ucwords(strtolower($vname)) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kelurahan')" class="mt-2" />
                        </div>
                        <div>
                            <div class="flex flex-col sm:flex-row gap-2 items-end">
                                <div class="flex-1">
                                    <x-input-label for="edit-rt" value="RT / RW" />
                                    <div class="mt-1 flex items-center gap-0">
                                        <input id="edit-rt" type="text" maxlength="3" inputmode="numeric" placeholder="001"
                                            class="rt-rw-input input-field !w-16 !rounded-r-none !text-center !px-2 {{ $errors->has('rt_rw') ? '!border-red-400 !focus:border-red-400 !focus:ring-red-100' : '' }}"
                                            data-target="edit-rw" data-group="edit" />
                                        <span class="input-field !w-auto !rounded-none !px-2 !border-l-0 !border-r-0 cursor-default select-none">/</span>
                                        <input id="edit-rw" type="text" maxlength="3" inputmode="numeric" placeholder="002"
                                            class="rt-rw-input input-field !w-16 !rounded-l-none !text-center !px-2 {{ $errors->has('rt_rw') ? '!border-red-400 !focus:border-red-400 !focus:ring-red-100' : '' }}"
                                            data-group="edit" />
                                    </div>
                                    <input type="hidden" wire:model="rt_rw" id="edit-rt_rw" />
                                    <x-input-error :messages="$errors->get('rt_rw')" class="mt-2" />
                                </div>
                                <div class="w-full sm:w-[300px]">
                                    <x-input-label for="edit-kode_pos" value="Kode Pos" />
                                    <x-text-input id="edit-kode_pos" wire:model="kode_pos" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('kode_pos') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" placeholder="40134" />
                                    <x-input-error :messages="$errors->get('kode_pos')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="edit-alamat" value="Alamat Lengkap" />
                            <textarea id="edit-alamat" wire:model="alamat" rows="2" class="mt-1 block w-full rounded-xl border @error('alamat') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"></textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>
                    </div>
                </div>
                @endif

                {{-- STEP 2: Data Pekerjaan --}}
                @if($step == 2)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <x-input-label for="edit-nik" value="NIP (Nomor Induk Pegawai) *" />
                            <x-text-input id="edit-nik" wire:model="nik" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('nik') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                        </div>
                        <div x-data="{ open: false }" class="relative">
                            <x-input-label value="Divisi" />
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full mt-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                                <span>{{ count($division_ids) > 0 ? count($division_ids) . ' divisi dipilih' : 'Pilih divisi' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                @foreach($divisions as $div)
                                    <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors {{ in_array($div->id, $division_ids) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <input type="checkbox" value="{{ $div->id }}" wire:model="division_ids"
                                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $div->nama }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @error('division_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div x-data="{ open: false }" class="relative">
                            <x-input-label value="Jabatan" />
                            <input type="hidden" wire:model="position">
                            <button type="button" @click="open = !open"
                                    class="flex items-center justify-between w-full mt-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all">
                                <span>{{ count($position_ids) > 0 ? count($position_ids) . ' jabatan dipilih' : 'Pilih jabatan' }}</span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute z-20 mt-1 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 shadow-lg max-h-48 overflow-y-auto p-1.5 space-y-0.5">
                                @foreach($allPositions as $pos)
                                    <label class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors {{ in_array($pos->id, $position_ids) ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                        <input type="checkbox" value="{{ $pos->id }}" wire:model.live="position_ids"
                                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $pos->nama }}</span>
                                        <input type="radio" value="{{ $pos->id }}" wire:model.live="main_position_id"
                                               class="text-primary-600 focus:ring-primary-500">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">Utama</span>
                                    </label>
                                @endforeach
                            </div>
                        @error('position_ids') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        @error('main_position_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-input-label for="edit-atasan" value="Atasan 1" />
                            <select id="edit-atasan" wire:model="atasan" class="mt-1 block w-full rounded-xl border @error('atasan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih atasan --</option>
                                @foreach(\App\Models\Employee::ATASAN_OPTIONS as $namaAtasan)
                                    <option value="{{ $namaAtasan }}">{{ $namaAtasan }}</option>
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('atasan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-atasan2" value="Atasan 2" />
                            <select id="edit-atasan2" wire:model="atasan2" class="mt-1 block w-full rounded-xl border @error('atasan2') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih atasan 2 --</option>
                                @foreach(\App\Models\Employee::ATASAN_OPTIONS as $namaAtasan)
                                    <option value="{{ $namaAtasan }}">{{ $namaAtasan }}</option>
                                @endforeach
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('atasan2')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-tanggal_masuk" value="Tanggal Bergabung" />
                            <x-text-input id="edit-tanggal_masuk" wire:model="tanggal_masuk" type="date" class="mt-1 block w-full {{ $errors->has('tanggal_masuk') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('tanggal_masuk')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-jenis_karyawan" value="Jenis Karyawan" />
                            <select id="edit-jenis_karyawan" wire:model="jenis_karyawan" class="mt-1 block w-full rounded-xl border @error('jenis_karyawan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak</option>
                                <option value="magang">Magang</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_karyawan')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-lokasi_kerja" value="Lokasi Kerja" />
                            <select id="edit-lokasi_kerja" wire:model="lokasi_kerja" class="mt-1 block w-full rounded-xl border @error('lokasi_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih lokasi kerja</option>
                                <option value="Summarecon">Summarecon</option>
                                <option value="Baleendah">Baleendah</option>
                            </select>
                            <x-input-error :messages="$errors->get('lokasi_kerja')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-jenis_kerja" value="Jenis Kerja" />
                            <select id="edit-jenis_kerja" wire:model="jenis_kerja" class="mt-1 block w-full rounded-xl border @error('jenis_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih jenis kerja</option>
                                @foreach(\App\Models\Employee::JENIS_KERJA_OPTIONS as $jenis => $ket)
                                    <option value="{{ $jenis }}">{{ $jenis }} — {{ $ket }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kerja')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-jam_kerja" value="Jam Kerja" />
                            <select id="edit-jam_kerja" wire:model.live="jam_kerja" class="mt-1 block w-full rounded-xl border @error('jam_kerja') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">Pilih jam kerja</option>
                                @foreach(\App\Models\Employee::SHIFT_OPTIONS as $label => $mulai)
                                    <option value="{{ $label }}">{{ $label }}</option>
                                @endforeach
                                @if($jam_kerja !== '' && ! array_key_exists($jam_kerja, \App\Models\Employee::SHIFT_OPTIONS))
                                    <option value="{{ $jam_kerja }}">{{ $jam_kerja }} (nilai lama)</option>
                                @endif
                            </select>
                            <x-input-error :messages="$errors->get('jam_kerja')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="edit-jobdesk" value="Jobdesk" />
                            <textarea id="edit-jobdesk" wire:model="jobdesk" rows="3" class="mt-1 block w-full rounded-xl border @error('jobdesk') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"></textarea>
                            <x-input-error :messages="$errors->get('jobdesk')" class="mt-2" />
                        </div>
                    </div>
                </div>
                @endif

                {{-- STEP 3: Kontak & Darurat --}}
                @if($step == 3)
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
<x-input-label for="edit-no_hp" value="No. Telepon" />
                           <x-text-input id="edit-no_hp" wire:model="no_hp" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_hp') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-email" value="Email" />
                            <x-text-input id="edit-email" wire:model="email" type="email" class="mt-1 block w-full {{ $errors->has('email') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-informasi_lowongan" value="Informasi Lowongan" />
                            <select id="edit-informasi_lowongan" wire:model="informasi_lowongan" class="mt-1 block w-full rounded-xl border @error('informasi_lowongan') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="kita lulus">Kita Lulus</option>
                                <option value="loker bandung">Loker Bandung</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="kerabat">Kerabat</option>
                            </select>
                            <x-input-error :messages="$errors->get('informasi_lowongan')" class="mt-2" />
                        </div>
                    </div>
                    <hr class="border-gray-100 dark:border-gray-700">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kontak Darurat</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
<x-input-label for="edit-no_kontak_darurat1" value="No. Kontak Darurat 1" />
                           <x-text-input id="edit-no_kontak_darurat1" wire:model="no_kontak_darurat1" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_kontak_darurat1') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('no_kontak_darurat1')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-hubungan_darurat1" value="Hubungan" />
                            <select id="edit-hubungan_darurat1" wire:model="hubungan_darurat1" class="mt-1 block w-full rounded-xl border @error('hubungan_darurat1') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="saudara">Saudara</option>
                                <option value="teman">Teman</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('hubungan_darurat1')" class="mt-2" />
                        </div>
                        <div>
<x-input-label for="edit-no_kontak_darurat2" value="No. Kontak Darurat 2" />
                           <x-text-input id="edit-no_kontak_darurat2" wire:model="no_kontak_darurat2" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_kontak_darurat2') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('no_kontak_darurat2')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-hubungan_darurat2" value="Hubungan" />
                            <select id="edit-hubungan_darurat2" wire:model="hubungan_darurat2" class="mt-1 block w-full rounded-xl border @error('hubungan_darurat2') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="suami">Suami</option>
                                <option value="istri">Istri</option>
                                <option value="ayah">Ayah</option>
                                <option value="ibu">Ibu</option>
                                <option value="saudara">Saudara</option>
                                <option value="teman">Teman</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <x-input-error :messages="$errors->get('hubungan_darurat2')" class="mt-2" />
                        </div>
                        <div>
<x-input-label for="edit-no_bpjs" value="No. BPJS" />
                           <x-text-input id="edit-no_bpjs" wire:model="no_bpjs" type="number" inputmode="numeric" pattern="[0-9]*" class="mt-1 block w-full {{ $errors->has('no_bpjs') ? 'border-red-400 focus:border-red-400 focus:ring-red-100' : '' }}" />
                            <x-input-error :messages="$errors->get('no_bpjs')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="edit-status_bpjs" value="Status BPJS" />
                            <select id="edit-status_bpjs" wire:model="status_bpjs" class="mt-1 block w-full rounded-xl border @error('status_bpjs') border-red-400 focus:border-red-400 focus:ring-red-100 @else border-gray-200 dark:border-gray-600 @enderror bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                                <option value="">-- Pilih --</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                                <option value="Other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('status_bpjs')" class="mt-2" />
                        </div>
                    </div>
                </div>
                @endif

                {{-- FOOTER NAVIGATION --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 mt-4">
                    <div>
                        @if($step > 1)
                            <button type="button" wire:click="prevStep" class="btn-secondary text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                                Sebelumnya
                            </button>
                        @else
                            <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                        @endif
                    </div>
                    <div>
                        @if($step < 3)
                            <button type="button" wire:click="nextStep" class="btn-primary text-xs">
                                Selanjutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        @else
                            <button type="submit" class="btn-primary text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/></svg>
                                Preview
                            </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>
    </template>

    {{-- ============ PREVIEW MODAL ============ --}}
    <template x-teleport="body">
    <div x-data="{ open: $wire.entangle('showPreview') }"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative flex flex-col w-full max-w-5xl max-h-[90vh] rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-2xl overflow-hidden">

            <div class="flex items-center justify-between mb-4 shrink-0">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Pratinjau Data Karyawan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Periksa kembali data sebelum menyimpan</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-6 overflow-y-auto flex-1 min-h-0 pr-1">
                {{-- Informasi Pribadi --}}
                <div>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        Informasi Pribadi
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="preview-field"><span class="preview-label">NIK KTP</span><span class="preview-value">{{ $nik_ktp ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Nama Lengkap</span><span class="preview-value">{{ $nama ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Tempat Lahir</span><span class="preview-value">{{ $tempat_lahir ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Tanggal Lahir</span><span class="preview-value">{{ $tanggal_lahir ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jenis Kelamin</span><span class="preview-value">{{ $jenis_kelamin == 'L' ? 'Laki-laki' : ($jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span></div>
                        <div class="preview-field"><span class="preview-label">Tipe Karyawan</span><span class="preview-value">{{ \App\Models\Employee::TIPE_OPTIONS[$tipe] ?? ucfirst($tipe) }}</span></div>
                        <div class="preview-field"><span class="preview-label">Status Pernikahan</span><span class="preview-value">{{ $status_pernikahan ? ucfirst($status_pernikahan) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Ukuran Baju</span><span class="preview-value">{{ $ukuran_baju ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Agama</span><span class="preview-value">{{ $agama ? ucfirst($agama) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Pendidikan Terakhir</span><span class="preview-value">{{ $pendidikan_terakhir ? ucfirst($pendidikan_terakhir) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Asal Sekolah</span><span class="preview-value whitespace-pre-line">{{ $asal_sekolah ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Provinsi</span><span class="preview-value">{{ $provinsi ? ucwords(strtolower($provinceList[$provinsi] ?? '')) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kota/Kabupaten</span><span class="preview-value">{{ $kota ? ucwords(strtolower($cityList[$kota] ?? '')) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kecamatan</span><span class="preview-value">{{ $kecamatan ? ucwords(strtolower($districtList[$kecamatan] ?? '')) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kelurahan/Desa</span><span class="preview-value">{{ $kelurahan ? ucwords(strtolower($villageList[$kelurahan] ?? '')) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">RT/RW</span><span class="preview-value">{{ $rt_rw ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kode Pos</span><span class="preview-value">{{ $kode_pos ?: '-' }}</span></div>
                        <div class="preview-field sm:col-span-2 lg:col-span-3"><span class="preview-label">Alamat Lengkap</span><span class="preview-value">{{ $alamat ?: '-' }}</span></div>
                    </div>
                </div>

                {{-- Data Pekerjaan --}}
                <div>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                        Data Pekerjaan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="preview-field"><span class="preview-label">NIP</span><span class="preview-value">{{ $nik ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Divisi</span><span class="preview-value">{{ $divisions->whereIn('id', $division_ids)->pluck('nama')->implode(' & ') ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jabatan</span><span class="preview-value">
                            @php
                                $previewPositions = \App\Models\Position::whereIn('id', $position_ids)->get();
                            @endphp
                            @if($previewPositions->count() > 0)
                                @foreach($previewPositions as $pp)
                                    {{ $pp->nama }}{{ $pp->id == (int) $main_position_id ? ' (Utama)' : '' }}@if(!$loop->last), @endif
                                @endforeach
                            @else
                                {{ $position ?: '-' }}
                            @endif
                        </span></div>
                        <div class="preview-field"><span class="preview-label">Atasan 1</span><span class="preview-value">{{ $atasan ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Atasan 2</span><span class="preview-value">{{ $atasan2 ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Tanggal Bergabung</span><span class="preview-value">{{ $tanggal_masuk ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jenis Karyawan</span><span class="preview-value">{{ $jenis_karyawan ? ucfirst($jenis_karyawan) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Lokasi Kerja</span><span class="preview-value">{{ $lokasi_kerja ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jenis Kerja</span><span class="preview-value">{{ $jenis_kerja ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jam Kerja</span><span class="preview-value">{{ $jam_kerja ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Jam Masuk</span><span class="preview-value">{{ $jam_masuk ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Tanggal Resign</span><span class="preview-value">{{ $tanggal_resign ?: '-' }}</span></div>
                        <div class="preview-field sm:col-span-2 lg:col-span-3"><span class="preview-label">Jobdesk</span><span class="preview-value whitespace-pre-line">{{ $jobdesk ?: '-' }}</span></div>
                    </div>
                </div>

                {{-- Kontak & Darurat --}}
                <div>
                    <h4 class="text-sm font-semibold text-primary-600 dark:text-primary-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        Kontak & Darurat
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="preview-field"><span class="preview-label">No. Telepon</span><span class="preview-value">{{ $no_hp ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Email</span><span class="preview-value">{{ $email ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">No. BPJS</span><span class="preview-value">{{ $no_bpjs ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Status BPJS</span><span class="preview-value">
                            @if($status_bpjs === 'aktif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                            @elseif($status_bpjs === 'tidak aktif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700">Tidak Aktif</span>
                            @elseif($status_bpjs === 'Other')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Other</span>
                            @else
                                -
                            @endif
                        </span></div>
                        <div class="preview-field"><span class="preview-label">Informasi Lowongan</span><span class="preview-value">{{ $informasi_lowongan ? ucfirst($informasi_lowongan) : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Device User ID</span><span class="preview-value">{{ $device_user_id ?: '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kontak Darurat 1</span><span class="preview-value">{{ $no_kontak_darurat1 ? $no_kontak_darurat1 . ' (' . $hubungan_darurat1 . ')' : '-' }}</span></div>
                        <div class="preview-field"><span class="preview-label">Kontak Darurat 2</span><span class="preview-value">{{ $no_kontak_darurat2 ? $no_kontak_darurat2 . ' (' . $hubungan_darurat2 . ')' : '-' }}</span></div>
                        <div class="preview-field sm:col-span-2 lg:col-span-3"><span class="preview-label">Catatan</span><span class="preview-value whitespace-pre-line">{{ $catatan ?: '-' }}</span></div>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="mt-4 p-3 rounded-xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <div class="text-xs text-red-700 dark:text-red-400">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700 mt-4 shrink-0">
                <button type="button" @click="$wire.backToForm()" class="btn-secondary text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Kembali Edit
                </button>
                @if($editId)
                    <button type="button" @click="$wire.update()" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan Perubahan
                    </button>
                @else
                    <button type="button" @click="$wire.save()" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan
                    </button>
                @endif
            </div>
        </div>
    </div>
    </template>

    <x:confirm-delete-modal title="Hapus Karyawan" message="Apakah Anda yakin ingin menghapus data karyawan ini? Semua data terkait juga akan dihapus." />

    <script>
        document.addEventListener('livewire:init', () => {
            const syncRtRw = (group) => {
                const rt = document.getElementById(`${group}-rt`);
                const rw = document.getElementById(`${group}-rw`);
                const hidden = document.getElementById(`${group}-rt_rw`);
                if (rt && rw && hidden) {
                    const rtVal = rt.value === '-' ? '-' : rt.value.replace(/\D/g, '');
                    const rwVal = rw.value === '-' ? '-' : rw.value.replace(/\D/g, '');
                    hidden.value = (rtVal || rwVal) ? `${rtVal}/${rwVal}` : '';
                    hidden.dispatchEvent(new Event('input', { bubbles: true }));
                }
            };

            const splitRtRw = (group, value) => {
                const rt = document.getElementById(`${group}-rt`);
                const rw = document.getElementById(`${group}-rw`);
                if (rt && rw && value) {
                    const parts = value.split('/');
                    rt.value = parts[0] || '';
                    rw.value = parts[1] || '';
                }
            };

            const initRtRwInputs = () => {
                document.querySelectorAll('.rt-rw-input').forEach(input => {
                    if (input.dataset.rtrwInit) return;
                    input.dataset.rtrwInit = 'true';

                    input.addEventListener('input', function() {
                        const isDash = this.value === '-';
                        if (isDash) {
                            this.value = '-';
                        } else {
                            this.value = this.value.replace(/[^0-9-]/g, '').replace(/-/g, '').substring(0, 3);
                        }
                        const group = this.dataset.group;
                        if ((this.value.length === 3 || this.value === '-') && this.dataset.target) {
                            const target = document.getElementById(this.dataset.target);
                            if (target) target.focus();
                        }
                        syncRtRw(group);
                    });

                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Backspace' && this.value === '' && this.id.endsWith('-rw')) {
                            const group = this.dataset.group;
                            const rt = document.getElementById(`${group}-rt`);
                            if (rt) {
                                rt.focus();
                                rt.value = rt.value.substring(0, rt.value.length - 1);
                                syncRtRw(group);
                            }
                        }
                        if (e.key === '/' || e.key === 'ArrowRight') {
                            e.preventDefault();
                            const group = this.dataset.group;
                            const rw = document.getElementById(`${group}-rw`);
                            if (rw && this.id.endsWith('-rt')) rw.focus();
                        }
                        if (e.key === 'ArrowLeft' && this.id.endsWith('-rw')) {
                            const group = this.dataset.group;
                            const rt = document.getElementById(`${group}-rt`);
                            if (rt && this.value === '') rt.focus();
                        }
                    });

                    input.addEventListener('focus', function() {
                        this.select();
                    });
                });
            };

            const watchEditModal = () => {
                const editModal = document.querySelector('[x-data*="showEditModal"]');
                if (editModal && !editModal.dataset.rtrwWatch) {
                    editModal.dataset.rtrwWatch = 'true';
                    const observer = new MutationObserver(() => {
                        setTimeout(() => {
                            const hidden = document.getElementById('edit-rt_rw');
                            const rt = document.getElementById('edit-rt');
                            const rw = document.getElementById('edit-rw');
                            if (hidden && rt && rw) {
                                const val = hidden.value || '';
                                if (val && val.includes('/')) {
                                    const parts = val.split('/');
                                    rt.value = parts[0] || '';
                                    rw.value = parts[1] || '';
                                } else {
                                    rt.value = '';
                                    rw.value = '';
                                }
                            }
                            initRtRwInputs();
                        }, 100);
                    });
                    observer.observe(editModal, { attributes: true, subtree: true, childList: true });
                }
            };

            Livewire.on('showEditModal', () => {
                setTimeout(() => {
                    const hidden = document.getElementById('edit-rt_rw');
                    const rt = document.getElementById('edit-rt');
                    const rw = document.getElementById('edit-rw');
                    if (hidden && rt && rw) {
                        const val = hidden.value || '';
                        if (val && val.includes('/')) {
                            const parts = val.split('/');
                            rt.value = parts[0] || '';
                            rw.value = parts[1] || '';
                        }
                    }
                }, 150);
            });

            initRtRwInputs();
            watchEditModal();

            const checkForModals = setInterval(() => {
                initRtRwInputs();
                watchEditModal();
            }, 500);
            setTimeout(() => clearInterval(checkForModals), 10000);
        });
    </script>
</div>


