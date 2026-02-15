<?php
session_start();
include 'koneksi.php';

if(isset($_POST['masuk'])){
    $nis = $_POST['nis'];
    
    // Cek apakah NIS ada di database
    $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_array($cek);
        
        // Simpan data siswa ke session biar sistem tahu siapa yang login
        $_SESSION['status_siswa'] = "login";
        $_SESSION['id_siswa'] = $data['id_siswa'];
        $_SESSION['nama_siswa'] = $data['nama_lengkap'];
        
        echo "<script>alert('Selamat Datang, $data[nama_lengkap]!'); window.location='dashboard_siswa.php';</script>";
    } else {
        echo "<script>alert('NIS tidak ditemukan! Coba cek lagi atau tanya Pak Guru.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Siswa - Eco School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #e2e6ea; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { max-width: 400px; width: 100%; border-radius: 15px; }
        .btn-green { background: #28a745; color: white; border-radius: 50px; }
    </style>
</head>
<body>

    <div class="card shadow-lg card-login border-0">
        <div class="card-body p-5 text-center">
            <h1 class="mb-3">🌿</h1>
            <h4 class="font-weight-bold text-dark">Portal Siswa</h4>
            <p class="text-muted small">Masukkan NIS untuk Cek Tabungan & Info Sehat</p>

            <form method="POST" class="mt-4">
                <div class="form-group">
                    <input type="text" name="nis" class="form-control form-control-lg text-center" placeholder="Contoh: 12345" required autocomplete="off">
                </div>
                <button type="submit" name="masuk" class="btn btn-green btn-block btn-lg shadow">MASUK SEKARANG</button>
            </form>
            
            <div class="mt-4">
                <a href="login.php" class="text-secondary small">Login Guru / Admin</a>
            </div>
        </div>
    </div>

</body>
</html>