$(document).ready(function() {
    $('#play-form').on('submit', function(e) {
        e.preventDefault();

        var button = $('#play-button');
        button.prop("disabled", true);

        $('.text-danger').addClass('d-none').text('');
        $('.form-control').removeClass('is-invalid');

        $.ajax({
            url: site_url + 'playings/playSubmit',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else if (response.play) {
                    Toastify({
                        text: __['the game is initiated'],
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
                        text: __['the game is finished'],
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" },
                        stopOnFocus: true
                    }).showToast();
                } else if (response.initiated) {
                    Toastify({
                        text: __['the game is initiated'],
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" },
                        stopOnFocus: true
                    }).showToast();
                } else if (response.payments) {
                    Toastify({
                        text: 'Para jugar los cartones de su premio recargue al menos ' + response.amount + 'Bs. a su billetera.',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: { background: "#dc3545" },
                        stopOnFocus: true
                    }).showToast();
                } else if (response.time) {
                    Toastify({
                        text: 'Debe ingresar a la apartida 10min antes de iniciar.',
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
            complete: function() {
                button.prop("disabled", false);
            }
        });
    });
});

function RemoveVolume() {
    $.ajax({
        url: site_url + 'playings/volumeSubmit',
        method: 'POST',
        success: function(data) {
            if (data.status === 'success') {
                console.log("sound disabled successfully");
            } else {
                console.log("error sending request");
            }
        }
    });
}