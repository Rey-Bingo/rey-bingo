<div class="row mt-4">
    <div class="col-md-12">
        <h4><?= translate('roulette statistics'); ?></h4>
    </div>
</div>
    
<div class="card mt-2">
    <div class="row">
        <div class="col-12 col-md">
            <div class="card bingo-bg-primary text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total roulette amount'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($total_roulette, 2); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-success text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total transactions'); ?></h5>
                    <h2 class="card-text"><?= number_format($count_roulette); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-info text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total cartons'); ?></h5>
                    <h2 class="card-text"><?= number_format($total_cartons_roulette); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-warning text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('avg. price per carton'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($avg_price_per_carton, 2); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 col-md">
        <div class="card">
            <div class="card-header pt-3">
                <h5><?= translate('top 10 roulette users'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="topRoulettesChart" width="100%" height="100px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 col-md">
        <div class="card">
            <div class="card-header pt-3">
                <h5><?= translate('recent roulette transactions'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th><?= translate('user'); ?></th>
                                <th class="text-center"><?= translate('cartons'); ?></th>
                                <th class="text-center"><?= translate('price'); ?></th>
                                <th class="text-center"><?= translate('amount'); ?></th>
                                <th><?= translate('date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentRoulettes)): ?>
                                <?php 
                                    $recentRoulettes = array_slice($roulettes, 0, 15);
                                    foreach ($recentRoulettes as $roulette): 
                                ?>
                                <tr>
                                    <td><?= $roulette['firstname'] . ' ' . $roulette['lastname']; ?></td>
                                    <td class="text-center"><?= number_format($roulette['cartons']); ?></td>
                                    <td class="text-center"><?= systemGet('currency'); ?> <?= number_format($roulette['price'], 2); ?></td>
                                    <td class="text-center"><?= systemGet('currency'); ?> <?= number_format($roulette['amount'], 2); ?></td>
                                    <td><?= translate_date($roulette['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center"><?= translate('no referrals found'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Gráfico de top usuarios de ruleta
    var userLabels = [];
    var userData = [];
    var userCartons = [];

    <?php foreach ($roulette_by_user as $userId => $stats): ?>
        userLabels.push('<?= $stats['username']; ?>');
        userData.push(<?= $stats['amount']; ?>);
        userCartons.push(<?= $stats['cartons']; ?>);
    <?php endforeach; ?>

    var ctxTop = document.getElementById('topRoulettesChart').getContext('2d');
    var topRoulettesChart = new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: userLabels,
            datasets: [
                {
                    label: '<?= translate('amount'); ?>',
                    data: userData,
                    backgroundColor: '#007bff',
                    yAxisID: 'y'
                },
                {
                    label: '<?= translate('cartons'); ?>',
                    data: userCartons,
                    backgroundColor: '#28a745',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: '<?= translate('amount'); ?>'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: '<?= translate('cartons'); ?>'
                    }
                }
            }
        }
    });
</script>
