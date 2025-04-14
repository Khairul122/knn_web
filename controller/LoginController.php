<?php
session_start();
include '../koneksi.php';

// Login logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    $data  = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['temp_login'] = [
            'id_user' => $data['id_user'],
            'username' => $data['username'],
            'email' => $data['email'],
            'level' => $data['level']
        ];
        $_SESSION['login_status'] = 'success';
    } else {
        $_SESSION['login_status'] = 'error';
    }

    header("Location: ../index.php?page=login");
    exit;
} else {
    header("Location: ../index.php?page=login");
    exit;
}

// Logout logic
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header("Location: ../index.php?page=login");
    exit;
}
