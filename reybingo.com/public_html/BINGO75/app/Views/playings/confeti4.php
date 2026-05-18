<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Confeti con ¡Felicidades!</title>
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
            font-size: 20px;
            opacity: 0.9;
            pointer-events: none;
            animation: explode 1s forwards, rotate 1s infinite;
        }

        /* Animaciones del confeti */
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

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Estilo para el mensaje de "Felicidades" */
        #felicidades-message {
            position: absolute;
            font-size: 80px;
            font-weight: bold;
            color: #5F40FF;
            text-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: scale(0.5);
            animation: showFelicidades 2s forwards 1.5s;
            z-index: 5;
        }

        /* Animación para hacer que "Felicidades" aparezca */
        @keyframes showFelicidades {
            0% {
                opacity: 0;
                transform: scale(0.5);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <button id="explosion-button">¡Iniciar celebración!</button>
    <div id="felicidades-message">¡Felicidades!</div>

    <script>
        $(document).ready(function() {
            $('#explosion-button').click(function() {
                createConfetti();
                showFelicidades();
            });

            function createConfetti() {
                const emojis = ['🎉', '🎊', '✨', '💥', '🎈', '💫', '🌟']; // Emojis
                const confettiCount = 150;

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = $('<div class="confetti"></div>');
                    const emoji = emojis[Math.floor(Math.random() * emojis.length)];
                    confetti.text(emoji);

                    // Asignar una posición inicial aleatoria
                    confetti.css({
                        left: Math.random() * 100 + 'vw',
                        top: Math.random() * 100 + 'vh',
                        fontSize: Math.random() * 30 + 20 + 'px',
                        animationDuration: (Math.random() * 1.5 + 1) + 's',
                        '--x': `${Math.random() * 200 - 100}vw`,
                        '--y': `${Math.random() * 200 - 100}vh`,
                    });

                    // Añadir confeti al DOM
                    $('body').append(confetti);

                    // Eliminar el confeti después de la animación
                    confetti.on('animationend', function() {
                        $(this).remove();
                    });
                }
            }

            function showFelicidades() {
                $('#felicidades-message').css({
                    display: 'block'
                });
            }
        });
    </script>

</body>
</html>
