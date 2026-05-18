<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Confeti con Ganaste</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            position: relative;
        }

        #ganaste-message {
            position: absolute;
            font-size: 80px;
            font-weight: bold;
            color: #FF6347; /* Color del texto */
            text-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: scale(0.5);
            animation: showGanaste 2s forwards 1.5s;
            z-index: 5;
        }

        /* Estilo y animación del confeti */
        .confetti {
            position: absolute;
            font-size: 30px;
            opacity: 0.9;
            pointer-events: none;
            animation: explodeFromSide 3s forwards, rotate 1s infinite linear;
        }

        @keyframes explodeFromSide {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--x), var(--y)) scale(1.5);
                opacity: 0;
            }
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Animación del texto "Ganaste" */
        @keyframes showGanaste {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        #explosion-button {
            padding: 15px 30px;
            font-size: 20px;
            border: none;
            background-color: #5F40FF;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            z-index: 10;
        }

        #explosion-button:hover {
            background-color: #472fbf;
        }
    </style>
</head>
<body>

    <button id="explosion-button">¡Iniciar Celebración!</button>
    <div id="ganaste-message">¡Ganaste!</div>

    <script>
        document.getElementById('explosion-button').addEventListener('click', function() {
            createConfetti();
            showGanaste();
        });

        function createConfetti() {
            const emojis = ['🎉', '🎊', '✨', '💥', '🎈', '💫', '🌟']; // Emojis
            const confettiCount = 150;

            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                const emoji = emojis[Math.floor(Math.random() * emojis.length)];
                confetti.textContent = emoji;

                // Asignar posición desde los lados o desde arriba
                const fromSide = Math.random() < 0.5; // 50% de probabilidad de salir de los lados o arriba
                if (fromSide) {
                    confetti.style.top = Math.random() * 100 + 'vh';
                    confetti.style.left = Math.random() < 0.5 ? '-5vw' : '105vw'; // Sale de izquierda o derecha
                    confetti.style.fontSize = Math.random() * 20 + 20 + 'px';
                    confetti.style.animationDuration = (Math.random() * 1.5 + 1.5) + 's';
                    confetti.style.setProperty('--x', `${Math.random() * 150 - 75}vw`); // Movimiento horizontal
                    confetti.style.setProperty('--y', `${Math.random() * 150 - 75}vh`); // Movimiento vertical
                } else {
                    confetti.style.top = '-10vh'; // Sale desde arriba
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.fontSize = Math.random() * 20 + 20 + 'px';
                    confetti.style.animationDuration = (Math.random() * 1.5 + 1.5) + 's';
                    confetti.style.setProperty('--x', `${Math.random() * 150 - 75}vw`); // Movimiento horizontal
                    confetti.style.setProperty('--y', `${Math.random() * 150 + 100}vh`); // Movimiento hacia abajo
                }

                document.body.appendChild(confetti);

                // Eliminar confeti después de la animación
                confetti.addEventListener('animationend', function() {
                    confetti.remove();
                });
            }
        }

        function showGanaste() {
            const message = document.getElementById('ganaste-message');
            message.style.display = 'block';
        }
    </script>

</body>
</html>
