// ==========================================
// CONFIGURACIÓN Y CONSTANTES
// ==========================================
const CONFIG = {
    MAX_MESSAGES: 50,           // Aumentado para el nuevo sistema
    MAX_CONFETTI: 100,
    BASE_POLL_INTERVAL: 2000,
    MAX_POLL_INTERVAL: 10000,
    USER_COUNT_INTERVAL: 2500,
    ACCUMULATED_COUNT_INTERVAL: 2500,
    MESSAGE_LIFETIME: 30000,    // 30 segundos para mensajes
    FADE_OUT_TIME: 500,         // Tiempo de animación de desvanecimiento
    DEBOUNCE_DELAY: 100,
    AUDIO_POOL_SIZE: 10,
    MESSAGE_POOL_SIZE: 15,
    WINNER_SLIDER_INTERVAL: 5000,
    COUNTDOWN_INTERVAL: 1000,
    CACHE_TTL: 5000,            // Tiempo de vida de la caché (5 segundos)
    OFFLINE_STORAGE: true,      // Habilitar almacenamiento offline
    RETRY_ATTEMPTS: 3,          // Intentos de reintento para operaciones fallidas
    RETRY_DELAY: 1000           // Retraso entre reintentos en ms
};

// ==========================================
// VARIABLES GLOBALES
// ==========================================
let numbersgenerated = [];
let lastNumbers = fiveNumbers || [];
let narrationAudio;
let soundWinner;
let isGameFinishedShown = false;
let messagesDisplayed = [];
let intervalNextGame;
let winners = [];
let winnerIndex = 0;
let winnerSliderTimeout;
let gameTimerInterval;
let startTime;
let bingoInProgress = false;     // Variable para controlar si hay un bingo en progreso
let simultaneousBingos = [];     // Variable para manejar bingos simultáneos
let offlineMode = false;         // Indicador de modo offline
let consecutiveErrors = 0;       // Contador de errores consecutivos
let pendingActions = [];         // Acciones pendientes para sincronizar
let lastPollTime = 0;            // Último tiempo de polling

// ==========================================
// CLASES DE UTILIDAD
// ==========================================

/**
 * Gestor de caché para reducir solicitudes al servidor
 */
class CacheSystem {
    constructor(ttl = CONFIG.CACHE_TTL) {
        this.cache = new Map();
        this.ttl = ttl; // Tiempo de vida en ms
    }
    
    set(key, value) {
        this.cache.set(key, {
            value,
            timestamp: Date.now()
        });
        
        // También guardar en localStorage para persistencia
        if (CONFIG.OFFLINE_STORAGE) {
            try {
                const storageKey = `cache_${key}`;
                localStorage.setItem(storageKey, JSON.stringify({
                    value,
                    timestamp: Date.now()
                }));
            } catch (e) {
                console.warn('Error al guardar en localStorage:', e);
            }
        }
    }
    
    get(key) {
        // Intentar obtener de la memoria caché primero
        let item = this.cache.get(key);
        
        // Si no está en memoria, intentar obtener de localStorage
        if (!item && CONFIG.OFFLINE_STORAGE) {
            try {
                const storageKey = `cache_${key}`;
                const storedItem = localStorage.getItem(storageKey);
                if (storedItem) {
                    item = JSON.parse(storedItem);
                    // Restaurar a la memoria caché
                    this.cache.set(key, item);
                }
            } catch (e) {
                console.warn('Error al leer de localStorage:', e);
            }
        }
        
        if (!item) return null;
        
        // Verificar si el item ha expirado
        if (Date.now() - item.timestamp > this.ttl) {
            this.cache.delete(key);
            if (CONFIG.OFFLINE_STORAGE) {
                try {
                    localStorage.removeItem(`cache_${key}`);
                } catch (e) {
                    console.warn('Error al eliminar de localStorage:', e);
                }
            }
            return null;
        }
        
        return item.value;
    }
    
    has(key) {
        return this.get(key) !== null;
    }
    
    clear() {
        this.cache.clear();
        
        // Limpiar también localStorage
        if (CONFIG.OFFLINE_STORAGE) {
            try {
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith('cache_')) {
                        localStorage.removeItem(key);
                    }
                });
            } catch (e) {
                console.warn('Error al limpiar localStorage:', e);
            }
        }
    }
    
    // Limpiar items expirados periódicamente
    cleanup() {
        const now = Date.now();
        
        // Limpiar memoria caché
        this.cache.forEach((item, key) => {
            if (now - item.timestamp > this.ttl) {
                this.cache.delete(key);
            }
        });
        
        // Limpiar localStorage
        if (CONFIG.OFFLINE_STORAGE) {
            try {
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith('cache_')) {
                        try {
                            const item = JSON.parse(localStorage.getItem(key));
                            if (now - item.timestamp > this.ttl) {
                                localStorage.removeItem(key);
                            }
                        } catch (e) {
                            // Si hay error al parsear, eliminar la entrada
                            localStorage.removeItem(key);
                        }
                    }
                });
            } catch (e) {
                console.warn('Error al limpiar localStorage:', e);
            }
        }
    }
}

/**
 * Gestor de audio optimizado
 */
class AudioManager {
    constructor() {
        this.audioCache = new Map();
        this.preloadedAudios = new Set();
        this.muted = false;
        this.currentlyPlaying = new Set();
    }
    
    preload(src) {
        if (this.preloadedAudios.has(src)) return;
        
        try {
            const audio = new Audio();
            audio.preload = 'auto';
            audio.src = src;
            this.audioCache.set(src, audio);
            this.preloadedAudios.add(src);
        } catch (e) {
            console.warn('Error preloading audio:', e);
        }
    }
    
    play(src) {
        if (this.muted) return null;
        
        try {
            let audio = this.audioCache.get(src);
            if (!audio) {
                audio = new Audio();
                audio.src = src;
                this.audioCache.set(src, audio);
            }
            
            // Limitar el número de reproducciones simultáneas
            if (this.currentlyPlaying.size > 3) {
                const oldestAudio = this.currentlyPlaying.values().next().value;
                if (oldestAudio) {
                    oldestAudio.pause();
                    this.currentlyPlaying.delete(oldestAudio);
                }
            }
            
            // Clone para permitir múltiples reproducciones simultáneas
            const audioClone = audio.cloneNode();
            
            // Registrar el audio en reproducción
            this.currentlyPlaying.add(audioClone);
            
            // Eliminar del conjunto cuando termine
            audioClone.addEventListener('ended', () => {
                this.currentlyPlaying.delete(audioClone);
            });
            
            audioClone.play().catch(e => console.warn('Audio play failed:', e));
            
            return audioClone;
        } catch (e) {
            console.warn('Error playing audio:', e);
            return null;
        }
    }
    
    stopAll() {
        this.currentlyPlaying.forEach(audio => {
            try {
                audio.pause();
            } catch (e) {
                console.warn('Error stopping audio:', e);
            }
        });
        this.currentlyPlaying.clear();
    }
    
    setMuted(muted) {
        this.muted = muted;
        if (muted) {
            this.stopAll();
        }
    }
    
    toggleMute() {
        this.setMuted(!this.muted);
        return this.muted;
    }
    
    preloadCommonSounds() {
        // Precargar sonidos comunes
        if (typeof audioPath !== 'undefined') {
            this.preload(audioPath + 'winner.mp3');
        }
    }
}

/**
 * Cache de elementos DOM
 */
class DOMCache {
    constructor() {
        this.cache = new Map();
    }
    
    get(id) {
        if (!this.cache.has(id)) {
            const element = document.getElementById(id);
            if (element) {
                this.cache.set(id, element);
            }
            return element;
        }
        return this.cache.get(id);
    }
    
