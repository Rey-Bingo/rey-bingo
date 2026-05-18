<div class="row mt-4">
    <div class="col-md-12">
        <h4><?= translate('transactions statistics'); ?></h4>
    </div>
</div>
    
<div class="card mt-2">
    <div class="row">
        <div class="col-12 col-md">
            <div class="card bingo-bg-primary text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total in deposits'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($total_deposits ?? 0, 2); ?></h2>
                    <span class="float-end"><?= number_format($count_deposits ?? 0); ?> <?= translate('transactions'); ?></span>
                </div>
            </div>
        </div>
            
        <div class="col-12 col-md">
            <div class="card bingo-bg-success text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total in retires'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format($total_retires ?? 0, 2); ?></h2>
                    <span class="float-end"><?= number_format($count_retires ?? 0); ?> <?= translate('transactions'); ?></span>
                </div>
            </div>
        </div>
            
        <div class="col-12 col-md">
            <div class="card bingo-bg-info text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('net balance'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format(($total_deposits ?? 0) - ($total_retires ?? 0), 2); ?></h2>
                    <span class="float-end"><?= ($count_deposits ?? 0) - ($count_retires ?? 0); ?> <?= translate('transactions'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('deposits by method'); ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($deposits_by_method)): ?>
                    <canvas id="depositsByMethodChart" width="100%" height="100px"></canvas>
                <?php else: ?>
                    <p class="text-center"><?= translate('no data available'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('deposits by bank'); ?></h5>
            </div>
            <div class="card-body">
                <?php if (!empty($deposits_by_bank)): ?>
                    <canvas id="depositsByBankChart" width="100%" height="100px"></canvas>
                <?php else: ?>
                    <p class="text-center"><?= translate('no data available'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('recent deposits'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th><?= translate('user'); ?></th>
                                <th><?= translate('method'); ?></th>
                                <th><?= translate('bank'); ?></th>
                                <th><?= translate('reference'); ?></th>
                                <th><?= translate('amount'); ?></th>
                                <th><?= translate('date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($deposits)): ?>
                                <?php 
                                $recentDeposits = array_slice($deposits, 0, 10);
                                foreach ($recentDeposits as $deposit): 
                                ?>
                                <tr>
                                    <td><?= $deposit['firstname'] . ' ' . $deposit['lastname']; ?></td>
                                    <td><?= translate($deposit['method'] ?? 'unknown'); ?></td>
                                    <td><?= $deposit['bank'] ?? 'N/A'; ?></td>
                                    <td><?= $deposit['reference'] ?? 'N/A'; ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($deposit['amount'] ?? 0, 2); ?></td>
                                    <td><?= translate_date($deposit['date']) ?? 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center"><?= translate('no deposits found'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('recent retires'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th><?= translate('player'); ?></th>
                                <th><?= translate('Bank'); ?></th>
                                <th><?= translate('account'); ?></th>
                                <th><?= translate('amount'); ?></th>
                                <th><?= translate('date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($retires)): ?>
                                <?php 
                                $recentRetires = array_slice($retires, 0, 10);
                                foreach ($recentRetires as $retire): 
                                ?>
                                <tr>
                                    <td><?= $retire['firstname'] . ' ' . $retire['lastname']; ?></td>
                                    <td><?= $retire['bank'] ?? 'N/A'; ?></td>
                                    <td><?= $retire['account'] ?? 'N/A'; ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($retire['amount'] ?? 0, 2); ?></td>
                                    <td><?= translate_date($retire['created_at']) ?? 'N/A'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center"><?= translate('no retires found'); ?></td>
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
    // Gráfico de depósitos por método
    <?php if (!empty($deposits_by_method)): ?>
    var methodLabels = [];
    var methodData = [];
    
    <?php foreach ($deposits_by_method as $method => $amount): ?>
        methodLabels.push('<?= esc(translate($method)); ?>');
        methodData.push(<?= $amount; ?>);
    <?php endforeach; ?>
    
    var methodCanvas = document.getElementById('depositsByMethodChart');
    if (methodCanvas) {
        var ctxMethod = methodCanvas.getContext('2d');
        var depositsByMethodChart = new Chart(ctxMethod, {
            type: 'pie',
            data: {
                labels: methodLabels,
                datasets: [{
                    data: methodData,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6c757d', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false,
                        text: ''
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let total = context.chart._metasets[0].total;
                                let value = context.parsed;
                                let percentage = ((value / total) * 100).toFixed(1) + '%';
                                return context.label + ': ' + value + ' (' + percentage + ')';
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
    
    // Gráfico de depósitos por banco
    <?php if (!empty($deposits_by_bank)): ?>
    var bankLabels = [];
    var bankData = [];
    
    <?php foreach ($deposits_by_bank as $bank => $amount): ?>
        bankLabels.push('<?= esc($bank); ?>');
        bankData.push(<?= $amount; ?>);
    <?php endforeach; ?>
    
    var bankCanvas = document.getElementById('depositsByBankChart');
    if (bankCanvas) {
        var ctxBank = bankCanvas.getContext('2d');
        var depositsByBankChart = new Chart(ctxBank, {
            type: 'doughnut',
            data: {
                labels: bankLabels,
                datasets: [{
                    data: bankData,
                    backgroundColor: [
                        '#20c997', '#e83e8c', '#6610f2', '#d63384', '#0dcaf0',
                        '#198754', '#0d6efd', '#6c757d', '#fd7e14', '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false,
                        text: ''
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let total = context.chart._metasets[0].total;
                                let value = context.parsed;
                                let percentage = ((value / total) * 100).toFixed(1) + '%';
                                return context.label + ': ' + value + ' (' + percentage + ')';
                            }
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>
</script>