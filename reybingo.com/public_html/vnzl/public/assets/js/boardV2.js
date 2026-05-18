// ==========================================
// CONFIGURACIÓN Y CONSTANTES
// ==========================================
const CONFIG = {
    MAX_MESSAGES: 5,
    MAX_CONFETTI: 30,
    BASE_POLL_INTERVAL: 2000,
    MAX_POLL_INTERVAL: 10000,
    USER_COUNT_INTERVAL: 2500,
    MESSAGE_LIFETIME: 3000,
    DEBOUNCE_DELAY: 100,
    AUDIO_POOL_SIZE: 10,
    MESSAGE_POOL_SIZE: 15
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

// Pool de elementos de mensajes para reutilización
class MessagePool {
    constructor(maxSize = CONFIG.MESSAGE_POOL_SIZE) {
        this.pool = [];
        this.maxSize = maxSize;
    }
    
    get() {
        if (this.pool.length > 0) {
            return this.pool.pop();
        }
        return this.createNew();
    }
    
    release(element) {
        if (this.pool.length < this.maxSize) {
            // Reset element
            element.className = 'message-bubble';
            element.style.cssText = '';
            element.innerHTML = '';
            this.pool.push(element);
        }
    }
    
    createNew() {
        const bubble = document.createElement("div");
        bubble.classList.add("message-bubble");
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
        this.preload(audioPath + 'winning.mp3');
    }
}

// Polling inteligente con backoff exponencial
class SmartPoller {
    constructor(baseInterval = CONFIG.BASE_POLL_INTERVAL) {
        this.baseInterval = baseInterval;
        this.currentInterval = baseInterval;
        this.maxInterval = CONFIG.MAX_POLL_INTERVAL;
        this.consecutiveErrors = 0;
        this.isActive = true;
        this.timeoutId = null;
    }
    
    async poll(callback) {
        if (!this.isActive) return;
        
        try {
            const result = await callback();
            
            // Reset interval on success
            if (result && result.status === 'success') {
                this.currentInterval = this.baseInterval;
                this.consecutiveErrors = 0;
            }
            
        } catch (error) {
            this.consecutiveErrors++;
            // Exponential backoff on errors
            this.currentInterval = Math.min(
                this.baseInterval * Math.pow(2, this.consecutiveErrors),
                this.maxInterval
            );
            console.warn('Polling error:', error);
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
        this.poll(this.lastCallback);
    }
}

// Confetti optimizado con Canvas
class CanvasConfetti {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.isActive = false;
        this.animationId = null;
        
        this.setupCanvas();
    }
    
    setupCanvas() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.pointerEvents = 'none';
        this.canvas.style.zIndex = '9999';
        this.resize();
        
        window.addEventListener('resize', () => this.resize());
    }
    
    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }
    
    createParticles() {
        const emojis = ['🎉', '🎊', '✨', '🌟', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
        this.particles = [];
        
        for (let i = 0; i < CONFIG.MAX_CONFETTI; i++) {
            this.particles.push({
                emoji: emojis[Math.floor(Math.random() * emojis.length)],
                x: Math.random() * this.canvas.width,
                y: -50,
                vx: (Math.random() - 0.5) * 4,
                vy: Math.random() * 3 + 2,
                rotation: Math.random() * 360,
                rotationSpeed: (Math.random() - 0.5) * 10,
                size: Math.random() * 30 + 10,
                alpha: 1,
                decay: Math.random() * 0.02 + 0.01
            });
        }
    }
    
    animate() {
        if (!this.isActive) return;
        
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        for (let i = this.particles.length - 1; i >= 0; i--) {
            const particle = this.particles[i];
            
            particle.x += particle.vx;
            particle.y += particle.vy;
            particle.rotation += particle.rotationSpeed;
            particle.vy += 0.1; // gravity
            particle.alpha -= particle.decay;
            
            this.ctx.save();
            this.ctx.globalAlpha = particle.alpha;
            this.ctx.translate(particle.x, particle.y);
            this.ctx.rotate(particle.rotation * Math.PI / 180);
            this.ctx.font = `${particle.size}px Arial`;
            this.ctx.textAlign = 'center';
            this.ctx.fillText(particle.emoji, 0, 0);
            this.ctx.restore();
            
            if (particle.y > this.canvas.height + 50 || particle.alpha <= 0) {
                this.particles.splice(i, 1);
            }
        }
        
        if (this.particles.length > 0) {
            this.animationId = requestAnimationFrame(() => this.animate());
        } else {
            this.stop();
        }
    }
    
    start() {
        if (this.isActive) return;
        
        this.isActive = true;
        document.body.appendChild(this.canvas);
        this.createParticles();
        this.animate();
    }
    
    stop() {
        this.isActive = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
            this.animationId = null;
        }
        if (this.canvas.parentNode) {
            this.canvas.parentNode.removeChild(this.canvas);
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
// FUNCIONES PRINCIPALES
// ==========================================

// Scroll optimizado con debounce
const debouncedScroll = debounce(() => {
    const el = $id("message-display");
    if (el) el.scrollTop = el.scrollHeight;
}, CONFIG.DEBOUNCE_DELAY);

function scrollToBottom() {
    debouncedScroll();
}

function getColumnClass(number) {
    if (number <= 15) return 'B';
    if (number <= 30) return 'I';
    if (number <= 45) return 'N';
    if (number <= 60) return 'G';
    return 'O';
}

function createMessageBubbleOptimized(content, profilePicUrl) {
    const bubble = messagePool.get();
    
    // Reutilizar o crear imagen
    let img = bubble.querySelector('.profile-pic');
    if (!img) {
        img = document.createElement("img");
        img.classList.add("profile-pic");
        bubble.appendChild(img);
    }
    img.src = profilePicUrl;
    
    // Reutilizar o crear span
    let span = bubble.querySelector('span');
    if (!span) {
        span = document.createElement("span");
        bubble.appendChild(span);
    }
    span.textContent = content;
    span.className = /[\u1F600-\u1F6FF]/.test(content) ? "text-message" : "emoji-message";
    
    return bubble;
}

function removeMessageWithFade(el) {
    el.classList.add("fade-out");
    setTimeout(() => {
        if (el.parentNode) {
            el.parentNode.removeChild(el);
            messagePool.release(el);
        }
    }, 500);
}

function limitMessages() {
    const display = $id("message-display");
    if (!display) return;
    
    const bubbles = display.getElementsByClassName("message-bubble");
    while (bubbles.length >= CONFIG.MAX_MESSAGES) {
        removeMessageWithFade(bubbles[0]);
    }
}

function displayMessage(messageData, imageUrl) {
    const display = $id("message-display");
    if (!display) return;
    
    limitMessages();
    const bubble = createMessageBubbleOptimized(messageData.message || messageData, imageUrl);
    
    display.appendChild(bubble);
    messagesDisplayed.push(messageData.id);
    scrollToBottom();
    
    setTimeout(() => removeMessageWithFade(bubble), CONFIG.MESSAGE_LIFETIME);
}

function sendMessage(content, id) {
    if (!content || !content.trim()) return;
    
    const trimmedContent = content.trim();
    displayMessage({ message: trimmedContent, id: id || Date.now() }, imagePath);
    
    $.post(site_url + 'playings/messageSubmit', { message: trimmedContent })
        .done((data) => {
            if (data.status === 'success') {
                $('#message-send-new').val('');
            }
        })
        .fail(() => {
            console.warn('Failed to send message');
        });
}

function sendEmoji(content, id) {
    sendMessage(content, id); // Reutilizar la lógica optimizada
}

// Polling optimizado de mensajes
function pollMessagesOptimized() {
    return new Promise((resolve) => {
        $.get(site_url + 'playings/messageGet')
            .done((data) => {
                if (data.status === 'stop') {
                    messagePoller.stop();
                    return resolve(data);
                }
                
                if (data.status === 'success' && data.message && !messagesDisplayed.includes(data.message.id)) {
                    displayMessage(data.message, data.image);
                }
                resolve(data);
            })
            .fail((error) => {
                console.warn('Message polling failed:', error);
                resolve({ status: 'error' });
            });
    });
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
    audioManager.play(audioPath + 'winning.mp3');

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

// Funciones de generación de números optimizadas
function generateAutoNumber() {
    $.get(site_url + 'boards/numberAutoSubmit')
        .done((data) => {
            if (data.status === 'pause') {
                showCountdown(data, startAutomaticGeneration);
            } else if (data.status === 'completed') {
                showGameFinalized();
            } else if (data.status === 'success') {
                handleNewNumber(data.number, data.totalNumbersGenerated);
            }
        })
        .fail(() => {
            console.warn('Failed to generate auto number');
        });
}

function generateNumber(number) {
    $.get(site_url + 'boards/numberSubmit/' + number)
        .done((data) => {
            if (data.status === 'pause') {
                showCountdown(data, startAutomaticGeneration);
            } else if (data.status === 'completed') {
                showGameFinalized();
            } else if (data.status === 'success') {
                handleNewNumber(data.number, data.totalNumbersGenerated);
            }
        })
        .fail(() => {
            console.warn('Failed to generate number:', number);
        });
}

function startAutomaticGeneration() {
    intervalManager.clear('generation');
    intervalManager.set('generation', generateAutoNumber, timeBallGet);
}

function stopAutomaticGeneration() {
    intervalManager.clear('generation');
}

function lastNumberGet() {
    $.get(site_url + 'boards/numberGet')
        .done((data) => {
            if (data.status === 'pause') {
                intervalManager.clear('lastNumber');
                showCountdown(data, startAutomaticLast);
            } else if (data.status === 'success') {
                showCountdown(data, startAutomaticLast);
            } else if (data.status === 'completed') {
                intervalManager.clear('lastNumber');
                stopAutomaticLast();
                setTimeout(showGameFinalized, timeBallGet);
            }
        })
        .fail(() => {
            console.warn('Failed to get last number');
        });
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

    stopAutomaticGeneration();
    stopAutomaticLast();
    stopUpdateUserCount();
    messagePoller.stop();

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
            if (data.userCount && data.userCount > 0) {
                countEl.text(data.userCount).show();
            } else {
                countEl.hide();
            }
        })
        .fail(() => {
            console.warn('Failed to update user count');
        });
}, 1000);