    clear() {
        this.cache.clear();
    }
}

// Pool de elementos de mensajes mejorado para el nuevo chat
class MessagePool {
    constructor(maxSize = CONFIG.MESSAGE_POOL_SIZE) {
        this.pool = [];
        this.maxSize = maxSize;
    }
    
    get() {
        if (this.pool.length > 0) {
            const bubble = this.pool.pop();
            bubble.classList.remove("fade-out");
            bubble.style.display = "flex";
            return bubble;
        }
        return this.createNew();
    }
    
    release(element) {
        if (this.pool.length < this.maxSize) {
            // Reset element
            element.className = 'message-bubble';
            element.style.display = 'none';
            element.innerHTML = '';
            this.pool.push(element);
        }
    }
    
    createNew() {
        const bubble = document.createElement("div");
        bubble.className = "message-bubble";
        return bubble;
    }
}

/**
 * Gestor centralizado de intervalos
 */
class IntervalManager {
    constructor() {
        this.intervals = new Map();
    }
    
    set(name, callback, delay) {
        this.clear(name);
        this.intervals.set(name, setInterval(callback, delay));
    }
    
    clear(name) {
        if (this.intervals.has(name)) {
            clearInterval(this.intervals.get(name));
            this.intervals.delete(name);
        }
    }
    
    clearAll() {
        this.intervals.forEach(interval => clearInterval(interval));
        this.intervals.clear();
    }
}

/**
 * Polling inteligente con backoff exponencial
 */
class SmartPoller {
    constructor(baseInterval = CONFIG.BASE_POLL_INTERVAL) {
        this.baseInterval = baseInterval;
        this.currentInterval = baseInterval;
        this.maxInterval = CONFIG.MAX_POLL_INTERVAL;
        this.consecutiveErrors = 0;
        this.isActive = true;
        this.timeoutId = null;
        this.lastCallback = null;
        this.lastSuccessTime = Date.now();
    }
    
    async poll(callback) {
        if (!this.isActive) return;
        
        this.lastCallback = callback;
        
        try {
            const result = await callback();
            
            // Reset interval on success
            if (result && result.status === 'success') {
                this.currentInterval = this.baseInterval;
                this.consecutiveErrors = 0;
                this.lastSuccessTime = Date.now();
            }
            
        } catch (error) {
            this.consecutiveErrors++;
            // Exponential backoff on errors
            this.currentInterval = Math.min(
                this.baseInterval * Math.pow(2, this.consecutiveErrors),
                this.maxInterval
            );
            console.warn('Polling error:', error);
            
            // Si estamos offline, intentar usar datos en caché
            if (navigator.onLine === false) {
                offlineMode = true;
                this.handleOfflineMode();
            }
        }
        
        this.timeoutId = setTimeout(() => this.poll(callback), this.currentInterval);
    }
    
    stop() {
        this.isActive = false;
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }
    }
    
    restart() {
        this.stop();
        this.isActive = true;
        this.currentInterval = this.baseInterval;
        this.consecutiveErrors = 0;
        if (this.lastCallback) {
            this.poll(this.lastCallback);
        }
    }
    
    handleOfflineMode() {
        // Intentar usar datos en caché cuando estamos offline
        console.log('Modo offline activado, usando datos en caché');
    }
}

/**
 * Gestor de recursos
 */
class ResourceManager {
    constructor() {
        this.isCleaningUp = false;
    }

    cleanup() {
        if (this.isCleaningUp) return;
        this.isCleaningUp = true;

        console.log('Cleaning up resources...');

        // Limpiar intervalos
        intervalManager.clearAll();
        
        // Detener polling
        messagePoller.stop();
        
        // Limpiar timeouts
        if (winnerSliderTimeout) {
            clearTimeout(winnerSliderTimeout);
            winnerSliderTimeout = null;
        }
        
        if (intervalNextGame) {
            clearInterval(intervalNextGame);
            intervalNextGame = null;
        }

        if (gameTimerInterval) {
            clearInterval(gameTimerInterval);
            gameTimerInterval = null;
        }
        
        // Limpiar arrays
        messagesDisplayed.length = 0;
        winners.length = 0;
        
        // Limpiar caché
        cacheSystem.clear();
        
        console.log('Resource cleanup completed');
        this.isCleaningUp = false;
    }

    initialize() {
        this.isCleaningUp = false;
        
        // Precargar recursos de audio
        audioManager.preloadCommonSounds();
        
        // Configurar eventos de limpieza
        window.addEventListener('beforeunload', () => this.cleanup());
        window.addEventListener('unload', () => this.cleanup());
        
        // Limpiar recursos cuando la página pierde el foco por mucho tiempo
        let pageHiddenTime = 0;
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                pageHiddenTime = Date.now();
            } else {
                const hiddenDuration = Date.now() - pageHiddenTime;
                // Si la página estuvo oculta por más de 5 minutos, reiniciar algunos recursos
                if (hiddenDuration > 300000) {
                    this.softReset();
                }
            }
        });
    }

    softReset() {
        console.log('Performing soft reset...');
        
        // Reiniciar polling si está detenido
        if (!messagePoller.isActive) {
            messagePoller.restart();
        }
        
        // Limpiar mensajes antiguos
        const display = $id("message-display");
        if (display) {
            const bubbles = display.getElementsByClassName("message-bubble");
            Array.from(bubbles).forEach(bubble => {
                messagePool.release(bubble);
                bubble.remove();
            });
        }
        
        // Resetear arrays de mensajes mostrados
        messagesDisplayed.length = 0;
    }
}

/**
 * Gestor de notificaciones
 */
class NotificationManager {
    constructor() {
        this.container = null;
        this.queue = [];
        this.isProcessing = false;
        this.createContainer();
    }
    
    createContainer() {
        // Crear contenedor de notificaciones si no existe
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.className = 'notification-container';
            this.container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                width: 300px;
            `;
            document.body.appendChild(this.container);
        }
    }
    
    show(message, type = 'info', duration = 3000) {
        // Añadir a la cola
        this.queue.push({ message, type, duration });
        
        // Procesar cola si no está en proceso
        if (!this.isProcessing) {
            this.processQueue();
        }
    }
    
    processQueue() {
        if (this.queue.length === 0) {
            this.isProcessing = false;
            return;
        }
        
        this.isProcessing = true;
        const { message, type, duration } = this.queue.shift();
        
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-icon">${this.getIcon(type)}</div>
            <div class="notification-content">${message}</div>
            <div class="notification-close">×</div>
        `;
        
        notification.style.cssText = `
            display: flex;
            align-items: center;
            background-color: ${this.getColor(type)};
            color: white;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            opacity: 0;
        `;
        
        // Estilos para los componentes internos
        notification.querySelector('.notification-icon').style.cssText = `
            margin-right: 10px;
            font-size: 20px;
        `;
        
        notification.querySelector('.notification-content').style.cssText = `
            flex: 1;
        `;
        
        notification.querySelector('.notification-close').style.cssText = `
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
        `;
        
        // Añadir al contenedor
        this.container.appendChild(notification);
        
        // Evento para cerrar
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.close(notification);
        });
        
        // Mostrar con animación
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);
        
        // Auto-cerrar después de la duración
        if (duration > 0) {
            setTimeout(() => {
                this.close(notification);
            }, duration);
        }
    }
    
    close(notification) {
        // Ocultar con animación
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        
        // Eliminar después de la animación
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
                
                // Procesar siguiente en la cola
                this.processQueue();
            }
        }, 300);
    }
    
    getIcon(type) {
        switch (type) {
            case 'success': return '✅';
            case 'error': return '❌';
            case 'warning': return '⚠️';
            default: return 'ℹ️';
        }
    }
    
    getColor(type) {
        switch (type) {
            case 'success': return '#4CAF50';
            case 'error': return '#F44336';
            case 'warning': return '#FF9800';
            default: return '#2196F3';
        }
    }
}

