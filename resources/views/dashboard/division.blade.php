@push('topbar-left')
    <div>
        <h1 class="text-lg font-display font-bold text-gray-900 dark:text-gray-100">Divisi {{ $division->nama }}</h1>
        <p class="text-xs text-gray-400 mt-0.5">Menu dan informasi divisi</p>
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
                        <h1 class="text-lg sm:text-xl font-display font-bold text-white">{{ $division->nama }}</h1>
                        <p class="text-sm text-white/80 mt-0.5">{{ $division->deskripsi ?: 'Menu dan aktivitas divisi ' . $division->nama }}</p>
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
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-5 sm:p-6 mb-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-display font-bold text-gray-900 dark:text-gray-100">{{ $menu['label'] }}</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Menu yang tersedia untuk divisi ini</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($menu['items'] as $item)
                <a href="{{ route($item['route'], $item['params']) }}" class="group rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 p-5 hover:border-primary-200 dark:hover:border-primary-800 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 hover:shadow-md transition-all duration-300">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white shadow-md group-hover:scale-110 transition-transform duration-300 mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! \App\Support\DivisionMenu::icon($item['icon']) !!}</svg>
                    </div>
                    <p class="text-sm font-bold font-display text-gray-900 dark:text-gray-100">{{ $item['label'] }}</p>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">{{ $item['desc'] }}</p>
                    <div class="mt-3 flex items-center gap-1 text-[11px] font-medium text-primary-600 dark:text-primary-400 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span>Buka Menu</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
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
