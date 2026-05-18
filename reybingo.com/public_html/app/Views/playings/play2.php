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
        // Event handlers existentes...
        $('#cartons-container').on('scroll', function() {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight - 100) {
                if (hasMorePages && !isLoading) {
                    loadMoreCartons();
                }
            }
        });

        $(document).on('click', '.carton-action-btn', function() {
            const cartonId = $(this).data('carton-id');
            const action = $(this).data('action');
            
            if (action === 'select') {
                selectCarton(cartonId);
            } else if (action === 'deselect') {
                deselectCarton(cartonId);
            }
        });

        $(document).on('click', '.bingo-carton.select-carton', function() {
            const cartonId = $(this).data('carton-id');
            deselectCarton(cartonId);
        });

        // Nuevo event handler para el botón de jugar
        $('#play-button').on('click', function(e) {
            e.preventDefault();
            playGame();
        });

        // Inicializar actualizaciones en tiempo real
        startRealTimeUpdates();
        loadSelectedCartons();
    });

    // Nueva función para procesar el juego
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
                        `¡Cartones asignados correctamente! Cartones: ${response.cartons_assigned}, Costo: ${response.total_cost}Bs, Saldo restante: ${response.new_balance}Bs`, 
                        'success'
                    );
                    
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
                        `Para jugar debes recargar al menos ${response.amount}Bs a tu billetera`, 
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
                        showNotification('Error desconocido al procesar el juego', 'error');
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
            button.text(`<?= translate('play'); ?> (${selectedCount} cartones)`);
        }
    }

    // Actualizar las funciones existentes para llamar updatePlayButton
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
                    updatePlayButton(); // Nueva línea
                    startCartonTimer(cartonId);
                    updateSelectionTime();
                    
                    setTimeout(updateCartonsRealTime, 500);
                    showNotification(`Cartón C${getCartonSerial(cartonId)} seleccionado correctamente`, 'success');
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

    function removeCartonSelection(cartonId) {
        updateCartonToDeselected(cartonId);
        
        selectedCartons = selectedCartons.filter(id => id != cartonId);
        delete selectionTimers[cartonId];
        
        $('#select-cartons').text(selectedCartons.length);
        updatePlayButton(); // Nueva línea
        
        if (selectedCartons.length === 0) {
            $('#selection-time').text('');
        }
    }

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
                    showNotification(`Cartón C${getCartonSerial(cartonId)} deseleccionado`, 'success');
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

    // Resto de funciones existentes...
    function startRealTimeUpdates() {
        realTimeInterval = setInterval(function() {
            updateCartonsRealTime();
        }, 3000);
        
        console.log('Actualizaciones en tiempo real iniciadas');
    }

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
                        
                        const releasedCartons = currentOtherUsersCartons.filter(cartonId => 
                            !newOtherUsersCartons.includes(cartonId)
                        );
                        
                        const newlySelectedByOthers = newOtherUsersCartons.filter(cartonId => 
                            !currentOtherUsersCartons.includes(cartonId)
                        );
                        
                        releasedCartons.forEach(cartonId => {
                            if (!selectedCartons.includes(cartonId)) {
                                updateCartonToAvailable(cartonId);
                                showNotification(`Cartón C${getCartonSerial(cartonId)} ahora está disponible`, 'success');
                            }
                        });
                        
                        newlySelectedByOthers.forEach(cartonId => {
                            if (!selectedCartons.includes(cartonId)) {
                                updateCartonToUnavailable(cartonId);
                                showNotification(`Cartón C${getCartonSerial(cartonId)} fue seleccionado por otro jugador`, 'warning');
                            }
                        });
                        
                        otherUsersCartons = newOtherUsersCartons;
                        
                        const currentUserCartons = response.userCartons || [];
                        const expiredUserCartons = selectedCartons.filter(cartonId => 
                            !currentUserCartons.includes(cartonId)
                        );
                        
                        expiredUserCartons.forEach(cartonId => {
                            removeCartonSelection(cartonId);
                            showNotification(`Tu selección del cartón C${getCartonSerial(cartonId)} expiró`, 'error');
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en actualización en tiempo real:', error);
            }
        });
    }

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
        const notification = $(`
            <div class="alert alert-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'danger'} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fa ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.alert('close');
        }, 5000);
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

    // Resto de funciones existentes (loadMoreCartons, loadSelectedCartons, etc.)...
    // [Incluir todas las funciones que ya tenías]

    // Limpiar intervalo cuando se cierre el modal
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

    // Inicializar el estado del botón al cargar
    updatePlayButton();
</script>
