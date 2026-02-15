<?php
include 'koneksi.php';

$id_siswa  = $_POST['id_siswa'];
$id_hadiah = $_POST['id_hadiah'];

// 1. Ambil Data Harga & Stok Barang
$qBarang = mysqli_query($koneksi, "SELECT * FROM katalog_hadiah WHERE id_hadiah = '$id_hadiah'");
$barang  = mysqli_fetch_array($qBarang);

// 2. Ambil Data Poin Siswa
$qSiswa  = mysqli_query($koneksi, "SELECT saldo_poin FROM siswa WHERE id_siswa = '$id_siswa'");
$siswa   = mysqli_fetch_array($qSiswa);

// 3. Validasi (Cegah Curang)
if ($siswa['saldo_poin'] >= $barang['harga_poin'] && $barang['stok'] > 0) {
    
    // A. Kurangi Poin Siswa
    mysqli_query($koneksi, "UPDATE siswa SET saldo_poin = saldo_poin - $barang[harga_poin] WHERE id_siswa = '$id_siswa'");
    
    // B. Kurangi Stok Barang
    mysqli_query($koneksi, "UPDATE katalog_hadiah SET stok = stok - 1 WHERE id_hadiah = '$id_hadiah'");
    
    // C. Catat Riwayat
    mysqli_query($koneksi, "INSERT INTO riwayat_tukar (id_siswa, id_hadiah) VALUES ('$id_siswa', '$id_hadiah')");
    
    echo "<script>
            alert('Berhasil! Silakan ambil hadiahnya.');
            window.location='tukar_poin.php?id_siswa=$id_siswa'; 
          </script>";
} else {
    echo "<script>alert('Gagal! Poin tidak cukup atau stok habis.'); window.location='tukar_poin.php';</script>";
}
?>