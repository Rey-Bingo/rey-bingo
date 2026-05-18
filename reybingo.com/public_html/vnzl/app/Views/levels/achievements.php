<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('levels') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Mis Logros</h4>
            </div>
        </div>
    </div>

    <!-- Achievement Stats Section -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-lighten rounded-circle text-primary">
                                <i class="mdi mdi-trophy font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $achievementStats['completed'] ?> / <?= $achievementStats['total'] ?></h3>
                            <p class="text-muted mb-1">Logros Completados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-lighten rounded-circle text-success">
                                <i class="mdi mdi-percent font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $achievementStats['percentage'] ?>%</h3>
                            <p class="text-muted mb-1">Porcentaje Completado</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info-lighten rounded-circle text-info">
                                <i class="mdi mdi-star font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= number_format($achievementStats['points']) ?></h3>
                            <p class="text-muted mb-1">Puntos Ganados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning-lighten rounded-circle text-warning">
                                <i class="mdi mdi-lock-open font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $achievementStats['incomplete'] ?></h3>
                            <p class="text-muted mb-1">Logros Pendientes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Achievements Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Logros Completados</h4>
                    <p class="text-muted">Logros que has desbloqueado hasta ahora.</p>

                    <?php if (empty($completedAchievements)) : ?>
                        <div class="alert alert-info">
                            Aún no has completado ningún logro. ¡Sigue jugando para desbloquearlos!
                        </div>
                    <?php else : ?>
                        <div class="row">
                            <?php foreach ($completedAchievements as $achievement) : ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-sm flex-shrink-0 me-3">
                                                    <img src="<?= base_url('uploads/achievements/' . $achievement['icon']) ?>" alt="<?= $achievement['name'] ?>" class="img-thumbnail rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mt-0 mb-1"><?= $achievement['name'] ?></h5>
                                                    <p class="mb-0 text-muted">
                                                        <i class="mdi mdi-calendar"></i> <?= date('d/m/Y', strtotime($achievement['completed_at'])) ?>
                                                    </p>
                                                </div>
                                                <div class="badge bg-success">+<?= number_format($achievement['points']) ?></div>
                                            </div>
                                            <p><?= $achievement['description'] ?></p>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-end mt-1">
                                                <small class="text-muted">Completado</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Incomplete Achievements Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Logros Pendientes</h4>
                    <p class="text-muted">Logros que aún puedes desbloquear.</p>

                    <?php if (empty($incompleteAchievements)) : ?>
                        <div class="alert alert-success">
                            ¡Felicidades! Has completado todos los logros disponibles.
                        </div>
                    <?php else : ?>
                        <div class="row">
                            <?php foreach ($incompleteAchievements as $achievement) : ?>
                                <?php 
                                $progressPercentage = min(100, round(($achievement['progress'] / $achievement['requirement_value']) * 100));
                                ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar-sm flex-shrink-0 me-3">
                                                    <img src="<?= base_url('uploads/achievements/' . $achievement['icon']) ?>" alt="<?= $achievement['name'] ?>" class="img-thumbnail rounded-circle" style="filter: grayscale(100%);">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="mt-0 mb-1"><?= $achievement['name'] ?></h5>
                                                    <p class="mb-0 text-muted">
                                                        <i class="mdi mdi-star"></i> <?= number_format($achievement['points']) ?> puntos
                                                    </p>
                                                </div>
                                                <div class="badge bg-secondary"><?= $progressPercentage ?>%</div>
                                            </div>
                                            <p><?= $achievement['description'] ?></p>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $progressPercentage ?>%;" aria-valuenow="<?= $progressPercentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">
                                                    <?php
                                                    $requirementLabels = [
                                                        'games_played' => 'Partidas jugadas',
                                                        'wins' => 'Victorias',
                                                        'consecutive_days' => 'Días consecutivos',
                                                        'cartons_bought' => 'Cartones comprados',
                                                        'referrals' => 'Referidos'
                                                    ];
                                                    echo $requirementLabels[$achievement['requirement_type']] ?? $achievement['requirement_type'];
                                                    ?>
                                                </small>
                                                <small><?= $achievement['progress'] ?> / <?= $achievement['requirement_value'] ?></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Achievement Categories Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Categorías de Logros</h4>
                    <p class="text-muted">Explora los diferentes tipos de logros que puedes desbloquear.</p>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-primary-lighten rounded-circle text-primary">
                                                <i class="mdi mdi-gamepad-variant font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Partidas</h5>
                                        </div>
                                    </div>
                                    <p>Logros relacionados con la participación en partidas de bingo.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-success-lighten rounded-circle text-success">
                                                <i class="mdi mdi-trophy font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Victorias</h5>
                                        </div>
                                    </div>
                                    <p>Logros relacionados con ganar partidas y premios.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-info-lighten rounded-circle text-info">
                                                <i class="mdi mdi-calendar-check font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Constancia</h5>
                                        </div>
                                    </div>
                                    <p>Logros relacionados con la actividad diaria y constante.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-warning-lighten rounded-circle text-warning">
                                                <i class="mdi mdi-cards font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Colección</h5>
                                        </div>
                                    </div>
                                    <p>Logros relacionados con la compra y colección de cartones.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-danger-lighten rounded-circle text-danger">
                                                <i class="mdi mdi-account-group font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Social</h5>
                                        </div>
                                    </div>
                                    <p>Logros relacionados con invitar amigos y actividades sociales.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-title bg-purple-lighten rounded-circle text-purple">
                                                <i class="mdi mdi-crown font-24"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mt-0 mb-0">Especiales</h5>
                                        </div>
                                    </div>
                                    <p>Logros especiales y eventos limitados.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>