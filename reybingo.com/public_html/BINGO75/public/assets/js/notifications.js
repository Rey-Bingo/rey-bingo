class NotificationManager {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
        this.hasAskedPermission = localStorage.getItem('notification-asked') === 'true';
        // Clave pública directa para evitar problemas de fetch
        this.vapidPublicKey = 'BHdM69ue9dsb7wxXHWJrea_2F45sWLUd2hx33imtzLotBrIeqReJWcSHW48hCkL7iU5XGns96G7G6ViIC6MC8nI';
        this.init();
    }

    async init() {
        if (!this.isSupported) {
            console.log('Push notifications no soportadas');
            return;
        }

        try {
            // Registrar service worker
            this.registration = await navigator.serviceWorker.register('/sw.js');
            console.log('✅ Service Worker registrado');

            // Esperar a que esté listo
            await navigator.serviceWorker.ready;

            // Verificar estado actual
            await this.checkNotificationStatus();
            
            // Mostrar banner si es necesario
            this.showBannerIfNeeded();
            
        } catch (error) {
            console.error('❌ Error registrando Service Worker:', error);
            this.showAlert('Error inicializando notificaciones: ' + error.message, 'error');
        }
    }

    async checkNotificationStatus() {
        if (!this.registration) return { permission: 'default', subscribed: false };

        try {
            const permission = Notification.permission;
            const subscription = await this.registration.pushManager.getSubscription();
            
            console.log('Estado actual:', { permission, subscribed: !!subscription });
            
            if (permission === 'granted' && !subscription) {
                // Tiene permiso pero no está suscrito, suscribir automáticamente
                await this.subscribe();
            }
            
            return {
                permission: permission,
                subscribed: !!subscription
            };
        } catch (error) {
            console.error('Error verificando estado:', error);
            return { permission: 'default', subscribed: false };
        }
    }

    showBannerIfNeeded() {
        const permission = Notification.permission;
        
        // Mostrar banner si no ha dado permiso y no hemos preguntado antes
        if (permission === 'default' && !this.hasAskedPermission) {
            setTimeout(() => {
                const banner = document.getElementById('notification-banner');
                if (banner) {
                    banner.style.display = 'block';
                    document.body.style.paddingTop = '120px';
                }
            }, 3000);
        }
    }

    async requestPermission(showModal = false) {
        if (!this.isSupported) {
            this.showAlert('Tu navegador no soporta notificaciones push', 'warning');
            return false;
        }

        // Marcar que ya preguntamos
        localStorage.setItem('notification-asked', 'true');
        this.hasAskedPermission = true;

        // Mostrar modal explicativo si se solicita
        if (showModal && document.getElementById('notification-modal')) {
            return new Promise((resolve) => {
                const modal = new bootstrap.Modal(document.getElementById('notification-modal'));
                modal.show();
                
                const confirmBtn = document.getElementById('confirm-notifications');
                if (confirmBtn) {
                    confirmBtn.onclick = async () => {
                        modal.hide();
                        const result = await this.requestBrowserPermission();
                        resolve(result);
                    };
                } else {
                    // Si no hay modal, proceder directamente
                    modal.hide();
                    resolve(this.requestBrowserPermission());
                }
            });
        } else {
            return await this.requestBrowserPermission();
        }
    }

    async requestBrowserPermission() {
        try {
            console.log('🔔 Solicitando permisos...');
            const permission = await Notification.requestPermission();
            console.log('Permiso obtenido:', permission);
            
            if (permission === 'granted') {
                const subscribed = await this.subscribe();
                if (subscribed) {
                    this.showAlert('¡Notificaciones activadas! Ahora recibirás alertas de nuevos juegos', 'success');
                    this.hideBanner();
                    return true;
                } else {
                    this.showAlert('Error al configurar la suscripción', 'error');
                    return false;
                }
            } else if (permission === 'denied') {
                this.showAlert('Notificaciones bloqueadas. Puedes activarlas desde la configuración de tu navegador', 'warning');
                this.hideBanner();
                return false;
            } else {
                this.showAlert('Necesitas permitir las notificaciones para recibir alertas', 'info');
                return false;
            }
        } catch (error) {
            console.error('❌ Error solicitando permisos:', error);
            this.showAlert('Error al activar notificaciones: ' + error.message, 'error');
            return false;
        }
    }

    async subscribe() {
        if (!this.registration) {
            console.error('❌ Service Worker no registrado');
            return false;
        }

        try {
            console.log('🔄 Iniciando suscripción...');

            // Usar clave pública directa en lugar de fetch
            const publicKey = this.vapidPublicKey;
            console.log('📋 Usando clave pública:', publicKey);

            // Crear suscripción
            console.log('🔄 Creando suscripción push...');
            const subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(publicKey)
            });

            console.log('✅ Suscripción creada:', subscription);

            // Enviar suscripción al servidor
            console.log('🔄 Enviando suscripción al servidor...');
            const response = await fetch('/notifications/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(subscription)
            });

            console.log('📡 Respuesta del servidor:', response.status);

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);
            }

            const result = await response.json();
            console.log('📋 Resultado:', result);

            if (result.success) {
                console.log('✅ Suscripción guardada correctamente');
                this.sendTestNotification();
                return true;
            } else {
                throw new Error(result.message || 'Error desconocido del servidor');
            }

        } catch (error) {
            console.error('❌ Error en subscribe:', error);
            
            // Mostrar error específico
            let errorMessage = 'Error al configurar notificaciones';
            if (error.message.includes('not allowed')) {
                errorMessage = 'Permisos de notificación denegados';
            } else if (error.message.includes('network')) {
                errorMessage = 'Error de conexión al servidor';
            } else if (error.message.includes('servidor')) {
                errorMessage = error.message;
            }
            
            this.showAlert(errorMessage + ': ' + error.message, 'error');
            return false;
        }
    }

    sendTestNotification() {
        // Enviar notificación de bienvenida
        if (Notification.permission === 'granted') {
            try {
                new Notification('🎉 ¡Notificaciones activadas!', {
                    body: 'Ahora recibirás alertas de nuevas partidas de bingo',
                    icon: '/assets/img/logo.png',
                    tag: 'welcome',
                    requireInteraction: false
                });
            } catch (error) {
                console.log('No se pudo mostrar notificación de prueba:', error);
            }
        }
    }

    hideBanner() {
        const banner = document.getElementById('notification-banner');
        if (banner) {
            banner.style.display = 'none';
            document.body.style.paddingTop = '0';
        }
    }

    showAlert(message, type = 'info') {
        console.log(`Alert [${type}]:`, message);
        
        // Crear alerta personalizada
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show notification-alert`;
        alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; word-wrap: break-word;';
        alertDiv.innerHTML = `
            <strong>${type === 'error' ? '❌' : type === 'success' ? '✅' : type === 'warning' ? '⚠️' : 'ℹ️'}</strong>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remover después de 8 segundos para errores, 5 para otros
        const timeout = type === 'error' ? 8000 : 5000;
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, timeout);
    }

    urlBase64ToUint8Array(base64String) {
        try {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        } catch (error) {
            console.error('Error convirtiendo clave VAPID:', error);
            throw new Error('Clave VAPID inválida');
        }
    }
}

