<?php
require_once 'model/PrediksiModel.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prediksiModel = new PrediksiModel();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'train_knn':
                try {
                    $k_value = (int)$_POST['k_value'];
                    
                    if ($k_value < 1 || $k_value > 20) {
                        throw new Exception("Nilai K harus antara 1-20");
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
                        <div class="col-sm-12">
                            <div class="home-tab">
                                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                                    <div>
                                        <div class="btn-wrapper">
                                            <h3 class="mb-0">Prediksi Risiko KNN</h3>
                                            <p class="text-muted">Training dan prediksi risiko menggunakan K-Nearest Neighbors</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mt-3" role="alert">
                                        <?php echo htmlspecialchars($message); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                        <i class="mdi mdi-alert-circle"></i>
                                        <?= $_SESSION['error']; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['train_info'])): ?>
                                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                                        <i class="mdi mdi-information"></i>
                                        <strong>Informasi Training:</strong><br>
                                        K Value: <?= $_SESSION['train_info']['k_value'] ?> | 
                                        Total Penyulang: <?= $_SESSION['train_info']['total_penyulang'] ?> | 
                                        Data Training: <?= $_SESSION['train_info']['training_data_count'] ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['train_info']); ?>
                                <?php endif; ?>

                                <div class="row mt-4">
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo $statistics['total_prediksi']; ?></h4>
                                                        <p class="mb-0">Total Prediksi</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-brain"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo $statistics['tinggi_count']; ?></h4>
                                                        <p class="mb-0">Risiko Tinggi (<?php echo $statistics['tinggi_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-alert"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo $statistics['sedang_count']; ?></h4>
                                                        <p class="mb-0">Risiko Sedang (<?php echo $statistics['sedang_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-alert-outline"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo $statistics['rendah_count']; ?></h4>
                                                        <p class="mb-0">Risiko Rendah (<?php echo $statistics['rendah_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-check-circle"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Training KNN</h5>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="k_value">Nilai K (1-20):</label>
                                                                <input type="number" class="form-control" id="k_value" name="k_value" 
                                                                       value="<?php echo $statistics['last_k_value']; ?>" min="1" max="20" required>
                                                                <small class="form-text text-muted">Masukkan nilai K untuk algoritma KNN</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <div class="d-flex">
                                                                    <button type="submit" name="action" value="train_knn" class="btn btn-primary me-2" 
                                                                            onclick="return confirm('Mulai training KNN? Data prediksi sebelumnya akan dihapus.')">
                                                                        <i class="mdi mdi-play"></i> Train KNN
                                                                    </button>
                                                                    <button type="submit" name="action" value="reset_data" class="btn btn-danger" 
                                                                            onclick="return confirm('Reset semua data prediksi? Tindakan ini tidak dapat dibatalkan!')">
                                                                        <i class="mdi mdi-refresh"></i> Reset Data
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <p class="text-muted mt-2">
                                                    Training akan menggunakan data training dari split data untuk memprediksi tingkat risiko per penyulang.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($confusionMatrix['matrix'])): ?>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Confusion Matrix</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered text-center">
                                                        <thead class="table-dark">
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
                                                                    <th class="table-dark"><?php echo $actualClass; ?></th>
                                                                    <?php foreach ($confusionMatrix['classes'] as $predictedClass): ?>
                                                                        <td class="<?php echo $actualClass === $predictedClass ? 'table-success' : ''; ?>">
                                                                            <?php echo $confusionMatrix['matrix'][$actualClass][$predictedClass]; ?>
                                                                        </td>
                                                                    <?php endforeach; ?>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Metrik Evaluasi</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>Accuracy: <?php echo $confusionMatrix['accuracy']; ?>%</strong>
                                                </div>
                                                
                                                <h6>Precision:</h6>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($confusionMatrix['precision'] as $class => $value): ?>
                                                        <li><?php echo $class; ?>: <?php echo $value; ?>%</li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                
                                                <h6>Recall:</h6>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($confusionMatrix['recall'] as $class => $value): ?>
                                                        <li><?php echo $class; ?>: <?php echo $value; ?>%</li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                
                                                <h6>F1-Score:</h6>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($confusionMatrix['f1_score'] as $class => $value): ?>
                                                        <li><?php echo $class; ?>: <?php echo $value; ?>%</li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Hasil Prediksi (<?php echo count($prediksiData); ?> data)</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Nama Penyulang</th>
                                                                <th>Tingkat Risiko</th>
                                                                <th>Nilai Risiko</th>
                                                                <th>Total Kegiatan</th>
                                                                <th>K Value</th>
                                                                <th>Tanggal Prediksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($prediksiData)): ?>
                                                                <?php foreach ($prediksiData as $row): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($row['id_prediksi']); ?></td>
                                                                        <td><?php echo htmlspecialchars($row['nama_penyulang']); ?></td>
                                                                        <td>
                                                                            <?php
                                                                            $badgeClass = '';
                                                                            switch (strtolower($row['tingkat_risiko'])) {
                                                                                case 'tinggi':
                                                                                    $badgeClass = 'bg-danger';
                                                                                    break;
                                                                                case 'sedang':
                                                                                    $badgeClass = 'bg-warning';
                                                                                    break;
                                                                                default:
                                                                                    $badgeClass = 'bg-success';
                                                                            }
                                                                            ?>
                                                                            <span class="badge <?php echo $badgeClass; ?>">
                                                                                <?php echo htmlspecialchars($row['tingkat_risiko']); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo number_format($row['nilai_risiko'], 2); ?></td>
                                                                        <td><?php echo number_format($row['total_kegiatan']); ?></td>
                                                                        <td>
                                                                            <span class="badge bg-info">
                                                                                K=<?php echo $row['k_value']; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_prediksi'])); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="7" class="text-center">
                                                                        <div class="py-4">
                                                                            <i class="mdi mdi-brain mdi-48px text-muted"></i>
                                                                            <p class="text-muted mt-2">Belum ada data prediksi. Jalankan training KNN terlebih dahulu.</p>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                
                                                <?php if (!empty($prediksiData)): ?>
                                                <div class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <small class="text-muted">
                                                                Rata-rata Risk Score: <?php echo $statistics['avg_risk_score']; ?> | 
                                                                Rata-rata Total Kegiatan: <?php echo $statistics['avg_total_kegiatan']; ?>
                                                            </small>
                                                        </div>
                                                        <div class="col-md-6 text-end">
                                                            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                                                                <i class="mdi mdi-refresh"></i> Refresh
                                                            </button>
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
    
    <script>
        function refreshData() {
            location.reload();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    if (alert && alert.parentNode) {
                        alert.classList.remove('show');
                        setTimeout(function() {
                            if (alert && alert.parentNode) {
                                alert.remove();
                            }
                        }, 150);
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>