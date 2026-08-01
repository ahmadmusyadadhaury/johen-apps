@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Project IT</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola project IT perusahaan</p>
    </div>
@endpush

<x-app-layout title="Project IT">
    <div x-data="{ showModal: false, editMode: false, deleteMode: false, selected: null, formNama: '', formDeadline: '', formStatus: 'aktif' }" class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-200 dark:shadow-blue-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                    </div>
                    <span class="badge-info">Total</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->count() }} <span class="text-sm font-medium text-gray-400">project</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total Semua Project</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <span class="badge-warning">Aktif</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->where('status', 'aktif')->count() }} <span class="text-sm font-medium text-gray-400">project</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Sedang Berjalan</p>
            </div>

            <div class="stat-card group">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="badge-success">Selesai</span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $projects->where('status', 'selesai')->count() }} <span class="text-sm font-medium text-gray-400">project</span></p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Project Selesai</p>
            </div>
        </div>

        {{-- Table --}}
        <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-100 p-5 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Daftar Project</h2>
                    <p class="mt-0.5 text-xs text-gray-400">Semua project IT yang sedang berjalan</p>
                </div>
                @if(auth()->user()->isKoordinatorIt())
                <button @click="showModal = true; editMode = false; formNama = ''; formDeadline = ''; formStatus = 'aktif'" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Project
                </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Nama Project</th>
                            <th class="px-5 py-3">Deadline</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Dibuat Oleh</th>
                            @if(auth()->user()->isKoordinatorIt())
                            <th class="px-5 py-3">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                        @forelse($projects as $index => $project)
                            @php
                                $isOverdue = $project->status === 'aktif' && $project->deadline->isPast();
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition">
                                <td class="px-5 py-3.5 text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-gray-100">{{ $project->nama }}</td>
                                <td class="px-5 py-3.5 {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $project->deadline->format('d M Y') }}
                                    @if($isOverdue)
                                        <span class="ml-1.5 text-[10px] font-bold uppercase tracking-wider">Terlambat</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($project->status === 'aktif')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Selesai</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">{{ $project->creator->employee?->nama ?? $project->creator->name }}</td>
                                @if(auth()->user()->isKoordinatorIt())
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <button @click="editMode = true; deleteMode = false; selected = {{ $project->toJson() }}; formNama = '{{ addslashes($project->nama) }}'; formDeadline = '{{ $project->deadline->format('Y-m-d') }}'; formStatus = '{{ $project->status }}'; showModal = true" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </button>
                                        <button @click="deleteMode = true; editMode = false; selected = {{ $project->toJson() }}; showModal = true" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isKoordinatorIt() ? 6 : 5 }}" class="px-5 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                                    Belum ada project.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Modal Tambah/Edit --}}
        <template x-if="showModal && !deleteMode">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" x-text="editMode ? 'Edit Project' : 'Tambah Project'"></h3>
                    <form :action="editMode ? '{{ url('it/project') }}/' + selected.id : '{{ route('it.project.store') }}'" method="POST" class="mt-5 space-y-4">
                        @csrf
                        <template x-if="editMode"><input type="hidden" name="_method" value="PATCH"></template>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Project</label>
                            <input type="text" name="nama" x-model="formNama" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deadline</label>
                            <input type="date" name="deadline" x-model="formDeadline" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                        </div>
                        <template x-if="editMode">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                <select name="status" x-model="formStatus" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                    <option value="aktif">Aktif</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </template>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                            <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition" x-text="editMode ? 'Simpan' : 'Tambah'"></button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- Modal Hapus --}}
        <template x-if="showModal && deleteMode">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
                <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
                <div class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Hapus Project?</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Project <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="selected?.nama"></span> akan dihapus secara permanen.
                    </p>
                    <div class="flex justify-end gap-3 mt-5">
                        <button @click="showModal = false" class="rounded-xl px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">Batal</button>
                        <form :action="'{{ url('it/project') }}/' + selected?.id" method="POST">
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
