<?php
require_once 'model/PrediksiModel.php';

class PrediksiController {
    private $model;

    public function __construct() {
        $this->model = new PrediksiModel();
    }

    public function index() {
        $message = '';
        $messageType = '';
        $optimalKResult = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'find_optimal_k':
                    $result = $this->findOptimalKDetailed();
                    if ($result['success']) {
                        $optimalKResult = $result['data'];
                        $message = $result['message'];
                        $messageType = 'success';
                        $_SESSION['optimal_k'] = $result['data']['optimal_k'];
                    } else {
                        $message = $result['message'];
                        $messageType = 'error';
                    }
                    break;

                case 'train_knn':
                    $result = $this->trainKNN();
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                    break;

                case 'reset_data':
                    $result = $this->resetData();
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'error';
                    break;
            }
        }

        $data = $this->loadData();
        $data['message'] = $message;
        $data['messageType'] = $messageType;
        $data['optimalKResult'] = $optimalKResult;

        include 'view/prediksi/index.php';
    }

    private function loadData() {
        try {
            return [
                'statistics' => $this->model->getPrediksiStatistics(),
                'prediksiData' => $this->model->getPrediksiData(),
                'confusionMatrix' => $this->model->getConfusionMatrix(),
                'riskDistribution' => $this->model->getRiskDistributionData(),
                'accuracyTrend' => $this->model->getAccuracyTrendData(),
                'topRiskPenyulang' => $this->model->getTopRiskPenyulang(5),
                'overfittingCheck' => ['overfitting' => false],
                'modelHealth' => ['health_score' => 85, 'status' => 'GOOD', 'issues' => []]
            ];
        } catch (Exception $e) {
            return [
                'statistics' => [
                    'total_prediksi' => 0, 'tinggi_count' => 0, 'sedang_count' => 0, 'rendah_count' => 0,
                    'tinggi_percentage' => 0, 'sedang_percentage' => 0, 'rendah_percentage' => 0,
                    'avg_risk_score' => 0, 'avg_total_kegiatan' => 0, 'last_k_value' => 3
                ],
                'prediksiData' => [], 'confusionMatrix' => ['matrix' => [], 'accuracy' => 0],
                'riskDistribution' => [], 'accuracyTrend' => [], 'topRiskPenyulang' => [],
                'overfittingCheck' => ['overfitting' => false],
                'modelHealth' => ['health_score' => 0, 'status' => 'ERROR', 'issues' => []]
            ];
        }
    }

    private function findOptimalKDetailed() {
        try {
            $maxK = isset($_POST['max_k']) ? (int)$_POST['max_k'] : 15;
            $cvFolds = isset($_POST['cv_folds']) ? (int)$_POST['cv_folds'] : 5;
            
            if ($maxK < 3 || $maxK > 25) {
                throw new Exception("Max K harus antara 3-25");
            }
            
            if ($cvFolds < 3 || $cvFolds > 10) {
                throw new Exception("CV Folds harus antara 3-10");
            }
            
            $result = $this->model->findOptimalKWithDetailedCV($maxK, $cvFolds);
            return [
                'success' => $result['success'],
                'data' => $result,
                'message' => $result['success'] ? 
                    "K optimal ditemukan: K={$result['optimal_k']} dengan accuracy {$result['best_accuracy']}% (CV {$cvFolds}-fold)" :
                    $result['message']
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function findOptimalK() {
        return $this->findOptimalKDetailed();
    }

    private function trainKNN() {
        try {
            $k_value = isset($_POST['k_value']) ? (int)$_POST['k_value'] : 5;
            if ($k_value < 1 || $k_value > 25) {
                throw new Exception("Nilai K harus antara 1-25");
            }
            
            $result = $this->model->trainKNN($k_value);
            if ($result['success']) {
                $_SESSION['train_info'] = [
                    'total_penyulang' => $result['total_penyulang'],
                    'k_value' => $result['k_value'],
                    'training_data_count' => $result['training_data_count']
                ];
                $_SESSION['last_k_value'] = $k_value;
            }
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    private function resetData() {
        try {
            $result = $this->model->resetAllPrediksiData();
            if ($result['success']) {
                unset($_SESSION['train_info']);
                unset($_SESSION['optimal_k']);
                unset($_SESSION['last_k_value']);
            }
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}