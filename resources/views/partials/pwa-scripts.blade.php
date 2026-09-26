{{--
    Registrasi service worker + logika menutup splash.

    Letakkan sebelum </body> supaya tidak menunda render konten.

    Catatan service worker:
      - Halaman HTML TIDAK di-cache (lihat public/service-worker.js), jadi tidak
        ada risiko data user bocor lewat cache.
      - updateViaCache 'none' memaksa browser memeriksa service worker baru
        setiap navigasi, penting supaya versi lama tidak nempel.
      - Saat service worker baru siap, halaman di-reload SEKALI. Penjaga
        sessionStorage mencegah reload loop kalau update datang berulang.
--}}
@php
    $swPath = public_path('service-worker.js');
    $swUrl = '/service-worker.js?v='.(is_file($swPath) ? filemtime($swPath) : '1');
@endphp
<script>
    (function () {
        'use strict';

        var SW_URL = @json($swUrl);

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

        window.addEventListener('load', function () {
            navigator.serviceWorker.register(SW_URL, { updateViaCache: 'none' })
                .then(function (reg) {
                    reg.addEventListener('updatefound', function () {
                        var sw = reg.installing;
                        if (!sw) return;

                        sw.addEventListener('statechange', function () {
                            // Hanya reload kalau ini/update worker pertama
                            // yang benar-benar mengambil alih.
                            if (sw.state !== 'installed' || !navigator.serviceWorker.controller) return;

                            var KEY = 'johen_sw_reload';
                            var already = false;
                            try { already = sessionStorage.getItem(KEY) === '1'; } catch (e) { /* noop */ }
                            if (already) return;

                            sw.postMessage({ type: 'SKIP_WAITING' });
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
