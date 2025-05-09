<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$page = $_GET['page'] ?? 'home';

// Halaman yang bisa diakses tanpa login
$public_pages = ['login'];

// Cek autentikasi
if (!isset($_SESSION['login']) && !in_array($page, $public_pages)) {
    header("Location: index.php?page=login");
    exit;
}

// Daftar halaman yang ditangani langsung
$direct_pages = [
    'login' => 'view/login.php',
    'home' => 'view/home.php',
    'import-form-gardu' => 'view/gardu/import-data.php',
    'import-form-sutm' => 'view/sutm/import-data.php'
];

// Jika halaman ada di daftar direct pages
if (isset($direct_pages[$page])) {
    include $direct_pages[$page];
    exit;
}

// Jika halaman adalah logout
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// Mapping halaman ke controller dan metode
$page_mapping = [
    // Gardu
    'data-gardu' => ['controller' => 'GarduController', 'method' => 'index'],
    'import-gardu-save' => ['controller' => 'GarduController', 'method' => 'importGarduSave'],
    'tambah-data-gardu' => ['controller' => 'GarduController', 'method' => 'tambahData'],
    'tambah-gardu' => ['controller' => 'GarduController', 'method' => 'tambahData'],
    'simpan-manual-gardu' => ['controller' => 'GarduController', 'method' => 'simpanManual'],
    'hapus-gardu' => ['controller' => 'GarduController', 'method' => 'delete'],
    'edit-gardu' => ['controller' => 'GarduController', 'method' => 'edit'],
    'update-gardu' => ['controller' => 'GarduController', 'method' => 'edit'],
    
    // SUTM
    'data-sutm' => ['controller' => 'SutmController', 'method' => 'index'],
    'tambah-data-sutm' => ['controller' => 'SutmController', 'method' => 'tambahData'],
    'tambah-sutm' => ['controller' => 'SutmController', 'method' => 'tambahData'],
    'import-excel-sutm' => ['controller' => 'SutmController', 'method' => 'importExcel'],
    'import-sutm-save' => ['controller' => 'SutmController', 'method' => 'importSutmSave'],
    'simpan-manual-sutm' => ['controller' => 'SutmController', 'method' => 'simpanManual'],
    'hapus-sutm' => ['controller' => 'SutmController', 'method' => 'delete'],
    'edit-sutm' => ['controller' => 'SutmController', 'method' => 'edit'],
    'update-sutm' => ['controller' => 'SutmController', 'method' => 'edit'],
    'preview-import-sutm' => ['controller' => 'SutmController', 'method' => 'previewImport'],

    // Data Pemeliharaan
    'data-pemeliharaan' => ['controller' => 'PemeliharaanController', 'method' => 'index'],

    // Prediksi
    'prediksi' => ['controller' => 'PrediksiController', 'method' => 'index'],
    'prediksi-process' => ['controller' => 'PrediksiController', 'method' => 'process'],
    'prediksi-filter' => ['controller' => 'PrediksiController', 'method' => 'filterByRisk'],
    'prediksi-log' => ['controller' => 'PrediksiController', 'method' => 'viewLog'],
    'prediksi-export-log' => ['controller' => 'PrediksiController', 'method' => 'exportLog'],
    'prediksi-clear-log' => ['controller' => 'PrediksiController', 'method' => 'clearLog'],
];

// Jika halaman ada di mapping
if (isset($page_mapping[$page])) {
    $controller_name = $page_mapping[$page]['controller'];
    $method_name = $page_mapping[$page]['method'];
    $controller_path = "controller/{$controller_name}.php";
    
    if (file_exists($controller_path)) {
        include_once $controller_path;
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            if (method_exists($controller, $method_name)) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $method_name === 'importExcel' && isset($_FILES['fileExcel'])) {
                    // Handle khusus untuk upload file
                    $preview = isset($_POST['preview']) ? $_POST['preview'] : 0;
                    $bulan = $_POST['bulan'] ?? '';
                    $tahun = $_POST['tahun'] ?? '';
                    $controller->$method_name($_FILES['fileExcel'], $preview, $bulan, $tahun);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->$method_name($_POST);
                } else {
                    $controller->$method_name();
                }
                exit;
            }
        }
    }
}

// Jika tidak ada di mapping yang eksplisit, coba parsing otomatis
$parts = explode('-', $page);
if (count($parts) > 0) {
    $controller_name = ucfirst($parts[0]) . 'Controller';
    $method_name = isset($parts[1]) ? implode('', array_slice($parts, 1)) : 'index';
    $controller_path = "controller/{$controller_name}.php";
    
    if (file_exists($controller_path)) {
        include_once $controller_path;
        if (class_exists($controller_name)) {
            $controller = new $controller_name();
            if (method_exists($controller, $method_name)) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->$method_name($_POST);
                } else {
                    $controller->$method_name();
                }
                exit;
            }
        }
    }
}

// Jika sampai di sini, halaman tidak ditemukan
include 'view/404.php';