self.addEventListener('install', (event) => {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
  let payload = {};
  try {
    payload = event.data ? event.data.json() : {};
  } catch {
    payload = { title: event.data ? event.data.text() : undefined };
  }

  const title = payload.title || 'AtoZ';
  const notificationData = payload.data || {};
  const url = payload.url || notificationData.url || '/';

  const options = {
    body: payload.body,
    data: { ...notificationData, url },
    icon: payload.icon || '/favicon.ico',
    badge: payload.badge,
    tag: payload.tag,
    renotify: Boolean(payload.renotify),
    requireInteraction: Boolean(payload.requireInteraction),
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();

  const url = event.notification?.data?.url || '/';

  event.waitUntil(
    self.clients
      .matchAll({ type: 'window', includeUncontrolled: true })
      .then((clientList) => {
        for (const client of clientList) {
          if (client.url === url && 'focus' in client) {
            return client.focus();
          }
        }

        for (const client of clientList) {
          if ('navigate' in client && 'focus' in client) {
            return client.navigate(url).then(() => client.focus());
          }
        }

        if (self.clients.openWindow) {
          return self.clients.openWindow(url);
        }

        return undefined;
      })
  );
});
