<?php if (!empty($data['topRiskPenyulang'])): ?>
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
                                <?php foreach ($data['topRiskPenyulang'] as $index => $penyulang): ?>
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