// ==========================================
// INSTANCIAS GLOBALES
// ==========================================
const intervalManager = new IntervalManager();
const domCache = new DOMCache();
const messagePool = new MessagePool();
const audioManager = new AudioManager();
const messagePoller = new SmartPoller(CONFIG.BASE_POLL_INTERVAL);
const resourceManager = new ResourceManager();
const cacheSystem = new CacheSystem();
const notificationManager = new NotificationManager();

// ==========================================
// UTILIDADES
// ==========================================
const $id = (id) => domCache.get(id);

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Función para detectar si el dispositivo tiene recursos limitados
function isLowEndDevice() {
    // Detectar dispositivos con recursos limitados
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    const isSlowConnection = connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g');
    const isLowMemory = navigator.deviceMemory && navigator.deviceMemory < 4;
    const isOldDevice = navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;
    
    return isSlowConnection || isLowMemory || isOldDevice;
}

// Función para manejar errores de red de forma elegante
function handleNetworkError(error, context = '') {
    console.warn(`Network error in ${context}:`, error);
    
    // Incrementar contador de errores consecutivos
    consecutiveErrors++;
    
    // Verificar si estamos offline
    if (navigator.onLine === false) {
        offlineMode = true;
        showOfflineNotification();
    }
    
    // Mostrar notificación discreta al usuario
    const notification = document.createElement('div');
    notification.className = 'network-error-notification';
    notification.textContent = 'Conexión inestable. Reintentando...';
    notification.style.cssText = `
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff6b6b;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        z-index: 10000;
        font-size: 13px;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => {
        notification.style.display = 'block';
        notification.style.opacity = '1';
    }, 100);
    
    // Fade out y remover después de 3 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Función para mostrar notificación de modo offline
function showOfflineNotification() {
    notificationManager.show('Estás trabajando sin conexión. Algunas funciones pueden no estar disponibles.', 'warning', 0);
}

// Función para ocultar notificación de modo offline
function hideOfflineNotification() {
    // La notificación se cerrará automáticamente
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info', duration = 3000) {
    notificationManager.show(message, type, duration);
}

// Función para procesar acciones pendientes cuando se recupera la conexión
function processPendingActions() {
    if (pendingActions.length === 0) return;
    
    console.log(`Procesando ${pendingActions.length} acciones pendientes`);
    
    // Procesar acciones en orden
    const actions = [...pendingActions];
    pendingActions = [];
    
    actions.forEach(action => {
        switch (action.type) {
            case 'dialNumber':
                dialNumber(action.number);
                break;
                
            case 'singBingo':
                singBingo();
                break;
        }
    });
}

// ==========================================
// FUNCIONES DE CHAT MEJORADAS
// ==========================================

// Función para crear burbujas de mensaje estilo redes sociales
function createMessageBubble(content, profilePicUrl) {
    const bubble = messagePool.get();
    bubble.style.display = "flex";
    
    // Reutilizar o crear imagen de perfil
    let img = bubble.querySelector('.profile-pic');
    if (!img) {
        img = document.createElement("img");
        img.classList.add("profile-pic");
        bubble.appendChild(img);
    }
    img.src = profilePicUrl || 'default-avatar.png';
    img.onerror = function() {
        this.src = 'default-avatar.png';
    };
    
    // Reutilizar o crear span para el contenido
    let span = bubble.querySelector('span');
    if (!span) {
        span = document.createElement("span");
        bubble.appendChild(span);
    }
    
    // Determinar si es solo emoji o texto
    const isOnlyEmoji = /[\u1F600-\u1F6FF]/.test(content);
    span.textContent = content;
    
    // Aplicar estilos según el tipo de contenido
    if (isOnlyEmoji) {
        span.style.fontSize = '13px';
        bubble.style.background = 'rgba(255, 255, 255, 0.2)';
    } else {
        span.style.fontSize = '24px';
        bubble.style.background = 'rgba(98, 54, 255, 0.7)';
    }
    
    // Añadir timestamp para limpieza automática
    bubble.dataset.timestamp = Date.now().toString();
    
    return bubble;
}

// Función para eliminar mensajes con animación mejorada
function removeMessageWithFade(el) {
    el.classList.add("fade-out");
    setTimeout(() => {
        if (el.parentNode) {
            el.parentNode.removeChild(el);
            messagePool.release(el);
        }
    }, CONFIG.FADE_OUT_TIME);
}

// Función para limitar mensajes con el nuevo sistema
function limitMessages() {
    const display = $id("message-display");
    if (!display) return;
    
    const bubbles = display.getElementsByClassName("message-bubble");
    while (bubbles.length >= CONFIG.MAX_MESSAGES) {
        removeMessageWithFade(bubbles[0]);
    }
}

// Scroll optimizado con debounce para el nuevo chat
const debouncedScroll = debounce(() => {
    const el = $id("message-display");
    if (el) {
        // Para el nuevo sistema que usa column-reverse, scroll al final
        el.scrollTop = el.scrollHeight;
    }
}, CONFIG.DEBOUNCE_DELAY);

function scrollToBottom() {
    debouncedScroll();
}

// Función mejorada para mostrar mensajes estilo redes sociales
function displayMessage(messageData, imageUrl) {
    const display = $id("message-display");
    if (!display) return;
    
    limitMessages();
    
    const bubble = createMessageBubble(
        messageData.message || messageData, 
        imageUrl || imagePath || 'default-avatar.png'
    );
    
    // Insertar al principio para que aparezca abajo (ya que usamos column-reverse)
    display.insertBefore(bubble, display.firstChild);
    
    // Guardar ID del mensaje si existe
    if (messageData.id) {
        messagesDisplayed.push(messageData.id);
        
        // Limitar tamaño del array de mensajes mostrados
        if (messagesDisplayed.length > 200) {
            messagesDisplayed.shift();
        }
    }
    
    // Programar eliminación automática
    setTimeout(() => removeMessageWithFade(bubble), CONFIG.MESSAGE_LIFETIME);
}

// Función mejorada para enviar mensajes
function sendMessage(content, id) {
    if (!content || !content.trim()) return;
    
    const trimmedContent = content.trim();
    const messageId = id || Date.now();
    
    // Mostrar mensaje inmediatamente en la interfaz
    displayMessage({ message: trimmedContent, id: messageId }, imagePath);
    
    // Si estamos offline, guardar para enviar más tarde
    if (offlineMode) {
        pendingActions.push({
            type: 'message',
            content: trimmedContent,
            id: messageId,
            timestamp: Date.now()
        });
        return;
    }
    
    // Enviar al servidor
    $.post(site_url + 'playings/messageSubmit', { message: trimmedContent })
        .done((data) => {
            if (data.status === 'success') {
                $('#message-send-new').val('');
            }
        })
        .fail(() => {
            console.warn('Error al enviar mensaje');
            // Guardar para reintento
            pendingActions.push({
                type: 'message',
                content: trimmedContent,
                id: messageId,
                timestamp: Date.now()
            });
        });
}

// Polling optimizado de mensajes mejorado
function pollMessagesOptimized() {
    // Verificar si hay datos en caché recientes
    const cacheKey = 'messages_cache';
    const cachedData = cacheSystem.get(cacheKey);
    
    if (cachedData) {
        // Usar datos en caché
        if (cachedData.status === 'success' && cachedData.message && 
            !messagesDisplayed.includes(cachedData.message.id)) {
            displayMessage(cachedData.message, cachedData.image);
        }
        return Promise.resolve(cachedData);
    }
    
    return new Promise((resolve) => {
        $.get(site_url + 'playings/messageGet')
            .done((data) => {
                if (data.status === 'stop') {
                    messagePoller.stop();
                    return resolve(data);
                }
                
                // Guardar en caché
                cacheSystem.set(cacheKey, data);
                
                if (data.status === 'success' && data.message && 
                    !messagesDisplayed.includes(data.message.id)) {
                    displayMessage(data.message, data.image);
                }
                resolve(data);
            })
            .fail((error) => {
                console.warn('Error en polling de mensajes:', error);
                resolve({ status: 'error' });
            });
    });
}

// ==========================================
// FUNCIONES PRINCIPALES
// ==========================================

function getColumnClass(number) {
    if (number <= 15) return 'B';
    if (number <= 30) return 'I';
    if (number <= 45) return 'N';
    if (number <= 60) return 'G';
    return 'O';
}

function startWinnerSlider() {
    if (winners.length === 0) return;

    clearTimeout(winnerSliderTimeout);

    function showNext() {
        const current = winners[winnerIndex];
        const nextGameSpan = document.querySelector('.next-game');
        if (nextGameSpan) {
            nextGameSpan.textContent = `GANADOR: ${current.player} - ${current.modality}`;
        }
        winnerIndex = (winnerIndex + 1) % winners.length;
        winnerSliderTimeout = setTimeout(showNext, CONFIG.WINNER_SLIDER_INTERVAL);
    }

    showNext();
}

function showCountdown(data, callback) {
    const numberHe = $id('countdown');
    const container = $id('countdown-container');
    const textHe = $id('text-countdown');
    
    if (!numberHe || !container || !textHe) return;
    
    let countdown = 5;

    container.style.display = 'block';
    numberHe.textContent = __['bingo!'] || 'BINGO!';
    textHe.innerHTML = `${data.modality}<br />${data.player}`;
    numberHe.style.color = 'white';

    // Agregar ganador si no existe
    if (!winners.some(w => w.player === data.player && w.modality === data.modality)) {
        winners.push({ player: data.player, modality: data.modality });
    }

    startWinnerSlider();

    // Reproducir sonido de victoria
    if (typeof audioPath !== 'undefined') {
        audioManager.play(audioPath + 'winner.mp3');
    }

    // Actualizar cartón ganador
    const cartn = $id(`modality-${data.modalityId}`);
    if (cartn) {
        cartn.classList.add('cartn-sing');
        cartn.querySelectorAll('.card-number.modality-sing').forEach(el => {
            el.classList.add('sing');
            el.innerText = '⭐️';
        });
    }

    // Secuencia de countdown
    setTimeout(() => {
        if (data.image) {
            numberHe.style.backgroundImage = `url(${data.image})`;
            numberHe.style.backgroundSize = 'cover';
            numberHe.style.backgroundPosition = 'center';
            numberHe.style.color = 'transparent';
        }

        setTimeout(() => {
            numberHe.style.backgroundImage = '';
            numberHe.style.background = 'linear-gradient(145deg, #6236ff, #8767fa)';
            numberHe.style.color = 'white';
            numberHe.textContent = countdown;

            const interval = setInterval(() => {
                numberHe.textContent = --countdown;
                if (countdown === 0) {
                    clearInterval(interval);
                    container.style.display = 'none';
                    
                    // Resetear la bandera de bingo en progreso
                    bingoInProgress = false;
                    
                    // Verificar si hay bingos simultáneos en cola
                    if (simultaneousBingos.length > 0) {
                        const nextBingo = simultaneousBingos.shift();
                        showCountdown(nextBingo, callback);
                    } else if (callback) {
                        callback();
                    }
                }
            }, CONFIG.COUNTDOWN_INTERVAL);
        }, 3000);
    }, 2000);
}

function updateBallsCounter(totalNumbersGenerated) {
    const totalBalls = 75;
    const drawn = totalNumbersGenerated;
    const remaining = totalBalls - drawn;
    
    const counter = $('#balls-counter');
    if (counter.length) {
        counter.text(`${drawn} - ${remaining}`);
    }

    const nextGameSpan = document.querySelector('.next-game');
    if (nextGameSpan && drawn === 1) {
        if (intervalNextGame) {
            clearInterval(intervalNextGame);
            intervalNextGame = null;
        }
        nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
    }
}

function handleNewNumber(newNumber, totalNumbersGenerated) {
    if (numbersgenerated.includes(newNumber)) return;

    updateBallsCounter(totalNumbersGenerated);
    numbersgenerated.push(newNumber);

    // Marcar automáticamente el número si está habilitado
    if (typeof autoMarkEnabled !== 'undefined' && autoMarkEnabled) {
        const elementsNumber = $(".number-" + newNumber);
        if (elementsNumber.length) {
            elementsNumber.each(function() {
                const elementNumber = $(this);
                if (!elementNumber.hasClass('marked')) {
                    const originalContent = elementNumber.text();
                    elementNumber.text('⭐️').addClass('explosive-effect marked');
                    
                    // Restaurar el número después de la animación
                    setTimeout(function() {
                        elementNumber.text(originalContent); 
                        elementNumber.removeClass('explosive-effect');
                        elementNumber.addClass('marked'); 
                    }, 1000);
                }
            });
        }
    }

    if (!lastNumbers.includes(newNumber)) {
        setTimeout(() => {
            setTimeout(() => {
                lastNumbers.push(newNumber);
                if (lastNumbers.length > 5) lastNumbers.shift();
                
                const latestUncurrent = lastNumbers.slice(0, -1);
                const container = $("#last-five-numbers");
                if (container.length) {
                    container.empty();
                    latestUncurrent.forEach(num => {
                        container.append(`<div class="bingo-ball ${getColumnClass(num)} size-40"><span>${num}</span></div>`);
                    });
                }
            }, 1000);
            
            const lastNumberEl = $('#last-number');
            if (lastNumberEl.length) {
                lastNumberEl.html(`<small style="position: absolute; top: -13px; font-size: 1.2rem; z-index: 1;">${getColumnClass(newNumber)}</small><span>${newNumber}</span>`)
                    .removeClass()
                    .addClass(`bingo-ball ${getColumnClass(newNumber)} size-100`);
            }
        }, 1500);
    }

    // Reproducir narración si está activada
    if (typeof narrationPlaying !== 'undefined' && narrationPlaying) {
        audioManager.play(audioPath + newNumber + '.mp3');
    }

    setTimeout(() => {
        const lastNumberEl = $('#last-number');
        if (lastNumberEl.length) {
            lastNumberEl.removeClass("move-number");
        }
    }, 1000);

    // Auto-marcar si está habilitado
    if (typeof autoMarkEnabled !== 'undefined' && autoMarkEnabled) {
        dialNumber(newNumber);
    }

    const numberEl = $("#board-number-" + newNumber);
    if (numberEl.length) {
        numberEl.addClass(getColumnClass(newNumber));
    }
}

function lastNumberGet() {
    // Si hay un bingo en progreso, no solicitar nuevos números
    if (bingoInProgress) return;
    
    // Verificar si hay datos en caché
    const cacheKey = 'last_number';
    const cachedData = cacheSystem.get(cacheKey);
    
    if (cachedData && !offlineMode) {
        // Usar datos en caché
        processLastNumberData(cachedData);
        return;
    }
    
    // Si estamos offline, no hacer nada
    if (offlineMode) {
        return;
    }
    
    $.get(site_url + 'playings/numberGet')
        .done((data) => {
            // Guardar en caché
            cacheSystem.set(cacheKey, data);
            
            processLastNumberData(data);
        })
        .fail((xhr, status, error) => {
            console.warn('Failed to get last number:', error);
            handleNetworkError(error, 'lastNumberGet');
        });
}

// Función para procesar datos del último número
function processLastNumberData(data) {
    if (data.status === 'pause') {
        // Hay un bingo que notificar - pausar el juego
        intervalManager.clear('lastNumber');
        
        // Marcar que hay un bingo en progreso
        bingoInProgress = true;
        
        // Si ya hay un bingo mostrándose, agregar este a la cola
        const countdownContainer = $id('countdown-container');
        if (countdownContainer && countdownContainer.style.display === 'block') {
            simultaneousBingos.push(data);
        } else {
            showCountdown(data, startAutomaticLast);
        }
        
    } else if (data.status === 'success') {
        // Funcionamiento normal - continuar generando números
        // Actualizar interfaz con último número si es necesario
        if (data.number) {
            handleNewNumber(data.number, data.totalNumbersGenerated);
        }
        // Continuar el ciclo automático
        startAutomaticLast();
        
    } else if (data.status === 'completed') {
        // Juego terminado
        intervalManager.clear('lastNumber');
        
        // Si hay información de jugador, mostrar notificación final
        if (data.player && data.player !== '') {
            showCountdown(data, () => {
                setTimeout(showGameFinalized, timeBallGet);
            });
        } else {
            // No hay notificación final, ir directo a finalizar
            setTimeout(showGameFinalized, timeBallGet);
        }
    }
}

function startAutomaticLast() {
    intervalManager.clear('lastNumber');
    intervalManager.set('lastNumber', lastNumberGet, timeBallLast);
}

function stopAutomaticLast() {
    intervalManager.clear('lastNumber');
}

function showGameFinalized() {
    if (isGameFinishedShown) return;
    isGameFinishedShown = true;
    
    const container = $id('game-finalized');
    const text = $id('finalized');
    
    if (container && text) {
        container.style.display = 'block';
        text.innerHTML = __['game finished!'] || 'JUEGO FINALIZADO!';
        
        setTimeout(() => {
            if (typeof awardsGet === 'function') {
                awardsGet();
            }
            container.style.display = 'none';
        }, 5000);
    }

    stopAutomaticLast();
    messagePoller.stop();
}

// Función para marcar un número en el cartón
function dialNumber(number) {
    const elementsNumber = $(".number-" + number);

    if (!elementsNumber.length) {
        console.warn("No se encontró el número en el DOM:", number);
        return;
    }

    // Si estamos offline, guardar para sincronizar después
    if (offlineMode) {
        // Marcar localmente
        elementsNumber.each(function() {
            const elementNumber = $(this);
            if (!elementNumber.hasClass('marked')) {

                const originalContent = elementNumber.text();
                elementNumber.text('⭐️').addClass('explosive-effect marked');
                
                // Restaurar el número después de la animación
                setTimeout(function() {
                    elementNumber.text(originalContent); 
                    elementNumber.removeClass('explosive-effect');
                    elementNumber.addClass('marked'); 
                }, 1000);
            }
        });
        
        pendingActions.push({
            type: 'dialNumber',
            number: number,
            timestamp: Date.now()
        });
        return;
    }

    // Implementar sistema de reintentos
    let retryCount = 0;
    
    function attemptDialNumber() {
        $.ajax({
            url: site_url + 'playings/dialNumber',
            method: 'POST',
            data: { number: number },
            success: function(data) {
                if (data.status === 'success') {
                    elementsNumber.each(function() {
                        const elementNumber = $(this);

                        if (elementNumber.hasClass('marked')) return;

                        const originalContent = elementNumber.text();
                        elementNumber.text('⭐️').addClass('explosive-effect marked');
                        
                        // Restaurar el número después de la animación
                        setTimeout(function() {
                            elementNumber.text(originalContent); 
                            elementNumber.removeClass('explosive-effect');
                            elementNumber.addClass('marked'); 
                        }, 1000);
                    });
                }
            },
            error: function() {
                retryCount++;
                if (retryCount < CONFIG.RETRY_ATTEMPTS) {
                    // Reintento con backoff exponencial
                    setTimeout(attemptDialNumber, CONFIG.RETRY_DELAY * Math.pow(2, retryCount));
                } else {
                    console.warn("Error al marcar el número:", number);
                    handleNetworkError(new Error("Error al marcar el número"), 'dialNumber');
                    
                    // Guardar para reintento posterior
                    pendingActions.push({
                        type: 'dialNumber',
                        number: number,
                        timestamp: Date.now()
                    });
                }
            }
        });
    }
    
    attemptDialNumber();
}

// Función para cantar bingo
function singBingo() {
    // Si ya hay un bingo en progreso, no permitir cantar otro
    if (bingoInProgress) {
        // Mostrar mensaje al usuario
        showNotification('Por favor espera mientras se verifica el bingo actual', 'warning');
        return;
    }
    
    const bingoButton = document.querySelector('.btn-bingooo');
    if (bingoButton) {
        bingoButton.classList.remove('animate-click');
        void bingoButton.offsetWidth;
        bingoButton.classList.add('animate-click');
    }
    
    // Si estamos offline, guardar para sincronizar después
    if (offlineMode) {
        showNotification('No se puede cantar bingo sin conexión. Se intentará cuando vuelvas a estar en línea.', 'warning');
        
        pendingActions.push({
            type: 'singBingo',
            timestamp: Date.now()
        });
        return;
    }

    // Implementar sistema de reintentos
    let retryCount = 0;
    
    function attemptSingBingo() {
        $.ajax({
            url: site_url + 'playings/singBingo',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    intervalManager.clear('lastNumber');
                    bingoInProgress = true;

                    sendMessage((__['bingo!'] || 'BINGO!') + ' 🥳', 21);

                    // Marcar números ganadores en el cartón
                    const cartonElement = document.getElementById(`carton-${data.carton}`);
                    if (cartonElement && data.numbers) {
                        data.numbers.forEach(num => {
                            const numberElement = cartonElement.querySelector(`.bingo-carton-number.number-${num}`);
                            if (numberElement) {
                                numberElement.classList.add('carton-sing');
                            }
                        });
                    }

                    // Mostrar countdown de victoria
                    showCountdown({
                        player: data.player,
                        modality: data.modality,
                        modalityId: data.modalityId,
                        image: data.image
                    }, startAutomaticLast);
                } else {
                    // Mostrar mensaje de error
                    showNotification(data.message || 'Error al verificar el bingo', 'error');
                }
            },
            error: function() {
                retryCount++;
                if (retryCount < CONFIG.RETRY_ATTEMPTS) {
                    // Reintento con backoff exponencial
                    setTimeout(attemptSingBingo, CONFIG.RETRY_DELAY * Math.pow(2, retryCount));
                } else {
                    console.warn("Error al cantar bingo");
                    handleNetworkError(new Error("Error al cantar bingo"), 'singBingo');
                    
                    // Guardar para reintento posterior
                    pendingActions.push({
                        type: 'singBingo',
                        timestamp: Date.now()
                    });
                }
            }
        });
    }
    
    attemptSingBingo();
}

// Función para activar/desactivar el sonido
function RemoveVolume() {
    // Silenciar audio localmente
    audioManager.setMuted(true);
    
    $.ajax({
        url: site_url + 'playings/volumeSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("Sound disabled successfully");
            }
        },
        error: function() {
            console.warn("Error disabling sound");
            
            // Si estamos offline, guardar para sincronizar después
            if (offlineMode) {
                pendingActions.push({
                    type: 'volume',
                    value: false,
                    timestamp: Date.now()
                });
            }
        }
    });
}

// Función para activar/desactivar la narración
function RemoveMicrophone() {
    // Desactivar narración localmente
    if (typeof narrationPlaying !== 'undefined') {
        narrationPlaying = false;
    }
    
    $.ajax({
        url: site_url + 'playings/microphoneSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("Narrator disabled successfully");
            }
        },
        error: function() {
            console.warn("Error disabling narrator");
            
            // Si estamos offline, guardar para sincronizar después
            if (offlineMode) {
                pendingActions.push({
                    type: 'microphone',
                    value: false,
                    timestamp: Date.now()
                });
            }
        }
    });
}

// ==========================================
// CONFIGURACIÓN DE EVENTOS MEJORADA
// ==========================================
function setupEvents() {
    // Eventos de mensajes mejorados
    $('#message-button').on('click', sendMessageText);
    
    $('#message-send-new').on('keypress', (e) => {
        if (e.which === 13) {
            e.preventDefault();
            sendMessageText();
        }
    });

    // Eventos para emojis (si tienes botones de emoji)
    $('.emoji-button').on('click', function() {
        const emoji = $(this).data('emoji') || $(this).text();
        sendEmoji(emoji);
    });
    
    // Eventos para marcado de números
    $(".bingo-carton-number").on('click', function() {
        if (typeof autoMarkEnabled === 'undefined' || !autoMarkEnabled) {
            const number = $(this).data('number'); 
            if (number) {
                dialNumber(number);
            }
        }
    });
    
    // Evento para cantar bingo
    $('.btn-bingooo').on('click', function() {
        singBingo();
    });
    
    // Evento para activar/desactivar marcado automático
    $('.btn-auto-mark').on('click', function() {
        if (typeof autoMarkEnabled !== 'undefined') {
            autoMarkEnabled = !autoMarkEnabled;
            $(this).html(autoMarkEnabled ? 
                '<i class="fa-duotone fa-solid fa-check-double"></i>' : 
                '<i class="fa-duotone fa-solid fa-xmark"></i>'
            );
            
            // Guardar preferencia
            try {
                localStorage.setItem('bingo_auto_mark_enabled', autoMarkEnabled ? '1' : '0');
            } catch (e) {
                console.warn('Error al guardar preferencia de auto-marcado:', e);
            }
        }
    });

    // Gestión de modales
    $('.modal').on("hidden.bs.modal", function(e) {
        if ($('.modal:visible').length) {
            $('.modal-backdrop').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) - 10);
            $('body').addClass('modal-open');
        }
    }).on("show.bs.modal", function(e) {
        if ($('.modal:visible').length) {
            $('.modal-backdrop.in').first().css('z-index', parseInt($('.modal:visible').last().css('z-index')) + 10);
            $(this).css('z-index', parseInt($('.modal-backdrop.in').first().css('z-index')) + 10);
        }
    });

    // Toggle de mensajes mejorado
    const toggleBtn = $id("toggle-messages-btn");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function(event) {
            const messageContainer = $id("message-display-container");
            if (messageContainer) {
                const isVisible = messageContainer.style.display === "flex";
                messageContainer.style.display = isVisible ? "none" : "flex";
                
                // Actualizar icono del botón si existe
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.className = isVisible ? 'fa fa-comments' : 'fa fa-times';
                }
            }
            event.stopPropagation();
        });
    }

    // Click fuera para cerrar mensajes
    document.addEventListener("click", function(event) {
        const messageContainer = $id("message-display-container");
        const toggleButton = $id("toggle-messages-btn");
        
        if (messageContainer && toggleButton && 
            messageContainer.style.display === "flex" && 
            !messageContainer.contains(event.target) && 
            !toggleButton.contains(event.target)) {
            messageContainer.style.display = "none";
            
            // Actualizar icono del botón
            const icon = toggleButton.querySelector('i');
            if (icon) {
                icon.className = 'fa fa-comments';
            }
        }
    });

    // Eventos para auto-scroll del chat
    const messageDisplay = $id("message-display");
    if (messageDisplay) {
        // Detectar cuando el usuario hace scroll manual
        let userScrolled = false;
        messageDisplay.addEventListener('scroll', () => {
            const { scrollTop, scrollHeight, clientHeight } = messageDisplay;
            userScrolled = scrollTop < scrollHeight - clientHeight - 50; // 50px de tolerancia
        });

        // Observer para nuevos mensajes
        const observer = new MutationObserver(() => {
            if (!userScrolled) {
                scrollToBottom();
            }
        });

        observer.observe(messageDisplay, { childList: true });
    }
    
    // Eventos para detección de conexión
    window.addEventListener('online', () => {
        console.log('Conexión restablecida');
        offlineMode = false;
        hideOfflineNotification();
        
        // Procesar acciones pendientes
        processPendingActions();
        
        // Reiniciar polling
        messagePoller.restart();
        
        // Reiniciar intervalos
        if (!isGameFinishedShown) {
            startAutomaticLast();
        }
        
        // Mostrar notificación
        showNotification('Conexión restablecida', 'success');
    });
    
    window.addEventListener('offline', () => {
        console.log('Conexión perdida');
        offlineMode = true;
        showOfflineNotification();
        
        // Mostrar notificación
        showNotification('Conexión perdida. Modo sin conexión activado.', 'warning');
    });
    
    // Cargar preferencia de auto-marcado
    try {
        const autoMarkSetting = localStorage.getItem('bingo_auto_mark_enabled');
        if (autoMarkSetting !== null) {
            autoMarkEnabled = autoMarkSetting === '1';
            
            // Actualizar interfaz
            const autoMarkButton = $('.btn-auto-mark');
            if (autoMarkButton.length) {
                autoMarkButton.html(autoMarkEnabled ? 
                    '<i class="fa-duotone fa-solid fa-check-double"></i>' : 
                    '<i class="fa-duotone fa-solid fa-xmark"></i>'
                );
            }
        }
    } catch (e) {
        console.warn('Error al cargar preferencia de auto-marcado:', e);
    }
}

// ==========================================
// CONFIGURACIÓN DE MÁSCARAS Y SCROLL
// ==========================================
function setupScrollMask() {
    const container = document.querySelector(".board-section");
    if (!container) return;

    function isMobile() {
        return window.innerWidth <= 700;
    }

    function isTablet() {
        return window.innerWidth >= 701 && window.innerWidth <= 1024;
    }

    function shouldApplyMask() {
        return isMobile() || isTablet();
    }

    const updateMask = debounce(() => {
        const scrollTop = container.scrollTop;
        const scrollHeight = container.scrollHeight;
        const clientHeight = container.clientHeight;

        if (!shouldApplyMask()) {
            container.style.maskImage = "none";
            container.style.webkitMaskImage = "none";
            return;
        }

        if (scrollHeight <= clientHeight) {
            container.style.maskImage = "none";
            container.style.webkitMaskImage = "none";
            return;
        }

        let maskValue;
        if (scrollTop === 0) {
            maskValue = "linear-gradient(to bottom, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
        } else if (scrollTop + clientHeight >= scrollHeight) {
            maskValue = "linear-gradient(to top, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
        } else {
            maskValue = "linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 1) 15%, rgba(0, 0, 0, 1) 80%, rgba(0, 0, 0, 0) 100%)";
        }

        container.style.maskImage = maskValue;
        container.style.webkitMaskImage = maskValue;
    }, 50);

    container.addEventListener("scroll", updateMask);
    window.addEventListener("resize", updateMask);
    updateMask();
}

// ==========================================
// CONFIGURACIÓN DE COUNTDOWN Y GANADORES
// ==========================================
function setupGameCountdown() {
    const nextGameSpan = document.querySelector('.next-game');
    if (!nextGameSpan || typeof gameDate === 'undefined') return;

    const targetDate = new Date(gameDate);
    let winnerIndex = 0;

    function updateCountdown() {
        const now = new Date();
        const timeDiff = targetDate - now;

        if (timeDiff <= 0) {
            clearInterval(intervalNextGame);

            if (typeof totalNumbersGenerated !== 'undefined' && totalNumbersGenerated > 0) {
                if (winners.length > 0) {
                    startWinnerSlider();
                } else {
                    nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
                }
            } else {
                nextGameSpan.textContent = 'ESPERE QUE INICIE LA PARTIDA...';
            }
            return;
        }

        const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);

        let text = '';
        if (days > 0) {
            text = `EL JUEGO INICIA EN: ${days} DÍA${days > 1 ? 'S' : ''} ${hours} HORA${hours > 1 ? 'S' : ''} - ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MIN`;
        } else if (hours > 0) {
            text = `EL JUEGO INICIA EN: ${hours} HORA${hours > 1 ? 'S' : ''} - ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MIN`;
        } else {
            if (minutes === 0) {
                const sec = Math.max(0, seconds);
                text = `EL JUEGO INICIA EN: ${sec} SEGUNDO${sec === 1 ? '' : 'S'}`;
            } else {
                text = `EL JUEGO INICIA EN: ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MINUTO${minutes === 1 ? '' : 'S'}`;
            }
        }

        nextGameSpan.textContent = text;
    }

    const now = new Date();
    if (now < targetDate) {
        updateCountdown();
        intervalNextGame = setInterval(updateCountdown, 1000);
    } else {
        if (typeof totalNumbersGenerated !== 'undefined' && totalNumbersGenerated > 0) {
            if (winners.length > 0) {
                startWinnerSlider();
            } else {
                nextGameSpan.textContent = '¡EL JUEGO HA INICIADO!';
            }
        } else {
            nextGameSpan.textContent = 'ESPERE QUE INICIE LA PARTIDA...';
        }
    }
}

// ==========================================
// INICIALIZACIÓN PRINCIPAL
// ==========================================

// Función de inicialización principal
function initializeApp() {
    console.log('Initializing Bingo App...');
    
    // Ajustar configuración según el dispositivo
    adjustConfigForDevice();
    
    // Inicializar gestor de recursos
    resourceManager.initialize();
    
    // Inicializar gestor de recursos
    resourceManager.initialize();
    
    // Configurar eventos
    setupEvents();
    
    // Configurar scroll mask
    setupScrollMask();
    
    // Configurar countdown del juego
    setupGameCountdown();
    
    // Iniciar polling de mensajes
    messagePoller.lastCallback = pollMessagesOptimized;
    messagePoller.poll(pollMessagesOptimized);
    
    // Iniciar último número si es necesario
    if (typeof timeBallLast !== 'undefined') {
        startAutomaticLast();
    }
    
    // Limpiar caché periódicamente
    setInterval(() => cacheSystem.cleanup(), 60000); // Cada minuto
    
    console.log('Bingo App initialized successfully');
}

// Ajustar configuración según el dispositivo
function adjustConfigForDevice() {
    if (isLowEndDevice()) {
        console.log('Low-end device detected, adjusting configuration...');
        
        // Reducir frecuencia de polling
        CONFIG.BASE_POLL_INTERVAL = 3000;
        
        // Reducir efectos visuales
        CONFIG.MAX_CONFETTI = 15;
        CONFIG.MESSAGE_LIFETIME = 20000; // 20 segundos en lugar de 30
        
        // Reducir tamaños de pool
        CONFIG.MESSAGE_POOL_SIZE = 8;
        CONFIG.AUDIO_POOL_SIZE = 5;
        CONFIG.MAX_MESSAGES = 30; // Menos mensajes en pantalla
    }
}

// ==========================================
// EVENT LISTENERS PRINCIPALES
// ==========================================

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeApp);

// Manejo de errores globales
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
    handleNetworkError(event.error, 'global');
});

// Manejo de promesas rechazadas
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
    handleNetworkError(event.reason, 'promise');
});

// Optimización para cambios de orientación en móviles
window.addEventListener('orientationchange', debounce(() => {
    // Recalcular elementos que dependen del viewport
    setTimeout(() => {
        const container = document.querySelector(".board-section");
        if (container) {
            container.dispatchEvent(new Event('scroll'));
        }
    }, 100);
}, 250));

// Optimización para cambios de tamaño de ventana
window.addEventListener('resize', debounce(() => {
    // Limpiar cache de elementos que pueden haber cambiado
    domCache.clear();
}, 250));

// ==========================================
// FUNCIONES ESPECÍFICAS PARA EL CHAT MEJORADO
// ==========================================

// Función para limpiar mensajes antiguos automáticamente
function cleanupOldMessages() {
    const display = $id("message-display");
    if (!display) return;
    
    const bubbles = Array.from(display.getElementsByClassName("message-bubble"));
    const now = Date.now();
    
    bubbles.forEach(bubble => {
        const timestamp = parseInt(bubble.dataset.timestamp || '0');
        if (now - timestamp > CONFIG.MESSAGE_LIFETIME) {
            removeMessageWithFade(bubble);
        }
    });
}

// Función para formatear mensajes con menciones y enlaces
function formatMessageContent(content) {
    // Detectar menciones (@usuario)
    content = content.replace(/@(\w+)/g, '<span class="mention">@$1</span>');
    
    // Detectar URLs simples
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    content = content.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener">$1</a>');
    
    return content;
}

// Función para mostrar indicador de escritura
function showTypingIndicator(show = true) {
    const display = $id("message-display");
    if (!display) return;
    
    let indicator = display.querySelector('.typing-indicator');
    
    if (show && !indicator) {
        indicator = document.createElement('div');
        indicator.className = 'typing-indicator message-bubble';
        indicator.innerHTML = `
            <div class="typing-dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        `;
        display.appendChild(indicator);
        scrollToBottom();
    } else if (!show && indicator) {
        indicator.remove();
    }
}

