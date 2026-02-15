<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php");
}
include 'koneksi.php';

// --- PROSES TAMBAH SAMPAH BARU (OTOMATIS) ---
if(isset($_POST['simpan_baru'])){
    $nama  = $_POST['nama_jenis'];
    $pengepul = $_POST['harga_pengepul']; // Inputan Admin
    
    // RUMUS KEUNTUNGAN 50%
    // Harga Siswa = 50% dari Harga Pengepul
    $siswa = $pengepul * 0.5; 
    
    $simpan = mysqli_query($koneksi, "INSERT INTO kategori_sampah (nama_jenis, harga_pengepul, harga_poin_dasar) VALUES ('$nama', '$pengepul', '$siswa')");
    
    if($simpan){
        echo "<script>alert('Berhasil! Harga siswa otomatis diset: Rp $siswa'); window.location='kelola_sampah.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Atur Harga & Profit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">🏫 Admin Eco-Health</a>
    <div class="navbar-nav ml-auto">
      <a class="nav-link" href="dashboard.php">Dashboard</a>
      <a class="nav-link btn btn-danger btn-sm text-white ml-2" href="logout.php">Keluar</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="alert alert-info">
        <strong>Info Profit:</strong> Sistem ini otomatis mengatur harga beli ke siswa sebesar 
        <strong>50%</strong> dari harga jual ke pengepul.
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Jenis Sampah</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Jenis Sampah</label>
                            <input type="text" name="nama_jenis" class="form-control" placeholder="Contoh: Tembaga" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Jual ke Pengepul (Rp)</label>
                            <input type="number" name="harga_pengepul" class="form-control" placeholder="Misal: 4000" required>
                            <small class="text-danger">Masukkan harga asli dari pengepul.</small>
                        </div>
                        <button type="submit" name="simpan_baru" class="btn btn-primary btn-block">Simpan & Hitung Otomatis</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tabel Harga & Margin Keuntungan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Jenis Sampah</th>
                                <th>Harga Pengepul</th>
                                <th>Harga Siswa (50%)</th>
                                <th>Profit Sekolah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tampil = mysqli_query($koneksi, "SELECT * FROM kategori_sampah ORDER BY nama_jenis ASC");
                            while($d = mysqli_fetch_array($tampil)){
                                // Hitung profit per kg untuk ditampilkan
                                $profit = $d['harga_pengepul'] - $d['harga_poin_dasar'];
                            ?>
                            <tr>
                                <td><?= $d['nama_jenis'] ?></td>
                                <td>Rp <?= number_format($d['harga_pengepul']) ?></td>
                                <td class="font-weight-bold text-success">Rp <?= number_format($d['harga_poin_dasar']) ?></td>
                                <td class="text-primary">+ Rp <?= number_format($profit) ?>/kg</td>
                                <td>
                                    <a href="edit_sampah_auto.php?id=<?= $d['id_kategori'] ?>" class="btn btn-sm btn-warning">Update Harga</a>
                                    <a href="hapus_sampah.php?id=<?= $d['id_kategori'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>