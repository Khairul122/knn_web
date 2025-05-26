<?php
require_once 'model/ClusteringModel.php';

class ClusteringController {
    private $model;
    
    public function __construct() {
        try {
            $this->model = new ClusteringModel();
        } catch (Exception $e) {
            $_SESSION['clustering_message'] = 'Error koneksi database: ' . $e->getMessage();
            $_SESSION['clustering_success'] = false;
        }
    }
    
    public function index() {
        try {
            $clusteringResults = $this->model->getClusteringResults();
            $splitDataResults = $this->model->getSplitDataResults();
            $clusterStats = $this->model->getClusterStats();
            $splitStats = $this->model->getSplitDataStats();
            
            include 'view/clustering/index.php';
        } catch (Exception $e) {
            $clusteringResults = [];
            $splitDataResults = [];
            $clusterStats = [];
            $splitStats = [];
            $_SESSION['clustering_message'] = 'Error mengambil data: ' . $e->getMessage();
            $_SESSION['clustering_success'] = false;
            include 'view/clustering/index.php';
        }
    }
    
    public function performClustering($data = null) {
        try {
            if (!$this->model) {
                throw new Exception("Model tidak tersedia");
            }
            
            $k = isset($_POST['k']) ? intval($_POST['k']) : 3;
            $maxIterations = isset($_POST['max_iterations']) ? intval($_POST['max_iterations']) : 100;
            
            if ($k < 2 || $k > 10) {
                throw new Exception("Jumlah cluster harus antara 2-10");
            }
            
            if (!$this->model->testConnection()) {
                throw new Exception("Koneksi database bermasalah");
            }
            
            $maintenanceData = $this->model->getMaintenanceDataWithFeatures();
            
            if (empty($maintenanceData)) {
                throw new Exception("Tidak ada data untuk clustering");
            }
            
            $clusteredData = $this->model->performKMeansClustering($maintenanceData, $k, $maxIterations);
            
            if (empty($clusteredData)) {
                throw new Exception("Proses clustering gagal");
            }
            
            $savedCount = $this->model->saveClusteringResults($clusteredData);
            
            if ($savedCount == 0) {
                throw new Exception("Gagal menyimpan data cluster ke database");
            }
            
            $_SESSION['clustering_message'] = "Clustering berhasil! {$savedCount} data disimpan dengan {$k} cluster.";
            $_SESSION['clustering_success'] = true;
            header('Location: index.php?page=clustering');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['clustering_message'] = 'Clustering gagal: ' . $e->getMessage();
            $_SESSION['clustering_success'] = false;
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function splitData($data = null) {
        try {
            if (!$this->model) {
                throw new Exception("Model tidak tersedia");
            }
            
            $testRatio = isset($_POST['test_ratio']) ? floatval($_POST['test_ratio']) : 0.2;
            
            if ($testRatio < 0.1 || $testRatio > 0.5) {
                throw new Exception("Rasio test harus antara 10%-50%");
            }
            
            $result = $this->model->splitData($testRatio);
            
            if (!$result['success']) {
                throw new Exception($result['message']);
            }
            
            $_SESSION['split_message'] = "Split data berhasil! Total: {$result['total_data']}, Training: {$result['train_count']}, Testing: {$result['test_count']}";
            $_SESSION['split_success'] = true;
            header('Location: index.php?page=clustering');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['split_message'] = 'Split data gagal: ' . $e->getMessage();
            $_SESSION['split_success'] = false;
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function viewResults() {
        try {
            $clusteringResults = $this->model->getClusteringResults();
            $clusterStats = $this->model->getClusterStats();
            
            include 'view/clustering/results.php';
        } catch (Exception $e) {
            $_SESSION['clustering_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['clustering_success'] = false;
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function viewSplitData() {
        try {
            $splitDataResults = $this->model->getSplitDataResults();
            $splitStats = $this->model->getSplitDataStats();
            
            include 'view/clustering/split_data.php';
        } catch (Exception $e) {
            $_SESSION['split_message'] = 'Error: ' . $e->getMessage();
            $_SESSION['split_success'] = false;
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function exportClusteringResults() {
        try {
            $clusteringResults = $this->model->getClusteringResults();
            
            if (empty($clusteringResults)) {
                throw new Exception("Tidak ada hasil clustering untuk export");
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=clustering_results_' . date('Y-m-d_H-i-s') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'ID Cluster',
                'ID Data Pemeliharaan', 
                'Tanggal',
                'Nama Objek',
                'Nama Penyulang',
                'Cluster Label'
            ]);
            
            foreach ($clusteringResults as $row) {
                fputcsv($output, [
                    $row['id_cluster'],
                    $row['id_data_pemeliharaan'],
                    $row['tanggal'],
                    $row['nama_objek'],
                    $row['nama_penyulang'],
                    'Cluster ' . $row['cluster_label']
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['export_message'] = 'Export gagal: ' . $e->getMessage();
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function exportSplitData() {
        try {
            $splitDataResults = $this->model->getSplitDataResults();
            
            if (empty($splitDataResults)) {
                throw new Exception("Tidak ada hasil split data untuk export");
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=split_data_results_' . date('Y-m-d_H-i-s') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            fputcsv($output, [
                'ID Split',
                'ID Data Pemeliharaan',
                'Tanggal', 
                'Nama Objek',
                'Nama Penyulang',
                'Tipe Data'
            ]);
            
            foreach ($splitDataResults as $row) {
                fputcsv($output, [
                    $row['id_split'],
                    $row['id_data_pemeliharaan'],
                    $row['tanggal'],
                    $row['nama_objek'],
                    $row['nama_penyulang'],
                    ucfirst($row['tipe_data'])
                ]);
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            $_SESSION['export_message'] = 'Export gagal: ' . $e->getMessage();
            header('Location: index.php?page=clustering');
            exit;
        }
    }
    
    public function getStats() {
        try {
            $clusterStats = $this->model->getClusterStats();
            $splitStats = $this->model->getSplitDataStats();
            
            header('Content-Type: application/json');
            echo json_encode([
                'cluster_stats' => $clusterStats,
                'split_stats' => $splitStats
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>