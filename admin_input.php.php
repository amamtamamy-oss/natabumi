<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$pesan = "";

// --- PROSES SIMPAN TRANSAKSI ---
if(isset($_POST['simpan'])){
    $id_siswa     = $_POST['id_siswa'];
    $id_kategori  = $_POST['id_kategori'];
    $berat        = $_POST['berat'];
    $tanggal      = date('Y-m-d H:i:s');

    // Ambil info kategori (harga poin)
    $qK = mysqli_query($koneksi, "SELECT * FROM kategori_sampah WHERE id_kategori='$id_kategori'");
    $dK = mysqli_fetch_array($qK);
    $harga_poin = $dK['harga_poin'];

    // Hitung Poin (Berat x Harga)
    // Asumsi: Berat dalam Gram. Jika Kg, sesuaikan logikanya.
    // Disini saya pakai logika: Berat (gram) * Harga per gram (atau per kg dibagi 1000)
    // Sederhananya: Poin = Berat * Harga
    $total_poin = $berat * $harga_poin;

    // 1. Simpan Transaksi
    $simpan = mysqli_query($koneksi, "INSERT INTO transaksi (id_siswa, id_kategori, berat_jumlah, poin_total, tanggal_transaksi) VALUES ('$id_siswa', '$id_kategori', '$berat', '$total_poin', '$tanggal')");

    // 2. Tambah Saldo Siswa
    $updateSiswa = mysqli_query($koneksi, "UPDATE siswa SET saldo_poin = saldo_poin + $total_poin WHERE id_siswa='$id_siswa'");

    if($simpan && $updateSiswa){
        $pesan = "✅ Sukses! Saldo bertambah +$total_poin Poin.";
    } else {
        $pesan = "❌ Gagal menyimpan data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Setoran Sampah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 50px; border-radius: 0 0 30px 30px; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        /* Select2 Custom Style */
        .select2-container .select2-selection--single { height: 45px !important; border-radius: 50px !important; display: flex; align-items: center; padding-left: 10px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 45px !important; right: 15px !important; }
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">♻️ Input Setoran Sampah</h3>
        <p>Catat timbangan sampah siswa disini</p>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4">Kembali ke Dashboard</a>
    </div>

    <div class="container" style="margin-top: -40px;">
        <div class="row justify-content-center">
            
            <div class="col-md-5 mb-4">
                <div class="card card-custom p-4 bg-white">
                    <?php if($pesan){ echo "<div class='alert alert-success shadow-sm'>$pesan</div>"; } ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Pilih Siswa</label>
                            <select name="id_siswa" class="form-control select2-cari" style="width: 100%;" required>
                                <option value="">-- Cari Nama / NIS --</option>
                                <?php
                                $qS = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY kelas ASC, nama_lengkap ASC");
                                while($s = mysqli_fetch_array($qS)){
                                    echo "<option value='$s[id_siswa]'>$s[kelas] - $s[nama_lengkap] (Saldo: $s[saldo_poin])</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Jenis Sampah</label>
                            <select name="id_kategori" class="form-control rounded-pill" style="height: 45px;" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $qK = mysqli_query($koneksi, "SELECT * FROM kategori_sampah");
                                while($k = mysqli_fetch_array($qK)){
                                    echo "<option value='$k[id_kategori]'>$k[nama_jenis] (@$k[harga_poin] Poin)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Berat (Satuan Database)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="berat" class="form-control rounded-pill-left" placeholder="0" required>
                                <div class="input-group-append">
                                    <span class="input-group-text rounded-pill-right bg-success text-white">Kg/Gram</span>
                                </div>
                            </div>
                            <small class="text-muted">*Sesuaikan dengan satuan di database (Kg atau Gram)</small>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success btn-block rounded-pill font-weight-bold py-3 shadow mt-4">
                            SIMPAN TRANSAKSI <i class="fas fa-save ml-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card card-custom p-4 bg-white">
                    <h6 class="font-weight-bold text-secondary mb-3">🕒 Riwayat Input Hari Ini</h6>
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Jam</th>
                                    <th>Siswa</th>
                                    <th>Sampah</th>
                                    <th>Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tgl_ini = date('Y-m-d');
                                $qRiwayat = mysqli_query($koneksi, "SELECT t.*, s.nama_lengkap, k.nama_jenis FROM transaksi t JOIN siswa s ON t.id_siswa=s.id_siswa JOIN kategori_sampah k ON t.id_kategori=k.id_kategori WHERE DATE(t.tanggal_transaksi)='$tgl_ini' ORDER BY t.id_transaksi DESC LIMIT 7");
                                
                                if(mysqli_num_rows($qRiwayat) > 0){
                                    while($r = mysqli_fetch_array($qRiwayat)){
                                ?>
                                <tr>
                                    <td><?= date('H:i', strtotime($r['tanggal_transaksi'])) ?></td>
                                    <td><?= substr($r['nama_lengkap'], 0, 15) ?>..</td>
                                    <td><?= $r['nama_jenis'] ?> (<?= $r['berat_jumlah'] ?>)</td>
                                    <td class="font-weight-bold text-success">+<?= $r['poin_total'] ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-3 text-muted'>Belum ada transaksi hari ini.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-cari').select2({ placeholder: "Cari Siswa...", allowClear: true });
        });
    </script>
</body>
</html>