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
                                   <div class="d-flex justify-content-between align-items-center mb-3">
                                       <h4 class="card-title mb-0">Data Pemeliharaan SUTM</h4>
                                       <div>
                                           <a href="index.php?page=tambah-data-sutm" class="btn btn-sm btn-primary">Tambah Data</a>
                                           <a href="index.php?page=import-form-sutm" class="btn btn-sm btn-success">Import</a>
                                       </div>
                                   </div>

                                   <div class="row mb-3">
                                       <div class="col-md-6">
                                           <form action="" method="get" class="d-flex">
                                               <input type="hidden" name="page" value="data-sutm">
                                               <div class="form-group mr-2 mb-0">
                                                   <select name="bulan" class="form-control form-control-sm">
                                                       <option value="">Semua Bulan</option>
                                                       <option value="Januari" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Januari' ? 'selected' : '' ?>>Januari</option>
                                                       <option value="Februari" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Februari' ? 'selected' : '' ?>>Februari</option>
                                                       <option value="Maret" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Maret' ? 'selected' : '' ?>>Maret</option>
                                                       <option value="April" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'April' ? 'selected' : '' ?>>April</option>
                                                       <option value="Mei" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Mei' ? 'selected' : '' ?>>Mei</option>
                                                       <option value="Juni" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Juni' ? 'selected' : '' ?>>Juni</option>
                                                       <option value="Juli" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Juli' ? 'selected' : '' ?>>Juli</option>
                                                       <option value="Agustus" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Agustus' ? 'selected' : '' ?>>Agustus</option>
                                                       <option value="September" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'September' ? 'selected' : '' ?>>September</option>
                                                       <option value="Oktober" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Oktober' ? 'selected' : '' ?>>Oktober</option>
                                                       <option value="November" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'November' ? 'selected' : '' ?>>November</option>
                                                       <option value="Desember" <?= isset($_GET['bulan']) && $_GET['bulan'] == 'Desember' ? 'selected' : '' ?>>Desember</option>
                                                   </select>
                                               </div>
                                               <div class="form-group mr-2 mb-0">
                                                   <select name="tahun" class="form-control form-control-sm">
                                                       <option value="">Semua Tahun</option>
                                                       <?php
                                                       $tahunSekarang = date('Y');
                                                       for ($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) { ?>
                                                           <option value="<?= $i ?>" <?= isset($_GET['tahun']) && $_GET['tahun'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                                                       <?php } ?>
                                                   </select>
                                               </div>
                                               <button type="submit" class="btn btn-sm btn-info">Filter</button>
                                           </form>
                                       </div>
                                   </div>

                                   <div class="table-responsive">
                                       <table class="table table-bordered">
                                           <thead>
                                               <tr>
                                                   <th>No</th>
                                                   <th>Nama Penyulang</th>
                                                   <th>Bulan/Tahun</th>
                                                   <th>T1 Inpeksi</th>
                                                   <th>T1 Realisasi</th>
                                                   <th>T2 Inpeksi</th>
                                                   <th>T2 Realisasi</th>
                                                   <th>Pangkas KMS</th>
                                                   <th>Pangkas Batang</th>
                                                   <th>Tebang</th>
                                                   <th>ROW Lain</th>
                                                   <th>Pin Isolator</th>
                                                   <th>Suspension Isolator</th>
                                                   <th>Traves dan Armtie</th>
                                                   <th>Tiang</th>
                                                   <th>Accesoris SUTM</th>
                                                   <th>Arrester SUTM</th>
                                                   <th>FCO SUTM</th>
                                                   <th>Grounding SUTM</th>
                                                   <th>Perbaikan Andong Kendor</th>
                                                   <th>Kawat Terburai</th>
                                                   <th>Jamperan SUTM</th>
                                                   <th>Skur</th>
                                                   <th>Ganti Kabel Isolasi</th>
                                                   <th>Pemasangan Cover Isolasi</th>
                                                   <th>Pemasangan Penghalang Panjang</th>
                                                   <th>Alat Ultrasonik</th>
                                                   <th>Aksi</th>
                                               </tr>
                                           </thead>
                                           <tbody>
                                               <?php
                                               $no = 1;
                                               foreach ($dataSutm as $sutm) {
                                               ?>
                                                   <tr>
                                                       <td><?= $no++; ?></td>
                                                       <td><?= $sutm['nama_penyulang']; ?></td>
                                                       <td><?= $sutm['tanggal']; ?></td>
                                                       <td><?= $sutm['t1_inspeksi']; ?></td>
                                                       <td><?= $sutm['t1_realisasi']; ?></td>
                                                       <td><?= $sutm['t2_inspeksi']; ?></td>
                                                       <td><?= $sutm['t2_realisasi']; ?></td>
                                                       <td><?= $sutm['pangkas_kms']; ?></td>
                                                       <td><?= $sutm['pangkas_batang']; ?></td>
                                                       <td><?= $sutm['tebang']; ?></td>
                                                       <td><?= $sutm['row_lain']; ?></td>
                                                       <td><?= $sutm['pin_isolator']; ?></td>
                                                       <td><?= $sutm['suspension_isolator']; ?></td>
                                                       <td><?= $sutm['traves_dan_armtie']; ?></td>
                                                       <td><?= $sutm['tiang']; ?></td>
                                                       <td><?= $sutm['accesoris_sutm']; ?></td>
                                                       <td><?= $sutm['arrester_sutm']; ?></td>
                                                       <td><?= $sutm['fco_sutm']; ?></td>
                                                       <td><?= $sutm['grounding_sutm']; ?></td>
                                                       <td><?= $sutm['perbaikan_andong_kendor']; ?></td>
                                                       <td><?= $sutm['kawat_terburai']; ?></td>
                                                       <td><?= $sutm['jamperan_sutm']; ?></td>
                                                       <td><?= $sutm['skur']; ?></td>
                                                       <td><?= $sutm['ganti_kabel_isolasi']; ?></td>
                                                       <td><?= $sutm['pemasangan_cover_isolasi']; ?></td>
                                                       <td><?= $sutm['pemasangan_penghalang_panjang']; ?></td>
                                                       <td><?= $sutm['alat_ultrasonik']; ?></td>
                                                       <td>
                                                           <a href="index.php?page=edit-sutm&id=<?= $sutm['id_sutm']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                           <a href="index.php?page=hapus-sutm&id=<?= $sutm['id_sutm']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                                       </td>
                                                   </tr>
                                               <?php } ?>
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