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
                  <h4 class="card-title">Prediksi dengan Metode KNN</h4>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <!-- Card for K Value Input -->
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

                            <!-- Additional parameters can be added here in the future -->
                            
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

                      <!-- Information Card -->
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
                      <!-- Placeholder for future content -->
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Hasil Prediksi Sebelumnya</h5>
                          <p class="card-text text-muted">
                            Hasil prediksi akan ditampilkan di sini setelah proses prediksi dijalankan.
                          </p>
                          <!-- Empty placeholder for previous results -->
                          <div class="text-center py-5">
                            <i class="ti-stats-up icon-lg text-muted"></i>
                            <p class="text-muted mt-3">Belum ada hasil prediksi</p>
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
      </div>
    </div>
  </div>
  <?php include 'view/template/script.php'; ?>
</body>

</html>