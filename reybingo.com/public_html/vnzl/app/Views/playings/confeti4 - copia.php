<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Explosión de Confeti con Emojis</title>
    <style>
        body {
            margin: 0;
            overflow: hidden; /* Evitar scroll */
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
            z-index: 10; /* Asegura que el botón esté por encima */
        }

        #explosion-button:hover {
            background-color: #472fbf;
        }

        .confetti {
            position: fixed; /* Asegura que el confeti esté fijo en la ventana */
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
            opacity: 0.8;
            animation: explode 3s forwards;
        }

        @keyframes explode {
            0% {
                transform: translateY(-100vh) rotate(0deg); /* Comienza fuera del viewport */
            }
            100% {
                transform: translateY(100vh) rotate(720deg); /* Cae a través del viewport */
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <button id="explosion-button">¡Felicidades!</button>

    <script>
        $(document).ready(function() {
            $('#explosion-button').click(createConfetti);

            function createConfetti() {
                const emojis = ['🎉', '🎊', '✨', '🌟', '🥳', '🍾', '💥', '🔥', '💫', '🍬', '🎈'];
                const confettiCount = 150; // Cantidad de confeti

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = $('<div class="confetti"></div>');
                    // Asignar emoji aleatorio
                    confetti.text(emojis[Math.floor(Math.random() * emojis.length)]);
                    // Asignar posición y tamaño aleatorio
                    confetti.css({
                        left: Math.random() * 150 + 'vw',
                        top: Math.random() * -100 + 'vh', // Comienza fuera del viewport hacia arriba
                        fontSize: (Math.random() * 30 + 10) + 'px', // Tamaño entre 10px y 30px
                        animationDuration: (Math.random() * 5 + 1) + 's',
                        animationDelay: Math.random() + 's'
                    });

                    // Añadir confeti al DOM
                    $('body').append(confetti);

                    // Eliminar el confeti después de que termine la animación
                    confetti.on('animationend', function() {
                        $(this).remove();
                    });
                }
            }
        });
    </script>
</body>
</html>
