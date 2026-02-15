<?php
session_start();
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM kategori_sampah WHERE id_kategori='$id'"));

if(isset($_POST['update'])){
    $pengepul = $_POST['harga_pengepul'];
    
    // RUMUS OTOMATIS LAGI
    $siswa = $pengepul * 0.5; 
    
    mysqli_query($koneksi, "UPDATE kategori_sampah SET harga_pengepul='$pengepul', harga_poin_dasar='$siswa' WHERE id_kategori='$id'");
    header("location:kelola_sampah.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Harga</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-body">
            <h4>Update Harga Pengepul</h4>
            <p>Harga siswa akan otomatis disesuaikan 50%.</p>
            <form method="POST">
                <div class="form-group">
                    <label>Harga Pengepul Terbaru</label>
                    <input type="number" name="harga_pengepul" class="form-control" value="<?= $data['harga_pengepul'] ?>" required>
                </div>
                <button type="submit" name="update" class="btn btn-warning btn-block">Simpan Perubahan</button>
                <a href="kelola_sampah.php" class="btn btn-secondary btn-block">Batal</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>