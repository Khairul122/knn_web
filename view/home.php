<?php include('template/header.php'); ?>

<body class="with-welcome-text">
  <div class="container-scroller">
    <?php include 'template/navbar.php'; ?>
    <div class="container-fluid page-body-wrapper">
      <?php include 'template/setting_panel.php'; ?>
      <?php include 'template/sidebar.php'; ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-sm-12">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Dashboard Sistem Klasifikasi Risiko Gangguan Jaringan</h4>
                  
                  <div class="row mt-4">
                    <div class="col-md-4">
                      <div class="card bg-primary text-white">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <h5 class="font-weight-bold mb-2">Data Gardu</h5>
                              <?php
                                include_once 'koneksi.php';
                                $query = "SELECT COUNT(*) as total FROM gardu";
                                $result = mysqli_query($koneksi, $query);
                                $data = mysqli_fetch_assoc($result);
                              ?>
                              <h3 class="mb-0"><?= number_format($data['total']) ?></h3>
                              <small>Total data pemeliharaan gardu</small>
                            </div>
                            <div class="icon-lg rounded-circle bg-white text-primary">
                              <i class="ti-server"></i>
                            </div>
                          </div>
                          <div class="mt-3">
                            <a href="index.php?page=data-gardu" class="text-white">
                              <small>Lihat Data <i class="ti-arrow-right ml-1"></i></small>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="card bg-success text-white">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <h5 class="font-weight-bold mb-2">Data SUTM</h5>
                              <?php
                                $query = "SELECT COUNT(*) as total FROM sutm";
                                $result = mysqli_query($koneksi, $query);
                                $data = mysqli_fetch_assoc($result);
                              ?>
                              <h3 class="mb-0"><?= number_format($data['total']) ?></h3>
                              <small>Total data pemeliharaan SUTM</small>
                            </div>
                            <div class="icon-lg rounded-circle bg-white text-success">
                              <i class="ti-pulse"></i>
                            </div>
                          </div>
                          <div class="mt-3">
                            <a href="index.php?page=data-sutm" class="text-white">
                              <small>Lihat Data <i class="ti-arrow-right ml-1"></i></small>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="card bg-danger text-white">
                        <div class="card-body">
                          <div class="d-flex justify-content-between align-items-center">
                            <div>
                              <h5 class="font-weight-bold mb-2">Klasifikasi Risiko</h5>
                              <?php
                                $query = "SELECT COUNT(*) as total FROM hasil_prediksi_risiko";
                                $result = mysqli_query($koneksi, $query);
                                $data = mysqli_fetch_assoc($result);
                              ?>
                              <h3 class="mb-0"><?= number_format($data['total']) ?></h3>
                              <small>Total klasifikasi penyulang</small>
                            </div>
                            <div class="icon-lg rounded-circle bg-white text-danger">
                              <i class="ti-stats-up"></i>
                            </div>
                          </div>
                          <div class="mt-3">
                            <a href="index.php?page=prediksi" class="text-white">
                              <small>Lihat Klasifikasi <i class="ti-arrow-right ml-1"></i></small>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                                  
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">Sistem Prediksi Risiko Gangguan Jaringan</h5>
                          <p>Selamat datang di Sistem Prediksi Risiko Gangguan Jaringan Listrik PLN berdasarkan data pemeliharaan. Sistem ini menggunakan metode K-Nearest Neighbors (KNN) untuk memprediksi tingkat risiko gangguan pada penyulang.</p>
                          <div class="row mt-4">
                            <div class="col-md-4">
                              <div class="d-flex align-items-center mb-3">
                                <div class="icon-md bg-primary text-white rounded-circle mr-3">
                                  <i class="ti-server"></i>
                                </div>
                                <div>
                                  <h6 class="mb-1">Data Pemeliharaan Gardu</h6>
                                  <p class="text-muted mb-0">Pengelolaan data pemeliharaan gardu</p>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="d-flex align-items-center mb-3">
                                <div class="icon-md bg-success text-white rounded-circle mr-3">
                                  <i class="ti-pulse"></i>
                                </div>
                                <div>
                                  <h6 class="mb-1">Data Pemeliharaan SUTM</h6>
                                  <p class="text-muted mb-0">Pengelolaan data pemeliharaan SUTM</p>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="d-flex align-items-center mb-3">
                                <div class="icon-md bg-danger text-white rounded-circle mr-3">
                                  <i class="ti-stats-up"></i>
                                </div>
                                <div>
                                  <h6 class="mb-1">Prediksi Risiko KNN</h6>
                                  <p class="text-muted mb-0">Prediksi risiko gangguan jaringan</p>
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
      </div>
    </div>
  </div>
  <?php include 'template/script.php'; ?>
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <script>
   $(document).ready(function() {
     var riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
     
     var riskLabels = [
       <?php foreach ($riskDistribution as $risk): ?>
         "<?= $risk['tingkat_risiko'] ?>",
       <?php endforeach; ?>
     ];
     
     var riskData = [
       <?php foreach ($riskDistribution as $risk): ?>
         <?= $risk['jumlah'] ?>,
       <?php endforeach; ?>
     ];
     
     var riskColors = [
       '#dc3545',  // Merah untuk TINGGI
       '#ffc107',  // Kuning untuk SEDANG
       '#28a745'   // Hijau untuk RENDAH
     ];
     
     var riskDistChart = new Chart(riskCtx, {
       type: 'doughnut',
       data: {
         labels: riskLabels,
         datasets: [{
           label: 'Distribusi Tingkat Risiko',
           data: riskData,
           backgroundColor: riskColors,
           borderWidth: 1
         }]
       },
       options: {
         responsive: true,
         maintainAspectRatio: false,
         legend: {
           position: 'bottom'
         },
         title: {
           display: true,
           text: 'Distribusi Tingkat Risiko Penyulang'
         },
         animation: {
           animateScale: true,
           animateRotate: true
         },
         tooltips: {
           callbacks: {
             label: function(tooltipItem, data) {
               var dataset = data.datasets[tooltipItem.datasetIndex];
               var total = dataset.data.reduce(function(previousValue, currentValue) {
                 return previousValue + currentValue;
               });
               var currentValue = dataset.data[tooltipItem.index];
               var percentage = Math.round((currentValue/total) * 100);
               return data.labels[tooltipItem.index] + ': ' + currentValue + ' (' + percentage + '%)';
             }
           }
         }
       }
     });
   });
 </script>
</body>

</html>