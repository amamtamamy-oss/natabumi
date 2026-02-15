<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';
$nama_petugas = $_SESSION['nama_petugas'];

// --- 1. HITUNG STOK SAMPAH GUDANG ---
// A. Total Sampah Masuk (Dari Siswa) - Konversi ke Kg
$qMasuk = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(berat_jumlah) as total FROM transaksi"));
$masuk_kg = $qMasuk['total'] / 1000; 

// B. Total Sampah Keluar (Jual ke Pengepul) - Sudah dalam Kg (Kolom baru)
// Cek dulu apakah kolom berat_keluar ada (untuk menghindari error jika lupa langkah 1)
$cekKolom = mysqli_query($koneksi, "SHOW COLUMNS FROM keuangan_masuk LIKE 'berat_keluar'");
if(mysqli_num_rows($cekKolom) > 0){
    $qKeluar = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(berat_keluar) as total FROM keuangan_masuk"));
    $keluar_kg = $qKeluar['total'];
} else {
    $keluar_kg = 0; // Kalau kolom belum dibuat, anggap 0
}

// C. Stok Gudang (Sisa)
$stok_gudang = $masuk_kg - $keluar_kg;
if($stok_gudang < 0) $stok_gudang = 0; // Jaga-jaga biar gak minus

// --- DATA LAINNYA ---
$qPoin = mysqli_fetch_array(mysqli_query($koneksi, "SELECT SUM(saldo_poin) as total FROM siswa"));
$total_poin = $qPoin['total'];
$qMerah = mysqli_fetch_array(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM menu_sehat WHERE level_sehat <= 2"));
$zona_merah = $qMerah['total'];

// Grafik
$labels_sampah = ""; $data_sampah = "";
$qPie = mysqli_query($koneksi, "SELECT k.nama_jenis, SUM(t.berat_jumlah) as berat FROM transaksi t JOIN kategori_sampah k ON t.id_kategori=k.id_kategori GROUP BY k.nama_jenis");
while($p = mysqli_fetch_array($qPie)){ $labels_sampah .= "'$p[nama_jenis]',"; $data_sampah .= ($p['berat']/1000) . ","; }
$labels_kelas = ""; $data_kelas = "";
$qBar = mysqli_query($koneksi, "SELECT kelas, SUM(saldo_poin) as poin FROM siswa GROUP BY kelas");
while($b = mysqli_fetch_array($qBar)){ $labels_kelas .= "'$b[kelas]',"; $data_kelas .= "$b[poin],"; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Eco School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 90px; border-radius: 0 0 50px 50px; color: white; }
        .card-menu { background: white; border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: 0.3s; }
        .card-menu:hover { transform: translateY(-5px); }
        .icon-circle { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; }
        .bg-soft-green { background: #e0f7fa; color: #11998e; }
        .bg-soft-blue { background: #e3f2fd; color: #1e88e5; }
        .bg-soft-orange { background: #fff3e0; color: #fb8c00; }
        .card-stat { border: none; border-radius: 15px; }
        .btn-keping { background: white; border: none; border-radius: 12px; padding: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: 0.2s; display: flex; align-items: center; text-decoration: none; color: #555; height: 100%; }
        .btn-keping:hover { transform: translateY(-3px); text-decoration: none; color: #11998e; }
    </style>
</head>
<body>

<div class="header-bg">
    <nav class="navbar navbar-expand-lg navbar-dark bg-transparent mb-3 pt-4">
      <div class="container">
        <a class="navbar-brand font-weight-bold" href="dashboard.php"><i class="fas fa-leaf mr-2"></i>Eco-Health Admin</a>
        <div class="ml-auto"><span class="text-white mr-3">Halo, <b><?= $nama_petugas ?></b></span><a href="logout.php" class="btn btn-light btn-sm rounded-pill px-3">Keluar</a></div>
      </div>
    </nav>
    <div class="container text-center pb-4"><h2 class="font-weight-bold">Dashboard Monitoring</h2></div>
</div>

<div class="container" style="margin-top: -70px;">
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3"><a href="admin_input.php" class="text-decoration-none text-dark"><div class="card card-menu p-4 h-100 text-center"><div class="icon-circle bg-soft-green mx-auto"><i class="fas fa-recycle"></i></div><h5 class="font-weight-bold">Input Sampah</h5><p class="text-muted small">Terima setoran siswa</p></div></a></div>
        <div class="col-md-4 mb-3"><a href="tukar_hadiah.php" class="text-decoration-none text-dark"><div class="card card-menu p-4 h-100 text-center"><div class="icon-circle bg-soft-blue mx-auto"><i class="fas fa-shopping-cart"></i></div><h5 class="font-weight-bold">Kasir Koperasi</h5><p class="text-muted small">Belanja & Tukar Poin</p></div></a></div>
        <div class="col-md-4 mb-3"><a href="kelola_menu.php" class="text-decoration-none text-dark"><div class="card card-menu p-4 h-100 text-center"><div class="icon-circle bg-soft-orange mx-auto"><i class="fas fa-utensils"></i></div><h5 class="font-weight-bold">Cek Kantin</h5><p class="text-muted small">Monitoring menu sehat</p></div></a></div>
    </div>

    <h6 class="font-weight-bold text-secondary mb-3 ml-1"><i class="fas fa-warehouse mr-2"></i>Monitoring Gudang Sampah</h6>
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <div class="card card-stat bg-white p-3 h-100 d-flex flex-row align-items-center border-left-primary" style="border-left: 5px solid #4e73df;">
                <div class="mr-3 text-primary"><i class="fas fa-check-circle fa-2x"></i></div>
                <div><small class="text-muted font-weight-bold">TOTAL TERKELOLA</small><h4 class="mb-0 text-dark"><?= number_format($masuk_kg, 1) ?> Kg</h4><small class="text-xs text-primary">Dari Siswa</small></div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card card-stat bg-white p-3 h-100 d-flex flex-row align-items-center border-left-success" style="border-left: 5px solid #1cc88a;">
                <div class="mr-3 text-success"><i class="fas fa-truck-loading fa-2x"></i></div>
                <div><small class="text-muted font-weight-bold">SUDAH TERJUAL</small><h4 class="mb-0 text-dark"><?= number_format($keluar_kg, 1) ?> Kg</h4><small class="text-xs text-success">Ke Pengepul</small></div>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <div class="card card-stat bg-white p-3 h-100 d-flex flex-row align-items-center border-left-warning" style="border-left: 5px solid #f6c23e;">
                <div class="mr-3 text-warning"><i class="fas fa-boxes fa-2x"></i></div>
                <div><small class="text-muted font-weight-bold">STOK GUDANG (NUMPUK)</small><h4 class="mb-0 text-danger"><?= number_format($stok_gudang, 1) ?> Kg</h4><small class="text-xs text-danger">Belum jadi uang</small></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="section-title">Data Master</div>
            <div class="row">
                <div class="col-6 mb-3"><a href="kelola_siswa.php" class="btn-keping"><i class="fas fa-users text-primary"></i><div><b>Data Siswa</b><br><small>Edit & Cetak</small></div></a></div>
                <div class="col-6 mb-3"><a href="kelola_produk.php" class="btn-keping"><i class="fas fa-box-open text-warning"></i><div><b>Produk Koperasi</b><br><small>Stok Barang</small></div></a></div>
                <div class="col-6 mb-3"><a href="kelola_sampah.php" class="btn-keping"><i class="fas fa-trash-alt text-success"></i><div><b>Atur Sampah</b><br><small>Harga Pengepul</small></div></a></div>
                <div class="col-6 mb-3"><a href="kelola_pedagang.php" class="btn-keping"><i class="fas fa-store text-info"></i><div><b>Data Pedagang</b><br><small>Mitra Kantin</small></div></a></div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="section-title">Laporan & Keuangan</div>
            <div class="row">
                <div class="col-6 mb-3"><a href="laporan_transaksi.php" class="btn-keping"><i class="fas fa-receipt text-primary"></i><div><b>Jurnal Transaksi</b><br><small>Laporan Belanja</small></div></a></div>
                <div class="col-6 mb-3"><a href="laba_rugi.php" class="btn-keping"><i class="fas fa-chart-line text-success"></i><div><b>Laba Rugi</b><br><small>Keuangan Sekolah</small></div></a></div>
                <div class="col-6 mb-3"><a href="input_penjualan.php" class="btn-keping"><i class="fas fa-money-bill-wave text-success"></i><div><b>Catat Pemasukan</b><br><small>Jual ke Pengepul</small></div></a></div>
                <div class="col-6 mb-3"><a href="backup_data.php" class="btn-keping"><i class="fas fa-database text-secondary"></i><div><b>Backup Data</b><br><small>Simpan Database</small></div></a></div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6 mb-3"><div class="card card-stat h-100"><div class="card-body"><canvas id="chartSampah"></canvas></div></div></div>
        <div class="col-md-6 mb-3"><div class="card card-stat h-100"><div class="card-body"><canvas id="chartKelas"></canvas></div></div></div>
    </div>
</div>

<script>
    new Chart(document.getElementById('chartSampah'), { type: 'doughnut', data: { labels: [<?= $labels_sampah ?>], datasets: [{ data: [<?= $data_sampah ?>], backgroundColor: ['#11998e', '#38ef7d', '#ffc107', '#dc3545'], borderWidth: 0 }] }, options: { cutout: '70%', plugins: { legend: { position: 'right' } } } });
    new Chart(document.getElementById('chartKelas'), { type: 'bar', data: { labels: [<?= $labels_kelas ?>], datasets: [{ label: 'Poin', data: [<?= $data_kelas ?>], backgroundColor: '#11998e', borderRadius: 5 }] }, options: { scales: { y: { beginAtZero: true, grid: { display: false } } } } });
</script>
</body>
</html>