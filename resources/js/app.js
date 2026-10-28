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

async function setupTicketPush(button) {
    const panel = button.closest('[data-ticket-push-notice]');
    const status = panel?.querySelector('[data-ticket-push-status]');
    const setStatus = (message) => { if (status) status.textContent = message; };
    const saveSubscription = async (subscription) => fetch(button.dataset.subscribeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify(subscription.toJSON()),
    });

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        button.textContent = 'Notifikasi tidak didukung';
        button.disabled = true;
        setStatus('Browser ini tidak mendukung notifikasi push.');
        return;
    }

    try {
        const response = await fetch(button.dataset.configUrl, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Konfigurasi notifikasi gagal dimuat.');
        const config = await response.json();
        if (!config.available) {
            button.textContent = 'Notifikasi belum dikonfigurasi';
            button.disabled = true;
            setStatus('Push belum dikonfigurasi pada server.');
            return;
        }

        const workerUrl = import.meta.env.DEV ? '/push-sw.js' : '/sw.js';
        await navigator.serviceWorker.register(workerUrl, { scope: '/', updateViaCache: 'none' });
        const registration = await navigator.serviceWorker.ready;
        const existing = await registration.pushManager.getSubscription();
        button.disabled = false;
        button.textContent = existing && Notification.permission === 'granted' ? 'Notifikasi aktif' : 'Aktifkan notifikasi';
        if (existing && Notification.permission === 'granted') {
            const saved = await saveSubscription(existing);
            if (saved.ok) {
                setStatus('Notifikasi Ticketing aktif pada perangkat ini.');
                return;
            }
        } else if (Notification.permission === 'denied') {
            setStatus('Izin notifikasi diblokir. Izinkan notifikasi situs ini melalui setelan browser.');
        } else {
            setStatus('Aktifkan agar tiket baru dan pembaruan tiket tampil di perangkat ini.');
        }

        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                let permission = Notification.permission;
                if (permission === 'default') permission = await Notification.requestPermission();
                if (permission !== 'granted') throw new Error('Izin notifikasi belum diberikan.');
                const subscription = existing || await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: decodeVapidKey(config.publicKey),
                });
                const saved = await saveSubscription(subscription);
                if (!saved.ok) throw new Error('Langganan notifikasi gagal disimpan.');
                button.textContent = 'Notifikasi aktif';
                setStatus('Notifikasi Ticketing aktif pada perangkat ini.');
            } catch (error) {
                button.disabled = false;
                setStatus(error.message || 'Notifikasi belum berhasil diaktifkan.');
                window.Alpine?.store('toast')?.error(error.message || 'Notifikasi belum berhasil diaktifkan.');
            }
        }, { once: true });
    } catch (error) {
        button.textContent = 'Notifikasi belum siap';
        button.disabled = true;
        setStatus(error.message || 'Service worker push gagal disiapkan.');
        console.warn('[Ticketing] gagal menyiapkan push:', error);
    }
}

function initTicketPushButtons() {
    document.querySelectorAll('[data-ticket-push]:not([data-push-ready])').forEach((button) => {
        button.dataset.pushReady = '1';
        setupTicketPush(button);
    });
}

document.addEventListener('DOMContentLoaded', initTicketPushButtons);
document.addEventListener('livewire:navigated', initTicketPushButtons);

function initTicketRealtimeNotifications() {
    if (!window.Echo) return;

    document.querySelectorAll('[data-ticket-push-notice]:not([data-realtime-ready])').forEach((panel) => {
        const userId = panel.dataset.ticketUserId;
        if (!userId) return;

        panel.dataset.realtimeReady = '1';
        window.Echo.private(`it-ticket.user.${userId}`)
            .listen('.ItTicketNotification', (notification) => {
                const message = [notification.title, notification.body].filter(Boolean).join(' · ');
                window.Alpine?.store('toast')?.info(message || 'Ada pembaruan Ticketing.');
            });
    });
}

document.addEventListener('DOMContentLoaded', initTicketRealtimeNotifications);
document.addEventListener('livewire:navigated', initTicketRealtimeNotifications);

function initChatRealtimeNotifications() {
    const anchor = document.querySelector('#chat-realtime-anchor[data-chat-user-id]');
    if (!anchor || !window.Echo) return;

    const userId = anchor.dataset.chatUserId;
    if (!userId || anchor.dataset.chatRealtimeReady === '1') return;
    anchor.dataset.chatRealtimeReady = '1';

    window.Echo.private(`chat.user.${userId}`)
        .listen('.ChatMessageNotification', (notification) => {
            window.Livewire?.dispatch('chat-unread-refresh');

            const currentUrl = new URL(window.location.href);
            const chatUrl = new URL(anchor.dataset.chatUrl, window.location.origin);
            const isCurrentConversation = currentUrl.pathname === chatUrl.pathname
                && currentUrl.searchParams.get('user') === String(notification.sender_id);
            const targetUrl = new URL(notification.url || chatUrl.href, window.location.origin);
            const isVisible = document.visibilityState === 'visible';

            if (isCurrentConversation && isVisible) {
                window.Livewire?.dispatch('chat-message-received');
            }

            if ('Notification' in window && Notification.permission === 'granted' && (!isCurrentConversation || !isVisible)) {
                try {
                    const browserNotification = new Notification(`Pesan baru dari ${notification.sender_name}`, {
                        body: notification.body || 'Anda menerima pesan baru.',
                        icon: '/pwa/icon-192.png',
                        tag: `chat-message-${notification.message_id}`,
                        data: { url: targetUrl.href },
                    });
                    browserNotification.onclick = () => {
                        window.focus();
                        window.location.assign(targetUrl.href);
                    };
                } catch (_) {
                    // Service worker push remains available in browsers that restrict page notifications.
                }
            }
        })
        .listen('.ChatCallSignal', (signal) => {
            window.dispatchEvent(new CustomEvent('chat:call-signal', { detail: signal }));
        });
}

