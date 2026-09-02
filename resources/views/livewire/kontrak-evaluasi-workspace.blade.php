@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Evaluasi Kontrak</h1>
        <p class="text-xs text-gray-400 mt-0.5">Workspace penilaian masa kontrak karyawan</p>
    </div>
@endpush

<div x-data="{
        activeSection: 'info',
        sections: @js(collect($config::sections())->pluck('id')->toArray()),
        onScroll() {
            for (let i = this.sections.length - 1; i >= 0; i--) {
                const el = document.getElementById('section-' + this.sections[i]);
                if (el && el.getBoundingClientRect().top <= 180) { this.activeSection = this.sections[i]; return; }
            }
            this.activeSection = 'info';
        }
    }"
     x-init="onScroll(); window.addEventListener('scroll', onScroll, { passive: true })"
     class="pb-24 lg:pb-10">

    {{-- Page header --}}
    <header class="border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-4 lg:px-8">

            {{-- Breadcrumb + actions --}}
            <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 py-4">
                <nav class="flex items-center gap-1.5 min-w-0" aria-label="Breadcrumb">
                    <span class="shrink-0 text-xs font-medium text-gray-400 dark:text-gray-500">Evaluasi Kontrak</span>
                    <svg class="w-3 h-3 shrink-0 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    <span class="shrink-0 text-xs font-semibold text-gray-600 dark:text-gray-400 truncate max-w-[38vw] sm:max-w-[240px]">{{ $contract->employee->nama }}</span>
                    <svg class="w-3 h-3 shrink-0 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    <span class="shrink-0 text-xs font-bold text-gray-900 dark:text-gray-100">Kontrak #{{ str_pad($contract->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if($isSubmitted)
                        <span class="shrink-0 inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 text-[10px] font-bold text-emerald-700 dark:text-emerald-300">Final</span>
                    @else
                        <span class="shrink-0 inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-950 px-2 py-0.5 text-[10px] font-bold text-amber-700 dark:text-amber-300">Draft</span>
                    @endif
                </nav>

                <div class="flex items-center gap-2.5">
                    <span class="hidden sm:inline-flex items-center gap-1.5 text-xs" :class="$wire.saveState === 'error' ? 'text-red-500' : 'text-gray-400'">
                        <template x-if="$wire.saveState === 'saving'">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                Menyimpan...
                            </span>
                        </template>
                        <template x-if="$wire.saveState === 'saved'">
                            <span class="inline-flex items-center gap-1.5 text-emerald-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Tersimpan <span x-text="$wire.savedAt"></span>
                            </span>
                        </template>
                        <template x-if="$wire.saveState === 'error'">
                            <span class="inline-flex items-center gap-1.5">Gagal menyimpan</span>
                        </template>
                    </span>

                    <button type="button" wire:click="saveDraft"
                            wire:loading.attr="disabled" wire:target="saveDraft"
                            class="inline-flex h-9 items-center rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-60 transition-all">
                        Simpan Draft
                    </button>

                    <button type="button" wire:click="openSubmitDialog"
                            class="inline-flex h-9 items-center rounded-lg bg-emerald-600 hover:bg-emerald-700 px-4 text-sm font-semibold text-white shadow-sm shadow-emerald-600/25 transition-all">
                        {{ $isSubmitted ? 'Perbarui Evaluasi' : 'Submit Evaluasi' }}
                    </button>
                </div>
            </div>

            {{-- Progress evaluasi --}}
            <div class="pb-4">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Progress Evaluasi</p>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tabular-nums whitespace-nowrap">{{ $filledCount }}/{{ $config::indicatorCount() }} indikator selesai · {{ $progressPercent }}%</p>
                </div>
                <div class="mt-2 h-1 w-full rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
        </div>

        {{-- Sticky tab navigation --}}
        <div class="sticky top-0 z-20 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
            <div class="overflow-x-auto scrollbar-none">
                <div class="flex gap-1 px-4 lg:px-8 min-w-max">
                    @foreach($config::sections() as $section)
                        <button type="button"
                                @click="activeSection = '{{ $section['id'] }}'; document.getElementById('section-{{ $section['id'] }}')?.scrollIntoView({behavior:'smooth', block:'start'})"
                                class="relative -mb-px flex shrink-0 items-center gap-1.5 border-b-2 px-3.5 py-3 text-[13px] transition-colors duration-150"
                                :class="activeSection === '{{ $section['id'] }}' ? 'border-emerald-600 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-700 hover:text-gray-700 dark:hover:text-gray-200'">
                            <span :class="activeSection === '{{ $section['id'] }}' ? 'font-semibold' : 'font-medium'">{{ $section['label'] }}</span>
                            @if(in_array($section['id'], ['disiplin','kinerja','sikap','hasil']))
                                @php
                                    $cs = $this->categoryScores()[$section['id']] ?? null;
                                @endphp
                                @if($cs)
                                    <span class="inline-flex items-center gap-0.5 rounded-md px-1.5 py-0.5 text-[10px] font-semibold tabular-nums {{ $cs['filled'] === $cs['total'] ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                        @if($cs['filled'] === $cs['total'])
                                            <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        @endif
                                        {{ $cs['filled'] }}/{{ $cs['total'] }}
                                    </span>
                                @endif
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    {{-- Error summary --}}
    @if($errors->any())
    <div class="mt-5 rounded-2xl border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/40 p-5">
        <p class="text-sm font-bold text-red-700 dark:text-red-300">{{ count($this->missingIndicators()) }} penilaian belum diisi</p>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($this->missingIndicators() as $m)
            <button type="button" onclick="document.getElementById('card-{{ $m['field'] }}')?.scrollIntoView({behavior:'smooth'});document.getElementById('card-{{ $m['field'] }}')?.classList.add('ring-2','ring-red-400');setTimeout(()=>document.getElementById('card-{{ $m['field'] }}')?.classList.remove('ring-2','ring-red-400'),1600)"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-white dark:bg-gray-900 border border-red-200 dark:border-red-900 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-950 transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                {{ $m['label'] }}
            </button>
            @endforeach
        </div>
        @error('summary')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        @error('perpanjangan')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
    </div>
    @endif

    {{-- 2-column layout: content + summary --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-8 items-start">

        {{-- MAIN CONTENT --}}
        <main class="min-w-0 space-y-10">

            {{-- Section: Informasi Karyawan --}}
            <section id="section-info" class="scroll-mt-[200px]">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-5">
                            @if($contract->employee->fotoUrl)
                                <img src="{{ $contract->employee->fotoUrl }}" alt="{{ $contract->employee->nama }}" class="h-16 w-16 shrink-0 rounded-2xl object-cover ring-2 ring-emerald-100 dark:ring-emerald-900">
                            @else
                                <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 dark:bg-emerald-950 text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ strtoupper(substr($contract->employee->nama, 0, 1)) }}</span>
                            @endif
                            <div class="min-w-0">
                                <h2 class="text-lg font-extrabold text-gray-900 dark:text-gray-50 leading-tight">{{ $contract->employee->nama }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">NIK {{ $contract->employee->nik }}</p>
                                <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950 px-3 py-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-300">
                                    Kontrak #{{ str_pad($contract->id, 4, '0', STR_PAD_LEFT) }} · sisa {{ max(0, now()->startOfDay()->diffInDays($contract->tanggal_berakhir->copy()->startOfDay(), false)) }} hari
                                </span>
                            </div>
                        </div>

                        <dl class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-x-6 gap-y-4 pt-5 border-t border-gray-100 dark:border-gray-800">
                            <div><dt class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Jabatan</dt><dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $contract->posisi ?: '-' }}</dd></div>
                            <div><dt class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Divisi</dt><dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $contract->employee->divisionNames() ?: '-' }}</dd></div>
                            <div><dt class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Masa Kontrak</dt><dd class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $contract->tanggal_mulai?->isoFormat('D MMM YYYY') }} — {{ $contract->tanggal_berakhir?->isoFormat('D MMM YYYY') }}</dd></div>
                            <div><dt class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Surat Teguran / SP</dt><dd class="mt-1 text-sm font-semibold text-gray-400">—</dd></div>
                        </dl>
                    </div>
                </div>
            </section>

            {{-- Sections kategori skor --}}
            @foreach($config::categories() as $category)
            <section id="section-{{ $category['section'] }}" class="scroll-mt-[200px]">
                @php
                    $cs = $this->categoryScores()[$category['key']] ?? null;
                    $catFilled = $cs ? $cs['filled'] : 0;
                    $catTotal = count($category['indicators']);
                    $catComplete = $catFilled === $catTotal;
                @endphp
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-sm font-extrabold text-emerald-600 dark:text-emerald-400">{{ $category['code'] }}</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">Bobot {{ $category['weight'] }}%</p>
                            <h2 class="text-base font-extrabold text-gray-900 dark:text-gray-50 leading-tight">{{ $category['label'] }}</h2>
                        </div>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-bold {{ $catComplete ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}">
                            <span class="tabular-nums">{{ $catFilled }}/{{ $catTotal }}</span> indikator
                            @if($catComplete)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
                            @endif
                        </span>
                        <div class="mt-1.5 ml-auto h-1 w-24 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $catTotal > 0 ? $catFilled / $catTotal * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    @foreach($category['indicators'] as $indicator)
                        @include('livewire.partials.eval-card', ['indicator' => $indicator])
                    @endforeach
                </div>

                <div class="mt-5 flex items-center justify-between gap-3">
                    @php
                        $sectionIndex = array_search($category['section'], array_column($config::sections(), 'id'));
                        $prev = $sectionIndex > 0 ? $config::sections()[$sectionIndex - 1] : null;
                        $next = $sectionIndex < count($config::sections()) - 1 ? $config::sections()[$sectionIndex + 1] : null;
                    @endphp
                    @if($prev)
                        <button type="button" onclick="document.getElementById('section-{{ $prev['id'] }}')?.scrollIntoView({behavior:'smooth'})" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">← {{ $prev['label'] }}</button>
                    @else
                        <span></span>
                    @endif
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="saveDraft" class="text-xs font-semibold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">Simpan Draft</button>
                        @if($next)
                            <button type="button" onclick="document.getElementById('section-{{ $next['id'] }}')?.scrollIntoView({behavior:'smooth'})" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 dark:bg-gray-700 text-white text-xs font-semibold hover:bg-gray-700 dark:hover:bg-gray-600 transition-all">Lanjut ke {{ $next['label'] }} →</button>
                        @endif
                    </div>
                </div>
            </section>
            @endforeach

            @include('livewire.partials.eval-notes-section')
            @include('livewire.partials.eval-recommendation-section')
            @include('livewire.partials.eval-review-section')

        </main>

        {{-- SCORE SUMMARY (desktop) --}}
        @include('livewire.partials.eval-summary-panel')
    </div>

    {{-- Mobile: summary collapsible --}}
    <div class="lg:hidden mt-8">
        <details class="group rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
            <summary class="flex cursor-pointer select-none items-center justify-between gap-3 p-5 list-none">
                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">Ringkasan Skor</span>
                <span class="text-xl font-extrabold text-emerald-600">{{ number_format($finalScore ?? 0, 2) }}<span class="text-xs text-gray-400 font-semibold"> / 4.00</span></span>
                <svg class="w-4 h-4 text-gray-400 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </summary>
            <div class="px-5 pb-5">
                @php $mobileSummary = true; @endphp
                @include('livewire.partials.eval-summary-body')
            </div>
        </details>
    </div>

    {{-- Mobile sticky action bar --}}
    <div class="lg:hidden fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-900/95 backdrop-blur px-4 py-3 flex items-center gap-3">
        <button type="button" wire:click="saveDraft" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-semibold text-gray-600 dark:text-gray-300">Simpan Draft</button>
        <button type="button" wire:click="openSubmitDialog" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-sm">{{ $isSubmitted ? 'Perbarui' : 'Submit Evaluasi' }}</button>
    </div>

    {{-- Submit confirmation dialog --}}
    @include('livewire.partials.eval-submit-dialog')
</div>
