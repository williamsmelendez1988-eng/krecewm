const CACHE_NAME = 'krecewm-cache-v1';
const urlsToCache = [
    '/',
    '/favicon.ico',
    'https://cdn.tailwindcss.com',
    'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
    'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap'
];

// Instalar el Service Worker y almacenar recursos en caché
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(urlsToCache))
    );
});

// Activar el Service Worker y limpiar cachés antiguas
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Interceptar peticiones y responder desde caché o red
self.addEventListener('fetch', event => {
    // No interceptar peticiones que no sean GET, ni las llamadas de LiveReload de Vite/BrowserSync
    if (event.request.method !== 'GET' || event.request.url.includes('browser-sync') || event.request.url.includes('/admin/')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(event.request)
                    .then(response => {
                        // Solo cachear respuestas exitosas de origen local o fuentes CDN confiables
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, responseToCache);
                        });

                        return response;
                    })
                    .catch(() => {
                        // Retornar fallback para peticiones de navegación de páginas si falla la red
                        if (event.request.headers.get('accept').includes('text/html')) {
                            return caches.match('/');
                        }
                    });
            })
    );
});
