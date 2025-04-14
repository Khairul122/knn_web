<?php
session_start();

if (!isset($_SESSION['login']) && (!isset($_GET['page']) || $_GET['page'] !== 'login')) {
    header("Location: index.php?page=login");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'login':
        include 'view/login.php';
        break;
    case 'home':
        include 'view/home.php';
        break;
    
    // Data Pemeliharaan
    case 'data-pemeliharaan':
        include 'view/data-pemeliharaan/index.php';
        break;
    case 'data-pemeliharaan-tambah':
        include 'view/data-pemeliharaan/tambah-data.php';
        break;
    case 'data-pemeliharaan-edit':
        include 'view/data-pemeliharaan/edit-data.php';
        break;

    case 'training':
        include 'view/training.php';
        break;
    case 'prediksi':
        include 'view/prediksi.php';
        break;
    case 'hasil':
        include 'view/hasil.php';
        break;
    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        break;
    default:
        include 'view/404.php';
        break;
}
