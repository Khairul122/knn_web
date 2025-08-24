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
                                    <h4 class="card-title">Tambah Data SUTM</h4>
                                    <form class="forms-sample" method="post" action="index.php?page=simpan-manual-sutm">
                                        <div class="form-group">
                                            <label for="nama_penyulang">Nama Penyulang</label>
                                            <input type="text" class="form-control" id="nama_penyulang" name="nama_penyulang" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="bulan_tahun">Bulan dan Tahun</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <select name="bulan" class="form-control" required>
                                                        <option value="">Pilih Bulan</option>
                                                        <option value="Januari">Januari</option>
                                                        <option value="Februari">Februari</option>
                                                        <option value="Maret">Maret</option>
                                                        <option value="April">April</option>
                                                        <option value="Mei">Mei</option>
                                                        <option value="Juni">Juni</option>
                                                        <option value="Juli">Juli</option>
                                                        <option value="Agustus">Agustus</option>
                                                        <option value="September">September</option>
                                                        <option value="Oktober">Oktober</option>
                                                        <option value="November">November</option>
                                                        <option value="Desember">Desember</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="tahun" class="form-control" required>
                                                        <option value="">Pilih Tahun</option>
                                                        <?php 
                                                        $tahunSekarang = date('Y');
                                                        for($i = $tahunSekarang; $i >= $tahunSekarang - 5; $i--) { 
                                                        ?>
                                                            <option value="<?= $i ?>"><?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t1_inspeksi">T1 Inspeksi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t1_inspeksi" name="t1_inspeksi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t1_realisasi">T1 Realisasi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t1_realisasi" name="t1_realisasi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t2_inspeksi">T2 Inspeksi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t2_inspeksi" name="t2_inspeksi" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="t2_realisasi">T2 Realisasi</label>
                                                    <input type="number" step="0.01" class="form-control" id="t2_realisasi" name="t2_realisasi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pangkas_kms">Pangkas KMS</label>
                                                    <input type="number" step="0.01" class="form-control" id="pangkas_kms" name="pangkas_kms" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pangkas_batang">Pangkas Batang</label>
                                                    <input type="number" class="form-control" id="pangkas_batang" name="pangkas_batang" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tebang">Tebang</label>
                                                    <input type="number" class="form-control" id="tebang" name="tebang" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="row_lain">ROW Lain</label>
                                                    <input type="number" class="form-control" id="row_lain" name="row_lain" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pin_isolator">Pin Isolator</label>
                                                    <input type="number" class="form-control" id="pin_isolator" name="pin_isolator" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="suspension_isolator">Suspension Isolator</label>
                                                    <input type="number" class="form-control" id="suspension_isolator" name="suspension_isolator" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="traves_dan_armtie">Traves dan Armtie</label>
                                                    <input type="number" class="form-control" id="traves_dan_armtie" name="traves_dan_armtie" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="tiang">Tiang</label>
                                                    <input type="number" class="form-control" id="tiang" name="tiang" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="accesoris_sutm">Accesoris SUTM</label>
                                                    <input type="number" class="form-control" id="accesoris_sutm" name="accesoris_sutm" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="fco_sutm">FCO SUTM</label>
                                                    <input type="number" class="form-control" id="fco_sutm" name="fco_sutm" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="grounding_sutm">Grounding SUTM</label>
                                                    <input type="number" class="form-control" id="grounding_sutm" name="grounding_sutm" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="kawat_terburai">Kawat Terburai</label>
                                                    <input type="number" class="form-control" id="kawat_terburai" name="kawat_terburai" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="jamperan_sutm">Jamperan SUTM</label>
                                                    <input type="number" class="form-control" id="jamperan_sutm" name="jamperan_sutm" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="skur">Skur</label>
                                                    <input type="number" class="form-control" id="skur" name="skur" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="ganti_kabel_isolasi">Ganti Kabel Isolasi</label>
                                                    <input type="number" class="form-control" id="ganti_kabel_isolasi" name="ganti_kabel_isolasi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_cover_isolasi">Pemasangan Cover Isolasi</label>
                                                    <input type="number" class="form-control" id="pemasangan_cover_isolasi" name="pemasangan_cover_isolasi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_penghalang_panjang">Pemasangan Penghalang Panjang</label>
                                                    <input type="number" class="form-control" id="pemasangan_penghalang_panjang" name="pemasangan_penghalang_panjang" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="alat_ultrasonik">Alat Ultrasonik</label>
                                                    <input type="number" class="form-control" id="alat_ultrasonik" name="alat_ultrasonik" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary mr-2">Simpan Data</button>
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