<div class="row mt-4">
    <div class="col">
        <h4><?= translate('Games Statistics'); ?></h4>
    </div>
</div>

<div class="card mt-2">
    <div class="row">
        <div class="col-12 col-md">
            <div class="card bingo-bg-primary text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total games'); ?></h5>
                    <h2 class="card-text"><?= number_format(count($games)); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-success text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('cartons sold'); ?></h5>
                    <h2 class="card-text"><?= number_format(array_sum(array_column($games, 'cartons'))); ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md">
            <div class="card bingo-bg-info text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total players'); ?></h5>
                    <h2 class="card-text"><?= number_format(array_sum(array_column($games, 'players'))); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md">
            <div class="card bingo-bg-orange text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total accumulated in games'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format(array_sum(array_column($games, 'accumulated')), 2); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md">
            <div class="card bingo-bg-danger text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total in awards'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format(array_sum(array_column($games, 'total')), 2); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-12 col-md">
            <div class="card bingo-bg-purple text-white m-2">
                <div class="card-body">
                    <h5 class="card-title"><?= translate('total gaming winnings'); ?></h5>
                    <h2 class="card-text"><?= systemGet('currency'); ?> <?= number_format(array_sum(array_column($games, 'earnings')), 2); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('games by status'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="gamesByRoomChart" width="100%" height="100px"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('games by type'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="gamesByStatusChartDoughnut" width="100%" height="100px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-md">
        <div class="card m-2">
            <div class="card-header pt-3">
                <h5><?= translate('Games List'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?= translate('room'); ?></th>
                                <th><?= translate('description'); ?></th>
                                <th><?= translate('date'); ?></th>
                                <th><?= translate('price'); ?></th>
                                <th class="text-center"><?= translate('cartons'); ?></th>
                                <th class="text-center"><?= translate('players'); ?></th>
                                <th><?= translate('total award'); ?></th>
                                <th><?= translate('status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($games)) : ?>
                                <?php 
                                    usort($games, function($a, $b) {
                                        return $b['id'] - $a['id'];
                                    });
                                    
                                    $limitedGames = array_slice($games, 0, 5);
                                    foreach ($limitedGames as $game): 
                                ?>
                                <tr>
                                    <td><?= $game['room_name']; ?></td>
                                    <td><?= $game['description']; ?></td>
                                    <td><?= esc(translate_day($game['date'] . ' ' . $game['time'])) ?> <br /> <?= esc(translate_date($game['date'])) ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($game['price'], 2); ?></td>
                                    <td class="text-center"><?= number_format($game['cartons']); ?></td>
                                    <td class="text-center"><?= number_format($game['players']); ?></td>
                                    <td><?= systemGet('currency'); ?> <?= number_format($game['total'], 2) ?></td>
                                    <td><?= $game['status']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center"><?= translate('no data available'); ?></td>
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
    // Gráfico de juegos por sala
    var ctxRoom = document.getElementById('gamesByRoomChart');
    if (ctxRoom) {
        var roomLabels = [];
        var roomData = [];
        
        <?php foreach ($games_by_room as $room): ?>
            roomLabels.push(' <?= addslashes($room['name']); ?>');
            roomData.push(<?= $room['count']; ?>);
        <?php endforeach; ?>
        
        var gamesByRoomChart = new Chart(ctxRoom, {
            type: 'pie',
            data: {
                labels: roomLabels,
                datasets: [{
                    data: roomData,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6c757d', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c',
                        '#6610f2', '#d63384', '#0dcaf0', '#198754', '#0d6efd'
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
    
    // Gráfico de juegos por estado
    var ctxStatus = document.getElementById('gamesByStatusChartDoughnut');
    if (ctxStatus) {
        var gamesByStatusChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: [
                    ' <?= translate('unstarted'); ?>',
                    ' <?= translate('initiated'); ?>',
                    ' <?= translate('finished'); ?>',
                    ' <?= translate('earring'); ?>'
                ],
                datasets: [{
                    label: '',
                    data: [
                        <?= isset($games_by_status['unstarted']) ? $games_by_status['unstarted'] : 0 ?>,
                        <?= isset($games_by_status['initiated']) ? $games_by_status['initiated'] : 0 ?>,
                        <?= isset($games_by_status['finished']) ? $games_by_status['finished'] : 0 ?>,
                        <?= isset($games_by_status['earring']) ? $games_by_status['earring'] : 0 ?>
                    ],
                    backgroundColor: [
                        '#17a2b8',  // info - unstarted
                        '#007bff',  // primary - initiated
                        '#28a745',  // success - finished
                        '#ffc107'   // warning - earring
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
</script>
