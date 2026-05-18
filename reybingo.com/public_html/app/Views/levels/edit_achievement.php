<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('levels/manageAchievements') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Editar Logro</h4>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">¡Error!</h4>
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Información del Logro</h4>
                    <p class="text-muted">Actualiza la información del logro.</p>

                    <form action="<?= base_url('levels/editAchievement/' . $achievement['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $achievement['name']) ?>" required>
                                    <div class="form-text">Ejemplo: Primer Juego, Jugador Dedicado, etc.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="points" class="form-label">Puntos <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="points" name="points" value="<?= old('points', $achievement['points']) ?>" min="1" required>
                                    <div class="form-text">Cantidad de puntos que recibirá el usuario al desbloquear este logro.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requirement_type" class="form-label">Tipo de Requisito <span class="text-danger">*</span></label>
                                    <select class="form-select" id="requirement_type" name="requirement_type" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="games_played" <?= old('requirement_type', $achievement['requirement_type']) == 'games_played' ? 'selected' : '' ?>>Partidas jugadas</option>
                                        <option value="wins" <?= old('requirement_type', $achievement['requirement_type']) == 'wins' ? 'selected' : '' ?>>Victorias</option>
                                        <option value="consecutive_days" <?= old('requirement_type', $achievement['requirement_type']) == 'consecutive_days' ? 'selected' : '' ?>>Días consecutivos</option>
                                        <option value="cartons_bought" <?= old('requirement_type', $achievement['requirement_type']) == 'cartons_bought' ? 'selected' : '' ?>>Cartones comprados</option>
                                        <option value="referrals" <?= old('requirement_type', $achievement['requirement_type']) == 'referrals' ? 'selected' : '' ?>>Referidos</option>
                                    </select>
                                    <div class="form-text">Tipo de acción que el usuario debe realizar para desbloquear este logro.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requirement_value" class="form-label">Valor del Requisito <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="requirement_value" name="requirement_value" value="<?= old('requirement_value', $achievement['requirement_value']) ?>" min="1" required>
                                    <div class="form-text">Cantidad requerida para desbloquear este logro (ej. 10 partidas jugadas).</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description', $achievement['description']) ?></textarea>
                            <div class="form-text">Descripción del logro que se mostrará a los usuarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icono</label>
                            <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                            <div class="form-text">Imagen que representará este logro. Recomendado: 128x128 píxeles. Deja en blanco para mantener el icono actual.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Icono Actual</label>
                            <div class="mt-2">
                                <img id="icon-preview" src="<?= base_url('uploads/achievements/' . $achievement['icon']) ?>" alt="<?= $achievement['name'] ?>" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Actualizar Logro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title text-danger">Zona de Peligro</h4>
                    <p class="text-muted">Las acciones en esta sección pueden tener consecuencias importantes.</p>

                    <div class="alert alert-warning">
                        <h5><i class="mdi mdi-alert"></i> Advertencia</h5>
                        <p>Eliminar un logro eliminará también todo el progreso de los usuarios en este logro. Esta acción no se puede deshacer.</p>
                    </div>

                    <a href="<?= base_url('levels/deleteAchievement/' . $achievement['id']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este logro? Esta acción no se puede deshacer.');">
                        <i class="mdi mdi-delete me-1"></i> Eliminar Logro
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Estadísticas del Logro</h4>
                    <p class="text-muted">Información sobre el progreso de los usuarios en este logro.</p>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Usuarios Totales</h5>
                                    <h3>345</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Completado</h5>
                                    <h3>127</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">En Progreso</h5>
                                    <h3>218</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h5 class="text-muted">Tasa de Completado</h5>
                                    <h3>36.8%</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="achievementProgressChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview
        document.getElementById('icon').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('icon-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Dynamic form validation based on requirement type
        document.getElementById('requirement_type').addEventListener('change', function() {
            const requirementValue = document.getElementById('requirement_value');
            const requirementType = this.value;
            
            // Reset validation
            requirementValue.min = 1;
            
            // Set specific validation based on type
            switch (requirementType) {
                case 'consecutive_days':
                    requirementValue.min = 2; // At least 2 days for consecutive
                    break;
                case 'referrals':
                    requirementValue.min = 1;
                    break;
            }
        });

        // Progress chart (example data - in a real app, this would come from the backend)
        const progressData = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100];
        const userCounts = [218, 45, 32, 28, 25, 22, 18, 15, 12, 10, 127]; // Number of users at each progress level

        const ctx = document.getElementById('achievementProgressChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: progressData.map(p => p + '%'),
                datasets: [{
                    label: 'Número de Usuarios',
                    data: userCounts,
                    backgroundColor: function(context) {
                        const index = context.dataIndex;
                        const value = progressData[index];
                        return value < 100 ? 'rgba(54, 162, 235, 0.8)' : 'rgba(75, 192, 192, 0.8)';
                    },
                    borderColor: function(context) {
                        const index = context.dataIndex;
                        const value = progressData[index];
                        return value < 100 ? 'rgba(54, 162, 235, 1)' : 'rgba(75, 192, 192, 1)';
                    },
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
                            text: 'Progreso'
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>