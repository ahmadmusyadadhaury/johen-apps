// Alpine.js sudah termasuk dalam bundle Livewire 4 (@livewireScripts)
// Tidak perlu import terpisah untuk menghindari konflik dua instance Alpine.
//
// Livewire 4 juga menyediakan magic property $wire untuk entangle.
// Jika ada kode Alpine custom, gunakan window.Alpine yang disediakan Livewire.

import JsBarcode from 'jsbarcode';
import QRCode from 'qrcode';

// Pengguna koordinator dapat mendaftarkan browser/PWA untuk push deadline script.
function decodeVapidKey(value) {
    const padding = '='.repeat((4 - value.length % 4) % 4);
    const base64 = (value + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);
    return Uint8Array.from(raw, (character) => character.charCodeAt(0));
}

async function setupScriptVideoPush(button) {
    const panel = button.closest('[data-script-video-push-notice]');
    const status = panel?.querySelector('[data-script-video-push-status]');
    const setStatus = (message) => {
        if (status) status.textContent = message;
    };
    const markActive = () => {
        panel?.remove();
    };

    setStatus('Memeriksa status notifikasi pada browser ini...');
    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        button.textContent = 'Notifikasi tidak didukung';
        button.disabled = true;
        setStatus('Browser ini tidak mendukung notifikasi push.');
        return;
    }

    button.disabled = true;
    try {
        const configResponse = await fetch(button.dataset.configUrl, { headers: { Accept: 'application/json' } });
        if (!configResponse.ok) throw new Error('Konfigurasi notifikasi gagal dimuat. Muat ulang halaman lalu coba lagi.');
        const pushConfig = await configResponse.json();
        if (!pushConfig.available) {
            button.textContent = 'Notifikasi belum dikonfigurasi';
            button.disabled = true;
            setStatus('Push belum dikonfigurasi pada server.');
            return;
        }

        // Vite dev does not run the production-only registration at the end of
        // this file. Register the lightweight push worker so a fresh local
        // browser profile does not wait forever on serviceWorker.ready.
        const workerUrl = import.meta.env.DEV ? '/push-sw.js' : '/sw.js';
        await navigator.serviceWorker.register(workerUrl, {
            scope: '/',
            updateViaCache: 'none',
        });
        const registration = await navigator.serviceWorker.ready;
        const existing = await registration.pushManager.getSubscription();
        if (Notification.permission === 'denied') {
            setStatus('Izin notifikasi diblokir di browser ini. Ubah izin situs menjadi Izinkan pada setelan Chrome.');
        } else if (!existing) {
            setStatus('Belum aktif pada browser/perangkat ini.');
        }
        button.disabled = false;
        if (existing && Notification.permission === 'granted') {
            const response = await fetch(button.dataset.subscribeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(existing.toJSON()),
            });
            if (response.ok) {
                markActive();
                return;
            }
            setStatus('Langganan browser ditemukan, tetapi belum tersimpan di server. Tekan tombol untuk mencoba lagi.');
        }
        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                let permission = Notification.permission;
                if (permission === 'default') permission = await Notification.requestPermission();
                if (permission === 'denied') {
                    throw new Error('Notifikasi diblokir Chrome. Klik ikon pengaturan di kiri alamat situs, ubah Notifikasi menjadi Izinkan, muat ulang halaman, lalu tekan tombol ini lagi.');
                }
                if (permission !== 'granted') throw new Error('Chrome belum memberikan izin notifikasi. Izinkan notifikasi untuk situs ini lalu coba lagi.');

                const subscription = existing || await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: decodeVapidKey(pushConfig.publicKey),
                });
                const response = await fetch(button.dataset.subscribeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(subscription.toJSON()),
                });
                if (!response.ok) throw new Error('Langganan notifikasi gagal disimpan.');
                markActive();
                window.dispatchEvent(new CustomEvent('script-video-push-activated'));
            } catch (error) {
                button.disabled = false;
                const message = error.name === 'NotAllowedError'
                    ? 'Chrome menolak izin notifikasi. Klik ikon pengaturan di kiri alamat situs, ubah Notifikasi menjadi Izinkan, muat ulang halaman, lalu coba lagi.'
                    : error.message;
                if (Notification.permission === 'denied') {
                    setStatus('Izin notifikasi diblokir. Izinkan notifikasi untuk situs ini di setelan browser, lalu tekan tombol lagi.');
                } else {
                    setStatus('Aktivasi belum berhasil. ' + message);
                }
                if (window.Alpine?.store('toast')) window.Alpine.store('toast').error(message);
                else window.alert(message);
            }
        });
    } catch (error) {
        button.textContent = 'Notifikasi belum siap';
        button.disabled = true;
        setStatus('Service worker push gagal disiapkan. Muat ulang halaman setelah server lokal berjalan.');
        console.warn('[Script Video] gagal menyiapkan push:', error);
    }
}

