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
                <h4 class="page-title">Crear Nivel</h4>
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
                    <p class="text-muted">Completa el formulario para crear un nuevo nivel.</p>

                    <form action="<?= base_url('levels/create') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                    <div class="form-text">Ejemplo: Principiante, Aficionado, Experto, etc.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="required_points" class="form-label">Puntos Requeridos <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="required_points" name="required_points" value="<?= old('required_points') ?>" min="0" required>
                                    <div class="form-text">Cantidad de puntos necesarios para alcanzar este nivel.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Descuento en Cartones (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?= old('discount_percentage') ?>" step="0.01" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Porcentaje de descuento en la compra de cartones.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="free_cartons_per_day" class="form-label">Cartones Gratis Diarios <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="free_cartons_per_day" name="free_cartons_per_day" value="<?= old('free_cartons_per_day') ?>" min="0" required>
                                    <div class="form-text">Cantidad de cartones gratis que recibirá el usuario cada día.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description') ?></textarea>
                            <div class="form-text">Breve descripción del nivel que se mostrará a los usuarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Beneficios <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="benefits" name="benefits" rows="5" required><?= old('benefits') ?></textarea>
                            <div class="form-text">Lista detallada de beneficios que incluye este nivel.</div>
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">Icono <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="icon" name="icon" accept="image/*" required>
                            <div class="form-text">Imagen que representará este nivel. Recomendado: 128x128 píxeles.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vista Previa del Icono</label>
                            <div class="mt-2">
                                <img id="icon-preview" src="<?= base_url('assets/images/level-default.png') ?>" alt="Vista previa del icono" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Crear Nivel</button>
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
                    <h4 class="header-title">Niveles Existentes</h4>
                    <p class="text-muted">Asegúrate de que el nuevo nivel se integre correctamente con los niveles existentes.</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped">
                            <thead>
                                <tr>
                                    <th>Nivel</th>
                                    <th>Nombre</th>
                                    <th>Puntos Requeridos</th>
                                    <th>Descuento</th>
                                    <th>Cartones Gratis</th>
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