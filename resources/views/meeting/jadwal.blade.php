@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jadwal Meeting</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kalender jadwal meeting</p>
    </div>
@endpush

@php
    $idMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $idDays = ['monday'=>'Senin','tuesday'=>'Selasa','wednesday'=>'Rabu','thursday'=>'Kamis','friday'=>'Jumat','saturday'=>'Sabtu','sunday'=>'Minggu'];

    $statusLabels = ['booked'=>'Di Booking','ongoing'=>'Berlangsung','queue'=>'Antrian','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
    $statusDot = ['booked'=>'bg-blue-500','ongoing'=>'bg-yellow-500','queue'=>'bg-orange-500','completed'=>'bg-emerald-500','cancelled'=>'bg-red-500'];
    $statusChip = [
        'booked' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border-blue-500',
        'ongoing' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-500',
        'queue' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300 border-orange-500',
        'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-500',
        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border-red-500',
    ];
    $recurringChip = 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border-purple-500';
    $statusClasses = ['booked'=>'text-blue-600 dark:text-blue-400','ongoing'=>'text-yellow-600 dark:text-yellow-400','queue'=>'text-orange-600 dark:text-orange-400','completed'=>'text-emerald-600 dark:text-emerald-400','cancelled'=>'text-red-600 dark:text-red-400'];

    $baseUrl = route('meeting.jadwal');
    $monthTitle = $idMonths[$month - 1] . ' ' . $year;

    $meetingsArray = $meetings->map(fn($m) => [
        'id' => $m->id,
        'title' => $m->title,
        'room' => $m->room,
        'team' => $m->team ?? '-',
        'date' => $m->date ? $m->date->format('Y-m-d') : null,
        'start_time' => $m->start_time ? \Carbon\Carbon::parse($m->start_time)->format('H:i') : '--:--',
        'end_time' => $m->end_time ? \Carbon\Carbon::parse($m->end_time)->format('H:i') : '--:--',
        'actual_end_time' => $m->actual_end_time ? $m->actual_end_time->format('H:i') . ' WIB' : '-',
        'status' => $m->display_status ?? $m->status,
        'status_label' => $statusLabels[$m->display_status] ?? $statusLabels[$m->status] ?? $m->status,
        'status_class' => $statusClasses[$m->display_status] ?? $statusClasses[$m->status] ?? 'text-gray-600 dark:text-gray-400',
        'description' => $m->description ?? '-',
        'recurring_type' => $m->recurring_type,
        'recurring_day' => $m->recurring_day,
        'creator' => $m->requested_by_name ?? $m->creator?->name ?? '-',
    ])->values()->toArray();

    $meetingsJson = json_encode($meetingsArray);
@endphp

<x-app-layout title="Jadwal Meeting">
    <div x-data="calendarApp()" x-cloak class="grid grid-cols-1 md:grid-cols-[240px_minmax(0,1fr)] lg:grid-cols-[280px_minmax(0,1fr)] items-stretch gap-4 xl:gap-5">

        {{-- ══════════════════ SIDEBAR ══════════════════ --}}
        <aside class="space-y-4 xl:space-y-5">

            {{-- Card 1: Meeting Berulang --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <div class="space-y-3">
                    @forelse($recurring->sortBy('start_time')->take(3) as $rm)
                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 p-4">
                            <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 text-purple-700 ring-1 ring-purple-600/20 px-2 py-0.5 text-[10px] font-semibold dark:bg-purple-950/40 dark:text-purple-300 dark:ring-purple-500/30">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                Mingguan Berulang
                            </span>
                            <p class="mt-2.5 text-sm font-bold text-gray-900 dark:text-gray-100 leading-snug">{{ $rm->title }}</p>
                            <div class="mt-3 space-y-2 text-xs text-gray-500 dark:text-gray-400">
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    <span>Jadwal:</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">Setiap {{ $rm->recurring_day ? ($idDays[strtolower($rm->recurring_day)] ?? 'Minggu') : 'Minggu' }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Jam:</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($rm->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($rm->end_time)->format('H:i') }}</span>
                                </p>
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                    <span>Lokasi:</span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ $rm->room }}</span>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 mb-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Belum ada meeting berulang</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Card 2: Mini Calendar --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <button type="button" @click="miniPrev()" class="flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    </button>
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100" x-text="miniLabel"></p>
                    <button type="button" @click="miniNext()" class="flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-0.5 mb-1">
                    <template x-for="(d, i) in ['M','S','S','R','K','J','S']" :key="i">
                        <div class="py-1 text-center text-[10px] font-semibold uppercase text-gray-400 dark:text-gray-500" x-text="d"></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-0.5">
                    <template x-for="(cell, i) in miniCells" :key="i">
                        <button type="button" @click="selectDay(cell)" :class="cellClass(cell)" class="relative h-7 w-full rounded-lg text-[11px] font-medium transition-colors">
                            <span x-text="cell.getDate()"></span>
                            <span x-show="hasMeeting(cell)"
                                  class="absolute bottom-1 left-1/2 h-1 w-1 -translate-x-1/2 rounded-full"
                                  :class="fmtDate(cell) === fmtDate(cursor) ? 'bg-white' : 'bg-blue-500'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Card 3: Legenda --}}
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100">Legenda</h2>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500">Status meeting</p>
                    </div>
                </div>

                <ul class="space-y-2.5 text-xs text-gray-600 dark:text-gray-300">
                    <li class="flex items-center gap-2.5"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Di Booking</li>
                    <li class="flex items-center gap-2.5"><span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span> Berlangsung</li>
                    <li class="flex items-center gap-2.5"><span class="h-2.5 w-2.5 rounded-full bg-orange-500"></span> Antrian</li>
                    <li class="flex items-center gap-2.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Selesai</li>
                    <li class="flex items-center gap-2.5"><span class="h-2.5 w-2.5 rounded-full bg-purple-500"></span> Meeting Mingguan</li>
                </ul>
            </div>
        </aside>

        {{-- ══════════════════ KALENDER UTAMA ══════════════════ --}}
        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

            {{-- Header Kalender --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4 lg:px-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2 min-w-0">
                    @if($view === 'week')
                        <a href="{{ route('meeting.jadwal', ['view' => 'week', 'date' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        </a>
                    @elseif($view === 'day')
                        <a href="{{ route('meeting.jadwal', ['view' => 'day', 'date' => $focus->copy()->subDay()->format('Y-m-d')]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        </a>
                    @else
                        <a href="{{ route('meeting.jadwal', ['month' => $month - 1, 'year' => $month == 1 ? $year - 1 : $year, 'view' => $view]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        </a>
                    @endif

                    <h2 class="px-1 text-base lg:text-lg font-bold text-gray-900 dark:text-gray-100 truncate" x-text="headerTitle"></h2>

                    @if($view === 'week')
                        <a href="{{ route('meeting.jadwal', ['view' => 'week', 'date' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    @elseif($view === 'day')
                        <a href="{{ route('meeting.jadwal', ['view' => 'day', 'date' => $focus->copy()->addDay()->format('Y-m-d')]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    @else
                        <a href="{{ route('meeting.jadwal', ['month' => $month + 1, 'year' => $month == 12 ? $year + 1 : $year, 'view' => $view]) }}"
                           class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    @endif
                </div>

                {{-- View switcher --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="goToday()"
                            class="whitespace-nowrap rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3.5 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:shadow active:scale-95">
                        Hari Ini
                    </button>
                    <div class="flex items-center gap-1 rounded-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 p-1 overflow-x-auto">
                        @if($isAdvancedView)
                        <button type="button" @click="setMode('week')"
                                :class="btnClass('week')"
                                class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all duration-200 hover:bg-gray-200/70 dark:hover:bg-gray-600/70 active:scale-95">
                            Minggu
                        </button>
                        @endif
                        <button type="button" @click="setMode('month')"
                                :class="btnClass('month')"
                                class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all duration-200 hover:bg-gray-200/70 dark:hover:bg-gray-600/70 active:scale-95">
                            Bulan
                        </button>
                        @if($isAdvancedView)
                        <button type="button" @click="setMode('day')"
                                :class="btnClass('day')"
                                class="whitespace-nowrap rounded-full px-3.5 py-1.5 text-xs font-semibold transition-all duration-200 hover:bg-gray-200/70 dark:hover:bg-gray-600/70 active:scale-95">
                            Hari
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Month View --}}
            <div x-show="mode === 'month'">
                @php
                    $startOfMonth = \Carbon\Carbon::create($year, $month)->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::create($year, $month)->endOfMonth();
                    $startOfCalendar = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $endOfCalendar = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                @endphp

                <div class="grid grid-cols-7 border-t border-l border-gray-100 dark:border-gray-800">
                    @foreach($days as $day)
                        <div class="px-2 py-2.5 text-[11px] font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 text-center border-r border-b border-gray-100 dark:border-gray-800">{{ $day }}</div>
                    @endforeach

                    @for($date = $startOfCalendar; $date <= $endOfCalendar; $date->addDay())
                        @php
                            $isToday = $date->isToday();
                            $isCurrentMonth = $date->month === $month;
                            $dayMeetings = $meetings->filter(function ($m) use ($date) {
                                if ($m->recurring_day) {
                                    return strtolower($date->englishDayOfWeek) === strtolower($m->recurring_day);
                                }
                                return $m->date && $m->date->isSameDay($date);
                            });
                        @endphp
                        <div class="min-h-[112px] px-1.5 pt-2 pb-1 border-r border-b border-gray-100 dark:border-gray-800 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-900' : 'bg-gray-50/70 dark:bg-gray-950/50' }} {{ $isToday ? 'bg-blue-50/70 dark:bg-blue-950/30' : '' }}">
                            <div class="flex items-center justify-center h-6 w-6 mx-auto mb-1.5 {{ $isToday ? 'text-blue-600 dark:text-blue-400 font-bold' : ($isCurrentMonth ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400 dark:text-gray-600') }}">
                                {{ $date->day }}
                            </div>
                            <div class="space-y-1">
                                @foreach($dayMeetings->take(3) as $meeting)
                                    <div @click="$dispatch('open-detail', { id: {{ is_numeric($meeting->id) ? $meeting->id : json_encode($meeting->id) }} })"
                                         class="flex items-center gap-1 rounded-md border-l-2 px-1.5 py-1 text-[10px] font-medium cursor-pointer transition-all hover:shadow-sm hover:scale-[1.01] {{ $meeting->recurring_type ? $recurringChip : ($statusChip[$meeting->display_status ?? $meeting->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 border-gray-400 dark:text-gray-300') }}">
                                        <span class="shrink-0 font-semibold">{{ $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time)->format('H:i') : '--:--' }}</span>
                                        <span class="truncate">{{ $meeting->recurring_type ? '⟳ ' : '' }}{{ $meeting->title }}</span>
                                    </div>
                                @endforeach
                                @if($dayMeetings->count() > 3)
                                    <p class="text-[10px] text-gray-400 pl-1.5">+{{ $dayMeetings->count() - 3 }} lainnya</p>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Week View (time grid) --}}
            @if($isAdvancedView)
            <div x-show="mode === 'week'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-3 sm:p-4 lg:p-5">
                <style>
                    .week-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
                    .week-scroll::-webkit-scrollbar-thumb { background: rgba(148,163,184,.4); border-radius: 9999px; }
                    .week-scroll::-webkit-scrollbar-thumb:hover { background: rgba(148,163,184,.6); }
                    .week-scroll::-webkit-scrollbar-track { background: transparent; }
                    .week-scroll { scrollbar-width: thin; scrollbar-color: rgba(148,163,184,.4) transparent; }
                </style>
                @php
                    $slotH = 60; // px per hour
                    $gridHeight = 24 * $slotH;
                    $hoursList = [];
                    for ($hi = 0; $hi < 24; $hi++) { $hoursList[] = sprintf('%02d:00', $hi); }

                    $weekDates = collect();
                    for ($wd = $weekStart->copy(); $wd <= $weekEnd; $wd->addDay()) { $weekDates->push($wd->copy()); }

                    $weekColumns = [];
                    foreach ($weekDates as $wd) {
                        $dayKey = $wd->isoFormat('YYYY-MM-DD');
                        $dayEvents = [];
                        foreach ($meetings as $m) {
                            $occur = $m->recurring_day
                                ? strtolower($wd->englishDayOfWeek) === strtolower($m->recurring_day)
                                : ($m->date ? $m->date->isSameDay($wd) : false);
                            if (!$occur) continue;
                            $start = $m->start_time ? \Carbon\Carbon::parse($m->start_time) : null;
                            $end = $m->end_time ? \Carbon\Carbon::parse($m->end_time) : null;
                            $mins = $start ? ($start->hour * 60 + $start->minute) : 0;
                            $dur = ($start && $end) ? (($end->hour * 60 + $end->minute) - $mins) : 60;
                            $top = min(max($mins, 0), $gridHeight - 44);
                            $dur = min(max($dur, 44), $gridHeight - $top);
                            $dayEvents[] = [
                                'm' => $m,
                                'top' => $top,
                                'height' => $dur,
                            ];
                        }
                        usort($dayEvents, fn($a, $b) => $a['top'] <=> $b['top']);
                        $weekColumns[$dayKey] = $dayEvents;
                    }
                @endphp

                {{-- Fixed weekday header --}}
                <div class="flex border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-t-xl overflow-hidden">
                    <div class="w-14 shrink-0 border-r border-gray-200 dark:border-gray-700"></div>
                    @foreach($weekDates as $wd)
                        @php $today = $wd->isToday(); @endphp
                        <div class="flex-1 min-w-0 px-1 py-2 text-center transition-colors duration-300 {{ $today ? 'bg-blue-50 dark:bg-blue-950/30' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                                {{ strtoupper(substr($idDays[strtolower($wd->englishDayOfWeek)], 0, 3)) }}
                            </p>
                            <p class="text-sm sm:text-base font-bold leading-tight mt-0.5 {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $wd->day }}
                            </p>
                            <p class="text-[10px] mt-0.5 {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $wd->format('n/j') }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Scrollable time grid --}}
                <div class="week-scroll max-h-[58vh] overflow-y-auto border-x border-b border-gray-200 dark:border-gray-800 rounded-b-xl bg-white dark:bg-gray-900">
                    <div class="flex" style="height: {{ $gridHeight }}px;">
                        {{-- hour rail --}}
                        <div class="relative w-14 shrink-0 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                            @foreach($hoursList as $hi => $h)
                                <span class="absolute right-2 text-[10px] font-medium text-gray-400 dark:text-gray-500" style="transform: translateY(-50%); top: {{ $hi * $slotH }}px;">
                                    {{ $h }}
                                </span>
                            @endforeach
                        </div>

                        {{-- day columns --}}
                        @foreach($weekDates as $i => $wd)
                            @php $dayKey = $wd->isoFormat('YYYY-MM-DD'); $colEvents = $weekColumns[$dayKey] ?? []; $today = $wd->isToday(); @endphp
                            <div class="relative flex-1 {{ $i > 0 ? 'border-l border-gray-200 dark:border-gray-700' : '' }} {{ $today ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}" style="height: {{ $gridHeight }}px;">
                                @for($hi = 0; $hi <= 24; $hi++)
                                    <div class="absolute left-0 right-0 border-t border-gray-100 dark:border-gray-800" style="top: {{ $hi * $slotH }}px;"></div>
                                @endfor
                                @foreach($colEvents as $ev)
                                    @php
                                        $m = $ev['m'];
                                        $top = $ev['top'];
                                        $hgt = $ev['height'];
                                        $showSub = $hgt >= 84;
                                        $cardClass = $m->recurring_type
                                            ? ($recurringChip . ' border-l-2')
                                            : (($statusChip[$m->display_status ?? $m->status] ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 border-blue-500') . ' border-l-2');
                                    @endphp
                                    <div @click="$dispatch('open-detail', { id: {{ is_numeric($m->id) ? $m->id : json_encode($m->id) }} })"
                                         class="absolute left-1 right-1 rounded-lg p-1.5 shadow-sm cursor-pointer overflow-hidden transition-all duration-200 hover:shadow-md hover:z-10 {{ $cardClass }}"
                                         style="top: {{ $top }}px; height: {{ $hgt }}px;">
                                        <span class="block text-[9px] font-bold leading-tight opacity-90">
                                            {{ $m->start_time ? \Carbon\Carbon::parse($m->start_time)->format('H:i') : '--:--' }}{{ $m->end_time ? '–' . \Carbon\Carbon::parse($m->end_time)->format('H:i') : '' }}
                                        </span>
                                        <span class="block text-[10px] font-bold leading-tight truncate mt-0.5">{{ $m->recurring_type ? '⟳ ' : '' }}{{ $m->title }}</span>
                                        @if($showSub)
                                            <span class="block text-[9px] leading-tight mt-1 truncate opacity-80">{{ $m->room ?: ($m->team ?? '') }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Day View (time grid, single day) --}}
            <div x-show="mode === 'day'" class="p-3 sm:p-4 lg:p-5">
                @php
                    $dayMeetings = $meetings->filter(function ($m) use ($focus) {
                        if ($m->recurring_day) return strtolower($focus->englishDayOfWeek) === strtolower($m->recurring_day);
                        return $m->date && $m->date->isSameDay($focus);
                    })->sortBy('start_time');

                    $dayEvents = [];
                    foreach ($dayMeetings as $m) {
                        $start = $m->start_time ? \Carbon\Carbon::parse($m->start_time) : null;
                        $end = $m->end_time ? \Carbon\Carbon::parse($m->end_time) : null;
                        $mins = $start ? ($start->hour * 60 + $start->minute) : 0;
                        $dur = ($start && $end) ? (($end->hour * 60 + $end->minute) - $mins) : 60;
                        $top = min(max($mins, 0), $gridHeight - 44);
                        $dur = min(max($dur, 44), $gridHeight - $top);
                        $dayEvents[] = [
                            'm' => $m,
                            'top' => $top,
                            'height' => $dur,
                        ];
                    }
                    usort($dayEvents, fn($a, $b) => $a['top'] <=> $b['top']);
                    $today = $focus->isToday();
                @endphp

                {{-- Fixed day header --}}
                <div class="flex border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-t-xl overflow-hidden">
                    <div class="w-14 shrink-0 border-r border-gray-200 dark:border-gray-700"></div>
                    <div class="flex-1 min-w-0 px-1 py-2 text-center transition-colors duration-300 {{ $today ? 'bg-blue-50 dark:bg-blue-950/30' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                        <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-widest {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ strtoupper(substr($idDays[strtolower($focus->englishDayOfWeek)], 0, 3)) }}
                        </p>
                        <p class="text-sm sm:text-base font-bold leading-tight mt-0.5 {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100' }}">
                            {{ $focus->day }}
                        </p>
                        <p class="text-[10px] mt-0.5 {{ $today ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $focus->format('n/j') }}</p>
                    </div>
                </div>

                {{-- Scrollable time grid --}}
                <div class="week-scroll max-h-[58vh] overflow-y-auto border-x border-b border-gray-200 dark:border-gray-800 rounded-b-xl bg-white dark:bg-gray-900">
                    <div class="flex" style="height: {{ $gridHeight }}px;">
                        {{-- hour rail --}}
                        <div class="relative w-14 shrink-0 border-r border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                            @foreach($hoursList as $hi => $h)
                                <span class="absolute right-2 text-[10px] font-medium text-gray-400 dark:text-gray-500" style="transform: translateY(-50%); top: {{ $hi * $slotH }}px;">
                                    {{ $h }}
                                </span>
                            @endforeach
                        </div>

                        {{-- single day column --}}
                        <div class="relative flex-1 {{ $today ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}" style="height: {{ $gridHeight }}px;">
                            @for($hi = 0; $hi <= 24; $hi++)
                                <div class="absolute left-0 right-0 border-t border-gray-100 dark:border-gray-800" style="top: {{ $hi * $slotH }}px;"></div>
                            @endfor
                            @forelse($dayEvents as $ev)
                                @php
                                    $m = $ev['m'];
                                    $top = $ev['top'];
                                    $hgt = $ev['height'];
                                    $showSub = $hgt >= 84;
                                    $cardClass = $m->recurring_type
                                        ? ($recurringChip . ' border-l-2')
                                        : (($statusChip[$m->display_status ?? $m->status] ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200 border-blue-500') . ' border-l-2');
                                @endphp
                                <div @click="$dispatch('open-detail', { id: {{ is_numeric($m->id) ? $m->id : json_encode($m->id) }} })"
                                     class="absolute left-1 right-1 rounded-lg p-1.5 shadow-sm cursor-pointer overflow-hidden transition-all duration-200 hover:shadow-md hover:z-10 {{ $cardClass }}"
                                     style="top: {{ $top }}px; height: {{ $hgt }}px;">
                                    <span class="block text-[9px] font-bold leading-tight opacity-90">
                                        {{ $m->start_time ? \Carbon\Carbon::parse($m->start_time)->format('H:i') : '--:--' }}{{ $m->end_time ? '–' . \Carbon\Carbon::parse($m->end_time)->format('H:i') : '' }}
                                    </span>
                                    <span class="block text-[10px] font-bold leading-tight truncate mt-0.5">{{ $m->recurring_type ? '⟳ ' : '' }}{{ $m->title }}</span>
                                    @if($showSub)
                                        <span class="block text-[9px] leading-tight mt-1 truncate opacity-80">{{ $m->room ?: ($m->team ?? '') }}</span>
                                    @endif
                                </div>
                            @empty
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 mb-4">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">Belum Ada Meeting</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal meeting pada hari ini</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Meeting Detail Modal --}}
        <div x-data="{ open: false, meeting: null }"
             @open-detail.window="
                open = true;
                meeting = {{ $meetingsJson }}.find(m => m.id === $event.detail.id)
             "
             x-transition:enter.opacity.duration.200ms
             x-show="open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/50 backdrop-blur-sm"
             @click="open = false">
            <div @click.stop x-transition:enter.opacity.scale.90 x-show="open"
                 class="relative w-full max-w-lg overflow-hidden rounded-2xl bg-white dark:bg-gray-900 shadow-2xl shadow-gray-900/20 ring-1 ring-gray-200 dark:ring-gray-800">
                <div class="relative px-6 pt-6 pb-5 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2" x-show="meeting?.recurring_type">
                                <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2 py-0.5 text-[11px] font-semibold text-purple-700 dark:bg-purple-950/50 dark:text-purple-300">
                                    ⟳ Meeting Mingguan
                                </span>
                            </div>
                            <h3 class="mt-1.5 text-xl font-bold text-gray-900 dark:text-gray-100 leading-snug" x-text="meeting?.title"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="meeting?.room ? 'Ruangan: ' + meeting.room : ''"></p>
                        </div>
                        <button @click="open = false"
                                class="shrink-0 rounded-xl p-2 text-gray-400 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5" x-show="meeting">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                            <p class="flex items-center gap-1.5 text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Tim</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="meeting?.team"></p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Jam Mulai</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="meeting?.start_time"></p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Jam Selesai (Est.)</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="meeting?.end_time"></p>
                        </div>
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Selesai Aktual</p>
                            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="meeting?.actual_end_time"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Status</p>
                            <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold" :class="meeting?.status_class">
                                <span class="h-1.5 w-1.5 rounded-full" :class="
                                    meeting?.status === 'completed' ? 'bg-emerald-500' :
                                    meeting?.status === 'cancelled' ? 'bg-red-500' :
                                    meeting?.status === 'booked' ? 'bg-blue-500' :
                                    (meeting?.status === 'ongoing' || meeting?.status === 'queue') ? 'bg-yellow-500' : 'bg-gray-400'
                                "></span>
                                <span x-text="meeting?.status_label"></span>
                            </span>
                        </div>
                        <div class="col-span-2" x-show="meeting?.description">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">Deskripsi</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300" x-text="meeting?.description"></p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40 px-6 py-4">
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Dibuat oleh <span class="font-medium text-gray-600 dark:text-gray-300" x-text="meeting?.creator"></span>
                    </p>
                    <button @click="open = false"
                            class="rounded-lg bg-gray-900 dark:bg-white px-4 py-2 text-xs font-semibold text-white dark:text-gray-900 transition-colors hover:bg-gray-700 dark:hover:bg-gray-200">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function calendarApp() {
            const DAYS_EN = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            const MONTHS_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            const pad = (n) => String(n).padStart(2, '0');
            const fmtDate = (d) => d && `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            const todayDate = () => { const n = new Date(); return new Date(n.getFullYear(), n.getMonth(), n.getDate()); };

            return {
                mode: @js($view),
                meetings: @js($meetingsArray),
                focus: @js($focus->format('Y-m-d')),
                cursor: null,
                mini: null,
                init() {
                    const [y, m, d] = this.focus.split('-').map(Number);
                    this.cursor = new Date(y, m - 1, d);
                    this.mini = { y, m: m - 1 };
                },

                // ---- helpers ----
                fmtDate: (d) => fmtDate(d),

                meetingsForDate(d) {
                    const iso = fmtDate(d);
                    const dayName = DAYS_EN[d.getDay()];
                    return this.meetings.filter(m => {
                        if (m.recurring_day) return m.recurring_day.toLowerCase() === dayName;
                        return m.date === iso;
                    });
                },

                // ---- titles ----
                get parsedFocus() {
                    const [y, m, d] = this.focus.split('-').map(Number);
                    return new Date(y, m - 1, d);
                },
                get headerTitle() {
                    if (this.mode === 'day') {
                        const f = this.parsedFocus;
                        return `${f.getDate()} ${MONTHS_ID[f.getMonth()]} ${f.getFullYear()}`;
                    }
                    if (this.mode === 'week') {
                        const f = this.parsedFocus;
                        const start = new Date(f);
                        start.setDate(start.getDate() - ((start.getDay() + 6) % 7));
                        const end = new Date(start);
                        end.setDate(end.getDate() + 6);
                        if (start.getMonth() === end.getMonth()) {
                            return `${MONTHS_ID[start.getMonth()]} ${start.getFullYear()}`;
                        }
                        return `${MONTHS_ID[start.getMonth()]} – ${MONTHS_ID[end.getMonth()]} ${end.getFullYear()}`;
                    }
                    return @js($monthTitle);
                },

                // ---- mini calendar ----
                get miniCells() {
                    const first = new Date(this.mini.y, this.mini.m, 1);
                    const start = new Date(first);
                    start.setDate(start.getDate() - start.getDay());
                    const cells = [];
                    for (let i = 0; i < 42; i++) {
                        const d = new Date(start);
                        d.setDate(d.getDate() + i);
                        cells.push(d);
                    }
                    return cells;
                },
                get miniLabel() { return `${MONTHS_ID[this.mini.m]} ${this.mini.y}`; },
                miniPrev() {
                    let m = this.mini.m - 1, y = this.mini.y;
                    if (m < 0) { m = 11; y--; }
                    this.mini = { m, y };
                },
                miniNext() {
                    let m = this.mini.m + 1, y = this.mini.y;
                    if (m > 11) { m = 0; y++; }
                    this.mini = { m, y };
                },
                hasMeeting(d) { return this.meetingsForDate(d).length > 0; },
                cellClass(d) {
                    const iso = fmtDate(d);
                    const sel = fmtDate(this.cursor);
                    const today = fmtDate(todayDate());
                    const inMonth = d.getMonth() === this.mini.m;
                    let cls = 'h-7 w-full rounded-lg text-[11px] font-medium transition-colors ';
                    if (iso === sel) {
                        cls += 'bg-blue-600 text-white shadow-sm ';
                    } else if (iso === today) {
                        cls += 'text-blue-600 font-bold ring-1 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 ';
                    } else if (inMonth) {
                        cls += 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 ';
                    } else {
                        cls += 'text-gray-400 dark:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 ';
                    }
                    return cls;
                },
                selectDay(d) {
                    const iso = fmtDate(d);
                    let url = '{{ $baseUrl }}?view=' + this.mode + '&date=' + iso + '&month=' + (d.getMonth() + 1) + '&year=' + d.getFullYear();
                    window.location.href = url;
                },

                // ---- navigation ----
                goToday() {
                    let url = '{{ $baseUrl }}?view=' + this.mode + '&month=' + {{ now()->month }} + '&year=' + {{ now()->year }};
                    if (this.mode === 'week' || this.mode === 'day') {
                        url += '&date=' + fmtDate(todayDate());
                    }
                    window.location.href = url;
                },
                setMode(m) {
                    let url = '{{ $baseUrl }}?view=' + m;
                    if (m === 'week' || m === 'day') {
                        url += '&date=' + fmtDate(todayDate());
                    } else {
                        url += '&month={{ $month }}&year={{ $year }}';
                    }
                    window.location.href = url;
                },
                btnClass(m) {
                    return m === this.mode
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
                },

                openMeeting(m) {
                    window.dispatchEvent(new CustomEvent('open-detail', { detail: { id: m.id } }));
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
