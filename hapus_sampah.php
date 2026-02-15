<?php
include 'koneksi.php';
$id = $_GET['id'];

$hapus = mysqli_query($koneksi, "DELETE FROM kategori_sampah WHERE id_kategori='$id'");

if($hapus){
    header("location:kelola_sampah.php");
}
?>