// Función para validar mensajes antes de enviar
function validateMessage(content) {
    if (!content || !content.trim()) {
        return { valid: false, error: 'El mensaje no puede estar vacío' };
    }
    
    if (content.length > 500) {
        return { valid: false, error: 'El mensaje es demasiado largo (máximo 500 caracteres)' };
    }
    
    // Filtro básico de spam
    const spamPatterns = [
        /(.)\1{10,}/, // Caracteres repetidos
        /^[A-Z\s!]{20,}$/, // Solo mayúsculas y espacios
    ];
    
    for (const pattern of spamPatterns) {
        if (pattern.test(content)) {
            return { valid: false, error: 'El mensaje parece spam' };
        }
    }
    
    return { valid: true };
}

// Función para enviar mensaje desde el campo de texto
function sendMessageText() {
    const inputField = $id('message-send-new');
    if (inputField && inputField.value.trim()) {
        const validation = validateMessage(inputField.value);
        if (validation.valid) {
            sendMessage(inputField.value);
            inputField.value = '';
        } else {
            showNotification(validation.error, 'warning');
        }
    }
}

// Función para enviar emojis (reutiliza la lógica de sendMessage)
function sendEmoji(content, id) {
    sendMessage(content, id);
}

// ==========================================
// SISTEMA DE MARCADO AUTOMÁTICO MEJORADO
// ==========================================

