{{-- Section: Rekomendasi --}}
<section id="section-rekomendasi" class="scroll-mt-[200px]">
    <div class="mb-4">
        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Pengembangan & Kelanjutan</p>
        <h2 class="text-base font-extrabold text-gray-900 dark:text-gray-50 mt-0.5">Rekomendasi Pengembangan dari Atasan</h2>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-6 space-y-6">
        <div>
            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Fokus pengembangan</p>
            <p class="text-xs text-gray-400 mt-0.5">Pilih salah satu atau beberapa rekomendasi.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($config::devTags() as $tag)
                    @php($selected = in_array($tag, $devTags))
                    @php($isOther = $tag === 'Lainnya')
                    <button type="button"
                            wire:click="toggleDevTag('{{ $tag }}')"
                            class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-xs font-semibold transition-all duration-150 {{ $selected ? 'border-emerald-500 bg-emerald-500 text-white shadow-sm' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/60' }}">
                        @if(!$isOther && $selected)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                        @endif
                        {{ $tag }}
                    </button>
                @endforeach
            </div>
            @if(in_array('Lainnya', $devTags))
                <input type="text" wire:model.live.debounce.800ms="devOther" placeholder="Tuliskan rekomendasi lainnya..."
                       class="mt-3 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
            @endif
        </div>

        {{-- Recommendation Card --}}
        @php($lulus = ($finalScore !== null && $filledCount === $config::indicatorCount()) && $finalScore >= $config::PASSING_THRESHOLD)
        @php($complete = $filledCount === $config::indicatorCount())
        <div id="recommendation-card" class="rounded-2xl border p-5 transition-all duration-300 {{ !$complete ? 'border-gray-200 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-800/40' : ($lulus ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/40' : 'border-red-200 dark:border-red-900 bg-red-50/70 dark:bg-red-950/40') }}">
            @if(!$complete)
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></svg></span>
                    <div>
                        <p class="text-sm font-bold text-gray-500">Hasil rekomendasi belum dapat ditentukan</p>
                        <p class="text-xs text-gray-400 mt-0.5">Selesaikan seluruh indikator penilaian untuk melihat hasil evaluasi.</p>
                    </div>
                </div>
            @elseif($lulus)
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900 text-emerald-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Lulus Evaluasi · Nilai {{ number_format($finalScore, 2) }}</p>
                        <p class="text-base font-extrabold text-gray-900 dark:text-gray-50 mt-0.5">Rekomendasi: Perpanjang Masa Kontrak</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Durasi (bulan)</label>
                        <input type="number" min="1" max="36" wire:model.live.debounce.800ms="perpanjanganBulan"
                               class="mt-1.5 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm" placeholder="6">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Mulai</label>
                        <input type="date" wire:model.live.debounce.800ms="perpanjanganMulai"
                               class="mt-1.5 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Berakhir</label>
                        <input type="date" wire:model.live.debounce.800ms="perpanjanganBerakhir"
                               class="mt-1.5 w-full rounded-xl border-gray-200 dark:border-gray-700 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900 text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></span>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-red-500">Tidak Lulus Evaluasi · Nilai {{ number_format($finalScore, 2) }}</p>
                        <p class="text-base font-extrabold text-gray-900 dark:text-gray-50 mt-0.5">Rekomendasi: Tidak Diperpanjang</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
