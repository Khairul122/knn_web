<?php if (!empty($data['riskDistribution'])): ?>
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-chart-pie text-primary me-2"></i>
                        <span>Distribusi Tingkat Risiko</span>
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <canvas id="riskDistributionChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-12">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="mdi mdi-chart-line text-primary me-2"></i>
                        <span>Trend Akurasi per K Value</span>
                    </h5>
                </div>
                <div class="card-body pt-0">
                    <canvas id="accuracyTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>