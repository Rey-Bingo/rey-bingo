<div class="modal-dialog modal-dialog-centered max-w-40">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title ps-2 mt-2"><i class="fa-duotone fa-solid fa-users-line"></i> <?= translate('online players'); ?></h6>
            <button class="btn-close me-1" type="button" aria-label="close" data-bs-dismiss="modal"><i class="fa-duotone fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body pt-0 text-center">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?= translate('name'); ?></th>
                            <th><?= translate('no. of cartons'); ?></th>
                            <th><?= translate('no. of bingos'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)) : ?>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= esc($user['user_name']) ?></td>
                                    <td><?= esc($user['cartons_count']) ?></td>
                                    <td><?= esc($user['bingo_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="text-center"><?= translate('no data available'); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>