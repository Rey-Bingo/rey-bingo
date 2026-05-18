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
                    <?php if ($campaign['status'] == 'draft') : ?>
                        <a href="<?= base_url('emailMarketing/edit/' . $campaign['id']) ?>" class="btn btn-primary">
                            <i class="mdi mdi-pencil me-1"></i> Editar
                        </a>
                    <?php endif; ?>
                </div>
                <h4 class="page-title">Detalles de la Campaña</h4>
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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Información de la Campaña</h4>
                    
                    <div class="mb-4">
                        <h5><?= $campaign['subject'] ?></h5>
                        <p class="text-muted mb-2">Nombre interno: <?= $campaign['name'] ?></p>
                        <div class="d-flex align-items-center">
                            <span class="me-3">
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
                            </span>
                            <span class="text-muted">
                                Creada: <?= date('d/m/Y H:i', strtotime($campaign['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Segmento</h5>
                        <p>
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
                            <span class="text-muted ms-2">(<?= $recipientCount ?? 0 ?> destinatarios)</span>
                        </p>
                    </div>

                    <?php if ($campaign['status'] == 'scheduled') : ?>
                        <div class="mb-4">
                            <h5>Programación</h5>
                            <p>
                                Programada para: <?= date('d/m/Y H:i', strtotime($campaign['scheduled_at'])) ?>
                                <span class="ms-2">
                                    <?php
                                    $now = time();
                                    $scheduled = strtotime($campaign['scheduled_at']);
                                    $diff = $scheduled - $now;
                                    
                                    if ($diff > 0) {
                                        $days = floor($diff / (60 * 60 * 24));
                                        $hours = floor(($diff % (60 * 60 * 24)) / (60 * 60));
                                        $minutes = floor(($diff % (60 * 60)) / 60);
                                        
                                        if ($days > 0) {
                                            echo "(En $days días, $hours horas y $minutes minutos)";
                                        } else if ($hours > 0) {
                                            echo "(En $hours horas y $minutes minutos)";
                                        } else {
                                            echo "(En $minutes minutos)";
                                        }
                                    } else {
                                        echo "(Pendiente de envío)";
                                    }
                                    ?>
                                </span>
                            </p>
                            <div class="mt-2">
                                <a href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>" class="btn btn-sm btn-primary me-1">
                                    <i class="mdi mdi-send me-1"></i> Enviar Ahora
                                </a>
                                <a href="<?= base_url('emailMarketing/cancel/' . $campaign['id']) ?>" class="btn btn-sm btn-danger">
                                    <i class="mdi mdi-cancel me-1"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($campaign['status'] == 'sent') : ?>
                        <div class="mb-4">
                            <h5>Información de Envío</h5>
                            <p>Enviada: <?= date('d/m/Y H:i', strtotime($campaign['sent_at'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h5>Vista Previa del Contenido</h5>
                        <div class="border p-3 rounded">
                            <?= $campaign['content'] ?>
                        </div>
                    </div>

                    <?php if ($campaign['status'] == 'draft') : ?>
                        <div class="text-end">
                            <a href="<?= base_url('emailMarketing/edit/' . $campaign['id']) ?>" class="btn btn-secondary me-1">
                                <i class="mdi mdi-pencil me-1"></i> Editar
                            </a>
                            <a href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>" class="btn btn-primary me-1">
                                <i class="mdi mdi-send me-1"></i> Enviar Ahora
                            </a>
                            <a href="<?= base_url('emailMarketing/schedule/' . $campaign['id']) ?>" class="btn btn-info">
                                <i class="mdi mdi-calendar me-1"></i> Programar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <?php if ($campaign['status'] == 'sent') : ?>
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Estadísticas</h4>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tasa de apertura</span>
                                <span><?= $stats['open_rate'] ?>%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $stats['open_rate'] ?>%;" aria-valuenow="<?= $stats['open_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Tasa de clics</span>
                                <span><?= $stats['click_rate'] ?>%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= $stats['click_rate'] ?>%;" aria-valuenow="<?= $stats['click_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h3 class="text-dark mt-1"><?= $stats['delivered'] ?></h3>
                                        <p class="text-muted mb-0">Entregados</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-dark mt-1"><?= $stats['opened'] ?></h3>
                                    <p class="text-muted mb-0">Abiertos</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h3 class="text-dark mt-1"><?= $stats['clicked'] ?></h3>
                                        <p class="text-muted mb-0">Clics</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="text-dark mt-1"><?= $stats['bounced'] ?></h3>
                                    <p class="text-muted mb-0">Rebotados</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <a href="<?= base_url('emailMarketing/stats/' . $campaign['id']) ?>" class="btn btn-primary">
                                <i class="mdi mdi-chart-bar me-1"></i> Ver Estadísticas Detalladas
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Acciones</h4>
                    
                    <div class="list-group">
                        <?php if ($campaign['status'] == 'draft') : ?>
                            <a href="<?= base_url('emailMarketing/edit/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Editar</h5>
                                    <i class="mdi mdi-pencil"></i>
                                </div>
                                <p class="mb-1">Modificar el contenido o configuración de la campaña.</p>
                            </a>
                            <a href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Enviar Ahora</h5>
                                    <i class="mdi mdi-send"></i>
                                </div>
                                <p class="mb-1">Enviar la campaña inmediatamente a todos los destinatarios.</p>
                            </a>
                            <a href="<?= base_url('emailMarketing/schedule/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Programar</h5>
                                    <i class="mdi mdi-calendar"></i>
                                </div>
                                <p class="mb-1">Programar la campaña para enviarla en una fecha y hora específica.</p>
                            </a>
                        <?php elseif ($campaign['status'] == 'scheduled') : ?>
                            <a href="<?= base_url('emailMarketing/send/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Enviar Ahora</h5>
                                    <i class="mdi mdi-send"></i>
                                </div>
                                <p class="mb-1">Enviar la campaña inmediatamente en lugar de esperar a la fecha programada.</p>
                            </a>
                            <a href="<?= base_url('emailMarketing/cancel/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Cancelar</h5>
                                    <i class="mdi mdi-cancel"></i>
                                </div>
                                <p class="mb-1">Cancelar el envío programado de la campaña.</p>
                            </a>
                        <?php elseif ($campaign['status'] == 'sent') : ?>
                            <a href="<?= base_url('emailMarketing/stats/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Estadísticas</h5>
                                    <i class="mdi mdi-chart-bar"></i>
                                </div>
                                <p class="mb-1">Ver estadísticas detalladas de la campaña.</p>
                            </a>
                            <a href="<?= base_url('emailMarketing/duplicate/' . $campaign['id']) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">Duplicar</h5>
                                    <i class="mdi mdi-content-copy"></i>
                                </div>
                                <p class="mb-1">Crear una copia de esta campaña para modificarla y enviarla nuevamente.</p>
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('emailMarketing/delete/' . $campaign['id']) ?>" class="list-group-item list-group-item-action list-group-item-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar esta campaña?');">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">Eliminar</h5>
                                <i class="mdi mdi-delete"></i>
                            </div>
                            <p class="mb-1">Eliminar permanentemente esta campaña.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>