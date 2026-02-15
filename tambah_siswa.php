<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// PROSES SIMPAN
if(isset($_POST['simpan'])){
    $nis      = $_POST['nis'];
    $nama     = $_POST['nama_lengkap'];
    $kelas    = $_POST['kelas'];
    $hp       = $_POST['no_hp'];
    $password = md5($nis); // Password default = NIS/No HP
    
    // Cek apakah ID/NIS sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('❌ GAGAL! ID/NIS $nis sudah terdaftar.');</script>";
    } else {
        $simpan = mysqli_query($koneksi, "INSERT INTO siswa (nis, nama_lengkap, kelas, no_hp, password, saldo_poin) VALUES ('$nis', '$nama', '$kelas', '$hp', '$password', 0)");
        
        if($simpan){
            echo "<script>alert('✅ Berhasil! Nasabah baru ditambahkan.'); window.location='kelola_siswa.php';</script>";
        } else {
            echo "<script>alert('❌ Gagal menyimpan data.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Nasabah Baru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Tambah Nasabah (Siswa/Umum)</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> <b>Tips untuk Non-Siswa:</b><br>
                        Gunakan <b>Nomor HP</b> sebagai pengganti NIS agar mudah diingat saat login.
                    </div>

                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">ID Login / NIS / No. HP</label>
                            <input type="number" name="nis" class="form-control" placeholder="Contoh: 08123456789" required>
                            <small class="text-muted">ID ini digunakan untuk Login & Password Awal.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Nasabah / Lembaga" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Kategori / Kelas</label>
                            <select name="kelas" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                
                                <optgroup label="--- KATEGORI UMUM ---">
                                    <option value="GURU/STAF">👨‍🏫 GURU / STAF</option>
                                    <option value="WALI MURID">👨‍👩‍👧 WALI MURID</option>
                                    <option value="UMUM">🌍 MASYARAKAT UMUM</option>
                                    <option value="MITRA">🏢 LEMBAGA / MITRA</option>
                                </optgroup>

                                <optgroup label="--- KELAS SISWA ---">
                                    <option value="X IPA 1">X IPA 1</option>
                                    <option value="X IPA 2">X IPA 2</option>
                                    <option value="X IPS 1">X IPS 1</option>
                                    <option value="X IPS 2">X IPS 2</option>
                                    <option value="XI IPA 1">XI IPA 1</option>
                                    <option value="XI IPA 2">XI IPA 2</option>
                                    <option value="XI IPS 1">XI IPS 1</option>
                                    <option value="XI IPS 2">XI IPS 2</option>
                                    </optgroup>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">No HP / WhatsApp (Aktif)</label>
                            <input type="number" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                            <small class="text-muted">Untuk notifikasi & kartu digital.</small>
                        </div>
                        
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <a href="kelola_siswa.php" class="btn btn-secondary btn-block font-weight-bold">Batal</a>
                            </div>
                            <div class="col-6">
                                <button type="submit" name="simpan" class="btn btn-success btn-block font-weight-bold">SIMPAN</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>