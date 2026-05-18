<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('levels/manage') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Editar Nivel</h4>
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
                    <h4 class="header-title">Información del Nivel</h4>
                    <p class="text-muted">Actualiza la información del nivel.</p>

                    <form action="<?= base_url('levels/edit/' . $level['id']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $level['name']) ?>" required>
                                    <div class="form-text">Ejemplo: Principiante, Aficionado, Experto, etc.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="required_points" class="form-label">Puntos Requeridos <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="required_points" name="required_points" value="<?= old('required_points', $level['required_points']) ?>" min="0" required>
                                    <div class="form-text">Cantidad de puntos necesarios para alcanzar este nivel.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Descuento en Cartones (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?= old('discount_percentage', $level['discount_percentage']) ?>" step="0.01" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Porcentaje de descuento en la compra de cartones.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="free_cartons_per_day" class="form-label">Cartones Gratis Diarios <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="free_cartons_per_day" name="free_cartons_per_day" value="<?= old('free_cartons_per_day', $level['free_cartons_per_day']) ?>" min="0" required>
                                    <div class="form-text">Cantidad de cartones gratis que recibirá el usuario cada día.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description', $level['description']) ?></textarea>
                            <div class="form-text">Breve descripción del nivel que se mostrará a los usuarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Beneficios <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="benefits" name="benefits" rows="5" required><?= old('benefits', $level['benefits']) ?></textarea>
                            <div class="form-text">Lista detallada de beneficios que incluye este nivel.</div>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icono</label>
                            <input type="file" class="form-control" id="icon" name="icon" accept="image/*">
                            <div class="form-text">Imagen que representará este nivel. Recomendado: 128x128 píxeles. Deja en blanco para mantener el icono actual.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Icono Actual</label>
                            <div class="mt-2">
                                <img id="icon-preview" src="<?= base_url('uploads/levels/' . $level['icon']) ?>" alt="<?= $level['name'] ?>" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Actualizar Nivel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($level['id'] != 1) : ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title text-danger">Zona de Peligro</h4>
                        <p class="text-muted">Las acciones en esta sección pueden tener consecuencias importantes.</p>

                        <div class="alert alert-warning">
                            <h5><i class="mdi mdi-alert"></i> Advertencia</h5>
                            <p>Eliminar un nivel puede afectar a los usuarios que actualmente están en este nivel. Se recomienda reasignar a los usuarios a otro nivel antes de eliminar.</p>
                        </div>

                        <a href="<?= base_url('levels/delete/' . $level['id']) ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este nivel? Esta acción no se puede deshacer.');">
                            <i class="mdi mdi-delete me-1"></i> Eliminar Nivel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
    });
</script>
<?= $this->endSection() ?>