<?php
session_start();
include 'koneksi.php';

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $cek = mysqli_query($koneksi, "SELECT * FROM petugas WHERE username='$username' AND password='$password'");
    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_array($cek);
        $_SESSION['nama_petugas'] = $data['nama_petugas'];
        $_SESSION['status'] = "login";
        header("location:dashboard.php");
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Eco School</title>
    <link rel="icon" href="logo_ma.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: #fff;
            padding: 30px 20px 0 20px;
            text-center;
        }
        .btn-login {
            border-radius: 50px;
            font-weight: bold;
            letter-spacing: 1px;
            background: #11998e;
            border: none;
        }
        .btn-login:hover {
            background: #0e857b;
        }
        .form-control {
            border-radius: 50px;
            padding-left: 20px;
            height: 45px;
        }
    </style>
</head>
<body>

    <div class="card card-login">
        <div class="login-header">
            <img src="logo_ma.png" width="60" class="mb-2">
            <img src="logo_nata.png" width="60" class="mb-2 ml-2">
            <h4 class="font-weight-bold text-dark mt-2">Login Admin</h4>
            <p class="text-muted small">Silakan masuk untuk mengelola data.</p>
        </div>
        
        <div class="card-body bg-white p-4">
            
            <?php if(isset($error)) { ?>
                <div class="alert alert-danger text-center py-2 mb-3 small rounded-pill">
                    <i class="fas fa-exclamation-circle"></i> Username/Password Salah!
                </div>
            <?php } ?>

            <form method="POST">
                <div class="form-group mb-3">
                    <input type="text" name="username" class="form-control bg-light border-0" placeholder="Username" required autofocus>
                </div>
                <div class="form-group mb-4">
                    <input type="password" name="password" class="form-control bg-light border-0" placeholder="Password" required>
                </div>
                <button type="submit" name="login" class="btn btn-login btn-block btn-primary py-2 shadow-sm">
                    MASUK SEKARANG <i class="fas fa-arrow-right ml-1"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="index.php" class="text-secondary small" style="text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Depan
                </a>
            </div>
        </div>
    </div>

</body>
</html>