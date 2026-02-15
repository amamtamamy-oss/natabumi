<?php
include 'koneksi.php';
mysqli_query($koneksi, "DELETE FROM pedagang WHERE id_pedagang='$_GET[id]'");
header("location:kelola_pedagang.php");
?>