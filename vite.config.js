import os from 'os';
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

function getLanIp() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        const addrs = interfaces[name] ?? [];
        for (const iface of addrs) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return 'localhost';
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
