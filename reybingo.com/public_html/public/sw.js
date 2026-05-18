/**
 * Service Worker mejorado para notificaciones push
 * Versión: 3.0
 */

// Versión del cache para actualizaciones
const CACHE_VERSION = 'v3';
const CACHE_NAME = `bingo-cache-${CACHE_VERSION}`;

console.log('🔧 Service Worker cargado - Version 3.0');

// Archivos a cachear inicialmente
const INITIAL_CACHED_RESOURCES = [
    '/',
    '/assets/img/logo.png',
    '/assets/img/badge.png',
    '/assets/css/main.css',
    '/assets/js/app.js'
];

// Instalación del Service Worker
self.addEventListener('install', function(event) {
    console.log('⚙️ Service Worker instalando...');
    
    // Cachear archivos iniciales
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('📦 Cacheando recursos iniciales');
                return cache.addAll(INITIAL_CACHED_RESOURCES);
            })
            .then(() => {
                console.log('✅ Recursos iniciales cacheados correctamente');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('❌ Error cacheando recursos iniciales:', error);
            })
    );
});

// Activación del Service Worker
self.addEventListener('activate', function(event) {
    console.log('🚀 Service Worker activado');
    
    // Limpiar caches antiguas
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.filter(cacheName => {
                        return cacheName.startsWith('bingo-cache-') && cacheName !== CACHE_NAME;
                    }).map(cacheName => {
                        console.log('🗑️ Eliminando cache antigua:', cacheName);
                        return caches.delete(cacheName);
                    })
                );
            })
            .then(() => {
                console.log('✅ Caches antiguas eliminadas');
                return self.clients.claim();
            })
    );
});

// Evento push para notificaciones
self.addEventListener('push', function(event) {
    console.log('📨 PUSH EVENT RECIBIDO!', event);
    
    // Configuración por defecto para la notificación
    let notificationData = {
        title: 'Notificación de Bingo',
        body: 'Nueva notificación disponible',
        icon: '/assets/img/logo.png',
        badge: '/assets/img/badge.png',
        tag: 'bingo-notification',
        requireInteraction: true,
        vibrate: [200, 100, 200],
        data: {
            url: '/',
            timestamp: Date.now()
        }
    };

    // Procesar datos si existen
    if (event.data) {
        try {
            const data = event.data.json();
            console.log('📋 Datos recibidos (JSON):', data);
            
            // Actualizar datos de la notificación
            notificationData.title = data.title || notificationData.title;
            notificationData.body = data.message || data.body || notificationData.body;
            
            // Manejar URL y otros datos
            if (data.url) {
                notificationData.data = { 
                    ...notificationData.data,
                    url: data.url 
                };
            }
            
            // Manejar otros campos si existen
            if (data.icon) notificationData.icon = data.icon;
            if (data.badge) notificationData.badge = data.badge;
            if (data.tag) notificationData.tag = data.tag;
            if (data.vibrate) notificationData.vibrate = data.vibrate;
            if (data.requireInteraction !== undefined) notificationData.requireInteraction = data.requireInteraction;
            
            // Manejar acciones personalizadas
            if (data.actions && Array.isArray(data.actions)) {
                notificationData.actions = data.actions;
            }
            
        } catch (e) {
            console.error('❌ Error parseando datos JSON:', e);
            
            // Intentar como texto plano
            try {
                const textData = event.data.text();
                console.log('📋 Datos recibidos (texto):', textData);
                notificationData.body = textData || notificationData.body;
            } catch (textError) {
                console.error('❌ Error obteniendo texto:', textError);
            }
        }
    } else {
        console.log('📭 Push sin datos - usando valores predeterminados');
    }

    console.log('🔔 Mostrando notificación:', notificationData);

    // Mostrar la notificación
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            vibrate: notificationData.vibrate,
            data: notificationData.data || {},
            actions: notificationData.actions || [
                {
                    action: 'view',
                    title: '👀 Ver'
                },
                {
                    action: 'close',
                    title: '❌ Cerrar'
                }
            ],
            // Añadir timestamp para evitar problemas de caché
            timestamp: Date.now()
        })
        .then(() => {
            console.log('✅ Notificación mostrada exitosamente');
            
            // Registrar analíticas de notificación mostrada
            return self.registration.getNotifications()
                .then(notifications => {
                    console.log(`📊 Total de notificaciones activas: ${notifications.length}`);
                });
        })
        .catch((error) => {
            console.error('❌ Error mostrando notificación:', error);
        })
    );
});

