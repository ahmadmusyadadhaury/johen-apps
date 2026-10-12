/* Push event handlers imported by the generated Workbox service worker. */
self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (_) {
        data = { body: event.data ? event.data.text() : '' };
    }

    event.waitUntil(self.registration.showNotification(data.title || 'Johen Apps', {
        body: data.body || 'Ada deadline script yang perlu diselesaikan.',
        icon: '/pwa/icon-192.png',
        badge: '/pwa/icon-192.png',
        tag: data.tag || 'script-video-deadline',
        data: { url: data.url || '/hris/script-video' },
    }));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = new URL(event.notification.data?.url || '/hris/script-video', self.location.origin).href;

    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
        for (const client of clients) {
            if (new URL(client.url).origin === self.location.origin) {
                client.navigate(targetUrl);
                return client.focus();
            }
        }
        return self.clients.openWindow(targetUrl);
    }));
});
