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
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Edit Data SUTM</h4>
                                    <?php
                                    if (!isset($data) || !$data) {
                                        echo '<div class="alert alert-danger">Data SUTM tidak ditemukan!</div>';
                                        echo '<a href="index.php?page=data-sutm" class="btn btn-primary">Kembali</a>';
                                        exit;
                                    }
                                    
                                    $tanggalParts = explode('-', $data['tanggal'] ?? '');
                                    $bulan = $tanggalParts[0] ?? date('F');
                                    $tahun = $tanggalParts[1] ?? date('Y');
                                    ?>
                                    <form class="forms-sample" method="post" action="index.php?page=edit-sutm&id=<?= $data['id_sutm']; ?>">
                                        <input type="hidden" name="id_sutm" value="<?= $data['id_sutm']; ?>">
                                        
                                        <div class="form-group">
                                            <label for="nama_penyulang">Nama Penyulang</label>
                                            <input type="text" class="form-control" id="nama_penyulang" name="nama_penyulang" value="<?= $data['nama_penyulang']; ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="bulan_tahun">Bulan dan Tahun</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select name="bulan" class="form-control" required>
                                                        <option value="">Pilih Bulan</option>
                                                        <option value="Januari" <?= $bulan == 'Januari' ? 'selected' : ''; ?>>Januari</option>
                                                        <option value="Februari" <?= $bulan == 'Februari' ? 'selected' : ''; ?>>Februari</option>
                                                        <option value="Maret" <?= $bulan == 'Maret' ? 'selected' : ''; ?>>Maret</option>
                                                        <option value="April" <?= $bulan == 'April' ? 'selected' : ''; ?>>April</option>
                                                        <option value="Mei" <?= $bulan == 'Mei' ? 'selected' : ''; ?>>Mei</option>
                                                        <option value="Juni" <?= $bulan == 'Juni' ? 'selected' : ''; ?>>Juni</option>
                                                        <option value="Juli" <?= $bulan == 'Juli' ? 'selected' : ''; ?>>Juli</option>
                                                        <option value="Agustus" <?= $bulan == 'Agustus' ? 'selected' : ''; ?>>Agustus</option>
                                                        <option value="September" <?= $bulan == 'September' ? 'selected' : ''; ?>>September</option>
                                                        <option value="Oktober" <?= $bulan == 'Oktober' ? 'selected' : ''; ?>>Oktober</option>
                                                        <option value="November" <?= $bulan == 'November' ? 'selected' : ''; ?>>November</option>
                                                        <option value="Desember" <?= $bulan == 'Desember' ? 'selected' : ''; ?>>Desember</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="tahun" class="form-control" required>
                                                        <option value="">Pilih Tahun</option>
                                                        <?php 
                                                        $tahunSekarang = date('Y');
                                                        for($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) { 
                                                        ?>
                                                            <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : ''; ?>><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t1_inspeksi">T1 Inspeksi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t1_inspeksi" name="t1_inspeksi" value="<?= $data['t1_inspeksi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t1_realisasi">T1 Realisasi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t1_realisasi" name="t1_realisasi" value="<?= $data['t1_realisasi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t2_inspeksi">T2 Inspeksi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t2_inspeksi" name="t2_inspeksi" value="<?= $data['t2_inspeksi']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t2_realisasi">T2 Realisasi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t2_realisasi" name="t2_realisasi" value="<?= $data['t2_realisasi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pangkas_kms">Pangkas KMS</label>
                                                    <input type="number" step="0.01" class="form-control" id="pangkas_kms" name="pangkas_kms" value="<?= $data['pangkas_kms']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pangkas_batang">Pangkas Batang</label>
                                                    <input type="number" class="form-control" id="pangkas_batang" name="pangkas_batang" value="<?= $data['pangkas_batang']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tebang">Tebang</label>
                                                    <input type="number" class="form-control" id="tebang" name="tebang" value="<?= $data['tebang']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="row_lain">ROW Lain</label>
                                                    <input type="number" class="form-control" id="row_lain" name="row_lain" value="<?= $data['row_lain']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pin_isolator">Pin Isolator</label>
                                                    <input type="number" class="form-control" id="pin_isolator" name="pin_isolator" value="<?= $data['pin_isolator']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="suspension_isolator">Suspension Isolator</label>
                                                    <input type="number" class="form-control" id="suspension_isolator" name="suspension_isolator" value="<?= $data['suspension_isolator']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="traves_dan_armtie">Traves dan Armtie</label>
                                                    <input type="number" class="form-control" id="traves_dan_armtie" name="traves_dan_armtie" value="<?= $data['traves_dan_armtie']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tiang">Tiang</label>
                                                    <input type="number" class="form-control" id="tiang" name="tiang" value="<?= $data['tiang']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="accesoris_sutm">Accesoris SUTM</label>
                                                    <input type="number" class="form-control" id="accesoris_sutm" name="accesoris_sutm" value="<?= $data['accesoris_sutm']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fco_sutm">FCO SUTM</label>
                                                    <input type="number" class="form-control" id="fco_sutm" name="fco_sutm" value="<?= $data['fco_sutm']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="grounding_sutm">Grounding SUTM</label>
                                                    <input type="number" class="form-control" id="grounding_sutm" name="grounding_sutm" value="<?= $data['grounding_sutm']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kawat_terburai">Kawat Terburai</label>
                                                    <input type="number" class="form-control" id="kawat_terburai" name="kawat_terburai" value="<?= $data['kawat_terburai']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="jamperan_sutm">Jamperan SUTM</label>
                                                    <input type="number" class="form-control" id="jamperan_sutm" name="jamperan_sutm" value="<?= $data['jamperan_sutm']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="skur">Skur</label>
                                                    <input type="number" class="form-control" id="skur" name="skur" value="<?= $data['skur']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="ganti_kabel_isolasi">Ganti Kabel Isolasi</label>
                                                    <input type="number" class="form-control" id="ganti_kabel_isolasi" name="ganti_kabel_isolasi" value="<?= $data['ganti_kabel_isolasi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_cover_isolasi">Pemasangan Cover Isolasi</label>
                                                    <input type="number" class="form-control" id="pemasangan_cover_isolasi" name="pemasangan_cover_isolasi" value="<?= $data['pemasangan_cover_isolasi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_penghalang_panjang">Pemasangan Penghalang Panjang</label>
                                                    <input type="number" class="form-control" id="pemasangan_penghalang_panjang" name="pemasangan_penghalang_panjang" value="<?= $data['pemasangan_penghalang_panjang']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="alat_ultrasonik">Alat Ultrasonik</label>
                                                    <input type="number" class="form-control" id="alat_ultrasonik" name="alat_ultrasonik" value="<?= $data['alat_ultrasonik']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
                                        <a href="index.php?page=data-sutm" class="btn btn-light">Batal</a>
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