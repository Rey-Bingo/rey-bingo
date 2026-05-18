<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('notificationsAdmin') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Crear Notificación</h4>
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
                    <h4 class="header-title">Información de la Notificación</h4>
                    <p class="text-muted">Completa el formulario para crear una nueva notificación.</p>

                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#basic-form" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                Notificación Básica
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#template-form" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Usar Plantilla
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="basic-form">
                            <form action="<?= base_url('notificationsAdmin/create') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="form_type" value="basic">

                                <div class="mb-3">
                                    <label for="segment" class="form-label">Destinatarios <span class="text-danger">*</span></label>
                                    <select class="form-select" id="segment" name="segment" required>
                                        <option value="">Seleccionar destinatarios...</option>
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
                                        <optgroup label="Usuario Específico">
                                            <option value="specific" <?= old('segment') == 'specific' ? 'selected' : '' ?>>Seleccionar usuario específico</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div id="specific-user-container" class="mb-3" style="display: <?= old('segment') == 'specific' ? 'block' : 'none' ?>;">
                                    <label for="user_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="user_id" name="user_id">
                                        <option value="">Seleccionar usuario...</option>
                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                                <?= $user['username'] ?> (<?= $user['firstname'] ?> <?= $user['lastname'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Tipo</label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="">General</option>
                                                <option value="game" <?= old('type') == 'game' ? 'selected' : '' ?>>Partida</option>
                                                <option value="promo" <?= old('type') == 'promo' ? 'selected' : '' ?>>Promoción</option>
                                                <option value="system" <?= old('type') == 'system' ? 'selected' : '' ?>>Sistema</option>
                                                <option value="achievement" <?= old('type') == 'achievement' ? 'selected' : '' ?>>Logro</option>
                                                <option value="level" <?= old('type') == 'level' ? 'selected' : '' ?>>Nivel</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Mensaje <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required><?= old('message') ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="action_url" class="form-label">URL de Acción</label>
                                            <input type="url" class="form-control" id="action_url" name="action_url" value="<?= old('action_url') ?>">
                                            <div class="form-text">URL a la que se redirigirá al hacer clic en la notificación.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image_url" class="form-label">URL de Imagen</label>
                                            <input type="url" class="form-control" id="image_url" name="image_url" value="<?= old('image_url') ?>">
                                            <div class="form-text">URL de una imagen para mostrar en la notificación.</div>
                                        </div>
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

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="send_push" name="send_push" value="yes" <?= old('send_push') == 'yes' ? 'checked' : '' ?> checked>
                                        <label class="form-check-label" for="send_push">Enviar como notificación push</label>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Crear Notificación</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="template-form">
                            <form action="<?= base_url('notificationsAdmin/create') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="form_type" value="template">

                                <div class="mb-3">
                                    <label for="template_id" class="form-label">Plantilla <span class="text-danger">*</span></label>
                                    <select class="form-select" id="template_id" name="template_id" required>
                                        <option value="">Seleccionar plantilla...</option>
                                        <?php foreach ($templates as $template) : ?>
                                            <option value="<?= $template['id'] ?>" <?= old('template_id') == $template['id'] ? 'selected' : '' ?>>
                                                <?= $template['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="template_segment" class="form-label">Destinatarios <span class="text-danger">*</span></label>
                                    <select class="form-select" id="template_segment" name="segment" required>
                                        <option value="">Seleccionar destinatarios...</option>
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
                                        <optgroup label="Usuario Específico">
                                            <option value="specific" <?= old('segment') == 'specific' ? 'selected' : '' ?>>Seleccionar usuario específico</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div id="template-specific-user-container" class="mb-3" style="display: <?= old('segment') == 'specific' ? 'block' : 'none' ?>;">
                                    <label for="template_user_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="template_user_id" name="user_id">
                                        <option value="">Seleccionar usuario...</option>
                                        <?php foreach ($users as $user) : ?>
                                            <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                                <?= $user['username'] ?> (<?= $user['firstname'] ?> <?= $user['lastname'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="template_data" class="form-label">Datos de la Plantilla</label>
                                    <div class="alert alert-info">
                                        <p>Los datos específicos requeridos dependerán de la plantilla seleccionada.</p>
                                        <div id="template-fields">
                                            <p class="text-muted">Selecciona una plantilla para ver los campos requeridos.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="template_schedule" name="schedule" value="yes" <?= old('schedule') == 'yes' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="template_schedule">Programar envío</label>
                                    </div>
                                </div>

                                <div id="template-schedule-options" class="row mb-3" style="display: <?= old('schedule') == 'yes' ? 'flex' : 'none' ?>;">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="template_scheduled_date" class="form-label">Fecha de Envío <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="template_scheduled_date" name="scheduled_date" value="<?= old('scheduled_date') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="template_scheduled_time" class="form-label">Hora de Envío <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="template_scheduled_time" name="scheduled_time" value="<?= old('scheduled_time') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="template_send_push" name="send_push" value="yes" <?= old('send_push') == 'yes' ? 'checked' : '' ?> checked>
                                        <label class="form-check-label" for="template_send_push">Enviar como notificación push</label>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Crear Notificación</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2();

        // Mostrar/ocultar selector de usuario específico
        $('#segment').change(function() {
            if ($(this).val() === 'specific') {
                $('#specific-user-container').show();
                $('#user_id').prop('required', true);
            } else {
                $('#specific-user-container').hide();
                $('#user_id').prop('required', false);
            }
        });

        $('#template_segment').change(function() {
            if ($(this).val() === 'specific') {
                $('#template-specific-user-container').show();
                $('#template_user_id').prop('required', true);
            } else {
                $('#template-specific-user-container').hide();
                $('#template_user_id').prop('required', false);
            }
        });

        // Mostrar/ocultar opciones de programación
        $('#schedule').change(function() {
            if ($(this).is(':checked')) {
                $('#schedule-options').show();
                $('#scheduled_date').prop('required', true);
                $('#scheduled_time').prop('required', true);
            } else {
                $('#schedule-options').hide();
                $('#scheduled_date').prop('required', false);
                $('#scheduled_time').prop('required', false);
            }
        });

        $('#template_schedule').change(function() {
            if ($(this).is(':checked')) {
                $('#template-schedule-options').show();
                $('#template_scheduled_date').prop('required', true);
                $('#template_scheduled_time').prop('required', true);
            } else {
                $('#template-schedule-options').hide();
                $('#template_scheduled_date').prop('required', false);
                $('#template_scheduled_time').prop('required', false);
            }
        });

        // Cargar campos de plantilla
        $('#template_id').change(function() {
            const templateId = $(this).val();
            if (templateId) {
                $.ajax({
                    url: '<?= base_url('notificationsAdmin/getTemplateFields') ?>/' + templateId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#template-fields').html(response.html);
                        } else {
                            $('#template-fields').html('<p class="text-danger">Error al cargar los campos de la plantilla.</p>');
                        }
                    },
                    error: function() {
                        $('#template-fields').html('<p class="text-danger">Error al cargar los campos de la plantilla.</p>');
                    }
                });
            } else {
                $('#template-fields').html('<p class="text-muted">Selecciona una plantilla para ver los campos requeridos.</p>');
            }
        });
    });
</script>