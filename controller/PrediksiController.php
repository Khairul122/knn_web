<?php

require_once 'model/PrediksiModel.php';

class PrediksiController {
    private $model;

    public function __construct() {
        $this->model = new PemeliharaanModel();
    }

    public function index() {
        include 'view/prediksi/index.php';
    }
}