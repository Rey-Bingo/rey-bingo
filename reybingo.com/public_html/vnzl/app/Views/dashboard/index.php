<div class="card-bingo-dashboard mw-400px shadow-lg">
    <div class="dashboard-bingo">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="img" style="width: 250px;">
                    <div class="efecto-bingo"></div>
                    <h1 class="h5 text-gray-900 mb-2">Hola, <?= strtok(session()->get('name'), ' '); ?>!</h1>
                </div>

                <!-- Mostrar mensaje de error global -->
                <div id="global-error" class="alert alert-danger d-none"></div>
        
                <?php echo form_open(site_url() . 'games/gameSubmit', array('enctype' => 'multipart/form-data', 'class' => 'user', 'id' => 'game-form'));?>
                
                    <?= csrf_field() ?>
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" id="decrease-button" class="btn btn-primary btn-minus"><i class="fa-duotone fa-solid fa-minus"></i></button>
                                    <input type="number" class="form-control form-control-user input-bingo" name="cartons" id="cartons" value="1" min="1" placeholder="N° de cartones" autofocus autocomplete="off">
                                <button type="button" id="increase-button" class="btn btn-primary btn-plus"><i class="fa-duotone fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <small id="cartons-error" class="text-danger d-none pl-5"></small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-bingo mt-2" id="game-button">Jugar</button>
                <?= form_close(); ?>
                
                <hr />

                <div class="text-center fs-5">
                    ¿Aún no tienes cartones? <br /> <a class="small fs-6 linkPage" href="<?= site_url('purchase'); ?>">Comprar aquí</a>
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
    
    document.addEventListener('DOMContentLoaded', function() {
        const decreaseButton = document.getElementById('decrease-button');
        const increaseButton = document.getElementById('increase-button');
        const quantityInput = document.getElementById('cartons');
        const maxCartons = 4;

        decreaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        increaseButton.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue < maxCartons) {
                quantityInput.value = currentValue + 1;
            }
        });
    });
</script>