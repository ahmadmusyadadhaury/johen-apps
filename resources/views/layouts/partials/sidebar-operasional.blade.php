                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'operasional' ? null : 'operasional'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'operasional' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                Operasional
                                <livewire:sidebar-operasional-badge />
                                <livewire:sidebar-birthday-dot />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'operasional' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'operasional'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('hris.absensi') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.absensi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Presensi
                            </a>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.cuti-izin') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.cuti-izin') }}" class="flex-1">Cuti dan Izin</a>
                                <livewire:sidebar-leave-badge />
                            </div>
                            <a href="{{ route('hris.jobdesk') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.jobdesk') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jobdesk saya
                            </a>
                            @if(!auth()->user()->isKoordinatorIt() && !auth()->user()->isStaffIt() && !auth()->user()->isHeadOfStore())
                            <a href="{{ route('it.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('it.tickets.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Ticketing IT
                            </a>
                            @endif
                            @if(auth()->user()->isStaff() || auth()->user()->isStaffAdmin() || auth()->user()->isKoordinatorIt() || auth()->user()->isKoordinatorAdmin() || auth()->user()->isKoordinatorPubg() || auth()->user()->isKoordinatorFf() || auth()->user()->isKoordinatorRoblox() || auth()->user()->isKoordinatorMonkeyPubg() || auth()->user()->isStaffIt() || auth()->user()->isStaffHostPubg() || auth()->user()->isStaffHostFf() || auth()->user()->isStaffHostRoblox() || auth()->user()->isStaffHostMonkeyPubg() || auth()->user()->isStaffHostFcMobile() || auth()->user()->isSuperAdminLike())
                            <a href="{{ route('hris.manual-book') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.manual-book') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pelatihan
                            </a>
                            @endif
                            @if((auth()->user()->isKoordinator() || auth()->user()->isManager() || auth()->user()->isKoordinatorCreative()) && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.weekly-report') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.weekly-report') }}" class="flex-1">Weekly Plan Report</a>
                                <livewire:sidebar-feedback-badge type="weekly" />
                            </div>
                            @endif
                            @if((auth()->user()->isKoordinator() || auth()->user()->isManager()) && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.activity-competitor') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.activity-competitor') }}" class="flex-1">Activity Competitor</a>
                                <livewire:sidebar-feedback-badge type="activity" />
                            </div>
                            @endif
                            @if(auth()->user()->isManager() && !auth()->user()->isHeadOfStore())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.daily-tracking', 'hris.daily-tracking.game') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.daily-tracking') }}" class="flex-1">Daily Tracking</a>
                                <livewire:sidebar-daily-tracking-badge />
                            </div>
                            @endif
                            @if(auth()->user()->isManager() && !auth()->user()->isHeadOfStore2())
                            <a href="{{ route('reimbursement') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('reimbursement') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Reimbursement
                            </a>
                            @endif
                            @if(auth()->user()->isSuperAdminLike())
                            <a href="{{ route('hris.announcements') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.announcements', 'hris.pengumuman-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengumuman
                            </a>
                            <a href="{{ route('hris.birthday-wishes') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.birthday-wishes*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Ucapan Ulang Tahun
                                <livewire:sidebar-birthday-badge />
                            </a>
                            @elseif(auth()->user()->canViewPengumuman())
                            <a href="{{ route('hris.pengumuman-saya') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.pengumuman-saya', 'hris.announcements') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengumuman
                            </a>
                            @endif
                            @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('hris.pengarsipan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.pengarsipan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengarsipan
                            </a>
                            @endif
                            @if(auth()->user()->isGmCeo())
                            <a href="{{ route('history.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('history.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Slip Gaji
                            </a>
                            @endif
                            @if((auth()->user()->isManager() || auth()->user()->isKoordinatorCreative() || (auth()->user()->isStaffCreative() && str_starts_with(auth()->user()->employee?->mainPosition()?->nama ?? '', 'Admin KOL')) || auth()->user()->isKoordinatorAdmin() || auth()->user()->isStaffAdmin()) && !auth()->user()->isHeadOfStore())
                            <a href="{{ route('hris.influencer-pengajuan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.influencer-pengajuan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Pengajuan Influencer
                            </a>
                            @endif
                        </div>
                    </div>
