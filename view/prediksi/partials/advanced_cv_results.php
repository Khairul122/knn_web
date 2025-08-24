<?php if ($data['optimalKResult'] && $data['optimalKResult']['success']): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-chart-scatter-plot text-info me-2"></i>
                        <span>Advanced Cross Validation Results</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">K = <?= $data['optimalKResult']['optimal_k'] ?></h3>
                                <p class="mb-0 small text-muted">Optimal K Value</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1"><?= $data['optimalKResult']['best_accuracy'] ?>%</h3>
                                <p class="mb-0 small text-muted">Best Accuracy</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <h3 class="text-info mb-1"><?= $data['optimalKResult']['cv_folds'] ?>-Fold</h3>
                                <p class="mb-0 small text-muted">Cross Validation</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="mdi mdi-pound me-1"></i>K Value</th>
                                    <th><i class="mdi mdi-percent me-1"></i>Accuracy (%)</th>
                                    <th><i class="mdi mdi-sigma me-1"></i>Std Dev (%)</th>
                                    <th><i class="mdi mdi-target me-1"></i>Avg Precision (%)</th>
                                    <th><i class="mdi mdi-bullseye me-1"></i>Avg Recall (%)</th>
                                    <th><i class="mdi mdi-medal me-1"></i>Avg F1-Score (%)</th>
                                    <th><i class="mdi mdi-star me-1"></i>Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $sortedResults = $data['optimalKResult']['all_results'];
                                usort($sortedResults, function($a, $b) {
                                    return $b['accuracy'] <=> $a['accuracy'];
                                });
                                
                                foreach ($sortedResults as $index => $kResult): 
                                    $isOptimal = $kResult['k'] == $data['optimalKResult']['optimal_k'];
                                    $avgPrecision = array_sum($kResult['precision']) / count($kResult['precision']);
                                    $avgRecall = array_sum($kResult['recall']) / count($kResult['recall']);
                                    $avgF1 = array_sum($kResult['f1_score']) / count($kResult['f1_score']);
                                    $rank = $index + 1;
                                ?>
                                    <tr class="<?= $isOptimal ? 'table-success' : '' ?>">
                                        <td>
                                            <span class="badge <?= $isOptimal ? 'bg-success' : 'bg-secondary'; ?> px-2 py-1">
                                                K = <?= $kResult['k'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="<?= $isOptimal ? 'text-success' : '' ?>">
                                                <?= $kResult['accuracy'] ?>%
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">±<?= $kResult['std_dev'] ?>%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white"><?= round($avgPrecision, 1) ?>%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark"><?= round($avgRecall, 1) ?>%</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= round($avgF1, 1) ?>%</span>
                                        </td>
                                        <td>
                                            <?php if ($rank <= 3): ?>
                                                <span class="badge bg-<?= $rank == 1 ? 'warning' : ($rank == 2 ? 'secondary' : 'dark') ?>">
                                                    <i class="mdi mdi-medal me-1"></i>#<?= $rank ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">#<?= $rank ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="fw-semibold mb-2 d-flex align-items-center">
                            <i class="mdi mdi-information text-muted me-2"></i>
                            <span>Interpretasi Metrik:</span>
                        </h6>
                        <div class="row small text-muted">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Precision:</strong> Dari semua prediksi positif, berapa yang benar
                                </div>
                                <div class="mb-2">
                                    <strong>Recall:</strong> Dari semua kasus positif actual, berapa yang terdeteksi
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>F1-Score:</strong> Harmonic mean dari precision dan recall
                                </div>
                                <div class="mb-2">
                                    <strong>Std Dev:</strong> Konsistensi performa across folds
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>