// Inicializar cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando NotificationManager...');
    window.notificationManager = new NotificationManager();
    
    // Event listeners para los botones
    const enableBtn = document.getElementById('enable-notifications');
    if (enableBtn) {
        enableBtn.addEventListener('click', function() {
            console.log('🔔 Botón de activar notificaciones clickeado');
            window.notificationManager.requestPermission(false); // Sin modal por ahora
        });
    }
    
    const dismissBtn = document.getElementById('dismiss-banner');
    if (dismissBtn) {
        dismissBtn.addEventListener('click', function() {
            window.notificationManager.hideBanner();
            localStorage.setItem('notification-dismissed', 'true');
        });
    }
});

// Función global para testing
function testNotifications() {
    if (window.notificationManager) {
        window.notificationManager.requestPermission(false);
    }
}


/**
 * Script de depuración para notificaciones push
 * Incluye este script en tu página para diagnosticar problemas con las notificaciones push
 */

class PushNotificationDebugger {
    constructor() {
        this.debugInfo = {
            browserSupport: this.checkBrowserSupport(),
            serviceWorkerSupport: 'serviceWorker' in navigator,
            pushManagerSupport: 'PushManager' in window,
            notificationSupport: 'Notification' in window,
            permission: this.getPermissionStatus(),
            serviceWorkerStatus: null,
            subscription: null,
            vapidPublicKey: null
        };
        
        this.init();
    }
    
