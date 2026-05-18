<style>
  body { font-family: system-ui, sans-serif; margin: 20px; background: #f5f5f5; }
  .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto; }
  
  .card { 
    border: none; 
    border-radius: 12px; 
    padding: 12px; 
    background: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    position: relative;
  }
  
  .card.has-bingo {
    border: none;
    box-shadow: 0 0 20px rgba(255, 87, 34, 0.5);
    animation: pulse 2s infinite;
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
  }
  
  .card-header {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    letter-spacing: 8px;
  }
  
  .bingo-grid { 
    display: grid; 
    grid-template-columns: repeat(5, 1fr); 
    gap: 2px; 
    border: 2px solid #333;
    background: #333;
  }
  
  .column-header {
    background: #4CAF50;
    color: white;
    font-weight: bold;
    font-size: 18px;
    text-align: center;
    padding: 8px;
    border: none;
  }
  
  .cell { 
    height: 50px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    background: white;
    border: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
  }
  
  .cell.marked { 
    background: #FF5722; 
    color: white;
  }
  
  .cell.free {
    background: #2196F3;
    color: white;
    font-size: 12px;
    cursor: default;
  }
  
  .cell.winning-line {
    background: #FFD700 !important;
    color: #333 !important;
    animation: flash 1s ease-in-out 3;
  }
  
  @keyframes flash {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
  }
  
  .toolbar { 
    margin: 16px 0; 
    display: flex; 
    gap: 12px; 
    align-items: center;
    justify-content: center;
  }
  
  button { 
    padding: 12px 24px; 
    border-radius: 8px; 
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
  }
  
  #btnBingo {
    background: #FF5722;
    color: white;
    position: relative;
  }
  
  #btnBingo:hover {
    background: #E64A19;
    transform: translateY(-2px);
  }
  
  #btnBingo.has-bingo {
    background: #FFD700;
    color: #333;
    animation: bounce 1s infinite;
  }
  
  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
  }
  
  .status { 
    margin-left: auto; 
    font-weight: 600;
    font-size: 18px;
    color: #333;
  }
  
  .log { 
    font-family: ui-monospace, Menlo, Consolas, monospace; 
    font-size: 12px; 
    white-space: pre-wrap; 
    margin-top: 12px; 
    background: #000000; 
    padding: 8px; 
    border: none; 
    border-radius: 6px; 
    max-height: 220px; 
    overflow: auto;
  }

  .card-title {
    text-align: center;
    font-size: 14px;
    color: #666;
    margin-bottom: 8px;
  }
  
  .bingo-indicator {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #FFD700;
    color: #333;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    display: none;
  }
  
  .card.has-bingo .bingo-indicator {
    display: block;
  }
</style>

<!-- Constantes generadas por CI4 -->
<script>
  const GAME_ID    = <?= json_encode(session()->get('game_id')) ?>;
  const API_JOIN   = <?= json_encode(site_url("api/games/" . session()->get('game_id') . "/join")) ?>;
  const API_CLAIM  = <?= json_encode(site_url("api/games/" . session()->get('game_id') . "/claim-bingo")) ?>;
  const API_STATE  = <?= json_encode(site_url("api/games/" . session()->get('game_id') . "/state")) ?>;
  const AUTH_URL   = <?= json_encode(site_url('pusher/auth')) ?>;
  const PUSHER_KEY = <?= json_encode(env('PUSHER_KEY')) ?>;
  const PUSHER_CLUSTER = <?= json_encode(env('PUSHER_CLUSTER')) ?>;
</script>

<h1 style="text-align: center; color: #333;">🎯 BINGO - Juego <?= esc(session()->get('game_id')) ?></h1>

<div class="toolbar">
  <button id="btnBingo" type="button">🎉 ¡BINGO!</button>
  <span class="status" id="status">Esperando números...</span>
</div>

<div class="cards" id="cards"></div>
<div class="log" id="log"></div>

