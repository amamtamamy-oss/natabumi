<?php
session_start(); // Mulai sesi
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Cek di database
$login = mysqli_query($koneksi, "SELECT * FROM petugas WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($login);

if($cek > 0){
    // Jika ketemu, simpan data sesi
    $data = mysqli_fetch_assoc($login);
    $_SESSION['username'] = $username;
    $_SESSION['status'] = "login";
    
    // Alihkan ke Dashboard
    header("location:dashboard.php");
}else{
    // Jika salah, kembalikan ke login
    header("location:login.php?pesan=gagal");
}
?>