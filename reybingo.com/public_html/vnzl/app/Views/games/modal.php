<div class="modal-dialog games modal-dialog-centered max-w-70">
    <div class="modal-content">
        <div class="modal-header pb-2">
            <h6 class="modal-title ps-2">
                <i class="fa-duotone fa-solid fa-gamepad"></i> 
                <?= translate('list of'); ?> <?= translate('games'); ?>
            </h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body pt-0">
            <div class="card mb-3">
                <div class="collapse show" id="filtersCollapse">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <?php $today = date('Y-m-d'); ?>
                            <div class="col-md-4 mb-1">
                                <label for="datefilter" class="form-label"><?= translate('date'); ?></label>
                                <select class='form-control form-control-lg form-bingo' name="datefilter" id="datefilter" onchange="gameslistGet();">
                                    <option value="all"><?= translate('all dates') ?></option>
                                    <?php if (!empty($dates)) : ?>
                                        <?php foreach ($dates as $date): ?>
                                            <option value="<?= esc($date) ?>" <?= ($date === $today) ? 'selected' : '' ?>>
                                                <?= esc(translate_date($date)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="0"><?= translate('there are no active games'); ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-1">
                                <label for="roomfilter" class="form-label"><?= translate('room'); ?></label>
                                <select class='form-control form-control-lg form-bingo' name="roomfilter" id="roomfilter">
                                    <option value="all"><?= translate('all rooms') ?></option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?= esc($room['id']) ?>"><?= esc($room['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4 mb-1">
                                <label for="statusfilter" class="form-label"><?= translate('status'); ?></label>
                                <select class='form-control form-control-lg form-bingo' name="statusfilter" id="statusfilter">
                                    <?php foreach ($status as $key => $statu): ?>
                                        <option value="<?= esc($key) ?>"><?= esc($statu) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive" id="games-list"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        gameslistGet();
    });

    $('#datefilter, #roomfilter, #statusfilter').on('change', function() {
        gameslistGet();
    });

    function gameslistGet() {
        var date = $('#datefilter').val();
        var room = $('#roomfilter').val();
        var status = $('#statusfilter').val();

        $.ajax({
            url: '<?= site_url('games/gameslistGet') ?>/' + date + '/' + room + '/' + status,
            type: "GET",
            success: function(data) {  
                $("#games-list").html(data);
            },
            error: function () {
                Toastify({
                    text: '<?= translate('there was an error in the request to the server.'); ?>',
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: { background: "#dc3545" },
                    stopOnFocus: true
                }).showToast();

                $("#games-list").html('');
            }
        });
    }
</script>