function initScriptVideoPushButtons() {
    document.querySelectorAll('[data-script-video-push]:not([data-push-ready])').forEach((button) => {
        button.dataset.pushReady = '1';
        setupScriptVideoPush(button);
    });
}

document.addEventListener('DOMContentLoaded', initScriptVideoPushButtons);
document.addEventListener('livewire:navigated', initScriptVideoPushButtons);

// Ekspos ke window agar bisa dipakai di directive x-init "@barcode" pada halaman Asset.
window.JsBarcode = JsBarcode;
window.QRCode = QRCode;

let deferredPwaInstallPrompt = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredPwaInstallPrompt = event;
    window.dispatchEvent(new CustomEvent('pwa:installable'));
});

window.addEventListener('appinstalled', () => {
    deferredPwaInstallPrompt = null;
    window.dispatchEvent(new CustomEvent('pwa:installed'));
});

document.addEventListener('alpine:init', () => {
    // Toast notifications (top-right)
    Alpine.store('toast', {
        items: [],

        add(type, message, duration = 4000) {
            const id = Date.now() + Math.random();
            this.items.push({ id, type, message, duration });

            if (duration > 0) {
                setTimeout(() => { this.remove(id); }, duration);
            }
        },

        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        },

        success(message, duration) { this.add('success', message, duration); },
        error(message, duration) { this.add('error', message, duration); },
        warning(message, duration) { this.add('warning', message, duration); },
        info(message, duration) { this.add('info', message, duration); },
    });

    // Confirm modal (centered, untuk konfirmasi hapus)
    Alpine.store('confirmModal', {
        open: false,
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        onConfirm: null,
        onCancel: null,

        show(title, message, onConfirm, onCancel) {
            this.title = title;
            this.message = message;
            this.onConfirm = onConfirm || null;
            this.onCancel = onCancel || null;
            this.open = true;
        },

        confirm() {
            if (this.onConfirm) this.onConfirm();
            this.open = false;
        },

        cancel() {
            if (this.onCancel) this.onCancel();
            this.open = false;
        },

        hide() {
            this.open = false;
            this.onConfirm = null;
            this.onCancel = null;
        },
    });

    // Success modal (centered, for CRUD operations)
    Alpine.store('successModal', {
        open: false,
        message: '',

        show(msg) {
            this.message = msg;
            this.open = true;
        },

        hide() {
            this.open = false;
        },
    });

    Alpine.data('pwaInstall', () => ({
        installed: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true,
        installing: false,
        hasInstallPrompt: deferredPwaInstallPrompt !== null,

        init() {
            this.onInstallable = () => {
                this.hasInstallPrompt = true;
            };

            this.onInstalled = () => {
                this.hasInstallPrompt = false;
                this.installed = true;
            };

            window.addEventListener('pwa:installable', this.onInstallable);
            window.addEventListener('pwa:installed', this.onInstalled);
        },

        installMessage() {
            const isLocal = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);
            const isIos = /iPad|iPhone|iPod/.test(window.navigator.userAgent)
                || (window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1);

            if (isLocal) {
                return 'Prompt instalasi PWA tidak aktif di localhost. Jalankan build production melalui HTTPS.';
            }

            if (isIos) {
                return 'Di iPhone atau iPad, tap ikon Bagikan lalu pilih Add to Home Screen.';
            }

            return 'Browser ini belum menampilkan prompt instalasi. Gunakan Chrome atau Edge, atau pilih Install Johen App dari menu browser.';
        },

        async install() {
            const installPrompt = deferredPwaInstallPrompt;

            if (this.installed || this.installing) {
                return;
            }

            if (!installPrompt) {
                this.hasInstallPrompt = false;
                window.alert(this.installMessage());
                return;
            }

            this.installing = true;

            try {
                await installPrompt.prompt();
                const choice = await installPrompt.userChoice;
                this.installed = choice.outcome === 'accepted';
            } finally {
                deferredPwaInstallPrompt = null;
                this.hasInstallPrompt = false;
                this.installing = false;
            }
        },

        destroy() {
            window.removeEventListener('pwa:installable', this.onInstallable);
            window.removeEventListener('pwa:installed', this.onInstalled);
        },
    }));
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

if (import.meta.env.PROD && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', {
            scope: '/',
            updateViaCache: 'none',
        }).catch(() => undefined);
    }, { once: true });
}
