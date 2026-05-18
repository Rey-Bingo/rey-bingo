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
                <h4 class="page-title">Otorgar Puntos</h4>
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
                    <h4 class="header-title">Otorgar Puntos a Usuario</h4>
                    <p class="text-muted">Utiliza este formulario para otorgar puntos manualmente a un usuario específico.</p>

                    <form action="<?= base_url('levels/awardPoints') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuario <span class="text-danger">*</span></label>
                            <select class="form-select select2" id="user_id" name="user_id" required>
                                <option value="">Seleccionar usuario...</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                        <?= $user['username'] ?> (<?= $user['firstname'] ?> <?= $user['lastname'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Selecciona el usuario al que deseas otorgar puntos.</div>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Cantidad de Puntos <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount" name="amount" value="<?= old('amount') ?>" min="1" required>
                            <div class="form-text">Cantidad de puntos que deseas otorgar al usuario.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description') ?></textarea>
                            <div class="form-text">Motivo por el que se otorgan los puntos. Esta descripción será visible para el usuario.</div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Otorgar Puntos</button>
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
                    <h4 class="header-title">Otorgar Puntos a Múltiples Usuarios</h4>
                    <p class="text-muted">Utiliza este formulario para otorgar puntos a múltiples usuarios a la vez.</p>

                    <form action="<?= base_url('levels/awardPointsBulk') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="user_group" class="form-label">Grupo de Usuarios <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_group" name="user_group" required>
                                <option value="">Seleccionar grupo...</option>
                                <option value="all">Todos los usuarios</option>
                                <option value="pro">Usuarios Pro</option>
                                <option value="non_pro">Usuarios No Pro</option>
                                <option value="level_1">Nivel: Principiante</option>
                                <option value="level_2">Nivel: Aficionado</option>
                                <option value="level_3">Nivel: Entusiasta</option>
                                <option value="level_4">Nivel: Experto</option>
                                <option value="level_5">Nivel: Maestro</option>
                                <option value="level_6">Nivel: Leyenda</option>
                                <option value="active">Usuarios activos (últimos 7 días)</option>
                                <option value="inactive">Usuarios inactivos (más de 30 días)</option>
                            </select>
                            <div class="form-text">Selecciona el grupo de usuarios al que deseas otorgar puntos.</div>
                        </div>

                        <div class="mb-3">
                            <label for="amount_bulk" class="form-label">Cantidad de Puntos <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount_bulk" name="amount_bulk" min="1" required>
                            <div class="form-text">Cantidad de puntos que deseas otorgar a cada usuario del grupo seleccionado.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description_bulk" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description_bulk" name="description_bulk" rows="3" required></textarea>
                            <div class="form-text">Motivo por el que se otorgan los puntos. Esta descripción será visible para los usuarios.</div>
                        </div>

                        <div class="alert alert-warning">
                            <h5><i class="mdi mdi-alert"></i> Advertencia</h5>
                            <p>Esta acción otorgará puntos a múltiples usuarios a la vez. Asegúrate de seleccionar el grupo correcto y la cantidad adecuada de puntos.</p>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">Otorgar Puntos a Grupo</button>
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
                    <h4 class="header-title">Historial de Puntos Otorgados</h4>
                    <p class="text-muted">Registro de los últimos puntos otorgados manualmente.</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped dt-responsive nowrap w-100" id="points-history-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Puntos</th>
                                    <th>Descripción</th>
                                    <th>Otorgado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Aquí se mostrarían los registros de puntos otorgados manualmente -->
                                <tr>
                                    <td>15/09/2025 18:30</td>
                                    <td>usuario123</td>
                                    <td>+500</td>
                                    <td>Premio por participación en evento especial</td>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <td>14/09/2025 14:15</td>
                                    <td>jugador456</td>
                                    <td>+200</td>
                                    <td>Compensación por error técnico</td>
                                    <td>admin</td>
                                </tr>
                                <tr>
                                    <td>12/09/2025 10:45</td>
                                    <td>bingomaster</td>
                                    <td>+1000</td>
                                    <td>Premio por ganar torneo mensual</td>
                                    <td>admin</td>
                                </tr>
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
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Seleccionar usuario...",
            allowClear: true
        });

        // Initialize DataTable
        $('#points-history-table').DataTable({
            responsive: true,
            language: {
                paginate: {
                    previous: "<i class='mdi mdi-chevron-left'>",
                    next: "<i class='mdi mdi-chevron-right'>"
                },
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                lengthMenu: "Mostrar _MENU_ registros",
                search: "Buscar:",
                emptyTable: "No hay registros disponibles",
                zeroRecords: "No se encontraron coincidencias"
            },
            order: [[0, 'desc']],
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
            }
        });
    });
</script>
<?= $this->endSection() ?>