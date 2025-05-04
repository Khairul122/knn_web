<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$page = $_GET['page'] ?? 'home';

if (!isset($_SESSION['login']) && $page !== 'login') {
    header("Location: index.php?page=login");
    exit;
}
switch ($page) {
    case 'login':
        include 'view/login.php';
        exit;
    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    case 'home':
        include 'view/home.php';
        exit;
    
    // Gardu
    case 'import-form-gardu':
        include 'view/gardu/import-data.php';
        exit;
    case 'data-gardu':
        include_once 'controller/GarduController.php';
        $controller = new GarduController();
        $controller->index();
        exit;
    case 'import-gardu-save':
        include_once 'controller/GarduController.php';
        $controller = new GarduController();
        $controller->importGarduSave();
        exit;
    case 'tambah-data-gardu':
        include_once 'controller/GarduController.php';
        $controller = new GarduController();
        $controller->tambahData($_POST);
        exit;
    case 'simpan-manual-gardu':
        include 'controller/GarduController.php';
        $controller = new GarduController();
        $controller->simpanManual();
        break;
    case 'hapus-gardu':
        include 'controller/GarduController.php';
        $controller = new GarduController();
        $controller->delete();
        break;
}

$parts = explode('-', $page);
$controllerName = ucfirst($parts[0]) . 'Controller';
$methodName = isset($parts[1]) ? implode('', array_slice($parts, 1)) : 'index';
$controllerPath = "controller/$controllerName.php";

if (file_exists($controllerPath)) {
    include_once $controllerPath;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->$methodName($_POST);
            } else {
                $controller->$methodName();
            }
            exit;
        }
    }
}

include 'view/404.php';
