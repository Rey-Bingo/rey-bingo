<div class="modal-dialog modal-dialog-centered max-w-40">
    <div class="modal-content">
        <div class="modal-header pb-2">
            <h6 class="modal-title ps-2"><i class="fa-duotone fa-hand-holding-dollar"></i> <?= translate('payment to wallet'); ?></h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body pt-0">
            <div class="row">
                <div class="col-md-12 mb-1">
                    <div class="card shadow-sm p-2 d-flex flex-row align-items-center" style="border-radius: 10px;">
                        <div style="flex: 0 0 80px; text-align:center;">
                            <i class="fa-duotone fa-hand-holding-dollar fs-1"></i>
                        </div>
                        <div style="flex: 1; padding-left: 5px;">
                            <h6 class="mb-1"><strong><?= translate('payment details'); ?>:</strong> #<?= esc(str_pad($payment['id'], 4, '0', STR_PAD_LEFT)) ?></h6>
                            <h6 class="mb-1"><strong><?= translate('for'); ?>:</strong> <?= esc($user['code']) ?> - <?= esc($user['firstname']) ?> <?= esc($user['lastname']) ?></h6>
                            <small class="mb-0"><strong><?= translate('amount'); ?>:</strong> <?= systemGet('currency'); ?> <?= esc($payment['amount']) ?> - <strong><?= translate('date'); ?>:</strong> <?= esc(date('d/m/Y h:i A', strtotime($payment['created_at']))) ?></small> <br />
                            <small class="mb-0"><strong><?= translate('status'); ?>:</strong> <?= $status ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>