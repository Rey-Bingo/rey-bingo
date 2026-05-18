
<style>
    .bingo-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        width: 100%;
        max-width: 1200px;
    }

    .bingo-carton {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 5px;
        background-color: #fff;
        border-radius: 15px;
        padding: 5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 100%;
    }

    .bingo-border-carton {
        padding: 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 20px;
    }

    .bingo-carton-number {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffe6f2;
        font-size: 1.2vw;
        font-weight: bold;
        color: #303030;
        border-radius: 5px;
        transition: transform 0.3s ease;
        width: 40px;
        height: 40px;
    }

    .bingo-carton-header {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ff4081;
        font-size: 1.5rem;
        font-weight: bold;
        color: #ffffff;
        border-radius: 5px;
        transition: transform 0.3s ease;
        width: 40px;
        height: 40px;
    }

    .single-carton {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* Asegura que ocupe toda la pantalla */
    }

    /* 📱 Ajustes para móviles */
    @media (max-width: 768px) {
        .container-cartons {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 5px; /* Reducir espacio */
        }

        .bingo-carton {
            max-width: 180px; /* Ajustar para móviles */
            padding: 5px;
        }

        .bingo-carton-number {
            font-size: 3.5vw;  /* Ajustar tamaño de texto */
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }

        .bingo-carton-header {
            font-size: 3.5vw;
            width: 7vw;  /* Ajustar tamaño de los números */
            height: 7vw;
        }
        
        .single-carton {
            height: 80vh;
        }
    }
</style>

<div class="container-cartons" id="bingoContainer"></div>

<script>
    function generateBingoCard() {
        const letters = ['B', 'I', 'N', 'G', 'O'];
        
        // Contenedor externo para el borde
        let borderContainer = document.createElement('div');
        borderContainer.classList.add('bingo-border-carton');

        let card = document.createElement('div');
        card.classList.add('bingo-carton');

        // Encabezado con BINGO
        for (let i = 0; i < 5; i++) {
            let cell = document.createElement('div');
            cell.classList.add('bingo-carton-header');
            cell.textContent = letters[i];
            card.appendChild(cell);
        }

        // Números aleatorios
        for (let col = 0; col < 5; col++) {
            let usedNumbers = new Set();
            for (let row = 0; row < 5; row++) {
                let cell = document.createElement('div');
                cell.classList.add('bingo-carton-number');

                let min = col * 15 + 1;
                let max = (col + 1) * 15;
                let number;
                do {
                    number = Math.floor(Math.random() * (max - min + 1)) + min;
                } while (usedNumbers.has(number));
                usedNumbers.add(number);

                // Espacio libre en el centro
                if (col === 2 && row === 2) {
                    cell.textContent = '★';
                    cell.style.background = '#ffcc00';
                } else {
                    cell.textContent = number;
                }

                card.appendChild(cell);
            }
        }

        // Agregar el cartón dentro del borde
        borderContainer.appendChild(card);
        return borderContainer;
    }

    function generateBingoCards(count) {
        const container = document.getElementById('bingoContainer');
        container.innerHTML = ''; // Limpiar contenedor
        for (let i = 0; i < count; i++) {
            container.appendChild(generateBingoCard());
        }

        // Si solo hay un cartón, aplicar clase para centrarlo
        if (count === 1) {
            container.classList.add('single-carton');
        } else {
            container.classList.remove('single-carton');
        }
    }

    // Generar 10 cartones
    generateBingoCards(1);
</script>