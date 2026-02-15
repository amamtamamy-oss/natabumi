<?php
session_start();
// Cek Login Admin
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$pesan = "";

// --- PROSES VALIDASI ---
if(isset($_POST['validasi'])){
    $nama_makanan = $_POST['nama_makanan']; 
    $level_sehat  = $_POST['level_sehat'];  
    $catatan      = $_POST['catatan'];      
    $id_pedagang  = $_POST['id_pedagang']; // Tangkap ID Pedagang

    // 1. Update semua jurnal siswa (Level 0 -> Level Admin)
    // Kita update spesifik hanya untuk makanan dari pedagang ini
    $updateJurnal = mysqli_query($koneksi, "UPDATE jurnal_jajan SET level_sehat='$level_sehat' WHERE nama_makanan='$nama_makanan' AND id_pedagang='$id_pedagang'");

    // 2. Masukkan ke Database Resmi (menu_sehat)
    // Cek dulu biar gak dobel
    $cekMenu = mysqli_query($koneksi, "SELECT * FROM menu_sehat WHERE nama_makanan='$nama_makanan' AND id_pedagang='$id_pedagang'");
    if(mysqli_num_rows($cekMenu) == 0){
        // SIMPAN JUGA ID PEDAGANGNYA
        $insertMenu = mysqli_query($koneksi, "INSERT INTO menu_sehat (nama_makanan, level_sehat, catatan, id_pedagang) VALUES ('$nama_makanan', '$level_sehat', '$catatan', '$id_pedagang')");
    }

    if($updateJurnal){
        $pesan = "✅ Sukses! '$nama_makanan' berhasil dinilai & didaftarkan ke pedagang terkait.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Validasi Jajan Baru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 50px; border-radius: 0 0 30px 30px; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">⚖️ Validasi Jajan Siswa</h3>
        <p>Berikan penilaian gizi untuk makanan baru</p>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Kembali ke Dashboard</a>
    </div>

    <div class="container" style="margin-top: -40px;">
        
        <?php if($pesan){ echo "<div class='alert alert-success shadow-sm'>$pesan</div>"; } ?>

        <div class="card card-custom p-4 bg-white">
            <h5 class="font-weight-bold mb-4 text-secondary">Daftar Makanan Menunggu Review</h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>Nama Makanan</th>
                            <th>Pedagang / Kios</th> <th>Jumlah Laporan</th>
                            <th width="40%">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // QUERY DIGABUNG (JOIN) DENGAN TABEL PEDAGANG
                        // Dikelompokkan berdasarkan Nama Makanan DAN Nama Pedagang (Biar spesifik)
                        $query = "SELECT j.nama_makanan, COUNT(*) as jumlah, j.id_pedagang, p.nama_pedagang, p.nama_kios 
                                  FROM jurnal_jajan j 
                                  JOIN pedagang p ON j.id_pedagang = p.id_pedagang 
                                  WHERE j.level_sehat='0' 
                                  GROUP BY j.nama_makanan, j.id_pedagang";
                        
                        $qPending = mysqli_query($koneksi, $query);
                        
                        if(mysqli_num_rows($qPending) > 0){
                            while($row = mysqli_fetch_array($qPending)){
                        ?>
                        <tr>
                            <td class="font-weight-bold text-primary align-middle"><?= $row['nama_makanan'] ?></td>
                            
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark"><?= $row['nama_pedagang'] ?></div>
                                <small class="text-muted"><i class="fas fa-store mr-1"></i><?= $row['nama_kios'] ?></small>
                            </td>

                            <td class="align-middle"><span class="badge badge-warning"><?= $row['jumlah'] ?> Siswa</span></td>
                            
                            <td class="align-middle">
                                <form method="POST" class="form-inline">
                                    <input type="hidden" name="nama_makanan" value="<?= $row['nama_makanan'] ?>">
                                    <input type="hidden" name="id_pedagang" value="<?= $row['id_pedagang'] ?>"> <select name="level_sehat" class="form-control form-control-sm mr-2 mb-2 rounded-pill" style="width: 120px;" required>
                                        <option value="">- Nilai -</option>
                                        <option value="1">1 (Bahaya)</option>
                                        <option value="2">2 (Kurang)</option>
                                        <option value="3">3 (Biasa)</option>
                                        <option value="4">4 (Sehat)</option>
                                        <option value="5">5 (Super)</option>
                                    </select>
                                    
                                    <input type="text" name="catatan" class="form-control form-control-sm mr-2 mb-2 rounded-pill" placeholder="Catatan..." style="width: 150px;">

                                    <button type="submit" name="validasi" class="btn btn-success btn-sm rounded-circle mb-2" title="Validasi">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-4 text-muted'><i>Hore! Tidak ada antrian validasi. Semua aman.</i></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>