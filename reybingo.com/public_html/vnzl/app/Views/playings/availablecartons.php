<div class="modal-dialog modal-dialog-centered max-w-70">
    <div class="modal-content">
        <div class="modal-header pb-2">
            <h6 class="modal-title ps-2 text-uppercase"><i class="fa-duotone fa-solid fa-gamepad"></i> <?= $room['name']; ?></h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body p-1 pt-0 text-center">
            <div class="text-center p-2"><?= $game['description']; ?> <span id="selection-time"></span></div>
            <div class="action-sheet-content mb-3" style="max-height: 400px; overflow-y: auto;" id="cartons-container">
                <div class="cartons-section-select">
                    <?php if (isset($cartons) && count($cartons) == 0): ?>
                        <style>
                            .content-cartons-select {
                                grid-template-columns: repeat(1, 1fr) !important;
                            }
                        </style>
                    <?php endif; ?>
                    <div class="content-cartons-select" id="cartons-list">
                    <?php if (isset($cartons) && count($cartons) > 0): ?>
                        <?php foreach ($cartons as $cartonData): ?>
                            <div class="bingo-border-carton-select">
                                <h6 class="ms-2 mb-1 text-center text-muted" style="font-size: 0.8rem;">SERIAL: C<?= $cartonData['serial']; ?></h6>
                                <div class="bingo-carton" id="carton-<?= $cartonData['cartonId']; ?>" data-carton-id="<?= $cartonData['cartonId']; ?>">
                                    <div class="bingo-carton-header B"><span>B</span></div>
                                    <div class="bingo-carton-header I"><span>I</span></div>
                                    <div class="bingo-carton-header N"><span>N</span></div>
                                    <div class="bingo-carton-header G"><span>G</span></div>
                                    <div class="bingo-carton-header O"><span>O</span></div>
                                    
                                    <?php foreach ($cartonData['numbers'] as $index => $number): ?>
                                        <?php if ($index === 12): ?>
                                            <div class="bingo-carton-number modality" data-position="<?= $number['position']; ?>">⭐️</div>
                                        <?php else: ?>
                                            <div class="bingo-carton-number"><?= $number['number']; ?></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-small btn-primary d-block w-75 btn-bingo mt-1 carton-action-btn" data-carton-id="<?= $cartonData['cartonId']; ?>" data-action="select"><?= translate('select'); ?></button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h6 class="text-center mt-2"><?= translate('there are no cards available for this game'); ?></h6>
                    <?php endif; ?>
                    </div>
                </div>
                
                <div id="loading-indicator" style="display: none; padding: 20px; text-center;">
                    <i class="fa fa-spinner fa-spin"></i> Cargando más cartones...
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-small btn-primary d-block w-50 btn-bingo" id="play-button"><?= translate('play'); ?></button>
            </div>
            <div class="text-center p-2"><?= translate('my wallet'); ?> <?= systemGet('currency'); ?> <span class="available-wallet"><?= $user['wallet']; ?></span></div>
            <div class="text-center p-2 hidden"><?= translate('selected cartons'); ?> <span id="select-cartons">0</span></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var currentPage = <?= $currentPage ?? 1 ?>;
    var hasMorePages = <?= ($currentPage < $totalPages) ? 'true' : 'false' ?>;
    var isLoading = false;
    var selectedCartons = [];
    var otherUsersCartons = [];
    var selectionTimers = {};
    var gameId = <?= $game['id'] ?>;
    var realTimeInterval;
    var lastUpdateTimestamp = 0;

    $(document).ready(function() {
        // Event handlers para scroll infinito
        $('#cartons-container').on('scroll', function() {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                if (hasMorePages && !isLoading) {
                    loadMoreCartons();
                }
            }
        });

        // Event handlers para botones de cartones
        $(document).on('click', '.carton-action-btn', function() {
            const cartonId = $(this).data('carton-id');
            const action = $(this).data('action');
            
            if (action === 'select') {
                selectCarton(cartonId);
            } else if (action === 'deselect') {
                deselectCarton(cartonId);
            }
        });

        // Event handler para click en cartón seleccionado
        $(document).on('click', '.bingo-carton.select-carton', function() {
            const cartonId = $(this).data('carton-id');
            deselectCarton(cartonId);
        });

        // Event handler para el botón de jugar
        $('#play-button').on('click', function(e) {
            e.preventDefault();
            const selectedCount = selectedCartons.length;
            Swal.fire({
                title: '<?= translate('do you want to continue?'); ?>',
                text: `<?= translate('you have selected to play'); ?> (${selectedCount} <?= strtolower(translate('cartons')); ?>)`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?= translate('yes, play!'); ?>',
                cancelButtonText: '<?= translate('cancel'); ?>',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    playGame();
                }
            });
        });

        // Inicializar actualizaciones en tiempo real y cargar cartones seleccionados
        startRealTimeUpdates();
        loadSelectedCartons();
        updatePlayButton();
    });

    // Función para procesar el juego
    function playGame() {
        const button = $('#play-button');
        
        // Validar que haya cartones seleccionados
        if (selectedCartons.length === 0) {
            showNotification('Debes seleccionar al menos un cartón para jugar', 'error');
            return;
        }

        // Deshabilitar botón
        button.prop('disabled', true);
        const originalText = button.text();
        button.html('<i class="fa fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: '<?= site_url('playings/playGame') ?>',
            method: 'POST',
            data: {
                game_id: gameId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Éxito - mostrar mensaje y redirigir
                    showNotification(
                        `¡Cartones asignados correctamente!`, 
                        'success'
                    );

                    showNotification(
                        `Cartones: ${response.cartons_assigned}, Costo: <?= systemGet('currency'); ?> ${response.total_cost}`, 
                        'info'
                    );

                    /*showNotification(
                        `Saldo disponible: <?= systemGet('currency'); ?> ${response.new_balance}`, 
                        'success'
                    );*/
                    
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 2000);

                } else if (response.play) {
                    // Juego ya iniciado y usuario tiene cartones
                    showNotification('El juego ya ha iniciado', 'warning');
                    setTimeout(function() {
                        window.location.href = response.redirect;
                    }, 1500);

                } else if (response.finished) {
                    // Juego terminado
                    showNotification('El juego ha terminado', 'error');
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }

                } else if (response.initiated) {
                    // Juego iniciado pero usuario no tiene cartones
                    showNotification('El juego ya ha iniciado y no puedes unirte', 'error');

                } else if (response.payments) {
                    // Falta recarga mínima
                    showNotification(
                        `Para jugar debes recargar al menos <?= systemGet('currency'); ?> ${response.amount} a tu billetera`, 
                        'error'
                    );

                } else if (response.time) {
                    // Muy temprano para entrar
                    showNotification('Debes ingresar a la partida 10 minutos antes de iniciar', 'error');

                } else {
                    // Errores de validación
                    if (response.errors) {
                        let errorMessages = [];
                        $.each(response.errors, function(field, message) {
                            errorMessages.push(message);
                        });
                        showNotification(errorMessages.join(', '), 'error');
                    } else {
                        showNotification(response.message || 'Error desconocido al procesar el juego', 'error');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en playGame:', error);
                showNotification('Error de conexión al procesar el juego', 'error');
            },
            complete: function() {
                // Rehabilitar botón
                button.prop('disabled', false);
                button.text(originalText);
            }
        });
    }

    // Función para actualizar el botón de jugar según la selección
    function updatePlayButton() {
        const button = $('#play-button');
        const selectedCount = selectedCartons.length;
        
        if (selectedCount === 0) {
            button.prop('disabled', true);
            button.text('<?= translate('select cartons'); ?>');
        } else {
            button.prop('disabled', false);
            button.text(`<?= translate('play'); ?> (${selectedCount} <?= strtolower(translate('cartons')); ?>)`);
        }
    }

    // Función para iniciar actualizaciones en tiempo real
    function startRealTimeUpdates() {
        realTimeInterval = setInterval(function() {
            updateCartonsRealTime();
        }, 3000);
        
        console.log('Actualizaciones en tiempo real iniciadas');
    }

    // Función para actualizar cartones en tiempo real
    function updateCartonsRealTime() {
        $.ajax({
            url: '<?= site_url('playings/getRealTimeCartonsStatus') ?>',
            method: 'POST',
            data: {
                game_id: gameId
            },
            success: function(response) {
                if (response.success) {
                    if (response.timestamp > lastUpdateTimestamp) {
                        lastUpdateTimestamp = response.timestamp;
                        
                        const newOtherUsersCartons = response.otherUsersCartons || [];
                        const currentOtherUsersCartons = otherUsersCartons.slice();
                        
                        // Cartones liberados por otros usuarios
                        const releasedCartons = currentOtherUsersCartons.filter(cartonId => 
                            !newOtherUsersCartons.includes(cartonId)
                        );
                        
                        // Cartones recién seleccionados por otros usuarios
                        const newlySelectedByOthers = newOtherUsersCartons.filter(cartonId => 
                            !currentOtherUsersCartons.includes(cartonId)
                        );
                        
                        // Procesar cartones liberados
                        releasedCartons.forEach(cartonId => {
                            if (!selectedCartons.includes(cartonId)) {
                                updateCartonToAvailable(cartonId);
                                //showNotification(`Cartón C${getCartonSerial(cartonId)} ahora está disponible`, 'success');
                            }
                        });
                        
                        // Procesar cartones recién seleccionados por otros
                        newlySelectedByOthers.forEach(cartonId => {
                            if (!selectedCartons.includes(cartonId)) {
                                updateCartonToUnavailable(cartonId);
                                //showNotification(`Cartón C${getCartonSerial(cartonId)} fue seleccionado por otro jugador`, 'warning');
                            }
                        });
                        
                        otherUsersCartons = newOtherUsersCartons;
                        
                        // Verificar cartones del usuario que expiraron
                        const currentUserCartons = response.userCartons || [];
                        const expiredUserCartons = selectedCartons.filter(cartonId => 
                            !currentUserCartons.includes(cartonId)
                        );
                        
                        expiredUserCartons.forEach(cartonId => {
                            removeCartonSelection(cartonId);
                            //showNotification(`Tu selección del cartón C${getCartonSerial(cartonId)} expiró`, 'error');
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en actualización en tiempo real:', error);
            }
        });
    }

    // Función para seleccionar cartón
    function selectCarton(cartonId) {
        if (selectedCartons.includes(cartonId) || otherUsersCartons.includes(cartonId)) {
            showNotification('Este cartón no está disponible', 'error');
            return;
        }

        $.ajax({
            url: '<?= site_url('playings/selectCarton') ?>',
            method: 'POST',
            data: {
                carton_id: cartonId,
                game_id: gameId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    updateCartonToSelected(cartonId);
                    selectedCartons.push(parseInt(cartonId));
                    $('#select-cartons').text(selectedCartons.length);
                    updatePlayButton();
                    startCartonTimer(cartonId);
                    updateSelectionTime();
                    
                    setTimeout(updateCartonsRealTime, 500);
                    showNotification(`Cartón C${getCartonSerial(cartonId)} seleccionado.`, 'success');
                } else {
                    showNotification(response.message || 'Error al seleccionar cartón', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error selecting carton:', error);
                showNotification('Error al seleccionar cartón', 'error');
            }
        });
    }

    // Función para deseleccionar cartón
    function deselectCarton(cartonId) {
        if (!selectedCartons.includes(parseInt(cartonId))) {
            return;
        }

        $.ajax({
            url: '<?= site_url('playings/deselectCarton') ?>',
            method: 'POST',
            data: {
                carton_id: cartonId,
                game_id: gameId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    removeCartonSelection(cartonId);
                    setTimeout(updateCartonsRealTime, 500);
                    //showNotification(`Cartón C${getCartonSerial(cartonId)} deseleccionado`, 'success');
                } else {
                    showNotification(response.message || 'Error al deseleccionar cartón', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deselecting carton:', error);
                showNotification('Error al deseleccionar cartón', 'error');
            }
        });
    }

    // Función para cargar más cartones (scroll infinito)
    function loadMoreCartons() {
        if (isLoading) return;
        
        isLoading = true;
        currentPage++;
        
        $('#loading-indicator').show();
        
        $.ajax({
            url: '<?= site_url('playings/loadMoreCartons') ?>',
            method: 'POST',
            data: {
                game_id: gameId,
                page: currentPage
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            success: function(response) {
                if (response.success && response.cartons.length > 0) {
                    appendCartons(response.cartons);
                    hasMorePages = response.hasMore;
                } else {
                    hasMorePages = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading more cartons:', error);
                currentPage--; 
            },
            complete: function() {
                isLoading = false;
                $('#loading-indicator').hide();
            }
        });
    }

    // Función para cargar cartones ya seleccionados
    function loadSelectedCartons() {
        $.ajax({
            url: '<?= site_url('playings/getSelectedCartons') ?>/' + gameId,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    // Procesar cartones del usuario
                    if (response.userCartons && response.userCartons.length > 0) {
                        response.userCartons.forEach(carton => {
                            const cartonId = carton.carton;
                            
                            updateCartonToSelected(cartonId);
                            selectedCartons.push(parseInt(cartonId));
                            
                            const createdAt = new Date(carton.created_at).getTime();
                            const expirationTime = createdAt + (5 * 60 * 1000);
                            
                            if (expirationTime > Date.now()) {
                                selectionTimers[cartonId] = expirationTime;
                            }
                        });
                        
                        $('#select-cartons').text(selectedCartons.length);
                        updatePlayButton();
                        updateSelectionTime();
                    }
                    
                    // Procesar cartones de otros usuarios
                    if (response.otherUsersCartons && response.otherUsersCartons.length > 0) {
                        response.otherUsersCartons.forEach(carton => {
                            const cartonId = carton.carton;
                            otherUsersCartons.push(parseInt(cartonId));
                            updateCartonToUnavailable(cartonId);
                        });
                    }
                }
            }
        });
    }

    // Función para agregar cartones al DOM
    function appendCartons(cartons) {
        let html = '';
        cartons.forEach(function(cartonData) {
            const cartonId = cartonData.cartonId;
            const isUserSelected = selectedCartons.includes(cartonId);
            const isOtherUserSelected = otherUsersCartons.includes(cartonId);
            
            let cartonClass = 'bingo-carton';
            let buttonText = '<?= translate('select'); ?>';
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
            
            html += `<div class="bingo-border-carton-select">`;
            html += `<h6 class="ms-2 mb-1 text-center text-muted" style="font-size: 0.8rem;">SERIAL: C${cartonData.serial}</h6>`;
            html += `<div class="${cartonClass}" id="carton-${cartonId}" data-carton-id="${cartonId}">`;
            html += `<div class="bingo-carton-header B"><span>B</span></div><div class="bingo-carton-header I"><span>I</span></div><div class="bingo-carton-header N"><span>N</span></div><div class="bingo-carton-header G"><span>G</span></div><div class="bingo-carton-header O"><span>O</span></div>`;
            
            cartonData.numbers.forEach(function(number, index) {
                if (index === 12) {
                    html += `<div class="bingo-carton-number modality" data-position="${number.position}">⭐️</div>`;
                } else {
                    html += `<div class="bingo-carton-number">${number.number}</div>`;
                }
            });
            
            html += `</div>`;
            html += `<button type="button" class="${buttonClass}" data-carton-id="${cartonId}" data-action="${buttonAction}" ${buttonDisabled}>${buttonText}</button>`;
            html += `</div>`;
        });
        
        $('#cartons-list').append(html);
    }

    // Funciones de actualización visual de cartones
    function updateCartonToSelected(cartonId) {
        $(`#carton-${cartonId}`).addClass('select-carton');
        
        const button = $(`.carton-action-btn[data-carton-id="${cartonId}"]`);
        button.removeClass('btn-primary btn-secondary')
              .addClass('btn-danger')
              .text('Deseleccionar')
              .prop('disabled', false)
              .data('action', 'deselect');
    }

    function updateCartonToDeselected(cartonId) {
        $(`#carton-${cartonId}`).removeClass('select-carton');
        
        const button = $(`.carton-action-btn[data-carton-id="${cartonId}"]`);
        button.removeClass('btn-danger btn-secondary')
              .addClass('btn-primary')
              .text('<?= translate('select'); ?>')
              .prop('disabled', false)
              .data('action', 'select');
    }

    function updateCartonToUnavailable(cartonId) {
        const cartonElement = $(`#carton-${cartonId}`);
        const buttonElement = $(`.carton-action-btn[data-carton-id="${cartonId}"]`);
        
        if (!cartonElement.hasClass('already-select-carton')) {
            cartonElement.addClass('already-select-carton');
            
            buttonElement.removeClass('btn-primary btn-danger')
                         .addClass('btn-secondary')
                         .text('No disponible')
                         .prop('disabled', true)
                         .data('action', 'unavailable');
            
            cartonElement.fadeOut(200).fadeIn(200);
        }
    }

    function updateCartonToAvailable(cartonId) {
        const cartonElement = $(`#carton-${cartonId}`);
        const buttonElement = $(`.carton-action-btn[data-carton-id="${cartonId}"]`);
        
        if (cartonElement.hasClass('already-select-carton')) {
            cartonElement.removeClass('already-select-carton select-carton');
            
            buttonElement.removeClass('btn-secondary btn-danger')
                         .addClass('btn-primary')
                         .text('<?= translate('select'); ?>')
                         .prop('disabled', false)
                         .data('action', 'select');
            
            cartonElement.fadeOut(200).fadeIn(200);
        }
    }

    // Funciones de temporizador
    function startCartonTimer(cartonId) {
        const expirationTime = Date.now() + (5 * 60 * 1000);
        selectionTimers[cartonId] = expirationTime;
    }

    function updateSelectionTime() {
        if (selectedCartons.length > 0) {
            const now = Date.now();
            const times = Object.values(selectionTimers).filter(time => time > now);
            
            if (times.length > 0) {
                const oldestSelection = Math.min(...times);
                const timeLeft = Math.max(0, oldestSelection - now);
                
                if (timeLeft > 0) {
                    const minutes = Math.floor(timeLeft / 60000);
                    const seconds = Math.floor((timeLeft % 60000) / 1000);
                    $('#selection-time').text(`Tiempo restante: ${minutes}:${seconds.toString().padStart(2, '0')}`);
                    
                    setTimeout(updateSelectionTime, 1000);
                } else {
                    $('#selection-time').text('');
                }
            } else {
                $('#selection-time').text('');
            }
        } else {
            $('#selection-time').text('');
        }
    }

    function removeCartonSelection(cartonId) {
        updateCartonToDeselected(cartonId);
        
        selectedCartons = selectedCartons.filter(id => id != cartonId);
        delete selectionTimers[cartonId];
        
        $('#select-cartons').text(selectedCartons.length);
        updatePlayButton();
        
        if (selectedCartons.length === 0) {
            $('#selection-time').text('');
        }
    }

    // Funciones auxiliares
    function getCartonSerial(cartonId) {
        const cartonElement = $(`#carton-${cartonId}`);
        const serialElement = cartonElement.closest('.bingo-border-carton-select').find('h6');
        if (serialElement.length) {
            const serialText = serialElement.text();
            const match = serialText.match(/C(\d+)/);
            return match ? match[1] : cartonId;
        }
        return cartonId;
    }

    function showNotification(message, type = 'info') {
        var backgroundColor;
        
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
    }

    // Event handlers para limpieza
    $(document).on('hidden.bs.modal', function() {
        if (realTimeInterval) {
            clearInterval(realTimeInterval);
            console.log('Actualizaciones en tiempo real detenidas');
        }
    });

    $(window).on('beforeunload', function() {
        if (realTimeInterval) {
            clearInterval(realTimeInterval);
        }
    });
</script>
