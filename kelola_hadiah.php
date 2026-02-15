<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// --- PROSES SIMPAN HADIAH BARU ---
if(isset($_POST['simpan'])){
    $nama   = $_POST['nama'];
    $beli   = $_POST['beli']; // Harga Kulakan
    $poin   = $_POST['poin']; // Harga Jual ke Siswa
    $stok   = $_POST['stok'];
    $satuan = $_POST['satuan'];

    $simpan = mysqli_query($koneksi, "INSERT INTO katalog_hadiah (nama_barang, harga_beli, harga_poin, stok, satuan) VALUES ('$nama', '$beli', '$poin', '$stok', '$satuan')");
    
    if($simpan){
        echo "<script>alert('Hadiah berhasil ditambahkan!'); window.location='kelola_hadiah.php';</script>";
    }
}

// --- PROSES HAPUS HADIAH ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM katalog_hadiah WHERE id_hadiah='$id'");
    echo "<script>alert('Hadiah dihapus!'); window.location='kelola_hadiah.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Katalog Hadiah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">🎁 Admin Hadiah & Koperasi</a>
    <div class="navbar-nav ml-auto">
      <a class="nav-link btn btn-secondary btn-sm text-white" href="dashboard.php">Kembali</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row">
        
        <div class="col-md-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">Tambah Barang Baru</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Pulpen Standard" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="text-danger small font-weight-bold">Harga Modal (Rp)</label>
                                    <input type="number" name="beli" class="form-control" placeholder="1500" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="text-success small font-weight-bold">Jual (Poin)</label>
                                    <input type="number" name="poin" class="form-control" placeholder="2000" required>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted mb-3 d-block"><i>*Pastikan Harga Poin > Modal agar untung.</i></small>

                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="number" name="stok" class="form-control" placeholder="Contoh: 10" required>
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <select name="satuan" class="form-control">
                                <option value="Pcs">Pcs / Buah</option>
                                <option value="Bungkus">Bungkus</option>
                                <option value="Voucher">Voucher</option>
                                <option value="Paket">Paket</option>
                            </select>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-primary btn-block">Simpan ke Katalog</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="m-0 font-weight-bold text-dark">Daftar Barang & Analisa Profit</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Modal (Rp)</th>
                                    <th>Jual (Poin)</th>
                                    <th>Profit/Item</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tampil = mysqli_query($koneksi, "SELECT * FROM katalog_hadiah ORDER BY nama_barang ASC");
                                while($d = mysqli_fetch_array($tampil)){
                                    // Hitung Keuntungan
                                    $profit = $d['harga_poin'] - $d['harga_beli'];
                                    
                                    // Tentukan Warna Profit (Hijau=Untung, Merah=Rugi)
                                    if($profit > 0){
                                        $warna_profit = "text-success font-weight-bold";
                                        $tanda = "+";
                                    } elseif($profit == 0) {
                                        $warna_profit = "text-warning font-weight-bold";
                                        $tanda = "";
                                    } else {
                                        $warna_profit = "text-danger font-weight-bold";
                                        $tanda = "";
                                    }
                                ?>
                                <tr>
                                    <td class="text-left"><?= $d['nama_barang'] ?></td>
                                    <td>Rp <?= number_format($d['harga_beli']) ?></td>
                                    <td><?= number_format($d['harga_poin']) ?></td>
                                    
                                    <td class="<?= $warna_profit ?>">
                                        <?= $tanda . number_format($profit) ?>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                        if($d['stok'] <= 0){
                                            echo "<span class='badge badge-danger'>Habis</span>";
                                        } else {
                                            echo $d['stok'];
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="kelola_hadiah.php?hapus=<?= $d['id_hadiah'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?')">🗑️</a>
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
</div>

</body>
</html>