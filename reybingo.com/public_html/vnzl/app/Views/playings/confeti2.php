<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Explosivo de Confeti</title>
    <style>
        body {
            margin: 0;
            overflow: hidden; /* Evitar desplazamiento */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            position: relative;
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

        .confetti {
            position: absolute;
            font-size: 20px;
            opacity: 0.9;
            pointer-events: none;
            animation: explode 1s forwards, rotate 1s infinite;
        }

        /* Animaciones más explosivas */
        @keyframes explode {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(var(--x), var(--y)) scale(0.8);
                opacity: 0;
            }
        }

        /* Añade rotación aleatoria */
        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <button id="explosion-button">¡Explosión!</button>

    <script>
        $(document).ready(function() {
            $('#explosion-button').click(createConfetti);

            function createConfetti() {
                const emojis = ['🎉', '🎊', '✨', '🌟', '💥', '🔥', '💫', '🎈'];
                const confettiCount = 200; // Más confeti para mayor impacto

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = $('<div class="confetti"></div>');
                    const emoji = emojis[Math.floor(Math.random() * emojis.length)];
                    confetti.text(emoji);

                    // Asignar una posición inicial aleatoria
                    confetti.css({
                        left: Math.random() * 100 + 'vw',
                        top: Math.random() * 100 + 'vh',
                        fontSize: Math.random() * 30 + 15 + 'px', // Tamaño aleatorio
                        color: getRandomColor(), // Color aleatorio para algunos emojis
                        transform: `rotate(${Math.random() * 360}deg)`,
                        animationDuration: (Math.random() * 1.5 + 1) + 's',
                        '--x': `${Math.random() * 200 - 100}vw`, // Movimiento horizontal aleatorio
                        '--y': `${Math.random() * 200 - 100}vh`  // Movimiento vertical aleatorio
                    });

                    // Añadir confeti al DOM
                    $('body').append(confetti);

                    // Eliminar el confeti después de la animación
                    confetti.on('animationend', function() {
                        $(this).remove();
                    });
                }
            }

            // Función para generar colores aleatorios
            function getRandomColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    color += letters[Math.floor(Math.random() * 16)];
                }
                return color;
            }
        });
    </script>

</body>
</html>
