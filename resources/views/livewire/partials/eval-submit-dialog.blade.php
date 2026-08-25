<div x-data="{ open: false }"
     x-init="Livewire.on('eval-open-submit', () => open = true)"
     x-cloak x-show="open"
     class="fixed inset-0 z-[300] flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
     x-transition.opacity
     @click.self="open = false">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-gray-900 shadow-2xl p-6 sm:p-7"
         x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0">
        <div class="flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
            </span>
            <div>
                <h3 class="text-base font-extrabold text-gray-900 dark:text-gray-50">Submit Evaluasi?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Pastikan seluruh penilaian dan catatan sudah benar. Setelah dikirim, evaluasi akan masuk ke tahap approval.</p>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-3">
            <button type="button" @click="open = false" class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">Batal</button>
            <button type="button" @click="open = false; $wire.call('submit')"
                    wire:loading.attr="disabled" wire:target="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-semibold shadow-sm transition-all">
                <span wire:loading.remove wire:target="submit">Submit Evaluasi</span>
                <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    Mengirim...
                </span>
            </button>
        </div>
    </div>
</div>
