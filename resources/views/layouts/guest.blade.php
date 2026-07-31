<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Johen Sukses Abadi') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
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
        <div class="relative min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden bg-[#07080F] selection:bg-primary-500/20">

            {{-- Animated background layer --}}
            <div
                x-data="bgParallax()"
                x-on:mousemove="move"
                x-on:mouseleave="reset"
                class="absolute inset-0 overflow-hidden"
            >
                {{-- Gradient mesh base --}}
                <div class="absolute inset-0 opacity-35 dark:opacity-45"
                    style="background: linear-gradient(135deg, #0987F5 0%, #7C3AED 25%, #1E1B4B 50%, #4C1D95 75%, #0987F5 100%);
                    background-size: 400% 400%;
                    animation: meshShift 12s ease-in-out infinite;">
                </div>

                {{-- Aurora light ribbons --}}
                <div
                    class="absolute top-[10%] right-[5%] w-[500px] h-[200px] rounded-full opacity-40 dark:opacity-50 blur-[100px]"
                    x-bind:style="`transform: translate(${floatOffsetX[0]}px, ${floatOffsetY[0]}px); background: linear-gradient(90deg, rgba(9,135,245,0.5), rgba(124,58,237,0.3), transparent)`"
                ></div>

                <div
                    class="absolute bottom-[15%] left-[5%] w-[450px] h-[180px] rounded-full opacity-30 dark:opacity-40 blur-[100px]"
                    x-bind:style="`transform: translate(${floatOffsetX[1]}px, ${floatOffsetY[1]}px) rotate(-20deg); background: linear-gradient(90deg, transparent, rgba(124,58,237,0.4), rgba(9,135,245,0.3))`"
                ></div>

                <div
                    class="absolute top-[40%] left-[60%] w-[400px] h-[150px] rounded-full opacity-25 dark:opacity-35 blur-[80px]"
                    x-bind:style="`transform: translate(${floatOffsetX[2]}px, ${floatOffsetY[2]}px) rotate(15deg); background: linear-gradient(90deg, rgba(9,135,245,0.3), rgba(168,85,247,0.2), transparent)`"
                ></div>

                {{-- Subtle light streaks --}}
                <div
                    class="absolute top-[25%] left-[20%] w-[300px] h-[1px] opacity-20 blur-[2px]"
                    x-bind:style="`transform: translate(${floatOffsetX[3]}px, ${floatOffsetY[3]}px) rotate(${floatRotate[0]}deg); background: linear-gradient(90deg, transparent, rgba(9,135,245,0.6), transparent)`"
                ></div>

                <div
                    class="absolute bottom-[35%] right-[15%] w-[250px] h-[1px] opacity-15 blur-[2px]"
                    x-bind:style="`transform: translate(${floatOffsetX[4]}px, ${floatOffsetY[4]}px) rotate(${floatRotate[1]}deg); background: linear-gradient(90deg, transparent, rgba(168,85,247,0.5), transparent)`"
                ></div>

                {{-- Star dust particles --}}
                <template x-for="i in 12" :key="i">
                    <div
                        class="absolute rounded-full"
                        x-bind:class="`bg-${particles[i-1].color}`"
                        x-bind:style="`top: ${particles[i-1].y}%; left: ${particles[i-1].x}%; width: ${particles[i-1].size}px; height: ${particles[i-1].size}px; opacity: ${particles[i-1].opacity}; transform: translate(${particles[i-1].ox}px, ${particles[i-1].oy}px); box-shadow: 0 0 ${particles[i-1].glow}px ${particles[i-1].glowColor}`"
                    ></div>
                </template>

                {{-- Vignette overlay --}}
                <div class="absolute inset-0 bg-gradient-to-b from-[#07080F]/30 via-transparent to-[#07080F]/60 pointer-events-none"></div>
            </div>

            {{-- Theme toggle --}}
            <div class="fixed top-4 right-4 z-50">
                <button @click="toggleTheme()" x-data="themeToggle()" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 backdrop-blur-lg border border-white/10 text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-300" title="Toggle theme">
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    <svg x-show="isDark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                </button>
            </div>

            <div class="w-full max-w-md relative z-10">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-gray-600 relative z-10">&copy; {{ date('Y') }} PT. Johen Sukses Abadi. All rights reserved.</p>
        </div>

        <style>
            @keyframes meshShift {
                0%, 100% { background-position: 0% 50%; }
                25% { background-position: 100% 0%; }
                50% { background-position: 100% 100%; }
                75% { background-position: 0% 100%; }
            }
        </style>

        @livewireScripts

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

            function bgParallax() {
                return {
                    x: 0,
                    y: 0,
                    floatOffsetX: [0, 0, 0, 0, 0],
                    floatOffsetY: [0, 0, 0, 0, 0],
                    floatRotate: [0, 0],
                    particles: [
                        { x: 15, y: 12, size: 2, opacity: 0.6, glow: 4, glowColor: 'rgba(9,135,245,0.5)', color: 'primary-400/60', ox: 0, oy: 0 },
                        { x: 82, y: 18, size: 1.5, opacity: 0.5, glow: 3, glowColor: 'rgba(124,58,237,0.4)', color: 'violet-400/50', ox: 0, oy: 0 },
                        { x: 45, y: 75, size: 2.5, opacity: 0.4, glow: 5, glowColor: 'rgba(9,135,245,0.4)', color: 'primary-400/40', ox: 0, oy: 0 },
                        { x: 70, y: 65, size: 1, opacity: 0.7, glow: 3, glowColor: 'rgba(168,85,247,0.5)', color: 'violet-400/70', ox: 0, oy: 0 },
                        { x: 8, y: 85, size: 2, opacity: 0.35, glow: 4, glowColor: 'rgba(9,135,245,0.3)', color: 'primary-400/35', ox: 0, oy: 0 },
                        { x: 92, y: 40, size: 1.5, opacity: 0.5, glow: 3, glowColor: 'rgba(124,58,237,0.4)', color: 'violet-400/50', ox: 0, oy: 0 },
                        { x: 25, y: 50, size: 1, opacity: 0.6, glow: 2, glowColor: 'rgba(9,135,245,0.4)', color: 'primary-400/60', ox: 0, oy: 0 },
                        { x: 55, y: 30, size: 2, opacity: 0.3, glow: 4, glowColor: 'rgba(168,85,247,0.3)', color: 'violet-400/30', ox: 0, oy: 0 },
                        { x: 60, y: 90, size: 1.5, opacity: 0.45, glow: 3, glowColor: 'rgba(9,135,245,0.4)', color: 'primary-400/45', ox: 0, oy: 0 },
                        { x: 35, y: 8, size: 1, opacity: 0.5, glow: 2, glowColor: 'rgba(124,58,237,0.4)', color: 'violet-400/50', ox: 0, oy: 0 },
                        { x: 75, y: 50, size: 2, opacity: 0.35, glow: 4, glowColor: 'rgba(9,135,245,0.3)', color: 'primary-400/35', ox: 0, oy: 0 },
                        { x: 18, y: 35, size: 1.5, opacity: 0.55, glow: 3, glowColor: 'rgba(168,85,247,0.4)', color: 'violet-400/55', ox: 0, oy: 0 },
                    ],
                    init() {
                        this.animateFloating();
                    },
                    animateFloating() {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        const t = Date.now() / 1000;

                        this.floatOffsetX[0] = Math.sin(t * 0.12) * 30;
                        this.floatOffsetY[0] = Math.cos(t * 0.15) * 20;
                        this.floatOffsetX[1] = Math.sin(t * 0.18 + 1) * 35;
                        this.floatOffsetY[1] = Math.cos(t * 0.13 + 1) * 25;
                        this.floatOffsetX[2] = Math.sin(t * 0.08 + 2) * 40;
                        this.floatOffsetY[2] = Math.cos(t * 0.1 + 2) * 30;
                        this.floatOffsetX[3] = Math.sin(t * 0.22 + 3) * 45;
                        this.floatOffsetY[3] = Math.cos(t * 0.17 + 3) * 15;
                        this.floatOffsetX[4] = Math.sin(t * 0.2 + 4) * 35;
                        this.floatOffsetY[4] = Math.cos(t * 0.25 + 4) * 20;

                        this.floatRotate[0] = Math.sin(t * 0.1) * 8;
                        this.floatRotate[1] = Math.sin(t * 0.13 + 1) * 10;

                        for (let i = 0; i < 12; i++) {
                            const speed = [0.06, 0.09, 0.05, 0.11, 0.07, 0.13, 0.04, 0.1, 0.08, 0.12, 0.06, 0.15][i];
                            const amp = [25, 18, 30, 15, 22, 20, 28, 16, 24, 19, 26, 17][i];
                            this.particles[i].ox = Math.sin(t * speed + i * 1.8) * amp;
                            this.particles[i].oy = Math.cos(t * speed * 0.7 + i * 2.1) * amp;
                        }

                        requestAnimationFrame(() => this.animateFloating());
                    },
                    move(e) {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        const rect = e.currentTarget.getBoundingClientRect();
                        const cx = rect.width / 2;
                        const cy = rect.height / 2;
                        this.x = (e.clientX - rect.left - cx) / cx;
                        this.y = (e.clientY - rect.top - cy) / cy;
                    },
                    reset() {
                        this.x = 0;
                        this.y = 0;
                    }
                }
            }
        </script>
    </body>
</html>
