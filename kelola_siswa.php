<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// --- LOGIKA FILTER KELAS (UNTUK CETAK MASSAL) ---
$kelas_pilih = "";
if(isset($_POST['filter_kelas'])){
    $kelas_pilih = $_POST['kelas'];
    // Ambil siswa sesuai kelas
    $qSiswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE kelas='$kelas_pilih' ORDER BY nama_lengkap ASC");
} else {
    // Default tampilkan semua
    $qSiswa = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY id_siswa DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Siswa & Cetak Kartu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
  <div class="container">
    <a class="navbar-brand font-weight-bold" href="dashboard.php"><i class="fas fa-users mr-2"></i>Data Siswa</a>
    <div class="navbar-nav ml-auto">
      <a class="nav-link btn btn-secondary btn-sm text-white" href="dashboard.php">Kembali ke Dashboard</a>
    </div>
  </div>
</nav>

<div class="container">
    
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-body py-3">
            <form method="POST" class="form-inline justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <label class="font-weight-bold mr-3 text-secondary">🖨️ Filter Cetak Massal:</label>
                    <select name="kelas" class="form-control mr-2" required>
                        <option value="">-- Pilih Kelas --</option>
                        <option value="X IPA 1" <?= ($kelas_pilih == 'X IPA 1')?'selected':'' ?>>X IPA 1</option>
                        <option value="X IPA 2" <?= ($kelas_pilih == 'X IPA 2')?'selected':'' ?>>X IPA 2</option>
                        <option value="X IPS 1" <?= ($kelas_pilih == 'X IPS 1')?'selected':'' ?>>X IPS 1</option>
                        <option value="X IPS 2" <?= ($kelas_pilih == 'X IPS 2')?'selected':'' ?>>X IPS 2</option>
                        </select>
                    <button type="submit" name="filter_kelas" class="btn btn-primary shadow-sm"><i class="fas fa-search"></i> Tampilkan</button>
                </div>
                
                <?php if($kelas_pilih != ""){ ?>
                    <a href="cetak_kartu.php?kelas=<?= $kelas_pilih ?>" target="_blank" class="btn btn-warning font-weight-bold text-dark shadow">
                        <i class="fas fa-print mr-2"></i> CETAK SEMUA KELAS <?= $kelas_pilih ?>
                    </a>
                <?php } ?>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa <?= ($kelas_pilih) ? "Kelas $kelas_pilih" : "" ?></h6>
            
            <a href="tambah_siswa.php" class="btn btn-success btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-user-plus mr-1"></i> Tambah Manual
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>NIS</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>No HP (WA)</th>
                            <th class="text-center">Kartu & WA</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = mysqli_fetch_array($qSiswa)){ 
                            // --- LOGIKA NOMOR WA ---
                            $hp_bersih = preg_replace('/[^0-9]/', '', $d['no_hp']);
                            if(substr($hp_bersih, 0, 1) == '0'){
                                $hp_ready = '62'.substr($hp_bersih, 1);
                            } else {
                                $hp_ready = $hp_bersih;
                            }

                            // Link Login Siswa
                            $link_kartu = "http://localhost/banksampah_ma/login_siswa.php"; 
                            $pesan = "Assalamualaikum $d[nama_lengkap]. Ini kartu digital Bank Sampah kamu. Login NIS: *$d[nis]*. Akses di sini: $link_kartu";
                            $link_wa = "https://wa.me/$hp_ready?text=".urlencode($pesan);
                        ?>
                        <tr>
                            <td><?= $d['nis'] ?></td>
                            <td class="font-weight-bold text-dark"><?= $d['nama_lengkap'] ?></td>
                            <td><span class="badge badge-info"><?= $d['kelas'] ?></span></td>
                            <td><?= $d['no_hp'] ?></td>
                            
                            <td class="text-center">
                                <a href="<?= $link_wa ?>" target="_blank" class="btn btn-sm btn-success mb-1" title="Kirim Kartu ke WA">
                                    <i class="fab fa-whatsapp"></i> Kirim
                                </a>
                                <a href="cetak_kartu.php?id_siswa=<?= $d['id_siswa'] ?>" target="_blank" class="btn btn-sm btn-warning mb-1" title="Cetak Kartu">
                                    <i class="fas fa-print"></i> Cetak
                                </a>
                            </td>

                            <td class="text-center">
                                <a href="edit_siswa.php?id=<?= $d['id_siswa'] ?>" class="btn btn-sm btn-info mb-1"><i class="fas fa-edit"></i></a>
                                <a href="hapus_siswa.php?id=<?= $d['id_siswa'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Yakin hapus siswa ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>