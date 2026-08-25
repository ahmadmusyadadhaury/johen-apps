<aside class="hidden lg:block sticky top-44 space-y-4">
    {{-- Big Score Card --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="p-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Skor Evaluasi</p>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-gray-50 tabular-nums">{{ number_format($finalScore ?? 0, 2) }}</span>
                <span class="text-sm font-bold text-gray-300 dark:text-gray-600">/ 4.00</span>
            </div>
            @include('livewire.partials.eval-summary-body')
        </div>
    </div>

    {{-- Employee Quick Card --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Data Karyawan</p>
        <div class="flex items-center gap-3">
            @if($contract->employee->fotoUrl)
                <img src="{{ $contract->employee->fotoUrl }}" class="h-10 w-10 rounded-xl object-cover ring-1 ring-gray-100 dark:ring-gray-800" alt="">
            @else
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-sm font-extrabold text-emerald-600">{{ strtoupper(substr($contract->employee->nama, 0, 1)) }}</span>
            @endif
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ $contract->employee->nama }}</p>
                <p class="text-[11px] text-gray-400 truncate">{{ $contract->posisi ?: '-' }}</p>
            </div>
        </div>
        <div class="mt-4 space-y-2.5 text-xs">
            <div class="flex items-center justify-between"><span class="text-gray-400">Divisi</span><span class="font-semibold text-gray-700 dark:text-gray-300 truncate ml-2">{{ $contract->employee->divisionNames() ?: '-' }}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-400">Kontrak</span><span class="font-semibold text-gray-700 dark:text-gray-300">#{{ str_pad($contract->id, 4, '0', STR_PAD_LEFT) }}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-400">Berakhir</span><span class="font-semibold text-gray-700 dark:text-gray-300">{{ $contract->tanggal_berakhir?->isoFormat('D MMM YYYY') }}</span></div>
            <div class="flex items-center justify-between"><span class="text-gray-400">Sisa Hari</span>
                @php($daysLeft = max(0, now()->startOfDay()->diffInDays($contract->tanggal_berakhir->copy()->startOfDay(), false)))
                <span class="font-semibold {{ $daysLeft <= 14 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $daysLeft }} hari</span>
            </div>
        </div>
    </div>
</aside>
