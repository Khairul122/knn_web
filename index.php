<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$page = $_GET['page'] ?? 'home';

if (!isset($_SESSION['login']) && $page !== 'login') {
    header("Location: index.php?page=login");
    exit;
}

if ($page === 'login') {
    include 'view/login.php';
    exit;
}

if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

if ($page === 'home') {
    include 'view/home.php';
    exit;
}

if ($page === 'import-form-gardu') {
    include 'view/gardu/import-data.php';
    exit;
}

if ($page === 'data-gardu') {
    include_once 'controller/GarduController.php';
    $controller = new GarduController();
    $controller->index();
    exit;
}

if ($page === 'import-gardu-save') {
    include_once 'controller/GarduController.php';
    $controller = new GarduController();
    $controller->importGarduSave();
    exit;
}

// Routing dinamis: page=gardu-importExcel → GarduController::importExcel()
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
