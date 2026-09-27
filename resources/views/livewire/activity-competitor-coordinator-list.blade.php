<div>
    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Pilih koordinator untuk melihat Activity Competitor</p>

    @php($allEmpty = $hos1Coordinators->isEmpty() && $hos2Coordinators->isEmpty() && $generalCoordinators->isEmpty())
    @if($allEmpty)
        <div class="rounded-2xl border border-gray-100 bg-white px-4 py-12 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Koordinator</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ditemukan koordinator di bawah Anda</p>
        </div>
    @else
        @foreach(['hos1' => ['Head of Store 1', 'blue', $hos1Coordinators], 'hos2' => ['Head of Store 2', 'purple', $hos2Coordinators], 'general' => ['Koordinator', 'emerald', $generalCoordinators]] as $key => [$title, $color, $coordinators])
            @if($coordinators->isNotEmpty())
                <section class="mb-6">
                    <h2 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        <span class="h-2 w-2 rounded-full {{ ['hos1' => 'bg-blue-500', 'hos2' => 'bg-purple-500', 'general' => 'bg-emerald-500'][$key] }}"></span>{{ $title }}
                    </h2>
                    <div class="divide-y divide-gray-50 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:divide-gray-800 dark:border-gray-800 dark:bg-gray-900">
                        @foreach($coordinators as $coordinator)
                            @php($positionName = $coordinator->positions->first(fn ($position) => (bool) $position->pivot?->is_main)?->nama ?? '')
                            <a href="{{ route('hris.activity-competitor.show', $coordinator->id) }}" class="group flex items-center gap-4 px-5 py-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                                    @if($coordinator->foto_url)
                                        <img src="{{ $coordinator->foto_url }}" alt="Foto {{ $coordinator->nama }}" class="h-full w-full object-cover" loading="lazy">
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Foto tidak tersedia"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-gray-900 transition-colors group-hover:text-primary-600 dark:text-gray-100 dark:group-hover:text-primary-400">{{ $coordinator->nama }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $positionName }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $activityCounts[$coordinator->id] ?? 0 }} Data</span>
                                <svg class="h-5 w-5 shrink-0 text-gray-400 transition-colors group-hover:text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    @endif
</div>