/**
 * Sistema de marcado de números en cartones
 */
class BingoCardMarker {
    constructor() {
        this.markedNumbers = new Set();
        this.pendingMarks = new Set();
        this.processingMarks = false;
        this.storageKey = 'bingo_marked_numbers';
        this.initialized = false;
    }
    
    init(gameId) {
        if (this.initialized) return;
        
        this.gameId = gameId || 'default';
        this.storageKey = `bingo_marked_numbers_${this.gameId}`;
        
        // Cargar números marcados desde localStorage
        this.loadMarkedNumbers();
        
        // Aplicar marcas a los elementos existentes
        this.applyMarkedNumbers();
        
        this.initialized = true;
        console.log('BingoCardMarker inicializado para el juego:', this.gameId);
    }
    
    loadMarkedNumbers() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                const numbers = JSON.parse(saved);
                this.markedNumbers = new Set(numbers);
                console.log(`Cargados ${this.markedNumbers.size} números marcados desde localStorage`);
            }
        } catch (error) {
            console.error('Error al cargar números marcados:', error);
            // Reiniciar en caso de error
            this.markedNumbers = new Set();
            localStorage.removeItem(this.storageKey);
        }
    }
    
    saveMarkedNumbers() {
        try {
            const numbers = Array.from(this.markedNumbers);
            localStorage.setItem(this.storageKey, JSON.stringify(numbers));
        } catch (error) {
            console.warn('Error al guardar números marcados:', error);
        }
    }
    
    applyMarkedNumbers() {
        // Aplicar marcas según los números guardados
        this.markedNumbers.forEach(number => {
            this.markNumberWithoutSaving(number);
        });
    }
    
    queueMark(number) {
        number = parseInt(number, 10);
        if (isNaN(number)) return;
        
        // Añadir a la cola de pendientes
        this.pendingMarks.add(number);
        
        // Procesar la cola si no está en proceso
        if (!this.processingMarks) {
            this.processMarkQueue();
        }
    }
    
    async processMarkQueue() {
        if (this.processingMarks || this.pendingMarks.size === 0) return;
        
        this.processingMarks = true;
        
        // Procesar cada número pendiente
        for (const number of this.pendingMarks) {
            this.markNumber(number);
            this.pendingMarks.delete(number);
            
            // Pequeña pausa para no bloquear el UI
            await new Promise(resolve => setTimeout(resolve, 10));
        }
        
        this.processingMarks = false;
    }
    
    markNumber(number) {
        number = parseInt(number, 10);
        if (isNaN(number)) return;
        
        // Marcar el número
        const marked = this.markNumberWithoutSaving(number);
        
        // Si se marcó correctamente, guardar
        if (marked) {
            this.markedNumbers.add(number);
            this.saveMarkedNumbers();
        }
    }
    
    markNumberWithoutSaving(number) {
        const elementsNumber = $(`.number-${number}`);
        
        if (elementsNumber.length === 0) {
            return false;
        }
        
        elementsNumber.each(function() {
            const elementNumber = $(this);
            
            if (elementNumber.hasClass('marked')) return;

            const originalContent = elementNumber.text();
            elementNumber.text('⭐️').addClass('explosive-effect marked');
            
            // Restaurar el número después de la animación
            setTimeout(function() {
                elementNumber.text(originalContent); 
                elementNumber.removeClass('explosive-effect');
                elementNumber.addClass('marked'); 
            }, 1000);
        });
        
        return true;
    }
    
    isMarked(number) {
        return this.markedNumbers.has(parseInt(number, 10));
    }
    
    clearMarks() {
        this.markedNumbers.clear();
        this.pendingMarks.clear();
        this.saveMarkedNumbers();
        
        // Limpiar marcas visuales
        $('.number-marked').removeClass('marked explosive-effect');
    }
}

