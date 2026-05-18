<div class="card-bingo-dashboard mw-600px shadow-lg">
    <div class="dashboard-bingo">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="LOGO" style="width: 250px;">
                    <div class="efecto-bingo"></div>
                    <h1 class="h5 text-gray-900 mb-2">Hola, <?= strtok(session()->get('name'), ' '); ?>!</h1>
                </div>
        
                <?php echo form_open(site_url() . 'games/gameSubmit', array('enctype' => 'multipart/form-data', 'class' => 'user', 'id' => 'game-form'));?>
                
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user input-bingo" name="description" id="description" placeholder="Descripción" autofocus autocomplete="off">
                            </div>
                            <small id="description-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="date" class="form-control form-control-user input-bingo" name="date" id="date" placeholder="Fecha" autofocus autocomplete="off" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <small id="date-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="time" class="form-control form-control-user input-bingo" name="time" id="time" placeholder="Hora" autofocus autocomplete="off" value="<?php echo date('H:i'); ?>">
                            </div>
                            <small id="time-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-bingo mt-2" id="game-button">Iniciar</button>
                    </div>
                <?= form_close(); ?>
                
                <hr />

                <div class="text-center fs-7">
                    ¿Aún no tienes cartones? <br /> <a class="small fs-7 linkPage" href="<?= site_url('purchase'); ?>">Comprar aquí</a>
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
                    alert('Hubo un error en la solicitud.');
                },
                complete: function() {
                    button.prop("disabled", false);
                }
            });
        });
    });
</script>