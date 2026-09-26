<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="application-name" content="{{ config('app.name', 'Johen Sukses Abadi') }}">

        <title>{{ config('app.name', 'Johen Sukses Abadi') }}</title>

        @include('partials.pwa-head')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,700&display=swap" rel="stylesheet" />


        <script>
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @include('partials.pwa-splash')

        {{-- safe-t/safe-b/safe-x: keluar dari notch & gesture bar saat PWA standalone --}}
        <div class="relative min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden bg-gradient-to-br from-primary-50 via-white to-violet-50 dark:bg-none dark:bg-[#07080F] selection:bg-primary-500/20 safe-t safe-b safe-x">

            {{-- Animated background layer

                 Semua gerak di sini memakai CSS keyframe pada transform, bukan
                 requestAnimationFrame + x-bind:style. Animasi transform
                 dijalankan thread kompositor sehingga tidak memblokir event
                 keyboard - itulah yang bikin halaman login terasa lag saat
                 mengetik di Android. Gradient juga dipisah dari animasi:
                 kalau gradient ikut ditulis ulang tiap frame, browser memaksa
                 re-rasterisasi ulang tiap blur 80-100px. --}}
            <div x-data="starDust()" class="absolute inset-0 overflow-hidden" aria-hidden="true">
                {{-- Gradient mesh base (dark) --}}
                <div class="johen-mesh absolute opacity-40 dark:opacity-45 hidden dark:block"
                    style="inset: -25%; background: linear-gradient(135deg, #0987F5 0%, #7C3AED 25%, #1E1B4B 50%, #4C1D95 75%, #0987F5 100%);">
                </div>

                {{-- Gradient mesh base (light) --}}
                <div class="johen-mesh absolute opacity-60 dark:hidden"
                    style="inset: -25%; background: linear-gradient(135deg, #BFDFFE 0%, #E8E0FF 30%, #FFFFFF 55%, #D1C0FE 80%, #BFDFFE 100%);">
                </div>

                {{-- Aurora light ribbons (dark) --}}
                <div class="johen-drift-a absolute top-[10%] right-[5%] w-[500px] h-[200px] rounded-full opacity-40 dark:opacity-50 blur-[100px] hidden dark:block"
                    style="background: linear-gradient(90deg, rgba(9,135,245,0.5), rgba(124,58,237,0.3), transparent)"></div>

                <div class="johen-drift-b absolute bottom-[15%] left-[5%] w-[450px] h-[180px] rounded-full opacity-30 dark:opacity-40 blur-[100px] hidden dark:block"
                    style="background: linear-gradient(90deg, transparent, rgba(124,58,237,0.4), rgba(9,135,245,0.3))"></div>

                <div class="johen-drift-c absolute top-[40%] left-[60%] w-[400px] h-[150px] rounded-full opacity-25 dark:opacity-35 blur-[80px] hidden dark:block"
                    style="background: linear-gradient(90deg, rgba(9,135,245,0.3), rgba(168,85,247,0.2), transparent)"></div>

                {{-- Aurora light ribbons (light) --}}
                <div class="johen-drift-a absolute top-[5%] right-[8%] w-[480px] h-[180px] rounded-full opacity-60 blur-[90px] dark:hidden"
                    style="background: linear-gradient(90deg, rgba(9,135,245,0.20), rgba(124,58,237,0.14), transparent)"></div>

                <div class="johen-drift-b absolute bottom-[12%] left-[6%] w-[430px] h-[160px] rounded-full opacity-50 blur-[90px] dark:hidden"
                    style="background: linear-gradient(90deg, transparent, rgba(124,58,237,0.16), rgba(9,135,245,0.14))"></div>

                <div class="johen-drift-c absolute top-[42%] left-[58%] w-[380px] h-[140px] rounded-full opacity-45 blur-[80px] dark:hidden"
                    style="background: linear-gradient(90deg, rgba(9,135,245,0.15), rgba(168,85,237,0.12), transparent)"></div>

                {{-- Subtle light streaks (dark) --}}
                <div class="johen-drift-d absolute top-[25%] left-[20%] w-[300px] h-[1px] opacity-20 blur-[2px] hidden dark:block"
                    style="background: linear-gradient(90deg, transparent, rgba(9,135,245,0.6), transparent)"></div>

                <div class="johen-drift-e absolute bottom-[35%] right-[15%] w-[250px] h-[1px] opacity-15 blur-[2px] hidden dark:block"
                    style="background: linear-gradient(90deg, transparent, rgba(168,85,247,0.5), transparent)"></div>

                {{-- Subtle light streaks (light) --}}
                <div class="johen-drift-d absolute top-[25%] left-[20%] w-[320px] h-[2px] opacity-40 blur-[2px] dark:hidden"
                    style="background: linear-gradient(90deg, transparent, rgba(9,135,245,0.35), transparent)"></div>

                <div class="johen-drift-e absolute bottom-[35%] right-[15%] w-[260px] h-[2px] opacity-35 blur-[2px] dark:hidden"
                    style="background: linear-gradient(90deg, transparent, rgba(124,58,237,0.30), transparent)"></div>

                {{-- Star dust particles. Posisi, ukuran, dan warna statis; hanya
                     transform yang dianimasikan, lewat durasi + delay berbeda. --}}
                <template x-for="p in particles" :key="p.seed">
                    <div
                        class="johen-drift-p absolute rounded-full"
                        x-bind:style="`top: ${p.y}%; left: ${p.x}%; width: ${p.size}px; height: ${p.size}px; opacity: ${p.opacity}; background: ${p.glowColor}; box-shadow: 0 0 ${p.glow}px ${p.glowColor}; animation-duration: ${p.d}s; animation-delay: -${p.seed * 1.7}s;`"
                    ></div>
                </template>

                {{-- Vignette overlay --}}
                <div class="absolute inset-0 bg-gradient-to-b from-primary-100/40 via-transparent to-primary-100/30 pointer-events-none dark:from-[#07080F]/30 dark:via-transparent dark:to-[#07080F]/60"></div>
            </div>

            {{-- Theme and PWA controls --}}
            <div x-data="pwaInstall()" class="fixed right-4 top-4 z-50 flex items-center gap-2">
                <button
                    type="button"
                    x-show="!installed"
                    x-cloak
                    @click="install"
                    :disabled="installing"
                    :aria-busy="installing"
                    class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/60 bg-white/80 text-primary-600 shadow-sm backdrop-blur-lg transition-all duration-300 hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/30 disabled:cursor-wait disabled:opacity-60 dark:border-white/10 dark:bg-white/5 dark:text-primary-400 dark:hover:bg-white/10"
                    title="Pasang Johen App"
                    aria-label="Pasang Johen App di perangkat"
                >
                    <svg x-show="!installing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 10.5L12 15m0 0l4.5-4.5M12 15V3"/></svg>
                    <svg x-show="installing" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path class="opacity-75" fill="currentColor" d="M21 12a9 9 0 00-9-9v3a6 6 0 016 6h3z"/></svg>
                </button>
                <button
                    type="button"
                    @click="toggleTheme()"
                    x-data="themeToggle()"
                    :aria-label="isDark ? 'Aktifkan tema terang' : 'Aktifkan tema gelap'"
                    :aria-pressed="isDark"
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/80 backdrop-blur-lg border border-white/60 shadow-sm text-gray-500 hover:text-gray-900 hover:bg-white transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500/30 dark:bg-white/5 dark:border-white/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 dark:shadow-none"
                >
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    <svg x-show="isDark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                </button>
            </div>

            <div class="w-full max-w-md relative z-10">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-gray-500 dark:text-gray-600 relative z-10">&copy; {{ date('Y') }} PT. Johen Sukses Abadi. All rights reserved.</p>
        </div>

        <style>
            /* Background halaman login.

               Semua animasi hanya menyentuh `transform`, karena itu satu-satunya
               properti yang bisa dikompositor sehingga tidak memblokir thread
               utama. `background-position` (yang dipakai meshShift sebelumnya)
               memaksa repaint seluruh layar tiap frame - itu penyebab utama
               lag mengetik di mobile.

               Gradient dan blur sekarang statis: cukup di-rasterisasi sekali,
               lalu hanya tekturnya yang digeser. */

            @keyframes johen-mesh {
                0%, 100% { transform: translate3d(0, 0, 0); }
                25%      { transform: translate3d(2%, -1.5%, 0); }
                50%      { transform: translate3d(-1.5%, 2%, 0); }
                75%      { transform: translate3d(1.5%, 1%, 0); }
            }

            @keyframes johen-drift-a {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50%      { transform: translate3d(30px, -20px, 0); }
            }

            @keyframes johen-drift-b {
                0%, 100% { transform: translate3d(0, 0, 0) rotate(-20deg); }
                50%      { transform: translate3d(-35px, 25px, 0) rotate(-20deg); }
            }

            @keyframes johen-drift-c {
                0%, 100% { transform: translate3d(0, 0, 0) rotate(15deg); }
                50%      { transform: translate3d(40px, 30px, 0) rotate(15deg); }
            }

            @keyframes johen-drift-d {
                0%, 100% { transform: rotate(-4deg); }
                50%      { transform: translate3d(45px, -15px, 0) rotate(4deg); }
            }

            @keyframes johen-drift-e {
                0%, 100% { transform: rotate(-5deg); }
                50%      { transform: translate3d(-35px, 20px, 0) rotate(5deg); }
            }

            @keyframes johen-drift-p {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50%      { transform: translate3d(20px, -18px, 0); }
            }

            .johen-mesh    { animation: johen-mesh 18s ease-in-out infinite; }
            .johen-drift-a { animation: johen-drift-a 14s ease-in-out infinite; }
            .johen-drift-b { animation: johen-drift-b 17s ease-in-out infinite; }
            .johen-drift-c { animation: johen-drift-c 21s ease-in-out infinite; }
            .johen-drift-d { animation: johen-drift-d 11s ease-in-out infinite; }
            .johen-drift-e { animation: johen-drift-e 13s ease-in-out infinite; }
            .johen-drift-p { animation: johen-drift-p 9s ease-in-out infinite; }

            @media (prefers-reduced-motion: reduce) {
                .johen-mesh,
                .johen-drift-a,
                .johen-drift-b,
                .johen-drift-c,
                .johen-drift-d,
                .johen-drift-e,
                .johen-drift-p {
                    animation: none;
                }
            }
        </style>

        @livewireScripts
        @stack('scripts')
        @include('partials.pwa-scripts')

        <script>
            function themeToggle() {
                return {
                    isDark: document.documentElement.classList.contains('dark'),
                    toggleTheme() {
                        this.isDark = !this.isDark;
                        document.documentElement.classList.toggle('dark', this.isDark);
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    }
                }
            }

            function logoAnimation() {
                return {
                    glowIntensity: 0.3,
                    flipAngle: 0,
                    sweepX: -150,
                    init() {
                        this.startSweep();
                        this.startFlipCycle();
                    },
                    startSweep() {
                        setInterval(() => {
                            this.sweepX = 150;
                            this.$nextTick(() => {
                                setTimeout(() => { this.sweepX = -150; }, 100);
                            });
                        }, 4000);
                    },
                    startFlipCycle() {
                        setInterval(() => {
                            this.flipAngle = 360;
                            this.$nextTick(() => {
                                setTimeout(() => { this.flipAngle = 0; }, 800);
                            });
                        }, 6000);
                    }
                }
            }

            /* Cukup data statis untuk partikel. Geraknya ditangani CSS
               (keyframes .johen-drift-p) - durasi & delay dibedakan per partikel
               lewat index seed supaya tidak denyut serempak.

               Dulu fungsi ini menjalankan requestAnimationFrame tanpa henti dan
               menulis ulang x-bind:style (termasuk gradient) tiap frame. Itu
               yang bikin mengetik di field login terasa lag. */
            function starDust() {
                return {
                    particles: [
                        { x: 15, y: 12, size: 2,   opacity: 0.60, glow: 4, glowColor: 'rgba(9,135,245,0.5)',   d: 9,  seed: 1 },
                        { x: 82, y: 18, size: 1.5, opacity: 0.50, glow: 3, glowColor: 'rgba(124,58,237,0.4)', d: 11, seed: 2 },
                        { x: 45, y: 75, size: 2.5, opacity: 0.40, glow: 5, glowColor: 'rgba(9,135,245,0.4)',   d: 10, seed: 3 },
                        { x: 70, y: 65, size: 1,   opacity: 0.70, glow: 3, glowColor: 'rgba(168,85,247,0.5)',  d: 12, seed: 4 },
                        { x: 8,  y: 85, size: 2,   opacity: 0.35, glow: 4, glowColor: 'rgba(9,135,245,0.3)',   d: 9.5, seed: 5 },
                        { x: 92, y: 40, size: 1.5, opacity: 0.50, glow: 3, glowColor: 'rgba(124,58,237,0.4)', d: 13, seed: 6 },
                        { x: 25, y: 50, size: 1,   opacity: 0.60, glow: 2, glowColor: 'rgba(9,135,245,0.4)',   d: 10.5, seed: 7 },
                        { x: 55, y: 30, size: 2,   opacity: 0.30, glow: 4, glowColor: 'rgba(168,85,247,0.3)',  d: 12.5, seed: 8 },
                        { x: 60, y: 90, size: 1.5, opacity: 0.45, glow: 3, glowColor: 'rgba(9,135,245,0.4)',   d: 9,  seed: 9 },
                        { x: 35, y: 8,  size: 1,   opacity: 0.50, glow: 2, glowColor: 'rgba(124,58,237,0.4)', d: 14, seed: 10 },
                        { x: 75, y: 50, size: 2,   opacity: 0.35, glow: 4, glowColor: 'rgba(9,135,245,0.3)',   d: 11.5, seed: 11 },
                        { x: 18, y: 35, size: 1.5, opacity: 0.55, glow: 3, glowColor: 'rgba(168,85,247,0.4)',  d: 10, seed: 12 },
                    ]
                }
            }
        </script>
    </body>
</html>
