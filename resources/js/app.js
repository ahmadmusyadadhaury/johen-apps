// Alpine.js sudah termasuk dalam bundle Livewire 4 (@livewireScripts)
// Tidak perlu import terpisah untuk menghindari konflik dua instance Alpine.
//
// Livewire 4 juga menyediakan magic property $wire untuk entangle.
// Jika ada kode Alpine custom, gunakan window.Alpine yang disediakan Livewire.

import JsBarcode from 'jsbarcode';
import QRCode from 'qrcode';

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
