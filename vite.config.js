import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';
import os from 'node:os';
import { resolve } from 'node:path';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

/**
 * Aset statis PWA yang di-precache beserta revision dari isi file.
 *
 * Hanya logo + ikon. Sembilan splash iOS sengaja TIDAK di-precache:
 * totalnya ~1,2 MB, sedangkan iOS mengambil startup image lewat OS (bukan
 * service worker) sehingga precache tidak akan membantu cold start offline.
 */
const pwaPublicAssets = [
    'logo.png',
    'pwa/favicon-32.png',
    'pwa/apple-touch-icon.png',
    'pwa/icon-192.png',
    'pwa/icon-512.png',
    'pwa/icon-maskable-192.png',
    'pwa/icon-maskable-512.png',
];

function getPwaAssetRevision(asset) {
    return createHash('md5')
        .update(readFileSync(resolve('public', asset)))
        .digest('hex');
}

function getLanIp() {
    const interfaces = os.networkInterfaces();
    const candidates = [];
    for (const name of Object.keys(interfaces)) {
        const addrs = interfaces[name] ?? [];
        for (const iface of addrs) {
            if (iface.family === 'IPv4' && !iface.internal) {
                candidates.push({ name, address: iface.address });
            }
        }
    }
    if (candidates.length === 0) {
        return 'localhost';
    }
    // Prefer physical/private LAN ranges over virtual adapters (WSL/Hyper-V/VPN use 172.x)
    return (
        candidates.find((c) => /^192\.168\./.test(c.address) || /^10\./.test(c.address))
        ?? candidates.find((c) => /^172\.(1[6-9]|2\d|3[01])\./.test(c.address))
        ?? candidates[0]
    ).address;
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const hmrHost = env.VITE_HMR_HOST || getLanIp();

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            VitePWA({
                registerType: 'autoUpdate',
                injectRegister: false,
                // Service worker HARUS di root, bukan di /build/: browser tidak
                // mengizinkan sebuah SW mengklaim scope ke luar foldernya tanpa
                // header Service-Worker-Allowed. SW di /build/ hanya menangkap
                // /build/*, sehingga navigasi ke /dashboard tidak pernah kena
                // dan fallback offline mati total.
                // Workbox menulis sw.js langsung ke disk (bukan lewat emitFile
                // rollup yang menolak '..'), jadi '..' di sini aman.
                buildBase: '/build/',
                filename: '../sw.js',
                scope: '/',
                // Manifest dibuat manual di public/manifest.webmanifest, bukan
                // oleh plugin. Alasannya: plugin menulis manifest ke dalam outDir
                // (public/build) lalu mendaftarkannya ke precache sebagai URL
                // relatif - yang salah begitu sw.js pindah ke root. Sebagai file
                // sumber, manifest juga gampang direview di git.
                manifest: false,
                workbox: {
                    cleanupOutdatedCaches: true,
                    clientsClaim: true,
                    skipWaiting: true,
                    // Halaman HTML/otorisasi tidak boleh dilayani dari cache.
                    navigateFallback: null,
                    globPatterns: ['**/*.{js,css}'],
                    additionalManifestEntries: [
                        ...pwaPublicAssets.map((asset) => ({
                            url: `/${asset}`,
                            revision: getPwaAssetRevision(asset),
                        })),
                        // Route Blade, jadi tidak bisa di-hash saat build.
                        // revision: null => URL disimpan apa adanya tanpa
                        // ?__WB_REVISION__, sehingga caches.match('/offline')
                        // di bawah bisa mencapainya.
                        { url: '/offline', revision: null },
                    ],
                    // globPatterns memindai outDir (public/build) dan menulis
                    // url relatif seperti 'assets/app-xxx.css'. Itu aman selama
                    // service worker ikut tinggal di /build/, tapi sw.js kita di
                    // root, sehingga url relatif akan resolve ke /assets/...
                    // yang salah. Prefiks '/build/' ke semua entri glob.
                    //
                    // additionalManifestEntries di atas sudah absolut, dan
                    // workbox menambahkannya SETELAH modifyURLPrefix, jadi
                    // entri itu tidak ikut diberi prefiks ganda.
                    modifyURLPrefix: { '': '/build/' },
                    runtimeCaching: [
                        {
                            urlPattern: ({ request }) => request.mode === 'navigate',
                            method: 'GET',
                            handler: async ({ request }) => {
                                try {
                                    return await fetch(request);
                                } catch {
                                    // ignoreVary wajib: respons Laravel mengirim
                                    // Vary, sedangkan cache precache menyimpan
                                    // apa adanya hasil fetch saat instalasi.
                                    const offlinePage = await caches.match('/offline', {
                                        ignoreVary: true,
                                    });
                                    return offlinePage ?? Response.error();
                                }
                            },
                        },
                    ],
                },
            }),
        ],
        server: {
            host: '0.0.0.0',
            port: 5174,
            origin: `http://${hmrHost}:5174`,
            cors: {
                origin: true,
                methods: ['GET', 'HEAD', 'PUT', 'PATCH', 'POST', 'DELETE'],
            },
            hmr: {
                host: hmrHost,
            },
        },
    };
});