function stopUpdateUserCount() {
    intervalManager.clear('userCount');
}

// Funciones de audio
function RemoveVolume() {
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
            }
        },
        error: function() {
            console.warn("Error disabling narrator");
        }
    });
}

// ==========================================
// CONFIGURACIÓN DE EVENTOS
// ==========================================
function setupEvents() {
    // Eventos de mensajes
    $('#message-button').on('click', () => {
        const messageInput = $('#message-send-new');
        sendMessage(messageInput.val());
    });
    
    $('#message-send-new').on('keypress', (e) => {
        if (e.which === 13) {
            sendMessage($(e.target).val());
        }
    });

    // Eventos de control del juego
    $('#start-button').on('click', () => {
        $('#start-button').hide();
        $('#stop-button, #next-number-button').show();
        sendMessage((__['game started!'] || 'JUEGO INICIADO!') + ' 😎', 26);
        setTimeout(() => {
            generateAutoNumber();
            startAutomaticGeneration();
        }, 2000);
    });

    $('#next-number-button').on('click', () => {
        intervalManager.clear('generation');
        generateAutoNumber();
        startAutomaticGeneration();
    });

    $('#stop-button').on('click', () => {
        stopAutomaticGeneration();
        $('#stop-button, #next-number-button').hide();
        $('#play-button').show();
    });

    $('#play-button').on('click', () => {
        startAutomaticGeneration();
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

    // Toggle de mensajes
    const toggleBtn = $id("toggle-messages-btn");
    if (toggleBtn) {
        toggleBtn.addEventListener("click", function(event) {
            const messageContainer = $id("message-display-container");
            if (messageContainer) {
                messageContainer.style.display = messageContainer.style.display === "flex" ? "none" : "flex";
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
        }
    });
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
            text = `EL JUEGO INICIA EN: ${minutes}:${seconds < 10 ? '0' : ''}${seconds} MINUTO${minutes > 1 ? 'S' : ''}`;
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

        // Detener confetti
        confettiManager.stop();
        
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

// ==========================================
// FUNCIONES DE UTILIDAD ADICIONALES
// ==========================================

// Función para manejar errores de red de forma elegante
function handleNetworkError(error, context = '') {
    console.warn(`Network error in ${context}:`, error);
    
    // Mostrar notificación discreta al usuario
    const notification = document.createElement('div');
    notification.className = 'network-error-notification';
    notification.textContent = 'Conexión inestable. Reintentando...';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #ff6b6b;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        z-index: 10000;
        font-size: 14px;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => notification.style.opacity = '1', 100);
    
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

// Función para detectar si el dispositivo tiene recursos limitados
function isLowEndDevice() {
    // Detectar dispositivos con recursos limitados
    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    const isSlowConnection = connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g');
    const isLowMemory = navigator.deviceMemory && navigator.deviceMemory < 4;
    const isOldDevice = navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;
    
    return isSlowConnection || isLowMemory || isOldDevice;
}

// Ajustar configuración según el dispositivo
function adjustConfigForDevice() {
    if (isLowEndDevice()) {
        console.log('Low-end device detected, adjusting configuration...');
        
        // Reducir frecuencia de polling
        CONFIG.BASE_POLL_INTERVAL = 3000;
        CONFIG.USER_COUNT_INTERVAL = 5000;
        
        // Reducir efectos visuales
        CONFIG.MAX_CONFETTI = 15;
        CONFIG.MESSAGE_LIFETIME = 2000;
        
        // Reducir tamaños de pool
        CONFIG.MESSAGE_POOL_SIZE = 8;
        CONFIG.AUDIO_POOL_SIZE = 5;
    }
}

// ==========================================
// INICIALIZACIÓN PRINCIPAL
// ==========================================
const resourceManager = new ResourceManager();

// Función de inicialización principal
function initializeApp() {
    console.log('Initializing Bingo App...');
    
    // Ajustar configuración según el dispositivo
    adjustConfigForDevice();
    
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
    
    // Iniciar contador de usuarios
    intervalManager.set('userCount', updateUserCount, CONFIG.USER_COUNT_INTERVAL);
    updateUserCount();
    
    // Iniciar último número si es necesario
    if (typeof timeBallLast !== 'undefined') {
        startAutomaticLast();
    }
    
    console.log('Bingo App initialized successfully');
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
    generateNumber,
    startAutomaticGeneration,
    stopAutomaticGeneration,
    showGameFinalized,
    RemoveVolume,
    RemoveMicrophone,
    
    // Gestores
    intervalManager,
    audioManager,
    resourceManager,
    confettiManager,
    
    // Utilidades
    handleNetworkError,
    isLowEndDevice,
    
    // Estado
    get winners() { return winners; },
    get numbersGenerated() { return numbersgenerated; },
    get isGameFinished() { return isGameFinishedShown; }
};

// ==========================================
// FUNCIONES DE DEBUGGING (solo en desarrollo)
// ==========================================
if (typeof DEBUG !== 'undefined' && DEBUG) {
    window.BingoDebug = {
        // Información de estado
        getState() {
            return {
                numbersGenerated: numbersgenerated.length,
                messagesDisplayed: messagesDisplayed.length,
                winners: winners.length,
                intervals: intervalManager.intervals.size,
                isPollingActive: messagePoller.isActive,
                audioCache: audioManager.audioCache.size,
                domCache: domCache.cache.size,
                messagePool: messagePool.pool.length
            };
        },
        
        // Forzar limpieza de recursos
        forceCleanup() {
            resourceManager.cleanup();
        },
        
        // Simular error de red
        simulateNetworkError() {
            handleNetworkError(new Error('Simulated network error'), 'debug');
        },
        
        // Información de rendimiento
        getPerformanceInfo() {
            return {
                memory: performance.memory ? {
                    used: Math.round(performance.memory.usedJSHeapSize / 1048576) + ' MB',
                    total: Math.round(performance.memory.totalJSHeapSize / 1048576) + ' MB',
                    limit: Math.round(performance.memory.jsHeapSizeLimit / 1048576) + ' MB'
                } : 'Not available',
                timing: performance.timing,
                navigation: performance.navigation
            };
        }
    };
    
    console.log('Bingo Debug tools available in window.BingoDebug');
}

console.log('Bingo App script loaded successfully');