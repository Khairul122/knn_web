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
                                    <h4 class="card-title">Tambah Data Gardu</h4>
                                    <form action="index.php?page=simpan-manual-gardu" method="post">
                                        <div class="form-group">
                                            <label for="nama_penyulang">Nama Penyulang</label>
                                            <input type="text" class="form-control" id="nama_penyulang" name="nama_penyulang" placeholder="Nama Penyulang" required>
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
                                                    <label for="inpeksi_t1">Inpeksi T1</label>
                                                    <input type="number" class="form-control" id="inpeksi_t1" name="inpeksi_t1" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="inpeksi_t2">Inpeksi T2</label>
                                                    <input type="number" class="form-control" id="inpeksi_t2" name="inpeksi_t2" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pengukuran">Pengukuran</label>
                                                    <input type="number" class="form-control" id="pengukuran" name="pengukuran" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_arrester">Pergantian Arrester</label>
                                                    <input type="number" class="form-control" id="pergantian_arrester" name="pergantian_arrester" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_fco">Pergantian FCO</label>
                                                    <input type="number" class="form-control" id="pergantian_fco" name="pergantian_fco" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="relokasi_gardu">Relokasi Gardu</label>
                                                    <input type="number" class="form-control" id="relokasi_gardu" name="relokasi_gardu" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pembangunan_gardu_siapan">Pembangunan Gardu Siapan</label>
                                                    <input type="number" class="form-control" id="pembangunan_gardu_siapan" name="pembangunan_gardu_siapan" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="penyimbang_beban_gardu">Penyimbang Beban Gardu</label>
                                                    <input type="number" class="form-control" id="penyimbang_beban_gardu" name="penyimbang_beban_gardu" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemecahan_beban_gardu">Pemecahan Beban Gardu</label>
                                                    <input type="number" class="form-control" id="pemecahan_beban_gardu" name="pemecahan_beban_gardu" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="perubahan_tap_charger_trafo">Perubahan Tap Charger Trafo</label>
                                                    <input type="number" class="form-control" id="perubahan_tap_charger_trafo" name="perubahan_tap_charger_trafo" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_box">Pergantian Box</label>
                                                    <input type="number" class="form-control" id="pergantian_box" name="pergantian_box" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_opstic">Pergantian OPSTIC</label>
                                                    <input type="number" class="form-control" id="pergantian_opstic" name="pergantian_opstic" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="perbaikan_grounding">Perbaikan Grounding</label>
                                                    <input type="number" class="form-control" id="perbaikan_grounding" name="perbaikan_grounding" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="accesoris_gardu">Accesoris Gardu</label>
                                                    <input type="number" class="form-control" id="accesoris_gardu" name="accesoris_gardu" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_kabel_isolasi">Pergantian Kabel Isolasi</label>
                                                    <input type="number" class="form-control" id="pergantian_kabel_isolasi" name="pergantian_kabel_isolasi" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_cover_isolasi">Pemasangan Cover Isolasi</label>
                                                    <input type="number" class="form-control" id="pemasangan_cover_isolasi" name="pemasangan_cover_isolasi" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_penghalang_panjat">Pemasangan Penghalang Panjat</label>
                                                    <input type="number" class="form-control" id="pemasangan_penghalang_panjat" name="pemasangan_penghalang_panjat" value="0" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="alat_ultrasonik">Alat Ultrasonik</label>
                                                    <input type="number" class="form-control" id="alat_ultrasonik" name="alat_ultrasonik" value="0" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                        <a href="index.php?page=data-gardu" class="btn btn-light">Batal</a>
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