<?php
session_start();
include 'koneksi.php';

// Ambil ID dari link
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM kategori_sampah WHERE id_kategori='$id'");
$data = mysqli_fetch_array($query);

// PROSES UPDATE
if(isset($_POST['update'])){
    $nama  = $_POST['nama_jenis'];
    $harga = $_POST['harga'];
    
    $update = mysqli_query($koneksi, "UPDATE kategori_sampah SET nama_jenis='$nama', harga_poin_dasar='$harga' WHERE id_kategori='$id'");
    
    if($update){
        echo "<script>alert('Harga berhasil diubah!'); window.location='kelola_sampah.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Harga Sampah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4>✏️ Edit Harga Sampah</h4>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label>Nama Jenis Sampah</label>
                            <input type="text" name="nama_jenis" class="form-control" value="<?= $data['nama_jenis'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Harga Poin Terbaru (Per Kg)</label>
                            <input type="number" name="harga" class="form-control" value="<?= $data['harga_poin_dasar'] ?>" required>
                            <small class="text-muted">Sesuaikan dengan harga pasar terbaru.</small>
                        </div>
                        
                        <a href="kelola_sampah.php" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>