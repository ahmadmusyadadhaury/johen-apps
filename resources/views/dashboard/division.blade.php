@push('topbar-left')
    <div>
        <h1 class="text-base sm:text-lg font-display font-bold text-gray-900 dark:text-gray-100 truncate">Divisi {{ $division->nama }}</h1>
        <p class="hidden sm:block text-xs text-gray-400 mt-0.5">Menu dan informasi divisi</p>
    </div>
@endpush

<x-app-layout title="Divisi {{ $division->nama }}">

    <div>
        {{-- Welcome Header --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-violet-700 p-6 sm:p-8 mb-6">
            <div class="absolute top-0 right-0 w-96 h-96 opacity-5">
                <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="100" cy="100" r="100" fill="white"/><circle cx="180" cy="50" r="30" fill="white"/><circle cx="30" cy="160" r="20" fill="white"/></svg>
            </div>
            <div class="relative">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm text-white shadow-lg ring-2 ring-white/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-display font-bold text-white">{{ $division->nama }} Workspace</h1>
                        <p class="text-sm text-white/80 mt-0.5">Divisi {{ $division->nama }}</p>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-3">
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                        <span class="text-xs font-semibold text-white/80">Karyawan</span>
                        <span class="text-sm font-bold text-white">{{ $division->employees_count }}</span>
                    </div>
                    @if($division->koordinator)
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                        <span class="text-xs font-semibold text-white/80">Koordinator</span>
                        <span class="text-sm font-bold text-white">{{ $division->koordinator }}</span>
                    </div>
                    @endif
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 backdrop-blur-sm px-3 py-1.5">
                        <span class="text-xs font-semibold text-white/80">Menu</span>
                        <span class="text-sm font-bold text-white">{{ count($menu['items']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Menu Grid --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-4 sm:p-5 mb-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm font-display font-bold text-gray-900 dark:text-gray-100">{{ $menu['label'] }}</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Menu yang tersedia untuk divisi ini</p>
                </div>
            </div>
            @php $groupedMenu = collect($menu['items'])->groupBy(fn ($item) => $item['group'] ?? '__flat__'); @endphp
            @foreach($groupedMenu as $group => $groupItems)
                @if($group !== '__flat__')
                <p class="text-[10px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">{{ $group }}</p>
                @endif
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 {{ $groupIsLast = ($loop->last) ? 'mb-0' : 'mb-3' }}">
                    @foreach($groupItems as $item)
                    <a href="{{ route($item['route'], $item['params']) }}" class="group flex items-center gap-2.5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 px-3 py-2.5 hover:border-primary-200 dark:hover:border-primary-800 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all duration-300">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-sm group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! \App\Support\DivisionMenu::icon($item['icon']) !!}</svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate">{{ $item['label'] }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">{{ $item['desc'] }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Division Employees --}}
        @if($employees->count() > 0)
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">Karyawan {{ $division->nama }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Daftar karyawan di divisi ini</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($employees as $emp)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-bold text-xs shrink-0 overflow-hidden">
                        @if($emp->foto_url)
                            <img src="{{ $emp->foto_url }}" alt="{{ $emp->nama }}" class="w-full h-full object-cover">
                        @else
                            {{ substr($emp->nama, 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $emp->nama }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $emp->position ?? '-' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</x-app-layout>