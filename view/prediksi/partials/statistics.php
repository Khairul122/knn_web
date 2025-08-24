<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold"><?php echo $data['statistics']['total_prediksi']; ?></h4>
                        <p class="mb-0 small">Total Prediksi</p>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-25 rounded-circle p-3">
                        <i class="mdi mdi-brain fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-danger text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold"><?php echo $data['statistics']['tinggi_count']; ?></h4>
                        <p class="mb-0 small">Risiko Tinggi (<?php echo $data['statistics']['tinggi_percentage']; ?>%)</p>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-25 rounded-circle p-3">
                        <i class="mdi mdi-alert fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold"><?php echo $data['statistics']['sedang_count']; ?></h4>
                        <p class="mb-0 small">Risiko Sedang (<?php echo $data['statistics']['sedang_percentage']; ?>%)</p>
                    </div>
                    <div class="icon-box bg-warning bg-opacity-25 rounded-circle p-3">
                        <i class="mdi mdi-alert-outline fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold"><?php echo $data['statistics']['rendah_count']; ?></h4>
                        <p class="mb-0 small">Risiko Rendah (<?php echo $data['statistics']['rendah_percentage']; ?>%)</p>
                    </div>
                    <div class="icon-box bg-success bg-opacity-25 rounded-circle p-3">
                        <i class="mdi mdi-check-circle fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>