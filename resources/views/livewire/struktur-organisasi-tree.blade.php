<div x-data="{ collapsed: {{ $level > 2 ? 'true' : 'false' }} }" class="flex flex-col items-center">
    {{-- Card --}}
    <div class="relative" @click="focusedId = {{ $node['id'] }}">
        @php $treeEmployees = $node['employees'] ?? collect(); @endphp
        @if($treeEmployees->count() > 1)
            <div class="flex items-start justify-center gap-3">
                @foreach($treeEmployees as $treeEmp)
                    @include('livewire.struktur-organisasi-tree-card', [
                        'cardNode' => $node,
                        'cardEmployee' => $treeEmp,
                        'level' => $level,
                        'notesByPosition' => $notesByPosition,
                        'myPositionId' => $myPositionId,
                        'canGiveNotesByPosition' => $canGiveNotesByPosition,
                    ])
                @endforeach
            </div>
        @else
            @include('livewire.struktur-organisasi-tree-card', [
                'cardNode' => $node,
                'cardEmployee' => $treeEmployees->first(),
                'level' => $level,
                'notesByPosition' => $notesByPosition,
                'myPositionId' => $myPositionId,
                'canGiveNotesByPosition' => $canGiveNotesByPosition,
            ])
        @endif
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
