<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Explosión de Confeti con Emojis</title>
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
            top: 50%; /* Comienza desde el centro de la pantalla */
            left: 50%;
            font-size: 20px;
            opacity: 0.8;
            pointer-events: none;
            transform: translate(-50%, -50%);
            animation: explode 2s ease-out forwards;
        }

        @keyframes explode {
            0% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(0);
            }
            100% {
                opacity: 0;
                transform: translate(var(--x), var(--y)) rotate(720deg) scale(1);
            }
        }
    </style>
    <!-- Incluir jQuery desde un CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <button id="explosion-button">¡Felicidades!</button>

    <script>
        $(document).ready(function() {
            $('#explosion-button').click(createConfetti);

            function createConfetti() {
                const emojis = ['🎉', '🎊', '✨', '🌟', '💥', '🎈'];
                const confettiCount = 250;

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = $('<div class="confetti"></div>');
                    confetti.text(emojis[Math.floor(Math.random() * emojis.length)]);

                    const size = (Math.random() * 20 + 0.5) + 'rem'; // Tamaño aleatorio
                    const x = (Math.random() * 200 - 100) + 'vw'; // Movimiento horizontal aleatorio
                    const y = (Math.random() * 200 - 100) + 'vh'; // Movimiento vertical aleatorio

                    confetti.css({
                        '--x': x,
                        '--y': y,
                        'font-size': size,
                        'animation-duration': (Math.random() * 1 + 1.5) + 's',
                        'animation-delay': Math.random() * 0.5 + 's'
                    });

                    $('body').append(confetti);

                    confetti.on('animationend', function() {
                        $(this).remove();
                    });
                }
            }
        });
    </script>
</body>
</html>
