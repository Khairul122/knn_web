<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="card-title mb-2 mb-md-0 d-flex align-items-center">
                        <i class="mdi mdi-table text-primary me-2"></i>
                        <span>Hasil Prediksi & Saran Perbaikan</span>
                        <span class="badge bg-info ms-2"><?php echo count($data['prediksiData']); ?> data</span>
                    </h5>
                    <?php if (!empty($data['prediksiData'])): ?>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($data['prediksiData'])): ?>
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
                            <?php if (!empty($data['prediksiData'])): ?>
                                <?php foreach ($data['prediksiData'] as $row): ?>
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

                <?php if (!empty($data['prediksiData'])): ?>
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
                                            <strong>Rata-rata Risk Score:</strong> <?php echo $data['statistics']['avg_risk_score']; ?>
                                        </div>
                                        <div>
                                            <strong>Rata-rata Total Kegiatan:</strong> <?php echo $data['statistics']['avg_total_kegiatan']; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <strong>Distribusi Risiko:</strong>
                                        </div>
                                        <div>
                                            Tinggi: <?php echo $data['statistics']['tinggi_count']; ?> (<?php echo $data['statistics']['tinggi_percentage']; ?>%) |
                                            Sedang: <?php echo $data['statistics']['sedang_count']; ?> (<?php echo $data['statistics']['sedang_percentage']; ?>%) |
                                            Rendah: <?php echo $data['statistics']['rendah_count']; ?> (<?php echo $data['statistics']['rendah_percentage']; ?>%)
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
                                        Total Records: <?php echo count($data['prediksiData']); ?>
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