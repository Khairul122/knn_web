<?php
require_once 'model/PrediksiModel.php';

class PrediksiController {
    private $model;

    public function __construct() {
        $this->model = new PrediksiModel();
    }

    public function index() {
        try {
            $dataStatus = $this->model->getDataStatus();
            $lastPrediction = $this->model->getLastPrediction();
            $hasPrediction = ($lastPrediction !== null);
            
            $predictionResults = [];
            $predictionSummary = [];
            
            if ($hasPrediction) {
                $predictionResults = $this->model->getPredictionResults();
                $predictionSummary = $this->model->getPredictionSummary();
            }
            
            include 'view/prediksi/index.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
            $dataStatus = ['ready_for_prediction' => false];
            $hasPrediction = false;
            $predictionResults = [];
            $predictionSummary = [];
            $lastPrediction = null;
            include 'view/prediksi/index.php';
        }
    }
    
    public function process() {
        try {
            $dataStatus = $this->model->getDataStatus();
            
            if (!$dataStatus['ready_for_prediction']) {
                throw new Exception("Data belum siap untuk prediksi. Lakukan clustering dan split data terlebih dahulu.");
            }
            
            $kValue = isset($_POST['k_value']) ? intval($_POST['k_value']) : 3;
            
            if ($kValue < 1) {
                $kValue = 1;
            } elseif ($kValue > 20) {
                $kValue = 20;
            }
            
            $result = $this->model->runKnnPrediction($kValue);
            
            $_SESSION['success_message'] = "Prediksi KNN berhasil! {$result['total_predictions']} data diproses dengan K={$kValue}. Training: {$result['training_count']}, Testing: {$result['test_count']}";
            
            header('Location: index.php?page=prediksi');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Prediksi gagal: ' . $e->getMessage();
            header('Location: index.php?page=prediksi');
            exit;
        }
    }
    
    public function filterByRisk() {
        try {
            $tingkatRisiko = isset($_GET['tingkat']) ? $_GET['tingkat'] : null;
            
            $predictionResults = $this->model->getPredictionResults($tingkatRisiko);
            $predictionSummary = $this->model->getPredictionSummary();
            $dataStatus = $this->model->getDataStatus();
            $lastPrediction = $this->model->getLastPrediction();
            $hasPrediction = ($lastPrediction !== null);
            
            $isFiltered = true;
            $filterTingkat = $tingkatRisiko;
            
            include 'view/prediksi/index.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?page=prediksi');
            exit;
        }
    }
    
    public function viewLog() {
        try {
            $predictionResults = $this->model->getPredictionResults();
            $predictionSummary = $this->model->getPredictionSummary();
            $lastPrediction = $this->model->getLastPrediction();
            $dataStatus = $this->model->getDataStatus();
            
            include 'view/prediksi/log.php';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?page=prediksi');
            exit;
        }
    }
    
    public function exportLog() {
        try {
            $predictionResults = $this->model->getPredictionResults();
            
            if (empty($predictionResults)) {
                throw new Exception("Tidak ada hasil prediksi untuk export");
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=prediksi_risiko_' . date('Y-m-d_H-i-s') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'ID Prediksi',
                'Nama Penyulang',
                'Tingkat Risiko',
                'Nilai Risiko',
                'Total Kegiatan',
                'K Value',
                'Tanggal Prediksi'
            ]);
            
            foreach ($predictionResults as $row) {
                fputcsv($output, [
                    $row['id_prediksi'],
                    $row['nama_penyulang'],
                    $row['tingkat_risiko'],
                    round($row['nilai_risiko'], 2),
                    $row['total_kegiatan'],
                    $row['k_value'],
                    $row['tanggal_prediksi']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Export gagal: ' . $e->getMessage();
            header('Location: index.php?page=prediksi');
            exit;
        }
    }
    
    public function clearLog() {
        try {
            $query = "DELETE FROM hasil_prediksi_risiko";
            global $koneksi;
            $result = mysqli_query($koneksi, $query);
            
            if ($result) {
                $_SESSION['success_message'] = 'Log prediksi berhasil dihapus';
            } else {
                throw new Exception('Gagal menghapus log prediksi');
            }
            
            header('Location: index.php?page=prediksi');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Error: ' . $e->getMessage();
            header('Location: index.php?page=prediksi');
            exit;
        }
    }
}
?>