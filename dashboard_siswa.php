<?php
session_start();
// 1. Cek Login (Wajib)
if(!isset($_SESSION['status_siswa']) || $_SESSION['status_siswa'] != "login"){
    header("location:login_siswa.php");
    exit;
}
include 'koneksi.php';

// 2. Ambil Data Siswa
$id_siswa = $_SESSION['id_siswa'];
$query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
$d = mysqli_fetch_array($query);

// 3. LOGIKA GAMIFIKASI (LEVEL & PANGKAT)
$poin = $d['saldo_poin'];
$target = 1000;
$level = "Pemula 🌱";
$persen = 0;

if($poin < 1000){
    $level = "Pemula 🌱";
    $target = 1000;
} elseif($poin >= 1000 && $poin < 5000){
    $level = "Agen Perubahan 🌿";
    $target = 5000;
} elseif($poin >= 5000 && $poin < 10000){
    $level = "Pejuang Lingkungan 🌳";
    $target = 10000;
} else {
    $level = "Sultan Sampah 👑";
    $target = 20000; // Target visual
}

// Hitung Persentase Progress Bar
$persen = ($poin / $target) * 100;
if($persen > 100) $persen = 100; // Mentok di 100%
$kurang = $target - $poin;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Siswa</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        /* HEADER HIJAU MODERN */
        .header-bg { 
            background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%); 
            padding-bottom: 120px; /* Memberi ruang untuk kartu saldo */
            border-radius: 0 0 40px 40px; 
            color: white; 
            text-align: center;
            position: relative;
        }
        
        /* TOMBOL LOGOUT (Pojok Kanan Atas) */
        .btn-logout {
            position: absolute; 
            top: 20px; 
            right: 20px;
            width: 45px; height: 45px;
            background: rgba(255,255,255,0.25);
            border: 1px solid rgba(255,255,255,0.5);
            color: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            text-decoration: none; transition: 0.3s;
            z-index: 100;
        }
        .btn-logout:hover { background: #dc3545; color: white; border-color: #dc3545; }

        /* FOTO PROFIL */
        .profile-pic {
            width: 80px; height: 80px; background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; color: #11998e; font-weight: bold; margin: 0 auto 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border: 3px solid rgba(255,255,255,0.4);
        }
        
        /* KARTU SALDO & PROGRESS */
        .saldo-box {
            background: white; border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            margin-top: -80px; /* Naik ke atas header */
            padding: 25px; position: relative; z-index: 10;
        }

        /* MENU NAVIGASI */
        .card-menu {
            border: none; border-radius: 15px; transition: 0.3s;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05); text-align: center; padding: 20px 10px;
            height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

    <div class="header-bg pt-5">
        
        <a href="logout.php" class="btn-logout shadow" onclick="return confirm('Yakin ingin keluar aplikasi?')" title="Keluar">
            <i class="fas fa-power-off"></i>
        </a>

        <div class="profile-pic">
            <?= strtoupper(substr($d['nama_lengkap'], 0, 1)) ?>
        </div>
        <h4 class="font-weight-bold mb-0">Hi, <?= explode(' ', $d['nama_lengkap'])[0] ?>!</h4>
        <p class="mb-0 opacity-75 small"><i class="fas fa-graduation-cap mr-1"></i> Kelas <?= $d['kelas'] ?></p>
        
        <span class="badge badge-pill badge-light text-success mt-2 px-3 py-2 font-weight-bold shadow-sm">
            Level: <?= $level ?>
        </span>
    </div>

    <div class="container mb-5">
        
        <div class="saldo-box mb-4">
            <div class="row align-items-center">
                <div class="col-8 border-right">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size: 10px;">Saldo Poin</small>
                    <h1 class="font-weight-bold text-success mb-2"><?= number_format($poin) ?></h1>
                    
                    <div class="mr-2">
                        <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 10px;">
                            <span>Naik Level</span>
                            <span>Target: <?= number_format($target) ?></span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $persen ?>%"></div>
                        </div>
                        <small class="text-warning font-weight-bold d-block mt-1" style="font-size: 10px;">
                            <?php if($kurang > 0) { ?>
                                🔥 Kurang <?= number_format($kurang) ?> poin lagi!
                            <?php } else { ?>
                                👑 Level Maksimal!
                            <?php } ?>
                        </small>
                    </div>
                </div>
                
                <div class="col-4 text-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= $d['nis'] ?>" class="img-fluid rounded mb-1" style="max-width: 80px;">
                    <small class="d-block font-weight-bold text-dark" style="font-size: 9px;">NIS: <?= $d['nis'] ?></small>
                </div>
            </div>
        </div>

        <h6 class="font-weight-bold text-secondary mb-3 ml-2">Menu Siswa</h6>
        <div class="row mb-4">
            
            <div class="col-4 pr-1">
                <a href="input_jajan.php" class="text-decoration-none text-dark">
                    <div class="card card-menu bg-white">
                        <i class="fas fa-edit fa-2x text-info mb-2"></i>
                        <h6 class="font-weight-bold small mb-0">Lapor Jajan</h6>
                    </div>
                </a>
            </div>

            <div class="col-4 px-1">
                <a href="katalog_siswa.php" class="text-decoration-none text-dark">
                    <div class="card card-menu bg-white">
                        <i class="fas fa-gift fa-2x text-warning mb-2"></i>
                        <h6 class="font-weight-bold small mb-0">Katalog</h6>
                    </div>
                </a>
            </div>

            <div class="col-4 pl-1">
                <a href="profil_siswa.php" class="text-decoration-none text-dark">
                    <div class="card card-menu bg-white">
                        <i class="fas fa-user fa-2x text-success mb-2"></i>
                        <h6 class="font-weight-bold small mb-0">Profil</h6>
                    </div>
                </a>
            </div>
        </div>

        <div class="alert alert-success shadow-sm border-0 rounded-lg d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-lightbulb fa-2x mr-3 text-success"></i>
            <div>
                <strong>Tips Hemat:</strong>
                <p class="mb-0 small" style="line-height: 1.2;">"Bawa bekal dari rumah, sehat di badan, hemat di kantong, banyak poin!"</p>
            </div>
        </div>

        <div class="row text-center mb-4">
            <div class="col-6 pr-1">
                <a href="#" class="btn btn-outline-primary btn-sm btn-block rounded-pill">Riwayat Setor</a>
            </div>
            <div class="col-6 pl-1">
                <a href="#" class="btn btn-outline-success btn-sm btn-block rounded-pill">Riwayat Belanja</a>
            </div>
        </div>
        
        <div class="text-center text-muted small py-4">
            &copy; 2026 Eco-Health School v2.0
        </div>
    </div>

</body>
</html>