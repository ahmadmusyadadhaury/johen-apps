<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih karyawan untuk melihat Weekly Plan Report</p>
        </div>
    </div>

    @php
        $allEmpty = $hos1Coordinators->isEmpty() && $hos2Coordinators->isEmpty() && $generalCoordinators->isEmpty();

        function getCoordinatorNameForEmployee($emp) {
            $positions = $emp->positions;
            foreach ($positions as $position) {
                $current = $position;
                while ($current && $current->parent_id) {
                    $current = \App\Models\Position::find($current->parent_id);
                    if ($current && str_contains(strtolower($current->nama), 'koordinator')) {
                        return $current->nama;
                    }
                }
            }
            return '';
        }
    @endphp

    @if($allEmpty)
    <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-4 py-12 text-center">
            <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Belum Ada Karyawan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ditemukan karyawan di bawah Anda</p>
        </div>
    </div>
    @else

    @if($hos1Coordinators->isNotEmpty())
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            Head of Store 1
        </h2>
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($hos1Coordinators as $emp)
            @php
                $wprCount = \App\Models\WeeklyPlanReport::where('employee_id', $emp->id)->count();
                $coordinatorName = getCoordinatorNameForEmployee($emp);
            @endphp
            <a href="{{ route('hris.weekly-report.show', $emp->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr($emp->nama, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $emp->nama }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $coordinatorName }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                        {{ $wprCount }} WPR
                    </span>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-primary-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($hos2Coordinators->isNotEmpty())
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
            Head of Store 2
        </h2>
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($hos2Coordinators as $emp)
            @php
                $wprCount = \App\Models\WeeklyPlanReport::where('employee_id', $emp->id)->count();
                $coordinatorName = getCoordinatorNameForEmployee($emp);
            @endphp
            <a href="{{ route('hris.weekly-report.show', $emp->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr($emp->nama, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $emp->nama }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $coordinatorName }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                        {{ $wprCount }} WPR
                    </span>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-primary-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if($generalCoordinators->isNotEmpty())
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Koordinator
        </h2>
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden divide-y divide-gray-50 dark:divide-gray-800">
            @foreach($generalCoordinators as $emp)
            @php
                $wprCount = \App\Models\WeeklyPlanReport::where('employee_id', $emp->id)->count();
                $coordinatorName = getCoordinatorNameForEmployee($emp);
            @endphp
            <a href="{{ route('hris.weekly-report.show', $emp->id) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                    {{ strtoupper(substr($emp->nama, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $emp->nama }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $coordinatorName }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                        {{ $wprCount }} WPR
                    </span>
                </div>
                <div class="flex-shrink-0 text-gray-400 group-hover:text-primary-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
