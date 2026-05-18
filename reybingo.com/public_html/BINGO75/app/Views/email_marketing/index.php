<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('emailMarketing/create') ?>" class="btn btn-primary">
                        <i class="mdi mdi-plus-circle me-1"></i> Nueva Campaña
                    </a>
                </div>
                <h4 class="page-title">Email Marketing</h4>
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

    <!-- Email Stats Overview -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-lighten rounded-circle text-primary">
                                <i class="mdi mdi-email-outline font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $stats['total_campaigns'] ?? 0 ?></h3>
                            <p class="text-muted mb-1">Campañas Totales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-lighten rounded-circle text-success">
                                <i class="mdi mdi-email-open-outline font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $stats['open_rate'] ?? '0' ?>%</h3>
                            <p class="text-muted mb-1">Tasa de Apertura</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-info-lighten rounded-circle text-info">
                                <i class="mdi mdi-cursor-pointer font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $stats['click_rate'] ?? '0' ?>%</h3>
                            <p class="text-muted mb-1">Tasa de Clics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning-lighten rounded-circle text-warning">
                                <i class="mdi mdi-account-group font-24"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="text-dark mt-1"><?= $stats['total_subscribers'] ?? 0 ?></h3>
                            <p class="text-muted mb-1">Suscriptores</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Campañas de Email</h4>
                    <p class="text-muted">Listado de todas las campañas de email marketing.</p>

                    <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#all-campaigns" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                Todas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#draft-campaigns" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Borradores
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#scheduled-campaigns" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Programadas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#sent-campaigns" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                Enviadas
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="all-campaigns">
                            <div class="table-responsive">
                                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="campaigns-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Asunto</th>
                                            <th>Segmento</th>
                                            <th>Programada</th>
                                            <th>Enviada</th>
                                            <th>Estado</th>
                                            <th>Estadísticas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($campaigns as $campaign) : ?>
                                            <tr>
                                                <td><?= $campaign['id'] ?></td>
                                                <td><?= $campaign['name'] ?></td>
                                                <td><?= $campaign['subject'] ?></td>
                                                <td>
                                                    <?php
                                                    switch ($campaign['segment']) {
                                                        case 'all':
                                                            echo 'Todos los usuarios';
                                                            break;
                                                        case 'pro':
                                                            echo 'Usuarios Pro';
                                                            break;
                                                        case 'non_pro':
                                                            echo 'Usuarios No Pro';
                                                            break;
                                                        default:
                                                            if (strpos($campaign['segment'], 'level_') === 0) {
                                                                echo 'Nivel ' . substr($campaign['segment'], 6);
                                                            } else {
                                                                echo $campaign['segment'];
                                                            }
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= $campaign['scheduled_at'] ? date('d/m/Y H:i', strtotime($campaign['scheduled_at'])) : '-' ?>
                                                </td>
                                                <td>
                                                    <?= $campaign['sent_at'] ? date('d/m/Y H:i', strtotime($campaign['sent_at'])) : '-' ?>
                                                </td>
                                                <td>
                                                    <?php if ($campaign['status'] == 'draft') : ?>
                                                        <span class="badge bg-secondary">Borrador</span>
                                                    <?php elseif ($campaign['status'] == 'scheduled') : ?>
                                                        <span class="badge bg-info">Programada</span>
                                                    <?php elseif ($campaign['status'] == 'sending') : ?>
                                                        <span class="badge bg-warning">Enviando</span>
                                                    <?php elseif ($campaign['status'] == 'sent') : ?>
                                                        <span class="badge bg-success">Enviada</span>
                                                    <?php elseif ($campaign['status'] == 'cancelled') : ?>
                                                        <span class="badge bg-danger">Cancelada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($campaign['status'] == 'sent') : ?>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $campaign['stats']['open_rate'] ?>%;" aria-valuenow="<?= $campaign['stats']['open_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <span class="ms-2"><?= $campaign['stats']['open_rate'] ?>%</span>
                                                        </div>
                                                    <?php else : ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group dropdown">
                                                        <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item" href="<?= base_url('emailMarketing/view/' . $campaign['id']) ?>">
                                                                <i class="mdi mdi-eye me-1"></i> Ver Detalles
                                                            </a>
                                                            <?php if ($campaign['status'] == 'draft') : ?>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/edit/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-pencil me-1"></i> Editar
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-send me-1"></i> Enviar Ahora
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/schedule/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-calendar me-1"></i> Programar
                                                                </a>
                                                            <?php elseif ($campaign['status'] == 'scheduled') : ?>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-send me-1"></i> Enviar Ahora
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/cancel/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-cancel me-1"></i> Cancelar
                                                                </a>
                                                            <?php elseif ($campaign['status'] == 'sent') : ?>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/duplicate/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-content-copy me-1"></i> Duplicar
                                                                </a>
                                                            <?php endif; ?>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger" href="<?= base_url('emailMarketing/delete/' . $campaign['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta campaña?');">
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

                        <div class="tab-pane" id="draft-campaigns">
                            <div class="table-responsive">
                                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="draft-campaigns-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Asunto</th>
                                            <th>Segmento</th>
                                            <th>Creada</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($campaigns as $campaign) : ?>
                                            <?php if ($campaign['status'] == 'draft') : ?>
                                                <tr>
                                                    <td><?= $campaign['id'] ?></td>
                                                    <td><?= $campaign['name'] ?></td>
                                                    <td><?= $campaign['subject'] ?></td>
                                                    <td>
                                                        <?php
                                                        switch ($campaign['segment']) {
                                                            case 'all':
                                                                echo 'Todos los usuarios';
                                                                break;
                                                            case 'pro':
                                                                echo 'Usuarios Pro';
                                                                break;
                                                            case 'non_pro':
                                                                echo 'Usuarios No Pro';
                                                                break;
                                                            default:
                                                                if (strpos($campaign['segment'], 'level_') === 0) {
                                                                    echo 'Nivel ' . substr($campaign['segment'], 6);
                                                                } else {
                                                                    echo $campaign['segment'];
                                                                }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($campaign['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="mdi mdi-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/view/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-eye me-1"></i> Ver Detalles
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/edit/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-pencil me-1"></i> Editar
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-send me-1"></i> Enviar Ahora
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/schedule/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-calendar me-1"></i> Programar
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="<?= base_url('emailMarketing/delete/' . $campaign['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta campaña?');">
                                                                    <i class="mdi mdi-delete me-1"></i> Eliminar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="scheduled-campaigns">
                            <div class="table-responsive">
                                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="scheduled-campaigns-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Asunto</th>
                                            <th>Segmento</th>
                                            <th>Programada</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($campaigns as $campaign) : ?>
                                            <?php if ($campaign['status'] == 'scheduled') : ?>
                                                <tr>
                                                    <td><?= $campaign['id'] ?></td>
                                                    <td><?= $campaign['name'] ?></td>
                                                    <td><?= $campaign['subject'] ?></td>
                                                    <td>
                                                        <?php
                                                        switch ($campaign['segment']) {
                                                            case 'all':
                                                                echo 'Todos los usuarios';
                                                                break;
                                                            case 'pro':
                                                                echo 'Usuarios Pro';
                                                                break;
                                                            case 'non_pro':
                                                                echo 'Usuarios No Pro';
                                                                break;
                                                            default:
                                                                if (strpos($campaign['segment'], 'level_') === 0) {
                                                                    echo 'Nivel ' . substr($campaign['segment'], 6);
                                                                } else {
                                                                    echo $campaign['segment'];
                                                                }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($campaign['scheduled_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="mdi mdi-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/view/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-eye me-1"></i> Ver Detalles
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-send me-1"></i> Enviar Ahora
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/cancel/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-cancel me-1"></i> Cancelar
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="<?= base_url('emailMarketing/delete/' . $campaign['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta campaña?');">
                                                                    <i class="mdi mdi-delete me-1"></i> Eliminar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="sent-campaigns">
                            <div class="table-responsive">
                                <table class="table table-centered table-striped dt-responsive nowrap w-100" id="sent-campaigns-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Asunto</th>
                                            <th>Segmento</th>
                                            <th>Enviada</th>
                                            <th>Estadísticas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($campaigns as $campaign) : ?>
                                            <?php if ($campaign['status'] == 'sent') : ?>
                                                <tr>
                                                    <td><?= $campaign['id'] ?></td>
                                                    <td><?= $campaign['name'] ?></td>
                                                    <td><?= $campaign['subject'] ?></td>
                                                    <td>
                                                        <?php
                                                        switch ($campaign['segment']) {
                                                            case 'all':
                                                                echo 'Todos los usuarios';
                                                                break;
                                                            case 'pro':
                                                                echo 'Usuarios Pro';
                                                                break;
                                                            case 'non_pro':
                                                                echo 'Usuarios No Pro';
                                                                break;
                                                            default:
                                                                if (strpos($campaign['segment'], 'level_') === 0) {
                                                                    echo 'Nivel ' . substr($campaign['segment'], 6);
                                                                } else {
                                                                    echo $campaign['segment'];
                                                                }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= date('d/m/Y H:i', strtotime($campaign['sent_at'])) ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $campaign['stats']['open_rate'] ?>%;" aria-valuenow="<?= $campaign['stats']['open_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <span class="ms-2"><?= $campaign['stats']['open_rate'] ?>%</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group dropdown">
                                                            <a href="javascript:void(0);" class="dropdown-toggle arrow-none btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="mdi mdi-dots-vertical"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/view/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-eye me-1"></i> Ver Detalles
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/stats/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-chart-bar me-1"></i> Estadísticas
                                                                </a>
                                                                <a class="dropdown-item" href="<?= base_url('emailMarketing/duplicate/' . $campaign['id']) ?>">
                                                                    <i class="mdi mdi-content-copy me-1"></i> Duplicar
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-danger" href="<?= base_url('emailMarketing/delete/' . $campaign['id']) ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta campaña?');">
                                                                    <i class="mdi mdi-delete me-1"></i> Eliminar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#campaigns-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
        
        $('#draft-campaigns-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
        
        $('#scheduled-campaigns-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
        
        $('#sent-campaigns-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            }
        });
    });
</script>
<?= $this->endSection() ?>