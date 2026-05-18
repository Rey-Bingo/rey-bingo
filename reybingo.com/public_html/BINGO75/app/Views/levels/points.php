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
                <h4 class="page-title">Historial de Puntos</h4>
            </div>
        </div>
    </div>

    <!-- Points Summary Section -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-lighten rounded-circle text-primary">
                                <i class="mdi mdi-star-circle font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= number_format($pointsSummary['current']) ?></h3>
                            <p class="text-muted mb-1">Puntos Disponibles</p>
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
                                <i class="mdi mdi-plus-circle font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= number_format($pointsSummary['earned']) ?></h3>
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
                                <i class="mdi mdi-minus-circle font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= number_format($pointsSummary['spent']) ?></h3>
                            <p class="text-muted mb-1">Puntos Gastados</p>
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
                            <span class="avatar-title bg-danger-lighten rounded-circle text-danger">
                                <i class="mdi mdi-clock-outline font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= number_format($pointsSummary['expired']) ?></h3>
                            <p class="text-muted mb-1">Puntos Expirados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Points History Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Historial Completo de Puntos</h4>
                    <p class="text-muted">Registro de todas tus transacciones de puntos.</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="points-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Fuente</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Puntos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pointsHistory as $point) : ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($point['created_at'])) ?></td>
                                        <td>
                                            <?php if ($point['type'] == 'earned') : ?>
                                                <span class="badge bg-success">Ganados</span>
                                            <?php elseif ($point['type'] == 'spent') : ?>
                                                <span class="badge bg-warning">Gastados</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">Expirados</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $sourceLabels = [
                                                'game' => 'Partida',
                                                'package' => 'Paquete Pro',
                                                'daily' => 'Bono Diario',
                                                'level' => 'Subida de Nivel',
                                                'referral' => 'Referido',
                                                'achievement' => 'Logro',
                                                'admin' => 'Administrador'
                                            ];
                                            echo $sourceLabels[$point['source']] ?? $point['source'];
                                            
                                            if ($point['source_id']) {
                                                echo ' #' . $point['source_id'];
                                            }
                                            ?>
                                        </td>
                                        <td><?= $point['description'] ?></td>
                                        <td class="text-end">
                                            <?php if ($point['type'] == 'earned') : ?>
                                                <span class="text-success">+<?= number_format($point['amount']) ?></span>
                                            <?php else : ?>
                                                <span class="text-danger">-<?= number_format($point['amount']) ?></span>
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

    <!-- Points Usage Guide Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Guía de Puntos</h4>
                    <p class="text-muted">Aprende cómo ganar y usar tus puntos en Bingo Family.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Cómo ganar puntos</h5>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Bono diario
                                    <span class="badge bg-primary rounded-pill">5-20 puntos</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Participar en partidas
                                    <span class="badge bg-primary rounded-pill">10-50 puntos</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Ganar partidas
                                    <span class="badge bg-primary rounded-pill">100-500 puntos</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Desbloquear logros
                                    <span class="badge bg-primary rounded-pill">10-1000 puntos</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Invitar amigos
                                    <span class="badge bg-primary rounded-pill">100 puntos por referido</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Suscripción Pro
                                    <span class="badge bg-primary rounded-pill">10-20 puntos diarios</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Cómo usar puntos</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Comprar cartones
                                    <span class="badge bg-primary rounded-pill">100 puntos = 1 cartón</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Participar en sorteos especiales
                                    <span class="badge bg-primary rounded-pill">Varía por sorteo</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Desbloquear temas y personalización
                                    <span class="badge bg-primary rounded-pill">500-1000 puntos</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Acceso a partidas exclusivas
                                    <span class="badge bg-primary rounded-pill">Varía por partida</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#points-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ transacciones",
                lengthMenu: "Mostrar _MENU_ transacciones",
                search: "Buscar:",
                emptyTable: "No hay transacciones disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            order: [[0, 'desc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>
<?= $this->endSection() ?>