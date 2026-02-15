<?php
include 'koneksi.php';

$id_siswa = $_POST['id_siswa'];
$id_menu  = $_POST['id_menu'];

// Simpan data
$query = "INSERT INTO riwayat_jajan (id_siswa, id_menu) VALUES ('$id_siswa', '$id_menu')";

if (mysqli_query($koneksi, $query)) {
    // Tampilkan Alert Sukses & Motivasi
    echo "<script>
            alert('Terima kasih sudah jujur! Data jajanmu tercatat.'); 
            window.location='lapor_jajan.php';
          </script>";
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>