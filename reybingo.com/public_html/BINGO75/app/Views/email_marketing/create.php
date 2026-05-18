<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('emailMarketing') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Crear Campaña de Email</h4>
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
                    <h4 class="header-title">Información de la Campaña</h4>
                    <p class="text-muted">Completa el formulario para crear una nueva campaña de email marketing.</p>

                    <form action="<?= base_url('emailMarketing/create') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre de la Campaña <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                    <div class="form-text">Nombre interno para identificar la campaña.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Asunto del Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?= old('subject') ?>" required>
                                    <div class="form-text">El asunto que verán los destinatarios en su bandeja de entrada.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="segment" class="form-label">Segmento de Usuarios <span class="text-danger">*</span></label>
                            <select class="form-select" id="segment" name="segment" required>
                                <option value="">Seleccionar segmento...</option>
                                <option value="all" <?= old('segment') == 'all' ? 'selected' : '' ?>>Todos los usuarios</option>
                                <option value="pro" <?= old('segment') == 'pro' ? 'selected' : '' ?>>Usuarios Pro</option>
                                <option value="non_pro" <?= old('segment') == 'non_pro' ? 'selected' : '' ?>>Usuarios No Pro</option>
                                <?php if (!empty($levels)) : ?>
                                    <optgroup label="Niveles">
                                        <?php foreach ($levels as $level) : ?>
                                            <option value="level_<?= $level['id'] ?>" <?= old('segment') == 'level_' . $level['id'] ? 'selected' : '' ?>>
                                                <?= $level['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            </select>
                            <div class="form-text">Selecciona el grupo de usuarios a los que se enviará este email.</div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Contenido del Email <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="15" required><?= old('content') ?></textarea>
                            <div class="form-text">
                                Puedes usar HTML para dar formato al contenido. También puedes usar las siguientes variables:
                                <ul class="mt-1">
                                    <li><code>{{nombre}}</code> - Nombre del usuario</li>
                                    <li><code>{{apellido}}</code> - Apellido del usuario</li>
                                    <li><code>{{email}}</code> - Email del usuario</li>
                                    <li><code>{{username}}</code> - Nombre de usuario</li>
                                    <li><code>{{nivel}}</code> - Nivel actual del usuario</li>
                                    <li><code>{{puntos}}</code> - Puntos actuales del usuario</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="schedule" name="schedule" value="yes" <?= old('schedule') == 'yes' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="schedule">Programar envío</label>
                            </div>
                        </div>

                        <div id="schedule-options" class="row mb-3" style="display: <?= old('schedule') == 'yes' ? 'flex' : 'none' ?>;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Fecha de Envío <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" value="<?= old('scheduled_date') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_time" class="form-label">Hora de Envío <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="scheduled_time" name="scheduled_time" value="<?= old('scheduled_time') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" name="action" value="draft" class="btn btn-secondary me-2">Guardar como Borrador</button>
                            <button type="submit" name="action" value="send" class="btn btn-primary">Enviar Ahora</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/vendor/tinymce/tinymce.min.js') ?>"></script>
<script>
    $(document).ready(function() {
        // Inicializar TinyMCE
        tinymce.init({
            selector: '#content',
            height: 400,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }'
        });

        // Mostrar/ocultar opciones de programación
        $('#schedule').change(function() {
            if ($(this).is(':checked')) {
                $('#schedule-options').show();
            } else {
                $('#schedule-options').hide();
            }
        });
    });
</script>
<?= $this->endSection() ?>