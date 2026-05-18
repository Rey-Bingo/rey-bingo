<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('packages/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Crear Paquete
                    </a>
                </div>
                <h4 class="page-title">Administrar Paquetes</h4>
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
                        <h4 class="header-title">Paquetes Disponibles</h4>
                        <a href="<?= base_url('packages/processExpiration') ?>" class="btn btn-sm btn-info">
                            <i class="mdi mdi-refresh me-1"></i> Procesar Expiración
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="packages-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Duración</th>
                                    <th>Descuento</th>
                                    <th>Cartones</th>
                                    <th>Puntos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($packages as $package) : ?>
                                    <tr>
                                        <td><?= $package['id'] ?></td>
                                        <td><?= $package['name'] ?></td>
                                        <td>Bs <?= number_format($package['price'], 2) ?></td>
                                        <td><?= $package['duration_days'] ?> días</td>
                                        <td><?= $package['discount_percentage'] ?>%</td>
                                        <td><?= $package['free_cartons'] ?></td>
                                        <td><?= $package['daily_points'] ?></td>
                                        <td>
                                            <?php if ($package['status'] == 1) : ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="<?= base_url('packages/view/' . $package['id']) ?>">
                                                        <i class="mdi mdi-eye me-1"></i> Ver
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('packages/edit/' . $package['id']) ?>">
                                                        <i class="mdi mdi-pencil me-1"></i> Editar
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('packages/subscribers/' . $package['id']) ?>">
                                                        <i class="mdi mdi-account-group me-1"></i> Suscriptores
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="<?= base_url('packages/delete/' . $package['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este paquete?');">
                                                        <i class="mdi mdi-delete me-1"></i> Eliminar
                                                    </a>
                                                </div>
                                            </div>
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#packages-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ paquetes",
                lengthMenu: "Mostrar _MENU_ paquetes",
                search: "Buscar:",
                emptyTable: "No hay paquetes disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>