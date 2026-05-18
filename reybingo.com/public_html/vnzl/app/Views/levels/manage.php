<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('levels/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Crear Nivel
                    </a>
                </div>
                <h4 class="page-title">Administrar Niveles</h4>
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
                    <h4 class="header-title">Niveles Disponibles</h4>
                    <p class="text-muted">Administra los niveles de usuario y sus beneficios.</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="levels-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Icono</th>
                                    <th>Nombre</th>
                                    <th>Puntos Requeridos</th>
                                    <th>Descuento</th>
                                    <th>Cartones Gratis</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($levels as $level) : ?>
                                    <tr>
                                        <td><?= $level['id'] ?></td>
                                        <td>
                                            <img src="<?= base_url('uploads/levels/' . $level['icon']) ?>" alt="<?= $level['name'] ?>" class="rounded-circle" width="32">
                                        </td>
                                        <td><?= $level['name'] ?></td>
                                        <td><?= number_format($level['required_points']) ?></td>
                                        <td><?= $level['discount_percentage'] ?>%</td>
                                        <td><?= $level['free_cartons_per_day'] ?></td>
                                        <td>
                                            <div class="btn-group dropdown">
                                                <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item" href="<?= base_url('levels/edit/' . $level['id']) ?>">
                                                        <i class="mdi mdi-pencil me-1"></i> Editar
                                                    </a>
                                                    <?php if ($level['id'] != 1) : ?>
                                                        <a class="dropdown-item text-danger" href="<?= base_url('levels/delete/' . $level['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este nivel?');">
                                                            <i class="mdi mdi-delete me-1"></i> Eliminar
                                                        </a>
                                                    <?php endif; ?>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Distribución de Usuarios por Nivel</h4>
                    <p class="text-muted">Visualización de la cantidad de usuarios en cada nivel.</p>

                    <div class="chart-container" style="height: 300px;">
                        <canvas id="levelDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Acciones Adicionales</h4>
                    <p class="text-muted">Otras acciones relacionadas con el sistema de niveles.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Gestionar Logros</h5>
                                    <p class="card-text">Administra los logros disponibles para los usuarios.</p>
                                    <a href="<?= base_url('levels/manageAchievements') ?>" class="btn btn-primary">
                                        <i class="mdi mdi-trophy me-1"></i> Gestionar Logros
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Otorgar Puntos</h5>
                                    <p class="card-text">Otorga puntos manualmente a usuarios específicos.</p>
                                    <a href="<?= base_url('levels/awardPoints') ?>" class="btn btn-success">
                                        <i class="mdi mdi-star me-1"></i> Otorgar Puntos
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Configuración</h5>
                                    <p class="card-text">Configura parámetros del sistema de niveles y puntos.</p>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#levelSettingsModal">
                                        <i class="mdi mdi-cog me-1"></i> Configuración
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Level Settings Modal -->
<div class="modal fade" id="levelSettingsModal" tabindex="-1" aria-labelledby="levelSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="levelSettingsModalLabel">Configuración del Sistema de Niveles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="dailyBonusBase" class="form-label">Bono Diario Base</label>
                        <input type="number" class="form-control" id="dailyBonusBase" value="10">
                        <div class="form-text">Cantidad base de puntos otorgados por el bono diario.</div>
                    </div>
                    <div class="mb-3">
                        <label for="streakBonus" class="form-label">Bono por Racha</label>
                        <input type="number" class="form-control" id="streakBonus" value="2">
                        <div class="form-text">Puntos adicionales por día consecutivo (máximo 5 días).</div>
                    </div>
                    <div class="mb-3">
                        <label for="gameParticipationPoints" class="form-label">Puntos por Participación en Partida</label>
                        <input type="number" class="form-control" id="gameParticipationPoints" value="10">
                        <div class="form-text">Puntos otorgados por participar en una partida.</div>
                    </div>
                    <div class="mb-3">
                        <label for="gameWinPoints" class="form-label">Puntos por Victoria</label>
                        <input type="number" class="form-control" id="gameWinPoints" value="100">
                        <div class="form-text">Puntos otorgados por ganar una partida.</div>
                    </div>
                    <div class="mb-3">
                        <label for="referralPoints" class="form-label">Puntos por Referido</label>
                        <input type="number" class="form-control" id="referralPoints" value="100">
                        <div class="form-text">Puntos otorgados por cada referido que se registre.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DataTable initialization
        $('#levels-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ niveles",
                lengthMenu: "Mostrar _MENU_ niveles",
                search: "Buscar:",
                emptyTable: "No hay niveles disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        // Chart initialization (example data - in a real app, this would come from the backend)
        const levelNames = <?= json_encode(array_column($levels, 'name')) ?>;
        const userCounts = [120, 85, 65, 40, 25, 10]; // Example data

        const ctx = document.getElementById('levelDistributionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: levelNames,
                datasets: [{
                    label: 'Usuarios',
                    data: userCounts,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(54, 185, 204, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)',
                        'rgba(133, 135, 150, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Usuarios'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Niveles'
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>