<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confeti con Emojis y Partículas</title>
    <style>
        body {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            overflow: hidden;
            position: relative;
        }

        button {
            padding: 15px 30px;
            font-size: 1.2rem;
            background-color: #ff4081;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e91e63;
        }

        .confetti {
            position: absolute;
            font-size: 24px;
            will-change: transform, opacity;
        }

        @keyframes fall {
            0% {
                opacity: 1;
                transform: translateY(0) rotate(0deg);
            }
            100% {
                opacity: 0;
                transform: translateY(100vh) rotate(360deg);
            }
        }

        @keyframes drift {
            0% {
                transform: translateX(0);
            }
            50% {
                transform: translateX(10px);
            }
            100% {
                transform: translateX(-10px);
            }
        }
    </style>
</head>
<body>

    <button id="confetti-btn">¡Generar Confeti!</button>

    <script>
        const confettiBtn = document.getElementById('confetti-btn');

        confettiBtn.addEventListener('click', () => {
            createConfetti();
        });

        function createConfetti() {
            const emojis = ['🎉', '🎊', '✨', '💥', '🎈'];
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.innerText = emojis[Math.floor(Math.random() * emojis.length)];
                confetti.style.left = `${Math.random() * 100}vw`;
                confetti.style.animation = `fall ${(Math.random() * 3) + 2}s ease-in, drift ${(Math.random() * 3) + 2}s ease-in-out infinite`;
                confetti.style.fontSize = `${Math.random() * 20 + 15}px`;
                document.body.appendChild(confetti);

                // Eliminar confeti después de la animación
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }
    </script>

</body>
</html>