<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script>
  // ---- log util ----
  const logEl = document.getElementById('log');
  function log(x){ 
    console.log(x); 
    try{ 
      const timestamp = new Date().toLocaleTimeString();
      const message = typeof x === 'string' ? x : JSON.stringify(x);
      logEl.textContent += `[${timestamp}] ${message}\n`;
      logEl.scrollTop = logEl.scrollHeight;
    } catch{} 
  }

  // ---- ids ----
  const playerId = <?= esc(session()->get('id')) ?>;

  // ---- Genera cartón de BINGO tradicional 5x5 ----
  function makeBingoCard() {
    const card = {
      id: (crypto && crypto.randomUUID) ? crypto.randomUUID() : (Date.now().toString(36)+Math.random().toString(36).slice(2)),
      numbers: []
    };

    // Rangos por columna: B(1-15), I(16-30), N(31-45), G(46-60), O(61-75)
    const ranges = [
      [1, 15],   // B
      [16, 30],  // I  
      [31, 45],  // N
      [46, 60],  // G
      [61, 75]   // O
    ];

    // Generar números para cada columna (5 números por columna)
    const columnNumbers = [];
    for (let col = 0; col < 5; col++) {
      const [min, max] = ranges[col];
      const availableNumbers = [];
      
      // Crear array de números disponibles para esta columna
      for (let i = min; i <= max; i++) {
        availableNumbers.push(i);
      }
      
      // Mezclar números
      for (let i = availableNumbers.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [availableNumbers[i], availableNumbers[j]] = [availableNumbers[j], availableNumbers[i]];
      }
      
      // Tomar 5 números y ordenarlos
      const selectedNumbers = availableNumbers.slice(0, 5).sort((a, b) => a - b);
      columnNumbers[col] = selectedNumbers;
    }

    // Construir el cartón fila por fila, columna por columna
    for (let row = 0; row < 5; row++) {
      for (let col = 0; col < 5; col++) {
        if (row === 2 && col === 2) {
          // Centro siempre es FREE
          card.numbers.push('FREE');
        } else {
          card.numbers.push(columnNumbers[col][row]);
        }
      }
    }

    return card;
  }

  // Generar 3 cartones
  const myCards = [0, 1, 2].map(() => makeBingoCard());

  // Exponer para pruebas
  window.PLAYER_ID = playerId;
  window.CARDS = myCards;

  // Función para validar cartón (para debugging)
  function validateCard(card) {
    const ranges = [[1,15], [16,30], [31,45], [46,60], [61,75]];
    const errors = [];
    
    for (let row = 0; row < 5; row++) {
      for (let col = 0; col < 5; col++) {
        const index = row * 5 + col;
        const number = card.numbers[index];
        
        if (row === 2 && col === 2) {
          if (number !== 'FREE') {
            errors.push(`Centro debe ser FREE, encontrado: ${number}`);
          }
        } else {
          const [min, max] = ranges[col];
          if (number < min || number > max) {
            errors.push(`Posición [${row},${col}] (columna ${['B','I','N','G','O'][col]}): ${number} fuera del rango ${min}-${max}`);
          }
        }
      }
    }
    
    return errors;
  }

  // Validar cartones generados
  myCards.forEach((card, index) => {
    const errors = validateCard(card);
    if (errors.length > 0) {
      console.error(`Errores en cartón #${index + 1}:`, errors);
    } else {
      console.log(`✅ Cartón #${index + 1} válido`);
    }
  });

  // ---- render ----
  const cardsEl = document.getElementById('cards');
  const cellRefs = new Map(); // `${cardId}-${n}` => DOM
  const cardElements = new Map(); // cardId => DOM element

  function renderCards() {
    cardsEl.innerHTML = '';
    
    myCards.forEach((card, cardIndex) => {
      const cardWrapper = document.createElement('div');
      cardWrapper.className = 'card';
      cardWrapper.dataset.cardId = card.id;
      cardElements.set(card.id, cardWrapper);
      
      // Indicador de BINGO
      const bingoIndicator = document.createElement('div');
      bingoIndicator.className = 'bingo-indicator';
      bingoIndicator.textContent = '¡BINGO!';
      cardWrapper.appendChild(bingoIndicator);
      
      // Título del cartón
      const cardTitle = document.createElement('div');
      cardTitle.className = 'card-title';
      cardTitle.textContent = `Cartón #${cardIndex + 1}`;
      cardWrapper.appendChild(cardTitle);
      
      // Header BINGO
      const header = document.createElement('div');
      header.className = 'card-header';
      header.textContent = 'BINGO';
      cardWrapper.appendChild(header);
      
      // Grid del cartón
      const grid = document.createElement('div');
      grid.className = 'bingo-grid';
      
      // Headers de columnas
      const columnHeaders = ['B', 'I', 'N', 'G', 'O'];
      columnHeaders.forEach(letter => {
        const headerCell = document.createElement('div');
        headerCell.className = 'column-header';
        headerCell.textContent = letter;
        grid.appendChild(headerCell);
      });
      
      // Celdas de números (5x5 = 25 celdas)
      for (let i = 0; i < 25; i++) {
        const cell = document.createElement('div');
        cell.className = 'cell';
        cell.dataset.position = i; // Posición en el grid (0-24)
        
        const number = card.numbers[i];
        cell.textContent = number;
        cell.dataset.cardId = card.id;
        
        if (number === 'FREE') {
          cell.classList.add('free', 'marked'); // FREE siempre está marcado
          cell.dataset.n = 'FREE';
        } else {
          cell.dataset.n = number;
          cellRefs.set(`${card.id}-${number}`, cell);
        }
        
        grid.appendChild(cell);
      }
      
      cardWrapper.appendChild(grid);
      cardsEl.appendChild(cardWrapper);
    });
  }

  function markOnCards(n) {
    myCards.forEach(card => {
      // Buscar si el número está en este cartón (excluyendo FREE)
      const numberIndex = card.numbers.findIndex(num => num === n);
      if (numberIndex !== -1) {
        const cell = cellRefs.get(`${card.id}-${n}`);
        if (cell) {
          cell.classList.add('marked');
          log(`Marcado ${n} en cartón ${card.id}`);
        }
      }
    });
    
    // Verificar BINGO después de marcar
    checkAllCardsBingo();
  }

  function clearCards() { 
    cellRefs.forEach(el => {
      el.classList.remove('marked', 'winning-line');
    });
    
    // Marcar FREE nuevamente
    myCards.forEach(card => {
      const freeCell = document.querySelector(`[data-card-id="${card.id}"][data-n="FREE"]`);
      if (freeCell) {
        freeCell.classList.add('marked');
      }
    });
    
    // Limpiar indicadores de BINGO
    cardElements.forEach(cardEl => {
      cardEl.classList.remove('has-bingo');
    });
    
    document.getElementById('btnBingo').classList.remove('has-bingo');
  }

  // Función para verificar si un cartón tiene BINGO
  function checkBingo(card) {
    const markedPositions = new Set();
    
    // Obtener posiciones marcadas de este cartón
    card.numbers.forEach((num, position) => {
      if (num === 'FREE') {
        markedPositions.add(position); // FREE siempre está marcado
      } else {
        const cell = cellRefs.get(`${card.id}-${num}`);
        if (cell && cell.classList.contains('marked')) {
          markedPositions.add(position);
        }
      }
    });

    // Patrones ganadores para un cartón 5x5 (posiciones 0-24)
    const winningPatterns = [
      // Filas horizontales
      [0, 1, 2, 3, 4],     // Fila 1
      [5, 6, 7, 8, 9],     // Fila 2
      [10, 11, 12, 13, 14], // Fila 3
      [15, 16, 17, 18, 19], // Fila 4
      [20, 21, 22, 23, 24], // Fila 5
      
      // Columnas verticales
      [0, 5, 10, 15, 20],   // Columna B
      [1, 6, 11, 16, 21],   // Columna I
      [2, 7, 12, 17, 22],   // Columna N
      [3, 8, 13, 18, 23],   // Columna G
      [4, 9, 14, 19, 24],   // Columna O
      
      // Diagonales
      [0, 6, 12, 18, 24],   // Diagonal \
      [4, 8, 12, 16, 20],   // Diagonal /
      
      // Cartón completo (todas las posiciones)
      [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]
    ];

    // Verificar cada patrón
    for (let pattern of winningPatterns) {
      if (pattern.every(pos => markedPositions.has(pos))) {
        return {
          hasBingo: true,
          winningPattern: pattern,
          type: pattern.length === 25 ? 'full-card' : 
                pattern.length === 5 && pattern[1] - pattern[0] === 1 ? 'row' :
                pattern.length === 5 && pattern[1] - pattern[0] === 5 ? 'column' : 'diagonal'
        };
      }
    }

    return { hasBingo: false };
  }

  function checkAllCardsBingo() {
    let hasBingo = false;
    
    myCards.forEach((card, index) => {
      const result = checkBingo(card);
      const cardEl = cardElements.get(card.id);
      
      if (result.hasBingo) {
        hasBingo = true;
        cardEl.classList.add('has-bingo');
        
        // Destacar línea ganadora
        if (result.winningPattern) {
          result.winningPattern.forEach(pos => {
            const cell = cardEl.querySelector(`[data-position="${pos}"]`);
            if (cell) {
              cell.classList.add('winning-line');
            }
          });
        }
        
        log(`¡BINGO en cartón #${index + 1}! Tipo: ${result.type}`);
      } else {
        cardEl.classList.remove('has-bingo');
        // Remover destacado de líneas
        cardEl.querySelectorAll('.winning-line').forEach(cell => {
          cell.classList.remove('winning-line');
        });
      }
    });
    
    const btnBingo = document.getElementById('btnBingo');
    const statusEl = document.getElementById('status');
    
    if (hasBingo) {
      btnBingo.classList.add('has-bingo');
      statusEl.textContent = '¡Tienes BINGO! ¡Canta BINGO!';
      statusEl.style.color = '#FF5722';
      statusEl.style.fontWeight = 'bold';
    } else {
      btnBingo.classList.remove('has-bingo');
      if (statusEl.textContent.includes('BINGO')) {
        statusEl.textContent = 'Esperando números...';
        statusEl.style.color = '#333';
      }
    }
  }

  async function post(url, data) {
    const res = await fetch(url, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)});
    const json = await res.json();
    log({POST: url, status: res.status, json});
    return json;
  }

  async function join() {
    // Convertir cartones al formato esperado por el backend
    const cardsForBackend = myCards.map(card => ({
      id: card.id,
      numbers: card.numbers.filter(n => n !== 'FREE') // Excluir FREE del backend
    }));
    
    const payload = { playerId, cards: cardsForBackend };
    log({about_to_join: payload});
    const j = await post(API_JOIN, payload);
    if (j?.ok) log('join ok'); else log('join no-ok');
  }

  // ---- botón Cantar Bingo ----
  document.getElementById('btnBingo').addEventListener('click', async() => {
    const statusEl = document.getElementById('status');
    
    // Verificar si algún cartón tiene BINGO
    let winningCard = null;
    let winningResult = null;
    
    for (let card of myCards) {
      const result = checkBingo(card);
      if (result.hasBingo) {
        winningCard = card;
        winningResult = result;
        break;
      }
    }
    
    if (!winningCard) {
      alert('¡No tienes BINGO aún! Necesitas completar una línea completa (fila, columna, diagonal) o el cartón completo.');
      return;
    }
    
    const claimPayload = { playerId, cardId: winningCard.id };
    log({about_to_claim: claimPayload, winningType: winningResult.type});
    
    try {
      const snap = await post(API_CLAIM, claimPayload);

      if (snap?.winner) {
        statusEl.textContent = '¡Bingo aceptado! ¡Felicidades!';
        statusEl.style.color = '#4CAF50';
        return;
      }

      if (snap?.check) {
        const { matches, required, valid } = snap.check;
        statusEl.textContent = valid
          ? '¡Bingo aceptado! ¡Felicidades!'
          : `Aún no: ${matches}/${required} aciertos.`;
        statusEl.style.color = valid ? '#4CAF50' : '#FF5722';
        return;
      }

      statusEl.textContent = 'Solicitud enviada (sin detalles).';
    } catch (e) {
      log({claim_error: String(e)});
      alert('Error al cantar bingo (ver consola)');
    }
  });

  renderCards();
  join();

  // ---- Pusher v8 (canal PRIVADO) ----
  console.log('AUTH_URL=', AUTH_URL, 'KEY=', PUSHER_KEY, 'CLUSTER=', PUSHER_CLUSTER);
  Pusher.logToConsole = true;
  const pusher = new Pusher(PUSHER_KEY, {
    cluster: PUSHER_CLUSTER,
    channelAuthorization: {
      endpoint: AUTH_URL,
      transport: 'ajax',
    },
  });
  const channelName = 'private-game-' + GAME_ID;
  const channel = pusher.subscribe(channelName);
  console.log('channelName', channelName)

  channel.bind('pusher:subscription_succeeded', () => {
    console.log('SUSCRITO', channelName);
    log('✅ Conectado a Pusher');
  });
  
  channel.bind('pusher:subscription_error', (s) => {
    console.error('SUB ERROR', s);
    log('❌ Error de conexión Pusher: ' + JSON.stringify(s));
  });

  channel.bind('game:number_drawn', ({n}) => { 
    log(`📡 Número sorteado: ${n}`); 
    markOnCards(n);
  });
  
  channel.bind('game:game_reset', () => { 
    log('📡 Juego reiniciado'); 
    clearCards(); 
    document.getElementById('status').textContent = 'Juego reiniciado - Esperando números...';
    document.getElementById('status').style.color = '#333';
  });
  
  channel.bind('game:bingo_accepted', (p) => {
    log(`📡 ¡BINGO GANADOR! ${p.playerId} (Cartón ${p.cardId})`);
    const statusEl = document.getElementById('status');
    if (p.playerId === playerId) {
      statusEl.textContent = '¡FELICIDADES! ¡Ganaste el BINGO!';
      statusEl.style.color = '#4CAF50';
    } else {
      statusEl.textContent = `Juego terminado. Ganador: ${p.playerId}`;
      statusEl.style.color = '#666';
    }
  });

  // ---- Sync inicial + polling de respaldo ----
  (async () => {
    try {
      log('🔄 Sincronizando estado inicial...');
      const r = await fetch(API_STATE); 
      const snap = await r.json();
      
      if (snap.drawn && snap.drawn.length > 0) {
        log(`Marcando ${snap.drawn.length} números ya sorteados`);
        snap.drawn.forEach(markOnCards);
      }
      
      if (snap.stopped) {
        document.getElementById('status').textContent = 'Juego detenido - Hubo ganador';
        document.getElementById('status').style.color = '#666';
      }
      
      log('✅ Sincronización completada');

      // Polling de respaldo (cada 2 segundos)
      let lastLen = (snap.drawn || []).length;
      setInterval(async () => {
        try {
          const r = await fetch(API_STATE); 
          const s = await r.json();
          const arr = s.drawn || [];
          if (arr.length !== lastLen) { 
            log(`📊 Polling: ${arr.length - lastLen} números nuevos`);
            arr.slice(lastLen).forEach(markOnCards); 
            lastLen = arr.length; 
          }
        } catch(e) {
          // Silencioso para no spam en consola
        }
      }, 2000);
      
    } catch (error) {
      log('❌ Error en sincronización inicial: ' + error.message);
    }
  })();
</script>