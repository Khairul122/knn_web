<?php
include 'koneksi.php';
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM data_pemeliharaan WHERE id_data_pemeliharaan = '$id'");
$data = mysqli_fetch_assoc($query);
?>

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
              <h4 class="mb-4">Edit Data Pemeliharaan</h4>
              <div class="card">
                <div class="card-body">
                  <form action="controller/DataPemeliharaanController.php" method="POST">
                    <input type="hidden" name="id_data_pemeliharaan" value="<?= $data['id_data_pemeliharaan'] ?>">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Nama Penyulang</label>
                        <input type="text" name="nama_penyulang" class="form-control" value="<?= $data['nama_penyulang'] ?>" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                          <option value="gardu" <?= $data['kategori'] == 'gardu' ? 'selected' : '' ?>>GARDU</option>
                          <option value="sutm" <?= $data['kategori'] == 'sutm' ? 'selected' : '' ?>>SUTM</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <?php
                      $fields = ['pengukuran', 'arrester', 'grounding', 'ultrasonic', 'cover_isolasi', 'pangkas', 'tebang', 'row_lain_lain', 'jamperan', 'kawat_terburai'];
                      foreach ($fields as $field) {
                        echo "
                          <div class='col-md-4 mb-3'>
                            <label>" . ucfirst(str_replace('_', ' ', $field)) . "</label>
                            <input type='number' name='$field' class='form-control' min='0' value='{$data[$field]}'>
                          </div>
                        ";
                      }
                      ?>
                      <div class="col-md-4 mb-3">
                        <label>Gangguan</label>
                        <select name="gangguan" class="form-control">
                          <option value="1" <?= $data['gangguan'] == 1 ? 'selected' : '' ?>>Ada Gangguan</option>
                          <option value="0" <?= $data['gangguan'] == 0 ? 'selected' : '' ?>>Tidak Ada Gangguan</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label>Set Label</label>
                        <select name="set_label" class="form-control">
                          <option value="latih" <?= $data['set_label'] == 'latih' ? 'selected' : '' ?>>Latih</option>
                          <option value="uji" <?= $data['set_label'] == 'uji' ? 'selected' : '' ?>>Uji</option>
                        </select>
                      </div>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Perbarui</button>
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
