<div class="modal-dialog modal-dialog-centered max-w-75">
    <div class="modal-content">
        <div class="modal-header pb-2">
            <h6 class="modal-title ps-2 text-uppercase"><i class="fa-duotone fa-solid fa-gamepad"></i> <?= $room['name']; ?></h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body p-1 pt-0 text-center">
            <div class="text-center p-2"><?= $game['description']; ?></div>
            <div class="action-sheet-content mb-3" style="max-height: 400px; overflow-y: auto;" id="cartons-container">
                <div class="cartons-section-select">
                    <div class="content-cartons-select" id="cartons-list">
                        <!-- Cartones se cargarán dinámicamente -->
                    </div>
                </div>
                
                <div id="loading-indicator" style="display: none; padding: 20px; text-center;">
                    <i class="fa fa-spinner fa-spin"></i> Cargando más cartones...
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-small btn-primary d-block w-50 btn-bingo" id="modal-play-button"><?= translate('play'); ?></button>
            </div>
            <div class="card text-center w-50 mx-auto mt-1">
                <?= translate('available my wallet'); ?> 
                <h6><?= systemGet('currency'); ?> <span class="modal-available-wallet"><?= $user['wallet']; ?></span></h6>
            </div>
        </div>
    </div>
</div>

<style>
.bingo-border-carton-select:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.bingo-carton {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 2px;
    max-width: 300px;
    margin: 0 auto;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
}

.bingo-carton:hover {
    border-color: #007bff;
}

.carton-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.carton-action-btn:disabled {
    transform: none;
    box-shadow: none;
}