    async init() {
        console.group('🔍 Diagnóstico de Notificaciones Push');
        
        // Verificar soporte básico
        console.log('Soporte del navegador:', this.debugInfo.browserSupport);
        console.log('Soporte de Service Worker:', this.debugInfo.serviceWorkerSupport);
        console.log('Soporte de Push Manager:', this.debugInfo.pushManagerSupport);
        console.log('Soporte de Notificaciones:', this.debugInfo.notificationSupport);
        console.log('Estado de permisos:', this.debugInfo.permission);
        
        if (!this.debugInfo.serviceWorkerSupport || !this.debugInfo.pushManagerSupport) {
            console.error('❌ Tu navegador no soporta notificaciones push');
            console.groupEnd();
            return;
        }
        
        // Verificar Service Worker
        await this.checkServiceWorker();
        
        // Verificar suscripción
        await this.checkSubscription();
        
        // Verificar clave VAPID
        this.checkVapidKey();
        
        // Mostrar resumen
        this.showSummary();
        
        console.groupEnd();
    }
    
    checkBrowserSupport() {
        const browser = this.detectBrowser();
        const isSupported = ['chrome', 'firefox', 'edge', 'opera'].includes(browser.name.toLowerCase());
        
        return {
            name: browser.name,
            version: browser.version,
            isSupported: isSupported
        };
    }
    
    detectBrowser() {
        const userAgent = navigator.userAgent;
        let name = 'Unknown';
        let version = 'Unknown';
        
        if (userAgent.indexOf('Firefox') > -1) {
            name = 'Firefox';
            version = userAgent.match(/Firefox\/([0-9.]+)/)[1];
        } else if (userAgent.indexOf('Edg') > -1) {
            name = 'Edge';
            version = userAgent.match(/Edg\/([0-9.]+)/)[1];
        } else if (userAgent.indexOf('Chrome') > -1) {
            name = 'Chrome';
            version = userAgent.match(/Chrome\/([0-9.]+)/)[1];
        } else if (userAgent.indexOf('Safari') > -1) {
            name = 'Safari';
            version = userAgent.match(/Version\/([0-9.]+)/)[1];
        } else if (userAgent.indexOf('Opera') > -1 || userAgent.indexOf('OPR') > -1) {
            name = 'Opera';
            version = userAgent.match(/(?:Opera|OPR)\/([0-9.]+)/)[1];
        }
        
        return { name, version };
    }
    
    getPermissionStatus() {
        return Notification.permission;
    }
    
    async checkServiceWorker() {
        try {
            // Verificar si hay un Service Worker registrado
            const registrations = await navigator.serviceWorker.getRegistrations();
            
            if (registrations.length === 0) {
                console.error('❌ No hay Service Workers registrados');
                this.debugInfo.serviceWorkerStatus = {
                    registered: false,
                    count: 0
                };
                return;
            }
            
            // Buscar el Service Worker para notificaciones push
            let pushServiceWorker = null;
            for (const reg of registrations) {
                console.log(`Service Worker registrado en: ${reg.scope}`);
                
                // Verificar si este Service Worker maneja eventos push
                const sw = reg.active || reg.installing || reg.waiting;
                if (sw) {
                    // No podemos verificar los event listeners directamente
                    // Asumimos que si el scope es la raíz, podría ser nuestro SW de push
                    if (reg.scope.endsWith('/')) {
                        pushServiceWorker = reg;
                    }
                }
            }
            
            if (pushServiceWorker) {
                console.log('✅ Service Worker para push encontrado:', pushServiceWorker);
                this.debugInfo.serviceWorkerStatus = {
                    registered: true,
                    count: registrations.length,
                    pushServiceWorker: {
                        scope: pushServiceWorker.scope,
                        state: pushServiceWorker.active ? 'active' : 
                               pushServiceWorker.installing ? 'installing' : 
                               pushServiceWorker.waiting ? 'waiting' : 'unknown'
                    }
                };
            } else {
                console.warn('⚠️ Service Workers registrados, pero ninguno parece manejar push');
                this.debugInfo.serviceWorkerStatus = {
                    registered: true,
                    count: registrations.length,
                    pushServiceWorker: null
                };
            }
        } catch (error) {
            console.error('❌ Error verificando Service Workers:', error);
            this.debugInfo.serviceWorkerStatus = {
                registered: false,
                error: error.message
            };
        }
    }
    