// Instancia global del marcador de cartones
const bingoCardMarker = new BingoCardMarker();

// ==========================================
// AÑADIR ESTILOS CSS PARA MEJORAR LA INTERFAZ
// ==========================================
function addCustomStyles() {
    const styleElement = document.createElement('style');
    styleElement.textContent = `
    /* Variables CSS */
    :root {
        --primary-color: #6236ff;
        --secondary-color: #8767fa;
        --success-color: #4CAF50;
        --error-color: #F44336;
        --warning-color: #FF9800;
        --info-color: #2196F3;
    }
    
    /* Estilos para números marcados */
    .bingo-carton-number.marked {
        background-color: rgba(98, 54, 255, 0.8) !important;
        color: white !important;
        font-weight: bold !important;
        box-shadow: 0 0 5px rgba(98, 54, 255, 0.5) !important;
    }
    
    /* Efecto de animación para números recién marcados */
    .explosive-effect {
        animation: explosive-mark 1s ease !important;
    }
    
    @keyframes explosive-mark {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); background-color: rgba(255, 215, 0, 0.8); }
        100% { transform: scale(1); }
    }
    
    /* Estilos para notificaciones */
    .notification {
        margin-bottom: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        overflow: hidden;
    }
    
    .notification-info {
        background-color: var(--info-color);
    }
    
    .notification-success {
        background-color: var(--success-color);
    }
    
    .notification-warning {
        background-color: var(--warning-color);
    }
    
    .notification-error {
        background-color: var(--error-color);
    }
    
    /* Animación para el botón de bingo */
    .animate-click {
        animation: button-click 0.5s ease;
    }
    
    @keyframes button-click {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Estilos para mensajes del sistema */
    .system-message {
        background-color: rgba(33, 150, 243, 0.7);
        color: white;
        padding: 10px;
        border-radius: 5px;
        margin: 5px 0;
        transition: opacity 0.3s ease;
    }
    
    /* Estilos para indicador de escritura */
    .typing-indicator {
        padding: 10px;
        display: flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .typing-dots {
        display: flex;
        align-items: center;
    }
    
    .typing-dots span {
        height: 8px;
        width: 8px;
        margin: 0 2px;
        background-color: #fff;
        border-radius: 50%;
        opacity: 0.6;
        animation: typing-dot 1.4s infinite ease-in-out both;
    }
    
    .typing-dots span:nth-child(1) {
        animation-delay: -0.32s;
    }
    
    .typing-dots span:nth-child(2) {
        animation-delay: -0.16s;
    }
    
    @keyframes typing-dot {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    `;
    document.head.appendChild(styleElement);
}

