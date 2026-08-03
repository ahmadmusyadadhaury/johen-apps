@props(['menu' => []])

<div class="mb-3">
    <a href="{{ route('dashboard') }}" class="group flex items-center justify-center gap-2.5 rounded-xl px-3 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-violet-600 hover:from-primary-700 hover:to-violet-700 shadow-sm transition-all duration-200">
        <svg class="w-4 h-4 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18"/></svg>
        <span>Kembali ke Dashboard Anda</span>
    </a>
</div>

<p class="px-3 py-2 text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">{{ $menu['label'] }}</p>

<div class="space-y-0.5">
    @foreach($menu['items'] as $item)
        @php $isActive = \App\Support\DivisionMenu::isActive($item, request()); @endphp
        <a href="{{ route($item['route'], $item['params']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! \App\Support\DivisionMenu::icon($item['icon']) !!}</svg>
            {{ $item['label'] }}
        </a>
    @endforeach
</div>
