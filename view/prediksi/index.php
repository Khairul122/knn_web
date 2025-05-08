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
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="card-title mb-0">Prediksi Risiko dengan Metode KNN</h4>
                                        <div>
                                            <?php if (isset($hasPrediction) && $hasPrediction): ?>
                                                <button id="exportPdfBtn" class="btn btn-danger mr-2">
                                                    <i class="ti-file"></i> Export PDF
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if (isset($_SESSION['success_message'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?= $_SESSION['success_message'] ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <?php unset($_SESSION['success_message']); ?>
                                    <?php endif; ?>

                                    <div id="predictionContent">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border">
                                                    <div class="card-header bg-primary text-white">
                                                        <h5 class="mb-0">Parameter K-Nearest Neighbors</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="index.php?page=prediksi-process" method="POST">
                                                            <div class="form-group mb-4">
                                                                <label for="k_value" class="form-label">Nilai K</label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control" id="k_value" name="k_value"
                                                                        min="1" max="20" value="3" required>
                                                                    <span class="input-group-text">tetangga terdekat</span>
                                                                </div>
                                                                <div class="form-text text-muted">
                                                                    <i class="ti-info-alt"></i>
                                                                    Masukkan nilai K (1-20) untuk menentukan jumlah tetangga terdekat yang digunakan dalam algoritma KNN.
                                                                </div>
                                                            </div>

                                                            <div class="mt-4">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="ti-stats-up"></i> Mulai Prediksi
                                                                </button>
                                                                <button type="reset" class="btn btn-light">
                                                                    <i class="ti-reload"></i> Reset
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <div class="card mt-4">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><i class="ti-info-alt text-info"></i> Tentang Metode KNN</h6>
                                                        <p>K-Nearest Neighbors (KNN) adalah algoritma supervised learning yang digunakan untuk klasifikasi dan regresi.
                                                            Algoritma ini bekerja dengan mencari K data terdekat dari data yang akan diprediksi.</p>
                                                        <p>Beberapa hal yang perlu diperhatikan:</p>
                                                        <ul>
                                                            <li>Nilai K yang kecil bisa sensitif terhadap noise</li>
                                                            <li>Nilai K yang besar bisa menghasilkan prediksi yang smooth tetapi kurang detail</li>
                                                            <li>Nilai K yang umum digunakan adalah 3, 5, atau 7</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <?php if (isset($hasPrediction) && $hasPrediction): ?>
                                                    <div class="card">
                                                        <div class="card-header bg-success text-white">
                                                            <h5 class="mb-0">Ringkasan Hasil Prediksi</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <p class="card-text">
                                                                Prediksi terakhir: <strong><?= $lastPrediction['tanggal_prediksi'] ?></strong>
                                                                dengan nilai K = <strong><?= $lastPrediction['k_value'] ?></strong>
                                                            </p>
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr class="text-center">
                                                                            <th>Tingkat Risiko</th>
                                                                            <th>Jumlah Penyulang</th>
                                                                            <th>Rata-Rata Nilai</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($predictionSummary as $summary): ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <span class="badge bg-<?= strtolower($summary['tingkat_risiko']) === 'tinggi' ? 'danger' : (strtolower($summary['tingkat_risiko']) === 'sedang' ? 'warning' : 'success') ?>">
                                                                                        <?= $summary['tingkat_risiko'] ?>
                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-center"><?= $summary['jumlah_penyulang'] ?></td>
                                                                                <td class="text-center"><?= number_format($summary['rata_nilai_risiko'], 2) ?></td>

                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card mt-4">
                                                        <div class="card-header bg-primary text-white">
                                                            <h5 class="mb-0">Hasil Prediksi Risiko</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table id="predictionTable" class="table table-striped table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>No</th>
                                                                            <th>Penyulang</th>
                                                                            <th>Tingkat Risiko</th>
                                                                            <th>Nilai Risiko</th>
                                                                            <th>Total Kegiatan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $no = 1;
                                                                        foreach ($predictionResults as $pred):
                                                                            $badgeClass = strtolower($pred['tingkat_risiko']) === 'tinggi' ? 'bg-danger' : (strtolower($pred['tingkat_risiko']) === 'sedang' ? 'bg-warning' : 'bg-success');
                                                                        ?>
                                                                            <tr>
                                                                                <td><?= $no++ ?></td>
                                                                                <td><?= $pred['nama_penyulang'] ?></td>
                                                                                <td>
                                                                                    <span class="badge <?= $badgeClass ?>"><?= $pred['tingkat_risiko'] ?></span>
                                                                                </td>
                                                                                <td><?= number_format($pred['nilai_risiko'], 2) ?></td>
                                                                                <td><?= $pred['total_kegiatan'] ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>

                                                    <div class="card">
                                                        <div class="card-body text-center py-5">
                                                            <i class="ti-stats-up icon-lg text-muted"></i>
                                                            <p class="text-muted mt-3">Belum ada hasil prediksi. Silakan klik tombol "Mulai Prediksi" untuk memulai proses prediksi risiko.</p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#exportPdfBtn").click(function() {
                exportPredictionToPdf();
            });

            function exportPredictionToPdf() {
                const {
                    jsPDF
                } = window.jspdf;

                const doc = new jsPDF('landscape');

                doc.setFontSize(18);
                doc.text('Hasil Prediksi Risiko Gangguan Jaringan Listrik', 14, 22);

                doc.setFontSize(10);
                doc.text('Tanggal Cetak: ' + new Date().toLocaleDateString('id-ID'), 14, 30);

                doc.setFontSize(12);
                doc.text('Metode: K-Nearest Neighbors (KNN)', 14, 38);
                doc.text('Nilai K: <?= $lastPrediction['k_value'] ?? "3" ?>', 14, 44);
                doc.text('Tanggal Prediksi: <?= $lastPrediction['tanggal_prediksi'] ?? date('d-m-Y H:i') ?>', 14, 50);

                doc.setFontSize(14);
                doc.text('Ringkasan Hasil Prediksi', 14, 60);

                const summaryColumns = ['Tingkat Risiko', 'Jumlah Penyulang', 'Rata-Rata Nilai Risiko'];
                const summaryData = [
                    <?php foreach ($predictionSummary as $summary): ?>['<?= $summary['tingkat_risiko'] ?>', '<?= $summary['jumlah_penyulang'] ?>', '<?= number_format($summary['rata_nilai_risiko'], 2) ?>'],
                    <?php endforeach; ?>
                ];

                doc.autoTable({
                    startY: 65,
                    head: [summaryColumns],
                    body: summaryData,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255
                    },
                    styles: {
                        fontSize: 10
                    }
                });


                doc.setFontSize(14);
                const finalY = doc.lastAutoTable.finalY || 110;
                doc.text('Detail Hasil Prediksi', 14, finalY + 10);

                const predictionColumns = ['No', 'Nama Penyulang', 'Tingkat Risiko', 'Nilai Risiko', 'Total Kegiatan'];
                const predictionData = [];

                <?php $no = 1;
                foreach ($predictionResults as $pred): ?>
                    predictionData.push([
                        '<?= $no++ ?>',
                        '<?= $pred['nama_penyulang'] ?>',
                        '<?= $pred['tingkat_risiko'] ?>',
                        '<?= number_format($pred['nilai_risiko'], 2) ?>',
                        '<?= $pred['total_kegiatan'] ?>'
                    ]);
                <?php endforeach; ?>

                doc.autoTable({
                    startY: finalY + 15,
                    head: [predictionColumns],
                    body: predictionData,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [41, 128, 185],
                        textColor: 255
                    },
                    styles: {
                        fontSize: 10
                    },
                    columnStyles: {
                        2: {
                            fillColor: function(cell) {
                                if (cell.text[0] === 'TINGGI') {
                                    return [231, 76, 60];
                                } else if (cell.text[0] === 'SEDANG') {
                                    return [243, 156, 18];
                                } else {
                                    return [46, 204, 113];
                                }
                            },
                            textColor: 255
                        }
                    }
                });

                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(10);
                    doc.text(`Halaman ${i} dari ${pageCount}`, doc.internal.pageSize.width - 20, doc.internal.pageSize.height - 10);
                    doc.text('Sistem Prediksi Risiko Gangguan Jaringan Listrik', 14, doc.internal.pageSize.height - 10);
                }
                doc.save('prediksi_risiko_pln.pdf');
            }
        });
    </script>
</body>

</html>