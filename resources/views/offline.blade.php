<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Offline - {{ config('app.name', 'Johen Apps') }}</title>
    <link rel="icon" href="/pwa/favicon-32.png" sizes="32x32">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            display: grid;
            place-items: center;
            padding: calc(24px + env(safe-area-inset-top)) 24px calc(24px + env(safe-area-inset-bottom));
            background: #07080F;
            color: #e2e8f0;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .wrap { text-align: center; max-width: 26rem; }
        .logo {
            width: 88px; height: 88px;
            margin: 0 auto 28px;
            object-fit: contain;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-9px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .logo { animation: none; }
        }
        h1 { font-size: 1.25rem; font-weight: 700; margin: 0 0 8px; letter-spacing: -0.01em; }
        p { color: #94a3b8; font-size: 0.9rem; line-height: 1.6; margin: 0 0 26px; }
        .status {
            display: inline-flex; align-items: center; gap: 8px;
            margin-bottom: 24px; padding: 6px 14px;
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.08);
            color: #94a3b8; font-size: 0.75rem; font-weight: 600;
        }
        .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.7);
        }
        .dot.ok { background: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.7); }
        button {
            display: inline-flex; align-items: center; justify-content: center;
            width: 100%; padding: 13px 24px;
            border: 0; border-radius: 10px;
            background: #0987F5; color: #fff;
            font-family: inherit; font-size: 0.9rem; font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, opacity 0.2s ease;
        }
        button:hover:not(:disabled) { background: #036bd1; }
        button:disabled { opacity: 0.5; cursor: not-allowed; }
        .note { margin: 18px 0 0; font-size: 0.72rem; color: #475569; }
    </style>
</head>
<body>
    <div class="wrap">
        <img src="/pwa/icon-512.png" alt="" class="logo" width="88" height="88">

        <div class="status">
            <span class="dot" id="dot"></span>
            <span id="statusText">Koneksi terputus</span>
        </div>

        <h1>Tidak ada koneksi</h1>
        <p>
            Halaman ini butuh koneksi ke server. Data yang belum pernah
            dimuat tidak bisa ditampilkan.
        </p>

        <button type="button" id="retry">Coba lagi</button>

        <p class="note" id="note">Menunggu jaringan...</p>
    </div>

    <script>
        (function () {
            'use strict';

            var btn = document.getElementById('retry');
            var note = document.getElementById('note');
            var dot = document.getElementById('dot');
            var label = document.getElementById('statusText');

            function setOnline(isOnline) {
                dot.className = isOnline ? 'dot ok' : 'dot';
                label.textContent = isOnline ? 'Jaringan kembali aktif' : 'Koneksi terputus';
                btn.disabled = !isOnline;
                note.textContent = isOnline
                    ? 'Muat ulang halaman sekarang.'
                    : 'Menunggu jaringan...';
            }

            function reload() {
                btn.disabled = true;
                location.reload();
            }

            btn.addEventListener('click', function () {
                if (navigator.onLine) reload();
            });

            window.addEventListener('online', function () {
                setOnline(true);
                reload();
            });

            window.addEventListener('offline', function () {
                setOnline(false);
            });

            setOnline(navigator.onLine);
        })();
    </script>
</body>
</html>
