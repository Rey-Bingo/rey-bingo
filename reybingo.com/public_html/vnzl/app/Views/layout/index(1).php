<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="Family Bingo es un juego de bingo online diseñado para toda la familia. Disfruta de partidas interactivas con cartones virtuales, bombo automático y la posibilidad de jugar en tiempo real desde cualquier dispositivo. Fácil de usar, sin descargas y totalmente gratuito.">
    <meta name="keywords" content="Family Bingo, juego de bingo online, bingo para la familia, bingo en línea, jugar bingo online gratis, juego de bingo familiar, bingo HTML y PHP, bingo interactivo online, bingo con cartones virtuales, generador de cartones de bingo, bingo personalizado para familias, juego de bingo con sonido, bingo multijugador online, bingo en tiempo real, bingo para niños y adultos, crear cartones de bingo online, bombo de bingo automático, marcador de números de bingo, juego de bingo responsivo, bingo PHP MySQL, juego de bingo en HTML5, bingo con animaciones, bingo online en dispositivos móviles, bingo para reuniones familiares, bingo online gratis sin descarga">
    <meta name="author" content="IsAppWeb">
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" href="<?= site_url('assets/img/favicon.ico'); ?>?<?php echo md5(date("Hms")); ?>" type="image/x-icon">
    <link rel="icon" href="<?= site_url('assets/img/favicon-16x16.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="16x16" type="image/png">
    <link rel="icon" href="<?= site_url('assets/img/favicon-32x32.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="32x32" type="image/png">
    <link rel="icon" href="<?= site_url('assets/img/favicon-96x96.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="96x96" type="image/png">
    
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-57x57.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="57x57">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-72x72.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="72x72">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-76x76.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="76x76">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-114x114.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="114x114">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-120x120.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="120x120">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-144x144.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="144x144">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-152x152.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="152x152">
    <link rel="apple-touch-icon" href="<?= site_url('assets/img/favicon-180x180.png'); ?>?<?php echo md5(date("Hms")); ?>" sizes="180x180">

    <link rel="manifest" href="<?= site_url('assets/img/site.webmanifest'); ?>?<?php echo md5(date("Hms")); ?>">
    <meta name="theme-color" content="#ffffff">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= site_url('assets/img/ms-icon-144x144.png'); ?>?<?php echo md5(date("Hms")); ?>">

    <link rel="mask-icon" href="<?= site_url('assets/img/safari-pinned-tab.svg'); ?>?<?php echo md5(date("Hms")); ?>" color="#5bbad5">

    <meta property="og:title" content="<?= APP_NAME; ?>">
    <meta property="og:site_name" content="<?= APP_NAME; ?>">
    <meta property="og:description" content="Family Bingo es un juego de bingo online diseñado para toda la familia. Disfruta de partidas interactivas con cartones virtuales, bombo automático y la posibilidad de jugar en tiempo real desde cualquier dispositivo. Fácil de usar, sin descargas y totalmente gratuito.">
    <meta property="og:image" content="<?= site_url('assets/img/og-image.png'); ?>?<?php echo md5(date("Hms")); ?>">
    <meta property="og:url" content="https://www.bingo.isappweb.com/">
    <meta property="og:type" content="website">

    <title><?= APP_NAME; ?></title>

    <!-- Custom fonts for this template-->
    <link href="<?= site_url('assets/iconos/css/all.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= site_url('assets/css/sb-admin-2.min.css'); ?>?<?php echo md5(date("Hms")); ?>" rel="stylesheet">

    <link href="<?= site_url('assets/css/styles.css'); ?>?<?php echo md5(date("Hms")); ?>" rel="stylesheet">

