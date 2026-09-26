{{--
    Aset PWA: web app manifest, ikon, dan theme-color.

    Manifest + service worker dihasilkan vite-plugin-pwa (lihat vite.config.js),
    bukan file statis di repo: keduanya output build seperti aset Vite lain.
    Karena itu dibungkus file_exists - saat `npm run dev` belum ada build,
    sehingga halaman ini sengaja tidak punya manifest dan tidak bisa di-install.

    Overlay splash aplikasi ditampilkan setelah dokumen mulai dimuat. iOS
    startup-image sengaja tidak dipakai agar tidak muncul splash logo kedua.
--}}

{{-- Web app manifest. File statis di repo, bukan output build. --}}
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}?v={{ filemtime(public_path('manifest.webmanifest')) }}">

{{-- Ikon. favicon.ico bawaan tidak dipakai karena 0 byte --}}
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('pwa/favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('pwa/icon-192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('pwa/apple-touch-icon.png') }}">

{{-- Warna UI browser. Satu untuk light & dark karena standalone PWA butuh
     warna stabil; warna biru brand dipakai untuk keduanya. --}}
<meta name="theme-color" content="#0987F5">
<meta name="color-scheme" content="light dark">


{{-- iOS: mode standalone + sembunyikan address bar Safari --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ config('app.short_name', 'Johen Apps') }}">

{{--
    Putuskan apakah overlay splash perlu dirender, SEBELUM body digambar.
    Kalau putuskan di body, akan ada kedipan splash di kunjungan berikutnya.

    Aturan:
      ?splash=0  paksa mati          ?splash=1  paksa tampil
      selain itu  tampil sekali per sesi browser (sessionStorage)

    Kenapa sekali per sesi: aplikasi ini dipakai intensif seharian. Splash
    1,5 detik di setiap page load akan sangat mengganggu.
--}}
<script>
    (function () {
        'use strict';
        try {
            var KEY = 'johen_splash_seen';
            var param = new URLSearchParams(window.location.search).get('splash');
            var show = param === '0' ? false : (param === '1' ? true : sessionStorage.getItem(KEY) !== '1');
            document.documentElement.setAttribute('data-splash', show ? 'on' : 'off');
        } catch (e) {
            document.documentElement.setAttribute('data-splash', 'on');
        }
    })();
</script>

<style>
    /* ---- Overlay splash ----
       Sepenuhnya inline, tidak bergantung Tailwind/Vite, supaya tetap tampil
       walau bundel CSS masih diunduh. */
    #johen-splash {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.45s ease, visibility 0.45s ease;
        background:
            radial-gradient(120% 60% at 50% 34%, rgba(9, 135, 245, 0.50) 0%, transparent 70%),
            radial-gradient(110% 55% at 42% 78%, rgba(133, 78, 234, 0.34) 0%, transparent 70%),
            #07080F;
    }

    html[data-splash="on"] #johen-splash {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    /* Body tidak boleh bisa di-scroll selama splash tampil. */
    html[data-splash="on"] body { overflow: hidden; }

    /* Logo + teks adalah satu blok yang dipusatkan vertikal, bukan logo
       sendirian. Dulu teksnya position:absolute di bawah stage, sehingga
       kelihatan melayang ke bawah titik tengah layar. */
    .johen-splash-stack {
        display: grid;
        justify-items: center;
        row-gap: 40px;
    }

    .johen-splash-stage {
        position: relative;
        display: grid;
        place-items: center;
        width: 80px;
        height: 80px;
    }

    /* Glow di belakang logo, meniru radial-gradient di sidebar. Statis. */
    .johen-splash-glow {
        position: absolute;
        inset: -22%;
        border-radius: 24px;
        background: radial-gradient(circle, rgba(9, 135, 245, 0.55), rgba(124, 58, 237, 0.28) 45%, transparent 70%);
        filter: blur(14px);
    }

    /* Logo statis - satu-satunya yang bergerak di splash ini progress bar. */
    .johen-splash-logo {
        position: relative;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .johen-splash-text {
        text-align: center;
        white-space: nowrap;
    }

    .johen-splash-title {
        font-family: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
        font-size: 1.0625rem;
        font-weight: 800;
        letter-spacing: 0.22em;
        color: #e2e8f0;
        text-indent: 0.22em;
    }

    .johen-splash-sub {
        margin-top: 5px;
        font-size: 0.6875rem;
        font-weight: 500;
        letter-spacing: 0.1em;
        color: #64748b;
    }

    .johen-splash-bar {
        position: fixed;
        left: 50%;
        bottom: calc(38px + env(safe-area-inset-bottom));
        transform: translateX(-50%);
        width: 116px;
        height: 2px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.16);
        overflow: hidden;
    }

    .johen-splash-bar::after {
        content: "";
        display: block;
        width: 40%;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(to right, #0987F5, #854DEA);
        animation: johen-splash-bar 1.15s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    @keyframes johen-splash-bar {
        0%   { transform: translateX(-110%); }
        100% { transform: translateX(360%); }
    }

    @media (prefers-reduced-motion: reduce) {
        .johen-splash-bar::after { animation: none; width: 100%; transform: none; }
    }
</style>
