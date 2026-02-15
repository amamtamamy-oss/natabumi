<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lengkap - Eco School</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .nav-tabs .nav-link.active {
            background-color: #f8f9fa;
            border-bottom-color: #f8f9fa;
            font-weight: bold;
            border-top: 3px solid #28a745;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
  <div class="container">
    <a class="navbar-brand font-weight-bold" href="dashboard.php">📊 Pusat Laporan</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menuAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuAdmin">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php">Input Sampah</a></li>
        <li class="nav-item"><a class="nav-link" href="tukar_hadiah.php">Tukar Poin</a></li>
        <li class="nav-item active"><a class="nav-link text-warning" href="laporan.php">Laporan</a></li>
      </ul>
      <a href="logout.php" class="btn btn-danger btn-sm ml-2">Keluar</a>
    </div>
  </div>
</nav>

<div class="container pb-5">

    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="text-secondary">Rekapitulasi Data Sekolah</h4>
            <p class="small text-muted">Data real-time dari seluruh aktivitas Bank Sampah & Kantin.</p>
        </div>
        <div class="col-md-4 text-right">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print"></i> Cetak Halaman Ini
            </button>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="sampah-tab" data-toggle="tab" href="#sampah" role="tab">
                ♻️ Riwayat Sampah
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tukar-tab" data-toggle="tab" href="#tukar" role="tab">
                🛍️ Riwayat Penukaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="makan-tab" data-toggle="tab" href="#makan" role="tab">
                🍔 Monitoring Kantin
            </a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        
        <div class="tab-pane fade show active" id="sampah" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-success mb-3">Laporan Sampah Masuk</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Jenis Sampah</th>
                                    <th>Berat</th>
                                    <th>Kondisi</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Join 3 Tabel: Transaksi -> Siswa -> Kategori
                                $qSampah = mysqli_query($koneksi, "SELECT t.*, s.nama_lengkap, s.kelas, k.nama_jenis 
                                                                   FROM transaksi t
                                                                   JOIN siswa s ON t.id_siswa = s.id_siswa
                                                                   JOIN kategori_sampah k ON t.id_kategori = k.id_kategori
                                                                   ORDER BY t.tanggal DESC LIMIT 50");
                                while($row = mysqli_fetch_array($qSampah)){
                                    // Warna Badge Kondisi
                                    $badge = "badge-secondary";
                                    if($row['tingkat_kebersihan'] == 'bersih') $badge = "badge-success";
                                    if($row['tingkat_kebersihan'] == 'kotor') $badge = "badge-danger";
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                                    <td>
                                        <strong><?= $row['nama_lengkap'] ?></strong><br>
                                        <small class="text-muted"><?= $row['kelas'] ?></small>
                                    </td>
                                    <td><?= $row['nama_jenis'] ?></td>
                                    <td><?= number_format($row['berat_jumlah']) ?> Gram</td>
                                    <td><span class="badge <?= $badge ?>"><?= ucfirst($row['tingkat_kebersihan']) ?></span></td>
                                    <td class="font-weight-bold text-success">+<?= number_format($row['poin_total']) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tukar" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-info mb-3">Laporan Barang Keluar (Hadiah)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>Tanggal Tukar</th>
                                    <th>Siswa Penukar</th>
                                    <th>Barang Diambil</th>
                                    <th>Poin Dipotong</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $qTukar = mysqli_query($koneksi, "SELECT r.*, s.nama_lengkap, s.kelas, h.nama_barang 
                                                                  FROM riwayat_penukaran r
                                                                  JOIN siswa s ON r.id_siswa = s.id_siswa
                                                                  JOIN katalog_hadiah h ON r.id_hadiah = h.id_hadiah
                                                                  ORDER BY r.tanggal DESC LIMIT 50");
                                while($r = mysqli_fetch_array($qTukar)){
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                                    <td><?= $r['nama_lengkap'] ?> <small>(<?= $r['kelas'] ?>)</small></td>
                                    <td><?= $r['nama_barang'] ?></td>
                                    <td class="text-danger font-weight-bold">-<?= number_format($r['poin_keluar']) ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="makan" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="text-warning mb-3">Laporan Monitoring Kantin</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th>Tanggal Cek</th>
                                    <th>Pedagang</th>
                                    <th>Menu Makanan</th>
                                    <th>Level Sehat</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Pastikan nama tabelnya 'menu_sehat' sesuai database Bapak
                                $qMakan = mysqli_query($koneksi, "SELECT * FROM menu_sehat ORDER BY id_menu DESC LIMIT 50");
                                while($m = mysqli_fetch_array($qMakan)){
                                    // Warna Level
                                    $bg_level = "";
                                    if($m['level_sehat'] <= 2) $bg_level = "bg-danger text-white"; // Bahaya
                                    elseif($m['level_sehat'] == 3) $bg_level = "bg-warning text-dark"; // Kurang
                                    else $bg_level = "bg-success text-white"; // Sehat
                                ?>
                                <tr>
                                    <td><?= isset($m['tanggal_cek']) ? $m['tanggal_cek'] : '-' ?></td>
                                    <td><?= $m['pedagang'] ?></td>
                                    <td><?= $m['nama_makanan'] ?></td>
                                    <td class="<?= $bg_level ?> text-center font-weight-bold">Level <?= $m['level_sehat'] ?></td>
                                    <td><?= $m['catatan'] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div> </div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>