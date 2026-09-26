/*
 * Johen Apps - Service Worker
 *
 * SANGAT PENTING untuk app yang full-auth:
 * Halaman HTML TIDAK PERNAH di-cache. Kalau HTML ikut ter-cache, user yang
 * sudah logout masih bisa membuka dashboard lama dari cache - itu kebocoran
 * data antar user. Semua request navigasi selalu ke network, dengan fallback
 * ke halaman /offline ketika jaringan mati.
 *
 * Yang di-cache hanya aset statis yang namanya sudah di-hash (Vite) atau
 * aset PWA sendiri - aman karena isinya identik untuk semua user.
 */

const VERSION = 'v1';
const STATIC_CACHE = `johen-static-${VERSION}`;
const PWA_CACHE = `johen-pwa-${VERSION}`;
const OFFLINE_URL = '/offline';

const KEEP = new Set([STATIC_CACHE, PWA_CACHE]);

/** Aset yang di-precache saat install. */
const PRECACHE = [
    '/pwa/icon-192.png',
    '/pwa/icon-512.png',
    '/pwa/favicon-32.png',
    '/pwa/apple-touch-icon.png',
    '/manifest.webmanifest',
    OFFLINE_URL,
];

self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const cache = await caches.open(PWA_CACHE);
        // Jangan pakai cache.addAll: satu aset 404 akan menggagalkan seluruh install.
        // Masukkan satu per satu supaya aset yang gagal hanya dilewati.
        await Promise.all(
            PRECACHE.map(async (url) => {
                try {
                    const res = await fetch(url, { credentials: 'same-origin', cache: 'reload' });
                    if (res && (res.ok || res.type === 'opaque')) {
                        await cache.put(url, res);
                    }
                } catch (e) {
                    // abaikan aset yang gagal, install tetap selesai
                }
            })
        );
        await self.skipWaiting();
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const names = await caches.keys();
        await Promise.all(names.filter((n) => !KEEP.has(n)).map((n) => caches.delete(n)));
        await self.clients.claim();
    })());
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

/** true kalau response layak disimpan (bukan opaque, bukan error, bukan partial). */
function isCacheable(response) {
    if (!response || !response.ok) return false;
    if (response.type === 'opaque') return false;
    if (response.status !== 200) return false;
    return true;
}

/**
 * Cache-first. Hanya untuk URL yang namanya mengandung hash konten
 * (output Vite), jadi URL baru = konten baru dan cache lama tidak akan
 * pernah dipakai untuk konten yang salah.
 */
async function cacheFirst(request, cacheName) {
    const cache = await caches.open(cacheName);
    const hit = await cache.match(request);
    if (hit) return hit;

    const response = await fetch(request);
    if (isCacheable(response)) {
        cache.put(request, response.clone());
    }
    return response;
}

/** Stale-while-revalidate: sajikan yang cached, perbarui di belakang. */
async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const hit = await cache.match(request);

    const network = fetch(request)
        .then((response) => {
            if (isCacheable(response)) {
                cache.put(request, response.clone());
            }
            return response;
        })
        .catch(() => null);

    if (hit) return hit;

    const response = await network;
    if (response) return response;

    throw new Error('offline');
}

/** Halaman fallback saat navigasi gagal. */
async function offlineResponse() {
    const cache = await caches.open(PWA_CACHE);
    const cached = await cache.match(OFFLINE_URL);
    if (cached) return cached;

    return new Response(
        '<!doctype html><meta charset="utf-8"><title>Offline</title>' +
        '<body style="margin:0;min-height:100vh;display:grid;place-items:center;background:#07080F;color:#e2e8f0;' +
        'font:16px system-ui,-apple-system,sans-serif;text-align:center">' +
        '<div><div style="font-size:40px;margin-bottom:12px">&#128246;</div>' +
        '<h1 style="font-size:18px;margin:0 0 8px">Koneksi terputus</h1>' +
        '<p style="color:#94a3b8;margin:0 0 20px">Periksa jaringan lalu coba lagi.</p>' +
        '<button onclick="location.reload()" ' +
        'style="padding:10px 20px;border:0;border-radius:8px;background:#0987F5;color:#fff;font-weight:600;cursor:pointer">' +
        'Coba lagi</button></div></body>',
        { status: 503, headers: { 'Content-Type': 'text/html; charset=utf-8' } }
    );
}

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    // 1. Navigasi: SELALU network-only. Jangan pernah cache HTML dashboard.
    if (request.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                return await fetch(request);
            } catch (e) {
                return offlineResponse();
            }
        })());
        return;
    }

    // 2. Aset hasil build Vite: nama file di-hash, jadi aman cache-first.
    if (url.pathname.startsWith('/build/assets/')) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // 3. Aset PWA: kecil, jarang berubah, SWR aman.
    if (url.pathname.startsWith('/pwa/') || url.pathname === '/manifest.webmanifest') {
        event.respondWith(staleWhileRevalidate(request, PWA_CACHE));
        return;
    }

    // 4. Livewire, API, gambar user, dsb: biarkan lewat, jangan di-intercept.
});
