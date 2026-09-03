{{-- Section: Catatan Evaluasi --}}
<section id="section-catatan" class="scroll-mt-[200px]">
    <div class="mb-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Kualitatif</p>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-gray-50 mt-0.5">Catatan Evaluasi</h2>
    </div>

    @php
        $notesEditable = $editable ?? true;
        $showKelebihan = $notesEditable ? $this->catatanKelebihan : ($this->peerEval?->catatan_kelebihan ?? '');
        $showKekurangan = $notesEditable ? $this->catatanKekurangan : ($this->peerEval?->catatan_kekurangan ?? '');
        $showTambahan = $notesEditable ? $this->catatanTambahan : ($this->peerEval?->catatan ?? '');
    @endphp

    @if($notesEditable)
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 space-y-5">
        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-bold text-gray-800 dark:text-gray-200">Kelebihan Karyawan</label>
                <span class="text-[10px] tabular-nums text-gray-300 dark:text-gray-600"><span x-text="$wire.catatanKelebihan.length"></span>/2000</span>
            </div>
            <textarea wire:model.live.debounce.800ms="catatanKelebihan" rows="3" maxlength="2000"
                      placeholder="Tuliskan kelebihan utama karyawan selama masa kontrak..."
                      class="mt-2 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-y"></textarea>
            @error('catatanKelebihan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-bold text-gray-800 dark:text-gray-200">Hal yang Perlu Ditingkatkan</label>
                <span class="text-[10px] tabular-nums text-gray-300 dark:text-gray-600"><span x-text="$wire.catatanKekurangan.length"></span>/2000</span>
            </div>
            <textarea wire:model.live.debounce.800ms="catatanKekurangan" rows="3" maxlength="2000"
                      placeholder="Tuliskan aspek yang masih perlu ditingkatkan..."
                      class="mt-2 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-y"></textarea>
            @error('catatanKekurangan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Catatan Tambahan <span class="font-normal text-gray-400">(opsional)</span></label>
                <span class="text-[10px] tabular-nums text-gray-300 dark:text-gray-600"><span x-text="$wire.catatanTambahan.length"></span>/2000</span>
            </div>
            <textarea wire:model.live.debounce.800ms="catatanTambahan" rows="2" maxlength="2000"
                      placeholder="Tambahkan catatan lain bila diperlukan..."
                      class="mt-2 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm resize-y"></textarea>
            @error('catatanTambahan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-800/40 p-6 space-y-5">
        <p class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-[10px] font-bold text-gray-500 dark:text-gray-400">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            Read Only — bagian ini diisi oleh Koordinator
        </p>
        <div>
            <label class="text-sm font-bold text-gray-800 dark:text-gray-200">Kelebihan Karyawan</label>
            <p class="mt-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $showKelebihan ?: '—' }}</p>
        </div>
        <div>
            <label class="text-sm font-bold text-gray-800 dark:text-gray-200">Hal yang Perlu Ditingkatkan</label>
            <p class="mt-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $showKekurangan ?: '—' }}</p>
        </div>
        <div>
            <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Catatan Tambahan</label>
            <p class="mt-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $showTambahan ?: '—' }}</p>
        </div>
    </div>
    @endif
</section>
