<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('levels/createAchievement') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Crear Logro
                    </a>
                </div>
                <h4 class="page-title">Administrar Logros</h4>
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
                    <h4 class="header-title">Logros Disponibles</h4>
                    <p class="text-muted">Administra los logros que los usuarios pueden desbloquear.</p>

                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#all-achievements" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                Todos los Logros
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#games-played" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Partidas Jugadas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#wins" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Victorias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#consecutive-days" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Días Consecutivos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#cartons-bought" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Cartones Comprados
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#referrals" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Referidos
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="all-achievements">
                            <div class="table-responsive">
                                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="achievements-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Icono</th>
                                            <th>Nombre</th>
                                            <th>Tipo</th>
                                            <th>Requisito</th>
                                            <th>Puntos</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($achievements as $achievement) : ?>
                                            <tr>
                                                <td><?= $achievement['id'] ?></td>
                                                <td>
                                                    <img src="<?= base_url('uploads/achievements/' . $achievement['icon']) ?>" alt="<?= $achievement['name'] ?>" class="rounded-circle" width="32">
                                                </td>
                                                <td><?= $achievement['name'] ?></td>
                                                <td>
                                                    <?php
                                                    $typeLabels = [
                                                        'games_played' => 'Partidas jugadas',
                                                        'wins' => 'Victorias',
                                                        'consecutive_days' => 'Días consecutivos',
                                                        'cartons_bought' => 'Cartones comprados',
                                                        'referrals' => 'Referidos'
                                                    ];
                                                    echo $typeLabels[$achievement['requirement_type']] ?? $achievement['requirement_type'];
                                                    ?>
                                                </td>
                                                <td><?= $achievement['requirement_value'] ?></td>
                                                <td><?= number_format($achievement['points']) ?></td>
                                                <td>
                                                    <div class="btn-group dropdown">
                                                        <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="<?= base_url('levels/editAchievement/' . $achievement['id']) ?>">
                                                                <i class="mdi mdi-pencil me-1"></i> Editar
                                                            </a>
                                                            <a class="dropdown-item text-danger" href="<?= base_url('levels/deleteAchievement/' . $achievement['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este logro?');">
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

                        <?php
                        $achievementTypes = [
                            'games_played' => 'games-played',
                            'wins' => 'wins',
                            'consecutive_days' => 'consecutive-days',
                            'cartons_bought' => 'cartons-bought',
                            'referrals' => 'referrals'
                        ];

                        foreach ($achievementTypes as $type => $tabId) :
                            $filteredAchievements = array_filter($achievements, function($achievement) use ($type) {
                                return $achievement['requirement_type'] === $type;
                            });
                        ?>
                            <div class="tab-pane" id="<?= $tabId ?>">
                                <?php if (empty($filteredAchievements)) : ?>
                                    <div class="alert alert-info">
                                        No hay logros de este tipo. <a href="<?= base_url('levels/createAchievement') ?>" class="alert-link">Crear uno nuevo</a>.
                                    </div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-centered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Icono</th>
                                                    <th>Nombre</th>
                                                    <th>Requisito</th>
                                                    <th>Puntos</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($filteredAchievements as $achievement) : ?>
                                                    <tr>
                                                        <td><?= $achievement['id'] ?></td>
                                                        <td>
                                                            <img src="<?= base_url('uploads/achievements/' . $achievement['icon']) ?>" alt="<?= $achievement['name'] ?>" class="rounded-circle" width="32">
                                                        </td>
                                                        <td><?= $achievement['name'] ?></td>
                                                        <td><?= $achievement['requirement_value'] ?></td>
                                                        <td><?= number_format($achievement['points']) ?></td>
                                                        <td>
                                                            <a href="<?= base_url('levels/editAchievement/' . $achievement['id']) ?>" class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-pencil"></i>
                                                            </a>
                                                            <a href="<?= base_url('levels/deleteAchievement/' . $achievement['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este logro?');">
                                                                <i class="mdi mdi-delete"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Estadísticas de Logros</h4>
                    <p class="text-muted">Información sobre el progreso de los usuarios en los logros.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Logros Más Completados</h5>
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="topAchievementsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Logros Menos Completados</h5>
                                    <div class="chart-container" style="height: 250px;">
                                        <canvas id="leastAchievementsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    <p class="text-muted">Otras acciones relacionadas con el sistema de logros.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Inicializar Logros</h5>
                                    <p class="card-text">Inicializa los logros para todos los usuarios que no los tengan.</p>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#initializeAchievementsModal">
                                        <i class="mdi mdi-refresh me-1"></i> Inicializar Logros
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Recalcular Progreso</h5>
                                    <p class="card-text">Recalcula el progreso de los logros para todos los usuarios.</p>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#recalculateProgressModal">
                                        <i class="mdi mdi-calculator me-1"></i> Recalcular Progreso
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Exportar Datos</h5>
                                    <p class="card-text">Exporta los datos de logros a un archivo CSV.</p>
                                    <a href="javascript:void(0);" class="btn btn-success">
                                        <i class="mdi mdi-download me-1"></i> Exportar Datos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Initialize Achievements Modal -->
<div class="modal fade" id="initializeAchievementsModal" tabindex="-1" aria-labelledby="initializeAchievementsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="initializeAchievementsModalLabel">Inicializar Logros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Esta acción inicializará los logros para todos los usuarios que no los tengan. ¿Estás seguro de que deseas continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Inicializar</button>
            </div>
        </div>
    </div>
</div>

<!-- Recalculate Progress Modal -->
<div class="modal fade" id="recalculateProgressModal" tabindex="-1" aria-labelledby="recalculateProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recalculateProgressModalLabel">Recalcular Progreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Esta acción recalculará el progreso de los logros para todos los usuarios. Este proceso puede tardar varios minutos. ¿Estás seguro de que deseas continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info">Recalcular</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DataTable initialization
        $('#achievements-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ logros",
                lengthMenu: "Mostrar _MENU_ logros",
                search: "Buscar:",
                emptyTable: "No hay logros disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });

        // Charts initialization (example data - in a real app, this would come from the backend)
        const topAchievementNames = ['Primer Juego', 'Jugador Constante', 'Primera Victoria', 'Jugador Dedicado', 'Coleccionista'];
        const topAchievementCounts = [95, 80, 75, 60, 45]; // Percentage of users who completed

        const leastAchievementNames = ['Maestro del Bingo', 'Jugador Leal', 'Ganador Frecuente', 'Referente', 'Jugador Veterano'];
        const leastAchievementCounts = [5, 10, 15, 20, 25]; // Percentage of users who completed

        // Top Achievements Chart
        const topCtx = document.getElementById('topAchievementsChart').getContext('2d');
        new Chart(topCtx, {
            type: 'horizontalBar',
            data: {
                labels: topAchievementNames,
                datasets: [{
                    label: '% de Usuarios',
                    data: topAchievementCounts,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: '% de Usuarios'
                        }
                    }
                }
            }
        });

        // Least Achievements Chart
        const leastCtx = document.getElementById('leastAchievementsChart').getContext('2d');
        new Chart(leastCtx, {
            type: 'horizontalBar',
            data: {
                labels: leastAchievementNames,
                datasets: [{
                    label: '% de Usuarios',
                    data: leastAchievementCounts,
                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: '% de Usuarios'
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>