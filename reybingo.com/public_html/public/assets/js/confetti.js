class ConfettiManager {
    constructor() {
        this.emojis = ['🎉', '🎊', '✨', '🌟', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
        this.container = document.getElementById('confetti-container');
        this.activeConfetti = new Set();
        this.maxConfetti = this.isMobile() ? 30 : 50; // Limitar en móviles
        
        // Optimización: Pre-crear pool de elementos
        this.confettiPool = [];
        this.initializePool();
    }

    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    initializePool() {
        // Pre-crear elementos para reutilizar
        for (let i = 0; i < this.maxConfetti; i++) {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.display = 'none';
            this.container.appendChild(piece);
            this.confettiPool.push(piece);
        }
    }

    getConfettiPiece() {
        // Reutilizar elementos del pool
        return this.confettiPool.find(piece => piece.style.display === 'none') || null;
    }

    AppcreateConfetti(count = null) {
        // Limpiar confeti anterior si hay demasiado
        if (this.activeConfetti.size > this.maxConfetti * 0.7) {
            this.clearConfetti();
        }

        const confettiCount = count || (this.isMobile() ? 20 : 30);
        const screenWidth = window.innerWidth;
        const screenHeight = window.innerHeight;

        for (let i = 0; i < confettiCount; i++) {
            // Crear confeti con delay escalonado para mejor rendimiento
            setTimeout(() => {
                this.createSingleConfetti(screenWidth, screenHeight);
            }, i * 50);
        }
    }

    createSingleConfetti(screenWidth, screenHeight) {
        const piece = this.getConfettiPiece();
        if (!piece) return;

        // Configurar el emoji y posición
        const emoji = this.emojis[Math.floor(Math.random() * this.emojis.length)];
        piece.textContent = emoji;
        piece.style.display = 'block';
        
        // Posición inicial aleatoria
        const startX = Math.random() * screenWidth;
        const startY = -50;
        
        piece.style.left = startX + 'px';
        piece.style.top = startY + 'px';

        // Animación principal de caída
        const fallDuration = 3000 + Math.random() * 2000; // 3-5 segundos
        const swingDuration = 1000 + Math.random() * 1000; // 1-2 segundos
        
        piece.style.animationDuration = `${fallDuration}ms, ${swingDuration}ms`;
        piece.className = 'confetti-piece confetti-fall confetti-swing';

        // Agregar rotación y escala aleatoria
        const rotation = Math.random() * 360;
        const scale = 0.8 + Math.random() * 0.4; // 0.8 - 1.2
        
        piece.style.transform = `rotate(${rotation}deg) scale(${scale})`;

        // Tracking del confeti activo
        this.activeConfetti.add(piece);

        // Limpiar después de la animación
        setTimeout(() => {
            this.removeConfettiPiece(piece);
        }, fallDuration);
    }

    removeConfettiPiece(piece) {
        piece.style.display = 'none';
        piece.className = 'confetti-piece';
        piece.style.animation = '';
        piece.style.transform = '';
        this.activeConfetti.delete(piece);
    }

    clearConfetti() {
        this.activeConfetti.forEach(piece => {
            this.removeConfettiPiece(piece);
        });
        this.activeConfetti.clear();
    }

    // Efecto especial: explosión de confeti
    AppcreateConfettiExplosion(x = null, y = null) {
        const centerX = x || window.innerWidth / 2;
        const centerY = y || window.innerHeight / 2;
        const explosionCount = this.isMobile() ? 15 : 25;

        for (let i = 0; i < explosionCount; i++) {
            setTimeout(() => {
                const piece = this.getConfettiPiece();
                if (!piece) return;

                const emoji = this.emojis[Math.floor(Math.random() * this.emojis.length)];
                piece.textContent = emoji;
                piece.style.display = 'block';

                // Posición desde el centro
                const angle = (i / explosionCount) * Math.PI * 2;
                const velocity = 100 + Math.random() * 100;
                const finalX = centerX + Math.cos(angle) * velocity;
                const finalY = centerY + Math.sin(angle) * velocity;

                piece.style.left = centerX + 'px';
                piece.style.top = centerY + 'px';

                // Animación de explosión
                piece.style.transition = 'all 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                
                requestAnimationFrame(() => {
                    piece.style.left = finalX + 'px';
                    piece.style.top = finalY + 'px';
                    piece.style.transform = `rotate(${Math.random() * 720}deg) scale(0)`;
                    piece.style.opacity = '0';
                });

                this.activeConfetti.add(piece);

                setTimeout(() => {
                    this.removeConfettiPiece(piece);
                    piece.style.transition = '';
                }, 1500);
            }, i * 30);
        }
    }

    // Lluvia continua de confeti
    startConfettiRain(duration = 5000) {
        const interval = setInterval(() => {
            if (this.activeConfetti.size < this.maxConfetti) {
                this.createSingleConfetti(window.innerWidth, window.innerHeight);
            }
        }, 200);

        setTimeout(() => {
            clearInterval(interval);
        }, duration);
    }
}

// Inicializar el manager
const appconfettiManager = new ConfettiManager();

// Función global para crear confeti
function AppcreateConfetti(count) {
    appconfettiManager.AppcreateConfetti(count);
}

// Funciones adicionales
function AppcreateConfettiExplosion(x, y) {
    appconfettiManager.AppcreateConfettiExplosion(x, y);
}

function startConfettiRain(duration) {
    appconfettiManager.startConfettiRain(duration);
}

function clearConfetti() {
    appconfettiManager.clearConfetti();
}

// Event listeners para interacciones táctiles
document.addEventListener('DOMContentLoaded', () => {
    // Doble tap para explosión
    let lastTap = 0;
    document.addEventListener('touchend', (e) => {
        const currentTime = new Date().getTime();
        const tapLength = currentTime - lastTap;
        
        if (tapLength < 500 && tapLength > 0) {
            const touch = e.changedTouches[0];
            AppcreateConfettiExplosion(touch.clientX, touch.clientY);
        }
        lastTap = currentTime;
    });
});

// Optimización: Pausar animaciones cuando la página no está visible
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        appconfettiManager.clearConfetti();
    }
});
