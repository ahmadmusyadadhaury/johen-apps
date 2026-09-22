@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 truncate">Content Plan</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Perencanaan konten creative</p>
    </div>
@endpush

<div>
    @if(session('message'))
    <div class="mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-400">
        {{ session('message') }}
    </div>
    @endif

    @php
        $tabs = ['Desain Grafis', 'Content Creator', 'Video Animator'];
        $user = auth()->user();
        $isKoordinator = $user->isKoordinatorCreative();
        $userPosition = $user->employee?->mainPosition()?->nama;
        $isCC = $activeTab === 'Content Creator';
    @endphp

    <div class="card mb-4">
        <div class="border-b border-gray-50 dark:border-gray-800">
            <nav class="flex gap-1 px-4 sm:px-6" aria-label="Tabs">
                @foreach($tabs as $tab)
                    @if($isKoordinator || $userPosition === $tab)
                    <button wire:click="switchTab('{{ $tab }}')"
                            class="px-4 py-3 text-xs font-medium transition-all duration-200 border-b-2 -mb-px {{ $activeTab === $tab ? 'border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        {{ $tab }}
                    </button>
                    @endif
                @endforeach
            </nav>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-4 sm:px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $activeTab }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar content plan {{ $activeTab }}</p>
            </div>
            @if($isKoordinator)
            <button wire:click="openNew" class="btn-primary text-xs py-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Tambah Content Plan
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            @if($isCC)
            <table class="w-full text-xs">
                <thead>
                    <tr class="table-header">
                        <th class="px-3 py-3 text-center w-10">No</th>
                        <th class="px-3 py-3">Take</th>
                        <th class="px-3 py-3">Post</th>
                        <th class="px-3 py-3">Days</th>
                        <th class="px-3 py-3">Waktu</th>
                        <th class="px-3 py-3">Akun</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">PIC Utama</th>
                        <th class="px-3 py-3">Topic</th>
                        <th class="px-3 py-3">Creator</th>
                        <th class="px-3 py-3">Goals Content</th>
                        <th class="px-3 py-3">Content Pillar</th>
                        <th class="px-3 py-3">Type of Content</th>
                        <th class="px-3 py-3">Reference</th>
                        <th class="px-3 py-3">Storyline</th>
                        <th class="px-3 py-3">Caption</th>
                        <th class="px-3 py-3">Revisi</th>
                        <th class="px-3 py-3 text-center">ACC</th>
                        @if($isKoordinator)
                        <th class="px-3 py-3 text-center w-20">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-3 py-3 text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-3 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $item->take ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->post ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->days ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->waktu ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->akun ?: '-' }}</td>
                            <td class="px-3 py-3">
                                @php $label = $item->status ? ucfirst($item->status) : '-'; @endphp
                                @if($item->status === 'post')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Post</span>
                                @elseif($item->status === 'hold')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Hold</span>
                                @elseif($item->status === 'revisi')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Revisi</span>
                                @elseif($item->status === 'editing')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Editing</span>
                                @elseif($item->status === 'scripting')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Scripting</span>
                                @elseif($item->status === 'concept')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Concept</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->pic_utama ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->topic ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->creator ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->goals_content ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->content_pillar ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->type_of_content ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->reference_content ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->storyline ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->caption ?: '-' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-400">{{ $item->revisi ?: '-' }}</td>
                            <td class="px-3 py-3 text-center">
                                @if($item->acc_to_posting)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            @if($isKoordinator)
                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openReport({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors" title="Report">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                        Report
                                    </button>
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus content plan ini?" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isKoordinator ? 19 : 18 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                    <p class="font-medium">Belum ada content plan</p>
                                    <p class="text-xs mt-1">Klik "Tambah Content Plan" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @elseif($activeTab === 'Desain Grafis')
            <table class="w-full text-xs">
                <thead>
                    <tr class="table-header">
                        <th class="px-2 py-3 text-center w-10">No</th>
                        <th class="px-2 py-3">Create</th>
                        <th class="px-2 py-3">Post</th>
                        <th class="px-2 py-3">Days</th>
                        <th class="px-2 py-3">Waktu</th>
                        <th class="px-2 py-3">Divisi</th>
                        <th class="px-2 py-3">Akun</th>
                        <th class="px-2 py-3">Status</th>
                        <th class="px-2 py-3">PIC</th>
                        <th class="px-2 py-3">Topic</th>
                        <th class="px-2 py-3">Type of Content</th>
                        <th class="px-2 py-3">Caption</th>
                        @if($isKoordinator)
                        <th class="px-4 sm:px-6 py-3 text-center w-24">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-2 py-3 text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-2 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $item->create ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->post ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->days ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->waktu ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->divisi ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->akun ?: '-' }}</td>
                            <td class="px-2 py-3">
                                @if($item->status === 'post')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Post</span>
                                @elseif($item->status === 'hold')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Hold</span>
                                @elseif($item->status === 'revisi')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Revisi</span>
                                @elseif($item->status === 'editing')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Editing</span>
                                @elseif($item->status === 'scripting')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Scripting</span>
                                @elseif($item->status === 'concept')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Concept</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->pic ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400 max-w-[100px] truncate">{{ $item->topic ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->type_of_content ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->caption ?: '-' }}</td>
                            @if($isKoordinator)
                            <td class="px-2 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openReport({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors" title="Report">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                        Report
                                    </button>
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus content plan ini?" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isKoordinator ? 13 : 12 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                    <p class="font-medium">Belum ada content plan</p>
                                    <p class="text-xs mt-1">Klik "Tambah Content Plan" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @elseif($activeTab === 'Video Animator')
            <table class="w-full text-xs">
                <thead>
                    <tr class="table-header">
                        <th class="px-2 py-3 text-center w-10">No</th>
                        <th class="px-2 py-3">Take</th>
                        <th class="px-2 py-3">VO</th>
                        <th class="px-2 py-3">Post</th>
                        <th class="px-2 py-3">Days</th>
                        <th class="px-2 py-3">Waktu</th>
                        <th class="px-2 py-3">Akun</th>
                        <th class="px-2 py-3">Status</th>
                        <th class="px-2 py-3">PIC</th>
                        <th class="px-2 py-3">Topic</th>
                        <th class="px-2 py-3">Script</th>
                        <th class="px-2 py-3">Caption</th>
                        <th class="px-2 py-3">Revisi</th>
                        <th class="px-2 py-3 text-center">ACC</th>
                        @if($isKoordinator)
                        <th class="px-2 py-3 text-center w-24">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-2 py-3 text-center text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-2 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $item->take ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->vo ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->post ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->days ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->waktu ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->akun ?: '-' }}</td>
                            <td class="px-2 py-3">
                                @if($item->status === 'post')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Post</span>
                                @elseif($item->status === 'hold')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Hold</span>
                                @elseif($item->status === 'ready')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400">Ready</span>
                                @elseif($item->status === 'revisi')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Revisi</span>
                                @elseif($item->status === 'editing')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Editing</span>
                                @elseif($item->status === 'scripting')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Scripting</span>
                                @elseif($item->status === 'concept')
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">Concept</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->pic ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400 max-w-[100px] truncate">{{ $item->topic ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->script ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400 max-w-[120px] truncate">{{ $item->caption ?: '-' }}</td>
                            <td class="px-2 py-3 text-gray-600 dark:text-gray-400">{{ $item->revisi ?: '-' }}</td>
                            <td class="px-2 py-3 text-center">
                                @if($item->acc_to_posting)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            @if($isKoordinator)
                            <td class="px-2 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button wire:click="openReport({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition-colors" title="Report">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                                        Report
                                    </button>
                                    <button wire:click="openEdit({{ $item->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $item->id }})" wire:confirm="Hapus content plan ini?" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isKoordinator ? 15 : 14 }}" class="px-6 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                    <p class="font-medium">Belum ada content plan</p>
                                    <p class="text-xs mt-1">Klik "Tambah Content Plan" untuk menambahkan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>

        @if($items->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-50 dark:border-gray-800">
            {{ $items->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
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
             @click.stop class="relative w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">

            @if($isCC)
            {{-- Content Creator Form --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Content Plan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} content plan Content Creator</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <input type="hidden" wire:model="posisi">

                <div class="grid grid-cols-5 gap-3">
                    <div>
                        <x-input-label value="Take" />
                        <x-text-input type="text" wire:model="take" class="mt-1 block w-full" placeholder="Take" />
                        @error('take') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Post" />
                        <x-text-input type="text" wire:model="post" class="mt-1 block w-full" placeholder="Post" />
                        @error('post') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Days" />
                        <x-text-input type="text" wire:model="days" class="mt-1 block w-full" placeholder="Days" />
                        @error('days') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Waktu" />
                        <x-text-input type="time" wire:model="waktu" class="mt-1 block w-full" />
                        @error('waktu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Akun" />
                        <x-text-input type="text" wire:model="akun" class="mt-1 block w-full" placeholder="Akun" />
                        @error('akun') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Status *" />
                        <select wire:model="status" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="concept">Concept</option>
                            <option value="scripting">Scripting</option>
                            <option value="editing">Editing</option>
                            <option value="revisi">Revisi</option>
                            <option value="post">Post</option>
                            <option value="hold">Hold</option>
                        </select>
                        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="PIC Utama" />
                        <select wire:model="pic_utama" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih PIC --</option>
                            <option value="Rizki">Rizki</option>
                            <option value="Rizki & Rangga">Rizki & Rangga</option>
                            <option value="Rangga">Rangga</option>
                            <option value="Adel">Adel</option>
                        </select>
                        @error('pic_utama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Goals Content" />
                        <select wire:model="goals_content" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Goals --</option>
                            <option value="Awareness">Awareness</option>
                            <option value="Consideration">Consideration</option>
                            <option value="Conversion">Conversion</option>
                        </select>
                        @error('goals_content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Content Pillar" />
                        <select wire:model="content_pillar" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Pillar --</option>
                            <option value="Education">Education</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Trust/Proof">Trust/Proof</option>
                            <option value="Engagement">Engagement</option>
                            <option value="Promotional">Promotional</option>
                        </select>
                        @error('content_pillar') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Type of Content" />
                        <select wire:model="type_of_content" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Type --</option>
                            <option value="Feeds">Feeds</option>
                            <option value="Reels & Tiktok">Reels & Tiktok</option>
                            <option value="Story">Story</option>
                            <option value="Carousel">Carousel</option>
                            <option value="Tiktok">Tiktok</option>
                        </select>
                        @error('type_of_content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Creator" />
                        <x-text-input type="text" wire:model="creator" class="mt-1 block w-full" placeholder="Creator" />
                        @error('creator') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label value="Topic" />
                    <x-text-input type="text" wire:model="topic" class="mt-1 block w-full" placeholder="Topic konten" />
                    @error('topic') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Reference Content" />
                    <x-text-input type="url" wire:model="reference_content" class="mt-1 block w-full" placeholder="https://..." />
                    @error('reference_content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Storyline" />
                    <textarea wire:model="storyline" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Storyline..."></textarea>
                    @error('storyline') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Caption" />
                    <textarea wire:model="caption" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Caption..."></textarea>
                    @error('caption') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Revisi" />
                        <x-text-input type="text" wire:model="revisi" class="mt-1 block w-full" placeholder="Revisi" />
                        @error('revisi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Link / URL" />
                        <x-text-input type="url" wire:model="link" class="mt-1 block w-full" placeholder="https://..." />
                        @error('link') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Deadline" />
                        <x-text-input type="date" wire:model="deadline" class="mt-1 block w-full" />
                        @error('deadline') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="acc_to_posting" id="acc_to_posting" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="acc_to_posting" class="text-sm font-medium text-gray-700 dark:text-gray-300">ACC to POSTING</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>

            @elseif($activeTab === 'Desain Grafis')
            {{-- Desain Grafis Form --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Content Plan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} content plan {{ $activeTab }}</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <input type="hidden" wire:model="posisi">

                <div class="grid grid-cols-4 gap-3">
                    <div>
                        <x-input-label value="Create" />
                        <x-text-input type="text" wire:model="create" class="mt-1 block w-full" placeholder="Create" />
                        @error('create') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Post" />
                        <x-text-input type="text" wire:model="post" class="mt-1 block w-full" placeholder="Post" />
                        @error('post') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Days" />
                        <x-text-input type="text" wire:model="days" class="mt-1 block w-full" placeholder="Days" />
                        @error('days') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Waktu" />
                        <x-text-input type="text" wire:model="waktu" class="mt-1 block w-full" placeholder="Waktu" />
                        @error('waktu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Divisi" />
                        <select wire:model="divisi" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih Divisi --</option>
                            <option value="Johen PUBG">Johen PUBG</option>
                            <option value="MLBB">MLBB</option>
                            <option value="Roblox">Roblox</option>
                            <option value="Monkey PUBG">Monkey PUBG</option>
                            <option value="FF">FF</option>
                            <option value="Valorant">Valorant</option>
                            <option value="Football">Football</option>
                            <option value="Info Game">Info Game</option>
                        </select>
                        @error('divisi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Akun" />
                        <x-text-input type="text" wire:model="akun" class="mt-1 block w-full" placeholder="Akun" />
                        @error('akun') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Status" />
                        <select wire:model="status" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="concept">Concept</option>
                            <option value="scripting">Scripting</option>
                            <option value="editing">Editing</option>
                            <option value="revisi">Revisi</option>
                            <option value="post">Post</option>
                            <option value="hold">Hold</option>
                        </select>
                        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="PIC" />
                        <select wire:model="pic" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih PIC --</option>
                            <option value="Fathir">Fathir</option>
                            <option value="Irfan">Irfan</option>
                        </select>
                        @error('pic') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Topic" />
                        <x-text-input type="text" wire:model="topic" class="mt-1 block w-full" placeholder="Topic" />
                        @error('topic') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Type of Content" />
                        <x-text-input type="text" wire:model="type_of_content" class="mt-1 block w-full" placeholder="Type of Content" />
                        @error('type_of_content') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label value="Caption" />
                    <textarea wire:model="caption" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Caption..."></textarea>
                    @error('caption') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Link" />
                        <x-text-input type="url" wire:model="link" class="mt-1 block w-full" placeholder="https://..." />
                        @error('link') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Deadline" />
                        <x-text-input type="date" wire:model="deadline" class="mt-1 block w-full" />
                        @error('deadline') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
            @else
            {{-- Video Animator Form --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $editId ? 'Edit' : 'Tambah' }} Content Plan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $editId ? 'Perbarui' : 'Isi' }} content plan {{ $activeTab }}</p>
                </div>
                <button wire:click="close" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <input type="hidden" wire:model="posisi">

                <div class="grid grid-cols-4 gap-3">
                    <div>
                        <x-input-label value="Take" />
                        <x-text-input type="text" wire:model="take" class="mt-1 block w-full" placeholder="Tanggal" />
                        @error('take') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="VO" />
                        <select wire:model="vo" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih VO --</option>
                            <option value="Rafly">Rafly</option>
                            <option value="Dhika">Dhika</option>
                            <option value="Reva">Reva</option>
                            <option value="Maul">Maul</option>
                            <option value="Yogi">Yogi</option>
                            <option value="Picky">Picky</option>
                        </select>
                        @error('vo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Post" />
                        <x-text-input type="text" wire:model="post" class="mt-1 block w-full" placeholder="Post" />
                        @error('post') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Days" />
                        <x-text-input type="text" wire:model="days" class="mt-1 block w-full" placeholder="Days" />
                        @error('days') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="Waktu" />
                        <x-text-input type="text" wire:model="waktu" class="mt-1 block w-full" placeholder="Waktu" />
                        @error('waktu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Akun" />
                        <x-text-input type="text" wire:model="akun" class="mt-1 block w-full" placeholder="Akun" />
                        @error('akun') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Status" />
                        <select wire:model="status" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="concept">Concept</option>
                            <option value="scripting">Scripting</option>
                            <option value="editing">Editing</option>
                            <option value="revisi">Revisi</option>
                            <option value="ready">Ready</option>
                            <option value="post">Post</option>
                        </select>
                        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <x-input-label value="PIC" />
                        <select wire:model="pic" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200">
                            <option value="">-- Pilih PIC --</option>
                            <option value="Picky">Picky</option>
                        </select>
                        @error('pic') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Topic" />
                        <x-text-input type="text" wire:model="topic" class="mt-1 block w-full" placeholder="Topic" />
                        @error('topic') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Revisi" />
                        <x-text-input type="text" wire:model="revisi" class="mt-1 block w-full" placeholder="Revisi" />
                        @error('revisi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <x-input-label value="Script" />
                    <textarea wire:model="script" rows="3" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Script..."></textarea>
                    @error('script') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input-label value="Caption" />
                    <textarea wire:model="caption" rows="2" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 outline-none transition-all duration-200" placeholder="Caption..."></textarea>
                    @error('caption') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="acc_to_posting" id="acc_to_posting" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="acc_to_posting" class="text-sm font-medium text-gray-700 dark:text-gray-300">ACC to POSTING</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="close" class="btn-secondary text-xs">Batal</button>
                    <button type="submit" class="btn-primary text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $editId ? 'Perbarui' : 'Simpan' }}
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- Report Modal --}}
    <div wire:ignore.self class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-data="{ open: false }"
         x-init="$watch('$wire.showReportModal', value => open = value)"
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
             @click.stop class="relative w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-800 p-6 sm:p-8 shadow-2xl my-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Reporting Content</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Data performa konten per minggu</p>
                </div>
                <button wire:click="closeReport" class="rounded-xl p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @php
                $plan = $items->firstWhere('id', $reportContentPlanId);
                $reports = $plan ? $plan->reports->keyBy('week') : collect();
                $maxWeek = $reports->keys()->max() ?: 0;
            @endphp

            @if($plan)
            <div class="mb-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $plan->take ?: $plan->create ?: $plan->judul ?: '-' }} — {{ $plan->akun ?: $plan->vo ?: '-' }}</p>
            </div>

            <div class="overflow-x-auto mb-4">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="table-header">
                            <th class="px-3 py-2.5 text-center w-16">Week</th>
                            <th class="px-3 py-2.5 text-right">Views</th>
                            <th class="px-3 py-2.5 text-right">Likes</th>
                            <th class="px-3 py-2.5 text-right">Comment</th>
                            <th class="px-3 py-2.5 text-right">Save</th>
                            <th class="px-3 py-2.5 text-right">Share</th>
                            <th class="px-3 py-2.5 text-right">Followers</th>
                            @if($isKoordinator)
                            <th class="px-3 py-2.5 text-center w-24">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse(range(1, max($maxWeek, 1)) as $w)
                            @php $r = $reports->get($w); @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-3 py-2.5 text-center font-semibold text-gray-700 dark:text-gray-300">Week {{ $w }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->views) : '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->likes) : '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->comment) : '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->save) : '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->share) : '-' }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 dark:text-gray-100 font-medium">{{ $r ? number_format($r->followers) : '-' }}</td>
                                @if($isKoordinator)
                                <td class="px-3 py-2.5 text-center">
                                    @if($r)
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="openEditReport({{ $r->id }})" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            Edit
                                        </button>
                                        <button wire:click="deleteReport({{ $r->id }})" wire:confirm="Hapus report week {{ $w }}?" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-[10px] font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            Hapus
                                        </button>
                                    </div>
                                    @else
                                    <span class="text-gray-300 dark:text-gray-600 italic text-[10px]">Belum ada data</span>
                                    @endif
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isKoordinator ? 8 : 7 }}" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada data report</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($isKoordinator)
            @if(!$showReportForm)
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                <button wire:click="openNewReport" class="btn-primary text-xs py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Tambah Data Report
                </button>
            </div>
            @endif

            @if($showReportForm)
            <form wire:submit.prevent="saveReport" class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ $reportEditId ? 'Edit' : 'Tambah' }} Report</h4>
                    <button type="button" wire:click="$set('showReportForm', false)" class="text-[10px] text-gray-400 hover:text-gray-600">Batal</button>
                </div>

                <div class="grid grid-cols-7 gap-2">
                    <div>
                        <x-input-label value="Week" />
                        <x-text-input type="number" wire:model="reportWeek" class="mt-1 block w-full" min="1" max="52" />
                        @error('reportWeek') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Views" />
                        <x-text-input type="number" wire:model="reportViews" class="mt-1 block w-full" min="0" />
                        @error('reportViews') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Likes" />
                        <x-text-input type="number" wire:model="reportLikes" class="mt-1 block w-full" min="0" />
                        @error('reportLikes') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Comment" />
                        <x-text-input type="number" wire:model="reportComment" class="mt-1 block w-full" min="0" />
                        @error('reportComment') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Save" />
                        <x-text-input type="number" wire:model="reportSave" class="mt-1 block w-full" min="0" />
                        @error('reportSave') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Share" />
                        <x-text-input type="number" wire:model="reportShare" class="mt-1 block w-full" min="0" />
                        @error('reportShare') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-input-label value="Followers" />
                        <x-text-input type="number" wire:model="reportFollowers" class="mt-1 block w-full" min="0" />
                        @error('reportFollowers') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button type="submit" class="btn-primary text-xs py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ $reportEditId ? 'Perbarui' : 'Simpan' }} Report
                    </button>
                </div>
            </form>
            @endif
            @endif
            @else
            <div class="py-8 text-center text-sm text-gray-400">Content plan tidak ditemukan.</div>
            @endif
        </div>
    </div>
</div>