<div class="row mt-4">
    <div class="col-md-12">
        <h4><?= translate('referrals statistics'); ?></h4>
    </div>
</div>
    
<div class="card mt-2">
    <div class="row">
        <div class="col-12 col-md">
            <div class="card bingo-bg-primary text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total referrals'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($count_referrals); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-success text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total referral amount'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($total_referrals, 2); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-info text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('avg. amount per referral'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($count_referrals > 0 ? $total_referrals / $count_referrals : 0, 2); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 col-md">
        <div class="card">
            <div class="card-header pt-3">
                <h5><?= translate('top 10 referrers'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="topReferrersChart" width="100%" height="100px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 col-md">
        <div class="card">
            <div class="card-header pt-3">
                <h5><?= translate('top referrers details'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th><?= translate('Name'); ?></th>
                                <th><?= translate('username'); ?></th>
                                <th><?= translate('referrals count'); ?></th>
                                <th><?= translate('total amount'); ?></th>
                                <th><?= translate('avg. per referral'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($referrals_by_referrer)): ?>
                                <?php foreach ($referrals_by_referrer as $userId => $stats): ?>
                                <tr>
                                    <td><?= $stats['firstname'] . ' ' . $stats['lastname']; ?></td>
                                    <td><?= $stats['username']; ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($stats['count']); ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($stats['amount'], 2); ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($stats['count'] > 0 ? $stats['amount'] / $stats['count'] : 0, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center"><?= translate('no referrals found'); ?>
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
    // Gráfico de top referidores
    var referrerLabels = [];
    var referrerCounts = [];
    var referrerAmounts = [];
    
    <?php foreach ($referrals_by_referrer as $userId => $stats): ?>
        referrerLabels.push('<?= $stats['username']; ?>');
        referrerCounts.push(<?= $stats['count']; ?>);
        referrerAmounts.push(<?= $stats['amount']; ?>);
    <?php endforeach; ?>
    
    var ctxTop = document.getElementById('topReferrersChart').getContext('2d');
    var topReferrersChart = new Chart(ctxTop, {
        type: 'bar',
        data: {
            labels: referrerLabels,
            datasets: [
                {
                    label: '<?= translate('number of referrals'); ?>',
                    data: referrerCounts,
                    backgroundColor: '#007bff',
                    yAxisID: 'y'
                },
                {
                    label: '<?= translate('amount'); ?>',
                    data: referrerAmounts,
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
                        text: '<?= translate('number of referrals'); ?>'
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
                        text: '<?= translate('amount'); ?>'
                    }
                }
            }
        }
    });
</script>
