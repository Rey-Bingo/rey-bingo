<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('packages/manage') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Suscriptores del Paquete</h4>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Paquete: <?= $package['name'] ?></h4>
                        <span class="badge bg-primary">Precio: Bs <?= number_format($package['price'], 2) ?></span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Duración</h5>
                                    <h3><?= $package['duration_days'] ?> días</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Descuento</h5>
                                    <h3><?= $package['discount_percentage'] ?>%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Cartones Gratis</h5>
                                    <h3><?= $package['free_cartons'] ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Puntos Diarios</h5>
                                    <h3><?= $package['daily_points'] ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">Lista de Suscriptores</h5>
                    
                    <?php if (empty($subscribers)) : ?>
                        <div class="alert alert-info">
                            No hay suscriptores para este paquete.
                        </div>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-centered table-striped dt-responsive nowrap w-100" id="subscribers-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Vencimiento</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscribers as $subscriber) : ?>
                                        <tr>
                                            <td><?= $subscriber['user_id'] ?></td>
                                            <td><?= $subscriber['username'] ?></td>
                                            <td><?= $subscriber['firstname'] . ' ' . $subscriber['lastname'] ?></td>
                                            <td><?= $subscriber['email'] ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($subscriber['start_date'])) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($subscriber['end_date'])) ?></td>
                                            <td>
                                                <?php if ($subscriber['is_active'] && strtotime($subscriber['end_date']) > time()) : ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary">Expirado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('users/view/' . $subscriber['user_id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Estadísticas del Paquete</h4>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Total Suscriptores</h5>
                                    <h3><?= count($subscribers) ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Suscriptores Activos</h5>
                                    <?php
                                    $activeCount = 0;
                                    foreach ($subscribers as $subscriber) {
                                        if ($subscriber['is_active'] && strtotime($subscriber['end_date']) > time()) {
                                            $activeCount++;
                                        }
                                    }
                                    ?>
                                    <h3><?= $activeCount ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Suscriptores Expirados</h5>
                                    <h3><?= count($subscribers) - $activeCount ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Ingresos Totales</h5>
                                    <h3>Bs <?= number_format(count($subscribers) * $package['price'], 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#subscribers-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ suscriptores",
                lengthMenu: "Mostrar _MENU_ suscriptores",
                search: "Buscar:",
                emptyTable: "No hay suscriptores disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>