@if(!$isOwnView && !$isOwnReadOnly && (auth()->user()->can('update-data') || auth()->user()->employee_id === $employee->id))
    <button type="button" @click="Livewire.dispatch('open-employee-edit', { employeeId: {{ $employee->id }} })" class="inline-flex items-center gap-2 rounded-xl bg-white text-blue-700 hover:bg-blue-50 dark:bg-white/10 dark:text-white dark:hover:bg-white/20 dark:ring-1 dark:ring-white/40 px-4 py-2 text-sm font-semibold transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Informasi
    </button>
@endif
@if(!$isOwnView && !$isOwnReadOnly)
    <div class="relative" @click.outside="aksiOpen = false">
        <button @click="aksiOpen = !aksiOpen" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm font-semibold text-white border border-white/60 hover:bg-white/20 transition-all">
            Aksi Lainnya
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
        </button>
        <div x-show="aksiOpen" x-cloak @click="aksiOpen = false" class="absolute top-full right-0 mt-2 min-w-[190px] bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 py-1.5 z-50">
            @if($canManageEmployeeData)
            <button type="button" @click="aksiOpen = false; openPromosiModal()" class="w-full text-left px-3 py-2.5 text-sm font-medium text-violet-700 hover:bg-violet-50 flex items-center gap-2.5 rounded-lg">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                Promosi / Mutasi
            </button>
            @endif
            <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2.5 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 15V3"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>
                Ekspor Data
            </button>
            <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2.5 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20"/></svg>
                Cetak Kartu Pegawai
            </button>
            @can('delete-data')
            <button type="button" class="w-full text-left px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 flex items-center gap-2.5 rounded-lg">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                {{ auth()->user()->pegawaiLabel('Hapus Karyawan') }}
            </button>
            @endcan
        </div>
    </div>
@endif