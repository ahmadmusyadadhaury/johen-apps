@props(['menu' => []])

@php
$items = collect($menu['items']);
$grouped = $items->groupBy(fn ($item) => $item['group'] ?? '__flat__');
$hasGroups = $grouped->keys()->filter(fn ($group) => $group !== '__flat__')->count() > 0;

$activeGroup = null;
if ($hasGroups) {
    foreach ($items as $item) {
        if (($item['group'] ?? null) && \App\Support\DivisionMenu::isActive($item, request())) {
            $activeGroup = $item['group'];
            break;
        }
    }
}

$groupIcons = [
    'Operasional' => 'report',
    'SDM' => 'users',
];
@endphp

<div class="mb-3">
    <a href="{{ route('dashboard') }}" class="group flex items-center justify-center gap-2.5 rounded-xl px-3 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-violet-600 hover:from-primary-700 hover:to-violet-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"/></svg>
        <span>Kembali ke Dashboard Anda</span>
    </a>
</div>

<p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">{{ $menu['label'] }}</p>

<div x-data="{ openGroup: @js($activeGroup) }" class="space-y-0.5">
    @foreach($grouped as $group => $groupItems)
        @if($group === '__flat__')
            @foreach($groupItems as $item)
                @php $isActive = \App\Support\DivisionMenu::isActive($item, request()); @endphp
                <a href="{{ route($item['route'], $item['params']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        @else
        <div>
            <button @click="openGroup = openGroup === @js($group) ? null : @js($group)" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openGroup === @js($group) ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                <span class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! \App\Support\DivisionMenu::icon($groupIcons[$group] ?? 'default') !!}</svg>
                    {{ $group }}
                </span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openGroup === @js($group) }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div x-show="openGroup === @js($group)" x-cloak
                 x-transition:enter="transition-all ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition-all ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="ml-2 mt-1 space-y-0.5">
                @foreach($groupItems as $item)
                    @php $isActive = \App\Support\DivisionMenu::isActive($item, request()); @endphp
                    <a href="{{ route($item['route'], $item['params']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
</div>

@if(auth()->user()->isManager() && auth()->user()->employee)
<div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
    <a href="{{ route('hris.informasi-saya') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.informasi-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        Informasi Saya
    </a>
</div>
@endif