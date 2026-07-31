@push('topbar-left')
    <div>
        <h1 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pengaturan Akun</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola informasi profil dan keamanan akun Anda</p>
    </div>
@endpush

<x-app-layout title="Pengaturan">


    @if(auth()->user()->isStaff())
    {{-- Staff layout: profile full-width, then info & password side by side --}}
    <div class="space-y-6">
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-violet-500 text-white font-bold text-3xl shadow-lg shadow-primary-200 overflow-hidden">
                    @php $fotoUrl = Auth::user()->employee?->foto_url; @endphp
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="text-center sm:text-left">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->employee?->position ?? Auth::user()->email }}</p>
                    <span class="badge-success mt-2 inline-flex">Akun Aktif</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="card p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
    @else
    {{-- Non-staff layout: profile full-width, then info & pin side by side, password full-width --}}
    <div class="space-y-6">
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-violet-500 text-white font-bold text-3xl shadow-lg shadow-primary-200 overflow-hidden">
                    @php $fotoUrl = Auth::user()->employee?->foto_url; @endphp
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="text-center sm:text-left">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Auth::user()->employee?->position ?? Auth::user()->email }}</p>
                    <span class="badge-success mt-2 inline-flex">Akun Aktif</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="card p-6">
                @include('profile.partials.update-pin-form')
            </div>
        </div>

        <div class="card p-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>
    @endif

    @if(session('pin_success'))
    <div x-data="{ open: true }" x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click="open = false">
        <div @click.stop class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 p-8 shadow-2xl my-10">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-4">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center mb-2">{{ session('pin_success') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">PIN Persetujuan Anda telah berhasil diproses.</p>
            <div class="flex items-center justify-center pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="open = false" class="btn-primary text-xs px-8">Tutup</button>
            </div>
        </div>
    </div>
    @endif

</x-app-layout>


