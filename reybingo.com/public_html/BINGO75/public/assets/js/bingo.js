// Obtener los elementos del DOM  
const cardCountInput = document.getElementById('cardCount');
const generateCardsButton = document.getElementById('generateCardsButton');
const cardsContainer = document.getElementById('cardsContainer');
const drawNumberButton = document.getElementById('drawNumberButton');
const playButton = document.getElementById('playButton');
const pauseButton = document.getElementById('pauseButton');
const autoMarkButton = document.getElementById('autoMarkButton');
const drawnNumberDisplay = document.getElementById('drawnNumber');

// Rango de números por columna
const columnRanges = {
    B: { start: 1, end: 15 },
    I: { start: 16, end: 30 },
    N: { start: 31, end: 45 },
    G: { start: 46, end: 60 },
    O: { start: 61, end: 75 }
};

// Almacenar los cartones generados y sus números
let boards = [];
let intervalId;
let drawnNumbers = [];

// Función para barajar (shuffle) un array de números
function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

// Función para generar un cartón de bingo
function createBoard(containerId) {
    const stage = new createjs.Stage(containerId);
    const boardContainer = new createjs.Container();
    const columns = ['B', 'I', 'N', 'G', 'O'];
    let boardNumbers = [];

    columns.forEach((column, colIndex) => {
        const letterText = new createjs.Text(column, "24px Arial", "#000");
        letterText.x = colIndex * 60 + 10;
        letterText.y = 10;
        boardContainer.addChild(letterText);

        let numbersInColumn = [];
        for (let i = columnRanges[column].start; i <= columnRanges[column].end; i++) {
            numbersInColumn.push(i);
        }
        numbersInColumn = shuffle(numbersInColumn);

        for (let rowIndex = 0; rowIndex < 5; rowIndex++) {
            let number;
            if (colIndex === 2 && rowIndex === 2) {
                // Casilla central "FREE"
                number = null;
                let freeCell = new createjs.Shape();
                freeCell.graphics.beginStroke("#000").beginFill("lightgray").drawRect(0, 0, 50, 50);
                freeCell.x = colIndex * 60 + 10;
                freeCell.y = rowIndex * 60 + 50;

                let freeText = new createjs.Text("FREE", "16px Arial", "#000");
                freeText.x = freeCell.x + 10;
                freeText.y = freeCell.y + 15;

                boardContainer.addChild(freeCell, freeText);

                // No almacenar referencias para la casilla FREE
            } else {
                number = numbersInColumn[rowIndex];
                let cell = new createjs.Shape();
                cell.graphics.beginStroke("#000").beginFill("#FFF").drawRect(0, 0, 50, 50);
                cell.x = colIndex * 60 + 10;
                cell.y = rowIndex * 60 + 50;

                let text = new createjs.Text(number, "20px Arial", "#000");
                text.x = cell.x + 20;
                text.y = cell.y + 15;

                boardContainer.addChild(cell, text);

                // Almacenar referencias a la celda y al texto
                boardNumbers.push({ 
                    number, 
                    cellShape: cell, // Referencia a la Shape de la celda
                    cellText: text    // Referencia al Text del número
                });
            }
        }
    });

    stage.addChild(boardContainer);
    stage.update();

    return { boardNumbers, stage };
}

// Evento para generar múltiples cartones
generateCardsButton.addEventListener('click', () => {
    const cardCount = parseInt(cardCountInput.value);

    // Limpiar los cartones anteriores
    cardsContainer.innerHTML = '';
    boards = []; // Resetear la lista de cartones generados

    for (let i = 0; i < cardCount; i++) {
        const canvasId = `bingoCanvas-${i}`;
        const canvasContainer = document.createElement('div');
        canvasContainer.style.margin = '20px';
        canvasContainer.style.display = 'inline-block';

        const canvas = document.createElement('canvas');
        canvas.id = canvasId;
        canvas.width = 360;
        canvas.height = 360;

        canvasContainer.appendChild(canvas);
        cardsContainer.appendChild(canvasContainer);

        // Generar el cartón de bingo y agregarlo a la lista de cartones
        const board = createBoard(canvasId);
        boards.push(board);
    }
});

// Función para generar el tablero de números del 1 al 75
function createNumberBoard() {
    const numberBoard = document.getElementById('numberBoard');

    for (let i = 1; i <= 75; i++) {
        const numberDiv = document.createElement('div');
        numberDiv.textContent = i;
        numberDiv.id = `number-${i}`;
        numberDiv.style.width = '30px';
        numberDiv.style.height = '30px';
        numberDiv.style.display = 'flex';
        numberDiv.style.justifyContent = 'center';
        numberDiv.style.alignItems = 'center';
        numberDiv.style.border = '1px solid #000';
        numberDiv.style.margin = '2px';
        numberDiv.style.backgroundColor = '#FFF';
        numberDiv.style.color = '#000';
        numberBoard.appendChild(numberDiv);
    }
}

// Llamar a la función para generar el tablero al cargar la página
createNumberBoard();

// Evento para sortear un número
async function drawNumber() {
    try {
        let drawnNumber;
        // Intentar obtener un número único
        do {
            const response = await fetch(`/bingo/generate-number`);
            const data = await response.json();

            if (data.error) {
                console.error(data.error);
                return;
            }

            drawnNumber = data.number;

            // Verifica si el número ya ha salido
        } while (drawnNumbers.includes(drawnNumber));

        drawnNumberDisplay.textContent = `Número Sorteado: ${drawnNumber}`;

        // Añadir el número a la lista de números sorteados
        drawnNumbers.push(drawnNumber);

        // Marcar el número en cada cartón generado
        boards.forEach(board => {
            markNumberInBoard(board, drawnNumber);
        });

        // Marcar el número en el tablero de números
        const numberDiv = document.getElementById(`number-${drawnNumber}`);
        if (numberDiv) {
            numberDiv.style.backgroundColor = 'green';
            numberDiv.style.color = 'white';
        }
    } catch (error) {
        console.error('Error fetching number:', error);
    }
}


// Función para marcar un número en un cartón específico
function markNumberInBoard(board, drawnNumber) {
    board.boardNumbers.forEach(cell => {
        if (cell.number === drawnNumber) {
            // Marcar la celda en verde si coincide el número
            cell.cellShape.graphics.clear().beginStroke("#000").beginFill("green").drawRect(0, 0, 50, 50);
            
            // Opcional: Cambiar el color del texto a blanco para mayor visibilidad
            cell.cellText.color = "#FFF";

            board.stage.update();
        }
    });
}

// Evento para el botón "Siguiente Bola"
drawNumberButton.addEventListener('click', () => {
    drawNumber();
});

// Función para iniciar el sorteo automático
function startAutoDraw() {
    intervalId = setInterval(drawNumber, 500); // Sorteo automático cada 5 segundos
}

// Evento para el botón "Play"
playButton.addEventListener('click', () => {
    if (!intervalId) { // Solo iniciar si no está ya en ejecución
        startAutoDraw();
    }
});

// Función para detener el sorteo automático
function stopAutoDraw() {
    clearInterval(intervalId);
    intervalId = null;
}

// Evento para el botón "Pause"
pauseButton.addEventListener('click', () => {
    stopAutoDraw();
});

// Evento para el botón "Automarcar"
autoMarkButton.addEventListener('click', () => {
    boards.forEach(board => {
        drawnNumbers.forEach(number => {
            markNumberInBoard(board, number);
        });
    });
});
