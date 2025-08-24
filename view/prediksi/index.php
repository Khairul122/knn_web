<?php
require_once 'model/PrediksiModel.php';

$message = '';
$messageType = '';
$optimalKResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prediksiModel = new PrediksiModel();

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'find_optimal_k':
                try {
                    $maxK = isset($_POST['max_k']) ? (int)$_POST['max_k'] : 15;

                    if ($maxK < 3 || $maxK > 25) {
                        throw new Exception("Max K harus antara 3-25");
                    }

                    $result = $prediksiModel->findOptimalK($maxK);
                    if ($result['success']) {
                        $optimalKResult = $result;
                        $message = "K optimal ditemukan: K=" . $result['optimal_k'] . " dengan accuracy " . $result['best_accuracy'] . "%";
                        $messageType = 'success';
                        $_SESSION['optimal_k'] = $result['optimal_k'];
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
                break;

            case 'train_knn':
                try {
                    $k_value = (int)$_POST['k_value'];

                    if ($k_value < 1 || $k_value > 25) {
                        throw new Exception("Nilai K harus antara 1-25");
                    }

                    $result = $prediksiModel->trainKNN($k_value);
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
                        $_SESSION['train_info'] = [
                            'total_penyulang' => $result['total_penyulang'],
                            'k_value' => $result['k_value'],
                            'training_data_count' => $result['training_data_count']
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
                    $result = $prediksiModel->resetAllPrediksiData();
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
                        unset($_SESSION['train_info']);
                        unset($_SESSION['optimal_k']);
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
                break;
        }
    }
}

try {
    $prediksiModel = new PrediksiModel();
    $statistics = $prediksiModel->getPrediksiStatistics();
    $prediksiData = $prediksiModel->getPrediksiData();
    $confusionMatrix = $prediksiModel->getConfusionMatrix();
    $riskDistribution = $prediksiModel->getRiskDistributionData();
    $accuracyTrend = $prediksiModel->getAccuracyTrendData();
    $topRiskPenyulang = $prediksiModel->getTopRiskPenyulang(5);
} catch (Exception $e) {
    $statistics = [
        'total_prediksi' => 0,
        'tinggi_count' => 0,
        'sedang_count' => 0,
        'rendah_count' => 0,
        'tinggi_percentage' => 0,
        'sedang_percentage' => 0,
        'rendah_percentage' => 0,
        'avg_risk_score' => 0,
        'avg_total_kegiatan' => 0,
        'last_k_value' => 3
    ];
    $prediksiData = [];
    $confusionMatrix = ['matrix' => [], 'accuracy' => 0];
    $riskDistribution = [];
    $accuracyTrend = [];
    $topRiskPenyulang = [];
    if (empty($message)) {
        $message = 'Error loading data: ' . $e->getMessage();
        $messageType = 'error';
    }
}

include('view/template/header.php');
?>

<body class="with-welcome-text">
    <div class="container-scroller">
        <?php include 'view/template/navbar.php'; ?>
        <div class="container-fluid page-body-wrapper">
            <?php include 'view/template/setting_panel.php'; ?>
            <?php include 'view/template/sidebar.php'; ?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-12">
                            <div class="home-tab">
                                <!-- Header Section -->
                                <div class="d-sm-flex align-items-center justify-content-between border-bottom mb-4">
                                    <div class="py-3">
                                        <h3 class="mb-1 fw-bold text-primary">Prediksi Risiko KNN</h3>
                                        <p class="text-muted mb-0">Training dan prediksi risiko menggunakan K-Nearest Neighbors dengan Cross Validation dan Saran Perbaikan</p>
                                    </div>
                                    <div class="d-none d-sm-block">
                                        <span class="badge bg-info fs-6 px-3 py-2">
                                            <i class="mdi mdi-database me-1"></i>
                                            <?php echo $statistics['total_prediksi']; ?> Data
                                        </span>
                                    </div>
                                </div>

                                <!-- Alert Messages -->
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-<?php echo $messageType === 'success' ? 'check-circle' : 'alert-circle'; ?> me-2 fs-5"></i>
                                            <div><?php echo htmlspecialchars($message); ?></div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-alert-circle me-2 fs-5"></i>
                                            <div><?= $_SESSION['error']; ?></div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['train_info'])): ?>
                                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-information me-2 fs-5"></i>
                                            <div>
                                                <strong>Informasi Training:</strong><br>
                                                <small>K Value: <?= $_SESSION['train_info']['k_value'] ?> | 
                                                       Total Penyulang: <?= $_SESSION['train_info']['total_penyulang'] ?> | 
                                                       Data Training: <?= $_SESSION['train_info']['training_data_count'] ?></small>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['train_info']); ?>
                                <?php endif; ?>

                                <!-- Statistics Cards -->
                                <div class="row g-3 mb-4">
                                    <div class="col-xl-3 col-md-6">
                                        <div class="card bg-primary text-white h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-1 fw-bold"><?php echo $statistics['total_prediksi']; ?></h4>
                                                        <p class="mb-0 small">Total Prediksi</p>
                                                    </div>
                                                    <div class="icon-box bg-primary bg-opacity-25 rounded-circle p-3">
                                                        <i class="mdi mdi-brain fs-2"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card bg-danger text-white h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-1 fw-bold"><?php echo $statistics['tinggi_count']; ?></h4>
                                                        <p class="mb-0 small">Risiko Tinggi (<?php echo $statistics['tinggi_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon-box bg-danger bg-opacity-25 rounded-circle p-3">
                                                        <i class="mdi mdi-alert fs-2"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card bg-warning text-white h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-1 fw-bold"><?php echo $statistics['sedang_count']; ?></h4>
                                                        <p class="mb-0 small">Risiko Sedang (<?php echo $statistics['sedang_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon-box bg-warning bg-opacity-25 rounded-circle p-3">
                                                        <i class="mdi mdi-alert-outline fs-2"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-3 col-md-6">
                                        <div class="card bg-success text-white h-100 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h4 class="mb-1 fw-bold"><?php echo $statistics['rendah_count']; ?></h4>
                                                        <p class="mb-0 small">Risiko Rendah (<?php echo $statistics['rendah_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon-box bg-success bg-opacity-25 rounded-circle p-3">
                                                        <i class="mdi mdi-check-circle fs-2"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charts Section -->
                                <?php if (!empty($riskDistribution)): ?>
                                    <div class="row g-3 mb-4">
                                        <div class="col-xl-6 col-lg-12">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                                    <h5 class="card-title mb-0 d-flex align-items-center">
                                                        <i class="mdi mdi-chart-pie text-primary me-2"></i>
                                                        <span>Distribusi Tingkat Risiko</span>
                                                    </h5>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <canvas id="riskDistributionChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl-6 col-lg-12">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                                    <h5 class="card-title mb-0 d-flex align-items-center">
                                                        <i class="mdi mdi-chart-line text-primary me-2"></i>
                                                        <span>Trend Akurasi per K Value</span>
                                                    </h5>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <canvas id="accuracyTrendChart" height="250"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Top Risk Penyulang -->
                                <?php if (!empty($topRiskPenyulang)): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card shadow-sm">
                                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                                    <h5 class="card-title mb-0 d-flex align-items-center">
                                                        <i class="mdi mdi-alert text-danger me-2"></i>
                                                        <span>Top 5 Penyulang Risiko Tinggi</span>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-3"><i class="mdi mdi-factory me-1"></i> Nama Penyulang</th>
                                                                    <th><i class="mdi mdi-chart-line me-1"></i> Nilai Risiko</th>
                                                                    <th><i class="mdi mdi-counter me-1"></i> Total Kegiatan</th>
                                                                    <th class="pe-3"><i class="mdi mdi-priority-high me-1"></i> Prioritas</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($topRiskPenyulang as $index => $penyulang): ?>
                                                                    <tr class="border-top">
                                                                        <td class="ps-3">
                                                                            <strong class="text-dark"><?php echo htmlspecialchars($penyulang['nama_penyulang']); ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-danger fs-6 px-2 py-1">
                                                                                <?php echo number_format($penyulang['nilai_risiko'], 2); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-warning text-dark px-2 py-1">
                                                                                <?php echo $penyulang['total_kegiatan']; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td class="pe-3">
                                                                            <?php
                                                                            $priorityLabels = ['Urgent', 'High', 'Medium', 'Normal', 'Low'];
                                                                            $priorityColors = ['danger', 'warning', 'info', 'primary', 'secondary'];
                                                                            ?>
                                                                            <span class="badge bg-<?php echo $priorityColors[$index] ?? 'secondary'; ?> px-2 py-1">
                                                                                <?php echo $priorityLabels[$index] ?? 'Low'; ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- KNN Configuration Section -->
                                <div class="row g-3 mb-4">
                                    <!-- Find Optimal K -->
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                                <h5 class="card-title mb-0 d-flex align-items-center">
                                                    <i class="mdi mdi-magnify text-info me-2"></i>
                                                    <span>Pencarian K Optimal</span>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST" id="form-optimal-k">
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-8">
                                                            <div class="form-group mb-2">
                                                                <label for="max_k" class="form-label small fw-semibold">Maximum K untuk Testing:</label>
                                                                <input type="number" class="form-control form-control-sm" id="max_k" name="max_k"
                                                                    value="15" min="3" max="25" required>
                                                                <div class="form-text small">Cross Validation akan test K dari 3 sampai nilai maksimum (ganjil saja)</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit" name="action" value="find_optimal_k"
                                                                class="btn btn-info w-100 btn-sm" id="btn-find-k">
                                                                <i class="mdi mdi-magnify me-1"></i> Cari K Optimal
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>

                                                <?php if ($optimalKResult && $optimalKResult['success']): ?>
                                                    <div class="mt-4 pt-3 border-top">
                                                        <div class="alert alert-success py-2 mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="mdi mdi-check-circle me-2"></i>
                                                                <div>
                                                                    <strong class="small">Hasil Cross Validation:</strong><br>
                                                                    <span class="small">K Optimal: <strong class="text-primary"><?= $optimalKResult['optimal_k'] ?></strong>
                                                                    dengan accuracy <strong class="text-success"><?= $optimalKResult['best_accuracy'] ?>%</strong></span><br>
                                                                    <small class="text-muted">Data Size: <?= $optimalKResult['data_size'] ?> samples | 5-Fold Cross Validation</small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                                            <i class="mdi mdi-chart-line me-2 text-muted"></i>
                                                            <span>Detail Performa Setiap K:</span>
                                                        </h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th class="small"><i class="mdi mdi-pound"></i> K Value</th>
                                                                        <th class="small"><i class="mdi mdi-percent"></i> Accuracy (%)</th>
                                                                        <th class="small"><i class="mdi mdi-star"></i> Status</th>
                                                                        <th class="small"><i class="mdi mdi-chart-bar"></i> Grafik</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    $maxAcc = max(array_column($optimalKResult['all_results'], 'accuracy'));
                                                                    foreach ($optimalKResult['all_results'] as $kResult):
                                                                        $isOptimal = $kResult['k'] == $optimalKResult['optimal_k'];
                                                                        $barWidth = ($kResult['accuracy'] / $maxAcc) * 100;
                                                                    ?>
                                                                        <tr class="<?= $isOptimal ? 'table-success' : '' ?>">
                                                                            <td>
                                                                                <span class="badge <?= $isOptimal ? 'bg-success' : 'bg-secondary'; ?>">
                                                                                    <?= $kResult['k'] ?>
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <strong class="small"><?= $kResult['accuracy'] ?>%</strong>
                                                                            </td>
                                                                            <td>
                                                                                <?php if ($isOptimal): ?>
                                                                                    <span class="badge bg-success small">
                                                                                        <i class="mdi mdi-star me-1"></i> Optimal
                                                                                    </span>
                                                                                <?php else: ?>
                                                                                    <span class="text-muted small">-</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td>
                                                                                <div class="progress" style="height: 20px;">
                                                                                    <div class="progress-bar <?= $isOptimal ? 'bg-success' : 'bg-info' ?>"
                                                                                        style="width: <?= $barWidth ?>%">
                                                                                        <span class="small"><?= $kResult['accuracy'] ?>%</span>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Train KNN -->
                                    <div class="col-xl-6 col-lg-12">
                                        <div class="card shadow-sm h-100">
                                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                                <h5 class="card-title mb-0 d-flex align-items-center">
                                                    <i class="mdi mdi-play text-primary me-2"></i>
                                                    <span>Training KNN</span>
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST">
                                                    <div class="row g-2 align-items-end">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-2">
                                                                <label for="k_value" class="form-label small fw-semibold">Nilai K:</label>
                                                                <input type="number" class="form-control form-control-sm" id="k_value" name="k_value"
                                                                    value="<?php echo isset($_SESSION['optimal_k']) ? $_SESSION['optimal_k'] : 5; ?>" min="1" max="25" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-grid gap-2">
                                                                <button type="submit" name="action" value="train_knn" class="btn btn-primary btn-sm"
                                                                    onclick="return confirm('Mulai training KNN? Data prediksi sebelumnya akan dihapus.')">
                                                                    <i class="mdi mdi-play me-1"></i> Train KNN
                                                                </button>
                                                                <button type="submit" name="action" value="reset_data" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Reset semua data prediksi? Tindakan ini tidak dapat dibatalkan!')">
                                                                    <i class="mdi mdi-refresh me-1"></i> Reset Data
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div class="mt-3 p-3 bg-light rounded small">
                                                    <h6 class="fw-semibold d-flex align-items-center mb-2">
                                                        <i class="mdi mdi-information text-muted me-2"></i>
                                                        <span>Informasi Training:</span>
                                                    </h6>
                                                    <p class="mb-0 text-muted">
                                                        Training akan menggunakan data dari split_data (tipe='train') untuk
                                                        memprediksi tingkat risiko pada data test, kemudian diagregasi per penyulang dengan saran perbaikan.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confusion Matrix -->
                                <?php if (!empty($confusionMatrix['matrix'])): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card shadow-sm">
                                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                                    <h5 class="card-title mb-0 d-flex align-items-center">
                                                        <i class="mdi mdi-matrix text-primary me-2"></i>
                                                        <span>Confusion Matrix</span>
                                                    </h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered text-center">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Actual / Predicted</th>
                                                                    <?php foreach ($confusionMatrix['classes'] as $class): ?>
                                                                        <th><?php echo $class; ?></th>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($confusionMatrix['classes'] as $actualClass): ?>
                                                                    <tr>
                                                                        <th class="table-light"><?php echo $actualClass; ?></th>
                                                                        <?php foreach ($confusionMatrix['classes'] as $predictedClass): ?>
                                                                            <td class="<?php echo $actualClass === $predictedClass ? 'table-success fw-bold' : ''; ?>">
                                                                                <?php echo $confusionMatrix['matrix'][$actualClass][$predictedClass]; ?>
                                                                            </td>
                                                                        <?php endforeach; ?>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="mt-2 text-muted small">
                                                        <i class="mdi mdi-information me-1"></i>
                                                        Diagonal hijau menunjukkan prediksi yang benar
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Prediction Results -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                    <h5 class="card-title mb-2 mb-md-0 d-flex align-items-center">
                                                        <i class="mdi mdi-table text-primary me-2"></i>
                                                        <span>Hasil Prediksi & Saran Perbaikan</span>
                                                        <span class="badge bg-info ms-2"><?php echo count($prediksiData); ?> data</span>
                                                    </h5>
                                                    <?php if (!empty($prediksiData)): ?>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-success btn-sm" onclick="exportToPDF()">
                                                                <i class="mdi mdi-file-pdf me-1"></i> Export PDF
                                                            </button>
                                                            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                                                                <i class="mdi mdi-refresh"></i>
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <?php if (!empty($prediksiData)): ?>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                                                <input type="text" class="form-control" id="searchTable"
                                                                    placeholder="Cari nama penyulang...">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select class="form-select form-select-sm" id="filterRisiko">
                                                                <option value="">Semua Tingkat Risiko</option>
                                                                <option value="TINGGI">Risiko Tinggi</option>
                                                                <option value="SEDANG">Risiko Sedang</option>
                                                                <option value="RENDAH">Risiko Rendah</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle" id="table-prediksi">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="ps-3"><i class="mdi mdi-pound"></i> ID</th>
                                                                <th><i class="mdi mdi-factory"></i> Nama Penyulang</th>
                                                                <th><i class="mdi mdi-alert"></i> Tingkat Risiko</th>
                                                                <th><i class="mdi mdi-chart-line"></i> Nilai Risiko</th>
                                                                <th><i class="mdi mdi-counter"></i> Total Kegiatan</th>
                                                                <th><i class="mdi mdi-settings"></i> K Value</th>
                                                                <th><i class="mdi mdi-lightbulb"></i> Saran Perbaikan</th>
                                                                <th class="pe-3"><i class="mdi mdi-calendar"></i> Tanggal Prediksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($prediksiData)): ?>
                                                                <?php foreach ($prediksiData as $row): ?>
                                                                    <tr class="border-top">
                                                                        <td class="ps-3 small"><?php echo htmlspecialchars($row['id_prediksi']); ?></td>
                                                                        <td>
                                                                            <strong class="text-dark"><?php echo htmlspecialchars($row['nama_penyulang']); ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                            $badgeClass = '';
                                                                            $iconClass = '';
                                                                            switch (strtolower($row['tingkat_risiko'])) {
                                                                                case 'tinggi':
                                                                                    $badgeClass = 'bg-danger';
                                                                                    $iconClass = 'mdi-alert';
                                                                                    break;
                                                                                case 'sedang':
                                                                                    $badgeClass = 'bg-warning text-dark';
                                                                                    $iconClass = 'mdi-alert-outline';
                                                                                    break;
                                                                                default:
                                                                                    $badgeClass = 'bg-success';
                                                                                    $iconClass = 'mdi-check-circle';
                                                                            }
                                                                            ?>
                                                                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                                                                <i class="mdi <?php echo $iconClass; ?> me-1"></i>
                                                                                <?php echo htmlspecialchars($row['tingkat_risiko']); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <strong class="text-dark"><?php echo number_format($row['nilai_risiko'], 2); ?></strong>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-info px-2 py-1">
                                                                                <?php echo number_format($row['total_kegiatan']); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge bg-secondary px-2 py-1">
                                                                                K=<?php echo $row['k_value']; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                                onclick="showSuggestion(
                                                                                    '<?php echo htmlspecialchars($row['nama_penyulang']); ?>', 
                                                                                    '<?php echo htmlspecialchars($row['saran_perbaikan']); ?>',
                                                                                    '<?php echo htmlspecialchars($row['tingkat_risiko']); ?>'
                                                                                )">
                                                                                <i class="mdi mdi-eye me-1"></i> Lihat
                                                                            </button>
                                                                        </td>
                                                                        <td class="pe-3">
                                                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['tanggal_prediksi'])); ?></small>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center py-5">
                                                                        <div class="py-4">
                                                                            <i class="mdi mdi-brain mdi-48px text-muted opacity-50"></i>
                                                                            <h5 class="text-muted mt-3 fw-semibold">Belum Ada Data Prediksi</h5>
                                                                            <p class="text-muted mb-3">
                                                                                Untuk memulai, silakan:
                                                                            </p>
                                                                            <ol class="text-muted text-start d-inline-block text-center text-md-start">
                                                                                <li class="mb-1">Lakukan split data terlebih dahulu</li>
                                                                                <li class="mb-1">Cari K optimal menggunakan Cross Validation</li>
                                                                                <li>Jalankan training KNN dengan K optimal</li>
                                                                            </ol>
                                                                            <div class="mt-3 d-flex flex-column flex-md-row gap-2 justify-content-center">
                                                                                <a href="?page=cluster" class="btn btn-outline-primary">
                                                                                    <i class="mdi mdi-database me-1"></i> Split Data
                                                                                </a>
                                                                                <button class="btn btn-info" onclick="document.getElementById('btn-find-k').click()">
                                                                                    <i class="mdi mdi-magnify me-1"></i> Cari K Optimal
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <?php if (!empty($prediksiData)): ?>
                                                    <div class="mt-4 p-3 bg-light rounded small">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-8">
                                                                <h6 class="fw-semibold mb-2 d-flex align-items-center">
                                                                    <i class="mdi mdi-chart-pie text-muted me-2"></i>
                                                                    <span>Ringkasan Statistik:</span>
                                                                </h6>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-1">
                                                                            <strong>Rata-rata Risk Score:</strong> <?php echo $statistics['avg_risk_score']; ?>
                                                                        </div>
                                                                        <div>
                                                                            <strong>Rata-rata Total Kegiatan:</strong> <?php echo $statistics['avg_total_kegiatan']; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-1">
                                                                            <strong>Distribusi Risiko:</strong>
                                                                        </div>
                                                                        <div>
                                                                            Tinggi: <?php echo $statistics['tinggi_count']; ?> (<?php echo $statistics['tinggi_percentage']; ?>%) |
                                                                            Sedang: <?php echo $statistics['sedang_count']; ?> (<?php echo $statistics['sedang_percentage']; ?>%) |
                                                                            Rendah: <?php echo $statistics['rendah_count']; ?> (<?php echo $statistics['rendah_percentage']; ?>%)
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 text-md-end">
                                                                <div class="text-muted">
                                                                    <div class="mb-1">
                                                                        <i class="mdi mdi-clock-outline me-1"></i>
                                                                        Last Updated: <?php echo date('d/m/Y H:i'); ?>
                                                                    </div>
                                                                    <div>
                                                                        <i class="mdi mdi-database me-1"></i>
                                                                        Total Records: <?php echo count($prediksiData); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggestion Modal -->
    <div class="modal fade" id="suggestionModal" tabindex="-1" aria-labelledby="suggestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center" id="suggestionModalLabel">
                        <i class="mdi mdi-lightbulb text-warning me-2"></i>
                        <span>Saran Perbaikan</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info py-2">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-factory me-2"></i>
                                    <div>
                                        <h6 class="mb-0">Penyulang: <span id="modalPenyulang"></span></h6>
                                        <span class="badge mt-1" id="modalRisikoBadge"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="mdi mdi-clipboard-list text-primary me-2"></i>
                                <span>Rekomendasi Tindakan:</span>
                            </h6>
                            <div id="modalSuggestions" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="printSuggestion()">
                        <i class="mdi mdi-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <script>
        // CSS untuk styling tambahan
        const style = document.createElement('style');
        style.textContent = `
            .icon-box {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .card {
                border: 1px solid #e9ecef;
                transition: transform 0.2s ease-in-out;
            }
            .card:hover {
                transform: translateY(-2px);
            }
            .table th {
                font-weight: 600;
                font-size: 0.875rem;
            }
            .table td {
                font-size: 0.875rem;
                vertical-align: middle;
            }
            .badge {
                font-size: 0.75rem;
            }
            .progress {
                border-radius: 10px;
            }
            .progress-bar {
                font-size: 0.7rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        `;
        document.head.appendChild(style);

        let riskDistributionChart;
        let accuracyTrendChart;

        function initCharts() {
            <?php if (!empty($riskDistribution)): ?>
                const riskData = <?php echo json_encode($riskDistribution); ?>;
                const riskLabels = riskData.map(item => item.label);
                const riskValues = riskData.map(item => item.value);
                const riskColors = riskLabels.map(label => {
                    switch (label) {
                        case 'TINGGI': return '#dc3545';
                        case 'SEDANG': return '#ffc107';
                        case 'RENDAH': return '#198754';
                        default: return '#6c757d';
                    }
                });

                const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
                if (riskDistributionChart) riskDistributionChart.destroy();
                
                riskDistributionChart = new Chart(riskCtx, {
                    type: 'doughnut',
                    data: {
                        labels: riskLabels,
                        datasets: [{
                            data: riskValues,
                            backgroundColor: riskColors,
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            <?php if (!empty($accuracyTrend)): ?>
                const trendData = <?php echo json_encode($accuracyTrend); ?>;
                const trendLabels = trendData.map(item => 'K=' + item.k_value);
                const trendValues = trendData.map(item => item.accuracy);

                const trendCtx = document.getElementById('accuracyTrendChart').getContext('2d');
                if (accuracyTrendChart) accuracyTrendChart.destroy();
                
                accuracyTrendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendLabels,
                        datasets: [{
                            label: 'Accuracy (%)',
                            data: trendValues,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#0d6efd',
                            pointBorderColor: '#fff',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            <?php endif; ?>
        }

        function showSuggestion(penyulang, suggestions, riskLevel) {
            document.getElementById('modalPenyulang').textContent = penyulang;

            const badge = document.getElementById('modalRisikoBadge');
            badge.textContent = riskLevel;
            badge.className = 'badge ';

            switch (riskLevel.toUpperCase()) {
                case 'TINGGI':
                    badge.className += 'bg-danger';
                    break;
                case 'SEDANG':
                    badge.className += 'bg-warning text-dark';
                    break;
                case 'RENDAH':
                    badge.className += 'bg-success';
                    break;
            }

            if (!suggestions || suggestions.trim() === '') {
                document.getElementById('modalSuggestions').innerHTML = 
                    '<div class="alert alert-warning py-2 mb-0"><i class="mdi mdi-alert me-2"></i>Tidak ada saran perbaikan tersedia.</div>';
            } else {
                const suggestionArray = suggestions.split(';').filter(s => s.trim() !== '');
                let suggestionHtml = '<div class="list-group list-group-flush">';
                suggestionArray.forEach((suggestion, index) => {
                    suggestionHtml += `
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-primary me-2 mt-1">${index + 1}</span>
                                <div class="flex-grow-1">
                                    <p class="mb-0 text-dark">${suggestion.trim()}</p>
                                </div>
                            </div>
                        </div>`;
                });
                suggestionHtml += '</div>';
                document.getElementById('modalSuggestions').innerHTML = suggestionHtml;
            }

            const suggestionModal = new bootstrap.Modal(document.getElementById('suggestionModal'));
            suggestionModal.show();
        }

        function printSuggestion() {
            const penyulang = document.getElementById('modalPenyulang').textContent;
            const riskLevel = document.getElementById('modalRisikoBadge').textContent;
            const suggestions = document.getElementById('modalSuggestions').innerHTML;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Saran Perbaikan - ${penyulang}</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 25px; line-height: 1.6; }
                            .header { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                            .badge { padding: 5px 10px; border-radius: 5px; color: white; font-size: 12px; }
                            .bg-danger { background-color: #dc3545; }
                            .bg-warning { background-color: #ffc107; color: black; }
                            .bg-success { background-color: #198754; }
                            .list-group-item { padding: 8px 0; border: none; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>SARAN PERBAIKAN SISTEM PREDIKSI RISIKO KNN</h2>
                            <p><strong>Penyulang:</strong> ${penyulang}</p>
                            <p><strong>Tingkat Risiko:</strong> <span class="badge bg-${riskLevel.toLowerCase() === 'tinggi' ? 'danger' : riskLevel.toLowerCase() === 'sedang' ? 'warning' : 'success'}">${riskLevel}</span></p>
                            <p><strong>Tanggal:</strong> ${new Date().toLocaleDateString('id-ID')}</p>
                        </div>
                        <div>
                            <h3>Rekomendasi Tindakan:</h3>
                            ${suggestions}
                        </div>
                        <div style="margin-top: 40px;">
                            <p><em>Generated by Sistem Prediksi Risiko KNN with Cross Validation</em></p>
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        function refreshData() {
            location.reload();
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // PDF implementation here...
            // (Tetap sama dengan implementasi sebelumnya)
        }

        document.addEventListener('DOMContentLoaded', function() {
            initCharts();

            // Search and filter functionality
            const searchInput = document.getElementById('searchTable');
            const filterSelect = document.getElementById('filterRisiko');
            const tableBody = document.querySelector('#table-prediksi tbody');
            
            if (tableBody) {
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                
                function filterTable() {
                    const searchText = searchInput ? searchInput.value.toLowerCase() : '';
                    const filterValue = filterSelect ? filterSelect.value : '';

                    rows.forEach(row => {
                        if (row.cells.length <= 1) return;

                        const penyulangName = row.cells[1].textContent.toLowerCase();
                        const risikoLevel = row.cells[2].textContent.trim();

                        const matchesSearch = penyulangName.includes(searchText);
                        const matchesFilter = !filterValue || risikoLevel.includes(filterValue);

                        row.style.display = matchesSearch && matchesFilter ? '' : 'none';
                    });
                }

                if (searchInput) searchInput.addEventListener('input', filterTable);
                if (filterSelect) filterSelect.addEventListener('change', filterTable);
            }

            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });

            // Form submission loading state
            const formOptimalK = document.getElementById('form-optimal-k');
            if (formOptimalK) {
                formOptimalK.addEventListener('submit', function(e) {
                    const button = document.getElementById('btn-find-k');
                    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';
                    button.disabled = true;
                });
            }

            // Responsive table adjustments
            function handleResponsiveTable() {
                const table = document.getElementById('table-prediksi');
                if (table && window.innerWidth < 768) {
                    table.classList.add('table-sm');
                } else if (table) {
                    table.classList.remove('table-sm');
                }
            }

            handleResponsiveTable();
            window.addEventListener('resize', handleResponsiveTable);
        });
    </script>
</body>
</html>