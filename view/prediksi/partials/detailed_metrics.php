<?php if (!empty($data['confusionMatrix']['matrix'])): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 d-flex align-items-center">
                            <i class="mdi mdi-chart-box text-primary me-2"></i>
                            <span>Detailed Performance Metrics</span>
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success">Accuracy: <?= $data['confusionMatrix']['accuracy'] ?>%</span>
                            <span class="badge bg-info">Samples: <?= $data['confusionMatrix']['total_samples'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="mdi mdi-table text-muted me-2"></i>
                                <span>Classification Report per Class:</span>
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Risk Level</th>
                                            <th><i class="mdi mdi-target me-1"></i>Precision (%)</th>
                                            <th><i class="mdi mdi-bullseye me-1"></i>Recall (%)</th>
                                            <th><i class="mdi mdi-medal me-1"></i>F1-Score (%)</th>
                                            <th><i class="mdi mdi-chart-pie me-1"></i>Support</th>
                                            <th><i class="mdi mdi-star me-1"></i>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['confusionMatrix']['classes'] as $class): 
                                            $colorClass = '';
                                            $bgClass = '';
                                            switch($class) {
                                                case 'TINGGI': 
                                                    $colorClass = 'text-danger'; 
                                                    $bgClass = 'bg-danger bg-opacity-10';
                                                    break;
                                                case 'SEDANG': 
                                                    $colorClass = 'text-warning'; 
                                                    $bgClass = 'bg-warning bg-opacity-10';
                                                    break;
                                                case 'RENDAH': 
                                                    $colorClass = 'text-success'; 
                                                    $bgClass = 'bg-success bg-opacity-10';
                                                    break;
                                            }
                                            
                                            $support = array_sum($data['confusionMatrix']['matrix'][$class]);
                                            $precision = $data['confusionMatrix']['precision'][$class];
                                            $recall = $data['confusionMatrix']['recall'][$class];
                                            $f1 = $data['confusionMatrix']['f1_score'][$class];
                                            
                                            $performance = 'Excellent';
                                            $perfBadge = 'success';
                                            if ($f1 < 90) { $performance = 'Good'; $perfBadge = 'primary'; }
                                            if ($f1 < 80) { $performance = 'Fair'; $perfBadge = 'warning'; }
                                            if ($f1 < 70) { $performance = 'Poor'; $perfBadge = 'danger'; }
                                        ?>
                                            <tr class="<?= $bgClass ?>">
                                                <td class="fw-bold <?= $colorClass ?>"><?= $class ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= $precision ?>%</span>
                                                    <div class="progress mt-1" style="height: 4px;">
                                                        <div class="progress-bar bg-info" style="width: <?= $precision ?>%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark"><?= $recall ?>%</span>
                                                    <div class="progress mt-1" style="height: 4px;">
                                                        <div class="progress-bar bg-warning" style="width: <?= $recall ?>%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary"><?= $f1 ?>%</span>
                                                    <div class="progress mt-1" style="height: 4px;">
                                                        <div class="progress-bar bg-primary" style="width: <?= $f1 ?>%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary"><?= $support ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $perfBadge ?>"><?= $performance ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        
                                        <tr class="table-light fw-bold">
                                            <td>WEIGHTED AVG</td>
                                            <td>
                                                <span class="badge bg-dark">
                                                    <?= round(array_sum($data['confusionMatrix']['precision']) / 3, 1) ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark">
                                                    <?= round(array_sum($data['confusionMatrix']['recall']) / 3, 1) ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark">
                                                    <?= round(array_sum($data['confusionMatrix']['f1_score']) / 3, 1) ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark"><?= $data['confusionMatrix']['total_samples'] ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $overallF1 = round(array_sum($data['confusionMatrix']['f1_score']) / 3, 1);
                                                $overallPerf = 'Excellent';
                                                $overallBadge = 'success';
                                                if ($overallF1 < 90) { $overallPerf = 'Good'; $overallBadge = 'primary'; }
                                                if ($overallF1 < 80) { $overallPerf = 'Fair'; $overallBadge = 'warning'; }
                                                if ($overallF1 < 70) { $overallPerf = 'Poor'; $overallBadge = 'danger'; }
                                                ?>
                                                <span class="badge bg-<?= $overallBadge ?>"><?= $overallPerf ?></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <h6 class="fw-semibold mb-3 d-flex align-items-center">
                                <i class="mdi mdi-chart-donut text-muted me-2"></i>
                                <span>Performance Summary:</span>
                            </h6>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold">Model Accuracy</span>
                                    <span class="small text-primary"><?= $data['confusionMatrix']['accuracy'] ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $data['confusionMatrix']['accuracy'] ?>%"></div>
                                </div>
                            </div>

                            <?php 
                            $overallPrecision = round(array_sum($data['confusionMatrix']['precision']) / 3, 1);
                            $overallRecall = round(array_sum($data['confusionMatrix']['recall']) / 3, 1);
                            $overallF1 = round(array_sum($data['confusionMatrix']['f1_score']) / 3, 1);
                            ?>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold">Avg Precision</span>
                                    <span class="small text-info"><?= $overallPrecision ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: <?= $overallPrecision ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold">Avg Recall</span>
                                    <span class="small text-warning"><?= $overallRecall ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: <?= $overallRecall ?>%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-semibold">Avg F1-Score</span>
                                    <span class="small text-success"><?= $overallF1 ?>%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?= $overallF1 ?>%"></div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 border rounded">
                                <h6 class="fw-semibold mb-2 text-center">Model Quality</h6>
                                <div class="text-center">
                                    <?php 
                                    $quality = 'Excellent';
                                    $qualityBadge = 'success';
                                    $qualityIcon = 'mdi-star';
                                    
                                    if ($data['confusionMatrix']['accuracy'] < 90) { 
                                        $quality = 'Good'; 
                                        $qualityBadge = 'primary'; 
                                        $qualityIcon = 'mdi-thumb-up';
                                    }
                                    if ($data['confusionMatrix']['accuracy'] < 80) { 
                                        $quality = 'Fair'; 
                                        $qualityBadge = 'warning'; 
                                        $qualityIcon = 'mdi-minus-circle';
                                    }
                                    if ($data['confusionMatrix']['accuracy'] < 70) { 
                                        $quality = 'Poor'; 
                                        $qualityBadge = 'danger'; 
                                        $qualityIcon = 'mdi-alert';
                                    }
                                    ?>
                                    <div class="mb-2">
                                        <i class="mdi <?= $qualityIcon ?> fs-1 text-<?= $qualityBadge ?>"></i>
                                    </div>
                                    <span class="badge bg-<?= $qualityBadge ?> fs-6 px-3 py-2"><?= $quality ?></span>
                                    <div class="mt-2 small text-muted">
                                        Based on overall performance metrics
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <h6 class="fw-semibold mb-2">Model Insights:</h6>
                                <div class="small text-muted">
                                    <div class="mb-2">
                                        <i class="mdi mdi-lightbulb me-1 text-warning"></i>
                                        <?php if ($data['confusionMatrix']['accuracy'] >= 90): ?>
                                            Model menunjukkan performa excellent dengan akurasi tinggi
                                        <?php elseif ($data['confusionMatrix']['accuracy'] >= 80): ?>
                                            Model menunjukkan performa good, dapat diandalkan untuk prediksi
                                        <?php elseif ($data['confusionMatrix']['accuracy'] >= 70): ?>
                                            Model menunjukkan performa fair, perlu evaluasi lebih lanjut
                                        <?php else: ?>
                                            Model menunjukkan performa poor, disarankan untuk re-training
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-2">
                                        <i class="mdi mdi-information me-1 text-info"></i>
                                        Total <?= $data['confusionMatrix']['correct_predictions'] ?> dari <?= $data['confusionMatrix']['total_samples'] ?> prediksi benar
                                    </div>
                                    <div>
                                        <i class="mdi mdi-chart-line me-1 text-success"></i>
                                        Balanced accuracy across all risk levels
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>