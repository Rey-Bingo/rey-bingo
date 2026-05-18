// ==========================================
// CONFIGURACIÓN Y CONSTANTES
// ==========================================
const CONFIG = {
    MAX_MESSAGES: 50,
    MAX_CONFETTI: 100,
    BASE_POLL_INTERVAL: 2000,
    MAX_POLL_INTERVAL: 10000,
    USER_COUNT_INTERVAL: 2500,
    ACCUMULATED_COUNT_INTERVAL: 2500,
    MESSAGE_LIFETIME: 30000,
    FADE_OUT_TIME: 500,
    DEBOUNCE_DELAY: 100,
    AUDIO_POOL_SIZE: 10,
    MESSAGE_POOL_SIZE: 15,
    PUSHER_RECONNECT_DELAY: 3000
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
let gameStarted = false;
let pusherClient = null;
let pusherChannel = null;
let autoDrawInterval = 3000;
let isDrawing = false;
let drawSpeed = 3000; // 3 segundos por defecto
let availableNumbers = [];

// ==========================================
// GESTORES DE RECURSOS
// ==========================================

// Gestor centralizado de intervalos
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

// Cache de elementos DOM
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

// Gestor inteligente de audio
class AudioManager {
    constructor() {
        this.audioCache = new Map();
        this.preloadedAudios = new Set();
        this.audioPool = [];
    }
    
    preload(src) {
        if (this.preloadedAudios.has(src)) return;
        
        const audio = new Audio();
        audio.preload = 'auto';
        audio.src = src;
        this.audioCache.set(src, audio);
        this.preloadedAudios.add(src);
    }
    
    play(src) {
        let audio = this.audioCache.get(src);
        if (!audio) {
            audio = new Audio();
            audio.src = src;
            this.audioCache.set(src, audio);
        }
        
        // Clone para permitir múltiples reproducciones simultáneas
        const audioClone = audio.cloneNode();
        audioClone.play().catch(e => console.warn('Audio play failed:', e));
        
        return audioClone;
    }
    
    preloadNumberAudios() {
        // Precargar audios de números 1-75
        for (let i = 1; i <= 75; i++) {
            this.preload(audioPath + i + '.mp3');
        }
        this.preload(audioPath + 'winner.mp3');
    }
}

// Confetti optimizado con Canvas
class CanvasConfetti {
    constructor() {
        this.particles = [];
        this.isActive = false;
        this.activeElements = new Set();
    }
    
    createParticles() {
        const emojis = ['🎉', '🎊', '✨', '🌟', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
        this.particles = [];
        
        // Limpiar partículas anteriores si existen
        this.cleanup();
        
        for (let i = 0; i < CONFIG.MAX_CONFETTI; i++) {
            // Crear elemento DOM para cada partícula
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.textContent = emojis[Math.floor(Math.random() * emojis.length)];
            
            // Propiedades mejoradas basadas en tu función preferida
            const particle = {
                element: confetti,
                emoji: confetti.textContent,
                x: Math.random() * 100,
                y: Math.random() * -100,
                vx: (Math.random() - 0.5) * 2,
                vy: Math.random() * 1.5 + 0.5, // velocidad más lenta
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 3,
                size: Math.random() * 30 + 10,
                alpha: 1,
                decay: Math.random() * 0.02 + 0.01,
                animationDuration: Math.random() * 6 + 4, // animación más lenta
                animationDelay: Math.random()
            };
            
            // Aplicar estilos CSS mejorados
            confetti.style.cssText = `
                position: fixed;
                left: ${particle.x}vw;
                top: ${particle.y}vh;
                font-size: ${particle.size}px;
                animation-duration: ${particle.animationDuration}s;
                animation-delay: ${particle.animationDelay}s;
                animation-name: confettiFall;
                animation-timing-function: ease-out;
                animation-fill-mode: forwards;
                pointer-events: none;
                z-index: 9999;
                transform: rotate(${particle.rotation}deg);
                user-select: none;
            `;
            
            // Agregar al DOM
            document.body.appendChild(confetti);
            this.activeElements.add(confetti);
            
            // Auto-eliminar cuando termine la animación
            const handleAnimationEnd = () => {
                if (confetti.parentNode) {
                    confetti.parentNode.removeChild(confetti);
                }
                this.activeElements.delete(confetti);
                confetti.removeEventListener('animationend', handleAnimationEnd);
            };
            
            confetti.addEventListener('animationend', handleAnimationEnd);
            
            this.particles.push(particle);
        }
    }
    
    cleanup() {
        // Limpiar elementos activos
        this.activeElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        this.activeElements.clear();
        this.particles = [];
    }
    
    start() {
        if (this.isActive) return;
        
        this.isActive = true;
        this.createParticles();
        
        // Auto-stop después de la duración máxima de animación
        setTimeout(() => {
            this.stop();
        }, 6000); // 5s max duration + 1s buffer
    }
    
    stop() {
        this.isActive = false;
        // Los elementos se limpiarán automáticamente cuando termine su animación
    }
    
    forceStop() {
        this.isActive = false;
        this.cleanup();
    }
    
    resize() {
        // Método para manejar cambios de tamaño
        if (this.isActive) {
            this.forceStop();
            setTimeout(() => this.start(), 100);
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
const confettiManager = new CanvasConfetti();

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
    
    // Enviar a través de Pusher en lugar de AJAX
    if (pusherChannel && pusherChannel.subscribed) {
        try {
            pusherChannel.trigger('client-message', {
                userId: session_id || 'admin',
                userName: 'Admin',
                userImage: imagePath || 'default-avatar.png',
                message: trimmedContent,
                timestamp: new Date().toISOString()
            });
            
            // Limpiar campo de texto
            const inputField = document.getElementById('message-send-new');
            if (inputField) {
                inputField.value = '';
            }
        } catch (error) {
            console.error('Error al enviar mensaje por Pusher:', error);
            
            // Fallback a AJAX si Pusher falla
            $.post(site_url + 'playings/messageSubmit', { message: trimmedContent })
                .done((data) => {
                    if (data.status === 'success') {
                        $('#message-send-new').val('');
                    }
                })
                .fail(() => {
                    console.warn('Error al enviar mensaje');
                });
        }
    } else {
        // Fallback a AJAX si Pusher no está disponible
        $.post(site_url + 'playings/messageSubmit', { message: trimmedContent })
            .done((data) => {
                if (data.status === 'success') {
                    $('#message-send-new').val('');
                }
            })
            .fail(() => {
                console.warn('Error al enviar mensaje');
            });
    }
}

// Función para enviar emojis (reutiliza la lógica de sendMessage)
function sendEmoji(content, id) {
    sendMessage(content, id);
}

// Función para enviar mensaje desde el campo de texto
function sendMessageText() {
    const inputField = $id('message-send-new');
    if (inputField && inputField.value.trim()) {
        sendMessage(inputField.value);
        inputField.value = '';
    }
}

// ==========================================
// FUNCIONES PRINCIPALES DE BINGO
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
        winnerSliderTimeout = setTimeout(showNext, 5000);
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
    audioManager.play(audioPath + 'winner.mp3');

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
                    if (callback) callback();
                }
            }, 1000);
        }, 3000);
    }, 2000);

    confettiManager.start();
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
    
    const el = $id('number-' + newNumber);
    if (el) el.removeAttribute('onclick');

    const centerBlock = $id('block-number');
    const centerBall = $id('last-number-center');
    
    if (!centerBlock || !centerBall) return;

    centerBlock.style.display = 'flex';
    centerBall.innerHTML = `<small style="position: absolute; top: -1px; font-size: 2.5rem; z-index: 1;">${getColumnClass(newNumber)}</small><span>${newNumber}</span>`;
    centerBall.className = `bingo-ball-200 ${getColumnClass(newNumber)} size-200`;
    centerBall.style.display = 'flex';

    setTimeout(() => {
        centerBall.style.transform = 'translate(-50%, -50%) scale(0)';
        centerBall.style.opacity = '0';
        
        setTimeout(() => {
            centerBall.removeAttribute('style');
            centerBall.className = '';
            centerBlock.style.display = 'none';
            
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
    }, 7000);

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

    const numberEl = $("#number-" + newNumber);
    if (numberEl.length) {
        numberEl.addClass(`bingo-ball ${getColumnClass(newNumber)} size-50`);
    }
}

function formatTimeUnit(value) {
    return value < 10 ? `0${value}` : value;
}

function updateGameTimer() {
    const now = new Date();
    const diffMs = now - startTime;

    const totalSeconds = Math.floor(diffMs / 1000);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    const label = hours > 0
        ? `HORA${hours > 1 ? 'S' : ''}`
        : minutes > 0
            ? `MINUTO${minutes > 1 ? 'S' : ''}`
            : `SEGUNDO${seconds > 1 ? 'S' : ''}`;

    const timeText = `${formatTimeUnit(hours)}:${formatTimeUnit(minutes)}:${formatTimeUnit(seconds)}`;

    $('.init-count small').text(label);
    $('.init-count .time-text').text(timeText);
}

// ==========================================
// FUNCIONES DE PUSHER
// ==========================================

// Inicializar los números disponibles (1-75)
function initAvailableNumbers() {
    availableNumbers = [];
    for (let i = 1; i <= 75; i++) {
        // Verificar si el número ya está marcado en el tablero
        const numberElement = document.getElementById('number-' + i);
        if (numberElement && !numberElement.classList.contains('B') && 
            !numberElement.classList.contains('I') && 
            !numberElement.classList.contains('N') && 
            !numberElement.classList.contains('G') && 
            !numberElement.classList.contains('O')) {
            availableNumbers.push(i);
        }
    }
    console.log('Números disponibles:', availableNumbers.length);
}

// Función para generar un número aleatorio entre los disponibles
function getRandomNumber() {
    if (availableNumbers.length === 0) {
        stopAutoDraw();
        alert('No hay más números disponibles para sortear');
        return null;
    }
    
    const randomIndex = Math.floor(Math.random() * availableNumbers.length);
    const number = availableNumbers[randomIndex];
    availableNumbers.splice(randomIndex, 1);
    return number;
}

// Función para enviar un número al servidor y a través de Pusher
async function sendNumberToServer(number) {
    try {
        // Enviar al servidor
        const response = await fetch(`${site_url}boards/numberSubmit/${number}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        console.log('Respuesta del servidor:', data);
        
        // Enviar a través de Pusher
        if (pusherChannel && pusherChannel.subscribed) {
            pusherChannel.trigger('game:number_drawn', {
                n: number,
                totalNumbersGenerated: data.totalNumbersGenerated || numbersgenerated.length + 1,
                timestamp: Date.now()
            });
        }
        
        if (data.status === 'completed' || data.status === 'pause') {
            stopAutoDraw();
            
            if (data.status === 'pause') {
                // Reiniciar después de la pausa
                setTimeout(() => {
                    if (isDrawing) startAutoDraw();
                }, 11000); // 11 segundos (10 de pausa + 1 de margen)
            }
        }
        
        return data;
    } catch (error) {
        console.error('Error al enviar número:', error);
        return null;
    }
}

// Función para sortear un número automáticamente
async function drawNumber() {
    const number = getRandomNumber();
    if (number === null) return;
    
    console.log('Sorteando número:', number);
    
    // Enviar el número al servidor y a través de Pusher
    const result = await sendNumberToServer(number);
    
    // Si el juego se detuvo o pausó, no continuar
    if (result && (result.status === 'completed' || result.status === 'pause')) {
        if (result.status === 'completed') {
            isDrawing = false;
            showControls('start');
        }
    }
}

// Iniciar sorteo automático
function startAutoDraw() {
    if (autoDrawInterval) {
        clearInterval(autoDrawInterval);
    }
    
    initAvailableNumbers();
    
    if (availableNumbers.length === 0) {
        alert('No hay más números disponibles para sortear');
        return;
    }
    
    isDrawing = true;
    showControls('stop');
    
    // Sortear el primer número inmediatamente
    drawNumber();
    
    // Configurar el intervalo para los siguientes números
    autoDrawInterval = setInterval(drawNumber, drawSpeed);
}

// Detener sorteo automático
function stopAutoDraw() {
    if (autoDrawInterval) {
        clearInterval(autoDrawInterval);
        autoDrawInterval = null;
    }
    
    isDrawing = false;
    showControls('start');
}

// Mostrar/ocultar controles según el estado
function showControls(state) {
    const startButton = document.getElementById('start-button');
    const playButton = document.getElementById('play-button');
    const stopButton = document.getElementById('stop-button');
    const nextNumberButton = document.getElementById('next-number-button');
    
    if (state === 'start') {
        if (startButton) startButton.style.display = 'block';
        if (playButton) playButton.style.display = 'none';
        if (stopButton) stopButton.style.display = 'none';
        if (nextNumberButton) nextNumberButton.style.display = 'none';
    } else if (state === 'stop') {
        if (startButton) startButton.style.display = 'none';
        if (playButton) playButton.style.display = 'none';
        if (stopButton) stopButton.style.display = 'block';
        if (nextNumberButton) nextNumberButton.style.display = 'block';
    }
}

// Inicializar Pusher
function initPusher() {
    if (!PUSHER_KEY || !PUSHER_CLUSTER) {
        console.error('Faltan las credenciales de Pusher');
        return;
    }
    
    try {
        // Inicializar cliente Pusher
        pusherClient = new Pusher(PUSHER_KEY, {
            cluster: PUSHER_CLUSTER,
            channelAuthorization: {
                endpoint: AUTH_URL,
                transport: 'ajax',
            },
        });
        
        // Suscribirse al canal del juego
        const channelName = 'private-game-' + GAME_ID;
        pusherChannel = pusherClient.subscribe(channelName);
        
        console.log('Conectando a canal:', channelName);
        
        // Eventos de Pusher
        pusherChannel.bind('pusher:subscription_succeeded', () => {
            console.log('✅ SUSCRITO correctamente a ' + channelName);
        });
        
        pusherChannel.bind('pusher:subscription_error', (error) => {
            console.error('❌ ERROR de suscripción:', error);
            
            // Reintentar después de un tiempo
            setTimeout(() => {
                console.log('Reintentando conexión a Pusher...');
                initPusher();
            }, CONFIG.PUSHER_RECONNECT_DELAY);
        });
        
        // Evento cuando se genera un nuevo número
        pusherChannel.bind('game:number_drawn', (data) => {
            console.log('📡 Evento Pusher: número generado', data);
            
            // Actualizar el tablero con el nuevo número
            handleNewNumber(data.n, data.totalNumbersGenerated);
            
            // Actualizar la lista de números disponibles
            const index = availableNumbers.indexOf(parseInt(data.n));
            if (index > -1) {
                availableNumbers.splice(index, 1);
            }
        });
        
        // Evento cuando alguien canta bingo
        pusherChannel.bind('game:bingo_claimed', (data) => {
            console.log('📡 Reclamo de BINGO recibido:', data);
            showBingoNotification(data);
            
            // Pausar el sorteo automático si está activo
            if (isDrawing) {
                clearInterval(autoDrawInterval);
                autoDrawInterval = null;
                
                // Reiniciar después de 10 segundos si sigue en modo de sorteo
                setTimeout(() => {
                    if (isDrawing) {
                        autoDrawInterval = setInterval(drawNumber, drawSpeed);
                    }
                }, 11000); // 11 segundos (10 de pausa + 1 de margen)
            }
        });
        
        // Evento cuando se acepta un bingo
        pusherChannel.bind('game:bingo_accepted', (data) => {
            console.log('📡 BINGO ACEPTADO:', data);
            showBingoWinner(data);
            
            // Si el juego se detuvo, detener el sorteo automático
            if (data.stopped) {
                stopAutoDraw();
            }
        });
        
        // Evento cuando el juego termina
        pusherChannel.bind('game:completed', (data) => {
            console.log('📡 Juego completado:', data);
            gameCompleted(data);
            
            // Detener el sorteo automático
            stopAutoDraw();
        });
        
        // Evento para mensajes de chat
        pusherChannel.bind('client-message', (data) => {
            console.log('📡 Mensaje recibido:', data);
            
            // Solo mostrar mensajes de otros usuarios
            if (data.userId !== session_id) {
                displayMessage({
                    message: data.message,
                    id: Date.now()
                }, data.userImage);
            }
        });
        
    } catch (error) {
        console.error('Error al inicializar Pusher:', error);
        
        // Reintentar después de un tiempo
        setTimeout(() => {
            console.log('Reintentando conexión a Pusher...');
            initPusher();
        }, CONFIG.PUSHER_RECONNECT_DELAY);
    }
}

// Función para mostrar notificación de bingo
function showBingoNotification(data) {
    // Mostrar notificación de bingo
    const countdownContainer = document.getElementById('countdown-container');
    const textCountdown = document.getElementById('text-countdown');
    
    if (countdownContainer && textCountdown) {
        textCountdown.innerHTML = `
            <div class="player-info">
                <img src="${data.image}" class="player-avatar">
                <div class="player-name">${data.playerName || data.player}</div>
            </div>
            <div class="bingo-text">¡BINGO!</div>
            <div class="modality-name">${data.modality}</div>
        `;
        
        countdownContainer.style.display = 'flex';
        
        // Iniciar cuenta regresiva
        let count = 10;
        const countdown = document.getElementById('countdown');
        if (countdown) {
            countdown.textContent = count;
            
            const interval = setInterval(() => {
                count--;
                countdown.textContent = count;
                if (count <= 0) {
                    clearInterval(interval);
                    countdownContainer.style.display = 'none';
                }
            }, 1000);
        }
    }
}

// Función para mostrar ganador del bingo
function showBingoWinner(data) {
    // Mostrar ganador del bingo
    const gameFinalized = document.getElementById('game-finalized');
    const finalized = document.getElementById('finalized');
    
    if (gameFinalized && finalized) {
        finalized.innerHTML = `
            <div class="winner-info">
                <img src="${data.image}" class="winner-avatar">
                <div class="winner-name">${data.playerName || data.player}</div>
            </div>
            <div class="winner-text">¡GANADOR!</div>
            <div class="modality-name">${data.modality}</div>
        `;
        
        gameFinalized.style.display = 'flex';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            gameFinalized.style.display = 'none';
        }, 5000);
    }
}

// Función para manejar el fin del juego
function gameCompleted(data) {
    // Mostrar mensaje de juego completado
    alert(data.message || 'El juego ha terminado');
    
    // Deshabilitar controles
    const controls = document.getElementById('controls');
    if (controls) {
        controls.style.display = 'none';
    }
    
    // Mostrar pantalla de fin de juego
    showGameFinalized();
}

// Funciones de generación de números optimizadas
function generateNumber(number) {
    // Iniciar conteo solo la primera vez que se llama manualmente
    if (!gameStarted) {
        gameStarted = true;
        startTime = new Date();
        updateGameTimer();
        gameTimerInterval = setInterval(updateGameTimer, 1000);
        sendMessage((__['game started!'] || '¡JUEGO INICIADO!') + ' 🎯', 26);
    }

    // Eliminar el número de la lista de disponibles
    const index = availableNumbers.indexOf(parseInt(number));
    if (index > -1) {
        availableNumbers.splice(index, 1);
    }
    
    // Enviar el número al servidor y a través de Pusher
    sendNumberToServer(number)
        .then(data => {
            if (data.status === 'pause') {
                showCountdown(data, startAutoDraw);
            } else if (data.status === 'completed') {
                showGameFinalized();
            } else if (data.status === 'success') {
                handleNewNumber(data.number, data.totalNumbersGenerated);
            }
        })
        .catch(error => {
            console.error('Error al generar número manualmente:', error);
        });
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

    stopAutoDraw();
    
    if (gameTimerInterval) {
        clearInterval(gameTimerInterval);
        gameTimerInterval = null;
    }

    const controlsDiv = $id('controls');
    if (controlsDiv) {
        controlsDiv.remove();
    }
}

// Contador de usuarios optimizado
const updateUserCount = throttle(() => {
    $.get(site_url + 'boards/playersGetCount')
        .done((data) => {
            const countEl = $('.count_notifications');
            if (data.status === 'success') {
                if (data.userCount && data.userCount > 0) {
                    countEl.text(data.userCount).show();
                } else {
                    countEl.hide();
                }
            } else {
                if (data.userCount && data.userCount > 0) {
                    countEl.text(data.userCount).show();
                } else {
                    countEl.hide();
                }
            }
        })
        .fail(() => {
            console.warn('Failed to update user count');
        });
}, 1000);

// Contador de acumulado optimizado
const updateGameAccumulated = throttle(() => {
    $.get(site_url + 'games/gameGetAccumulated')
        .done((data) => {
            const accumulatedEl = $('#accumulated-counter');

            if (data.status === 'success') {
                accumulatedEl.text(currency + ' ' + data.gameAccumulated);

                if (data.modalities && data.modalities.length > 0) {
                    data.modalities.forEach(modality => {
                        const modalityEl = $('#modality-amount-' + modality.id);
                        if (modalityEl.length > 0) {
                            modalityEl.text(currency + ' ' + modality.amount);
                        }
                    });
                }
            } else {
                accumulatedEl.text(currency + ' ' + data.gameAccumulated);

                if (data.modalities && data.modalities.length > 0) {
                    data.modalities.forEach(modality => {
                        const modalityEl = $('#modality-amount-' + modality.id);
                        if (modalityEl.length > 0) {
                            modalityEl.text(currency + ' ' + modality.amount);
                        }
                    });
                }
            }
        })
        .fail(() => {
            console.warn('Failed to update accumulated');
        });
}, 1000);

function RemoveVolume() {
    $.ajax({
        url: site_url + 'playings/volumeSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("Sound disabled successfully");
                
                // Actualizar UI
                const volumeBtn = document.querySelector('.btn-volume i');
                if (volumeBtn) {
                    if (volumeBtn.classList.contains('fa-volume')) {
                        volumeBtn.classList.remove('fa-volume');
                        volumeBtn.classList.add('fa-volume-slash');
                    } else {
                        volumeBtn.classList.remove('fa-volume-slash');
                        volumeBtn.classList.add('fa-volume');
                    }
                }
            }
        },
        error: function() {
            console.warn("Error disabling sound");
        }
    });
}

function RemoveMicrophone() {
    $.ajax({
        url: site_url + 'playings/microphoneSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("Narrator disabled successfully");
                
                // Actualizar UI
                const micBtn = document.querySelector('.btn-microphone i');
                if (micBtn) {
                    if (micBtn.classList.contains('fa-microphone')) {
                        micBtn.classList.remove('fa-microphone');
                        micBtn.classList.add('fa-microphone-slash');
                    } else {
                        micBtn.classList.remove('fa-microphone-slash');
                        micBtn.classList.add('fa-microphone');
                    }
                }
            }
        },
        error: function() {
            console.warn("Error disabling narrator");
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

    // Eventos de control del juego
    $('#start-button').on('click', () => {
        $('#start-button').hide();
        $('#stop-button, #next-number-button').show();
        sendMessage((__['game started!'] || '¡JUEGO INICIADO!') + ' 😎', 26);

        if (!gameStarted) {
            gameStarted = true;
            startTime = new Date();
            updateGameTimer();
            gameTimerInterval = setInterval(updateGameTimer, 1000);
        }

        setTimeout(() => {
            startAutoDraw();
        }, 2000);
    });

    $('#next-number-button').on('click', () => {
        if (autoDrawInterval) {
            clearInterval(autoDrawInterval);
            autoDrawInterval = null;
        }
        drawNumber();
        autoDrawInterval = setInterval(drawNumber, drawSpeed);
    });

    $('#stop-button').on('click', () => {
        stopAutoDraw();
        $('#stop-button, #next-number-button').hide();
        $('#play-button').show();
    });

    $('#play-button').on('click', () => {
        startAutoDraw();
        $('#play-button').hide();
        $('#stop-button, #next-number-button').show();
    });

    // Control de micrófono
    $('.btn-microphone').on('click', function() {
        if (typeof narrationPlaying !== 'undefined') {
            narrationPlaying = !narrationPlaying;
            $(this).html(narrationPlaying ? 
                '<i class="fa-duotone fa-solid fa-microphone"></i>' : 
                '<i class="fa-duotone fa-solid fa-microphone-slash"></i>'
            );
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
                nextGameSpan.textContent = 'LOS JUGADORES ESPERAN EL INICIO DE LA PARTIDA...';
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
            nextGameSpan.textContent = 'LOS JUGADORES ESPERAN EL INICIO DE LA PARTIDA...';
        }
    }
}

// ==========================================
// GESTIÓN DE RECURSOS Y LIMPIEZA
// ==========================================
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
        
        // Detener Pusher
        if (pusherClient) {
            if (pusherChannel) {
                pusherClient.unsubscribe('private-game-' + GAME_ID);
            }
            pusherClient.disconnect();
        }
        
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

        if (autoDrawInterval) {
            clearInterval(autoDrawInterval);
            autoDrawInterval = null;
        }

        // Detener confetti
        confettiManager.forceStop();
        
        // Limpiar cache DOM
        domCache.clear();
        
        // Limpiar arrays
        messagesDisplayed.length = 0;
        winners.length = 0;
        
        console.log('Resource cleanup completed');
    }

    initialize() {
        this.isCleaningUp = false;
        
        // Precargar recursos de audio
        audioManager.preloadNumberAudios();
        
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
        
        // Reiniciar Pusher si está desconectado
        if (pusherClient && pusherClient.connection.state !== 'connected') {
            console.log('Reiniciando conexión Pusher...');
            initPusher();
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

// ==========================================
// INICIALIZACIÓN PRINCIPAL
// ==========================================
const resourceManager = new ResourceManager();

// Función de inicialización principal
function initializeApp() {
    console.log('Initializing Bingo Admin App with Pusher...');
    
    // Inicializar gestor de recursos
    resourceManager.initialize();
    
    // Inicializar Pusher
    initPusher();
    
    // Inicializar números disponibles
    initAvailableNumbers();
    
    // Configurar eventos
    setupEvents();
    
    // Configurar scroll mask
    setupScrollMask();
    
    // Configurar countdown del juego
    setupGameCountdown();
    
    // Iniciar contador de usuarios
    setInterval(updateUserCount, CONFIG.USER_COUNT_INTERVAL);
    updateUserCount();

    // Iniciar contador de acumulado
    setInterval(updateGameAccumulated, CONFIG.ACCUMULATED_COUNT_INTERVAL);
    updateGameAccumulated();
    
    // Sincronizar con el estado actual del juego
    fetch(site_url + "boards/numberGet", {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Estado inicial del juego:', data);
        
        // Si el juego está completado o pausado, deshabilitar controles
        if (data.status === 'completed') {
            const controls = document.getElementById('controls');
            if (controls) {
                controls.style.display = 'none';
            }
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error al obtener estado inicial:', error);
    });
    
    console.log('Bingo Admin App initialized successfully');
}

// ==========================================
// EVENT LISTENERS PRINCIPALES
// ==========================================

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeApp);

// Manejo de errores globales
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
});

// Manejo de promesas rechazadas
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason);
});

// Optimización para cambios de orientación en móviles
window.addEventListener('orientationchange', debounce(() => {
    // Recalcular elementos que dependen del viewport
    confettiManager.resize();
    
    // Forzar recálculo de máscaras de scroll
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
    
    // Recalcular confetti canvas
    confettiManager.resize();
}, 250));

// ==========================================
// EXPORTAR FUNCIONES PARA USO GLOBAL
// ==========================================

// Hacer disponibles las funciones principales globalmente para compatibilidad
window.BingoApp = {
    // Funciones principales
    sendMessage,
    sendEmoji,
    sendMessageText,
    generateNumber,
    startAutoDraw,
    stopAutoDraw,
    showGameFinalized,
    RemoveVolume,
    RemoveMicrophone,
    
    // Funciones del chat mejorado
    displayMessage,
    
    // Gestores
    intervalManager,
    audioManager,
    resourceManager,
    confettiManager,
    messagePool,
    
    // Estado
    get winners() { return winners; },
    get numbersGenerated() { return numbersgenerated; },
    get isGameFinished() { return isGameFinishedShown; },
    get messagesDisplayed() { return messagesDisplayed; }
};

console.log('Bingo Admin App script loaded successfully');
