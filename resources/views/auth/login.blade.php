<x-guest-layout>
    <div class="login-card p-8 sm:p-10">
        {{-- Logo --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center mb-5">
                <div x-data="logoAnimation()" class="relative cursor-pointer select-none"
                    @mouseenter="glowIntensity = 0.6"
                    @mouseleave="glowIntensity = 0.3">
                    {{-- Glow behind logo --}}
                    <div class="absolute inset-0 rounded-2xl transition-all duration-1000 ease-out"
                        x-bind:style="`background: radial-gradient(circle, rgba(9,135,245,${glowIntensity}), rgba(124,58,237,${glowIntensity * 0.6}), transparent 70%); filter: blur(12px); transform: scale(${1 + glowIntensity * 0.3})`">
                    </div>
                    {{-- Light sweep overlay --}}
                    <div class="absolute inset-0 rounded-2xl overflow-hidden">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/25 to-transparent"
                            x-bind:style="`transform: translateX(${sweepX}%); transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)`">
                        </div>
                    </div>
                    {{-- Logo dengan 3D flip --}}
                    <div class="relative transition-transform duration-700 ease-out"
                        x-bind:style="`transform: perspective(400px) rotateY(${flipAngle}deg)`">
                        <img src="{{ asset('logo.png') }}" alt="Johen Sukses Abadi" width="64" height="64" class="h-16 w-auto relative">
                    </div>
                </div>
            </div>
            <h1 class="text-2xl font-display font-bold text-white tracking-tight">Masuk</h1>
            <p class="text-sm text-gray-400 mt-1.5">Masuk ke akun Johen Anda</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5"
              x-data="{ loading: false, showForgotModal: false }"
              x-on:submit="loading = true; $nextTick(() => { $el.querySelector('button[type=submit]').disabled = true })">
            @csrf

            {{-- Username --}}
            <div class="login-stagger-1 motion-reduce:opacity-100">
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" spellcheck="false"
                        class="login-input pl-10"
                        placeholder="Masukkan username…">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div x-data="{ show: false }" class="login-stagger-2 motion-reduce:opacity-100">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                <div class="relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                        class="login-input pl-10 pr-11"
                        placeholder="Masukkan password…">
                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'" class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-500 hover:text-gray-300 transition-all rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
                        <svg x-show="!show" x-cloak class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                        <svg x-show="show" x-cloak class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember & Forgot --}}
            <div class="login-stagger-3 motion-reduce:opacity-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 sm:gap-0">
                <label for="remember_me" class="flex items-center gap-2 cursor-pointer select-none">
                    <input id="remember_me" type="checkbox" class="rounded-lg bg-white/5 border-white/20 text-primary-500 shadow-sm focus:ring-primary-500/50 transition-all duration-200 motion-reduce:transition-none" name="remember">
                    <span class="text-sm text-gray-400">Ingat saya</span>
                </label>
                <button type="button" @click="showForgotModal = true" class="text-sm font-medium text-primary-400 hover:text-primary-300 transition-colors">
                    Lupa password?
                </button>
            </div>

            {{-- Submit --}}
            <div class="login-stagger-4 motion-reduce:opacity-100">
                <button type="submit" class="login-btn" :disabled="loading" :class="{ 'loading': loading }">
                    <template x-if="!loading">
                        <svg class="w-4 h-4 relative z-10" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    </template>
                    <template x-if="loading">
                        <svg class="w-4 h-4 relative z-10 animate-spin motion-reduce:animate-none" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
                    </template>
                    <span class="relative z-10" x-text="loading ? 'Memproses…' : 'Masuk'"></span>
                </button>
            </div>

            {{-- Contact admin --}}
            <div class="login-stagger-5 motion-reduce:opacity-100">
                <p class="text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="https://wa.me/6285156521726" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary-400 hover:text-primary-300 transition-colors">Hubungi admin</a>
                </p>
            </div>
        </form>

        {{-- Forgot password modal --}}
        <template x-teleport="body">
            <div x-show="showForgotModal" x-cloak
                x-transition:enter="transition-all duration-200 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-all duration-150 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[999] flex items-center justify-center p-4"
                @click.self="showForgotModal = false">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
                {{-- Modal --}}
                <div x-show="showForgotModal"
                    x-transition:enter="transition-all duration-200 ease-out"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition-all duration-150 ease-in"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative w-full max-w-md rounded-2xl bg-[#141822] border border-white/10 p-8 shadow-2xl">
                    {{-- Icon --}}
                    <div class="mx-auto mb-5 w-14 h-14 rounded-full bg-primary-500/10 flex items-center justify-center">
                        <svg class="w-7 h-7 text-primary-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    </div>
                    <h3 class="text-lg font-display font-semibold text-white text-center mb-2">Lupa Password?</h3>
                    <p class="text-sm text-gray-400 text-center mb-6">
                        Silakan hubungi admin untuk mereset password Anda.
                    </p>
                    <div class="flex flex-col gap-3">
                        <a href="https://wa.me/6285156521726" target="_blank" rel="noopener noreferrer"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-semibold transition-all duration-200">
                            <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Hubungi Admin via WhatsApp
                        </a>
                        <button type="button" @click="showForgotModal = false"
                            class="w-full px-5 py-2.5 rounded-xl border border-white/10 hover:bg-white/5 text-gray-400 hover:text-white text-sm transition-all duration-200">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-guest-layout>
