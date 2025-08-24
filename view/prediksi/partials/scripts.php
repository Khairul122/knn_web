<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

<script>
let riskDistributionChart;
let accuracyTrendChart;

function initCharts() {
    <?php if (!empty($data['riskDistribution'])): ?>
        const riskData = <?php echo json_encode($data['riskDistribution']); ?>;
        const riskLabels = riskData.map(item => item.label);
        const riskValues = riskData.map(item => item.value);
        const riskColors = riskLabels.map(label => {
            switch (label) {
                case 'TINGGI': return '#dc3545';
                case 'SEDANG': return '#ffc107';
                case 'RENDAH': return '#198754';
                default: return '#6c757d';
            }
        });

        const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
        if (riskDistributionChart) riskDistributionChart.destroy();
        
        riskDistributionChart = new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: riskLabels,
                datasets: [{
                    data: riskValues,
                    backgroundColor: riskColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    <?php endif; ?>

    <?php if (!empty($data['accuracyTrend'])): ?>
        const trendData = <?php echo json_encode($data['accuracyTrend']); ?>;
        const trendLabels = trendData.map(item => 'K=' + item.k_value);
        const trendValues = trendData.map(item => item.accuracy);

        const trendCtx = document.getElementById('accuracyTrendChart').getContext('2d');
        if (accuracyTrendChart) accuracyTrendChart.destroy();
        
        accuracyTrendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Accuracy (%)',
                    data: trendValues,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    <?php endif; ?>
}

function showSuggestion(penyulang, suggestions, riskLevel) {
    document.getElementById('modalPenyulang').textContent = penyulang;

    const badge = document.getElementById('modalRisikoBadge');
    badge.textContent = riskLevel;
    badge.className = 'badge ';

    switch (riskLevel.toUpperCase()) {
        case 'TINGGI':
            badge.className += 'bg-danger';
            break;
        case 'SEDANG':
            badge.className += 'bg-warning text-dark';
            break;
        case 'RENDAH':
            badge.className += 'bg-success';
            break;
    }

    if (!suggestions || suggestions.trim() === '') {
        document.getElementById('modalSuggestions').innerHTML = 
            '<div class="alert alert-warning py-2 mb-0"><i class="mdi mdi-alert me-2"></i>Tidak ada saran perbaikan tersedia.</div>';
    } else {
        const suggestionArray = suggestions.split(';').filter(s => s.trim() !== '');
        let suggestionHtml = '<div class="list-group list-group-flush">';
        suggestionArray.forEach((suggestion, index) => {
            suggestionHtml += `
                <div class="list-group-item border-0 px-0 py-2">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-primary me-2 mt-1">${index + 1}</span>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-dark">${suggestion.trim()}</p>
                        </div>
                    </div>
                </div>`;
        });
        suggestionHtml += '</div>';
        document.getElementById('modalSuggestions').innerHTML = suggestionHtml;
    }

    const suggestionModal = new bootstrap.Modal(document.getElementById('suggestionModal'));
    suggestionModal.show();
}

function printSuggestion() {
    const penyulang = document.getElementById('modalPenyulang').textContent;
    const riskLevel = document.getElementById('modalRisikoBadge').textContent;
    const suggestions = document.getElementById('modalSuggestions').innerHTML;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Saran Perbaikan - ${penyulang}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 25px; line-height: 1.6; }
                    .header { border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
                    .badge { padding: 5px 10px; border-radius: 5px; color: white; font-size: 12px; }
                    .bg-danger { background-color: #dc3545; }
                    .bg-warning { background-color: #ffc107; color: black; }
                    .bg-success { background-color: #198754; }
                    .list-group-item { padding: 8px 0; border: none; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>SARAN PERBAIKAN SISTEM PREDIKSI RISIKO KNN</h2>
                    <p><strong>Penyulang:</strong> ${penyulang}</p>
                    <p><strong>Tingkat Risiko:</strong> <span class="badge bg-${riskLevel.toLowerCase() === 'tinggi' ? 'danger' : riskLevel.toLowerCase() === 'sedang' ? 'warning' : 'success'}">${riskLevel}</span></p>
                    <p><strong>Tanggal:</strong> ${new Date().toLocaleDateString('id-ID')}</p>
                </div>
                <div>
                    <h3>Rekomendasi Tindakan:</h3>
                    ${suggestions}
                </div>
                <div style="margin-top: 40px;">
                    <p><em>Generated by Sistem Prediksi Risiko KNN with Cross Validation</em></p>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function refreshData() {
    location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    initCharts();

    const searchInput = document.getElementById('searchTable');
    const filterSelect = document.getElementById('filterRisiko');
    const tableBody = document.querySelector('#table-prediksi tbody');
    
    if (tableBody) {
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        
        function filterTable() {
            const searchText = searchInput ? searchInput.value.toLowerCase() : '';
            const filterValue = filterSelect ? filterSelect.value : '';

            rows.forEach(row => {
                if (row.cells.length <= 1) return;

                const penyulangName = row.cells[1].textContent.toLowerCase();
                const risikoLevel = row.cells[2].textContent.trim();

                const matchesSearch = penyulangName.includes(searchText);
                const matchesFilter = !filterValue || risikoLevel.includes(filterValue);

                row.style.display = matchesSearch && matchesFilter ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', filterTable);
        if (filterSelect) filterSelect.addEventListener('change', filterTable);
    }

    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });

    const formOptimalK = document.getElementById('form-optimal-k');
    if (formOptimalK) {
        formOptimalK.addEventListener('submit', function(e) {
            const button = document.getElementById('btn-find-k');
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';
            button.disabled = true;
        });
    }

    function handleResponsiveTable() {
        const table = document.getElementById('table-prediksi');
        if (table && window.innerWidth < 768) {
            table.classList.add('table-sm');
        } else if (table) {
            table.classList.remove('table-sm');
        }
    }

    handleResponsiveTable();
    window.addEventListener('resize', handleResponsiveTable);
});

const style = document.createElement('style');
style.textContent = `
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
    }
    .table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }
    .badge {
        font-size: 0.75rem;
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
`;
document.head.appendChild(style);
</script>