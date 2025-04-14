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
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Data Pemeliharaan</h4>
                                <a href="index.php?page=data-pemeliharaan-tambah" class="btn btn-primary">Tambah Data</a>
                            </div>
                            <?php
                            if (isset($_GET['status'])) {
                                $status = $_GET['status'];
                                $toastClass = ($status === 'success') ? 'bg-success' : 'bg-danger';
                                $toastMessage = ($status === 'success') ? 'Sukses!' : 'Gagal!';
                                echo <<<TOAST
                <div class='position-fixed top-0 end-0 p-3' style='z-index: 9999;'>
                  <div class='toast align-items-center text-white {$toastClass} border-0 show' role='alert'>
                    <div class='d-flex'>
                      <div class='toast-body'>{$toastMessage}</div>
                      <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast'></button>
                    </div>
                  </div>
                </div>
                TOAST;
                            }
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Penyulang</th>
                                                    <th>Kategori</th>
                                                    <th>Pengukuran</th>
                                                    <th>Arrester</th>
                                                    <th>Grounding</th>
                                                    <th>Ultrasonic</th>
                                                    <th>Cover Isolasi</th>
                                                    <th>Pangkas</th>
                                                    <th>Tebang</th>
                                                    <th>ROW Lain</th>
                                                    <th>Jamperan</th>
                                                    <th>Kawat Terburai</th>
                                                    <th>Gangguan</th>
                                                    <th>Label</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                include 'controller/DataPemeliharaanController.php';
                                                $data = getDataPemeliharaan();
                                                $no = 1;
                                                foreach ($data as $row) {
                                                    $gangguanText = $row['gangguan'] == 1 ? 'Ada Gangguan' : 'Tidak Ada Gangguan';
                                                    echo <<<ROW
                          <tr>
                            <td>{$no}</td>
                            <td>{$row['nama_penyulang']}</td>
                            <td>{$row['kategori']}</td>
                            <td>{$row['pengukuran']}</td>
                            <td>{$row['arrester']}</td>
                            <td>{$row['grounding']}</td>
                            <td>{$row['ultrasonic']}</td>
                            <td>{$row['cover_isolasi']}</td>
                            <td>{$row['pangkas']}</td>
                            <td>{$row['tebang']}</td>
                            <td>{$row['row_lain_lain']}</td>
                            <td>{$row['jamperan']}</td>
                            <td>{$row['kawat_terburai']}</td>
                           <td>{$gangguanText}</td>
                            <td>{$row['set_label']}</td>
                            <td>
                              <a href='index.php?page=data-pemeliharaan-edit&id={$row['id_data_pemeliharaan']}' class='btn btn-sm btn-warning'>Edit</a>
                              <a href='controller/DataPemeliharaanController.php?action=delete&id={$row['id_data_pemeliharaan']}' class='btn btn-sm btn-danger' onclick="return confirm('Yakin hapus data?')">Delete</a>
                            </td>
                          </tr>
                          ROW;
                                                    $no++;
                                                }
                                                ?>
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