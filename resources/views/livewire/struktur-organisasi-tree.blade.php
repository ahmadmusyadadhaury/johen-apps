<div x-data="{ collapsed: {{ $level > 2 ? 'true' : 'false' }} }" class="flex flex-col items-center">
    {{-- Card --}}
    <div class="relative" @click="focusedId = {{ $node['id'] }}">
        <div class="cursor-pointer w-64 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm text-center hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-md hover:scale-105 active:scale-95 transition-all duration-200">
            <div class="flex items-center justify-center mb-1.5">
                @if($node['employee'] && $node['employee']->foto_url)
                    <img src="{{ $node['employee']->foto_url }}" alt="{{ $node['employee']->nama }}"
                         class="w-8 h-8 rounded object-cover bg-gray-50 dark:bg-gray-700 shrink-0 shadow-sm">
                @else
                    <div class="flex h-8 w-8 items-center justify-center rounded text-white font-bold text-[13px] shrink-0 shadow-sm
                        {{ $level == 1 ? 'bg-emerald-500' : ($level == 2 ? 'bg-blue-500' : 'bg-purple-500') }}">
                        {{ strtoupper(substr($node['nama'], 0, 1)) }}
                    </div>
                @endif
            </div>
            <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 leading-snug">{{ $node['nama'] }}</p>
            @if($node['employee'])
                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 truncate">{{ $node['employee']->nama }}</p>
            @else
                <p class="text-[10px] text-gray-300 dark:text-gray-600 mt-1 italic">Kosong</p>
            @endif

            {{-- Note buttons --}}
            @php
                $canGive = $canGiveNotesByPosition[$node['id']] ?? false;
                $noteInfo = $notesByPosition->get($node['id']);
                $hasCurrentNote = $noteInfo && $noteInfo->has_current > 0;
            @endphp
            <div class="flex items-center justify-center gap-2 mt-2.5">
                @if($canGive)
                    <button wire:click.stop="openNoteModal({{ $node['id'] }}, 'history')"
                            class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-primary-600 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 text-[10px] font-medium transition-colors"
                            title="Tambah evaluasi">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Evaluasi
                    </button>
                    <button wire:click.stop="openNoteModal({{ $node['id'] }}, 'history')"
                            class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 text-[10px] font-medium transition-colors"
                            title="Riwayat evaluasi">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Evaluasi
                    </button>
                @endif
                @if($node['id'] == $myPositionId && !$canGive)
                    <button wire:click.stop="openNoteModal({{ $node['id'] }}, 'history')"
                            class="flex items-center gap-1 px-2.5 py-1 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 hover:border-primary-400 dark:hover:border-primary-500 text-[10px] font-medium transition-colors">
                        Lihat Evaluasi
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if(count($node['children']) > 0)
        <template x-if="!collapsed">
            <div>
                <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600 mx-auto"></div>

                <div class="relative flex items-start justify-center gap-6 sm:gap-10">
                    @if(count($node['children']) > 1)
                        <div class="absolute top-0 left-[8%] right-[8%] h-0.5 bg-gray-300 dark:bg-gray-600"></div>
                    @endif

                    @foreach($node['children'] as $child)
                        <div class="flex flex-col items-center shrink-0">
                            @if(count($node['children']) > 1)
                                <div class="w-0.5 h-6 bg-gray-300 dark:bg-gray-600"></div>
                            @else
                                <div class="h-2"></div>
                            @endif

                            @include('livewire.struktur-organisasi-tree', ['node' => $child, 'level' => $level + 1, 'notesByPosition' => $notesByPosition, 'myPositionId' => $myPositionId, 'canGiveNotesByPosition' => $canGiveNotesByPosition])
                        </div>
                    @endforeach
                </div>
            </div>
        </template>
    @endif
</div>
