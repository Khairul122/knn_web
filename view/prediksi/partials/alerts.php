<?php if (!empty($data['message'])): ?>
    <div class="alert alert-<?php echo $data['messageType'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-<?php echo $data['messageType'] === 'success' ? 'check-circle' : 'alert-circle'; ?> me-2 fs-5"></i>
            <div><?php echo htmlspecialchars($data['message']); ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-alert-circle me-2 fs-5"></i>
            <div><?= $_SESSION['error']; ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['train_info'])): ?>
    <div class="alert alert-info alert-dismissible fade show mb-4">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-information me-2 fs-5"></i>
            <div>
                <strong>Informasi Training:</strong><br>
                <small>K Value: <?= $_SESSION['train_info']['k_value'] ?> | 
                       Total Penyulang: <?= $_SESSION['train_info']['total_penyulang'] ?> | 
                       Data Training: <?= $_SESSION['train_info']['training_data_count'] ?></small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['train_info']); ?>
<?php endif; ?>