<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Paquetes Pro</h4>
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

    <!-- Active Package Section -->
    <?php if (!empty($activePackage)) : ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Tu Paquete Pro Activo</h4>
                        <div class="alert alert-success">
                            <h5><i class="mdi mdi-crown"></i> <?= $activePackage['name'] ?></h5>
                            <p><?= $activePackage['description'] ?></p>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Beneficios:</strong> <?= $activePackage['benefits'] ?></p>
                                    <p><strong>Descuento en cartones:</strong> <?= $activePackage['discount_percentage'] ?>%</p>
                                    <p><strong>Cartones gratis:</strong> <?= $activePackage['free_cartons'] ?></p>
                                    <p><strong>Puntos diarios:</strong> <?= $activePackage['daily_points'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha de inicio:</strong> <?= date('d/m/Y H:i', strtotime($activePackage['start_date'])) ?></p>
                                    <p><strong>Fecha de vencimiento:</strong> <?= date('d/m/Y H:i', strtotime($activePackage['end_date'])) ?></p>
                                    <p><strong>Días restantes:</strong> <?= ceil((strtotime($activePackage['end_date']) - time()) / (60 * 60 * 24)) ?></p>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('packages/benefits') ?>" class="btn btn-primary">Ver Beneficios</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Available Packages Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Paquetes Disponibles</h4>
                    <p class="text-muted">Mejora tu experiencia con nuestros paquetes Pro y disfruta de beneficios exclusivos.</p>

                    <div class="row">
                        <?php foreach ($packages as $package) : ?>
                            <div class="col-md-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0"><?= $package['name'] ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="text-center mb-3">Bs <?= number_format($package['price'], 2) ?></h3>
                                        <p class="text-muted"><?= $package['description'] ?></p>
                                        <hr>
                                        <ul class="list-unstyled">
                                            <li><i class="mdi mdi-check-circle text-success"></i> Duración: <?= $package['duration_days'] ?> días</li>
                                            <li><i class="mdi mdi-check-circle text-success"></i> Descuento en cartones: <?= $package['discount_percentage'] ?>%</li>
                                            <li><i class="mdi mdi-check-circle text-success"></i> Cartones gratis: <?= $package['free_cartons'] ?></li>
                                            <li><i class="mdi mdi-check-circle text-success"></i> Puntos diarios: <?= $package['daily_points'] ?></li>
                                        </ul>
                                        <div class="text-center mt-3">
                                            <a href="<?= base_url('packages/view/' . $package['id']) ?>" class="btn btn-outline-primary me-2">Detalles</a>
                                            <a href="<?= base_url('packages/purchase/' . $package['id']) ?>" class="btn btn-primary">Comprar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase History Section -->
    <?php if (!empty($packageHistory)) : ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Historial de Compras</h4>
                        <div class="table-responsive">
                            <table class="table table-centered table-striped">
                                <thead>
                                    <tr>
                                        <th>Paquete</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packageHistory as $history) : ?>
                                        <tr>
                                            <td><?= $history['name'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($history['start_date'])) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($history['end_date'])) ?></td>
                                            <td>
                                                <?php if ($history['is_active'] && strtotime($history['end_date']) > time()) : ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary">Expirado</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>