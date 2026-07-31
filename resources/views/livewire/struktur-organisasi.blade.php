<div x-data="strukturOrganisasi()">
    {{-- Header --}}
    <div class="flex items-center justify-end px-4 sm:px-8 pt-6 pb-2">
        <button x-show="focusedId" @click="focusedId = null"
                class="flex items-center gap-1.5 text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Kembali ke seluruh bagan
        </button>
    </div>

    <div class="py-6 px-4 sm:px-8 overflow-x-auto">
        {{-- Full tree view --}}
        <div x-show="!focusedId"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            @if(count($roots) > 0)
                <div class="flex flex-col items-center min-w-max">
                    @foreach($roots as $root)
                        @include('livewire.struktur-organisasi-tree', ['node' => $root, 'level' => 1, 'notesByPosition' => $notesByPosition, 'myPositionId' => $myPositionId, 'canGiveNotesByPosition' => $canGiveNotesByPosition])
                    @endforeach
                </div>
            @else
                <div class="py-16 text-center">
                    <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-xl bg-gray-100 dark:bg-gray-800">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Belum ada data organisasi</h3>
                    <p class="text-xs text-gray-400">Tambahkan jabatan terlebih dahulu</p>
                </div>
            @endif
        </div>

        {{-- Focus view: parent → focused → children --}}
        <div x-show="focusedId" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="flex flex-col items-center min-w-max py-4">
            {{-- Parent --}}
            <template x-if="parent">
                <div>
                    <div :class="children.length > 4 ? 'w-48 p-3.5' : 'w-64 p-4'"
                         class="relative cursor-pointer rounded-xl bg-white dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 shadow-sm text-center hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-md transition-all"
                         @click="focusedId = parent.id">
                        <div class="flex items-center justify-center gap-1.5 mb-1">
                            <template x-if="parent.employee_foto">
                                <img :src="parent.employee_foto" :alt="parent.employee_nama"
                                     :class="children.length > 4 ? 'w-6 h-6' : 'w-8 h-8'"
                                     class="rounded object-cover bg-gray-50 dark:bg-gray-700 shrink-0 shadow-sm">
                            </template>
                            <template x-if="!parent.employee_foto">
                                <div :class="children.length > 4 ? 'w-6 h-6 text-[11px]' : 'w-8 h-8 text-[13px]'"
                                     class="flex items-center justify-center rounded text-white font-bold shrink-0 bg-amber-500">
                                    <span x-text="parent.nama.charAt(0).toUpperCase()"></span>
                                </div>
                            </template>
                            <span class="text-[9px] font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wider">Atasan</span>
                        </div>
                        <p :class="children.length > 4 ? 'text-xs' : 'text-sm'" class="font-semibold text-gray-900 dark:text-gray-100 leading-tight" x-text="parent.nama"></p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 truncate" x-text="parent.employee_nama || ''"></p>
                    </div>
                    <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600 mx-auto"></div>
                </div>
            </template>

             {{-- Focused position --}}
             <div :class="children.length > 4 ? 'w-48 p-3.5' : 'w-64 p-4'"
                  class="relative rounded-xl bg-gradient-to-br from-primary-50 to-blue-50 dark:from-primary-950 dark:to-blue-950 border-2 border-primary-500 shadow-lg shadow-primary-100 dark:shadow-primary-900/30 text-center ring-2 ring-primary-200 dark:ring-primary-800">
                 <div class="flex items-center justify-center gap-1.5 mb-1">
                     <template x-if="focused.employee_foto">
                         <img :src="focused.employee_foto" :alt="focused.employee_nama"
                              :class="children.length > 4 ? 'w-6 h-6' : 'w-8 h-8'"
                              class="rounded object-cover bg-gray-50 dark:bg-gray-700 shrink-0 shadow-sm">
                     </template>
                     <template x-if="!focused.employee_foto">
                         <div :class="children.length > 4 ? 'w-6 h-6 text-[11px]' : 'w-8 h-8 text-[13px]'"
                              class="flex items-center justify-center rounded text-white font-bold shrink-0 bg-primary-600">
                             <span x-text="focused.nama.charAt(0).toUpperCase()"></span>
                         </div>
                     </template>
                     <span class="text-[9px] font-medium text-primary-600 dark:text-primary-400 uppercase tracking-wider">Terfokus</span>
                 </div>
                 <p :class="children.length > 4 ? 'text-xs' : 'text-sm'" class="font-bold text-primary-800 dark:text-primary-200 leading-tight" x-text="focused.nama"></p>
                 <p class="text-[10px] text-primary-600 dark:text-primary-400 mt-0.5 truncate font-medium" x-text="focused.employee_nama || ''"></p>

                 {{-- Note buttons for focused --}}
                  <div class="flex items-center justify-center gap-2 mt-2.5">
                      <template x-if="canGiveNotes[focused.id]">
                          <button @click.stop="$wire.openNoteModal(focused.id, 'history')"
                                  class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-[10px] font-medium transition-colors">
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                               Evaluasi
                           </button>
                           <button @click.stop="$wire.openNoteModal(focused.id, 'history')"
                                   class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-[10px] font-medium transition-colors">
                               <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                               Riwayat Evaluasi
                          </button>
                      </template>
                      <template x-if="!canGiveNotes[focused.id] && focused.id === myPositionId">
                          <button @click.stop="$wire.openNoteModal(focused.id, 'history')"
                                   class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 hover:border-primary-400 dark:hover:border-primary-500 text-[10px] font-medium transition-colors">
                               Lihat Evaluasi
                          </button>
                      </template>
                  </div>
             </div>

            {{-- Children --}}
            <template x-if="children.length > 0">
                <div>
                    <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600 mx-auto"></div>
                    <div class="relative flex items-start justify-center" :class="children.length > 4 ? 'gap-8' : 'gap-12'">
                        <template x-if="children.length > 1">
                            <div class="absolute top-0 left-[5%] right-[5%] h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                        </template>
                        <template x-for="child in children" :key="child.id">
                            <div class="flex flex-col items-center shrink-0">
                                <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600"></div>
                                 <div @click="focusedId = child.id"
                                      :class="children.length > 4 ? 'w-48 p-3.5' : 'w-64 p-4'"
                                      class="relative cursor-pointer rounded-xl bg-white dark:bg-gray-800 border-2 border-dashed border-gray-300 dark:border-gray-600 shadow-sm text-center hover:border-purple-400 dark:hover:border-purple-500 hover:shadow-md transition-all">
                                     <div class="flex items-center justify-center gap-1.5 mb-1">
                                         <template x-if="child.employee_foto">
                                             <img :src="child.employee_foto" :alt="child.employee_nama"
                                                  :class="children.length > 4 ? 'w-6 h-6' : 'w-8 h-8'"
                                                  class="rounded object-cover bg-gray-50 dark:bg-gray-700 shrink-0 shadow-sm">
                                         </template>
                                         <template x-if="!child.employee_foto">
                                             <div :class="children.length > 4 ? 'w-6 h-6 text-[11px]' : 'w-8 h-8 text-[13px]'"
                                                  class="flex items-center justify-center rounded text-white font-bold shrink-0 bg-purple-500">
                                                 <span x-text="child.nama.charAt(0).toUpperCase()"></span>
                                             </div>
                                         </template>
                                         <span class="text-[9px] font-medium text-purple-600 dark:text-purple-400 uppercase tracking-wider">Bawahan</span>
                                     </div>
                                     <p :class="children.length > 4 ? 'text-xs' : 'text-sm'" class="font-semibold text-gray-900 dark:text-gray-100 leading-tight" x-text="child.nama"></p>
                                     <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 truncate" x-text="child.employee_nama || ''"></p>

                                        {{-- Note buttons for child --}}
                                       <div class="flex items-center justify-center gap-2 mt-2.5">
                                           <template x-if="canGiveNotes[child.id]">
                                               <button @click.stop="$wire.openNoteModal(child.id, 'history')"
                                                       class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-[10px] font-medium transition-colors">
                                                   <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                    Evaluasi
                                                </button>
                                                <button @click.stop="$wire.openNoteModal(child.id, 'history')"
                                                        class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-[10px] font-medium transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Riwayat Evaluasi
                                               </button>
                                           </template>
                                            <template x-if="!canGiveNotes[child.id] && child.id === myPositionId">
                                                <button @click.stop="$wire.openNoteModal(child.id, 'history')"
                                                        class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 hover:border-primary-400 dark:hover:border-primary-500 text-[10px] font-medium transition-colors">
                                                        Lihat Evaluasi
                                                </button>
                                            </template>
                                       </div>
                                 </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="children.length === 0">
                <div class="mt-4 text-center">
                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Tidak ada bawahan</span>
                </div>
            </template>
        </div>
    </div>

    {{-- Note Modal --}}
    <x-modal name="note-modal" maxWidth="2xl">
        <div class="p-6">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                        @if($viewState === 'detail')
                            Detail Evaluasi
                        @elseif($viewState === 'history')
                            Riwayat Evaluasi
                        @else
                            {{ $existingNote ? 'Edit Evaluasi' : 'Tambah Evaluasi' }}
                        @endif
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $selectedPositionName ?: 'Evaluasi & Rekomendasi Jabatan' }}</p>
                </div>
                <button @click="$dispatch('close-modal', { name: 'note-modal' })"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if($selectedPositionId)
                {{-- FORM VIEW --}}
                @if($viewState === 'form')
                    <div class="mb-4 flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9"/></svg>
                                <span>{{ count($notesHistory) }} evaluasi tersimpan</span>
                        </span>
                        @if($isSuperior)
                            <span class="px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 font-medium">Anda dapat memberi evaluasi</span>
                        @endif
                    </div>

                    {{-- Period selector --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label>
                            <select wire:model.live="bulan"
                                    class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                                    <option value="{{ $i + 1 }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tahun</label>
                            <select wire:model.live="tahun"
                                    class="text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-800 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                @for($t = now()->year - 2; $t <= now()->year + 2; $t++)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Form --}}
                    @if($isSuperior)
                        <div class="space-y-3 mb-5">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Situasi</label>
                                <textarea wire:model="situasi" rows="2"
                                          class="w-full text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                          placeholder="Situasi jabatan..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Evaluasi</label>
                                <textarea wire:model="evaluasi" rows="2"
                                          class="w-full text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                          placeholder="Evaluasi untuk jabatan ini..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Komitmen</label>
                                <textarea wire:model="komitmen" rows="2"
                                          class="w-full text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                          placeholder="Komitmen..."></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Rekomendasi Jenjang</label>
                                <textarea wire:model="rekomendasi_jenjang" rows="2"
                                          class="w-full text-sm rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                          placeholder="Rekomendasi jenjang..."></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button @click="$dispatch('close-modal', { name: 'note-modal' })"
                                        class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                                <button wire:click="saveNote"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">Simpan</button>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 mb-5">
                            <p class="text-xs text-gray-400 italic">Anda tidak berwenang memberi evaluasi untuk jabatan ini.</p>
                        </div>
                    @endif

                    {{-- Comments in form --}}
                    @if($existingNote)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4">
                            <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Komentar</h4>

                            @if(count($noteComments) > 0)
                                <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                                    @foreach($noteComments as $comment)
                                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $comment['user']['employee']['nama'] ?? 'User #'.$comment['user_id'] }}
                                                </span>
                                                <span class="flex items-center gap-2">
                                                    @if($canComment && !$replyToId)
                                                        <button wire:click="replyToComment({{ $comment['id'] }})"
                                                                class="text-[10px] font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                                            Balas
                                                        </button>
                                                    @endif
                                                    <span class="text-[10px] text-gray-400">
                                                        {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                                                    </span>
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $comment['komentar'] }}</p>

                                            @if($replyToId === $comment['id'])
                                                <div class="flex items-start gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                    <textarea wire:model="komentar" rows="1"
                                                              class="flex-1 text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                              placeholder="Tulis balasan..."></textarea>
                                                    <button wire:click="saveComment"
                                                            class="px-2.5 py-1 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shrink-0">Kirim</button>
                                                    <button wire:click="cancelReply"
                                                            class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors shrink-0">Batal</button>
                                                </div>
                                                @error('komentar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                            @endif

                                            @if(!empty($comment['replies']))
                                                <div class="mt-2 space-y-1.5 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                                    @foreach($comment['replies'] as $reply)
                                                        <div class="p-2 rounded-lg bg-white/50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50">
                                                            <div class="flex items-center justify-between mb-0.5">
                                                                <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400">
                                                                    {{ $reply['user']['employee']['nama'] ?? 'User #'.$reply['user_id'] }}
                                                                </span>
                                                                <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($reply['created_at'])->format('d/m/Y H:i') }}</span>
                                                            </div>
                                                            <p class="text-[11px] text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $reply['komentar'] }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic mb-4">Belum ada komentar.</p>
                            @endif

                            @if($canComment && !$replyToId)
                                <div class="flex items-start gap-2">
                                    <textarea wire:model="komentar" rows="2"
                                              class="flex-1 text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                              placeholder="Tulis komentar..."></textarea>
                                    <button wire:click="saveComment"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shrink-0">Kirim</button>
                                </div>
                                @error('komentar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    @endif
                @endif

                {{-- HISTORY VIEW --}}
                @if($viewState === 'history')
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6A2.25 2.25 0 016 3.75h1.5m9 0h-9"/></svg>
                            <span>{{ count($notesHistory) }} evaluasi tersimpan</span>
                            </span>
                        </div>
                        @if($isSuperior)
                            <button wire:click="switchToForm"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-xs font-medium transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Tambah Evaluasi Baru
                            </button>
                        @endif
                    </div>

                    @if(count($notesHistory) > 0)
                        <div class="overflow-x-auto mb-5">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-2 pr-4 font-semibold text-gray-500">Periode</th>
                                        <th class="text-left py-2 pr-4 font-semibold text-gray-500">Pemberi</th>
                                        <th class="text-left py-2 pr-4 font-semibold text-gray-500">Evaluasi</th>
                                        <th class="text-right py-2 font-semibold text-gray-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notesHistory as $note)
                                        @php
                                            $bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$note['bulan'] - 1] ?? $note['bulan'];
                                            $creator = $note['creator']['employee']['nama'] ?? $note['from_position']['nama'] ?? '-';
                                        @endphp
                                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                            <td class="py-2.5 pr-4 text-gray-900 dark:text-gray-100 font-medium">{{ $bulanNama }} {{ $note['tahun'] }}</td>
                                            <td class="py-2.5 pr-4 text-gray-500">{{ $creator }}</td>
                                            <td class="py-2.5 pr-4 text-gray-600 dark:text-gray-400 max-w-[200px] truncate">{{ $note['evaluasi'] ?: '-' }}</td>
                                            <td class="py-2.5 text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <button wire:click="showDetail({{ $note['id'] }})"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-600 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                        Lihat Detail
                                                    </button>
                                                    @if((auth()->id() ?? null) === ($note['created_by'] ?? null))
                                                        <button wire:click="deleteNote({{ $note['id'] }})"
                                                                 wire:confirm="Yakin ingin menghapus evaluasi ini?"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium transition-colors">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 mb-5">
                            <p class="text-xs text-gray-400 italic py-4 text-center">Belum ada riwayat evaluasi.</p>
                        </div>
                    @endif
                @endif

                {{-- DETAIL VIEW --}}
                @if($viewState === 'detail' && $noteDetail)
                    {{-- Back button --}}
                    <button wire:click="backToHistory"
                            class="flex items-center gap-1 text-xs font-medium text-primary-600 hover:text-primary-700 mb-4 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                        Kembali ke Riwayat
                    </button>

                    {{-- Detail content --}}
                    <div class="space-y-3 mb-5">
                        @php
                            $bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$noteDetail['bulan'] - 1] ?? $noteDetail['bulan'];
                            $creator = $noteDetail['creator']['employee']['nama'] ?? $noteDetail['from_position']['nama'] ?? '-';
                        @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-500">{{ $bulanNama }} {{ $noteDetail['tahun'] }} — oleh {{ $creator }}</span>
                            @if((auth()->id() ?? null) === ($noteDetail['created_by'] ?? null))
                                <button wire:click="deleteNote({{ $noteDetail['id'] }})"
                                        wire:confirm="Yakin ingin menghapus evaluasi ini?"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-red-200 dark:border-red-800 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    Hapus
                                </button>
                            @endif
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 space-y-3">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Situasi</label>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $noteDetail['situasi'] ?: '(tidak ada)' }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Catatan</label>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $noteDetail['evaluasi'] ?: '(tidak ada)' }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Komitmen</label>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $noteDetail['komitmen'] ?: '(tidak ada)' }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-500 mb-1">Rekomendasi Jenjang</label>
                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $noteDetail['rekomendasi_jenjang'] ?: '(tidak ada)' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Comments --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Komentar</h4>

                        @if(count($noteComments) > 0)
                            <div class="space-y-2 mb-4 max-h-60 overflow-y-auto">
                                @foreach($noteComments as $comment)
                                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                {{ $comment['user']['employee']['nama'] ?? 'User #'.$comment['user_id'] }}
                                            </span>
                                            <span class="flex items-center gap-2">
                                                @if($canComment && !$replyToId)
                                                    <button wire:click="replyToComment({{ $comment['id'] }})"
                                                            class="text-[10px] font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                                        Balas
                                                    </button>
                                                @endif
                                                <span class="text-[10px] text-gray-400">
                                                    {{ \Carbon\Carbon::parse($comment['created_at'])->format('d/m/Y H:i') }}
                                                </span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $comment['komentar'] }}</p>

                                        {{-- Reply form inline --}}
                                        @if($replyToId === $comment['id'])
                                            <div class="flex items-start gap-2 mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                                <textarea wire:model="komentar" rows="1"
                                                          class="flex-1 text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                          placeholder="Tulis balasan..."></textarea>
                                                <button wire:click="saveComment"
                                                        class="px-2.5 py-1 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shrink-0">
                                                    Kirim
                                                </button>
                                                <button wire:click="cancelReply"
                                                        class="px-2.5 py-1 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors shrink-0">
                                                    Batal
                                                </button>
                                            </div>
                                            @error('komentar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        @endif

                                        {{-- Replies --}}
                                        @if(!empty($comment['replies']))
                                            <div class="mt-2 space-y-1.5 pl-3 border-l-2 border-gray-200 dark:border-gray-600">
                                                @foreach($comment['replies'] as $reply)
                                                    <div class="p-2 rounded-lg bg-white/50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50">
                                                        <div class="flex items-center justify-between mb-0.5">
                                                            <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400">
                                                                {{ $reply['user']['employee']['nama'] ?? 'User #'.$reply['user_id'] }}
                                                            </span>
                                                            <span class="text-[10px] text-gray-400">
                                                                {{ \Carbon\Carbon::parse($reply['created_at'])->format('d/m/Y H:i') }}
                                                            </span>
                                                        </div>
                                                        <p class="text-[11px] text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $reply['komentar'] }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic mb-4">Belum ada komentar.</p>
                        @endif

                        {{-- Add comment --}}
                        @if($canComment && !$replyToId)
                            <div class="flex items-start gap-2">
                                <textarea wire:model="komentar" rows="2"
                                          class="flex-1 text-xs rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                          placeholder="Tulis komentar..."></textarea>
                                <button wire:click="saveComment"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors shrink-0">
                                    Kirim
                                </button>
                            </div>
                            @error('komentar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>
                @endif

            @else
                <div class="py-8 text-center text-xs text-gray-400">Pilih jabatan untuk melihat evaluasi.</div>
            @endif
        </div>
    </x-modal>

    <script>
        function strukturOrganisasi() {
            return {
                focusedId: @json($myPositionId) || null,
                canGiveNotes: @json($canGiveNotesByPosition),
                myPositionId: @json($myPositionId),
                positions: @json($flatPositions),
                get focused() {
                    return this.focusedId ? this.positions[this.focusedId] : null;
                },
                get parent() {
                    if (!this.focused) return null;
                    return this.focused.parent_id ? this.positions[this.focused.parent_id] : null;
                },
                get children() {
                    if (!this.focusedId) return [];
                    return Object.values(this.positions).filter(p => p.parent_id === this.focusedId);
                }
            }
        }
    </script>
</div>
