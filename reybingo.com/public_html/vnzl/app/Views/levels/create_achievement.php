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
                <h4 class="page-title">Crear Logro</h4>
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
                    <p class="text-muted">Completa el formulario para crear un nuevo logro.</p>

                    <form action="<?= base_url('levels/createAchievement') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                    <div class="form-text">Ejemplo: Primer Juego, Jugador Dedicado, etc.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="points" class="form-label">Puntos <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="points" name="points" value="<?= old('points') ?>" min="1" required>
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
                                        <option value="games_played" <?= old('requirement_type') == 'games_played' ? 'selected' : '' ?>>Partidas jugadas</option>
                                        <option value="wins" <?= old('requirement_type') == 'wins' ? 'selected' : '' ?>>Victorias</option>
                                        <option value="consecutive_days" <?= old('requirement_type') == 'consecutive_days' ? 'selected' : '' ?>>Días consecutivos</option>
                                        <option value="cartons_bought" <?= old('requirement_type') == 'cartons_bought' ? 'selected' : '' ?>>Cartones comprados</option>
                                        <option value="referrals" <?= old('requirement_type') == 'referrals' ? 'selected' : '' ?>>Referidos</option>
                                    </select>
                                    <div class="form-text">Tipo de acción que el usuario debe realizar para desbloquear este logro.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="requirement_value" class="form-label">Valor del Requisito <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="requirement_value" name="requirement_value" value="<?= old('requirement_value') ?>" min="1" required>
                                    <div class="form-text">Cantidad requerida para desbloquear este logro (ej. 10 partidas jugadas).</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description') ?></textarea>
                            <div class="form-text">Descripción del logro que se mostrará a los usuarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icono <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="icon" name="icon" accept="image/*" required>
                            <div class="form-text">Imagen que representará este logro. Recomendado: 128x128 píxeles.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vista Previa del Icono</label>
                            <div class="mt-2">
                                <img id="icon-preview" src="<?= base_url('assets/images/achievement-default.png') ?>" alt="Vista previa del icono" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Crear Logro</button>
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
                    <h4 class="header-title">Recomendaciones</h4>
                    <p class="text-muted">Consejos para crear logros efectivos.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Buenas Prácticas</h5>
                                    <ul class="list-group mb-0">
                                        <li class="list-group-item">Crea logros con diferentes niveles de dificultad</li>
                                        <li class="list-group-item">Utiliza nombres claros y descriptivos</li>
                                        <li class="list-group-item">Asegúrate de que los logros sean alcanzables</li>
                                        <li class="list-group-item">Recompensa adecuadamente según la dificultad</li>
                                        <li class="list-group-item">Utiliza iconos que representen claramente el logro</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">Ejemplos de Logros</h5>
                                    <ul class="list-group mb-0">
                                        <li class="list-group-item"><strong>Primer Juego</strong> - Participar en 1 partida</li>
                                        <li class="list-group-item"><strong>Jugador Dedicado</strong> - Participar en 10 partidas</li>
                                        <li class="list-group-item"><strong>Primera Victoria</strong> - Ganar 1 partida</li>
                                        <li class="list-group-item"><strong>Jugador Constante</strong> - Iniciar sesión 7 días consecutivos</li>
                                        <li class="list-group-item"><strong>Coleccionista</strong> - Comprar 20 cartones diferentes</li>
                                    </ul>
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
    });
</script>
<?= $this->endSection() ?>