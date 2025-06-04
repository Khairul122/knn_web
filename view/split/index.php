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
        }
    }
}

if (isset($_GET['action'])) {
    $splitModel = new SplitModel();
    
    switch ($_GET['action']) {
        case 'exportTrainData':
            try {
                $trainData = $splitModel->getTrainData();
                
                if (empty($trainData)) {
                    $_SESSION['error'] = 'Tidak ada data training untuk diekspor';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                
                $filename = 'train_data_' . date('Y-m-d_H-i-s') . '.csv';
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                
                $headers = ['ID Split', 'ID Data Pemeliharaan', 'Nama Objek', 'Tipe Data', 'Tanggal', 'Nama Penyulang', 'Cluster Label', 'Risk Score', 'Tingkat Risiko'];
                fputcsv($output, $headers);
                
                foreach ($trainData as $row) {
                    fputcsv($output, [
                        $row['id_split'],
                        $row['id_data_pemeliharaan'],
                        $row['nama_objek'],
                        $row['tipe_data'],
                        $row['tanggal'],
                        $row['nama_penyulang'],
                        $row['cluster_label'],
                        $row['risk_score'],
                        $row['tingkat_risiko']
                    ]);
                }
                
                fclose($output);
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Export gagal: ' . $e->getMessage();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }
            break;
            
        case 'exportTestData':
            try {
                $testData = $splitModel->getTestData();
                
                if (empty($testData)) {
                    $_SESSION['error'] = 'Tidak ada data testing untuk diekspor';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                }
                
                $filename = 'test_data_' . date('Y-m-d_H-i-s') . '.csv';
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');
                
                $headers = ['ID Split', 'ID Data Pemeliharaan', 'Nama Objek', 'Tipe Data', 'Tanggal', 'Nama Penyulang', 'Cluster Label', 'Risk Score', 'Tingkat Risiko'];
                fputcsv($output, $headers);
                
                foreach ($testData as $row) {
                    fputcsv($output, [
                        $row['id_split'],
                        $row['id_data_pemeliharaan'],
                        $row['nama_objek'],
                        $row['tipe_data'],
                        $row['tanggal'],
                        $row['nama_penyulang'],
                        $row['cluster_label'],
                        $row['risk_score'],
                        $row['tingkat_risiko']
                    ]);
                }
                
                fclose($output);
                exit();
                
            } catch (Exception $e) {
                $_SESSION['error'] = 'Export gagal: ' . $e->getMessage();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }
            break;
    }
}

try {
    $splitModel = new SplitModel();
    $statistics = $splitModel->getSplitStatistics();
    $splitData = $splitModel->getSplitData(100);
} catch (Exception $e) {
    $statistics = [
        'total_data' => 0,
        'train_count' => 0,
        'test_count' => 0,
        'gardu_count' => 0,
        'sutm_count' => 0,
        'train_percentage' => 0,
        'test_percentage' => 0
    ];
    $splitData = [];
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
                                            <h3 class="mb-0">Split Data 80:20</h3>
                                            <p class="text-muted">Kelola pembagian data training dan testing</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Flash Message -->
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

                                <?php if (isset($_SESSION['split_info'])): ?>
                                    <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                                        <i class="mdi mdi-information"></i>
                                        <strong>Informasi Split:</strong><br>
                                        Total Data: <?= $_SESSION['split_info']['total_data'] ?> |
                                        Training: <?= $_SESSION['split_info']['train_data'] ?> (<?= $_SESSION['split_info']['train_percentage'] ?>%) |
                                        Testing: <?= $_SESSION['split_info']['test_data'] ?> (<?= $_SESSION['split_info']['test_percentage'] ?>%)
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php unset($_SESSION['split_info']); ?>
                                <?php endif; ?>

                                <!-- Statistics Cards -->
                                <div class="row mt-4">
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo $statistics['total_data']; ?></h4>
                                                        <p class="mb-0">Total Data</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-database"></i>
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
                                                        <h4 class="mb-0"><?php echo $statistics['train_count']; ?></h4>
                                                        <p class="mb-0">Training Data (<?php echo $statistics['train_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-school"></i>
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
                                                        <h4 class="mb-0"><?php echo $statistics['test_count']; ?></h4>
                                                        <p class="mb-0">Testing Data (<?php echo $statistics['test_percentage']; ?>%)</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-clipboard-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card bg-info text-white">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h4 class="mb-0"><?php echo ($statistics['total_data'] > 0) ? 'Ready' : 'No Data'; ?></h4>
                                                        <p class="mb-0">Status</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="mdi mdi-chart-pie"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Kontrol Split Data</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <form method="POST" style="display: inline-block;">
                                                            <input type="hidden" name="action" value="split_data">
                                                            <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Split data cluster menjadi 80:20? Data split sebelumnya akan dihapus.')">
                                                                <i class="mdi mdi-call-split"></i> Split Data 80:20
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form method="POST" style="display: inline-block;">
                                                            <input type="hidden" name="action" value="reset_data">
                                                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Reset semua data split? Tindakan ini tidak dapat dibatalkan!')">
                                                                <i class="mdi mdi-refresh"></i> Reset Data Split
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <p class="text-muted mt-2">Split data cluster menjadi training data (80%) dan testing data (20%) secara acak.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Export Buttons -->
                                <?php if ($statistics['total_data'] > 0): ?>
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Export Data</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <a href="?action=exportTrainData" class="btn btn-success">
                                                            <i class="mdi mdi-download"></i> Export Training Data
                                                        </a>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <a href="?action=exportTestData" class="btn btn-warning">
                                                            <i class="mdi mdi-download"></i> Export Testing Data
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Results Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Data Split (<?php echo count($splitData); ?> data)</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID Split</th>
                                                                <th>Nama Objek</th>
                                                                <th>Tipe Data</th>
                                                                <th>Tanggal</th>
                                                                <th>Nama Penyulang</th>
                                                                <th>Cluster Label</th>
                                                                <th>Risk Score</th>
                                                                <th>Tingkat Risiko</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($splitData)): ?>
                                                                <?php foreach ($splitData as $row): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($row['id_split']); ?></td>
                                                                        <td>
                                                                            <span class="badge <?php echo $row['nama_objek'] == 'gardu' ? 'bg-primary' : 'bg-secondary'; ?>">
                                                                                <?php echo ucfirst(htmlspecialchars($row['nama_objek'])); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="badge <?php echo $row['tipe_data'] == 'train' ? 'bg-success' : 'bg-warning'; ?>">
                                                                                <?php echo ucfirst(htmlspecialchars($row['tipe_data'])); ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <?php 
                                                                            if (isset($row['tanggal'])) {
                                                                                echo date('d/m/Y', strtotime($row['tanggal'])); 
                                                                            } else {
                                                                                echo '-';
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?php echo isset($row['nama_penyulang']) ? htmlspecialchars($row['nama_penyulang']) : '-'; ?></td>
                                                                        <td>
                                                                            <span class="badge bg-info">
                                                                                Cluster <?php echo isset($row['cluster_label']) ? $row['cluster_label'] : '0'; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo isset($row['risk_score']) ? number_format($row['risk_score'], 4) : '0.0000'; ?></td>
                                                                        <td>
                                                                            <?php
                                                                            $tingkatRisiko = isset($row['tingkat_risiko']) ? $row['tingkat_risiko'] : 'rendah';
                                                                            $badgeClass = '';
                                                                            switch (strtolower($tingkatRisiko)) {
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
                                                                                <?php echo ucfirst(htmlspecialchars($tingkatRisiko)); ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center">Tidak ada data split. Jalankan split data terlebih dahulu.</td>
                                                                </tr>
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
            </div>
        </div>
    </div>
    
    <script>
        // Auto-hide alerts after 5 seconds
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