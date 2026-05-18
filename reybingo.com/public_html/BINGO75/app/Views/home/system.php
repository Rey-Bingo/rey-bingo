<style>
    .edit-button.logo {
        right: 220px;
    }
</style>
<div class="card-bingo-signup mw-600px shadow-lg">
    <div class="signup-bingo">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="img" style="width: 250px;">
                    <h1 class="h5 text-gray-900 mb-2">Configuraciones</h1>
                </div>
        
                <?php echo form_open(site_url() . 'signup/signupSubmit', array('enctype' => 'multipart/form-data', 'class' => 'user', 'id' => 'signup-form'));?>
                
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-picture">
                                <!-- Mostrar la imagen del perfil del usuario -->
                                <img id="profileImage" src="<?= $imagePath ?>" alt="Logo">
                                <label for="fileInput" class="edit-button logo"><i class="fa-duotone fa-edit"></i></label>
                                <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="previewImage(event)">
                                <!-- Campo oculto para almacenar la imagen en base64 -->
                                <input type="hidden" id="profile_image_input" name="image">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user input-bingo" name="name" id="name" placeholder="Nombre del BINGO" autofocus autocomplete="off" value="<?= systemGet('name'); ?>">
                            </div>
                            <small id="name-error" class="text-danger d-none pl-5"></small>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user input-bingo" name="document" id="document" placeholder="Cédula / RIF" autocomplete="off">
                            </div>
                            <small id="document-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user input-bingo" name="username" id="username" placeholder="Usuario" autocomplete="off">
                            </div>
                            <small id="username-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="number" class="form-control form-control-user input-bingo" name="phone" id="phone" placeholder="Teléfono" autocomplete="off">
                            </div>
                            <small id="phone-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="email" class="form-control form-control-user input-bingo" name="email" id="email" placeholder="Correo eletrónico" autocomplete="off">
                            </div>
                            <small id="email-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user input-bingo" name="password" id="password" placeholder="Contraseña" autocomplete="off">
                            </div>
                            <small id="password-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user input-bingo" name="password_confirm" id="password_confirm" placeholder="Confirmar contraseña" autocomplete="off">
                            </div>
                            <small id="password_confirm-error" class="text-danger d-none pl-5"></small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-bingo mt-2" id="signup-button">Crear</button>
                    </div>
                <?= form_close(); ?>
                
                <hr />

                <div class="text-center fs-5">
                    ¿Ya tienes una cuenta? <br /> <a class="small fs-6 linkPage" href="<?= site_url('signin'); ?>">Ingresar</a>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script type="text/javascript">
    $(document).ready(function() {
        $('#signup-form').on('submit', function(e) {
            e.preventDefault(); 
    
            var button = $('#signup-button');
            button.prop("disabled", true); 
    
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
    
            $.ajax({
                url: '<?= site_url('signup/signupSubmit') ?>',
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

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profileImage');
            output.src = reader.result;

            document.getElementById('profile_image_input').value = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>