</head>
<body class="bg-gradient-bingo">
    <div class="preloader">
        <div class="canvas">
            <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="LOGO" style="width: 250px;">
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            <div class="loading-percentage">1%</div>
        </div>
        <div class="efecto-bingo"></div>
    </div>
    <div class="modal fade" id="settings" tabindex="-1" aria-labelledby="settingsLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsLabel">Configuraciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí puedes agregar los campos de configuración que necesites -->
                    <form id="settingsForm">
                        <div class="mb-3">
                            <label for="volume" class="form-label">Volumen</label>
                            <input type="range" class="form-range" id="ddvolume" min="0" max="100">
                        </div>
                        <div class="mb-3">
                            <label for="theme" class="form-label">Tema</label>
                            <select id="theme" class="form-select">
                                <option value="light">Claro</option>
                                <option value="dark">Oscuro</option>
                            </select>
                        </div>
                        <!-- Agrega más opciones de configuración aquí -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveSettings">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="controls">
        <button type="button" class="btn btn-primary controls-bingo volume"><i class="fa-duotone fa-solid fa-volume-slash"></i></button>

        <button type="button" class="btn btn-primary controls-bingo gear"><i class="fa-duotone fa-solid fa-gear"></i></button>
    </div>

    <div class="container-bingo">
        <div class="card-bingo-login shadow-lg">
            <div class="login-bingo">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center">
                            <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="LOGO" style="width: 150px;">
                            <div class="efecto-bingo"></div>
                            <h1 class="h4 text-gray-900 mb-2"><?= ($page['title'] ?? 'Inicio'); ?></h1>
                        </div>
        
                        <!-- Mostrar mensaje de error global -->
                        <div id="global-error" class="alert alert-danger d-none"></div>
                
                        <?php echo form_open(site_url() . 'loginSubmit', array('enctype' => 'multipart/form-data', 'class' => 'user', 'id' => 'data-form'));?>
                        
                            <?= csrf_field() ?>
                            
                            <div class="form-group">
                                <input type="text" class="form-control form-control-user input-bingo" name="username" id="username" placeholder="Usuario" autofocus autocomplete="off">
                                <small id="username-error" class="text-danger d-none pl-3"></small>
                            </div>
                            
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user input-bingo" name="password" id="password" placeholder="Contraseña" autocomplete="off">
                                <small id="password-error" class="text-danger d-none pl-3"></small>
                            </div>
                            
                            <div class="form-group hidden">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" value="1" checked>
                                    <label class="custom-control-label" for="remember">Recuérdame</label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-bingo" id="login-button">Ingresar</button>
                        <?= form_close(); ?>
                        
                        <hr />

                        <div class="text-center fs-5">
                            ¿Aún no tienes una cuenta? <br /> <a class="small fs-6" href="<?= site_url('register'); ?>">Crea una cuenta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?<?php echo md5(date("Hms")); ?>"></script>
            
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var preloader = document.querySelector('.preloader');
            var progressBar = document.querySelector('.progress');
            var loadingPercentage = document.querySelector('.loading-percentage');

            function updateProgress(event) {
                if (event.lengthComputable) {
                    var percentComplete = Math.round((event.loaded / event.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    loadingPercentage.textContent = percentComplete + '%';
                }
            }

            function removePreloader() {
                preloader.style.display = 'none';
            }

            window.addEventListener('load', function() {
                setTimeout(removePreloader, 500); // Retraso para mostrar el preloader brevemente al finalizar la carga
            });

            // Simular la carga para este ejemplo
            var fakeLoadingProgress = 0;
            var interval = setInterval(function() {
                fakeLoadingProgress += 10;
                progressBar.style.width = fakeLoadingProgress + '%';
                loadingPercentage.textContent = fakeLoadingProgress + '%';
                if (fakeLoadingProgress >= 100) {
                    clearInterval(interval);
                    removePreloader();
                }
            }, 100);
        });

        $(document).ready(function() {
            generateStars();

            function generateStars() {
                const modalBg = $('.efecto-bingo');
                let intervalId = setInterval(function () {
                    let moneda = $('<div class="moneda"></div>');
                    let randomX = Math.floor(Math.random() * window.innerWidth);
                    let randomY = Math.floor(Math.random() * window.innerHeight);
                    
                    // Definir un tamaño aleatorio entre 10px y 60px
                    let randomSize = Math.floor(Math.random() * 150) + 10;
                    
                    // Posicionar y dimensionar la estrella aleatoriamente
                    moneda.css({
                        top: randomY + 'px',
                        left: randomX + 'px',
                        width: randomSize + 'px',
                        height: randomSize + 'px',
                        transform: `translate(${randomX}px, ${randomY}px)`  // Control de la expansión
                    });

                    modalBg.append(moneda);

                    // Eliminar la estrella después de la animación
                    setTimeout(function() {
                        moneda.remove();
                    }, 20000);
                }, 20);  // Intervalo para generar estrellas

                // Limpiar las estrellas cuando el modal se cierra
                $('#login').on('hidden.bs.modal', function () {
                    clearInterval(intervalId);
                });

                // Limpiar las estrellas cuando el modal se cierra
                $('#jugar').on('hidden.bs.modal', function () {
                    clearInterval(intervalId);
                });
            };

            let soundtrack;  // Variable para el audio de fondo
            let audioStarted = false;  // Para evitar que el soundtrack se reproduzca más de una vez

            // Función para iniciar el soundtrack
            function startSoundtrack() {
                if (!audioStarted) {
                    soundtrack = new Audio('<?= site_url('assets/sounds/soundtrack.mp3'); ?>');
                    soundtrack.volume = 0.1;
                    soundtrack.loop = true;  // Hacer que el audio se repita
                    soundtrack.play().catch(error => {
                        console.log("Autoplay prevented. User interaction needed.");
                    });
                    audioStarted = true;
                }
            }

            // Función para activar/desactivar el soundtrack
            $('.volume').click(function() {
                if (soundtrack && !soundtrack.paused) {
                    soundtrack.pause();
                    $(this).html('<i class="fa-duotone fa-solid fa-volume-slash"></i>');
                } else {
                    if (!soundtrack) {
                        startSoundtrack();
                    } else {
                        soundtrack.play();
                    }
                    $(this).html('<i class="fa-duotone fa-solid fa-volume"></i>');
                }
            });

            // Reproduce el soundtrack automáticamente cuando se hace clic en la página
            function playSound() {
                startSoundtrack();
                document.removeEventListener('click', playSound);
                $('.volume').html('<i class="fa-duotone fa-solid fa-volume"></i>');
            }

            // Añadir el event listener para reproducir el soundtrack al hacer clic en la página
            document.addEventListener('click', playSound);
            
            $('#data-form').on('submit', function(e) {
                e.preventDefault(); // Evitar el envío tradicional del formulario
    
                $.ajax({
                    url: '<?= site_url('auth/loginSubmit') ?>',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Manejar la respuesta del servidor
                        if (response.success) {
                            window.location.href = response.redirect;
                        } else {
                            // Mostrar errores de validación
                            $('#global-error').text(response.error).removeClass('d-none');
                            if (response.errors) {
                                $.each(response.errors, function(field, message) {
                                    $('#' + field + '-error').text(message).removeClass('d-none');
                                    $('#' + field).addClass('is-invalid');
                                });
                            }
                        }
                    },
                    error: function() {
                        $('#global-error').text('Hubo un error en la solicitud.').removeClass('d-none');
                    },
                    complete: function() {
                        button.prop("disabled", false); // Habilitar el botón después de la solicitud
                    }
                });
            });
        });
    </script>
</body>
</html>