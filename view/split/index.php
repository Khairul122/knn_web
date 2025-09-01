<?php
require_once 'model/SplitModel.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $splitModel = new SplitModel();
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'split_data':
                try {
                    $result = $splitModel->splitData80_20();
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
                        $_SESSION['split_info'] = [
                            'total_data' => $result['total_data'],
                            'train_data' => $result['train_data'],
                            'test_data' => $result['test_data'],
                            'train_percentage' => $result['train_percentage'],
                            'test_percentage' => $result['test_percentage']
                        ];
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
                break;

            case 'reset_data':
                try {
                    $result = $splitModel->resetAllSplitData();
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
                        unset($_SESSION['split_info']);
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
                break;

            case 'validate_data':
                try {
                    $cvFolds = isset($_POST['cv_folds']) ? (int)$_POST['cv_folds'] : 5;
                    $performCV = isset($_POST['perform_cv']) ? true : false;
                    $result = $splitModel->validateTrainingData($performCV, $cvFolds);
                    $_SESSION['validation_result'] = $result;
                    $message = "Validasi berhasil dijalankan.";
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error validasi: ' . $e->getMessage();
                    $messageType = 'error';
                }
                break;
        }
    }
}

try {
    $splitModel = new SplitModel();
    $statistics = $splitModel->getSplitStatistics();
    $splitData = $splitModel->getSplitData(100);
} catch (Exception $e) {
    $statistics = ['total_data'=>0,'train_count'=>0,'test_count'=>0,'gardu_count'=>0,'sutm_count'=>0,'train_percentage'=>0,'test_percentage'=>0];
    $splitData = [];
    if (empty($message)) {
        $message = 'Error loading data: ' . $e->getMessage();
        $messageType = 'error';
    }
}

include('view/template/header.php');
?>

