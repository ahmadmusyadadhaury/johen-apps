/* Push event handlers imported by the generated Workbox service worker. */
self.addEventListener('push', (event) => {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (_) {
        data = { body: event.data ? event.data.text() : '' };
    }

    event.waitUntil((async () => {
        if (String(data.tag || '').startsWith('chat-message-')) {
            const clients = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
            const visibleClient = clients.some((client) => {
                try {
                    return client.visibilityState === 'visible'
                        && new URL(client.url).origin === self.location.origin;
                } catch (_) {
                    return false;
                }
            });

            // The open page receives the same message over Reverb and shows its own notification.
            if (visibleClient) return;
        }

        return self.registration.showNotification(data.title || 'Johen Apps', {
            body: data.body || 'Ada pembaruan yang perlu diperiksa.',
            icon: '/pwa/icon-192.png',
            badge: '/pwa/icon-192.png',
            tag: data.tag || 'johen-apps-notification',
            data: { url: data.url || '/it/tickets' },
        });
    })());
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
