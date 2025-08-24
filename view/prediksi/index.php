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
                        <div class="col-12">
                            <div class="home-tab">
                                <div class="d-sm-flex align-items-center justify-content-between border-bottom mb-4">
                                    <div class="py-3">
                                        <h3 class="mb-1 fw-bold text-primary">Prediksi Risiko KNN</h3>
                                        <p class="text-muted mb-0">Training dan prediksi risiko menggunakan K-Nearest Neighbors dengan Cross Validation</p>
                                    </div>
                                    <div class="d-none d-sm-block">
                                        <span class="badge bg-info fs-6 px-3 py-2">
                                            <i class="mdi mdi-database me-1"></i>
                                            <?php echo $data['statistics']['total_prediksi']; ?> Data
                                        </span>
                                    </div>
                                </div>

                                <?php include 'view/prediksi/partials/alerts.php'; ?>
                                <?php include 'view/prediksi/partials/statistics.php'; ?>
                                <?php include 'view/prediksi/partials/charts.php'; ?>
                                <?php include 'view/prediksi/partials/top_risk.php'; ?>
                                <?php include 'view/prediksi/partials/configuration.php'; ?>
                                <?php include 'view/prediksi/partials/advanced_cv_results.php'; ?>
                                <?php include 'view/prediksi/partials/evaluation_table.php'; ?>
                                <?php include 'view/prediksi/partials/detailed_metrics.php'; ?>
                                <?php include 'view/prediksi/partials/results_table.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'view/prediksi/partials/modal.php'; ?>
    <?php include 'view/prediksi/partials/scripts.php'; ?>
</body>
</html>