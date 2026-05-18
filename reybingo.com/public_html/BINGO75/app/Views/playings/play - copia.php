<style>
    .container-bingo {
        width: 100%;
        height: 100%;
        display: grid;
        grid-template-rows: 100%;
        place-items: center;
    }

    .controls-bingo.logout {
        box-shadow: 6px 6px 15px rgba(24, 28, 50, .2), -6px -6px 15px rgba(19, 22, 40, 0.3);
        background: linear-gradient(145deg, #181C32, #131628);
        top: 10px !important;
        right: 10px;
    }

    .controls-bingo.wallet {
        left: 65px;
    }
</style>

<link href="<?= site_url('assets/plugin/components/font-awesome/css/fontawesome.min.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">
<link href="<?= site_url('assets/plugin/czm-chat-support.css'); ?>?<?= md5(date("Hms")); ?>" rel="stylesheet">

<div class="controls-container">
    <button type="button" class="btn btn-primary controls-bingo music">
        <i class="fa-duotone fa-solid fa-tv-music"></i>
    </button>

    <button class="btn btn-primary controls-bingo logout" onclick="GoToPage('logout');">
        <i class="fa-duotone fa-solid fa-arrow-right-from-arc"></i>
    </button>

    <button type="button" class="btn btn-primary controls-bingo volume" onclick="RemoveVolume();">
        <i class="fa-duotone fa-solid fa-volume-slash"></i>
    </button>
</div>

<button type="button" class="btn btn-primary controls-bingo user">
    <div class="profile">
        <a class="small fs-6 linkPage" href="<?= site_url('profile'); ?>"><img src="<?= $imagePath ?>" alt="img"></a>
    </div>
</button>

<button type="button" class="btn btn-primary controls-bingo wallet" onclick="paymentsGet();">
    <i class="fa-duotone fa-solid fa-wallet"></i>
</button>

<button type="button" class="btn btn-primary controls-bingo game-board" onclick="gamesGet();">
    <i class="fa-duotone fa-solid fa-gamepad"></i>
</button>

<div class="card-bingo-dashboard mw-400px shadow-lg text-center">
    <div class="dashboard-bingo">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <img src="<?= site_url('assets/img/logo-bingo.gif'); ?>" class="img-fluid" alt="img" style="width: 250px;">
                    <h1 class="h5 text-gray-900 mb-2"><?= translate('hello'); ?>, <?= strtok(session()->get('name'), ' '); ?>!</h1>
                </div>

                <!-- Mostrar mensaje de error global -->
                <div id="global-error" class="alert alert-danger d-none"></div>
        
                <?php echo form_open(site_url() . 'playings/playSubmit', array('enctype' => 'multipart/form-data', 'class' => 'user', 'id' => 'game-form'));?>
                
                    <?= csrf_field() ?>

                    <div class="col-md-12">
                        <h1 class="h6 hidden-xs" style="padding-top: 10px; margin-bottom: 0px;"><?= translate('game'); ?></h1>
                        <div class="form-group">
                            <select class='form-control form-control-user input-bingo' name="game" id="game">
                                <?php if (!empty($games)) : ?>
                                    <option value=""><?= translate('select a game'); ?></option>
                                    <?php foreach ($games as $game): ?>
                                        <option value="<?= $game['id'] ?>"><?= $game['description'] ?> - <?= systemGet('currency'); ?> <?= $game['price'] ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option value=""><?= translate('there are no active games'); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <small id="game-error" class="text-danger d-none"></small>
                    </div>
                    
                    <div class="col-md-12">
                        <h1 class="h6 hidden-xs" style="padding-top: 10px; margin-bottom: 0px;"><?= translate('no. of cartons'); ?></h1>
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" id="decrease-button" class="btn btn-primary btn-minus"><i class="fa-duotone fa-solid fa-minus"></i></button>
                                    <input type="number" class="form-control form-control-user input-bingo" name="cartons" id="cartons" value="1" placeholder="<?= translate('enter a'); ?> <?= strtolower(translate('no. of cartons')); ?>" autofocus autocomplete="off">
                                <button type="button" id="increase-button" class="btn btn-primary btn-plus"><i class="fa-duotone fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                        <small id="cartons-error" class="text-danger d-none"></small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-bingo mt-2" id="game-button"><?= translate('play'); ?></button>
                <?= form_close(); ?>
                
                <hr />

                <div class="text-center fs-7">
                    <?= translate('my wallet'); ?> <?= systemGet('currency'); ?> <?= $user['wallet']; ?> <br /> <a class="small fs-7" href="javascript:void(0);" onclick="rechargeGet();"><?= translate('deposit'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="payments" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div class="modal fade" id="games" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div class="modal fade" id="recharge" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div class="modal fade" id="retire" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div class="modal fade" id="settingswallet" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div class="modal fade" id="details" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"></div></div>

<div id="whatsapp-plugin"></div>

<script src="<?= site_url('assets/plugin/components/moment/moment.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>
<script src="<?= site_url('assets/plugin/components/moment/moment-timezone-with-data.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>
<script src="<?= site_url('assets/plugin/czm-chat-support.min.js'); ?>?<?= md5(date("Hms")); ?>"></script>

<script type="text/javascript">
    function gamesGet() {
        $("#games").load('<?= site_url('games/gamesGet') ?>');
        $('#games').modal('show');
    }

    function paymentsGet() {
        $("#payments").load('<?= site_url('payments/paymentsGet') ?>');
        $('#payments').modal('show');
    }

    function rechargeGet() {
        $("#recharge").load('<?= site_url('payments/rechargeGet') ?>');
        $('#recharge').modal('show');
    }

    function retireGet() {
        $("#retire").load('<?= site_url('payments/retireGet') ?>');
        $('#retire').modal('show');
    }

    function settingswalletGet() {
        $("#settingswallet").load('<?= site_url('payments/settingswalletGet') ?>');
        $('#settingswallet').modal('show');
    }

    function paydetailsGet(type, id) {
        $("#details").load('<?= site_url('payments/paydetailsGet') ?>');
        $('#details').modal('show');
        $('#detail-text').html(type);
    }

    function GoToPage(page) {
        $.ajax({
            url: '<?= site_url(); ?>' + page,
            success: function (data) {
                if (page != 'logout') {
                    $('#content-page').html(data);
                } else {
                    window.location.href = page;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const musicButton = document.querySelector('.controls-bingo.music');
        const controlsContainer = document.querySelector('.controls-container');

        musicButton.addEventListener('click', function() {
            controlsContainer.classList.toggle('active');
        });
    });

    $(document).ready(function() {
        $('#game-form').on('submit', function(e) {
            e.preventDefault();
    
            var button = $('#game-button');
            button.prop("disabled", true);
    
            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');
    
            $.ajax({
                url: '<?= site_url('playings/playSubmit') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect;
                    } else if (response.play) {
                        Toastify({
                            text: "<?= translate('the game is initiated'); ?>",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            style: { background: "#dc3545" },
                            stopOnFocus: true
                        }).showToast();

                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 500);
                    } else if (response.finished) {
                        Toastify({
                            text: "<?= translate('the game is finished'); ?>",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            style: { background: "#dc3545" },
                            stopOnFocus: true
                        }).showToast();
                    } else if (response.initiated) {
                        Toastify({
                            text: "<?= translate('the game is initiated'); ?>",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            style: { background: "#dc3545" },
                            stopOnFocus: true
                        }).showToast();
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
        const maxCartons = 20;

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

    function RemoveVolume() {
        $.ajax({
            url: '<?= site_url('playings/volumeSubmit') ?>',
            method: 'POST',
            success: function(data) {
                if (data.status === 'success') {
                    console.log("Sonido desactivado con éxito.");
                } else {
                    console.log("Error al enviar la solicitud.");
                }
            },
            error: function(error) {
                console.log("Error en la solicitud.");
            }
        });
    }

    $('#whatsapp-plugin').czmChatSupport({
        /* Button Settings */
        button: {
            position: "right", /* left, right or false. "position:false" does not pin to the left or right */
            style: 1, /* Button style. Number between 1 and 7 */
            src: '<i class="fab fa-whatsapp"></i>', /* Image, Icon or SVG */
            backgroundColor: "#10c379", /* Html color code */
            effect: 1, /* Button effect. Number between 1 and 7 */
            notificationNumber: "1", /* Custom text or false. To remove, (notificationNumber:false) */
            speechBubble: "<?= translate('how can we help you?'); ?>", /* To remove, (speechBubble:false) */
            pulseEffect: true, /* To remove, (pulseEffect:false) */
            text: { /* For Button style larger than 1 */
                title: "<?= translate('do you need help? talk to us'); ?>", /* Writing is required */
                description: "<?= translate('customer service'); ?>", /* To remove, (description:false) */
                online: "<?= translate('online'); ?>", /* To remove, (online:false) */
                offline: "<?= translate('offline'); ?>" /* To remove, (offline:false) */
            }
        },
    
        /* Popup Settings */
        popup: {
            automaticOpen: false, /* true or false (Open popup automatically when the page is loaded) */
            outsideClickClosePopup: true, /* true or false (Clicking anywhere on the page will close the popup) */
            effect: 1, /* Popup opening effect. Number between 1 and 15 */
            header: {
                backgroundColor: "#10c379", /* Html color code */
                title: "<?= translate('do you need help? talk to us'); ?>", /* Writing is required */
                description: "<?= translate('one of our representatives will assist you'); ?>" /* To remove, (description:false) */
            },
    
            /* Representative Settings */
            persons: [

                <?php foreach ($contacts as $contact): ?>
                {
                    avatar: {
                        src: '<img src="<?= site_url('assets/img/person/' . $contact['id'] . '.svg'); ?>" alt="img">', /* Image, Icon or SVG */
                        backgroundColor: "#ffffff", /* Html color code */
                        onlineCircle: true /* Avatar online circle. To remove, (onlineCircle:false) */
                    },
                    text: {
                        title: "<?= $contact['name'] ?>", /* Writing is required */
                        description: "<?= $contact['charge'] ?>", /* To remove, (description:false) */
                        online: "<?= translate('online'); ?>", /* To remove, (online:false) */
                        offline: "<?= translate('offline'); ?>", /* To remove, (offline:false) */
                        button: "<?= translate('start chat'); ?>"
                    },
                    link: {
                        desktop: "https://web.whatsapp.com/send?phone=<?= $contact['phone'] ?>&text=<?= translate('hello'); ?> <?= $contact['name'] ?>, <?= translate('i need more information'); ?>.", /* Writing is required */
                        mobile: "https://wa.me/<?= $contact['phone'] ?>/?text=<?= translate('hello'); ?> <?= $contact['name'] ?>, <?= translate('i need more information'); ?>." /* If it is hidden desktop link will be valid. To remove, (mobile:false) */
                    },
                    onlineDay: {
                        /* Change the day you are offline like this. (sunday:false) */
                        sunday: "00:00-23:59",
                        monday: "00:00-23:59",
                        tuesday: "00:00-23:59",
                        wednesday: "00:00-23:59",
                        thursday: "00:00-23:59",
                        friday: "00:00-23:59",
                        saturday: "00:00-23:59"
                    }
                },
                <?php endforeach; ?>

            ],
        },
    
        /* Other Settings */
        sound: true, /* true (default sound), false or custom sound. Custom sound example, (sound:'assets/sound/notification.mp3') */
        changeBrowserTitle: false, /* Custom text or false. To remove, (changeBrowserTitle:false) */
        cookie: false, /* It does not show the speech bubble, notification number, and pulse effect again for the specified time. For example, do not show for 1 hour, (cookie:1) or to remove, (cookie:false) */
    });
</script>