<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['login']) && (!isset($_GET['page']) || $_GET['page'] !== 'login')) {
    header("Location: index.php?page=login");
    exit;
}

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'login':
        include 'view/login.php';
        break;

    case 'home':
        include 'view/home.php';
        break;

    case 'data-gardu':
    case 'import-gardu':
        include 'controller/GarduController.php';
        $controller = new GarduController();

        if ($page === 'data-gardu') {
            $controller->index();
        } elseif ($page === 'import-gardu') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
                $controller->importExcel($_FILES['file_excel']);
            } else {
                header("Location: index.php?page=data-gardu");
            }
        }
        break;

    case 'tambah-gardu':
        include 'view/gardu/tambah-data.php';
        break;

    case 'import-form-gardu':
        include 'view/gardu/import-data.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        break;

    default:
        include 'view/404.php';
        break;
}
