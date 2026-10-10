// This is the "Offline page" service worker from PWABuilder

importScripts('https://storage.googleapis.com/workbox-cdn/releases/5.1.2/workbox-sw.js');

const CACHE = "pwabuilder-page";
const offlineFallbackPage = "/offline.html";

self.addEventListener("message", (event) => {
  if (event.data && event.data.type === "SKIP_WAITING") {
    self.skipWaiting();
  }
});

self.addEventListener('install', async (event) => {
  event.waitUntil(
    caches.open(CACHE)
      .then((cache) => cache.add(offlineFallbackPage))
  );
});

if (workbox.navigationPreload.isSupported()) {
  workbox.navigationPreload.enable();
}

self.addEventListener('fetch', (event) => {
  if (event.request.mode === 'navigate') {
    event.respondWith((async () => {
      try {
        const preloadResp = await event.preloadResponse;

        if (preloadResp) {
          return preloadResp;
        }

        const networkResp = await fetch(event.request);
        return networkResp;
      } catch (error) {
        const cache = await caches.open(CACHE);
        const cachedResp = await cache.match(offlineFallbackPage);
        return cachedResp;
      }
    })());
  }
});

// 1. Background Sync Handler
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-presensi') {
    event.waitUntil(Promise.resolve());
  }
});

// 2. Periodic Background Sync Handler
self.addEventListener('periodicsync', (event) => {
  if (event.tag === 'periodic-presensi') {
    event.waitUntil(Promise.resolve());
  }
});

// 3. Push Notifications Handler
self.addEventListener('push', (event) => {
  const data = event.data ? event.data.text() : 'Notifikasi YabatPresensi';
  event.waitUntil(
    self.registration.showNotification('YabatPresensi', {
      body: data,
      icon: '/icon-192.png',
      badge: '/icon-192.png'
    })
  );
});
