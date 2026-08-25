{{-- Section: Final Review --}}
<section id="section-review" class="scroll-mt-[200px]">
    <div class="mb-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Sebelum Submit</p>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-gray-50 mt-0.5">Review Evaluasi</h2>
    </div>

    {{-- Ringkasan --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4">
            <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Nama</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $contract->employee->nama }}</p></div>
            <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Jabatan</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $contract->posisi ?: '-' }}</p></div>
            <div><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Divisi</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $contract->employee->divisionNames() ?: '-' }}</p></div>
            <div class="sm:col-span-3"><p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Periode Kontrak</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $contract->tanggal_mulai?->isoFormat('D MMM YYYY') }} — {{ $contract->tanggal_berakhir?->isoFormat('D MMM YYYY') }}</p></div>
        </div>

        <div class="border-t border-gray-100 dark:border-gray-800 p-6">
            @php($complete = $filledCount === $config::indicatorCount())
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($categoryScores as $cat)
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800 px-4 py-3">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $cat['label'] }}</p>
                        <p class="mt-1 text-lg font-extrabold tabular-nums {{ $cat['score'] !== null ? 'text-gray-900 dark:text-gray-50' : 'text-gray-300' }}">{{ $cat['score'] !== null ? number_format($cat['score'], 1) : '-' }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex items-center justify-between gap-4 rounded-xl px-4 py-3 {{ $complete && ($finalScore >= $config::PASSING_THRESHOLD) ? 'bg-emerald-50 dark:bg-emerald-950/60' : 'bg-gray-50 dark:bg-gray-800/40' }}">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Nilai Akhir</span>
                <span class="text-lg font-extrabold tabular-nums text-gray-900 dark:text-gray-50">{{ number_format($finalScore ?? 0, 2) }}<span class="text-xs text-gray-400 font-semibold"> / 4.00</span></span>
            </div>
        </div>

        {{-- Approval Timeline --}}
        <div class="border-t border-gray-100 dark:border-gray-800 p-6">
            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-5">Alur Approval</p>
            <ol class="relative space-y-6 before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-px before:bg-gray-200 dark:before:bg-gray-700">
                @foreach($this->approvalSteps as $step)
                    <li class="relative flex items-start gap-4 pl-0">
                        @if($step['state'] === 'done')
                            <span class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                        @elseif($step['state'] === 'rejected')
                            <span class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-red-500 text-white"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></span>
                        @elseif($step['state'] === 'current')
                            <span class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-emerald-500 bg-white dark:bg-gray-900"><span class="h-2 w-2 rounded-full bg-emerald-500"></span></span>
                        @else
                            <span class="relative z-10 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900"><span class="h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span></span>
                        @endif
                        <div class="min-w-0 -mt-0.5">
                            <p class="text-sm font-bold {{ $step['state'] === 'pending' ? 'text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">{{ $step['label'] }}</p>
                            <p class="text-xs mt-0.5 {{ $step['state'] === 'rejected' ? 'text-red-500 font-semibold' : ($step['state'] === 'done' ? 'text-emerald-600 font-medium' : 'text-gray-400') }}">{{ $step['desc'] }}</p>
                            @if($step['name'])<p class="text-[11px] text-gray-400 mt-0.5">{{ $step['name'] }}</p>@endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- Aksi final --}}
    <div class="mt-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5">
        <button type="button" wire:click="saveDraft" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
            ← Kembali Edit & Simpan Draft
        </button>
        <button type="button" wire:click="openSubmitDialog" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm shadow-emerald-600/30 transition-all">
            {{ $isSubmitted ? 'Perbarui Evaluasi' : 'Submit Evaluasi' }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
        </button>
    </div>
</section>
