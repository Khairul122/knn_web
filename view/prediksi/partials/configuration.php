<div class="row g-3 mb-4">
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
                        <div class="col-md-5">
                            <div class="form-group mb-2">
                                <label for="max_k" class="form-label small fw-semibold">Maximum K:</label>
                                <input type="number" class="form-control form-control-sm" id="max_k" name="max_k"
                                    value="15" min="3" max="25" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label for="cv_folds" class="form-label small fw-semibold">CV Folds:</label>
                                <select class="form-select form-select-sm" id="cv_folds" name="cv_folds" required>
                                    <option value="3">3-Fold</option>
                                    <option value="5" selected>5-Fold</option>
                                    <option value="7">7-Fold</option>
                                    <option value="10">10-Fold</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="action" value="find_optimal_k"
                                class="btn btn-info w-100 btn-sm" id="btn-find-k">
                                <i class="mdi mdi-magnify me-1"></i> Cari K Optimal
                            </button>
                        </div>
                    </div>
                    <div class="form-text small mt-2">Cross Validation akan test K dari 3 sampai nilai maksimum (ganjil saja) dengan evaluasi lengkap</div>
                </form>

                <?php if ($data['optimalKResult'] && $data['optimalKResult']['success']): ?>
                    <div class="mt-4 pt-3 border-top">
                        <div class="alert alert-success py-2 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-check-circle me-2"></i>
                                <div>
                                    <strong class="small">Hasil Cross Validation:</strong><br>
                                    <span class="small">K Optimal: <strong class="text-primary"><?= $data['optimalKResult']['optimal_k'] ?></strong>
                                    dengan accuracy <strong class="text-success"><?= $data['optimalKResult']['best_accuracy'] ?>%</strong></span><br>
                                    <small class="text-muted">Data Size: <?= $data['optimalKResult']['data_size'] ?> samples | <?= $data['optimalKResult']['cv_folds'] ?>-Fold Cross Validation</small>
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
                                        <th class="small"><i class="mdi mdi-pound"></i> K</th>
                                        <th class="small"><i class="mdi mdi-percent"></i> Accuracy</th>
                                        <th class="small"><i class="mdi mdi-sigma"></i> Std Dev</th>
                                        <th class="small"><i class="mdi mdi-target"></i> Precision</th>
                                        <th class="small"><i class="mdi mdi-bullseye"></i> Recall</th>
                                        <th class="small"><i class="mdi mdi-medal"></i> F1-Score</th>
                                        <th class="small"><i class="mdi mdi-star"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['optimalKResult']['all_results'] as $kResult): 
                                        $isOptimal = $kResult['k'] == $data['optimalKResult']['optimal_k'];
                                        $avgPrecision = array_sum($kResult['precision']) / count($kResult['precision']);
                                        $avgRecall = array_sum($kResult['recall']) / count($kResult['recall']);
                                        $avgF1 = array_sum($kResult['f1_score']) / count($kResult['f1_score']);
                                    ?>
                                        <tr class="<?= $isOptimal ? 'table-success' : '' ?>">
                                            <td>
                                                <span class="badge <?= $isOptimal ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?= $kResult['k'] ?>
                                                </span>
                                            </td>
                                            <td><strong><?= $kResult['accuracy'] ?>%</strong></td>
                                            <td><span class="text-muted"><?= $kResult['std_dev'] ?>%</span></td>
                                            <td><span class="text-info"><?= round($avgPrecision, 1) ?>%</span></td>
                                            <td><span class="text-warning"><?= round($avgRecall, 1) ?>%</span></td>
                                            <td><span class="text-primary"><?= round($avgF1, 1) ?>%</span></td>
                                            <td>
                                                <?php if ($isOptimal): ?>
                                                    <span class="badge bg-success small">
                                                        <i class="mdi mdi-star me-1"></i> Optimal
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6 class="fw-semibold mb-2 d-flex align-items-center">
                                <i class="mdi mdi-chart-bar me-2 text-muted"></i>
                                <span>Evaluasi Matriks K Optimal (K=<?= $data['optimalKResult']['optimal_k'] ?>):</span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Kelas</th>
                                            <th><i class="mdi mdi-target me-1"></i>Precision (%)</th>
                                            <th><i class="mdi mdi-bullseye me-1"></i>Recall (%)</th>
                                            <th><i class="mdi mdi-medal me-1"></i>F1-Score (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $bestMetrics = $data['optimalKResult']['best_metrics'];
                                        foreach (['RENDAH', 'SEDANG', 'TINGGI'] as $class):
                                            $colorClass = '';
                                            switch($class) {
                                                case 'TINGGI': $colorClass = 'text-danger'; break;
                                                case 'SEDANG': $colorClass = 'text-warning'; break;
                                                case 'RENDAH': $colorClass = 'text-success'; break;
                                            }
                                        ?>
                                            <tr>
                                                <td class="fw-semibold <?= $colorClass ?>"><?= $class ?></td>
                                                <td><span class="badge bg-info"><?= $bestMetrics['precision'][$class] ?>%</span></td>
                                                <td><span class="badge bg-warning text-dark"><?= $bestMetrics['recall'][$class] ?>%</span></td>
                                                <td><span class="badge bg-primary"><?= $bestMetrics['f1_score'][$class] ?>%</span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="table-light fw-bold">
                                            <td>RATA-RATA</td>
                                            <td><span class="badge bg-dark"><?= round(array_sum($bestMetrics['precision']) / 3, 1) ?>%</span></td>
                                            <td><span class="badge bg-dark"><?= round(array_sum($bestMetrics['recall']) / 3, 1) ?>%</span></td>
                                            <td><span class="badge bg-dark"><?= round(array_sum($bestMetrics['f1_score']) / 3, 1) ?>%</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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