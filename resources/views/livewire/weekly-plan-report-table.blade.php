<div>
    @if(session('message'))
        <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola rencana kerja mingguan</p>
        </div>
        @unless($hideCreateButton)
        <button wire:click="openNew" class="btn-primary text-xs py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah WPR
        </button>
        @endunless
    </div>

    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">Nama</th>
                        <th class="px-4 py-3 border-r border-gray-100 dark:border-gray-700"></th>
                        <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-primary-700 dark:text-primary-400 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">W-1</th>
                        <th colspan="3" class="px-4 py-3 text-center text-xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider border-r border-gray-100 dark:border-gray-700">W+1</th>
                        <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Feedback Atasan</th>
                    </tr>
                    <tr class="bg-gray-50/50 dark:bg-gray-800/50 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Week</th>
                        <th class="px-4 py-2.5">Tanggal</th>
                        <th class="px-4 py-2.5">Kategori</th>
                        <th class="px-4 py-2.5">Plan Activity</th>
                        <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Objective</th>
                        <th class="px-4 py-2.5">Keterangan</th>
                        <th class="px-4 py-2.5">Action Plan</th>
                        <th class="px-4 py-2.5 border-r border-gray-100 dark:border-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @php
                        $weekGroupCounts = [];
                        $weekGroupKey = null;
                        $nameGroupCounts = [];
                        $nameGroupKey = null;
                        foreach ($reports as $i => $r) {
                            $empId = $r->employee_id;
                            $week = $r->tanggal->isoWeek;
                            $wkKey = $empId . '-' . $week;
                            if ($wkKey !== $weekGroupKey) {
                                $weekGroupKey = $wkKey;
                                $weekGroupCounts[$i] = 1;
                            } else {
                                end($weekGroupCounts);
                                $weekGroupCounts[key($weekGroupCounts)]++;
                            }
                            $nKey = (string) $empId;
                            if ($nKey !== $nameGroupKey) {
                                $nameGroupKey = $nKey;
                                $nameGroupCounts[$i] = 1;
                            } else {
                                end($nameGroupCounts);
                                $nameGroupCounts[key($nameGroupCounts)]++;
                            }
                        }
                    @endphp
                    @forelse($reports as $i => $r)
                    @php
                        $reportEmployee = $r->employee;
                        $canGiveFeedback = isset($canGiveFeedbackMap[$reportEmployee?->id]);
                        $isFirstInWeekGroup = isset($weekGroupCounts[$i]);
                        $isFirstInNameGroup = isset($nameGroupCounts[$i]);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 dark:bg-gray-900 transition-colors">
                        @if($isFirstInNameGroup)
                        <td rowspan="{{ $nameGroupCounts[$i] }}" class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap align-middle border-r border-gray-100 dark:border-gray-700">{{ $reportEmployee?->nama ?? '-' }}</td>
                        @endif
                        @if($isFirstInWeekGroup)
                        <td rowspan="{{ $weekGroupCounts[$i] }}" class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-center whitespace-nowrap align-middle border-r border-gray-100 dark:border-gray-700">{{ $r->tanggal->isoWeek }}</td>
                        @endif
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $r->tanggal->isoFormat('D MMM YYYY') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $r->kategori }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ $r->plan_activity }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate border-r border-gray-100 dark:border-gray-700">{{ $r->objective }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                            @if($r->keterangan)
                                {{ $r->keterangan }}
                            @else
                                <span class="text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                            @if($r->action_plan)
                                {{ $r->action_plan }}
                            @else
                                <span class="text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center border-r border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-center gap-2">
                                @if($reportEmployee && auth()->user()->employee && $reportEmployee->id === auth()->user()->employee->id)
                                    @if(!$r->keterangan || !$r->action_plan)
                                    <button wire:click="openW1({{ $r->id }})" class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 hover:underline whitespace-nowrap">
                                        Isi W+1
                                    </button>
                                    @else
                                    <span class="text-xs text-gray-400">Selesai</span>
                                    @endif
                                    <button wire:click="delete({{ $r->id }})" wire:confirm="Hapus WPR ini?" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 whitespace-nowrap" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @elseif(auth()->user()->isKoordinator())
                                    <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                    <button wire:click="delete({{ $r->id }})" wire:confirm="Hapus WPR ini?" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 whitespace-nowrap" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 max-w-xs truncate">
                            @if($canGiveFeedback)
                                @if($r->feedback_atasan)
                                    {{ $r->feedback_atasan }}
                                @else
                                    <button wire:click="openFeedback({{ $r->id }})" class="text-xs font-medium text-amber-600 hover:text-amber-700 dark:text-amber-400 hover:underline whitespace-nowrap">
                                        Beri Feedback
                                    </button>
                                @endif
                            @else
                                @if($r->feedback_atasan)
                                    {{ $r->feedback_atasan }}
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 italic">-</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                            <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            </div>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Weekly Plan Report</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $hideCreateButton ? 'Belum ada WPR dari bawahan' : 'Klik tombol "Tambah WPR" untuk membuat rencana kerja mingguan' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $reports->links() }}
        </div>
        @endif
    </div>

    {{-- Modal Tambah WPR (W-1) --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showModal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Weekly Plan Report</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Isi rencana kerja mingguan (W-1)</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Tanggal *" />
                        <x-text-input type="date" wire:model="tanggal" class="mt-1 block w-full" />
                        @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Kategori *" />
                        <select wire:model="kategori" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">Pilih kategori</option>
                            <option value="justification">Justification</option>
                            <option value="assessment">Assessment</option>
                            <option value="direct">Direct</option>
                            <option value="supervision">Supervision</option>
                            <option value="other">Other</option>
                        </select>
                        @error('kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <x-input-label value="Plan Activity *" />
                    <textarea wire:model="plan_activity" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Deskripsi rencana aktivitas..."></textarea>
                    @error('plan_activity') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Objective *" />
                    <textarea wire:model="objective" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Tujuan dari aktivitas ini..."></textarea>
                    @error('objective') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan WPR
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Isi W+1 --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showW1Modal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Isi W+1</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tindak lanjut dari rencana kerja mingguan</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 p-4 mb-6 space-y-1">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Konteks W-1</p>
                <p class="text-sm text-gray-900 dark:text-gray-100"><span class="font-medium">Kategori:</span> <span class="capitalize">{{ $kategori }}</span></p>
                <p class="text-sm text-gray-900 dark:text-gray-100"><span class="font-medium">Tanggal:</span> {{ $tanggal }}</p>
                <p class="text-sm text-gray-900 dark:text-gray-100"><span class="font-medium">Plan Activity:</span> {{ $plan_activity }}</p>
                <p class="text-sm text-gray-900 dark:text-gray-100"><span class="font-medium">Objective:</span> {{ $objective }}</p>
            </div>

            <form wire:submit.prevent="saveW1" class="space-y-4">
                <div>
                    <x-input-label value="Kesesuaian Kategori" />
                    <x-text-input type="text" :value="$kategori" class="mt-1 block w-full bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 capitalize" readonly />
                </div>
                <div>
                    <x-input-label value="Keterangan *" />
                    <textarea wire:model="keterangan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Catatan tindak lanjut..."></textarea>
                    @error('keterangan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <x-input-label value="Action Plan *" />
                    <textarea wire:model="action_plan" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Rencana tindak lanjut..."></textarea>
                    @error('action_plan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Simpan W+1
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Feedback Atasan --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showFeedbackModal', value => open = value)"
         x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Feedback Atasan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Berikan feedback untuk rencana kerja ini</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="saveFeedback" class="space-y-4">
                <div>
                    <x-input-label value="Feedback *" />
                    <textarea wire:model="feedback_atasan" rows="4" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Tulis feedback Anda..."></textarea>
                    @error('feedback_atasan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL FOTO --}}
    <div x-show="showFotoModal" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center p-5 bg-gray-900/60 backdrop-blur-sm"
         @click="showFotoModal = false">
        <div x-show="showFotoModal" x-cloak
             x-transition:enter="transition-all ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.stop
             class="w-full max-w-4xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Foto</h3>
                <button @click="showFotoModal = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-3" x-text="fotoModalLabel"></p>
                <img :src="fotoModalUrl" :alt="fotoModalLabel" class="max-w-full max-h-[70vh] w-auto h-auto object-contain mx-auto rounded-xl border border-gray-200 dark:border-gray-600">
            </div>
            <div class="flex items-center justify-end px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="showFotoModal = false" class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
