<?php
session_start();
require_once 'model/ClusterModel.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clusterModel = new ClusterModel();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'run_clustering':
                try {
                    $result = $clusterModel->performKMeansClustering();
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
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
                    $result = $clusterModel->resetAllData();
                    if ($result['success']) {
                        $message = $result['message'];
                        $messageType = 'success';
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
    $clusterModel = new ClusterModel();
    $statistics = $clusterModel->getClusterStatistics();
    $results = $clusterModel->getClusterResults(50);
} catch (Exception $e) {
    $statistics = [
        'total_data' => 0,
        'tinggi' => 0,
        'sedang' => 0,
        'rendah' => 0
    ];
    $results = [];
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
                                            <h3 class="mb-0">Clustering Management</h3>
                                            <p class="text-muted">Kelola clustering data pemeliharaan</p>
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
                                                        <i class="ti-stats-up"></i>
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
                                                        <h4 class="mb-0"><?php echo $statistics['tinggi']; ?></h4>
                                                        <p class="mb-0">Risiko Tinggi</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ti-alert"></i>
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
                                                        <h4 class="mb-0"><?php echo $statistics['sedang']; ?></h4>
                                                        <p class="mb-0">Risiko Sedang</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ti-info"></i>
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
                                                        <h4 class="mb-0"><?php echo $statistics['rendah']; ?></h4>
                                                        <p class="mb-0">Risiko Rendah</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ti-check"></i>
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
                                                <h5 class="card-title mb-0">Aksi Clustering</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <form method="POST" style="display: inline-block;">
                                                            <input type="hidden" name="action" value="run_clustering">
                                                            <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Jalankan clustering sekarang?')">
                                                                <i class="ti-reload"></i> Jalankan Clustering
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <form method="POST" style="display: inline-block;">
                                                            <input type="hidden" name="action" value="reset_data">
                                                            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Reset semua data clustering? Tindakan ini tidak dapat dibatalkan!')">
                                                                <i class="ti-trash"></i> Reset Semua Data
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Results Table -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Hasil Clustering (<?php echo count($results); ?> data)</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Tanggal</th>
                                                                <th>Objek</th>
                                                                <th>Penyulang</th>
                                                                <th>Cluster</th>
                                                                <th>Risk Score</th>
                                                                <th>Tingkat Risiko</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($results)): ?>
                                                                <?php foreach ($results as $result): ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($result['id_data_pemeliharaan']); ?></td>
                                                                        <td>
                                                                            <?php 
                                                                            if (isset($result['tanggal'])) {
                                                                                echo date('d/m/Y', strtotime($result['tanggal'])); 
                                                                            } else {
                                                                                echo '-';
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td><?php echo isset($result['nama_objek']) ? ucfirst(htmlspecialchars($result['nama_objek'])) : '-'; ?></td>
                                                                        <td><?php echo isset($result['nama_penyulang']) ? htmlspecialchars($result['nama_penyulang']) : '-'; ?></td>
                                                                        <td>
                                                                            <span class="badge bg-secondary">
                                                                                Cluster <?php echo isset($result['cluster_label']) ? $result['cluster_label'] : '0'; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td><?php echo isset($result['risk_score']) ? number_format($result['risk_score'], 2) : '0.00'; ?></td>
                                                                        <td>
                                                                            <?php
                                                                            $tingkatRisiko = isset($result['tingkat_risiko']) ? $result['tingkat_risiko'] : 'RENDAH';
                                                                            $badgeClass = $tingkatRisiko === 'TINGGI' ? 'bg-danger' : 
                                                                                         ($tingkatRisiko === 'SEDANG' ? 'bg-warning' : 'bg-success');
                                                                            ?>
                                                                            <span class="badge <?php echo $badgeClass; ?>">
                                                                                <?php echo htmlspecialchars($tingkatRisiko); ?>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="7" class="text-center">Tidak ada data clustering. Jalankan clustering terlebih dahulu.</td>
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
                <?php include 'view/template/footer.php'; ?>
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