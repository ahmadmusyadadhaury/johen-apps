import { createHash } from 'node:crypto';
import { readFileSync } from 'node:fs';
import os from 'node:os';
import { resolve } from 'node:path';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

const pwaPublicAssets = [
    'offline.html',
    'logo.png',
    'pwa-192x192.png',
    'pwa-512x512.png',
    'pwa-maskable-512x512.png',
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
                buildBase: '/build/',
                includeManifestIcons: false,
                manifest: {
                    id: '/',
                    name: 'Johen Application',
                    short_name: 'Johen App',
                    description: 'Sistem manajemen operasional PT. Johen Sukses Abadi.',
                    lang: 'id',
                    dir: 'ltr',
                    start_url: '/',
                    scope: '/',
                    display: 'standalone',
                    background_color: '#f8fafc',
                    theme_color: '#0987F5',
                    categories: ['business', 'productivity'],
                    icons: [
                        {
                            src: '/pwa-192x192.png',
                            sizes: '192x192',
                            type: 'image/png',
                            purpose: 'any',
                        },
                        {
                            src: '/pwa-512x512.png',
                            sizes: '512x512',
                            type: 'image/png',
                            purpose: 'any',
                        },
                        {
                            src: '/pwa-maskable-512x512.png',
                            sizes: '512x512',
                            type: 'image/png',
                            purpose: 'maskable',
                        },
                    ],
                },
                workbox: {
                    cleanupOutdatedCaches: true,
                    clientsClaim: true,
                    skipWaiting: true,
                    navigateFallback: null,
                    globPatterns: ['**/*.{js,css}'],
                    additionalManifestEntries: pwaPublicAssets.map((asset) => ({
                        url: `/${asset}`,
                        revision: getPwaAssetRevision(asset),
                    })),
                    runtimeCaching: [
                        {
                            urlPattern: ({ request }) => request.mode === 'navigate',
                            method: 'GET',
                            handler: async ({ request }) => {
                                try {
                                    return await fetch(request);
                                } catch {
                                    const offlinePage = await caches.match('/offline.html');
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