    async checkSubscription() {
        try {
            // Obtener el registro del Service Worker
            const registration = await navigator.serviceWorker.ready;
            
            // Obtener la suscripción actual
            const subscription = await registration.pushManager.getSubscription();
            
            if (!subscription) {
                console.warn('⚠️ No hay suscripción activa');
                this.debugInfo.subscription = null;
                return;
            }
            
            console.log('✅ Suscripción activa encontrada:', subscription);
            
            // Extraer información relevante
            const endpoint = subscription.endpoint;
            const p256dh = subscription.getKey ? 
                btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : 
                'No disponible';
            const auth = subscription.getKey ? 
                btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : 
                'No disponible';
            
            this.debugInfo.subscription = {
                endpoint,
                keys: {
                    p256dh: p256dh,
                    auth: auth
                },
                expirationTime: subscription.expirationTime
            };
            
            // Verificar si el endpoint es válido
            if (endpoint.includes('fcm.googleapis.com')) {
                console.log('📱 Usando Firebase Cloud Messaging (FCM)');
            } else if (endpoint.includes('push.services.mozilla.com')) {
                console.log('🦊 Usando Mozilla Push Service');
            } else if (endpoint.includes('web.push.apple.com')) {
                console.log('🍎 Usando Apple Push Service');
            }
            
        } catch (error) {
            console.error('❌ Error verificando suscripción:', error);
            this.debugInfo.subscription = {
                error: error.message
            };
        }
    }
    
    checkVapidKey() {
        // Intentar obtener la clave VAPID del objeto global o del localStorage
        let vapidKey = null;
        
        if (window.notificationManager && window.notificationManager.vapidPublicKey) {
            vapidKey = window.notificationManager.vapidPublicKey;
        }
        
        if (!vapidKey && window.localStorage) {
            vapidKey = localStorage.getItem('vapid_public_key');
        }
        
        if (vapidKey) {
            console.log('✅ Clave VAPID pública encontrada:', vapidKey);
            
            // Verificar formato de la clave
            const isValidFormat = /^[A-Za-z0-9_-]+$/.test(vapidKey);
            if (!isValidFormat) {
                console.warn('⚠️ El formato de la clave VAPID parece incorrecto');
            }
            
            this.debugInfo.vapidPublicKey = {
                key: vapidKey,
                validFormat: isValidFormat
            };
        } else {
            console.warn('⚠️ No se encontró la clave VAPID pública');
            this.debugInfo.vapidPublicKey = null;
        }
    }
    
