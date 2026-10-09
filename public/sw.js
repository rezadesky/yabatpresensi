// Service Worker untuk PWA Offline & Installable Support
const CACHE_NAME = 'yabat-presensi-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Biarkan request live network berjalan normal untuk GPS real-time
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
