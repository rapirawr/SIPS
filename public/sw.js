// Service Worker - Sarana Pengaduan Sekolah PWA
const CACHE_NAME = 'silapor-v1';
const STATIC_CACHE = 'silapor-static-v1';
const DYNAMIC_CACHE = 'silapor-dynamic-v1';

// Assets to cache immediately on install
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ─── Install ────────────────────────────────────────────────────────────────
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(STATIC_CACHE).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.warn('[SW] Pre-cache failed for some assets:', err);
            });
        })
    );
});

// ─── Activate ────────────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
    const allowedCaches = [STATIC_CACHE, DYNAMIC_CACHE];
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => !allowedCaches.includes(key))
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ─── Fetch ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET, cross-origin, and admin/api requests
    if (
        request.method !== 'GET' ||
        !url.origin.includes(self.location.origin) ||
        url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/dashboard') ||
        url.pathname.includes('livewire') ||
        url.pathname.includes('horizon')
    ) {
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts)
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.match(/\.(png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/)
    ) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Network-first for HTML pages (always fresh, fallback to cache/offline)
    event.respondWith(networkFirst(request));
});

// ─── Strategies ───────────────────────────────────────────────────────────────
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('', { status: 404 });
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Fallback to offline page for navigation requests
        if (request.mode === 'navigate') {
            const offline = await caches.match('/offline');
            if (offline) return offline;
        }

        return new Response(
            '<html><body><h1>Anda sedang offline</h1><p>Periksa koneksi internet Anda.</p></body></html>',
            { status: 503, headers: { 'Content-Type': 'text/html' } }
        );
    }
}

// ─── Push Notifications (ready for future use) ────────────────────────────────
self.addEventListener('push', event => {
    const data = event.data?.json() ?? {};
    const title = data.title || 'Sarana Pengaduan Sekolah';
    const options = {
        body: data.body || 'Ada pembaruan pada laporan Anda.',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-96x96.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/' },
        actions: [
            { action: 'view', title: 'Lihat Sekarang' },
            { action: 'dismiss', title: 'Tutup' }
        ]
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    if (event.action === 'view' || !event.action) {
        const url = event.notification.data?.url || '/';
        event.waitUntil(clients.openWindow(url));
    }
});
