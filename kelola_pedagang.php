<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$pesan = "";

// --- 1. TAMBAH PEDAGANG ---
if(isset($_POST['simpan'])){
    $nama = $_POST['nama_pedagang'];
    $kios = $_POST['nama_kios'];
    $hp   = $_POST['no_hp'];

    $simpan = mysqli_query($koneksi, "INSERT INTO pedagang (nama_pedagang, nama_kios, no_hp, status) VALUES ('$nama', '$kios', '$hp', 'aktif')");
    if($simpan){
        $pesan = "✅ Berhasil menambahkan pedagang resmi!";
    }
}

// --- 2. AKTIFKAN PEDAGANG (VALIDASI) ---
if(isset($_GET['aktifkan'])){
    $id = $_GET['aktifkan'];
    mysqli_query($koneksi, "UPDATE pedagang SET status='aktif' WHERE id_pedagang='$id'");
    $pesan = "✅ Pedagang berhasil diresmikan!";
}

// --- 3. HAPUS PEDAGANG ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pedagang WHERE id_pedagang='$id'");
    header("location:kelola_pedagang.php"); // Refresh biar hilang
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pedagang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 50px; border-radius: 0 0 30px 30px; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .bg-pending { background-color: #fff3cd; } /* Warna Kuning untuk Pending */
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">🏪 Data Mitra Kantin</h3>
        <p>Kelola data pedagang & validasi inputan siswa</p>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Kembali ke Dashboard</a>
    </div>

    <div class="container" style="margin-top: -40px;">
        
        <?php if($pesan){ echo "<div class='alert alert-success shadow-sm'>$pesan</div>"; } ?>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card card-custom p-4 bg-white">
                    <h6 class="font-weight-bold mb-3 text-secondary">Tambah Pedagang Resmi</h6>
                    <form method="POST">
                        <div class="form-group">
                            <label>Nama Pedagang</label>
                            <input type="text" name="nama_pedagang" class="form-control rounded-pill" placeholder="Cth: Bu Siti" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Kios/Warung</label>
                            <input type="text" name="nama_kios" class="form-control rounded-pill" placeholder="Cth: Kantin Pojok" required>
                        </div>
                        <div class="form-group">
                            <label>No. HP (WA)</label>
                            <input type="text" name="no_hp" class="form-control rounded-pill" placeholder="0812..." required>
                        </div>
                        <button type="submit" name="simpan" class="btn btn-success btn-block rounded-pill font-weight-bold shadow">
                            SIMPAN <i class="fas fa-save ml-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-custom p-4 bg-white">
                    <h6 class="font-weight-bold mb-3 text-secondary">Daftar Pedagang</h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Kios</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Urutkan: Yang pending di atas, baru yang aktif
                                $q = mysqli_query($koneksi, "SELECT * FROM pedagang ORDER BY status DESC, id_pedagang DESC");
                                while($d = mysqli_fetch_array($q)){
                                    // Tentukan warna baris
                                    $rowClass = ($d['status'] == 'pending') ? 'bg-pending' : '';
                                    $badge    = ($d['status'] == 'pending') ? '<span class="badge badge-warning">Baru (Cek!)</span>' : '<span class="badge badge-success">Resmi</span>';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="font-weight-bold"><?= $d['nama_pedagang'] ?></td>
                                    <td><?= $d['nama_kios'] ?></td>
                                    <td><?= $badge ?></td>
                                    <td>
                                        <?php if($d['status'] == 'pending'){ ?>
                                            <a href="kelola_pedagang.php?aktifkan=<?= $d['id_pedagang'] ?>" class="btn btn-success btn-sm rounded-circle" title="Resmikan Pedagang Ini">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php } ?>
                                        
                                        <a href="kelola_pedagang.php?hapus=<?= $d['id_pedagang'] ?>" class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Hapus pedagang ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>