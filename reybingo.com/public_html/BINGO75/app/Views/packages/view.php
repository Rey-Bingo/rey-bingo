<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('packages') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Detalles del Paquete</h4>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0"><?= $package['name'] ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Descripción</h5>
                            <p><?= $package['description'] ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Beneficios</h5>
                            <p><?= $package['benefits'] ?></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Duración</h5>
                                    <h3><?= $package['duration_days'] ?> días</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Descuento</h5>
                                    <h3><?= $package['discount_percentage'] ?>%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Cartones Gratis</h5>
                                    <h3><?= $package['free_cartons'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Puntos Diarios</h5>
                                    <h3><?= $package['daily_points'] ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>¿Qué incluye este paquete?</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    Acceso a partidas exclusivas para usuarios Pro
                                </li>
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    <?= $package['discount_percentage'] ?>% de descuento en la compra de cartones
                                </li>
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    <?= $package['free_cartons'] ?> cartones gratis durante la suscripción
                                </li>
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    <?= $package['daily_points'] ?> puntos diarios por iniciar sesión
                                </li>
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    Insignia Pro en tu perfil
                                </li>
                                <li class="list-group-item">
                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                    Prioridad en el soporte al cliente
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Resumen</h4>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Precio:</span>
                        <span class="fw-bold">Bs <?= number_format($package['price'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Duración:</span>
                        <span class="fw-bold"><?= $package['duration_days'] ?> días</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Descuento en cartones:</span>
                        <span class="fw-bold"><?= $package['discount_percentage'] ?>%</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Cartones gratis:</span>
                        <span class="fw-bold"><?= $package['free_cartons'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Puntos diarios:</span>
                        <span class="fw-bold"><?= $package['daily_points'] ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold text-primary">Bs <?= number_format($package['price'], 2) ?></span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('packages/purchase/' . $package['id']) ?>" class="btn btn-primary">
                            <i class="mdi mdi-cart-plus me-1"></i> Comprar Ahora
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h4 class="header-title mb-3">¿Necesitas ayuda?</h4>
                    <p>Si tienes alguna pregunta sobre este paquete o necesitas asistencia, no dudes en contactarnos.</p>
                    <a href="javascript:void(0);" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-help-circle me-1"></i> Preguntas Frecuentes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>