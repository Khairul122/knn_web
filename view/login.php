<?php
$status = isset($_SESSION['login_status']) ? $_SESSION['login_status'] : null;
$redirect = false;

if ($status === 'success' && isset($_SESSION['temp_login'])) {
    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $_SESSION['temp_login']['id_user'];
    $_SESSION['username'] = $_SESSION['temp_login']['username'];
    $_SESSION['email'] = $_SESSION['temp_login']['email'];
    $_SESSION['level'] = $_SESSION['temp_login']['level'];
    unset($_SESSION['temp_login']);
    $redirect = true;
}
unset($_SESSION['login_status']);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistem Prediksi Gangguan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow p-4" style="min-width: 350px; max-width: 400px; width: 100%;">
        <h4 class="text-center mb-4">Login Admin</h4>
        <form action="controller/LoginController.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
        </form>
    </div>

    <?php if ($status): ?>
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
            <div id="toastLogin" class="toast align-items-center text-white <?= $status === 'success' ? 'bg-success' : 'bg-danger' ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $status === 'success' ? 'Login berhasil, Selamat Datang Admin' : 'Login gagal. Periksa kembali username dan password.' ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toastEl = document.getElementById('toastLogin');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
            <?php if ($redirect): ?>
                setTimeout(() => {
                    window.location.href = 'index.php?page=home';
                }, 2000);
            <?php endif; ?>
        }
    </script>
</body>

</html>