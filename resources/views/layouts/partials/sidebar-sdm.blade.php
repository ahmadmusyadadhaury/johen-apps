                    @can('view-all')
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'sdm' ? null : 'sdm'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'sdm' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                SDM
                                <livewire:sidebar-slip-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'sdm' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'sdm'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <a href="{{ route('hris.employees.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.employees.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                {{ auth()->user()->pegawaiLabel('Karyawan') }}
                            </a>
                            <a href="{{ route('hris.divisions.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.divisions.*') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Divisi
                            </a>
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isGmCeo() || auth()->user()->isStaffHr())
                            <a href="{{ route('kelola-jabatan') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('kelola-jabatan') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Jabatan
                            </a>
                            @endif
                            @unless(auth()->user()->isAnyKoordinator() || auth()->user()->isManager())
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.kontrak-kerja', 'hris.kontrak-kerja.evaluasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.kontrak-kerja') }}" class="flex flex-1 items-center gap-3">Kontrak Kerja</a>
                                <livewire:sidebar-kontrak-badge />
                            </div>
                            @endunless
                            <a href="{{ route('hris.freelance') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.freelance') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Freelance
                            </a>
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.struktur-organisasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.struktur-organisasi') }}" class="flex-1">Struktur Organisasi</a>
                                <livewire:sidebar-position-note-badge />
                            </div>
                            @if(auth()->user()->employee)
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.informasi-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.informasi-saya') }}" class="flex-1">Informasi saya</a>
                                <livewire:sidebar-slip-badge />
                            </div>
                            @endif
                            <a href="{{ route('assets.index', ['mine' => 1]) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.index') && request()->boolean('mine') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Asset Saya
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="mt-4">
                        <button @click="openMenu = openMenu === 'sdm' ? null : 'sdm'" class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200" :class="openMenu === 'sdm' ? 'text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'">
                            <span class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                SDM
                                <livewire:sidebar-slip-badge />
                            </span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'sdm' }" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="openMenu === 'sdm'" x-cloak
                             x-transition:enter="transition-all ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition-all ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="ml-2 mt-1 space-y-0.5">
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.struktur-organisasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.struktur-organisasi') }}" class="flex-1">Struktur Organisasi</a>
                                <livewire:sidebar-position-note-badge />
                            </div>
                            @if(auth()->user()->employee)
                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('hris.informasi-saya') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                <a href="{{ route('hris.informasi-saya') }}" class="flex-1">Informasi saya</a>
                                <livewire:sidebar-slip-badge />
                            </div>
                            @endif
                            <a href="{{ route('assets.index', ['mine' => 1]) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('assets.index') && request()->boolean('mine') ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                Asset Saya
                            </a>
                        </div>
                    </div>
                    @endcan
