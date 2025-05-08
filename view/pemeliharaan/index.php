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
                  <h4 class="card-title">Data Pemeliharaan</h4>
                  
                  <!-- Simple Filter Form -->
                  <div class="mb-4">
                    <form action="index.php?page=pemeliharaan-filter" method="POST" class="row">
                      <div class="col-md-3 form-group">
                        <select name="bulan" class="form-control form-control-sm">
                          <option value="">-- Pilih Bulan --</option>
                          <option value="January">Januari</option>
                          <option value="February">Februari</option>
                          <option value="March">Maret</option>
                          <option value="April">April</option>
                          <option value="May">Mei</option>
                          <option value="June">Juni</option>
                          <option value="July">Juli</option>
                          <option value="August">Agustus</option>
                          <option value="September">September</option>
                          <option value="October">Oktober</option>
                          <option value="November">November</option>
                          <option value="December">Desember</option>
                        </select>
                      </div>
                      <div class="col-md-3 form-group">
                        <select name="tahun" class="form-control form-control-sm">
                          <option value="">-- Pilih Tahun --</option>
                          <?php 
                          $currentYear = date('Y');
                          for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                            echo "<option value='$i'>$i</option>";
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-3 form-group">
                        <select name="objek" class="form-control form-control-sm">
                          <option value="">-- Pilih Objek --</option>
                          <option value="sutm">SUTM</option>
                          <option value="gardu">Gardu</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                          <i class="ti-filter"></i> Filter
                        </button>
                        <a href="index.php?page=pemeliharaan" class="btn btn-secondary btn-sm">
                          <i class="ti-reload"></i> Reset
                        </a>
                      </div>
                    </form>
                  </div>
                  
                  <!-- Basic Data Table -->
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Tanggal</th>
                          <th>Nama Objek</th>
                          <th>ID Sub Kategori</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($pemeliharaan)) : ?>
                          <tr>
                            <td colspan="4" class="text-center">Tidak ada data</td>
                          </tr>
                        <?php else : ?>
                          <?php $no = 1; foreach ($pemeliharaan as $data) : ?>
                            <tr>
                              <td><?= $no++ ?></td>
                              <td><?= $data['tanggal'] ?></td>
                              <td>
                                <?php 
                                  if ($data['nama_objek'] == 'sutm') {
                                    echo '<span class="badge bg-primary">SUTM</span>';
                                  } elseif ($data['nama_objek'] == 'gardu') {
                                    echo '<span class="badge bg-success">Gardu</span>';
                                  } else {
                                    echo $data['nama_objek'];
                                  }
                                ?>
                              </td>
                              <td><?= $data['id_sub_kategori'] ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
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