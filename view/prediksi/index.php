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
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Prediksi Risiko dengan Metode KNN</h4>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <!-- Form untuk Parameter KNN -->
                      <div class="card border">
                        <div class="card-header bg-primary text-white">
                          <h5 class="mb-0">Parameter K-Nearest Neighbors</h5>
                        </div>
                        <div class="card-body">
                          <form action="index.php?page=prediksi-process" method="POST">
                            <div class="form-group mb-4">
                              <label for="k_value" class="form-label">Nilai K</label>
                              <div class="input-group">
                                <input type="number" class="form-control" id="k_value" name="k_value" 
                                       min="1" max="20" value="3" required>
                                <span class="input-group-text">tetangga terdekat</span>
                              </div>
                              <div class="form-text text-muted">
                                <i class="ti-info-alt"></i> 
                                Masukkan nilai K (1-20) untuk menentukan jumlah tetangga terdekat yang digunakan dalam algoritma KNN.
                              </div>
                            </div>
                            
                            <div class="mt-4">
                              <button type="submit" class="btn btn-primary">
                                <i class="ti-stats-up"></i> Mulai Prediksi
                              </button>
                              <button type="reset" class="btn btn-light">
                                <i class="ti-reload"></i> Reset
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>

                      <!-- Informasi tentang KNN -->
                      <div class="card mt-4">
                        <div class="card-body">
                          <h6 class="card-title"><i class="ti-info-alt text-info"></i> Tentang Metode KNN</h6>
                          <p>K-Nearest Neighbors (KNN) adalah algoritma supervised learning yang digunakan untuk klasifikasi dan regresi. 
                             Algoritma ini bekerja dengan mencari K data terdekat dari data yang akan diprediksi.</p>
                          <p>Beberapa hal yang perlu diperhatikan:</p>
                          <ul>
                            <li>Nilai K yang kecil bisa sensitif terhadap noise</li>
                            <li>Nilai K yang besar bisa menghasilkan prediksi yang smooth tetapi kurang detail</li>
                            <li>Nilai K yang umum digunakan adalah 3, 5, atau 7</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <?php if (isset($hasPrediction) && $hasPrediction): ?>
                      <!-- Ringkasan Hasil Prediksi -->
                      <div class="card">
                        <div class="card-header bg-success text-white">
                          <h5 class="mb-0">Ringkasan Hasil Prediksi</h5>
                        </div>
                        <div class="card-body">
                          <p class="card-text">
                            Prediksi terakhir: <strong><?= $lastPrediction['tanggal_prediksi'] ?></strong>
                            dengan nilai K = <strong><?= $lastPrediction['k_value'] ?></strong>
                          </p>
                          
                          <div class="table-responsive">
                            <table class="table table-bordered">
                              <thead>
                                <tr class="text-center">
                                  <th>Tingkat Risiko</th>
                                  <th>Jumlah Penyulang</th>
                                  <th>Rata-Rata Nilai</th>
                                
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($predictionSummary as $summary): ?>
                                <tr>
                                  <td>
                                    <span class="badge bg-<?= strtolower($summary['tingkat_risiko']) === 'tinggi' ? 'danger' : (strtolower($summary['tingkat_risiko']) === 'sedang' ? 'warning' : 'success') ?>">
                                      <?= $summary['tingkat_risiko'] ?>
                                    </span>
                                  </td>
                                  <td class="text-center"><?= $summary['jumlah_penyulang'] ?></td>
                                  <td class="text-center"><?= number_format($summary['rata_nilai_risiko'], 2) ?></td>
                                
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>

                      <!-- Tabel Hasil Prediksi -->
                      <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                          <h5 class="mb-0">Hasil Prediksi Risiko</h5>
                        </div>
                        <div class="card-body">
                          <div class="table-responsive">
                            <table class="table table-striped table-hover">
                              <thead>
                                <tr>
                                  <th>Penyulang</th>
                                  <th>Tingkat Risiko</th>
                                  <th>Nilai Risiko</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($predictionResults as $pred): ?>
                                <tr>
                                  <td><?= $pred['nama_penyulang'] ?></td>
                                  <td>
                                    <span class="badge bg-<?= strtolower($pred['tingkat_risiko']) === 'tinggi' ? 'danger' : (strtolower($pred['tingkat_risiko']) === 'sedang' ? 'warning' : 'success') ?>">
                                      <?= $pred['tingkat_risiko'] ?>
                                    </span>
                                  </td>
                                  <td><?= number_format($pred['nilai_risiko'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <?php else: ?>
                      <!-- Belum ada prediksi -->
                      <div class="card">
                        <div class="card-body text-center py-5">
                          <i class="ti-stats-up icon-lg text-muted"></i>
                          <p class="text-muted mt-3">Belum ada hasil prediksi. Silakan klik tombol "Mulai Prediksi" untuk memulai proses prediksi risiko.</p>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include 'view/template/script.php'; ?>
</body>
</html>