document.addEventListener('DOMContentLoaded', initChatRealtimeNotifications);
document.addEventListener('livewire:navigated', initChatRealtimeNotifications);

async function setupChatPush(button) {
    const panel = button.closest('[data-chat-push-notice]');
    const status = panel?.querySelector('[data-chat-push-status]');
    const setStatus = (message) => { if (status) status.textContent = message; };
    const waitWithTimeout = (promise, milliseconds, message) => new Promise((resolve, reject) => {
        const timer = window.setTimeout(() => reject(new Error(message)), milliseconds);
        promise.then((value) => {
            window.clearTimeout(timer);
            resolve(value);
        }, (error) => {
            window.clearTimeout(timer);
            reject(error);
        });
    });
    const saveSubscription = async (subscription) => fetch(button.dataset.subscribeUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify(subscription.toJSON()),
    });

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        button.textContent = 'Tidak didukung';
        button.disabled = true;
        setStatus('Browser ini tidak mendukung notifikasi push.');
        return;
    }

    button.disabled = true;
    button.textContent = 'Memeriksa...';
    setStatus('Memeriksa dukungan notifikasi pada browser ini...');

    try {
        const response = await waitWithTimeout(
            fetch(button.dataset.configUrl, { headers: { Accept: 'application/json' } }),
            10000,
            'Permintaan konfigurasi notifikasi melewati batas waktu.'
        );
        if (!response.ok) throw new Error('Konfigurasi notifikasi gagal dimuat.');
        const config = await response.json();
        if (!config.available) {
            button.textContent = 'Belum tersedia';
            button.disabled = true;
            setStatus('Push browser belum dikonfigurasi pada server.');
            return;
        }

        const workerUrl = import.meta.env.DEV ? '/push-sw.js' : '/sw.js';
        setStatus('Menyiapkan service worker notifikasi...');
        await waitWithTimeout(
            navigator.serviceWorker.register(workerUrl, { scope: '/', updateViaCache: 'none' }),
            10000,
            'Pendaftaran service worker melewati batas waktu.'
        );
        setStatus('Menunggu service worker aktif...');
        const registration = await waitWithTimeout(
            navigator.serviceWorker.ready,
            12000,
            'Service worker belum aktif. Muat ulang halaman lalu coba lagi.'
        );
        const existing = await registration.pushManager.getSubscription();
        button.disabled = false;
        button.textContent = existing && Notification.permission === 'granted' ? 'Notifikasi aktif' : 'Aktifkan';
        if (existing && Notification.permission === 'granted') {
            const saved = await saveSubscription(existing);
            if (saved.ok) {
                button.disabled = true;
                setStatus('Pesan baru akan muncul sebagai notifikasi browser.');
                return;
            }
        }

        setStatus(Notification.permission === 'denied'
            ? 'Izin diblokir. Izinkan notifikasi untuk situs ini melalui setelan browser.'
            : 'Aktifkan agar pesan baru muncul saat chat tidak sedang dibuka.');

        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                let permission = Notification.permission;
                if (permission === 'default') permission = await Notification.requestPermission();
                if (permission !== 'granted') throw new Error('Izin notifikasi browser belum diberikan.');
                const subscription = existing || await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: decodeVapidKey(config.publicKey),
                });
                const saved = await saveSubscription(subscription);
                if (!saved.ok) throw new Error('Langganan notifikasi gagal disimpan.');
                button.textContent = 'Notifikasi aktif';
                button.disabled = true;
                setStatus('Pesan baru akan muncul sebagai notifikasi browser.');
            } catch (error) {
                button.disabled = false;
                setStatus(error.message || 'Notifikasi belum berhasil diaktifkan.');
                window.Alpine?.store('toast')?.error(error.message || 'Notifikasi belum berhasil diaktifkan.');
            }
        });
    } catch (error) {
        button.textContent = 'Coba lagi';
        button.disabled = false;
        setStatus(error.message || 'Service worker notifikasi gagal disiapkan.');
        button.addEventListener('click', () => setupChatPush(button), { once: true });
    }
}

function initChatPushButtons() {
    document.querySelectorAll('[data-chat-push]:not([data-push-ready])').forEach((button) => {
        button.dataset.pushReady = '1';
        setupChatPush(button);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChatPushButtons, { once: true });
} else {
    initChatPushButtons();
}
document.addEventListener('livewire:navigated', initChatPushButtons);

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
import './chat-calls';

if (import.meta.env.PROD && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', {
            scope: '/',
            updateViaCache: 'none',
        }).catch(() => undefined);
    }, { once: true });
}
