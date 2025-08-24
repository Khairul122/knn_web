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

    public function validate() {
        try {
            $performCV = isset($_POST['perform_cv']) && $_POST['perform_cv'] == 'on';
            $cvFolds = isset($_POST['cv_folds']) ? (int)$_POST['cv_folds'] : 5;
            
            if ($cvFolds < 3 || $cvFolds > 10) {
                throw new Exception("Jumlah fold harus antara 3-10");
            }
            
            $result = $this->model->validateTrainingData($performCV, $cvFolds);
            
            if (isset($result['error'])) {
                $_SESSION['error'] = 'Validasi gagal: ' . $result['error'];
            } else {
                $_SESSION['success'] = 'Validasi data berhasil dilakukan';
                $_SESSION['validation_result'] = $result;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error validasi: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    public function split() {
        try {
            $validationResult = $this->model->validateTrainingData(false);
            
            if (!$validationResult['data_quality']['is_valid']) {
                $_SESSION['error'] = 'Data tidak valid untuk split: ' . implode(', ', $validationResult['data_quality']['errors']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            }
            
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
                
                if (isset($result['validation_info']['warnings']) && !empty($result['validation_info']['warnings'])) {
                    $_SESSION['validation_warnings'] = $result['validation_info']['warnings'];
                }
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Split data gagal: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    public function reset() {
        try {
            $result = $this->model->resetAllSplitData();
            
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                unset($_SESSION['split_info']);
                unset($_SESSION['validation_result']);
                unset($_SESSION['validation_warnings']);
            } else {
                $_SESSION['error'] = $result['message'];
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Reset data gagal: ' . $e->getMessage();
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    public function getData() {
        try {
            $type = $_GET['type'] ?? 'all';
            
            switch ($type) {
                case 'train':
                    $data = $this->model->getSplitData();
                    $data = array_filter($data, function($row) {
                        return $row['tipe_data'] == 'train';
                    });
                    break;
                case 'test':
                    $data = $this->model->getSplitData();
                    $data = array_filter($data, function($row) {
                        return $row['tipe_data'] == 'test';
                    });
                    break;
                default:
                    $data = $this->model->getSplitData();
                    break;
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => array_values($data),
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
}
?>