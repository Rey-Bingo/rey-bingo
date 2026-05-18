<div class="game-bingo">
    <div class="game-tablero">
        <div class="game-logo">
            <div class="text-center">
                <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="img" style="width: 250px;">
                <h1 class="h5 text-gray-900 mb-2">Hola, <?= strtok(session()->get('firstname'), ' '); ?>!</h1>
            </div>
        </div>
        <div class="game-board">
            <div class="container-board">
            <div class="board">
                <img src="<?= site_url('assets/img/tube.png'); ?>" alt="img" class="tube">
                <div class="ball-area">
                    <div class="ball" id="ball">
                        <span class="ball-number" id="ball-number">0</span>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <div class="game-cartons">
        <div class="row">
            <div class="col-md-12">
                <div class="container-cartons">
                    <?php if (isset($cartons) && count($cartons) > 0): ?>
                        <?php foreach ($cartons as $cartonData): ?>
                            <div class="border-carton">
                                <div class="carton" id="carton-<?= $cartonData['cartonId']; ?>">
                                    <!-- Encabezado con letras BINGO -->
                                    <div class="letra-carton b-col"><span>B</span></div>
                                    <div class="letra-carton i-col"><span>I</span></div>
                                    <div class="letra-carton n-col"><span>N</span></div>
                                    <div class="letra-carton g-col"><span>G</span></div>
                                    <div class="letra-carton o-col"><span>O</span></div>
                                    
                                    <?php foreach ($cartonData['numbers'] as $index => $number): ?>
                                        <?php if ($index === 12): ?>
                                            <!-- Celda de posición libre -->
                                            <div class="numero-carton libre" data-posicion="<?= $number['position']; ?>">
                                                <img src="<?= site_url('assets/img/3.gif'); ?>" class="img-fluid img-carton" alt="img">
                                            </div>
                                        <?php else: ?>
                                            <!-- Celdas numeradas -->
                                            <div class="numero-carton numero-<?= $number['number']; ?>" 
                                                 data-posicion="<?= $number['position']; ?>" 
                                                 id="numero-<?= $number['number']; ?>" 
                                                 onclick="marcarNumero(<?= $number['number']; ?>);">
                                                <?= $number['number']; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay cartones disponibles para este juego.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (session()->get('logged_in')): ?>
    <button type="button" class="btn btn-primary controls-bingo microphone" id="toggle-narration-button">
        <i class="fa-duotone fa-solid fa-microphone"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo check" id="toggle-marcar-button">
        <i class="fa-duotone fa-solid fa-check"></i>
    </button>
    
    <button type="button" class="btn btn-primary controls-bingo gear" data-bs-toggle="modal" data-bs-target="#settings">
        <i class="fa-duotone fa-solid fa-gear"></i>
    </button>
    
    <button type="button" class="btn btn-primary controls-bingo user" data-bs-toggle="modal" data-bs-target="#cuenta">
        <i class="fa-duotone fa-solid fa-user-vneck-hair"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo message" data-bs-toggle="modal" data-bs-target="#chat">
        <i class="fa-duotone fa-solid fa-message-smile"></i>
    </button>
    
    <button  class="btn btn-primary controls-bingo logout" onclick="GoToPage('logout');">
        <i class="fa-duotone fa-solid fa-arrow-right-from-arc"></i>
    </button>

    <button type="button" class="btn btn-primary btn-lg bingo">BINGO</button>
<?php endif; ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const ball = document.getElementById('ball');
        const ballNumber = document.getElementById('ball-number');
    
        let number = 1; // Comienza con el número 1
    
        // Simular la salida de números cada 4 segundos
        setInterval(() => {
            // Cambia el número de la bola
            ballNumber.textContent = number;
            
            // Añade la clase para activar la animación
            ball.classList.add('active');
    
            // Elimina la animación después de 4 segundos para que pueda repetirse
            setTimeout(() => {
                ball.classList.remove('active');
            }, 4000);
    
            // Incrementa el número para la próxima bola
            number++;
            if (number > 75) number = 1; // Resetea los números después de 75 (si es bingo estándar)
            
        }, 5000); // Cambia el número cada 5 segundos (1 segundo de descanso entre animaciones)
    });

    /*document.addEventListener('DOMContentLoaded', () => {
        const ball = document.getElementById('ball');
        const ballNumber = document.getElementById('ball-number');
    
        let number = 1; // Comienza con el número 1
    
        // Simular la salida de números cada 2 segundos
        setInterval(() => {
            // Cambia el número de la bola
            ballNumber.textContent = number;
            
            // Añade la clase para activar la animación
            ball.classList.add('active');
    
            // Elimina la animación después de que termine
            setTimeout(() => {
                ball.classList.remove('active');
            }, 2000);
    
            // Incrementa el número para la próxima bola
            number++;
            if (number > 75) number = 1; // Resetea los números después de 75 (si es bingo estándar)
            
        }, 3000); // Cambia el número cada 3 segundos
    });*/
    
    $(document).ready(function() {
        $('#game-form').on('submit', function(e) {
            e.preventDefault();
    
            var button = $('#game-button');
            button.prop("disabled", true);
    
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
    
            $.ajax({
                url: '<?= site_url('games/gameSubmit') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(field, message) {
                                $('#' + field + '-error').text(message).removeClass('d-none');
                                $('#' + field).addClass('is-invalid');
                            });
                        }
                    }
                },
                error: function() {
                    Toastify({
                        text: "<?= translate('there was an error in the request to the server'); ?>",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" },
                        stopOnFocus: true
                    }).showToast();
                },
                complete: function() {
                    button.prop("disabled", false);
                }
            });
        });
    });
</script>