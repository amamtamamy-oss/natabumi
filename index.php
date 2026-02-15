<?php
include 'koneksi.php';

// --- 1. AMBIL DATA REAL-TIME ---
$qSampah = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(berat_jumlah) as total FROM transaksi"));
$masuk_kg = ($qSampah['total'] > 0) ? $qSampah['total'] / 1000 : 0; 

$qPoin = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(saldo_poin) as total FROM siswa"));
$total_poin = ($qPoin['total'] > 0) ? $qPoin['total'] : 0;

$qSiswa = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa"));
$jml_siswa = $qSiswa['total'];

$qMitra = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pedagang"));
$jml_mitra = $qMitra['total'];

// --- 2. LEADERBOARD ---
$qTop = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY saldo_poin DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eco-Health School - MA Almuslimun</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #fcfcfc; }
        
        /* HERO SECTION */
        .hero-section {
            background: linear-gradient(135deg, #0eb17f 0%, #06d6a0 100%);
            color: white; padding-top: 90px; padding-bottom: 160px; text-align: center;
            border-bottom-left-radius: 50% 30px; border-bottom-right-radius: 50% 30px;
        }
        
        /* LOGO & NAV */
        .top-nav { position: absolute; top: 20px; width: 100%; padding: 0 40px; display: flex; justify-content: space-between; align-items: center; z-index: 10; }
        .brand-logo { font-weight: bold; font-size: 1.2rem; display: flex; align-items: center; }
        .login-link { color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem; padding: 8px 20px; border: 2px solid rgba(255,255,255,0.5); border-radius: 30px; transition:0.3s;}
        .login-link:hover { background: white; color: #0eb17f; text-decoration: none; border-color: white; }

        /* AREA DUA LOGO */
        .hero-logo-wrapper { display: flex; justify-content: center; align-items: center; margin-bottom: 25px; }
        .hero-logo-box {
            background: white; padding: 10px; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.15); margin: 0 15px;
            display: flex; align-items: center; justify-content: center; height: 100px; width: 100px; transition: transform 0.3s;
        }
        .hero-logo-box:hover { transform: scale(1.1); }
        .hero-logo-img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .logo-separator { color: white; font-weight: bold; font-size: 1.5rem; opacity: 0.8; }

        /* KARTU STATISTIK */
        .stat-card {
            background: white; border: none; border-radius: 20px; padding: 30px 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); text-align: center; transition: transform 0.3s; height: 100%;
        }
        .stat-card:hover { transform: translateY(-10px); }
        .icon-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 24px; color: white; }
        
        /* TOMBOL UTAMA */
        .btn-main { background: white; color: #0eb17f; font-weight: bold; padding: 12px 30px; border-radius: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: none; transition: 0.3s; margin: 5px; }
        .btn-main:hover { transform: scale(1.05); background: #f0fff4; color: #098762; text-decoration: none; }
        .btn-outline-custom { background: transparent; border: 2px solid white; color: white; font-weight: bold; padding: 10px 30px; border-radius: 50px; transition: 0.3s; margin: 5px; }
        .btn-outline-custom:hover { background: white; color: #0eb17f; text-decoration: none; }

        /* LEADERBOARD & LAINNYA */
        .rank-badge { width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: bold; }
        .rank-1 { background: #FFD700; color: white; } .rank-2 { background: #C0C0C0; color: white; } .rank-3 { background: #CD7F32; color: white; } .rank-other { background: #f0f0f0; color: #777; }
        
        .footer { text-align: center; padding: 40px; color: #888; font-size: 0.9rem; margin-top: 50px; border-top: 1px solid #eee; }
    </style>
</head>
<body>

    <div class="top-nav">
        <div class="brand-logo"><i class="fas fa-leaf mr-2"></i> Eco-Health</div>
        <a href="login.php" class="login-link">Login Admin <i class="fas fa-sign-in-alt ml-1"></i></a>
    </div>

    <div class="hero-section">
        <div class="container mt-2">
            <div class="hero-logo-wrapper">
                <div class="hero-logo-box" title="Nata Bumi">
                    <img src="logo_nata.png" class="hero-logo-img" onerror="this.src='https://via.placeholder.com/100?text=LOGO+1'"> 
                </div>
                <span class="logo-separator"><i class="fas fa-times"></i></span>
                <div class="hero-logo-box" title="MA Almuslimun">
                    <img src="logo_ma.png" class="hero-logo-img" onerror="this.src='https://via.placeholder.com/100?text=LOGO+2'">
                </div>
            </div>

            <h1 class="font-weight-bold display-4">Eco-Health School</h1>
            <p class="lead mb-4" style="opacity: 0.9;">Sinergi Bank Sampah & Kantin Sehat<br>Mewujudkan Generasi Peduli Lingkungan</p>
            
            <div>
                <a href="login_siswa.php" class="btn btn-main shadow-lg"><i class="fas fa-user-graduate mr-2"></i>Masuk Siswa</a>
                <a href="login.php" class="btn btn-outline-custom"><i class="fas fa-user-cog mr-2"></i>Masuk Petugas</a>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: -80px; position: relative; z-index: 2;">
        <div class="row px-2">
            <div class="col-md-3 col-6 mb-4 px-2"><div class="stat-card"><div class="icon-circle" style="background: linear-gradient(135deg, #28a745, #20c997);"><i class="fas fa-recycle"></i></div><h3 class="font-weight-bold mb-0 text-dark"><?= number_format($masuk_kg, 1) ?> <small>Kg</small></h3><small class="text-muted font-weight-bold">TERKELOLA</small></div></div>
            <div class="col-md-3 col-6 mb-4 px-2"><div class="stat-card"><div class="icon-circle" style="background: linear-gradient(135deg, #ffc107, #fd7e14);"><i class="fas fa-coins"></i></div><h3 class="font-weight-bold mb-0 text-dark"><?= number_format($total_poin) ?></h3><small class="text-muted font-weight-bold">POIN SISWA</small></div></div>
            <div class="col-md-3 col-6 mb-4 px-2"><div class="stat-card"><div class="icon-circle" style="background: linear-gradient(135deg, #17a2b8, #36b9cc);"><i class="fas fa-users"></i></div><h3 class="font-weight-bold mb-0 text-dark"><?= $jml_siswa ?></h3><small class="text-muted font-weight-bold">SISWA AKTIF</small></div></div>
            <div class="col-md-3 col-6 mb-4 px-2"><div class="stat-card"><div class="icon-circle" style="background: linear-gradient(135deg, #dc3545, #e74a3b);"><i class="fas fa-utensils"></i></div><h3 class="font-weight-bold mb-0 text-dark"><?= $jml_mitra ?></h3><small class="text-muted font-weight-bold">MITRA KANTIN</small></div></div>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="text-center mb-5">
            <h3 class="font-weight-bold text-dark">Mengapa Bergabung?</h3>
            <div style="width: 60px; height: 3px; background: #0eb17f; margin: 10px auto;"></div>
        </div>
        
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="p-3">
                    <i class="fas fa-trash-alt fa-3x mb-3 text-success"></i>
                    <h5 class="font-weight-bold">Ubah Sampah Jadi Berkah</h5>
                    <p class="text-muted small px-3">Jangan buang sampahmu! Setorkan ke Bank Sampah sekolah dan tukar menjadi poin belanja yang bermanfaat.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-3">
                    <i class="fas fa-heartbeat fa-3x mb-3 text-danger"></i>
                    <h5 class="font-weight-bold">Pantau Asupan Gizi</h5>
                    <p class="text-muted small px-3">Fitur "Jurnal Jajanku" membantu siswa memantau apa yang mereka makan agar tetap sehat dan bugar.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="p-3">
                    <i class="fas fa-tree fa-3x mb-3 text-info"></i>
                    <h5 class="font-weight-bold">Sekolah Adiwiyata</h5>
                    <p class="text-muted small px-3">Dukung MA Almuslimun menjadi sekolah hijau yang bebas sampah plastik dan ramah lingkungan.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container bg-light py-5 rounded-lg mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center mb-4">
                <h4 class="font-weight-bold" style="color: #0eb17f;"><i class="fas fa-trophy mr-2"></i>Top 5 Pahlawan Lingkungan</h4>
                <p class="text-muted">Siswa teladan dengan kontribusi poin tertinggi minggu ini</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-lg p-2 bg-white">
                    <div class="list-group list-group-flush">
                        <?php 
                        $rank = 1;
                        if(mysqli_num_rows($qTop) > 0) {
                            while($top = mysqli_fetch_array($qTop)){ 
                                if($rank == 1) $bg_badge = "rank-1"; elseif($rank == 2) $bg_badge = "rank-2"; elseif($rank == 3) $bg_badge = "rank-3"; else $bg_badge = "rank-other";
                        ?>
                        <div class="list-group-item d-flex align-items-center py-3 border-0 bg-transparent" style="border-bottom: 1px solid #f8f9fa;">
                            <div class="mr-3"><div class="rank-badge <?= $bg_badge ?>"><?= $rank ?></div></div>
                            <div class="w-100">
                                <h6 class="mb-0 font-weight-bold text-dark"><?= $top['nama_lengkap'] ?></h6>
                                <small class="text-muted"><?= $top['kelas'] ?></small>
                            </div>
                            <div class="text-right"><h6 class="font-weight-bold text-success mb-0"><?= number_format($top['saldo_poin']) ?> <small>Poin</small></h6></div>
                        </div>
                        <?php $rank++; } } else { echo "<div class='text-center py-4 text-muted'>Belum ada data.</div>"; } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <h5 class="font-weight-bold mb-2" style="color: #0eb17f;">MA Almuslimun Eco-School</h5>
        <p class="mb-1">Kolaborasi Bersama Nata Bumi</p>
        <small>&copy; 2026 Sistem Informasi Bank Sampah Digital</small>
    </div>

</body>
</html>