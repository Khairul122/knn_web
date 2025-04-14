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
              <h4 class="mb-4">Tambah Data Pemeliharaan</h4>
              <div class="card">
                <div class="card-body">
                  <form action="controller/DataPemeliharaanController.php" method="POST">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Nama Penyulang</label>
                        <input type="text" name="nama_penyulang" class="form-control" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                          <option value="gardu">GARDU</option>
                          <option value="sutm">SUTM</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <?php
                      $fields = ['pengukuran', 'arrester', 'grounding', 'ultrasonic', 'cover_isolasi', 'pangkas', 'tebang', 'row_lain_lain', 'jamperan', 'kawat_terburai'];
                      foreach ($fields as $field) {
                        echo "<div class='col-md-4 mb-3'>
                                <label>" . ucfirst(str_replace('_', ' ', $field)) . "</label>
                                <input type='number' name='$field' class='form-control' min='0' value='0'>
                              </div>";
                      }
                      ?>
                      <div class="col-md-4 mb-3">
                        <label>Gangguan</label>
                        <select name="gangguan" class="form-control" required>
                          <option value="1">Gangguan</option>
                          <option value="0">Tidak Ada Gangguan</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label>Set Label</label>
                        <select name="set_label" class="form-control" required>
                          <option value="latih">Latih</option>
                          <option value="uji">Uji</option>
                        </select>
                      </div>
                    </div>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=data-pemeliharaan" class="btn btn-secondary">Kembali</a>
                  </form>
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