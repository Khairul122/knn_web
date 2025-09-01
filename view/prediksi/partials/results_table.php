<?php
include_once 'koneksi.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h5 class="card-title mb-2 mb-md-0 d-flex align-items-center">
                        <i class="mdi mdi-table text-primary me-2"></i>
                        <span>Hasil Prediksi & Saran Perbaikan</span>
                        <span class="badge bg-info ms-2"><?php echo count($data['prediksiData']); ?> data</span>
                    </h5>
                    <?php if (!empty($data['prediksiData'])): ?>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success btn-sm" onclick="exportToPDF()">
                                <i class="mdi mdi-file-pdf-box me-1"></i> Preview PDF
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($data['prediksiData'])): ?>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" class="form-control" id="searchTable"
                                    placeholder="Cari nama penyulang...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select form-select-sm" id="filterRisiko">
                                <option value="">Semua Tingkat Risiko</option>
                                <option value="TINGGI">Risiko Tinggi</option>
                                <option value="SEDANG">Risiko Sedang</option>
                                <option value="RENDAH">Risiko Rendah</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="table-prediksi">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3"><i class="mdi mdi-pound"></i> No</th>
                                <th><i class="mdi mdi-factory"></i> Nama Penyulang</th>
                                <th><i class="mdi mdi-alert"></i> Tingkat Risiko</th>
                                <th><i class="mdi mdi-chart-line"></i> Nilai Risiko</th>
                                <th><i class="mdi mdi-counter"></i> Total Kegiatan</th>
                                <th><i class="mdi mdi-settings"></i> K Value</th>
                                <th><i class="mdi mdi-lightbulb"></i> Saran Perbaikan</th>
                                <th class="pe-3"><i class="mdi mdi-calendar"></i> Tanggal Prediksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['prediksiData'])): ?>
                                <?php $no = 1; foreach ($data['prediksiData'] as $row): ?>
                                    <tr>
                                        <td class="ps-3 small text-center"><?php echo $no++; ?></td>
                                        <td>
                                            <strong class="text-dark"><?php echo htmlspecialchars($row['nama_penyulang']); ?></strong>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = '';
                                            $iconClass = '';
                                            switch (strtolower($row['tingkat_risiko'])) {
                                                case 'tinggi':
                                                    $badgeClass = 'bg-danger';
                                                    $iconClass = 'mdi-alert';
                                                    break;
                                                case 'sedang':
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $iconClass = 'mdi-alert-outline';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-success';
                                                    $iconClass = 'mdi-check-circle';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                                <i class="mdi <?php echo $iconClass; ?> me-1"></i>
                                                <?php echo htmlspecialchars($row['tingkat_risiko']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong class="text-dark"><?php echo number_format($row['nilai_risiko'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info px-2 py-1">
                                                <?php echo number_format($row['total_kegiatan']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary px-2 py-1">
                                                K=<?php echo $row['k_value']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <?php 
                                                $saranList = explode(';', $row['saran_perbaikan']);
                                                foreach ($saranList as $saran) {
                                                    $saran = trim($saran);
                                                    if (!empty($saran)) {
                                                        echo '<div class="mb-1">• ' . htmlspecialchars($saran) . '</div>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="pe-3">
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['tanggal_prediksi'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="mdi mdi-brain mdi-48px text-muted opacity-50"></i>
                                            <h5 class="text-muted mt-3 fw-semibold">Belum Ada Data Prediksi</h5>
                                            <p class="text-muted mb-3">
                                                Untuk memulai, silakan:
                                            </p>
                                            <ol class="text-muted text-start d-inline-block text-center text-md-start">
                                                <li class="mb-1">Lakukan split data terlebih dahulu</li>
                                                <li class="mb-1">Cari K optimal menggunakan Cross Validation</li>
                                                <li>Jalankan training KNN dengan K optimal</li>
                                            </ol>
                                            <div class="mt-3 d-flex flex-column flex-md-row gap-2 justify-content-center">
                                                <a href="?page=cluster" class="btn btn-outline-primary">
                                                    <i class="mdi mdi-database me-1"></i> Split Data
                                                </a>
                                                <button class="btn btn-info" onclick="document.getElementById('btn-find-k').click()">
                                                    <i class="mdi mdi-magnify me-1"></i> Cari K Optimal
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($data['prediksiData'])): ?>
                    <div class="mt-4 p-3 bg-light rounded small">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-2 d-flex align-items-center">
                                    <i class="mdi mdi-chart-pie text-muted me-2"></i>
                                    <span>Ringkasan Statistik:</span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <strong>Rata-rata Risk Score:</strong> <?php echo $data['statistics']['avg_risk_score']; ?>
                                        </div>
                                        <div>
                                            <strong>Rata-rata Total Kegiatan:</strong> <?php echo $data['statistics']['avg_total_kegiatan']; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-1">
                                            <strong>Distribusi Risiko:</strong>
                                        </div>
                                        <div>
                                            Tinggi: <?php echo $data['statistics']['tinggi_count']; ?> (<?php echo $data['statistics']['tinggi_percentage']; ?>%) |
                                            Sedang: <?php echo $data['statistics']['sedang_count']; ?> (<?php echo $data['statistics']['sedang_percentage']; ?>%) |
                                            Rendah: <?php echo $data['statistics']['rendah_count']; ?> (<?php echo $data['statistics']['rendah_percentage']; ?>%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="text-muted">
                                    <div class="mb-1">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        Last Updated: <?php echo date('d/m/Y H:i'); ?>
                                    </div>
                                    <div>
                                        <i class="mdi mdi-database me-1"></i>
                                        Total Records: <?php echo count($data['prediksiData']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
const statistikData = {
    avgRiskScore: <?php echo isset($data['statistics']['avg_risk_score']) ? $data['statistics']['avg_risk_score'] : 0; ?>,
    avgTotalKegiatan: <?php echo isset($data['statistics']['avg_total_kegiatan']) ? $data['statistics']['avg_total_kegiatan'] : 0; ?>,
    tinggiCount: <?php echo isset($data['statistics']['tinggi_count']) ? $data['statistics']['tinggi_count'] : 0; ?>,
    sedangCount: <?php echo isset($data['statistics']['sedang_count']) ? $data['statistics']['sedang_count'] : 0; ?>,
    rendahCount: <?php echo isset($data['statistics']['rendah_count']) ? $data['statistics']['rendah_count'] : 0; ?>,
    tinggiPercentage: <?php echo isset($data['statistics']['tinggi_percentage']) ? $data['statistics']['tinggi_percentage'] : 0; ?>,
    sedangPercentage: <?php echo isset($data['statistics']['sedang_percentage']) ? $data['statistics']['sedang_percentage'] : 0; ?>,
    rendahPercentage: <?php echo isset($data['statistics']['rendah_percentage']) ? $data['statistics']['rendah_percentage'] : 0; ?>
};

function refreshData() {
    location.reload();
}

document.getElementById('searchTable')?.addEventListener('input', function() {
    filterTable();
});

document.getElementById('filterRisiko')?.addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const searchInput = document.getElementById('searchTable');
    const filterSelect = document.getElementById('filterRisiko');
    
    if (!searchInput || !filterSelect) return;
    
    const searchValue = searchInput.value.toLowerCase();
    const filterValue = filterSelect.value;
    const table = document.getElementById('table-prediksi');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');
    
    let visibleCount = 0;
    
    for (let i = 0; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        if (cells.length === 1) continue;
        
        const penyulang = cells[1].textContent.toLowerCase();
        const risiko = cells[2].textContent;
        
        const matchSearch = penyulang.includes(searchValue);
        const matchFilter = !filterValue || risiko.includes(filterValue);
        
        if (matchSearch && matchFilter) {
            rows[i].style.display = '';
            visibleCount++;
        } else {
            rows[i].style.display = 'none';
        }
    }
    
    const countBadge = document.querySelector('.badge.bg-info');
    if (countBadge) {
        countBadge.textContent = `${visibleCount} data`;
    }
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a3');
    const pageWidth = 420;
    const pageHeight = 297;
    
    doc.setFontSize(20);
    doc.setFont('helvetica', 'bold');
    doc.text('LAPORAN HASIL PREDIKSI RISIKO KNN', pageWidth/2, 25, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    const tanggalCetak = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    const waktuCetak = new Date().toLocaleTimeString('id-ID');
    
    doc.text(`Tanggal Cetak: ${tanggalCetak}`, 20, 40);
    doc.text(`Waktu Cetak: ${waktuCetak}`, 20, 45);
    doc.text(`Format: Landscape Detail`, 20, 50);
    
    doc.line(20, 55, pageWidth - 20, 55);
    
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('RINGKASAN STATISTIK', 20, 68);
    
    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text(`Total Data Prediksi: ${statistikData.tinggiCount + statistikData.sedangCount + statistikData.rendahCount}`, 20, 78);
    doc.text(`Rata-rata Risk Score: ${statistikData.avgRiskScore}`, 20, 83);
    doc.text(`Rata-rata Total Kegiatan: ${statistikData.avgTotalKegiatan}`, 20, 88);
    
    doc.text(`Distribusi Risiko:`, 250, 78);
    doc.text(`• Tinggi: ${statistikData.tinggiCount} data (${statistikData.tinggiPercentage}%)`, 250, 83);
    doc.text(`• Sedang: ${statistikData.sedangCount} data (${statistikData.sedangPercentage}%)`, 250, 88);
    doc.text(`• Rendah: ${statistikData.rendahCount} data (${statistikData.rendahPercentage}%)`, 250, 93);
    
    doc.line(20, 98, pageWidth - 20, 98);
    
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('DATA PREDIKSI DETAIL', 20, 111);
    
    const table = document.getElementById('table-prediksi');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');
    
    const tableData = [];
    const visibleRows = Array.from(rows).filter(row => 
        row.style.display !== 'none' && 
        row.getElementsByTagName('td').length > 1
    );
    
    let nomor = 1;
    visibleRows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        if (cells.length > 1) {
            const tingkatRisiko = cells[2].textContent.replace(/\s+/g, ' ').trim();
            const button = cells[6].querySelector('button');
            let saran = '';
            if (button) {
                saran = button.getAttribute('data-saran') || '';
            }
            
            tableData.push([
                nomor++,
                cells[1].textContent.trim(),
                tingkatRisiko,
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                saran,
                cells[7].textContent.trim()
            ]);
        }
    });
    
    doc.autoTable({
        startY: 120,
        head: [['No', 'Nama Penyulang', 'Tingkat Risiko', 'Nilai Risiko', 'Total Kegiatan', 'K Value', 'Saran Perbaikan', 'Tanggal Prediksi']],
        body: tableData,
        styles: {
            fontSize: 9,
            cellPadding: 4,
            overflow: 'linebreak',
            cellWidth: 'wrap',
            valign: 'middle',
            lineColor: [206, 212, 218],
            lineWidth: 0.2
        },
        headStyles: {
            fillColor: [41, 128, 185],
            textColor: 255,
            fontStyle: 'bold',
            fontSize: 9,
            halign: 'center'
        },
        columnStyles: {
            0: { 
                cellWidth: 20, 
                halign: 'center',
                fontSize: 9
            },
            1: { 
                cellWidth: 60,
                fontStyle: 'bold',
                fontSize: 9
            },
            2: { 
                cellWidth: 35,
                halign: 'center',
                fontSize: 9
            },
            3: { 
                cellWidth: 30,
                halign: 'center',
                fontSize: 9
            },
            4: { 
                cellWidth: 35,
                halign: 'center',
                fontSize: 9
            },
            5: { 
                cellWidth: 25,
                halign: 'center',
                fontSize: 9
            },
            6: { 
                cellWidth: 140,
                overflow: 'linebreak',
                cellPadding: 5,
                fontSize: 8,
                valign: 'top'
            },
            7: { 
                cellWidth: 35,
                halign: 'center',
                fontSize: 8
            }
        },
        alternateRowStyles: {
            fillColor: [248, 249, 250]
        },
        tableLineColor: [206, 212, 218],
        tableLineWidth: 0.3,
        margin: { left: 20, right: 20 },
        didParseCell: function(data) {
            if (data.column.index === 2) {
                if (data.cell.text[0] && data.cell.text[0].includes('TINGGI')) {
                    data.cell.styles.fillColor = [248, 215, 218];
                    data.cell.styles.textColor = [114, 28, 36];
                } else if (data.cell.text[0] && data.cell.text[0].includes('SEDANG')) {
                    data.cell.styles.fillColor = [255, 243, 205];
                    data.cell.styles.textColor = [133, 77, 14];
                } else if (data.cell.text[0] && data.cell.text[0].includes('RENDAH')) {
                    data.cell.styles.fillColor = [212, 237, 218];
                    data.cell.styles.textColor = [21, 87, 36];
                }
            }
        }
    });
    
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text(`Halaman ${i} dari ${pageCount}`, pageWidth - 30, pageHeight - 10, { align: 'right' });
        doc.text('Generated by Sistem Prediksi Risiko KNN', 20, pageHeight - 10);
        doc.text(`Dicetak pada: ${new Date().toLocaleString('id-ID')}`, 20, pageHeight - 5);
    }
    
    window.open(doc.output('bloburl'), '_blank');
}
</script>