{{--
    Registrasi service worker + logika menutup splash.

    Letakkan sebelum </body> supaya tidak menunda render konten.

    Catatan service worker:
      - Didaftarkan dari partial ini karena vite-plugin-pwa disetel
        injectRegister: false, supaya tag <script> tidak di-inject Vite dan
        kita tetap pegang kendali penuh.
      - HTML TIDAK pernah masuk precache: navigateFallback null + navigasi
        selalu network-first. Tidak ada risiko data user bocor lewat cache.
      - updateViaCache 'none' memaksa browser memeriksa service worker baru
        setiap navigasi, penting supaya versi lama tidak nempel.
      - Saat service worker baru siap, halaman di-reload SEKALI. Penjaga
        sessionStorage mencegah reload loop kalau update datang berulang.
--}}
@php
    $swPath = public_path('sw.js');

    // Jangan daftar service worker saat `npm run dev` sedang jalan. SW akan
    // mem-precache /build/assets/* dari build terakhir, sehingga CSS/JS yang
    // sedang kamu kerjakan bisa tertahan versi basi.
    $swVersion = is_file(public_path('hot')) || !is_file($swPath) ? null : filemtime($swPath);
@endphp
<script>
    (function () {
        'use strict';

        var SW_URL = @json($swVersion ? '/sw.js?v=' . $swVersion : null);

        // ============================ SPLASH ============================
        (function splash() {
            var root = document.documentElement;
            if (root.getAttribute('data-splash') !== 'on') return;

            var MIN_MS = 1500;
            var MAX_MS = 4000;
            var started = Date.now();
            var dismissed = false;

            function dismiss() {
                if (dismissed) return;
                dismissed = true;

                try { sessionStorage.setItem('johen_splash_seen', '1'); } catch (e) { /* mode privat */ }

                // 'off' memicu transisi opacity + visibility dari CSS.
                root.setAttribute('data-splash', 'off');

                // Lepas dari DOM setelah transisi selesai, supaya tidak menambah
                // layer kompositasi di halaman yang sudah berjalan.
                window.setTimeout(function () {
                    var el = document.getElementById('johen-splash');
                    if (el && el.parentNode) el.parentNode.removeChild(el);
                }, 500);
            }

            function dismissWhenReady() {
                window.setTimeout(dismiss, Math.max(0, MIN_MS - (Date.now() - started)));
            }

            // Batas atas: kalau halaman macet, jangan tahan splash selamanya.
            var hardStop = window.setTimeout(dismiss, MAX_MS);

            if (document.readyState === 'complete') {
                window.clearTimeout(hardStop);
                dismissWhenReady();
            } else {
                window.addEventListener('load', function () {
                    window.clearTimeout(hardStop);
                    dismissWhenReady();
                });
            }

            var overlay = document.getElementById('johen-splash');
            if (overlay) overlay.addEventListener('click', dismiss);

            // Kalau tab di-switch lalu dikembalikan setelah splash dilewati,
            // tetap tutup supaya tidak ada overlay nyangkut di atas konten.
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden && Date.now() - started > MIN_MS) dismiss();
            });
        })();

        // ======================= SERVICE WORKER =======================
        if (!('serviceWorker' in navigator)) return;

        // Di `npm run dev` tidak ada build, jadi sw.js belum ada. Jangan
        // daftarkan apa pun daripada memicu 404 yang membingungkan.
        if (!SW_URL) return;

        window.addEventListener('load', function () {
            navigator.serviceWorker.register(SW_URL, { scope: '/', updateViaCache: 'none' })
                .then(function (reg) {
                    reg.addEventListener('updatefound', function () {
                        var sw = reg.installing;
                        if (!sw) return;

                        sw.addEventListener('statechange', function () {
                            // Hanya reload kalau ini worker pertama yang benar-
                            // benar mengambil alih halaman yang sedang terbuka.
                            if (sw.state !== 'activated' || !navigator.serviceWorker.controller) return;

                            var KEY = 'johen_sw_reload';
                            var already = false;
                            try { already = sessionStorage.getItem(KEY) === '1'; } catch (e) { /* noop */ }
                            if (already) return;

                            // skipWaiting + clientsClaim sudah aktif di Workbox,
                            // jadi aset ber-hash baru langsung tersaji tanpa
                            // perlu minta izin ke worker lama.
                            try { sessionStorage.setItem(KEY, '1'); } catch (e) { /* noop */ }
                            window.location.reload();
                        });
                    });
                })
                .catch(function (err) {
                    // Tidak fatal: aplikasi tetap berfungsi normal tanpa SW.
                    console.warn('[PWA] gagal mendaftarkan service worker:', err);
                });
        });
    })();
</script>
