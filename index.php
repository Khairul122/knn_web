<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$page = $_GET['page'] ?? 'home';
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

$public_pages = ['login'];

if (!isset($_SESSION['login']) && !in_array($page, $public_pages)) {
    header("Location: index.php?page=login");
    exit;
}

$direct_pages = [
    'login' => 'view/login.php',
    'home' => 'view/home.php',
    'import-form-gardu' => 'view/gardu/import-data.php',
    'import-form-sutm' => 'view/sutm/import-data.php'
];

if (isset($direct_pages[$page])) {
    include $direct_pages[$page];
    exit;
}

if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

if ($controller && $action) {
    $controller_mapping = [
        'clustering' => 'ClusteringController',
        'prediksi' => 'PrediksiController',
        'pemeliharaan' => 'PemeliharaanController',
        'gardu' => 'GarduController',
        'sutm' => 'SutmController',
        'cluster' => 'ClusterController'
    ];
    
    if (isset($controller_mapping[$controller])) {
        $controller_name = $controller_mapping[$controller];
        $controller_path = "controller/{$controller_name}.php";
        
        if (file_exists($controller_path)) {
            include_once $controller_path;
            if (class_exists($controller_name)) {
                $controller_instance = new $controller_name();
                if (method_exists($controller_instance, $action)) {
                    try {
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $controller_instance->$action($_POST);
                        } else {
                            $controller_instance->$action();
                        }
                    } catch (Exception $e) {
                        error_log("Controller action error: " . $e->getMessage());
                        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                    }
                    exit;
                } else {
                    error_log("Method {$action} not found in {$controller_name}");
                    header("HTTP/1.0 404 Not Found");
                    include 'view/404.php';
                    exit;
                }
            }
        } else {
            error_log("Controller file not found: {$controller_path}");
            header("HTTP/1.0 404 Not Found");
            include 'view/404.php';
            exit;
        }
    }
}

$page_mapping = [
    'data-gardu' => ['controller' => 'GarduController', 'method' => 'index'],
    'import-gardu-save' => ['controller' => 'GarduController', 'method' => 'importGarduSave'],
    'tambah-data-gardu' => ['controller' => 'GarduController', 'method' => 'tambahData'],
    'tambah-gardu' => ['controller' => 'GarduController', 'method' => 'tambahData'],
    'simpan-manual-gardu' => ['controller' => 'GarduController', 'method' => 'simpanManual'],
    'hapus-gardu' => ['controller' => 'GarduController', 'method' => 'delete'],
    'edit-gardu' => ['controller' => 'GarduController', 'method' => 'edit'],
    'update-gardu' => ['controller' => 'GarduController', 'method' => 'edit'],
    
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

    'data-pemeliharaan' => ['controller' => 'PemeliharaanController', 'method' => 'index'],

    'cluster' => ['controller' => 'ClusterController', 'method' => 'index'],
    
    'split-data' => ['controller' => 'ClusteringController', 'method' => 'splitData'],

    'prediksi' => ['controller' => 'PrediksiController', 'method' => 'index'],
    'prediksi-process' => ['controller' => 'PrediksiController', 'method' => 'process'],
    'prediksi-filter' => ['controller' => 'PrediksiController', 'method' => 'filterByRisk'],
    'prediksi-log' => ['controller' => 'PrediksiController', 'method' => 'viewLog'],
    'prediksi-export-log' => ['controller' => 'PrediksiController', 'method' => 'exportLog'],
    'prediksi-clear-log' => ['controller' => 'PrediksiController', 'method' => 'clearLog'],
];

if (isset($page_mapping[$page])) {
    $controller_name = $page_mapping[$page]['controller'];
    $method_name = $page_mapping[$page]['method'];
    $controller_path = "controller/{$controller_name}.php";
    
    if (file_exists($controller_path)) {
        include_once $controller_path;
        if (class_exists($controller_name)) {
            $controller_instance = new $controller_name();
            if (method_exists($controller_instance, $method_name)) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $method_name === 'importExcel' && isset($_FILES['fileExcel'])) {
                    $preview = isset($_POST['preview']) ? $_POST['preview'] : 0;
                    $bulan = $_POST['bulan'] ?? '';
                    $tahun = $_POST['tahun'] ?? '';
                    $controller_instance->$method_name($_FILES['fileExcel'], $preview, $bulan, $tahun);
                } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller_instance->$method_name($_POST);
                } else {
                    $controller_instance->$method_name();
                }
                exit;
            } else {
                error_log("Method {$method_name} not found in {$controller_name}");
                header("HTTP/1.0 404 Not Found");
                include 'view/404.php';
                exit;
            }
        }
    } else {
        error_log("Controller file not found for page {$page}: {$controller_path}");
        header("HTTP/1.0 404 Not Found");
        include 'view/404.php';
        exit;
    }
}

$parts = explode('-', $page);
if (count($parts) > 0) {
    $controller_name = ucfirst($parts[0]) . 'Controller';
    $method_name = isset($parts[1]) ? implode('', array_slice($parts, 1)) : 'index';
    $controller_path = "controller/{$controller_name}.php";
    
    if (file_exists($controller_path)) {
        include_once $controller_path;
        if (class_exists($controller_name)) {
            $controller_instance = new $controller_name();
            if (method_exists($controller_instance, $method_name)) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller_instance->$method_name($_POST);
                } else {
                    $controller_instance->$method_name();
                }
                exit;
            }
        }
    }
}

error_log("No route found for page: {$page}, controller: {$controller}, action: {$action}");
include 'view/404.php';