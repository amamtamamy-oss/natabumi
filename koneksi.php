<?php
$host = "localhost";
$user = "root";
$pass = ""; // Di XAMPP defaultnya kosong
$db   = "db_banksampah";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Gagal konek ke database: " . mysqli_connect_error());
}
?>