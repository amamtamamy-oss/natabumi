<?php
session_start();
if(!isset($_SESSION['status_siswa']) || $_SESSION['status_siswa'] != "login"){
    header("location:login_siswa.php");
    exit;
}
include 'koneksi.php';

$id_siswa = $_SESSION['id_siswa'];
$q = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
$d = mysqli_fetch_array($q);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%); padding-bottom: 100px; border-radius: 0 0 40px 40px; color: white; }
        .profile-img { width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 5px 15px rgba(0,0,0,0.2); font-size: 40px; color: #11998e; font-weight: bold; }
        .card-info { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-top: -60px; }
        .list-group-item { border: none; border-bottom: 1px solid #eee; padding: 15px 0; }
        .list-group-item:last-child { border-bottom: none; }
    </style>
</head>
<body>

    <div class="header-bg pt-5 text-center">
        <div class="profile-img mb-3">
            <?= strtoupper(substr($d['nama_lengkap'], 0, 1)) ?>
        </div>
        <h4 class="font-weight-bold mb-0"><?= $d['nama_lengkap'] ?></h4>
        <p class="mb-0 opacity-75">Kelas <?= $d['kelas'] ?></p>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <div class="card card-info p-4 bg-white mb-4">
                    <h6 class="font-weight-bold text-secondary mb-3">Biodata Diri</h6>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">NIS / ID</span>
                            <span class="font-weight-bold text-dark"><?= $d['nis'] ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">No. HP</span>
                            <span class="font-weight-bold text-dark"><?= ($d['no_hp']) ? $d['no_hp'] : '-' ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Saldo Poin</span>
                            <span class="font-weight-bold text-success"><?= number_format($d['saldo_poin']) ?> Poin</span>
                        </li>
                    </ul>
                </div>

                <div class="card border-0 shadow-sm rounded-lg p-4 text-center mb-4">
                    <h6 class="font-weight-bold text-secondary mb-3">Kartu Digital (Scan Me)</h6>
                    
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $d['nis'] ?>" class="img-fluid rounded mx-auto d-block" style="max-width: 180px;">
                    
                    <p class="small text-muted mt-2">Tunjukkan QR ini ke Admin saat setor sampah.</p>
                </div>

                <a href="login_siswa.php" class="btn btn-danger btn-block rounded-pill py-3 font-weight-bold mb-4" onclick="return confirm('Yakin ingin keluar?')">
                    KELUAR APLIKASI <i class="fas fa-sign-out-alt ml-1"></i>
                </a>

                <div class="text-center mb-5">
                    <a href="dashboard_siswa.php" class="text-secondary text-decoration-none">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>