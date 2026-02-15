<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// --- PROSES TAMBAH PRODUK ---
if(isset($_POST['simpan'])){
    $nama  = $_POST['nama_produk'];
    $harga = $_POST['harga_poin'];
    $stok  = $_POST['stok'];
    
    $simpan = mysqli_query($koneksi, "INSERT INTO produk (nama_produk, harga_poin, stok) VALUES ('$nama', '$harga', '$stok')");
    if($simpan){ 
        echo "<script>alert('✅ Produk Berhasil Ditambahkan'); window.location='tukar_hadiah.php';</script>"; 
    }
}

// --- PROSES HAPUS PRODUK ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM produk WHERE id_produk='$id'");
    header("location:kelola_produk.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Tambah Barang Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama Barang / Produk</label>
                            <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Pulpen Standard" required>
                        </div>
                        <div class="form-group">
                            <label>Harga (Poin)</label>
                            <input type="number" name="harga_poin" class="form-control" placeholder="Contoh: 2000" required>
                        </div>
                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="number" name="stok" class="form-control" value="50" required>
                        </div>
                        <hr>
                        <button type="submit" name="simpan" class="btn btn-primary btn-block font-weight-bold">SIMPAN PRODUK</button>
                        <a href="tukar_hadiah.php" class="btn btn-secondary btn-block">Kembali ke Kasir</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Daftar Produk di Koperasi</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = mysqli_query($koneksi, "SELECT * FROM produk ORDER BY nama_produk ASC");
                            while($d = mysqli_fetch_array($q)){
                            ?>
                            <tr>
                                <td><?= $d['nama_produk'] ?></td>
                                <td class="font-weight-bold text-success"><?= number_format($d['harga_poin']) ?></td>
                                <td><?= $d['stok'] ?></td>
                                <td>
                                    <a href="kelola_produk.php?hapus=<?= $d['id_produk'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
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