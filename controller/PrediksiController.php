<?php
require_once 'model/PrediksiModel.php';

class PrediksiController {
    private $model;

    public function __construct() {
        $this->model = new PrediksiModel();
    }

    public function index() {
        try {
            $data = [
                'title' => 'Prediksi Risiko KNN',
                'status' => $this->model->getDataStatus(),
                'stats' => $this->model->getPrediksiStatistics(),
                'success' => true
            ];
            
            include 'view/prediksi/index.php';
            
        } catch (Exception $e) {
            $data = [
                'title' => 'Prediksi Risiko KNN',
                'error' => 'Gagal memuat data: ' . $e->getMessage(),
                'success' => false
            ];
            
            include 'view/prediksi/index.php';
        }
    }
}