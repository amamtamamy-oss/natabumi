<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// 1. HITUNG PEMASUKAN (Uang dari Pengepul)
// Cek dulu apakah tabel keuangan_masuk ada datanya
$qMasuk = mysqli_query($koneksi, "SELECT SUM(nominal) as total_masuk FROM keuangan_masuk");
$totalMasuk = 0;
if($qMasuk){
    $dMasuk = mysqli_fetch_array($qMasuk);
    $totalMasuk = $dMasuk['total_masuk'] ?: 0;
}

// 2. HITUNG PENGELUARAN (Poin Siswa)
$qKeluar = mysqli_query($koneksi, "SELECT SUM(poin_total) as total_poin FROM transaksi");
$dKeluar = mysqli_fetch_array($qKeluar);
$totalKeluar = $dKeluar['total_poin'] ?: 0;

// 3. HITUNG LABA
$laba = $totalMasuk - $totalKeluar;
$warna = ($laba >= 0) ? "text-success" : "text-danger";
$status = ($laba >= 0) ? "UNTUNG (SURPLUS)" : "RUGI (DEFISIT)";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">💰 Keuangan Eco-Health</a>
    <div class="navbar-nav ml-auto">
      <a class="nav-link btn btn-success btn-sm text-white" href="input_penjualan.php">+ Catat Pemasukan</a>
      <a class="nav-link btn btn-secondary btn-sm text-white ml-2" href="dashboard.php">Kembali</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="card border-success mb-3 shadow">
                <div class="card-header bg-success text-white">Total Pemasukan</div>
                <div class="card-body">
                    <h3>Rp <?= number_format($totalMasuk) ?></h3>
                    <small>Dari Jual ke Pengepul</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning mb-3 shadow">
                <div class="card-header bg-warning text-dark">Total Beban Poin</div>
                <div class="card-body">
                    <h3>Rp <?= number_format($totalKeluar) ?></h3>
                    <small>Kewajiban ke Siswa</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary mb-3 shadow">
                <div class="card-header bg-primary text-white">Laba Bersih</div>
                <div class="card-body">
                    <h3 class="<?= $warna ?>">Rp <?= number_format($laba) ?></h3>
                    <strong><?= $status ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <h5>Riwayat Pemasukan Uang</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Sumber</th>
                        <th>Keterangan</th>
                        <th>Nominal</th>
                        <th>Pencatat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tampil = mysqli_query($koneksi, "SELECT * FROM keuangan_masuk ORDER BY tanggal DESC");
                    while($r = mysqli_fetch_array($tampil)){
                    ?>
                    <tr>
                        <td><?= $r['tanggal'] ?></td>
                        <td><?= $r['sumber_dana'] ?></td>
                        <td><?= $r['keterangan'] ?></td>
                        <td class="font-weight-bold text-success">Rp <?= number_format($r['nominal']) ?></td>
                        <td><?= $r['pencatat'] ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>