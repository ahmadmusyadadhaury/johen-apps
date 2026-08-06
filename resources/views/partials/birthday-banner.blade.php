@if(($birthdayEmployees ?? collect())->isNotEmpty() && !$birthdayEmployee && !($hideBirthdayBanner ?? false) && !($alreadySentWish ?? false))
{{-- Slim birthday strip shown to ALL OTHER users above the main card --}}
<div x-data="{ open: true, dontShow: false }"
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="flex flex-col lg:flex-row lg:items-center gap-2.5 lg:gap-3 rounded-xl border border-primary-100 dark:border-primary-900/40 bg-gradient-to-r from-primary-50 to-violet-50 dark:from-primary-950/30 dark:to-violet-950/30 px-4 py-3 mb-6">
    <div class="flex items-center gap-2.5 min-w-0">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-500/15 text-primary-600 dark:text-primary-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v.5m-6-2.5v.5m4.875-1.75v.5m4.5-3.5v.5m-2.25-1.5v.5m.75-2.25v.5M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
        </div>
        <div class="leading-tight">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-primary-600 dark:text-primary-400">Hari Ini</p>
            <p class="text-sm sm:text-[15px] font-display font-bold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                Selamat Ulang Tahun, {{ $birthdayEmployees->pluck('nama')->join(' & ') }}! 🎉
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.birthday-wish') }}" class="flex flex-wrap items-center gap-2 lg:flex-1 lg:justify-end">
        @csrf
        @if($birthdayEmployees->count() > 1)
        <select name="employee_id" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-2.5 py-1.5 text-xs font-medium text-gray-800 dark:text-gray-100 shadow-sm outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900/40 sm:w-36">
            <option value="" disabled selected>Untuk siapa?</option>
            @foreach($birthdayEmployees as $be)
            <option value="{{ $be->id }}">{{ $be->nama }}</option>
            @endforeach
        </select>
        @else
        <input type="hidden" name="employee_id" value="{{ $birthdayEmployees->first()->id }}">
        @endif
        <input type="text" name="message" required maxlength="300" placeholder="Tulis ucapanmu..." class="input-field flex-1 min-w-0 rounded-lg px-3 py-1.5 text-xs sm:mr-3">
        <button type="submit" class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition-all duration-200 hover:bg-primary-500 active:scale-[0.98">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
            Kirim
        </button>
    </form>

    <div class="flex items-center gap-2.5 shrink-0">
        <label class="flex items-center gap-1.5 cursor-pointer select-none">
            <input type="checkbox" x-model="dontShow" class="h-3.5 w-3.5 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
            <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Jangan tampilkan lagi</span>
        </label>
        <button type="button"
                @click="if (dontShow) { fetch('{{ route('dashboard.birthday-banner.hide') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'), 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ hide: true }) }) } open = false"
                class="rounded-lg p-1.5 text-gray-400 hover:text-gray-600 hover:bg-white/70 dark:hover:text-gray-200 dark:hover:bg-gray-800 transition-colors" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
@endif
