<?php

require_once 'model/PrediksiModel.php';

class PrediksiController {
    private $model;

    public function __construct() {
        $this->model = new PrediksiModel();
    }

    public function index() {
        $lastPrediction = $this->model->getLastPrediction();
        $hasPrediction = ($lastPrediction !== null);
        
        $predictionResults = [];
        $predictionSummary = [];
        
        if ($hasPrediction) {
            $predictionResults = $this->model->getPredictionResults();
            $predictionSummary = $this->model->getPredictionSummary();
        }
        
        include 'view/prediksi/index.php';
    }
    
    public function process() {
        $kValue = isset($_POST['k_value']) ? intval($_POST['k_value']) : 3;
        
        if ($kValue < 1) {
            $kValue = 1;
        } elseif ($kValue > 20) {
            $kValue = 20;
        }
        
        $predictionResults = $this->model->runKnnPrediction($kValue);
        
        $predictionSummary = $this->model->getPredictionSummary();
        
        $hasPrediction = true;
        
        $lastPrediction = $this->model->getLastPrediction();
        
        include 'view/prediksi/index.php';
    }
    
    public function filterByRisk() {
        $tingkatRisiko = isset($_GET['tingkat']) ? $_GET['tingkat'] : null;
        
        $predictionResults = $this->model->getPredictionResults($tingkatRisiko);
        
        $predictionSummary = $this->model->getPredictionSummary();
        
        $isFiltered = true;
        $filterTingkat = $tingkatRisiko;
        
        include 'view/prediksi/index.php';
    }
}