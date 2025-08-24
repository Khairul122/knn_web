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
                                        <h4 class="card-title mb-0">Data Pemeliharaan Gardu</h4>
                                        <div>
                                            <a href="index.php?page=tambah-data-gardu" class="btn btn-sm btn-primary">Tambah Data</a>
                                            <a href="index.php?page=import-form-gardu" class="btn btn-sm btn-success">Import</a>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <form action="" method="get" class="d-flex">
                                                <input type="hidden" name="page" value="data-gardu">
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
                                                    <th>Pengukuran</th>
                                                    <th>Pergantian Arrester</th>
                                                    <th>Pergantian FCO</th>
                                                    <th>Relokasi Gardu</th>
                                                    <th>Pembangunan Gardu Siapan</th>
                                                    <th>Penyimbang Beban</th>
                                                    <th>Pemecahan Beban</th>
                                                    <th>Perubahan Tap Charger Trafo</th>
                                                    <th>Pergantian Box</th>
                                                    <th>Pergantian OPSTIC</th>
                                                    <th>Perbaikan Grounding</th>
                                                    <th>Accesoris Gardu</th>
                                                    <th>Pergantian Kabel Isolasi</th>
                                                    <th>Pemasangan Cover Isolasi</th>
                                                    <th>Pemasangan Penghalang Panjat</th>
                                                    <th>Alat Ultrasonik</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                foreach ($dataGardu as $gardu) {
                                                ?>
                                                    <tr>
                                                        <td><?= $no++; ?></td>
                                                        <td><?= $gardu['nama_penyulang']; ?></td>
                                                        <td><?= $gardu['tanggal']; ?></td>
                                                        <td><?= $gardu['t1_inspeksi']; ?></td>
                                                        <td><?= $gardu['t1_realisasi']; ?></td>
                                                        <td><?= $gardu['t2_inspeksi']; ?></td>
                                                        <td><?= $gardu['t2_realisasi']; ?></td>
                                                        <td><?= $gardu['pengukuran']; ?></td>
                                                        <td><?= $gardu['pergantian_arrester']; ?></td>
                                                        <td><?= $gardu['pergantian_fco']; ?></td>
                                                        <td><?= $gardu['relokasi_gardu']; ?></td>
                                                        <td><?= $gardu['pembangunan_gardu_siapan']; ?></td>
                                                        <td><?= $gardu['penyimbang_beban_gardu']; ?></td>
                                                        <td><?= $gardu['pemecahan_beban_gardu']; ?></td>
                                                        <td><?= $gardu['perubahan_tap_charger_trafo']; ?></td>
                                                        <td><?= $gardu['pergantian_box']; ?></td>
                                                        <td><?= $gardu['pergantian_opstic']; ?></td>
                                                        <td><?= $gardu['perbaikan_grounding']; ?></td>
                                                        <td><?= $gardu['accesoris_gardu']; ?></td>
                                                        <td><?= $gardu['pergantian_kabel_isolasi']; ?></td>
                                                        <td><?= $gardu['pemasangan_cover_isolasi']; ?></td>
                                                        <td><?= $gardu['pemasangan_penghalang_panjat']; ?></td>
                                                        <td><?= $gardu['alat_ultrasonik']; ?></td>
                                                        <td>
                                                            <a href="index.php?page=edit-gardu&id=<?= $gardu['id_gardu']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                            <a href="index.php?page=hapus-gardu&id=<?= $gardu['id_gardu']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
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