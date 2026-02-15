<?php
include 'koneksi.php';

$id_pedagang  = $_POST['id_pedagang'];
$nama_makanan = $_POST['nama_makanan'];
$level_sehat  = $_POST['level_sehat'];
$catatan      = $_POST['catatan'];
$tanggal      = date('Y-m-d');

$query = "INSERT INTO menu_sehat (id_pedagang, nama_makanan, level_sehat, catatan, tanggal_cek)
          VALUES ('$id_pedagang', '$nama_makanan', '$level_sehat', '$catatan', '$tanggal')";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
            alert('Data makanan berhasil dicatat!'); 
            window.location='input_makanan.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>