<?php
session_start();
session_destroy(); // Hapus semua sesi login
header("location:index.php"); // Lempar kembali ke Halaman Depan (Publik)
?>