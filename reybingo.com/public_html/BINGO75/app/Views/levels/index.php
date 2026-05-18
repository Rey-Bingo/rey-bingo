<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Mi Nivel</h4>
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

    <!-- Current Level Section -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-lg">
                            <img src="<?= base_url('uploads/levels/' . $currentLevel['icon']) ?>" alt="<?= $currentLevel['name'] ?>" class="img-thumbnail rounded-circle">
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-1"><?= $currentLevel['name'] ?></h4>
                            <p class="text-muted mb-0">Nivel actual</p>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p><?= $currentLevel['description'] ?></p>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Puntos totales:</span>
                            <span class="fw-bold"><?= number_format($user['total_points']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Puntos disponibles:</span>
                            <span class="fw-bold"><?= number_format($user['current_points']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Días consecutivos:</span>
                            <span class="fw-bold"><?= $user['consecutive_days'] ?></span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <h5>Beneficios de tu nivel</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Descuento en cartones
                                <span class="badge bg-primary rounded-pill"><?= $currentLevel['discount_percentage'] ?>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Cartones gratis diarios
                                <span class="badge bg-primary rounded-pill"><?= $currentLevel['free_cartons_per_day'] ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <a href="<?= base_url('levels/dailyBonus') ?>" class="btn btn-primary w-100" id="claim-daily-bonus">
                            <i class="mdi mdi-gift me-1"></i> Reclamar Bono Diario
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Progreso de Nivel</h4>

                    <?php if ($nextLevelInfo['next_level']) : ?>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Progreso hacia <?= $nextLevelInfo['next_level']['name'] ?></span>
                                <span><?= $nextLevelInfo['progress_percentage'] ?>%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $nextLevelInfo['progress_percentage'] ?>%;" aria-valuenow="<?= $nextLevelInfo['progress_percentage'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span><?= number_format($user['total_points'] - $currentLevel['required_points']) ?> / <?= number_format($nextLevelInfo['next_level']['required_points'] - $currentLevel['required_points']) ?> puntos</span>
                                <span>Faltan <?= number_format($nextLevelInfo['points_needed']) ?> puntos</span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Beneficios del siguiente nivel</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Beneficio</th>
                                            <th class="text-center">Nivel Actual</th>
                                            <th class="text-center">Siguiente Nivel</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Descuento en cartones</td>
                                            <td class="text-center"><?= $currentLevel['discount_percentage'] ?>%</td>
                                            <td class="text-center"><?= $nextLevelInfo['next_level']['discount_percentage'] ?>%</td>
                                        </tr>
                                        <tr>
                                            <td>Cartones gratis diarios</td>
                                            <td class="text-center"><?= $currentLevel['free_cartons_per_day'] ?></td>
                                            <td class="text-center"><?= $nextLevelInfo['next_level']['free_cartons_per_day'] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-success mt-3">
                            <h5><i class="mdi mdi-crown"></i> ¡Felicidades!</h5>
                            <p>Has alcanzado el nivel máximo. ¡Disfruta de todos los beneficios!</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <h5>Todos los niveles</h5>
                        <div class="table-responsive">
                            <table class="table table-centered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nivel</th>
                                        <th>Nombre</th>
                                        <th>Puntos Requeridos</th>
                                        <th>Descuento</th>
                                        <th>Cartones Gratis</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($levels as $level) : ?>
                                        <tr>
                                            <td>
                                                <img src="<?= base_url('uploads/levels/' . $level['icon']) ?>" alt="<?= $level['name'] ?>" class="rounded-circle" width="32">
                                            </td>
                                            <td><?= $level['name'] ?></td>
                                            <td><?= number_format($level['required_points']) ?></td>
                                            <td><?= $level['discount_percentage'] ?>%</td>
                                            <td><?= $level['free_cartons_per_day'] ?></td>
                                            <td>
                                                <?php if ($user['total_points'] >= $level['required_points']) : ?>
                                                    <span class="badge bg-success">Desbloqueado</span>
                                                <?php else : ?>
                                                    <span class="badge bg-secondary">Bloqueado</span>
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
    </div>

    <!-- Points History Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Historial de Puntos Recientes</h4>
                        <a href="<?= base_url('levels/points') ?>" class="btn btn-sm btn-primary">Ver Todo</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily bonus claim
        document.getElementById('claim-daily-bonus').addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('<?= base_url('levels/dailyBonus') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Bono Diario Reclamado!',
                            html: `Has recibido <strong>${data.data.points}</strong> puntos.<br>
                                   Día consecutivo: <strong>${data.data.consecutive_days}</strong>`,
                            icon: 'success',
                            confirmButtonText: 'Genial'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'No disponible',
                            text: data.message,
                            icon: 'info',
                            confirmButtonText: 'Entendido'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al procesar tu solicitud',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                });
        });
    });
</script>
<?= $this->endSection() ?>