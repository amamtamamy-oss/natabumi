<?php
session_start();
include 'koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM pedagang WHERE id_pedagang='$id'"));

if(isset($_POST['update'])){
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    mysqli_query($koneksi, "UPDATE pedagang SET nama_pedagang='$nama', lokasi='$lokasi' WHERE id_pedagang='$id'");
    header("location:kelola_pedagang.php");
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<div class="container mt-5 col-md-4">
    <div class="card shadow">
        <div class="card-body">
            <h4>Edit Pedagang</h4>
            <form method="POST">
                <input type="text" name="nama" class="form-control mb-2" value="<?= $data['nama_pedagang'] ?>">
                <select name="lokasi" class="form-control mb-2">
                    <option value="Kantin Dalam" <?= ($data['lokasi']=='Kantin Dalam')?'selected':'' ?>>Kantin Dalam</option>
                    <option value="Luar Pagar" <?= ($data['lokasi']=='Luar Pagar')?'selected':'' ?>>Luar Pagar</option>
                </select>
                <button type="submit" name="update" class="btn btn-warning btn-block">Update Data</button>
            </form>
        </div>
    </div>
</div>