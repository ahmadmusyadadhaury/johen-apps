@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kelola Akun</h1>
        <p class="text-xs text-gray-400 mt-0.5">Atur akun pengguna aplikasi</p>
    </div>
@endpush

<div>
    <div class="card">
        {{-- Filter bar --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-4 border-b border-gray-50 dark:border-gray-800">
            <div class="flex items-center gap-3 flex-1 flex-wrap">
                <div class="relative flex-1 max-w-xs">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama atau username..."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200"
                    >
                </div>

                <select wire:model.live="filterRole" class="rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                    <option value="">Semua Role</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="gm_ceo">GM / CEO</option>
                    <option value="staff_hr">Staff HR</option>
                    <option value="manager">Manager</option>
                    <option value="koordinator">Koordinator</option>
                    <option value="staff">Staff</option>
                    <option value="koordinator_it">Koordinator IT</option>
                    <option value="staff_it">Staff IT</option>
                    <option value="koordinator_creative">Koordinator Creative</option>
                    <option value="koordinator_admin">Koordinator Admin</option>
                    <option value="koordinator_stock">Koordinator Admin Stock</option>
                    <option value="koordinator_pubg">Koordinator Johen PUBG</option>
                    <option value="koordinator_ff">Koordinator FF</option>
                    <option value="koordinator_mlbb">Koordinator MLBB</option>
                    <option value="koordinator_efootball">Koordinator E-football</option>
                    <option value="koordinator_valorant">Koordinator Valorant</option>
                    <option value="koordinator_roblox">Koordinator Roblox</option>
                    <option value="koordinator_monkey_pubg">Koordinator Monkey PUBG</option>
                    <option value="staff_creative">Staff Creative</option>
                    <option value="staff_host_pubg">Staff Host PUBG</option>
                    <option value="staff_host_ff">Staff Host FF</option>
                    <option value="staff_host_mlbb">Staff Host MLBB</option>
                    <option value="staff_host_efootball">Staff Host E-football</option>
                    <option value="staff_host_valorant">Staff Host Valorant</option>
                    <option value="staff_host_roblox">Staff Host Roblox</option>
                    <option value="staff_host_monkey_pubg">Staff Host Monkey PUBG</option>
                    <option value="staff_admin">Staff Admin</option>
                    <option value="staff_stock">Staff Admin Stock</option>
                </select>
            </div>

            @can('create-data')
            <button wire:click="openCreateModal" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Akun
            </button>
            @endcan
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="table-header">
                        <th class="px-6 py-3 w-12 text-center">No</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Username</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Karyawan</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-900 transition-colors">
                            <td class="table-cell text-center text-gray-500 dark:text-gray-400">{{ $users->firstItem() + $loop->index }}</td>
                            <td class="table-cell font-medium text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                            <td class="table-cell text-gray-600 dark:text-gray-400 font-mono">{{ $u->username }}</td>
                            <td class="table-cell">
                                @if($u->role === 'super_admin')
                                    <span class="badge-success">Super Admin</span>
                                @elseif($u->role === 'gm_ceo')
                                    <span class="badge-warning">GM / CEO</span>
                                @elseif($u->role === 'manager')
                                    <span class="badge-primary">Manager</span>
                                @elseif($u->role === 'koordinator')
                                    <span class="badge-info">Koordinator</span>
                                @elseif($u->role === 'koordinator_it')
                                    <span class="badge-secondary">Koordinator IT</span>
                                @elseif($u->role === 'staff_it')
                                    <span class="badge-secondary">Staff IT</span>
                                @elseif($u->role === 'koordinator_creative')
                                    <span class="badge-secondary">Koordinator Creative</span>
                                @elseif($u->role === 'koordinator_admin')
                                    <span class="badge-secondary">Koordinator Admin</span>
                                @elseif($u->role === 'koordinator_stock')
                                    <span class="badge-secondary">Koordinator Admin Stock</span>
                                @elseif($u->role === 'koordinator_pubg')
                                    <span class="badge-secondary">Koordinator Johen PUBG</span>
                                @elseif($u->role === 'koordinator_ff')
                                    <span class="badge-secondary">Koordinator FF</span>
                                @elseif($u->role === 'koordinator_mlbb')
                                    <span class="badge-secondary">Koordinator MLBB</span>
                                @elseif($u->role === 'koordinator_efootball')
                                    <span class="badge-secondary">Koordinator E-football</span>
                                @elseif($u->role === 'koordinator_valorant')
                                    <span class="badge-secondary">Koordinator Valorant</span>
                                @elseif($u->role === 'staff_creative')
                                    <span class="badge-secondary">Staff Creative</span>
                                @elseif($u->role === 'staff_host_pubg')
                                    <span class="badge-secondary">Staff Host PUBG</span>
                                @elseif($u->role === 'staff_host_ff')
                                    <span class="badge-secondary">Staff Host FF</span>
                                @elseif($u->role === 'staff_host_mlbb')
                                    <span class="badge-secondary">Staff Host MLBB</span>
                                @elseif($u->role === 'staff_host_efootball')
                                    <span class="badge-secondary">Staff Host E-football</span>
                                @elseif($u->role === 'staff_host_valorant')
                                    <span class="badge-secondary">Staff Host Valorant</span>
                                @elseif($u->role === 'koordinator_roblox')
                                    <span class="badge-secondary">Koordinator Roblox</span>
                                @elseif($u->role === 'staff_host_roblox')
                                    <span class="badge-secondary">Staff Host Roblox</span>
                                @elseif($u->role === 'koordinator_monkey_pubg')
                                    <span class="badge-secondary">Koordinator Monkey PUBG</span>
                                @elseif($u->role === 'staff_host_monkey_pubg')
                                    <span class="badge-secondary">Staff Host Monkey PUBG</span>
                                @elseif($u->role === 'staff_admin')
                                    <span class="badge-secondary">Staff Admin</span>
                                @elseif($u->role === 'staff_stock')
                                    <span class="badge-secondary">Staff Admin Stock</span>
                                @elseif($u->role === 'staff_hr')
                                    <span class="badge-info">Staff HR</span>
                                @else
                                    <span class="badge-secondary">Staff</span>
                                @endif
                            </td>
                            <td class="table-cell text-gray-600 dark:text-gray-400">
                                {{ $u->employee?->nama ?? '-' }}
                            </td>
                            <td class="table-cell text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    @can('update-data')
                                    <button wire:click="openEditModal({{ $u->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    @endcan
                                    @can('delete-data')
                                    <button wire:click="confirmDelete({{ $u->id }})" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-50 dark:bg-gray-900 mb-3">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
</div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Belum ada akun</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambah akun pengguna untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

<x:confirm-delete-modal title="Hapus Akun" message="Apakah Anda yakin ingin menghapus akun ini?" />
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-50 dark:border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <template x-teleport="body">
    {{-- CREATE MODAL --}}
    <div x-data="{ open: $wire.entangle('showCreateModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Akun</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Buat akun baru untuk karyawan</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <x-input-label for="create-name" value="Nama Lengkap *" />
                    <x-text-input id="create-name" wire:model="name" type="text" class="mt-1 block w-full" placeholder="Nama karyawan" />
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-username" value="Username *" />
                    <x-text-input id="create-username" wire:model="username" type="text" class="mt-1 block w-full" placeholder="username" />
                    @error('username') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-password" value="Password *" />
                    <x-text-input id="create-password" wire:model="password" type="password" class="mt-1 block w-full" placeholder="Min. 4 karakter" />
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-password_confirmation" value="Konfirmasi Password *" />
                    <x-text-input id="create-password_confirmation" wire:model="password_confirmation" type="password" class="mt-1 block w-full" placeholder="Ulangi password" />
                </div>

                <div>
                    <x-input-label for="create-role" value="Role *" />
                    <select id="create-role" wire:model="role" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="staff">Staff</option>
                        <option value="koordinator_it">Koordinator IT</option>
                    <option value="staff_it">Staff IT</option>
                        <option value="koordinator_creative">Koordinator Creative</option>
                        <option value="koordinator_admin">Koordinator Admin</option>
                    <option value="koordinator_stock">Koordinator Admin Stock</option>
                        <option value="koordinator_pubg">Koordinator Johen PUBG</option>
                        <option value="koordinator_ff">Koordinator FF</option>
                        <option value="koordinator_mlbb">Koordinator MLBB</option>
                        <option value="koordinator_efootball">Koordinator E-football</option>
                        <option value="koordinator_valorant">Koordinator Valorant</option>
                        <option value="koordinator_roblox">Koordinator Roblox</option>
                        <option value="koordinator_monkey_pubg">Koordinator Monkey PUBG</option>
                        <option value="staff_creative">Staff Creative</option>
                        <option value="staff_host_pubg">Staff Host PUBG</option>
                        <option value="staff_host_ff">Staff Host FF</option>
                        <option value="staff_host_mlbb">Staff Host MLBB</option>
                        <option value="staff_host_efootball">Staff Host E-football</option>
                        <option value="staff_host_valorant">Staff Host Valorant</option>
                        <option value="staff_host_roblox">Staff Host Roblox</option>
                        <option value="staff_host_monkey_pubg">Staff Host Monkey PUBG</option>
                        <option value="staff_admin">Staff Admin</option>
                    <option value="staff_stock">Staff Admin Stock</option>
                        <option value="koordinator">Koordinator</option>
                        <option value="manager">Manager</option>
                        <option value="gm_ceo">GM / CEO</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="staff_hr">Staff HR</option>
                    </select>
                    @error('role') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="create-employee" value="Hubungkan ke Karyawan" />
                    <select id="create-employee" wire:model="linkEmployeeId" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Tidak terhubung --</option>
                        @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    <template x-teleport="body">
    {{-- EDIT MODAL --}}
    <div x-data="{ open: $wire.entangle('showEditModal') }"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Edit Akun</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui data akun</p>
                </div>
                <button wire:click="closeModal" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <x-input-label for="edit-name" value="Nama Lengkap *" />
                    <x-text-input id="edit-name" wire:model="name" type="text" class="mt-1 block w-full" />
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-username" value="Username *" />
                    <x-text-input id="edit-username" wire:model="username" type="text" class="mt-1 block w-full" />
                    @error('username') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-password" value="Password (kosongkan jika tidak diubah)" />
                    <x-text-input id="edit-password" wire:model="password" type="password" class="mt-1 block w-full" placeholder="Min. 4 karakter" />
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label for="edit-password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="edit-password_confirmation" wire:model="password_confirmation" type="password" class="mt-1 block w-full" placeholder="Ulangi password" />
                </div>

                <div>
                    <x-input-label for="edit-role" value="Role *" />
                    <select id="edit-role" wire:model="role" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="staff">Staff</option>
                        <option value="koordinator_it">Koordinator IT</option>
                    <option value="staff_it">Staff IT</option>
                        <option value="koordinator_creative">Koordinator Creative</option>
                        <option value="koordinator_admin">Koordinator Admin</option>
                    <option value="koordinator_stock">Koordinator Admin Stock</option>
                        <option value="koordinator_pubg">Koordinator Johen PUBG</option>
                        <option value="koordinator_ff">Koordinator FF</option>
                        <option value="koordinator_mlbb">Koordinator MLBB</option>
                        <option value="koordinator_efootball">Koordinator E-football</option>
                        <option value="koordinator_valorant">Koordinator Valorant</option>
                        <option value="koordinator_roblox">Koordinator Roblox</option>
                        <option value="koordinator_monkey_pubg">Koordinator Monkey PUBG</option>
                        <option value="staff_creative">Staff Creative</option>
                        <option value="staff_host_pubg">Staff Host PUBG</option>
                        <option value="staff_host_ff">Staff Host FF</option>
                        <option value="staff_host_mlbb">Staff Host MLBB</option>
                        <option value="staff_host_efootball">Staff Host E-football</option>
                        <option value="staff_host_valorant">Staff Host Valorant</option>
                        <option value="staff_host_roblox">Staff Host Roblox</option>
                        <option value="staff_host_monkey_pubg">Staff Host Monkey PUBG</option>
                        <option value="staff_admin">Staff Admin</option>
                    <option value="staff_stock">Staff Admin Stock</option>
                        <option value="koordinator">Koordinator</option>
                        <option value="manager">Manager</option>
                        <option value="gm_ceo">GM / CEO</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="staff_hr">Staff HR</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="edit-employee" value="Hubungkan ke Karyawan" />
                    <select id="edit-employee" wire:model="linkEmployeeId" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                        <option value="">-- Tidak terhubung --</option>
                        @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->nik }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="closeModal" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

</div>