// ==========================================
// EXPORTAR FUNCIONES PARA USO GLOBAL
// ==========================================

// Hacer disponibles las funciones principales globalmente
window.BingoPlayer = {
    dialNumber,
    singBingo,
    toggleAutoMark: function() {
        if (typeof autoMarkEnabled !== 'undefined') {
            autoMarkEnabled = !autoMarkEnabled;
            
            // Actualizar interfaz
            const autoMarkButton = $('.btn-auto-mark');
            if (autoMarkButton.length) {
                autoMarkButton.html(autoMarkEnabled ? 
                    '<i class="fa-duotone fa-solid fa-check-double"></i>' : 
                    '<i class="fa-duotone fa-solid fa-xmark"></i>'
                );
            }
            
            // Guardar preferencia
            try {
                localStorage.setItem('bingo_auto_mark_enabled', autoMarkEnabled ? '1' : '0');
            } catch (e) {
                console.warn('Error al guardar preferencia de auto-marcado:', e);
            }
            
            return autoMarkEnabled;
        }
        return false;
    },
    showNotification,
    
    // Funciones del chat
    sendMessage,
    sendEmoji,
    
    // Gestores
    audioManager,
    bingoCardMarker,
    
    // Estado
    get offlineMode() { return offlineMode; },
    get numbersGenerated() { return numbersgenerated; }
};

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Añadir estilos personalizados
    addCustomStyles();
    
    // Inicializar la aplicación
    initializeApp();
    
    // Inicializar el marcador de cartones con el ID del juego actual
    const gameId = document.body.dataset.gameId || 'default';
    bingoCardMarker.init(gameId);
});

console.log('Bingo Player script loaded successfully');
