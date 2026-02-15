<?php
include 'koneksi.php';
mysqli_query($koneksi, "DELETE FROM menu_sehat WHERE id_menu='$_GET[id]'");
header("location:kelola_menu.php");
?>