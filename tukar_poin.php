<?php include 'koneksi.php'; ?>
<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
}
include 'koneksi.php'; 
// ... kode selanjutnya biarkan seperti biasa
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tukar Poin - Eco School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">🎁 Katalog Penukaran Poin</a>
  </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-body bg-white">
                    <form action="" method="GET" class="form-inline">
                        <label class="mr-2">Pilih Siswa:</label>
                        <select name="id_siswa" class="form-control mr-2" required>
                            <option value="">-- Siapa yang mau tukar? --</option>
                            <?php
                            $qSiswa = mysqli_query($koneksi, "SELECT * FROM siswa");
                            while ($s = mysqli_fetch_array($qSiswa)) {
                                // Fitur agar nama yang dipilih tetap terpilih setelah submit
                                $selected = (isset($_GET['id_siswa']) && $_GET['id_siswa'] == $s['id_siswa']) ? 'selected' : '';
                                echo "<option value='$s[id_siswa]' $selected>$s[nama_lengkap] (Sisa Poin: $s[saldo_poin])</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Cek Saldo</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        // Jika Siswa Sudah Dipilih, Tampilkan Katalog
        if (isset($_GET['id_siswa'])) {
            $id_siswa = $_GET['id_siswa'];
            // Ambil data saldo siswa terbaru
            $cekSiswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
            $dataSiswa = mysqli_fetch_array($cekSiswa);
        ?>
        
        <div class="col-md-12 mb-3">
            <div class="alert alert-info">
                Halo <strong><?= $dataSiswa['nama_lengkap'] ?></strong>! Saldo Poin kamu: <strong><?= $dataSiswa['saldo_poin'] ?></strong>
            </div>
        </div>

        <?php
        $qBarang = mysqli_query($koneksi, "SELECT * FROM katalog_hadiah");
        while ($b = mysqli_fetch_array($qBarang)) {
            // Logika Tombol: Jika Poin Cukup & Stok Ada -> Tombol Aktif
            $bisaBeli = ($dataSiswa['saldo_poin'] >= $b['harga_poin'] && $b['stok'] > 0);
            $btnClass = $bisaBeli ? "btn-success" : "btn-secondary disabled";
            $status   = $bisaBeli ? "Tukar Sekarang" : "Poin Kurang / Stok Habis";
        ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-body text-center">
                    <h4>🎁</h4>
                    <h5 class="card-title"><?= $b['nama_barang'] ?></h5>
                    <h3 class="text-warning font-weight-bold"><?= $b['harga_poin'] ?> Poin</h3>
                    <p class="text-muted">Sisa Stok: <?= $b['stok'] ?></p>
                    
                    <?php if($bisaBeli): ?>
                    <form action="proses_tukar.php" method="POST" onsubmit="return confirm('Yakin tukar poin?')">
                        <input type="hidden" name="id_siswa" value="<?= $id_siswa ?>">
                        <input type="hidden" name="id_hadiah" value="<?= $b['id_hadiah'] ?>">
                        <button type="submit" class="btn btn-success btn-block">Tukar Sekarang</button>
                    </form>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-block" disabled><?= $status ?></button>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
        <?php } // end while ?>

        <?php } // end if isset ?>

    </div>
</div>
</body>
</html>