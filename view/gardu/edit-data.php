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
                                    <h4 class="card-title">Edit Data Gardu</h4>
                                    <?php
                                    $tanggalParts = explode(' ', $gardu['tanggal']);
                                    $bulan = $tanggalParts[0] ?? 'Januari';
                                    $tahun = $tanggalParts[1] ?? date('Y');
                                    ?>
                                    <form class="forms-sample" method="post" action="index.php?page=update-gardu">
                                        <input type="hidden" name="id_gardu" value="<?= $gardu['id_gardu']; ?>">
                                        
                                        <div class="form-group">
                                            <label for="nama_penyulang">Nama Penyulang</label>
                                            <input type="text" class="form-control" id="nama_penyulang" name="nama_penyulang" value="<?= $gardu['nama_penyulang']; ?>" required>
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
                                                    <label for="inpeksi_t1">Inpeksi T1</label>
                                                    <input type="number" class="form-control" id="inpeksi_t1" name="inpeksi_t1" value="<?= $gardu['inpeksi_t1']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="inpeksi_t2">Inpeksi T2</label>
                                                    <input type="number" class="form-control" id="inpeksi_t2" name="inpeksi_t2" value="<?= $gardu['inpeksi_t2']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pengukuran">Pengukuran</label>
                                                    <input type="number" class="form-control" id="pengukuran" name="pengukuran" value="<?= $gardu['pengukuran']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_arrester">Pergantian Arrester</label>
                                                    <input type="number" class="form-control" id="pergantian_arrester" name="pergantian_arrester" value="<?= $gardu['pergantian_arrester']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_fco">Pergantian FCO</label>
                                                    <input type="number" class="form-control" id="pergantian_fco" name="pergantian_fco" value="<?= $gardu['pergantian_fco']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="relokasi_gardu">Relokasi Gardu</label>
                                                    <input type="number" class="form-control" id="relokasi_gardu" name="relokasi_gardu" value="<?= $gardu['relokasi_gardu']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pembangunan_gardu_siapan">Pembangunan Gardu Siapan</label>
                                                    <input type="number" class="form-control" id="pembangunan_gardu_siapan" name="pembangunan_gardu_siapan" value="<?= $gardu['pembangunan_gardu_siapan']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="penyimbang_beban_gardu">Penyimbang Beban Gardu</label>
                                                    <input type="number" class="form-control" id="penyimbang_beban_gardu" name="penyimbang_beban_gardu" value="<?= $gardu['penyimbang_beban_gardu']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemecahan_beban_gardu">Pemecahan Beban Gardu</label>
                                                    <input type="number" class="form-control" id="pemecahan_beban_gardu" name="pemecahan_beban_gardu" value="<?= $gardu['pemecahan_beban_gardu']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="perubahan_tap_charger_trafo">Perubahan Tap Charger Trafo</label>
                                                    <input type="number" class="form-control" id="perubahan_tap_charger_trafo" name="perubahan_tap_charger_trafo" value="<?= $gardu['perubahan_tap_charger_trafo']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_box">Pergantian Box</label>
                                                    <input type="number" class="form-control" id="pergantian_box" name="pergantian_box" value="<?= $gardu['pergantian_box']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_opstic">Pergantian OPSTIC</label>
                                                    <input type="number" class="form-control" id="pergantian_opstic" name="pergantian_opstic" value="<?= $gardu['pergantian_opstic']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="perbaikan_grounding">Perbaikan Grounding</label>
                                                    <input type="number" class="form-control" id="perbaikan_grounding" name="perbaikan_grounding" value="<?= $gardu['perbaikan_grounding']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="accesoris_gardu">Accesoris Gardu</label>
                                                    <input type="number" class="form-control" id="accesoris_gardu" name="accesoris_gardu" value="<?= $gardu['accesoris_gardu']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pergantian_kabel_isolasi">Pergantian Kabel Isolasi</label>
                                                    <input type="number" class="form-control" id="pergantian_kabel_isolasi" name="pergantian_kabel_isolasi" value="<?= $gardu['pergantian_kabel_isolasi']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_cover_isolasi">Pemasangan Cover Isolasi</label>
                                                    <input type="number" class="form-control" id="pemasangan_cover_isolasi" name="pemasangan_cover_isolasi" value="<?= $gardu['pemasangan_cover_isolasi']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="pemasangan_penghalang_panjat">Pemasangan Penghalang Panjat</label>
                                                    <input type="number" class="form-control" id="pemasangan_penghalang_panjat" name="pemasangan_penghalang_panjat" value="<?= $gardu['pemasangan_penghalang_panjat']; ?>" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="alat_ultrasonik">Alat Ultrasonik</label>
                                                    <input type="number" class="form-control" id="alat_ultrasonik" name="alat_ultrasonik" value="<?= $gardu['alat_ultrasonik']; ?>" min="0">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
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