<div class="modal-dialog modal-dialog-centered max-w-40">
    <div class="modal-content">
        <div class="modal-header pb-2">
            <h6 class="modal-title ps-2"><i class="fa-duotone fa-solid fa-wallet"></i> <?= translate('bank account'); ?></h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body pt-0">
            <?php echo form_open(site_url() . 'payments/settingswalletSubmit', array('enctype' => 'multipart/form-data', 'id' => 'settingswallet-form'));?>
                
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-12 mb-1">
                        <label for="setting-bank" class="form-label"><?= translate('bank'); ?></label>
                        <select class='form-control form-control-lg form-bingo' name="setting-bank" id="setting-bank">
                            <option value=""><?= translate('select a'); ?> <?= strtolower(translate('bank')); ?></option>
                            <option <?= $user['bank'] == '0102 - BANCO DE VENEZUELA' ? 'selected' : '' ?> value="0102 - BANCO DE VENEZUELA">0102 - BANCO DE VENEZUELA</option>
                            <option <?= $user['bank'] == '0104 - VENEZOLANO DE CRÉDITO' ? 'selected' : '' ?> value="0104 - VENEZOLANO DE CRÉDITO">0104 - VENEZOLANO DE CRÉDITO</option>
                            <option <?= $user['bank'] == '0105 - BANCO MERCANTIL' ? 'selected' : '' ?> value="0105 - BANCO MERCANTIL">0105 - BANCO MERCANTIL</option>
                            <option <?= $user['bank'] == '0108 - BANCO PROVINCIAL' ? 'selected' : '' ?> value="0108 - BANCO PROVINCIAL">0108 - BANCO PROVINCIAL</option>
                            <option <?= $user['bank'] == '0114 - BANCO DEL CARIBE' ? 'selected' : '' ?> value="0114 - BANCO DEL CARIBE">0114 - BANCO DEL CARIBE</option>
                            <option <?= $user['bank'] == '0115 - BANCO EXTERIOR' ? 'selected' : '' ?> value="0115 - BANCO EXTERIOR">0115 - BANCO EXTERIOR</option>
                            <option <?= $user['bank'] == '0116 - BANCO OCCIDENTAL DE DESCUENTO' ? 'selected' : '' ?> value="0116 - BANCO OCCIDENTAL DE DESCUENTO">0116 - BANCO OCCIDENTAL DE DESCUENTO</option>
                            <option <?= $user['bank'] == '0128 - BANCO CARONI' ? 'selected' : '' ?> value="0128 - BANCO CARONI">0128 - BANCO CARONI</option>
                            <option <?= $user['bank'] == '0134 - BANESCO' ? 'selected' : '' ?> value="0134 - BANESCO">0134 - BANESCO</option>
                            <option <?= $user['bank'] == '0137 - BANCO SOFITASA' ? 'selected' : '' ?> value="0137 - BANCO SOFITASA">0137 - BANCO SOFITASA</option>
                            <option <?= $user['bank'] == '0138 - BANCO PLAZA' ? 'selected' : '' ?> value="0138 - BANCO PLAZA">0138 - BANCO PLAZA</option>
                            <option <?= $user['bank'] == '0146 - BANCO DE LA GENTE EMPRENDEDORA BANGENTE' ? 'selected' : '' ?> value="0146 - BANCO DE LA GENTE EMPRENDEDORA BANGENTE">0146 - BANCO DE LA GENTE EMPRENDEDORA BANGENTE</option>
                            <option <?= $user['bank'] == '0149 - BANCO DEL PUEBLO SOBERANO' ? 'selected' : '' ?> value="0149 - BANCO DEL PUEBLO SOBERANO">0149 - BANCO DEL PUEBLO SOBERANO</option>
                            <option <?= $user['bank'] == '0151 - BFC BANCO FONDO COMUN C.A.' ? 'selected' : '' ?> value="0151 - BFC BANCO FONDO COMUN C.A.">0151 - BFC BANCO FONDO COMUN C.A.</option>
                            <option <?= $user['bank'] == '0156 - 100%BANCO' ? 'selected' : '' ?> value="0156 - 100%BANCO">0156 - 100%BANCO</option>
                            <option <?= $user['bank'] == '0157 - DELSUR' ? 'selected' : '' ?> value="0157 - DELSUR">0157 - DELSUR</option>
                            <option <?= $user['bank'] == '0163 - BANCO DEL TESORO' ? 'selected' : '' ?> value="0163 - BANCO DEL TESORO">0163 - BANCO DEL TESORO</option>
                            <option <?= $user['bank'] == '0164 - BANCO DE DESARROLLO DEL MICROEMPRESARIO' ? 'selected' : '' ?> value="0164 - BANCO DE DESARROLLO DEL MICROEMPRESARIO">0164 - BANCO DE DESARROLLO DEL MICROEMPRESARIO</option>
                            <option <?= $user['bank'] == '0166 - BANCO AGRICOLA DE VENEZUELA' ? 'selected' : '' ?> value="0166 - BANCO AGRICOLA DE VENEZUELA">0166 - BANCO AGRICOLA DE VENEZUELA</option>
                            <option <?= $user['bank'] == '0168 - BANCRECER' ? 'selected' : '' ?> value="0168 - BANCRECER">0168 - BANCRECER</option>
                            <option <?= $user['bank'] == '0169 - R4 BANCO MICROFINANCIERO' ? 'selected' : '' ?> value="0169 - R4 BANCO MICROFINANCIERO">0169 - R4 BANCO MICROFINANCIERO</option>
                            <option <?= $user['bank'] == '0171 - BANCO ACTIVO' ? 'selected' : '' ?> value="0171 - BANCO ACTIVO">0171 - BANCO ACTIVO</option>
                            <option <?= $user['bank'] == '0172 - BANCAMIGA' ? 'selected' : '' ?> value="0172 - BANCAMIGA">0172 - BANCAMIGA</option>
                            <option <?= $user['bank'] == '0173 - BANCO INTERNACIONAL DE DESARROLLO' ? 'selected' : '' ?> value="0173 - BANCO INTERNACIONAL DE DESARROLLO">0173 - BANCO INTERNACIONAL DE DESARROLLO</option>
                            <option <?= $user['bank'] == '0174 - BANPLUS' ? 'selected' : '' ?> value="0174 - BANPLUS">0174 - BANPLUS</option>
                            <option <?= $user['bank'] == '0175 - BANCO DIGITAL DE LOS TRABAJADORES' ? 'selected' : '' ?> value="0175 - BANCO DIGITAL DE LOS TRABAJADORES">0175 - BANCO DIGITAL DE LOS TRABAJADORES</option>
                            <option <?= $user['bank'] == '0176 - NOVO BANCO' ? 'selected' : '' ?> value="0176 - NOVO BANCO">0176 - NOVO BANCO</option>
                            <option <?= $user['bank'] == '0177 - BANCO DE LA FUERZA ARMADA NACIONAL BOLIVARIANA' ? 'selected' : '' ?> value="0177 - BANCO DE LA FUERZA ARMADA NACIONAL BOLIVARIANA">0177 - BANCO DE LA FUERZA ARMADA NACIONAL BOLIVARIANA</option>
                            <option <?= $user['bank'] == '0190 - CITIBANK N.A.' ? 'selected' : '' ?> value="0190 - CITIBANK N.A.">0190 - CITIBANK N.A.</option>
                            <option <?= $user['bank'] == '0191 - BANCO NACIONAL CRÉDITO' ? 'selected' : '' ?> value="0191 - BANCO NACIONAL CRÉDITO">0191 - BANCO NACIONAL CRÉDITO</option>
                        </select>
                        <small id="setting-bank-error" class="text-danger d-none"></small>
                    </div>

                    <div class="col-md-12 mb-1">
                        <label for="setting-account" class="form-label"><?= translate('naccount'); ?></label>
                        <input type="number" class="form-control form-control-lg form-bingo" name="setting-account" id="setting-account" placeholder="<?= translate('enter a'); ?> <?= strtolower(translate('naccount')); ?>" autocomplete="off" value="<?= $user['account']; ?>">
                        <small id="setting-account-error" class="text-danger d-none"></small>
                    </div>
                    
                    <div class="col-md-6 mb-1">
                        <label for="setting-document" class="form-label"><?= translate('document'); ?></label>
                        <input type="text" class="form-control form-control-lg form-bingo" name="setting-document" id="setting-document" placeholder="<?= translate('enter a'); ?> <?= strtolower(translate('document')); ?>" autocomplete="off" value="<?= $user['document']; ?>">
                        <small id="setting-document-error" class="text-danger d-none"></small>
                    </div>
                    
                    <div class="col-md-6 mb-1">
                        <label for="setting-phone" class="form-label"><?= translate('phone'); ?></label>
                        <input type="number" class="form-control form-control-lg form-bingo" name="setting-phone" id="setting-phone" placeholder="<?= translate('enter a'); ?> <?= strtolower(translate('phone')); ?>" autocomplete="off" value="<?= $user['phone']; ?>">
                        <small id="setting-phone-error" class="text-danger d-none"></small>
                    </div>

                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary d-block w-50 btn-bingo mt-3" id="settingswallet-button"><?= translate('update'); ?></button>
                    </div>
                </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#settingswallet-form').on('submit', function (e) {
            e.preventDefault(); 

            var button = $('#settingswallet-button');
            button.prop("disabled", true);

            $('.text-danger').addClass('d-none').text('');
            $('.form-control').removeClass('is-invalid');

            $.ajax({
                url: '<?= site_url('payments/settingswalletSubmit') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        
                        $('#modalSettings').modal('hide');

                        Toastify({
                            text: "<?= translate('account updated successfully'); ?>",
                            duration: 3000,
                            gravity: "top",
                            position: "right",
                            style: { background: "#198754" },
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
</script>