    showSummary() {
        console.group('📊 Resumen de diagnóstico');
        
        // Verificar soporte básico
        const hasBasicSupport = this.debugInfo.serviceWorkerSupport && 
                               this.debugInfo.pushManagerSupport && 
                               this.debugInfo.notificationSupport;
        
        console.log('Soporte básico:', hasBasicSupport ? '✅ Soportado' : '❌ No soportado');
        
        // Verificar permisos
        console.log('Permisos:', 
            this.debugInfo.permission === 'granted' ? '✅ Concedidos' : 
            this.debugInfo.permission === 'denied' ? '❌ Denegados' : 
            '⚠️ No solicitados');
        
        // Verificar Service Worker
        const hasServiceWorker = this.debugInfo.serviceWorkerStatus && 
                                this.debugInfo.serviceWorkerStatus.registered;
        
        console.log('Service Worker:', hasServiceWorker ? '✅ Registrado' : '❌ No registrado');
        
        // Verificar suscripción
        const hasSubscription = this.debugInfo.subscription && !this.debugInfo.subscription.error;
        
        console.log('Suscripción:', hasSubscription ? '✅ Activa' : '❌ No activa');
        
        // Verificar clave VAPID
        const hasVapidKey = this.debugInfo.vapidPublicKey && this.debugInfo.vapidPublicKey.validFormat;
        
        console.log('Clave VAPID:', hasVapidKey ? '✅ Válida' : '❌ No válida o no encontrada');
        
        // Estado general
        const isFullyFunctional = hasBasicSupport && 
                                 this.debugInfo.permission === 'granted' && 
                                 hasServiceWorker && 
                                 hasSubscription && 
                                 hasVapidKey;
        
        console.log('Estado general:', isFullyFunctional ? 
            '✅ Todo configurado correctamente' : 
            '❌ Hay problemas que resolver');
        
        // Recomendaciones
        if (!isFullyFunctional) {
            console.group('🔧 Recomendaciones');
            
            if (!hasBasicSupport) {
                console.log('- Usa un navegador compatible como Chrome, Firefox, Edge u Opera');
            }
            
            if (this.debugInfo.permission === 'denied') {
                console.log('- El usuario ha bloqueado los permisos. Debe habilitarlos en la configuración del navegador');
            } else if (this.debugInfo.permission === 'default') {
                console.log('- Solicita permisos al usuario con Notification.requestPermission()');
            }
            
            if (!hasServiceWorker) {
                console.log('- Registra el Service Worker correctamente');
            }
            
            if (!hasSubscription) {
                console.log('- Crea una suscripción con pushManager.subscribe()');
            }
            
            if (!hasVapidKey) {
                console.log('- Verifica que la clave VAPID pública sea correcta');
            }
            
            console.groupEnd();
        }
        
        console.groupEnd();
        
        // Devolver objeto con toda la información para uso externo
        return {
            isFullyFunctional,
            details: this.debugInfo
        };
    }
    
    // Método para probar una notificación local (no push)
    testLocalNotification() {
        if (!('Notification' in window)) {
            console.error('❌ Este navegador no soporta notificaciones');
            return false;
        }
        
        if (Notification.permission === 'granted') {
            try {
                const notification = new Notification('🧪 Notificación de prueba local', {
                    body: 'Esta es una notificación local (no push) para verificar permisos',
                    icon: '/assets/img/logo.png'
                });
                
                notification.onclick = function() {
                    console.log('Notificación clickeada');
                    notification.close();
                };
                
                console.log('✅ Notificación local mostrada correctamente');
                return true;
            } catch (error) {
                console.error('❌ Error mostrando notificación local:', error);
                return false;
            }
        } else if (Notification.permission === 'denied') {
            console.error('❌ Permisos de notificación denegados');
            return false;
        } else {
            console.warn('⚠️ Permisos de notificación no solicitados');
            return false;
        }
    }
    
    // Método para enviar mensaje al Service Worker
    async testServiceWorkerMessage() {
        if (!navigator.serviceWorker.controller) {
            console.error('❌ No hay Service Worker controlando esta página');
            return false;
        }
        
        try {
            navigator.serviceWorker.controller.postMessage({
                type: 'TEST_NOTIFICATION',
                timestamp: Date.now()
            });
            
            console.log('✅ Mensaje enviado al Service Worker');
            return true;
        } catch (error) {
            console.error('❌ Error enviando mensaje al Service Worker:', error);
            return false;
        }
    }
}

// Inicializar el depurador
document.addEventListener('DOMContentLoaded', function() {
    window.pushDebugger = new PushNotificationDebugger();
    
    console.log('🔍 Depurador de notificaciones push inicializado');
    console.log('Para probar una notificación local, ejecuta: window.pushDebugger.testLocalNotification()');
    console.log('Para enviar un mensaje al Service Worker, ejecuta: window.pushDebugger.testServiceWorkerMessage()');
});