/* Animaciones */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.bingo-border-carton-select {
    animation: fadeIn 0.3s ease-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.bingo-carton.select-carton {
    animation: pulse 0.5s ease-in-out;
}
</style>

<script type="text/javascript">
(function() {
    'use strict';
    
    // Verificar si ya existe una instancia y limpiarla
    if (window.currentPlayerSelection) {
        window.currentPlayerSelection.destroy();
        window.currentPlayerSelection = null;
    }
    
    if (window.currentWalletManager) {
        window.currentWalletManager.destroy();
        window.currentWalletManager = null;
    }

    // Clase WalletManager mejorada
    class WalletManager {
        constructor() {
            this.currentWallet = <?= $user['wallet']; ?>;
            this.listeners = [];
            this.destroyed = false;
        }
        
        subscribe(callback) {
            if (this.destroyed) return;
            this.listeners.push(callback);
        }
        
        unsubscribe(callback) {
            const index = this.listeners.indexOf(callback);
            if (index > -1) {
                this.listeners.splice(index, 1);
            }
        }
        
        updateWallet(newWallet, source = 'server') {
            if (this.destroyed || this.currentWallet === newWallet) return;
            
            const oldWallet = this.currentWallet;
            this.currentWallet = newWallet;
            
            this.listeners.forEach(callback => {
                try {
                    callback(newWallet, oldWallet, source);
                } catch (error) {
                    console.error('Error in wallet listener:', error);
                }
            });
        }
        
        getCurrentWallet() {
            return this.currentWallet;
        }
        
        destroy() {
            this.destroyed = true;
            this.listeners = [];
        }
    }

    // Clase PlayerSelection mejorada
    class PlayerSelection {
        constructor() {
            this.selectedGame = '<?= $game['id'] ?>';
            this.selectedCartons = new Map();
            this.cartons = [];
            this.page = 0;
            this.loading = false;
            this.otherUsersCartons = [];
            this.destroyed = false;
            
            // Variables para el wallet
            this.originalWallet = <?= $user['wallet']; ?>;
            this.gamePrice = <?= $game['price']; ?>;
            
            // Control de estado más robusto
            this.actionQueue = [];
            this.processingQueue = false;
            this.lastActionTime = 0;
            this.minActionInterval = 50;
            
            // Referencias a elementos DOM
            this.elements = {};
            
            this.init();
        }
        
        init() {
            if (this.destroyed) return;
            
            if (!this.cacheElements()) {
                console.error('Elementos requeridos no encontrados en el DOM');
                return;
            }
            
            this.setupEventListeners();
            this.setupInfiniteScroll();
            this.loadInitialCartons();
            
            // Suscribirse a cambios de wallet
            if (window.currentWalletManager) {
                this.walletListener = (newWallet, oldWallet, source) => {
                    if (source === 'notification') {
                        this.clearAllSelections();
                    }
                    this.updateWalletDisplayFromServer(newWallet);
                };
                window.currentWalletManager.subscribe(this.walletListener);
            }
        }

        cacheElements() {
            const selectors = {
                cartonsList: '#cartons-list',
                playButton: '#modal-play-button',
                cartonsContainer: '#cartons-container',
                loadingIndicator: '#loading-indicator',
                walletElement: '.modal-available-wallet'
            };
            
            for (const [key, selector] of Object.entries(selectors)) {
                this.elements[key] = document.querySelector(selector);
                if (!this.elements[key]) {
                    console.warn(`Elemento no encontrado: ${selector}`);
                    return false;
                }
            }
            
            return true;
        }
        
        setupEventListeners() {
            if (this.destroyed) return;
            
            // Event listener para el botón de jugar
            this.playButtonHandler = (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                if (this.elements.playButton.disabled || this.selectedCartons.size === 0) {
                    return;
                }
                
                this.showPlayConfirmation();
            };
            
            this.elements.playButton.addEventListener('click', this.playButtonHandler);
            
            // Event listener optimizado para cartones
            this.cartonsListHandler = (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.closest('.carton-action-btn');
                const carton = e.target.closest('.bingo-carton');
                
                if (btn) {
                    const cartonId = btn.dataset.cartonId;
                    const action = btn.dataset.action;
                    
                    this.queueAction(cartonId, action);
                } else if (carton && !btn) {
                    const cartonId = carton.dataset.cartonId;
                    const action = carton.classList.contains('select-carton') ? 'deselect' : 'select';
                    
                    if (!carton.classList.contains('already-select-carton')) {
                        this.queueAction(cartonId, action);
                    }
                }
            };
            
            this.elements.cartonsList.addEventListener('click', this.cartonsListHandler);
        }
        
        // Sistema de cola para acciones mejorado
        queueAction(cartonId, action) {
            if (this.destroyed) return;
            
            const now = Date.now();
            
            if (now - this.lastActionTime < this.minActionInterval) {
                return;
            }
            
            this.lastActionTime = now;
            
            // Limpiar acciones duplicadas para el mismo cartón
            this.actionQueue = this.actionQueue.filter(item => item.cartonId !== cartonId);
            
            this.actionQueue.push({ cartonId, action, timestamp: now });
            
            if (!this.processingQueue) {
                this.processActionQueue();
            }
        }
        
        async processActionQueue() {
            if (this.destroyed || this.processingQueue || this.actionQueue.length === 0) {
                return;
            }
            
            this.processingQueue = true;
            
            while (this.actionQueue.length > 0 && !this.destroyed) {
                const { cartonId, action } = this.actionQueue.shift();
                
                try {
                    if (action === 'select') {
                        this.executeSelectCarton(cartonId);
                    } else if (action === 'deselect') {
                        this.executeDeselectCarton(cartonId);
                    }
                } catch (error) {
                    console.error('Error processing action:', error);
                }
                
                await new Promise(resolve => setTimeout(resolve, 10));
            }
            
            this.processingQueue = false;
        }
        
        showPlayConfirmation() {
            if (this.destroyed) return;
            
            const cartonText = this.selectedCartons.size === 1 ? 'cartón' : '<?= strtolower(translate('cartons')); ?>';
            
            Swal.fire({
                title: '<?= translate('do you want to continue?'); ?>',
                text: `<?= translate('you have selected to play'); ?> (${this.selectedCartons.size} ${cartonText})`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?= translate('yes, play!'); ?>',
                cancelButtonText: '<?= translate('cancel'); ?>',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed && !this.destroyed) {
                    this.enterGame();
                }
            });
        }
        
        clearAllSelections() {
            if (this.destroyed) return;
            
            this.selectedCartons.clear();
            this.actionQueue = [];
            this.processingQueue = false;
            
            // Actualizar todos los cartones visualmente
            document.querySelectorAll('.select-carton').forEach(carton => {
                carton.classList.remove('select-carton');
            });
            
            document.querySelectorAll('.carton-action-btn').forEach(btn => {
                if (btn.dataset.action === 'deselect') {
                    btn.classList.remove('btn-danger');
                    btn.classList.add('btn-primary');
                    btn.textContent = 'Seleccionar';
                    btn.dataset.action = 'select';
                }
            });
            
            this.updatePlayButton();
        }
        
        updateWalletDisplay() {
            if (this.destroyed) return;
            
            const selectedCount = this.selectedCartons.size;
            const totalCost = selectedCount * this.gamePrice;
            const remainingWallet = (window.currentWalletManager ? window.currentWalletManager.getCurrentWallet() : this.originalWallet) - totalCost;
            
            this.renderWalletAmount(remainingWallet);
        }

        updateWalletDisplayFromServer(serverWallet) {
            if (this.destroyed) return;
            this.renderWalletAmount(serverWallet);
        }
        
        renderWalletAmount(amount) {
            if (this.destroyed || !this.elements.walletElement) return;
            
            const formattedAmount = amount.toLocaleString('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            
            this.elements.walletElement.textContent = formattedAmount;
            
            if (amount < 0) {
                this.elements.walletElement.style.color = '#dc3545';
                this.elements.walletElement.parentElement.classList.add('text-danger');
            } else {
                this.elements.walletElement.style.color = '';
                this.elements.walletElement.parentElement.classList.remove('text-danger');
            }
        }
        
        async loadInitialCartons() {
            if (this.destroyed) return;
            
            this.page = 0;
            this.cartons = [];
            this.selectedCartons.clear();
            this.actionQueue = [];
            this.processingQueue = false;
            
            this.elements.cartonsList.innerHTML = '';
            this.updatePlayButton();
            this.updateWalletDisplay();
            
            await this.generateMoreCartons();
        }
        
        async generateMoreCartons() {
            if (this.destroyed || this.loading) return;
            
            this.loading = true;
            this.elements.loadingIndicator.style.display = 'block';
            
            try {
                const newCartons = this.generateRandomCartons(12);
                this.cartons.push(...newCartons);
                this.renderCartons(newCartons);
            } catch (error) {
                console.error('Error generating cartons:', error);
                this.showNotification('Error al generar cartones', 'error');
            } finally {
                this.loading = false;
                if (this.elements.loadingIndicator) {
                    this.elements.loadingIndicator.style.display = 'none';
                }
            }
        }
        
        generateRandomCartons(count) {
            const cartons = [];
            const timestamp = Date.now();
            
            for (let i = 0; i < count; i++) {
                const id = `carton_${timestamp}_${i}_${Math.random().toString(36).substr(2, 5)}`;
                const serial = this.generateRandomSerial();
                cartons.push({
                    id: id,
                    serial: serial,
                    numbers: this.generateRandomCartonNumbers()
                });
            }
            
            return cartons;
        }
        
        generateRandomSerial() {
            const prefix = String(Math.floor(Math.random() * 1000)).padStart(3, '0');
            const number = String(Math.floor(Math.random() * 1000000)).padStart(6, '0');
            return number + prefix;
        }
        
        generateRandomCartonNumbers() {
            const numbers = [];
            const columns = {
                'B': {min: 1, max: 15},
                'I': {min: 16, max: 30},
                'N': {min: 31, max: 45},
                'G': {min: 46, max: 60},
                'O': {min: 61, max: 75}
            };
            
            const availableNumbers = {};
            for (const [letter, range] of Object.entries(columns)) {
                availableNumbers[letter] = [];
                for (let i = range.min; i <= range.max; i++) {
                    availableNumbers[letter].push(i);
                }
                availableNumbers[letter] = this.shuffleArray(availableNumbers[letter]);
            }
            
            let position = 1;
            const columnLetters = Object.keys(columns);
            
            for (let row = 0; row < 5; row++) {
                for (let col = 0; col < columnLetters.length; col++) {
                    const letter = columnLetters[col];
                    
                    if (position === 13) {
                        numbers.push({
                            position: position,
                            number: '⭐️'
                        });
                    } else {
                        const randomNum = availableNumbers[letter].pop();
                        numbers.push({
                            position: position,
                            number: randomNum
                        });
                    }
                    position++;
                }
            }
            
            return numbers;
        }

        shuffleArray(array) {
            const shuffled = [...array];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            return shuffled;
        }

        renderCartons(newCartons) {
            if (this.destroyed) return;
            
            const fragment = document.createDocumentFragment();
            
            newCartons.forEach(carton => {
                const isUserSelected = this.selectedCartons.has(carton.id);
                const isOtherUserSelected = this.otherUsersCartons.includes(carton.id);
                
                let cartonClass = 'bingo-carton';
                let buttonText = 'Seleccionar';
                let buttonClass = 'btn btn-small btn-primary d-block w-75 btn-bingo mt-1 carton-action-btn';
                let buttonAction = 'select';
                let buttonDisabled = '';
                
                if (isUserSelected) {
                    cartonClass += ' select-carton';
                    buttonText = 'Deseleccionar';
                    buttonClass = 'btn btn-small btn-danger d-block w-75 btn-bingo mt-1 carton-action-btn';
                    buttonAction = 'deselect';
                } else if (isOtherUserSelected) {
                    cartonClass += ' already-select-carton';
                    buttonText = 'No disponible';
                    buttonClass = 'btn btn-small btn-secondary d-block w-75 btn-bingo mt-1 carton-action-btn';
                    buttonAction = 'unavailable';
                    buttonDisabled = 'disabled';
                }
                
                const cartonDiv = document.createElement('div');
                cartonDiv.className = 'bingo-border-carton-select';
                
                cartonDiv.innerHTML = `
                    <h6 class="ms-2 mb-1 text-center text-muted" style="font-size: 0.8rem;">SERIAL: C${carton.serial}</h6>
                    <div class="${cartonClass}" id="carton-${carton.id}" data-carton-id="${carton.id}">
                        <div class="bingo-carton-header B"><span>B</span></div>
                        <div class="bingo-carton-header I"><span>I</span></div>
                        <div class="bingo-carton-header N"><span>N</span></div>
                        <div class="bingo-carton-header G"><span>G</span></div>
                        <div class="bingo-carton-header O"><span>O</span></div>
                        ${this.renderCartonNumbers(carton.numbers)}
                    </div>
                    <button type="button" class="${buttonClass}" data-carton-id="${carton.id}" data-action="${buttonAction}" ${buttonDisabled}>${buttonText}</button>
                `;
                
                fragment.appendChild(cartonDiv);
            });
            
            this.elements.cartonsList.appendChild(fragment);
        }
        
        renderCartonNumbers(numbers) {
            let html = '';
            
            numbers.forEach(number => {
                if (number.number === '⭐️') {
                    html += `<div class="bingo-carton-number modality" data-position="${number.position}">${number.number}</div>`;
                } else {
                    html += `<div class="bingo-carton-number" data-position="${number.position}" data-number="${number.number}">${number.number}</div>`;
                }
            });
            
            return html;
        }
        
        executeSelectCarton(cartonId) {
            if (this.destroyed) return false;
            
            if (this.selectedCartons.has(cartonId) || this.otherUsersCartons.includes(cartonId)) {
                this.showNotification('Este cartón no está disponible', 'error');
                return false;
            }
            
            const newTotalCost = (this.selectedCartons.size + 1) * this.gamePrice;
            const currentWallet = window.currentWalletManager ? window.currentWalletManager.getCurrentWallet() : this.originalWallet;
            
            if (currentWallet < newTotalCost) {
                this.showNotification('Saldo insuficiente para seleccionar más cartones', 'error');
                return false;
            }
            
            const cartonData = this.cartons.find(c => c.id === cartonId);
            if (!cartonData) {
                console.error('Carton data not found:', cartonId);
                return false;
            }
            
            this.selectedCartons.set(cartonId, cartonData);
            this.updateCartonToSelected(cartonId);
            this.updatePlayButton();
            this.updateWalletDisplay();
            
            this.showNotification(`Cartón C${cartonData.serial} seleccionado.`, 'success');
            return true;
        }
        
        executeDeselectCarton(cartonId) {
            if (this.destroyed || !this.selectedCartons.has(cartonId)) {
                return false;
            }
            
            this.selectedCartons.delete(cartonId);
            this.updateCartonToDeselected(cartonId);
            this.updatePlayButton();
            this.updateWalletDisplay();
            
            return true;
        }
        
        updateCartonToSelected(cartonId) {
            if (this.destroyed) return;
            
            const cartonElement = document.getElementById(`carton-${cartonId}`);
            const button = document.querySelector(`.carton-action-btn[data-carton-id="${cartonId}"]`);
            
            if (cartonElement) {
                cartonElement.classList.add('select-carton');
            }
            
            if (button) {
                button.classList.remove('btn-primary', 'btn-secondary');
                button.classList.add('btn-danger');
                button.textContent = 'Deseleccionar';
                button.disabled = false;
                button.dataset.action = 'deselect';
            }
        }
        
        updateCartonToDeselected(cartonId) {
            if (this.destroyed) return;
            
            const cartonElement = document.getElementById(`carton-${cartonId}`);
            const button = document.querySelector(`.carton-action-btn[data-carton-id="${cartonId}"]`);
            
            if (cartonElement) {
                cartonElement.classList.remove('select-carton');
            }
            
            if (button) {
                button.classList.remove('btn-danger', 'btn-secondary');
                button.classList.add('btn-primary');
                button.textContent = 'Seleccionar';
                button.disabled = false;
                button.dataset.action = 'select';
            }
        }
        
        updatePlayButton() {
            if (this.destroyed || !this.elements.playButton) return;
            
            const selectedCount = this.selectedCartons.size;
            
            if (selectedCount === 0) {
                this.elements.playButton.disabled = true;
                this.elements.playButton.textContent = 'Seleccionar cartones';
            } else {
                this.elements.playButton.disabled = false;
                const cartonText = selectedCount === 1 ? 'cartón' : 'cartones';
                this.elements.playButton.textContent = `Jugar (${selectedCount} ${cartonText})`;
            }
        }
        
        setupInfiniteScroll() {
            if (this.destroyed) return;
            
            let scrollTimeout;
            
            this.scrollHandler = () => {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }
                
                scrollTimeout = setTimeout(() => {
                    if (this.destroyed) return;
                    
                    const container = this.elements.cartonsContainer;
                    const scrollTop = container.scrollTop;
                    const scrollHeight = container.scrollHeight;
                    const clientHeight = container.clientHeight;
                    
                    if (scrollTop + clientHeight >= scrollHeight - 100) {
                        this.generateMoreCartons();
                    }
                }, 100);
            };
            
            this.elements.cartonsContainer.addEventListener('scroll', this.scrollHandler);
        }
        
        async enterGame() {
            if (this.destroyed || this.selectedCartons.size === 0) return;
            
            const button = this.elements.playButton;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            try {
                const selectedCartonsData = Array.from(this.selectedCartons.values()).map(carton => ({
                    id: carton.id,
                    serial: carton.serial,
                    numbers: carton.numbers
                }));
                
                const requestData = {
                    game_id: this.selectedGame,
                    carton_data: selectedCartonsData
                };
                
                const response = await fetch('<?= base_url('playings/saveCartons') ?>', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' 
                    },
                    body: JSON.stringify(requestData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showNotification('¡Cartones asignados correctamente!', 'success');

                    if (this.elements.walletElement) {
                        this.elements.walletElement.textContent = data.new_balance.toLocaleString('es-ES', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                    
                    // Actualizar el wallet manager
                    if (window.currentWalletManager) {
                        window.currentWalletManager.updateWallet(data.new_balance, 'server');
                    }
                    
                    setTimeout(() => {
                        if (!this.destroyed) {
                            window.location.href = data.redirect_url;
                        }
                    }, 1500);
                } else {
                    this.showNotification(data.message || 'Error al seleccionar cartones', 'error');
                    this.resetPlayButton();
                }
            } catch (error) {
                console.error('Error:', error);
                this.showNotification('Error de conexión', 'error');
                this.resetPlayButton();
            }
        }
        
        resetPlayButton() {
            if (this.destroyed || !this.elements.playButton) return;
            
            this.elements.playButton.disabled = false;
            const cartonText = this.selectedCartons.size === 1 ? 'cartón' : 'cartones';
            this.elements.playButton.innerHTML = `Jugar (${this.selectedCartons.size} ${cartonText})`;
        }
        
        showNotification(message, type = 'info') {
            if (this.destroyed) return;
            
            if (typeof Toastify === 'function') {
                let backgroundColor;
                
                switch(type) {
                    case 'success':
                        backgroundColor = "#28a745";
                        break;
                    case 'error':
                    case 'danger':
                        backgroundColor = "#ff4d49";
                        break;
                    case 'warning':
                        backgroundColor = "#fdb528";
                        break;
                    case 'info':
                    default:
                        backgroundColor = "#26c6f9";
                        break;
                }
                
                Toastify({
                    text: message,
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: { background: backgroundColor },
                    stopOnFocus: true
                }).showToast();
            } else {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 3000);
            }
        }
        
        destroy() {
            if (this.destroyed) return;
            
            this.destroyed = true;
            
            // Limpiar event listeners
            if (this.elements.playButton && this.playButtonHandler) {
                this.elements.playButton.removeEventListener('click', this.playButtonHandler);
            }
            
            if (this.elements.cartonsList && this.cartonsListHandler) {
                this.elements.cartonsList.removeEventListener('click', this.cartonsListHandler);
            }
            
            if (this.elements.cartonsContainer && this.scrollHandler) {
                this.elements.cartonsContainer.removeEventListener('scroll', this.scrollHandler);
            }
            
            // Desuscribirse del wallet manager
            if (window.currentWalletManager && this.walletListener) {
                window.currentWalletManager.unsubscribe(this.walletListener);
            }
            
            // Limpiar datos
            this.selectedCartons.clear();
            this.cartons = [];
            this.actionQueue = [];
            this.elements = {};
            
            console.log('PlayerSelection destroyed');
        }
    }

    // Función global para compatibilidad con notificaciones externas
    window.modalavailableWallet = function(wallet) {
        if (window.currentWalletManager) {
            window.currentWalletManager.updateWallet(wallet, 'notification');
        }
    };

    // Inicializar cuando el DOM esté listo
    function initializeBingoModal() {
        // Crear instancias globales
        window.currentWalletManager = new WalletManager();
        window.currentPlayerSelection = new PlayerSelection();
        
        console.log('Bingo modal initialized');
    }

    // Función de limpieza para cuando se cierre el modal
    function cleanupBingoModal() {
        if (window.currentPlayerSelection) {
            window.currentPlayerSelection.destroy();
            window.currentPlayerSelection = null;
        }
        
        if (window.currentWalletManager) {
            window.currentWalletManager.destroy();
            window.currentWalletManager = null;
        }
        
        console.log('Bingo modal cleaned up');
    }

    // Event listeners para el ciclo de vida del modal
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar el modal
        const modal = document.querySelector('.modal-dialog');
        if (modal) {
            // Si el modal ya está visible, inicializar inmediatamente
            initializeBingoModal();
        }
    });

    // Listener para cuando se muestre el modal (si se usa Bootstrap modal)
    document.addEventListener('shown.bs.modal', function(e) {
        const modal = e.target.querySelector('.modal-dialog');
        if (modal && modal.querySelector('#cartons-list')) {
            initializeBingoModal();
        }
    });

    // Listener para cuando se oculte el modal
    document.addEventListener('hidden.bs.modal', function(e) {
        const modal = e.target.querySelector('.modal-dialog');
        if (modal && modal.querySelector('#cartons-list')) {
            cleanupBingoModal();
        }
    });

    // Limpieza cuando se cierre la ventana
    window.addEventListener('beforeunload', function() {
        cleanupBingoModal();
    });

    // Limpieza adicional para navegación SPA
    window.addEventListener('popstate', function() {
        cleanupBingoModal();
    });

})();
</script>
