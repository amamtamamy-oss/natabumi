<?php
session_start();
if(!isset($_SESSION['status_siswa']) || $_SESSION['status_siswa'] != "login"){
    header("location:login_siswa.php");
    exit;
}
include 'koneksi.php';

// Ambil Poin Siswa Terkini
$id_siswa = $_SESSION['id_siswa'];
$qS = mysqli_query($koneksi, "SELECT saldo_poin FROM siswa WHERE id_siswa='$id_siswa'");
$dS = mysqli_fetch_array($qS);
$saldo = $dS['saldo_poin'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Katalog Hadiah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding-bottom: 80px; border-radius: 0 0 40px 40px; color: white; }
        .card-barang { border: none; border-radius: 15px; transition: 0.3s; overflow: hidden; }
        .card-barang:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .harga-badge { background: #f5576c; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">🎁 Tukar Poin</h3>
        <p>Poin Kamu: <span class="font-weight-bold text-warning" style="font-size: 1.2rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.2);"><?= number_format($saldo) ?></span></p>
        <a href="dashboard_siswa.php" class="btn btn-outline-light btn-sm rounded-pill px-4 mt-2">Kembali</a>
    </div>

    <div class="container" style="margin-top: -50px;">
        <div class="row">
            
            <?php
            $qH = mysqli_query($koneksi, "SELECT * FROM katalog_hadiah WHERE stok > 0 ORDER BY harga_poin ASC");
            if(mysqli_num_rows($qH) > 0){
                while($h = mysqli_fetch_array($qH)){
                    // Cek apakah poin cukup
                    $cukup = ($saldo >= $h['harga_poin']) ? 'text-success' : 'text-muted';
                    $btn = ($saldo >= $h['harga_poin']) ? 'btn-success' : 'btn-secondary disabled';
                    $ket = ($saldo >= $h['harga_poin']) ? 'Bisa Ditukar' : 'Poin Kurang';
            ?>
            <div class="col-6 col-md-4 mb-4">
                <div class="card card-barang shadow-sm h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <i class="fas fa-gift fa-3x text-warning mb-3"></i>
                            <h6 class="font-weight-bold text-dark mb-1"><?= $h['nama_barang'] ?></h6>
                            <small class="text-muted">Stok: <?= $h['stok'] ?></small>
                        </div>
                        <div class="mt-3">
                            <div class="harga-badge mb-2"><?= number_format($h['harga_poin']) ?> Poin</div>
                            <button class="btn <?= $btn ?> btn-sm btn-block rounded-pill" style="font-size: 0.8rem;">
                                <?= $ket ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<div class='col-12 text-center text-muted py-5'>Belum ada hadiah tersedia.</div>";
            }
            ?>

        </div>
        
        <div class="alert alert-info text-center rounded-lg shadow-sm mt-3">
            <i class="fas fa-info-circle mr-1"></i> Silakan ke <b>Koperasi / Admin</b> untuk menukarkan poinmu dengan barang di atas.
        </div>
    </div>

</body>
</html>