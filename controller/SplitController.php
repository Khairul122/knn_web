<?php

require_once 'model/SplitModel.php';

class SplitController {
    private $model;

    public function __construct() {
        $this->model = new SplitModel();
    }

    public function index() {
        try {
            $stats = $this->model->getSplitStatistics();
            $splitData = $this->model->getSplitData(100);
            
            $data = [
                'title' => 'Split Data 80:20',
                'stats' => $stats,
                'splitData' => $splitData,
                'success' => true
            ];
            
            include 'view/split/index.php';
            
        } catch (Exception $e) {
            $data = [
                'title' => 'Split Data 80:20',
                'error' => 'Gagal memuat data: ' . $e->getMessage(),
                'success' => false
            ];
            
            include 'view/split/index.php';
        }
    }

    public function split() {
        try {
            $result = $this->model->splitData80_20();
            
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                $_SESSION['split_info'] = [
                    'total_data' => $result['total_data'],
                    'train_data' => $result['train_data'],
                    'test_data' => $result['test_data'],
                    'train_percentage' => $result['train_percentage'],
                    'test_percentage' => $result['test_percentage']
                ];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Split data gagal: ' . $e->getMessage();
        }
        
        header('Location: index.php?page=split');
        exit();
    }

    public function reset() {
        try {
            $result = $this->model->resetAllSplitData();
            
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Reset data gagal: ' . $e->getMessage();
        }
        
        header('Location: index.php?page=split');
        exit();
    }

    public function getData() {
        try {
            $type = $_GET['type'] ?? 'all';
            
            switch ($type) {
                case 'train':
                    $data = $this->model->getTrainData();
                    break;
                case 'test':
                    $data = $this->model->getTestData();
                    break;
                default:
                    $data = $this->model->getSplitData();
                    break;
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }

    public function getStats() {
        try {
            $stats = $this->model->getSplitStatistics();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }

    public function exportTrainData() {
        try {
            $trainData = $this->model->getTrainData();
            
            if (empty($trainData)) {
                $_SESSION['error'] = 'Tidak ada data training untuk diekspor';
                header('Location: index.php?page=split');
                exit();
            }
            
            $filename = 'train_data_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            $headers = ['ID Split', 'ID Data Pemeliharaan', 'Nama Objek', 'Tipe Data', 'Tanggal', 'Nama Penyulang', 'Cluster Label', 'Risk Score', 'Tingkat Risiko'];
            fputcsv($output, $headers);
            
            foreach ($trainData as $row) {
                fputcsv($output, [
                    $row['id_split'],
                    $row['id_data_pemeliharaan'],
                    $row['nama_objek'],
                    $row['tipe_data'],
                    $row['tanggal'],
                    $row['nama_penyulang'],
                    $row['cluster_label'],
                    $row['risk_score'],
                    $row['tingkat_risiko']
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Export gagal: ' . $e->getMessage();
            header('Location: index.php?page=split');
            exit();
        }
    }

    public function exportTestData() {
        try {
            $testData = $this->model->getTestData();
            
            if (empty($testData)) {
                $_SESSION['error'] = 'Tidak ada data testing untuk diekspor';
                header('Location: index.php?page=split');
                exit();
            }
            
            $filename = 'test_data_' . date('Y-m-d_H-i-s') . '.csv';
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            $headers = ['ID Split', 'ID Data Pemeliharaan', 'Nama Objek', 'Tipe Data', 'Tanggal', 'Nama Penyulang', 'Cluster Label', 'Risk Score', 'Tingkat Risiko'];
            fputcsv($output, $headers);
            
            foreach ($testData as $row) {
                fputcsv($output, [
                    $row['id_split'],
                    $row['id_data_pemeliharaan'],
                    $row['nama_objek'],
                    $row['tipe_data'],
                    $row['tanggal'],
                    $row['nama_penyulang'],
                    $row['cluster_label'],
                    $row['risk_score'],
                    $row['tingkat_risiko']
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Export gagal: ' . $e->getMessage();
            header('Location: index.php?page=split');
            exit();
        }
    }
}