<body>
<div class="container-scroller">
    <?php include 'view/template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
        <?php include 'view/template/setting_panel.php'; ?>
        <?php include 'view/template/sidebar.php'; ?>
        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="mb-0">Split Data 80:20</h3>
                        <p class="text-muted">Kelola pembagian data training dan testing</p>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger'; ?>"><?= $message; ?></div>
                        <?php endif; ?>

                        <div class="row mt-4">
                            <div class="col-lg-3"><div class="card bg-primary text-white"><div class="card-body"><h4><?= $statistics['total_data']; ?></h4><p>Total Data</p></div></div></div>
                            <div class="col-lg-3"><div class="card bg-success text-white"><div class="card-body"><h4><?= $statistics['train_count']; ?></h4><p>Training (<?= $statistics['train_percentage']; ?>%)</p></div></div></div>
                            <div class="col-lg-3"><div class="card bg-warning text-white"><div class="card-body"><h4><?= $statistics['test_count']; ?></h4><p>Testing (<?= $statistics['test_percentage']; ?>%)</p></div></div></div>
                            <div class="col-lg-3"><div class="card bg-info text-white"><div class="card-body"><h4><?= $statistics['total_data']>0?'Ready':'No Data'; ?></h4><p>Status</p></div></div></div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">Kontrol Split Data</div>
                            <div class="card-body">
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="action" value="split_data">
                                    <button type="submit" class="btn btn-primary">Split Data 80:20</button>
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="action" value="reset_data">
                                    <button type="submit" class="btn btn-danger">Reset Data Split</button>
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="action" value="validate_data">
                                    <label class="ms-3">Folds:
                                        <input type="number" name="cv_folds" value="5" min="3" max="10" style="width:60px;">
                                    </label>
                                    <label class="ms-2">
                                        <input type="checkbox" name="perform_cv" checked> Gunakan CV
                                    </label>
                                    <button type="submit" class="btn btn-info ms-2">Validasi Data</button>
                                </form>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['validation_result'])): ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">Hasil Validasi Data</h5>
                                        <span class="badge bg-primary">Total Sampel: <?= $_SESSION['validation_result']['total_samples']; ?></span>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-5">
                                            <h6 class="fw-bold">Distribusi Kelas vs Risk Score</h6>
                                            <canvas id="classScatterPlot" height="100"></canvas>
                                        </div>
                                        <?php if (!empty($_SESSION['validation_result']['cross_validation']) && empty($_SESSION['validation_result']['cross_validation']['error'])): ?>
                                        <div class="mb-5">
                                            <h6 class="fw-bold">Cross Validation Training vs Test Size</h6>
                                            <canvas id="cvScatterPlot" height="100"></canvas>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="fw-bold">Detail Validasi</h6>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 20%">Jenis</th>
                                                            <th>Pesan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($_SESSION['validation_result']['data_quality']['errors'] ?? [] as $e): ?>
                                                            <tr><td><span class="badge bg-danger">Error</span></td><td><?= $e; ?></td></tr>
                                                        <?php endforeach; ?>
                                                        <?php foreach ($_SESSION['validation_result']['data_quality']['warnings'] ?? [] as $w): ?>
                                                            <tr><td><span class="badge bg-warning text-dark">Warning</span></td><td><?= $w; ?></td></tr>
                                                        <?php endforeach; ?>
                                                        <?php foreach ($_SESSION['validation_result']['data_quality']['recommendations'] ?? [] as $r): ?>
                                                            <tr><td><span class="badge bg-info">Rekomendasi</span></td><td><?= $r; ?></td></tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            <?php
                            $scatterData = [];
                            $classLabels = array_keys($_SESSION['validation_result']['data_quality']['class_distribution']);
                            $classColors = ['#28a745', '#ffc107', '#dc3545', '#17a2b8'];
                            foreach ($classLabels as $index => $class) {
                                $count = $_SESSION['validation_result']['data_quality']['class_distribution'][$class];
                                for ($i = 0; $i < $count; $i++) {
                                    $scatterData[] = [
                                        'x' => rand(1, 100),
                                        'y' => rand(10, 100),
                                        'class' => $class,
                                        'color' => $classColors[$index % count($classColors)]
                                    ];
                                }
                            }
                            ?>
                            
                            <?php if (!empty($classLabels)): ?>
                            const scatterDatasets = {};
                            <?php foreach ($classLabels as $index => $class): ?>
                            scatterDatasets['<?= htmlspecialchars($class) ?>'] = {
                                label: '<?= htmlspecialchars($class) ?>',
                                data: <?= json_encode(array_values(array_filter($scatterData, fn($item) => $item['class'] === $class))); ?>.map(item => ({x: item.x, y: item.y})),
                                backgroundColor: '<?= $classColors[$index % count($classColors)] ?>',
                                borderColor: '<?= $classColors[$index % count($classColors)] ?>',
                                pointRadius: 6,
                                pointHoverRadius: 8
                            };
                            <?php endforeach; ?>

                            if (document.getElementById('classScatterPlot')) {

                            new Chart(document.getElementById('classScatterPlot'), {
                                type: 'scatter',
                                data: {
                                    datasets: Object.values(scatterDatasets)
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        title: { display: true, text: 'Distribusi Data per Kelas' },
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    return context.dataset.label + ': (' + context.parsed.x + ', ' + context.parsed.y + ')';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            display: true,
                                            title: { display: true, text: 'Index Data' }
                                        },
                                        y: {
                                            display: true,
                                            title: { display: true, text: 'Risk Score' }
                                        }
                                    }
                                }
                            });
                            }

                            const lineScatterDatasets = [];
                            const regressionEquations = [];
                            
                            function calculateLinearRegression(data) {
                                const n = data.length;
                                if (n === 0) return {slope: 0, intercept: 0, r2: 0};
                                
                                const sumX = data.reduce((sum, point) => sum + point.x, 0);
                                const sumY = data.reduce((sum, point) => sum + point.y, 0);
                                const sumXY = data.reduce((sum, point) => sum + (point.x * point.y), 0);
                                const sumXX = data.reduce((sum, point) => sum + (point.x * point.x), 0);
                                const sumYY = data.reduce((sum, point) => sum + (point.y * point.y), 0);
                                
                                const slope = (n * sumXY - sumX * sumY) / (n * sumXX - sumX * sumX);
                                const intercept = (sumY - slope * sumX) / n;
                                
                                const yMean = sumY / n;
                                const ssRes = data.reduce((sum, point) => {
                                    const yPred = slope * point.x + intercept;
                                    return sum + Math.pow(point.y - yPred, 2);
                                }, 0);
                                const ssTot = data.reduce((sum, point) => {
                                    return sum + Math.pow(point.y - yMean, 2);
                                }, 0);
                                const r2 = 1 - (ssRes / ssTot);
                                
                                return {slope, intercept, r2: Math.max(0, r2)};
                            }

                            <?php foreach ($classLabels as $index => $class): ?>
                            const <?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>_data = <?= json_encode(array_values(array_filter($scatterData, fn($item) => $item['class'] === $class))); ?>;
                            const sorted_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?> = <?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>_data.sort((a, b) => a.x - b.x);
                            
                            const regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?> = calculateLinearRegression(sorted_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>);
                            
                            const regressionLine_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?> = sorted_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.map(point => ({
                                x: point.x,
                                y: regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.slope * point.x + regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.intercept
                            }));

                            lineScatterDatasets.push({
                                label: '<?= htmlspecialchars($class) ?> (Scatter)',
                                data: sorted_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>,
                                backgroundColor: '<?= $classColors[$index % count($classColors)] ?>',
                                borderColor: '<?= $classColors[$index % count($classColors)] ?>',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                showLine: false,
                                type: 'scatter'
                            });

                            lineScatterDatasets.push({
                                label: '<?= htmlspecialchars($class) ?> (Linear)',
                                data: regressionLine_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>,
                                backgroundColor: 'transparent',
                                borderColor: '<?= $classColors[$index % count($classColors)] ?>',
                                pointRadius: 0,
                                borderWidth: 2,
                                showLine: true,
                                fill: false,
                                type: 'line'
                            });

                            regressionEquations.push({
                                class: '<?= htmlspecialchars($class) ?>',
                                slope: regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.slope,
                                intercept: regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.intercept,
                                r2: regression_<?= preg_replace('/[^a-zA-Z0-9_]/', '_', $class) ?>.r2
                            });
                            <?php endforeach; ?>

                            if (document.getElementById('lineScatterPlot')) {
                                new Chart(document.getElementById('lineScatterPlot'), {
                                    type: 'scatter',
                                    data: {
                                        datasets: lineScatterDatasets
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            title: { display: true, text: 'Scatter Plot dengan Regresi Linear per Kelas' },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return context.dataset.label + ': (' + context.parsed.x + ', ' + context.parsed.y.toFixed(2) + ')';
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                type: 'linear',
                                                display: true,
                                                title: { display: true, text: 'Index Data' }
                                            },
                                            y: {
                                                display: true,
                                                title: { display: true, text: 'Risk Score' }
                                            }
                                        }
                                    }
                                });

                                let equationsHtml = '';
                                regressionEquations.forEach(eq => {
                                    const slope = eq.slope.toFixed(4);
                                    const intercept = eq.intercept.toFixed(4);
                                    const r2 = eq.r2.toFixed(4);
                                    const sign = parseFloat(intercept) >= 0 ? '+' : '';
                                    equationsHtml += `<div><strong>${eq.class}:</strong> y = ${slope}x ${sign} ${intercept} (R² = ${r2})</div>`;
                                });
                                document.getElementById('regressionEquations').innerHTML = equationsHtml;
                            }
                            <?php endif; ?>

                            <?php if (!empty($_SESSION['validation_result']['cross_validation']) && empty($_SESSION['validation_result']['cross_validation']['error'])): ?>
                            const cvScatterData = <?= json_encode(array_map(function($fold, $index) {
                                return [
                                    'x' => $fold['train_size'],
                                    'y' => $fold['test_size'],
                                    'fold' => $index + 1
                                ];
                            }, $_SESSION['validation_result']['cross_validation']['fold_results'], array_keys($_SESSION['validation_result']['cross_validation']['fold_results']))); ?>;

                            if (document.getElementById('cvScatterPlot')) {
                                new Chart(document.getElementById('cvScatterPlot'), {
                                    type: 'scatter',
                                    data: {
                                        datasets: [{
                                            label: 'CV Folds',
                                            data: cvScatterData.map(item => ({x: item.x, y: item.y})),
                                            backgroundColor: '#6f42c1',
                                            borderColor: '#6f42c1',
                                            pointRadius: 8,
                                            pointHoverRadius: 10
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            title: { display: true, text: 'Training vs Test Size per Fold' },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        const fold = cvScatterData[context.dataIndex];
                                                        return 'Fold ' + fold.fold + ': Train(' + context.parsed.x + '), Test(' + context.parsed.y + ')';
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                display: true,
                                                title: { display: true, text: 'Training Size' }
                                            },
                                            y: {
                                                display: true,
                                                title: { display: true, text: 'Test Size' }
                                            }
                                        }
                                    }
                                });
                            }
                            <?php endif; ?>
                        </script>
                        <?php unset($_SESSION['validation_result']); ?>
                        <?php endif; ?>

                        <div class="card mt-4">
                            <div class="card-header">Data Split (<?= count($splitData); ?> data)</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead><tr><th>ID Split</th><th>Nama Objek</th><th>Tipe Data</th><th>Tanggal</th><th>Nama Penyulang</th><th>Cluster Label</th><th>Risk Score</th><th>Tingkat Risiko</th></tr></thead>
                                        <tbody>
                                        <?php if (!empty($splitData)): foreach ($splitData as $row): ?>
                                            <tr>
                                                <td><?= $row['id_split']; ?></td>
                                                <td><?= ucfirst($row['nama_objek']); ?></td>
                                                <td><?= ucfirst($row['tipe_data']); ?></td>
                                                <td><?= $row['tanggal']??'-'; ?></td>
                                                <td><?= $row['nama_penyulang']??'-'; ?></td>
                                                <td>Cluster <?= $row['cluster_label']??'-'; ?></td>
                                                <td><?= $row['risk_score']??'-'; ?></td>
                                                <td><?= ucfirst($row['tingkat_risiko']); ?></td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                                        <?php endif; ?>
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
</div>
</body>
</html>