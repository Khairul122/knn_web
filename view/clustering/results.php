<?php
// view/clustering/results.php
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
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="card-title mb-0">Detail Hasil Clustering</h4>
                                        <div>
                                            <a href="index.php?controller=clustering&action=index" class="btn btn-sm btn-secondary">Kembali</a>
                                            <a href="index.php?controller=clustering&action=exportClusteringResults" class="btn btn-sm btn-primary">Export CSV</a>
                                        </div>
                                    </div>

                                    <?php if (!empty($clusterStats)): ?>
                                        <div class="row mb-4">
                                            <div class="col-md-12">
                                                <h5>Statistik Cluster</h5>
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
                                                                    <td><?= htmlspecialchars($stat['count']) ?></td>
                                                                    <td><?= htmlspecialchars($stat['percentage']) ?>%</td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($clusteringResults)): ?>
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
                                                    foreach ($clusteringResults as $result): 
                                                    ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= htmlspecialchars($result['id_data_pemeliharaan']) ?></td>
                                                            <td><?= htmlspecialchars($result['tanggal']) ?></td>
                                                            <td><?= ucfirst(htmlspecialchars($result['nama_objek'])) ?></td>
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
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada hasil clustering. Silakan jalankan clustering terlebih dahulu.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'view/template/script.php'; ?>
</body>
</html>
