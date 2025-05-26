<?php include('view/template/header.php'); ?>

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
                                            <h3 class="mb-0">Prediksi Risiko Gangguan Jaringan</h3>
                                            <p class="text-muted">Analisis prediksi menggunakan metode K-Nearest Neighbor (KNN) berdasarkan data clustering</p>
                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($_SESSION['success_message'])): ?>
                                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                        <i class="mdi mdi-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php unset($_SESSION['success_message']); ?>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['error_message'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                        <i class="mdi mdi-alert-circle"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php unset($_SESSION['error_message']); ?>
                                <?php endif; ?>

                                <div class="tab-content tab-content-basic">
                                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview">
                                        
                                        <!-- Data Status Card -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4 class="card-title">Status Data untuk Prediksi KNN</h4>
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="mdi mdi-database <?= isset($dataStatus) && $dataStatus['has_clustering'] ? 'text-success' : 'text-danger' ?> me-2"></i>
                                                                    <div>
                                                                        <h6 class="mb-0">Data Clustering</h6>
                                                                        <small class="text-muted"><?= isset($dataStatus) ? $dataStatus['clustering_count'] : 0 ?> data</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="mdi mdi-content-cut <?= isset($dataStatus) && $dataStatus['has_split'] ? 'text-success' : 'text-danger' ?> me-2"></i>
                                                                    <div>
                                                                        <h6 class="mb-0">Data Split</h6>
                                                                        <small class="text-muted"><?= isset($dataStatus) ? $dataStatus['split_count'] : 0 ?> data</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="mdi mdi-school text-info me-2"></i>
                                                                    <div>
                                                                        <h6 class="mb-0">Training Data</h6>
                                                                        <small class="text-muted"><?= isset($dataStatus) ? $dataStatus['training_count'] : 0 ?> data</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="mdi mdi-test-tube text-warning me-2"></i>
                                                                    <div>
                                                                        <h6 class="mb-0">Testing Data</h6>
                                                                        <small class="text-muted"><?= isset($dataStatus) ? $dataStatus['test_count'] : 0 ?> data</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if (isset($dataStatus) && !$dataStatus['ready_for_prediction']): ?>
                                                            <div class="alert alert-warning mt-3">
                                                                <i class="mdi mdi-alert"></i>
                                                                Data belum siap untuk prediksi. Silakan lakukan 
                                                                <a href="index.php?page=clustering" class="alert-link">clustering dan split data</a> terlebih dahulu.
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- KNN Prediction Form -->
                                        <div class="row mt-4">
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4 class="card-title">
                                                            <i class="mdi mdi-chart-line"></i> Prediksi Risiko KNN
                                                        </h4>
                                                        <p class="card-description">
                                                            Jalankan prediksi risiko menggunakan algoritma K-Nearest Neighbor
                                                        </p>
                                                        
                                                        <form action="index.php?page=prediksi-process" method="post" id="prediksiForm">
                                                            <div class="form-group">
                                                                <label for="k_value">Nilai K (Jumlah Tetangga Terdekat)</label>
                                                                <select name="k_value" id="k_value" class="form-control" required>
                                                                    <option value="3" selected>K = 3</option>
                                                                    <option value="5">K = 5</option>
                                                                    <option value="7">K = 7</option>
                                                                    <option value="9">K = 9</option>
                                                                    <option value="11">K = 11</option>
                                                                    <option value="15">K = 15</option>
                                                                </select>
                                                                <small class="form-text text-muted">
                                                                    Nilai K yang lebih kecil lebih sensitif terhadap noise, nilai K yang lebih besar lebih stabil
                                                                </small>
                                                            </div>
                                                            
                                                            <button type="submit" class="btn btn-primary" id="prediksiBtn" 
                                                                    <?= (isset($dataStatus) && !$dataStatus['ready_for_prediction']) ? 'disabled' : '' ?>>
                                                                <i class="mdi mdi-play"></i> Jalankan Prediksi KNN
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h4 class="card-title">
                                                            <i class="mdi mdi-information"></i> Informasi Algoritma
                                                        </h4>
                                                        
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h6>K-Nearest Neighbor (KNN)</h6>
                                                                <ul class="list-unstyled">
                                                                    <li><small><strong>Data Training:</strong> Hasil clustering dengan label risiko</small></li>
                                                                    <li><small><strong>Data Testing:</strong> Data dari split testing</small></li>
                                                                    <li><small><strong>Features:</strong> 19 atribut pemeliharaan</small></li>
                                                                    <li><small><strong>Distance:</strong> Euclidean Distance</small></li>
                                                                    <li><small><strong>Classification:</strong> Majority voting dari K tetangga</small></li>
                                                                </ul>
                                                                
                                                                <?php if (isset($lastPrediction) && $lastPrediction): ?>
                                                                    <div class="border-top pt-3 mt-3">
                                                                        <h6>Prediksi Terakhir</h6>
                                                                        <small class="text-muted">
                                                                            <i class="mdi mdi-calendar"></i> <?= $lastPrediction['tanggal_prediksi'] ?><br>
                                                                            <i class="mdi mdi-chart-donut"></i> K = <?= $lastPrediction['k_value'] ?><br>
                                                                            <i class="mdi mdi-database"></i> <?= $lastPrediction['total_predictions'] ?> prediksi
                                                                        </small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (isset($hasPrediction) && $hasPrediction): ?>
                                            <!-- Summary Stats -->
                                            <div class="row mt-4">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h4 class="card-title mb-0">Ringkasan Hasil Prediksi</h4>
                                                                <div>
                                                                    <a href="index.php?page=prediksi-export-log" class="btn btn-sm btn-success">
                                                                        <i class="mdi mdi-download"></i> Export CSV
                                                                    </a>
                                                                    <a href="index.php?page=prediksi-clear-log" class="btn btn-sm btn-danger" 
                                                                       onclick="return confirm('Yakin ingin menghapus semua log prediksi?')">
                                                                        <i class="mdi mdi-delete"></i> Clear Log
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            
                                                            <?php if (!empty($predictionSummary)): ?>
                                                                <div class="row">
                                                                    <?php foreach ($predictionSummary as $summary): ?>
                                                                        <div class="col-md-4">
                                                                            <div class="card border-<?= 
                                                                                $summary['tingkat_risiko'] == 'TINGGI' ? 'danger' : 
                                                                                ($summary['tingkat_risiko'] == 'SEDANG' ? 'warning' : 'success') 
                                                                            ?>">
                                                                                <div class="card-body text-center">
                                                                                    <h3 class="text-<?= 
                                                                                        $summary['tingkat_risiko'] == 'TINGGI' ? 'danger' : 
                                                                                        ($summary['tingkat_risiko'] == 'SEDANG' ? 'warning' : 'success') 
                                                                                    ?>">
                                                                                        <?= $summary['jumlah_penyulang'] ?>
                                                                                    </h3>
                                                                                    <h6>Risiko <?= $summary['tingkat_risiko'] ?></h6>
                                                                                    <small class="text-muted">
                                                                                        Rata-rata: <?= number_format($summary['rata_nilai_risiko'], 2) ?><br>
                                                                                        Kegiatan: <?= number_format($summary['rata_total_kegiatan'], 0) ?>
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Results Table -->
                                            <div class="row mt-4">
                                                <div class="col-lg-12">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h4 class="card-title mb-0">Hasil Prediksi Detail</h4>
                                                                <div class="btn-group" role="group">
                                                                    <a href="index.php?page=prediksi" class="btn btn-sm btn-outline-secondary <?= !isset($isFiltered) ? 'active' : '' ?>">
                                                                        Semua
                                                                    </a>
                                                                    <a href="index.php?page=prediksi-filter&tingkat=TINGGI" class="btn btn-sm btn-outline-danger <?= (isset($filterTingkat) && $filterTingkat == 'TINGGI') ? 'active' : '' ?>">
                                                                        Tinggi
                                                                    </a>
                                                                    <a href="index.php?page=prediksi-filter&tingkat=SEDANG" class="btn btn-sm btn-outline-warning <?= (isset($filterTingkat) && $filterTingkat == 'SEDANG') ? 'active' : '' ?>">
                                                                        Sedang
                                                                    </a>
                                                                    <a href="index.php?page=prediksi-filter&tingkat=RENDAH" class="btn btn-sm btn-outline-success <?= (isset($filterTingkat) && $filterTingkat == 'RENDAH') ? 'active' : '' ?>">
                                                                        Rendah
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <?php if (!empty($predictionResults)): ?>
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>No</th>
                                                                                <th>Nama Penyulang</th>
                                                                                <th>Tingkat Risiko</th>
                                                                                <th>Nilai Risiko</th>
                                                                                <th>Total Kegiatan</th>
                                                                                <th>K Value</th>
                                                                                <th>Tanggal Prediksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php 
                                                                            $no = 1;
                                                                            foreach ($predictionResults as $result): 
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?= $no++ ?></td>
                                                                                    <td><?= htmlspecialchars($result['nama_penyulang']) ?></td>
                                                                                    <td>
                                                                                        <span class="badge badge-<?= 
                                                                                            $result['tingkat_risiko'] == 'TINGGI' ? 'danger' : 
                                                                                            ($result['tingkat_risiko'] == 'SEDANG' ? 'warning' : 'success') 
                                                                                        ?>">
                                                                                            <?= $result['tingkat_risiko'] ?>
                                                                                        </span>
                                                                                    </td>
                                                                                    <td><?= number_format($result['nilai_risiko'], 2) ?></td>
                                                                                    <td><?= number_format($result['total_kegiatan']) ?></td>
                                                                                    <td><?= $result['k_value'] ?></td>
                                                                                    <td><?= $result['tanggal_prediksi'] ?></td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="text-center py-4">
                                                                    <i class="mdi mdi-chart-line-stacked display-4 text-muted"></i>
                                                                    <p class="text-muted mt-2">Belum ada hasil prediksi</p>
                                                                </div>
                                                            <?php endif; ?>
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

    <script>
        document.getElementById('prediksiForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('prediksiBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Processing...';
        });

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>

    <?php include 'view/template/script.php'; ?>
</body>
</html>