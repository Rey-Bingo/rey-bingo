<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efecto Explosión de Confeti con Emojis</title>
    <style>
        body {
            margin: 0;
            overflow: hidden; /* Evitar desplazamiento */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0; /* Fondo claro */
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
            position: absolute;
            top: -50px; /* Comienza fuera de la vista */
            font-size: 20px; /* Tamaño del emoji */
            opacity: 0.8;
            pointer-events: none;
            animation: explode 3s forwards; /* Aumenté la duración para mayor efecto */
        }

        @keyframes explode {
            0% {
                transform: translateY(-200vh) rotate(0); /* Comienza desde arriba */
            }
            100% {
                transform: translateY(100vh) rotate(720deg); /* Cae hacia abajo */
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
                const emojis = ['🎉', '✨', '🎊', '🌟', '🎈']; // Emojis de confeti
                const confettiCount = 150; // Cantidad de confeti

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = $('<div class="confetti"></div>');
                    // Asignar emoji aleatorio
                    confetti.text(emojis[Math.floor(Math.random() * emojis.length)]);
                    // Asignar posición horizontal aleatoria
                    confetti.css({
                        left: Math.random() * 100 + 'vw',
                        animationDuration: (Math.random() * 2 + 1) + 's',
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
