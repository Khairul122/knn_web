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

                            <?php if (isset($_SESSION['clustering_message'])): ?>
                                <div class="alert alert-<?= $_SESSION['clustering_success'] ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($_SESSION['clustering_message']) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['clustering_message'], $_SESSION['clustering_success']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['split_message'])): ?>
                                <div class="alert alert-<?= $_SESSION['split_success'] ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($_SESSION['split_message']) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['split_message'], $_SESSION['split_success']); ?>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['export_message'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($_SESSION['export_message']) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php unset($_SESSION['export_message']); ?>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">K-Means Clustering</h4>
                                            <p class="card-description">Lakukan clustering pada data pemeliharaan</p>
                                            
                                            <form id="clusteringForm" action="index.php?page=clustering-perform" method="post">
                                                <div class="form-group">
                                                    <label for="k">Jumlah Cluster (K)</label>
                                                    <select name="k" id="k" class="form-control" required>
                                                        <option value="2">2 Cluster</option>
                                                        <option value="3" selected>3 Cluster</option>
                                                        <option value="4">4 Cluster</option>
                                                        <option value="5">5 Cluster</option>
                                                        <option value="6">6 Cluster</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="max_iterations">Maksimal Iterasi</label>
                                                    <select name="max_iterations" id="max_iterations" class="form-control">
                                                        <option value="50">50</option>
                                                        <option value="100" selected>100</option>
                                                        <option value="200">200</option>
                                                        <option value="500">500</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary" id="clusteringBtn">
                                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                    Jalankan Clustering
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title">Split Data</h4>
                                            <p class="card-description">Bagi data menjadi training dan testing</p>
                                            
                                            <form id="splitDataForm" action="index.php?page=clustering-split" method="post">
                                                <div class="form-group">
                                                    <label for="test_ratio">Rasio Testing (%)</label>
                                                    <select name="test_ratio" id="test_ratio" class="form-control" required>
                                                        <option value="0.1">10% Testing - 90% Training</option>
                                                        <option value="0.2" selected>20% Testing - 80% Training</option>
                                                        <option value="0.3">30% Testing - 70% Training</option>
                                                        <option value="0.4">40% Testing - 60% Training</option>
                                                        <option value="0.5">50% Testing - 50% Training</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-success" id="splitDataBtn">
                                                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                                    Split Data
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4 class="card-title mb-0">Hasil Clustering</h4>
                                                <div>
                                                    <a href="index.php?page=clustering-results" class="btn btn-sm btn-info">Lihat Detail</a>
                                                    <a href="index.php?page=clustering-export-results" class="btn btn-sm btn-primary">Export CSV</a>
                                                </div>
                                            </div>

                                            <?php if (!empty($clusterStats)): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Cluster</th>
                                                                <th>Jumlah Data</th>
                                                                <th>Persentase</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($clusterStats as $stat): ?>
                                                                <tr>
                                                                    <td>Cluster <?= htmlspecialchars($stat['cluster_label']) ?></td>
                                                                    <td><?= number_format($stat['count']) ?></td>
                                                                    <td><?= htmlspecialchars($stat['percentage']) ?>%</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted">Belum ada hasil clustering. Jalankan clustering terlebih dahulu.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h4 class="card-title mb-0">Split Data</h4>
                                                <div>
                                                    <a href="index.php?page=clustering-split-data" class="btn btn-sm btn-info">Lihat Detail</a>
                                                    <a href="index.php?page=clustering-export-split" class="btn btn-sm btn-success">Export CSV</a>
                                                </div>
                                            </div>

                                            <?php if (!empty($splitStats)): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Tipe Data</th>
                                                                <th>Jumlah Data</th>
                                                                <th>Persentase</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($splitStats as $stat): ?>
                                                                <tr>
                                                                    <td><?= ucfirst(htmlspecialchars($stat['tipe_data'])) ?></td>
                                                                    <td><?= number_format($stat['count']) ?></td>
                                                                    <td><?= htmlspecialchars($stat['percentage']) ?>%</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted">Belum ada data split. Lakukan split data terlebih dahulu.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($clusteringResults)): ?>
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0">Preview Hasil Clustering</h4>
                                            <small class="text-muted">Menampilkan 15 data teratas dari <?= count($clusteringResults) ?> total data</small>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>ID Data</th>
                                                        <th>Tanggal</th>
                                                        <th>Nama Objek</th>
                                                        <th>Nama Penyulang</th>
                                                        <th>Cluster</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    $preview = array_slice($clusteringResults, 0, 15);
                                                    foreach ($preview as $result): 
                                                    ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= htmlspecialchars($result['id_data_pemeliharaan']) ?></td>
                                                            <td><?= htmlspecialchars($result['tanggal']) ?></td>
                                                            <td>
                                                                <span class="badge badge-secondary">
                                                                    <?= ucfirst(htmlspecialchars($result['nama_objek'])) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= htmlspecialchars($result['nama_penyulang']) ?></td>
                                                            <td>
                                                                <span class="badge badge-primary">
                                                                    Cluster <?= htmlspecialchars($result['cluster_label']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($splitDataResults)): ?>
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="card-title mb-0">Preview Split Data</h4>
                                            <small class="text-muted">Menampilkan 15 data teratas dari <?= count($splitDataResults) ?> total data</small>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>ID Data</th>
                                                        <th>Tanggal</th>
                                                        <th>Nama Objek</th>
                                                        <th>Nama Penyulang</th>
                                                        <th>Tipe Data</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    $preview = array_slice($splitDataResults, 0, 15);
                                                    foreach ($preview as $result): 
                                                    ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= htmlspecialchars($result['id_data_pemeliharaan']) ?></td>
                                                            <td><?= htmlspecialchars($result['tanggal']) ?></td>
                                                            <td>
                                                                <span class="badge badge-secondary">
                                                                    <?= ucfirst(htmlspecialchars($result['nama_objek'])) ?>
                                                                </span>
                                                            </td>
                                                            <td><?= htmlspecialchars($result['nama_penyulang']) ?></td>
                                                            <td>
                                                                <span class="badge badge-<?= $result['tipe_data'] == 'train' ? 'success' : 'warning' ?>">
                                                                    <?= ucfirst(htmlspecialchars($result['tipe_data'])) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h5 class="text-primary">
                                                        <i class="mdi mdi-information"></i> Informasi Clustering
                                                    </h5>
                                                    <ul class="list-unstyled">
                                                        <li><small><strong>Algorithm:</strong> K-Means Clustering</small></li>
                                                        <li><small><strong>Features:</strong> 18 atribut pemeliharaan</small></li>
                                                        <li><small><strong>Normalization:</strong> Min-Max Scaling</small></li>
                                                        <li><small><strong>Distance:</strong> Euclidean Distance</small></li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h5 class="text-success">
                                                        <i class="mdi mdi-chart-line"></i> Data Statistics
                                                    </h5>
                                                    <ul class="list-unstyled">
                                                        <li><small><strong>Total Data Clustering:</strong> <?= !empty($clusteringResults) ? count($clusteringResults) : 0 ?></small></li>
                                                        <li><small><strong>Total Data Split:</strong> <?= !empty($splitDataResults) ? count($splitDataResults) : 0 ?></small></li>
                                                        <li><small><strong>Database:</strong> db_knn</small></li>
                                                        <li><small><strong>Tables:</strong> hasil_cluster, split_data</small></li>
                                                    </ul>
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
        document.getElementById('clusteringForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('clusteringBtn');
            const spinner = btn.querySelector('.spinner-border');
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
        });

        document.getElementById('splitDataForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('splitDataBtn');
            const spinner = btn.querySelector('.spinner-border');
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
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