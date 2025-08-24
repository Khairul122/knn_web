<?php if (!empty($data['confusionMatrix']['matrix'])): ?>
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-matrix text-primary me-2"></i>
                        <span>Confusion Matrix</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Actual / Predicted</th>
                                    <?php foreach ($data['confusionMatrix']['classes'] as $class): ?>
                                        <th><?php echo $class; ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['confusionMatrix']['classes'] as $actualClass): ?>
                                    <tr>
                                        <th class="table-light"><?php echo $actualClass; ?></th>
                                        <?php foreach ($data['confusionMatrix']['classes'] as $predictedClass): ?>
                                            <td class="<?php echo $actualClass === $predictedClass ? 'table-success fw-bold' : ''; ?>">
                                                <?php echo $data['confusionMatrix']['matrix'][$actualClass][$predictedClass]; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-muted small">
                        <i class="mdi mdi-information me-1"></i>
                        Diagonal hijau menunjukkan prediksi yang benar
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-chart-bar text-success me-2"></i>
                        <span>Evaluasi Matriks</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="text-center">
                            <h4 class="text-primary mb-1"><?php echo $data['confusionMatrix']['accuracy']; ?>%</h4>
                            <p class="mb-0 small text-muted">Overall Accuracy</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="small">Kelas</th>
                                    <th class="small">Precision</th>
                                    <th class="small">Recall</th>
                                    <th class="small">F1-Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['confusionMatrix']['classes'] as $class): 
                                    $colorClass = '';
                                    switch($class) {
                                        case 'TINGGI': $colorClass = 'text-danger'; break;
                                        case 'SEDANG': $colorClass = 'text-warning'; break;
                                        case 'RENDAH': $colorClass = 'text-success'; break;
                                    }
                                ?>
                                    <tr>
                                        <td class="fw-semibold <?= $colorClass ?> small"><?= $class ?></td>
                                        <td class="small"><?= $data['confusionMatrix']['precision'][$class] ?>%</td>
                                        <td class="small"><?= $data['confusionMatrix']['recall'][$class] ?>%</td>
                                        <td class="small"><?= $data['confusionMatrix']['f1_score'][$class] ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th class="small">RATA-RATA</th>
                                    <th class="small"><?= round(array_sum($data['confusionMatrix']['precision']) / 3, 1) ?>%</th>
                                    <th class="small"><?= round(array_sum($data['confusionMatrix']['recall']) / 3, 1) ?>%</th>
                                    <th class="small"><?= round(array_sum($data['confusionMatrix']['f1_score']) / 3, 1) ?>%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-success mb-1"><?= $data['confusionMatrix']['correct_predictions'] ?></h6>
                                    <small class="text-muted">Correct</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <h6 class="text-danger mb-1"><?= $data['confusionMatrix']['total_samples'] - $data['confusionMatrix']['correct_predictions'] ?></h6>
                                    <small class="text-muted">Incorrect</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>