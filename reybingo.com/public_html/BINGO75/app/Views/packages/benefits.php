<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('packages') ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Volver
                    </a>
                </div>
                <h4 class="page-title">Beneficios Pro</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Tus Beneficios Pro</h4>
                    <p class="text-muted">Disfruta de estos beneficios exclusivos como miembro Pro.</p>

                    <?php if (!empty($activePackage)) : ?>
                        <div class="alert alert-success mb-4">
                            <h5><i class="mdi mdi-crown"></i> Paquete Activo: <?= $activePackage['name'] ?></h5>
                            <p>Válido hasta: <?= date('d/m/Y H:i', strtotime($activePackage['end_date'])) ?> (<?= ceil((strtotime($activePackage['end_date']) - time()) / (60 * 60 * 24)) ?> días restantes)</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-primary-lighten rounded-circle text-primary">
                                                    <i class="mdi mdi-ticket-percent font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Descuento en Cartones</h5>
                                                <p class="mb-0 text-muted">Ahorra en cada compra</p>
                                            </div>
                                        </div>
                                        <p>Disfruta de un <strong><?= $activePackage['discount_percentage'] ?>% de descuento</strong> en todas tus compras de cartones de bingo.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-success-lighten rounded-circle text-success">
                                                    <i class="mdi mdi-cards font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Cartones Gratis</h5>
                                                <p class="mb-0 text-muted">Juega sin costo adicional</p>
                                            </div>
                                        </div>
                                        <p>Recibe <strong><?= $activePackage['free_cartons'] ?> cartones gratis</strong> durante la vigencia de tu suscripción Pro.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-info-lighten rounded-circle text-info">
                                                    <i class="mdi mdi-star-circle font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Puntos Diarios</h5>
                                                <p class="mb-0 text-muted">Acumula más rápido</p>
                                            </div>
                                        </div>
                                        <p>Recibe <strong><?= $activePackage['daily_points'] ?> puntos adicionales</strong> cada día que inicies sesión en la plataforma.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-warning-lighten rounded-circle text-warning">
                                                    <i class="mdi mdi-gamepad-variant font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Partidas Exclusivas</h5>
                                                <p class="mb-0 text-muted">Solo para miembros Pro</p>
                                            </div>
                                        </div>
                                        <p>Accede a <strong>partidas exclusivas</strong> con premios especiales solo disponibles para usuarios Pro.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-danger-lighten rounded-circle text-danger">
                                                    <i class="mdi mdi-account-badge font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Insignia Pro</h5>
                                                <p class="mb-0 text-muted">Destaca entre los demás</p>
                                            </div>
                                        </div>
                                        <p>Muestra tu <strong>insignia Pro</strong> en tu perfil y en los chats de las partidas para destacar entre los demás jugadores.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-title bg-purple-lighten rounded-circle text-purple">
                                                    <i class="mdi mdi-headset font-24"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mt-0 mb-1">Soporte Prioritario</h5>
                                                <p class="mb-0 text-muted">Atención preferencial</p>
                                            </div>
                                        </div>
                                        <p>Recibe <strong>atención prioritaria</strong> por parte de nuestro equipo de soporte cuando necesites ayuda.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-info">
                            <h5><i class="mdi mdi-information-outline"></i> No tienes un paquete Pro activo</h5>
                            <p>Adquiere uno de nuestros paquetes Pro para disfrutar de beneficios exclusivos.</p>
                            <a href="<?= base_url('packages') ?>" class="btn btn-info mt-2">Ver Paquetes Disponibles</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">Comparativa de Paquetes</h4>
                    <p class="text-muted">Compara los diferentes paquetes Pro disponibles.</p>

                    <div class="table-responsive">
                        <table class="table table-centered table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Características</th>
                                    <th class="text-center">Usuario Básico</th>
                                    <th class="text-center">Pro Mensual</th>
                                    <th class="text-center">Pro Trimestral</th>
                                    <th class="text-center">Pro Anual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Precio</td>
                                    <td class="text-center">Gratis</td>
                                    <td class="text-center">Bs 19.99</td>
                                    <td class="text-center">Bs 49.99</td>
                                    <td class="text-center">Bs 149.99</td>
                                </tr>
                                <tr>
                                    <td>Duración</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">30 días</td>
                                    <td class="text-center">90 días</td>
                                    <td class="text-center">365 días</td>
                                </tr>
                                <tr>
                                    <td>Descuento en cartones</td>
                                    <td class="text-center">0%</td>
                                    <td class="text-center">15%</td>
                                    <td class="text-center">20%</td>
                                    <td class="text-center">25%</td>
                                </tr>
                                <tr>
                                    <td>Cartones gratis</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">20</td>
                                    <td class="text-center">100</td>
                                </tr>
                                <tr>
                                    <td>Puntos diarios</td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">10</td>
                                    <td class="text-center">15</td>
                                    <td class="text-center">20</td>
                                </tr>
                                <tr>
                                    <td>Partidas exclusivas</td>
                                    <td class="text-center"><i class="mdi mdi-close text-danger"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td>Insignia Pro</td>
                                    <td class="text-center"><i class="mdi mdi-close text-danger"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td>Soporte prioritario</td>
                                    <td class="text-center"><i class="mdi mdi-close text-danger"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                    <td class="text-center"><i class="mdi mdi-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">
                                        <a href="<?= base_url('packages') ?>" class="btn btn-sm btn-primary">Comprar</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('packages') ?>" class="btn btn-sm btn-primary">Comprar</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('packages') ?>" class="btn btn-sm btn-primary">Comprar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>