// Manejar clics en notificaciones
self.addEventListener('notificationclick', function(event) {
    console.log('🖱️ Notificación clickeada:', event);
    
    // Cerrar la notificación
    event.notification.close();

    // Determinar la URL a abrir
    let targetUrl = '/';
    
    // Si hay datos en la notificación, usar la URL de ahí
    if (event.notification.data && event.notification.data.url) {
        targetUrl = event.notification.data.url;
    }
    
    // Si se hizo clic en una acción específica
    if (event.action === 'view') {
        console.log('👁️ Acción "Ver" seleccionada');
    } else if (event.action === 'close') {
        console.log('🚫 Acción "Cerrar" seleccionada');
        return; // No abrir ninguna ventana
    }
    
    // Abrir o enfocar una ventana existente
    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        .then(clientList => {
            // Verificar si ya hay una ventana abierta con la URL
            for (const client of clientList) {
                const url = new URL(client.url);
                const targetUrlObj = new URL(targetUrl, self.location.origin);
                
                // Si la URL coincide o es la página principal
                if (url.pathname === targetUrlObj.pathname || client.url === self.location.origin + '/') {
                    console.log('🔍 Ventana existente encontrada, enfocando');
                    return client.focus();
                }
            }
            
            // Si no hay ventana abierta, abrir una nueva
            console.log('🔗 Abriendo nueva ventana:', targetUrl);
            return clients.openWindow(targetUrl);
        })
        .catch(error => {
            console.error('❌ Error manejando clic en notificación:', error);
        })
    );
});

// Manejar cierre de notificaciones
self.addEventListener('notificationclose', function(event) {
    console.log('🚫 Notificación cerrada por el usuario:', event);
    
    // Aquí podrías registrar analíticas de notificaciones cerradas
});

// Listener para mensajes del cliente
self.addEventListener('message', function(event) {
    console.log('💬 Mensaje recibido en SW:', event.data);
    
    // Manejar diferentes tipos de mensajes
    if (event.data) {
        switch (event.data.type) {
            case 'TEST_NOTIFICATION':
                console.log('🧪 Solicitud de notificación de prueba recibida');
                
                self.registration.showNotification('🧪 Test Manual', {
                    body: 'Esta es una notificación de prueba desde el Service Worker',
                    icon: '/assets/img/logo.png',
                    tag: 'test-notification',
                    data: {
                        url: '/',
                        timestamp: Date.now()
                    }
                });
                break;
                
            case 'SKIP_WAITING':
                console.log('⏭️ Solicitud para skipWaiting recibida');
                self.skipWaiting();
                break;
                
            case 'CLEAR_CACHE':
                console.log('🧹 Solicitud para limpiar cache recibida');
                caches.keys().then(cacheNames => {
                    return Promise.all(
                        cacheNames.map(cacheName => {
                            return caches.delete(cacheName);
                        })
                    );
                });
                break;
                
            default:
                console.log('📝 Mensaje no reconocido:', event.data);
        }
    }
});

// Estrategia de cache para solicitudes fetch
self.addEventListener('fetch', function(event) {
    // Solo cachear solicitudes GET
    if (event.request.method !== 'GET') return;
    
    // No cachear solicitudes a APIs o servicios externos
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api/') || 
        url.pathname.startsWith('/notifications/') ||
        !url.hostname.includes(self.location.hostname)) {
        return;
    }
    
    // Estrategia: Network first, fallback to cache
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Guardar una copia en cache
                const responseClone = response.clone();
                caches.open(CACHE_NAME)
                    .then(cache => {
                        cache.put(event.request, responseClone);
                    });
                return response;
            })
            .catch(() => {
                // Si falla la red, intentar desde cache
                return caches.match(event.request);
            })
    );
});

// Sincronización en segundo plano (para operaciones offline)
self.addEventListener('sync', function(event) {
    console.log('🔄 Evento de sincronización:', event);
    
    if (event.tag === 'sync-notifications') {
        console.log('🔄 Sincronizando notificaciones pendientes');
        // Aquí podrías implementar lógica para reenviar notificaciones fallidas
    }
});

console.log('✅ Service Worker inicializado completamente');