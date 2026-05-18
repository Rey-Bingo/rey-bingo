<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('packages/manage') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Editar Paquete</h4>
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
                    <h4 class="header-title">Información del Paquete</h4>
                    <p class="text-muted">Actualiza la información del paquete Pro.</p>

                    <form action="<?= base_url('packages/edit/' . $package['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $package['name']) ?>" required>
                                    <div class="form-text">Ejemplo: Pro Mensual, Pro Trimestral, etc.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Precio <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Bs</span>
                                        <input type="number" class="form-control" id="price" name="price" value="<?= old('price', $package['price']) ?>" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_days" class="form-label">Duración (días) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="duration_days" name="duration_days" value="<?= old('duration_days', $package['duration_days']) ?>" min="1" required>
                                    <div class="form-text">Ejemplo: 30 para un mes, 90 para un trimestre, 365 para un año.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Descuento en Cartones (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?= old('discount_percentage', $package['discount_percentage']) ?>" step="0.01" min="0" max="100" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="free_cartons" class="form-label">Cartones Gratis <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="free_cartons" name="free_cartons" value="<?= old('free_cartons', $package['free_cartons']) ?>" min="0" required>
                                    <div class="form-text">Cantidad de cartones gratis que recibirá el usuario durante la suscripción.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="daily_points" class="form-label">Puntos Diarios <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="daily_points" name="daily_points" value="<?= old('daily_points', $package['daily_points']) ?>" min="0" required>
                                    <div class="form-text">Cantidad de puntos adicionales que recibirá el usuario cada día.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description', $package['description']) ?></textarea>
                            <div class="form-text">Breve descripción del paquete que se mostrará a los usuarios.</div>
                        </div>

                        <div class="mb-3">
                            <label for="benefits" class="form-label">Beneficios <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="benefits" name="benefits" rows="5" required><?= old('benefits', $package['benefits']) ?></textarea>
                            <div class="form-text">Lista detallada de beneficios que incluye este paquete.</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= old('status', $package['status']) == 1 ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= old('status', $package['status']) == 0 ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Actualizar Paquete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>