<?php

require_once 'model/PemeliharaanModel.php';

class PemeliharaanController {
    private $model;

    public function __construct() {
        $this->model = new PemeliharaanModel();
    }

    public function index() {
        $pemeliharaan = $this->model->getAllPemeliharaan();
        include 'view/pemeliharaan/index.php';
    }

    public function filter() {
        $bulan = $_POST['bulan'] ?? '';
        $tahun = $_POST['tahun'] ?? '';
        $objek = $_POST['objek'] ?? '';
        
        if ($bulan && $tahun) {
            $pemeliharaan = $this->model->getPemeliharaanByPeriod($bulan, $tahun);
        } else if ($objek) {
            $pemeliharaan = $this->model->getPemeliharaanByObject($objek);
        } else {
            $pemeliharaan = $this->model->getAllPemeliharaan();
        }
        
        include 'view/pemeliharaan/index.php';
    }
}
?>