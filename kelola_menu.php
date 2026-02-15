<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// --- HAPUS MENU ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM menu_sehat WHERE id_menu='$id'");
    header("location:kelola_menu.php");
}

// --- UPDATE/EDIT (Simpel Logic: Redirect ke form edit atau pakai modal, disini saya pakai list dulu) ---
// Untuk kesederhanaan, kita pakai fitur Hapus & Input Baru atau Edit via phpMyAdmin untuk detailnya
// Tapi tampilan daftarnya kita buat bagus.
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Menu Sehat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 50px; border-radius: 0 0 30px 30px; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .badge-level-1, .badge-level-2 { background-color: #dc3545; color: white; } /* Merah */
        .badge-level-3 { background-color: #ffc107; color: black; } /* Kuning */
        .badge-level-4, .badge-level-5 { background-color: #28a745; color: white; } /* Hijau */
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">🥗 Database Menu Sehat</h3>
        <p>Monitoring kandungan gizi makanan kantin</p>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Kembali ke Dashboard</a>
    </div>

    <div class="container" style="margin-top: -40px;">
        <div class="card card-custom p-4 bg-white">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-weight-bold text-secondary">Daftar Menu & Pedagang</h5>
                <button class="btn btn-success btn-sm rounded-pill shadow-sm"><i class="fas fa-plus mr-1"></i> Tambah Menu</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Makanan</th>
                            <th>Pedagang</th>
                            <th>Level Sehat</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Join dengan tabel pedagang agar nama pedagang muncul
                        $q = mysqli_query($koneksi, "SELECT m.*, p.nama_pedagang, p.nama_kios FROM menu_sehat m LEFT JOIN pedagang p ON m.id_pedagang=p.id_pedagang ORDER BY m.nama_makanan ASC");
                        $no = 1;
                        while($d = mysqli_fetch_array($q)){
                            $star = str_repeat("⭐", $d['level_sehat']);
                            $badgeClass = "badge-level-" . $d['level_sehat'];
                            
                            // Cek Pedagang
                            $pedagang_info = ($d['nama_pedagang']) ? "$d[nama_pedagang] <br><small class='text-muted'>$d[nama_kios]</small>" : "<span class='text-muted font-italic'>- Belum diset -</span>";
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="font-weight-bold"><?= $d['nama_makanan'] ?></td>
                            <td><?= $pedagang_info ?></td>
                            <td>
                                <span class="badge badge-pill <?= $badgeClass ?> p-2">
                                    Level <?= $d['level_sehat'] ?>
                                </span>
                                <small class="d-block mt-1"><?= $star ?></small>
                            </td>
                            <td><small><?= $d['catatan'] ?></small></td>
                            <td>
                                <a href="kelola_menu.php?hapus=<?= $d['id_menu'] ?>" class="btn btn-outline-danger btn-sm rounded-circle" onclick="return confirm('Hapus menu ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>