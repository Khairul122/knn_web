<?php if (!empty($data['confusionMatrix']['matrix'])): ?>
    <div class="row mb-4">
        <div class="col-12">
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
                        Diagonal hijau menunjukkan prediksi yang benar. Accuracy: <?php echo $data['confusionMatrix']['accuracy']; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>