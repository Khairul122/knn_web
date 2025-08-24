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
                                        <h4 class="card-title mb-0">Import Data Gardu dari Excel</h4>
                                        <a href="index.php?page=data-gardu" class="btn btn-secondary btn-sm">Kembali</a>
                                    </div>

                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Upload File Excel (.xlsx)</label>
                                            <input type="file" name="file_excel" class="form-control" accept=".xlsx" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Preview</button>
                                    </form>
                                </div>
                            </div>

                            <?php
                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
                                require_once 'vendor/autoload.php';
                                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['file_excel']['tmp_name']);
                                $reader->setReadDataOnly(true);
                                $reader->setLoadSheetsOnly("LSK");
                                $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
                                $sheet = $spreadsheet->getActiveSheet();
                                $startRow = 9;
                                $highestRow = $sheet->getHighestDataRow();
                                $previewData = [];
                                for ($row = $startRow; $row <= $highestRow; $row++) {
                                    $nama = $sheet->getCell("B$row")->getValue();
                                    if (empty($nama)) continue;
                                    $data = [];
                                    $data['nama_penyulang'] = $nama;
                                    $colCodes = range('C', 'V');
                                    $fields = [
                                        't1_inspeksi',
                                        't1_realisasi',
                                        't2_inspeksi',
                                        't2_realisasi',
                                        'pengukuran',
                                        'pergantian_arrester',
                                        'pergantian_fco',
                                        'relokasi_gardu',
                                        'pembangunan_gardu_siapan',
                                        'penyimbang_beban_gardu',
                                        'pemecahan_beban_gardu',
                                        'perubahan_tap_charger_trafo',
                                        'pergantian_box',
                                        'pergantian_opstic',
                                        'perbaikan_grounding',
                                        'accesoris_gardu',
                                        'pergantian_kabel_isolasi',
                                        'pemasangan_cover_isolasi',
                                        'pemasangan_penghalang_panjat',
                                        'alat_ultrasonik'
                                    ];
                                    foreach ($fields as $i => $f) {
                                        $val = $sheet->getCell("{$colCodes[$i]}$row")->getValue();
                                        $val = trim((string)$val);
                                        $data[$f] = ($val === '-' || $val === '' || is_null($val)) ? 0 : (float)str_replace(',', '.', $val);
                                    }
                                    $previewData[] = $data;
                                }
                            ?>
                                <form action="index.php?page=import-gardu-save" method="post">
                                    <div class="card mt-4">
                                        <div class="card-body">
                                            <h5 class="card-title">Preview & Edit Data</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama Penyulang</th>
                                                            <th>TIER 1 INSPEKSI</th>
                                                            <th>TIER 1 REALISASI</th>
                                                            <th>TIER 2 INSPEKSI</th>
                                                            <th>TIER 2 REALISASI</th>
                                                            <th>PENGUKURAN</th>
                                                            <th>PERGANTIAN ARRESTER</th>
                                                            <th>PERGANTIAN FCO</th>
                                                            <th>RELOKASI GARDU</th>
                                                            <th>PEMBANGUNAN GARDU SISIPAN</th>
                                                            <th>PENYEIMBANGAN BEBAN GARDU</th>
                                                            <th>PEMECAHAN BEBAN GARDU</th>
                                                            <th>PERUBAHAN TAP CHAGER TRAFO</th>
                                                            <th>PERGANTIAN BOX PHB-TR</th>
                                                            <th>PERGANTIAN OPSTIC TRAFO</th>
                                                            <th>PERBAIKAN GROUNDING TRAFO (ARRESTER, TRAFO, BOX PHTR-TR)</th>
                                                            <th>ACCESOSRIS GARDU</th>
                                                            <th>PENGGANTIAN KABEL ISOLASI</th>
                                                            <th>PEMASANGAN COVER ISOLASI</th>
                                                            <th>PEMASANGAN PENGHALANG PANJAT</th>
                                                            <th>PEMASANGAN ALAT ULTRASONIC/INOVASI</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($previewData as $i => $row): ?>
                                                            <tr>
                                                                <td><?= $i + 1 ?></td>
                                                                <?php foreach ($row as $key => $val): ?>
                                                                    <td><input type="text" class="form-control form-control-sm" name="data[<?= $i ?>][<?= $key ?>]" value="<?= $val ?>"></td>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                                <div class="form-group">
                                                    <label>Bulan</label>
                                                    <select name="bulan" class="form-control" required>
                                                        <option value="">-- Pilih Bulan --</option>
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

                                                <div class="form-group">
                                                    <label>Tahun</label>
                                                    <input type="number" name="tahun" class="form-control" required>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success mt-3">Simpan ke Database</button>
                                        </div>
                                    </div>

                                </form>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'view/template/script.php'; ?>
</body>

</html>