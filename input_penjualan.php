<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$pesan = "";
$id_transaksi_baru = "";

if(isset($_POST['simpan'])){
    $tanggal    = $_POST['tanggal'];
    $sumber     = $_POST['sumber'];
    $berat_kg   = $_POST['berat_keluar']; // Input Baru
    $keterangan = $_POST['keterangan'];
    $nominal    = $_POST['nominal'];
    $pencatat   = $_SESSION['nama_petugas'];

    // Simpan data termasuk Berat
    $simpan = mysqli_query($koneksi, "INSERT INTO keuangan_masuk (tanggal, sumber_dana, berat_keluar, keterangan, nominal, pencatat) VALUES ('$tanggal', '$sumber', '$berat_kg', '$keterangan', '$nominal', '$pencatat')");

    if($simpan){
        $id_transaksi_baru = mysqli_insert_id($koneksi);
        $pesan = "<div class='alert alert-success'>✅ Penjualan Berhasil Disimpan! (Stok Berkurang $berat_kg Kg)</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>❌ Gagal Menyimpan Data!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Penjualan Sampah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 30px; border-radius: 15px 15px 0 0; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg rounded-lg">
                <div class="header-bg text-center">
                    <h4 class="font-weight-bold mb-0">💰 Jual Sampah (Pemasukan)</h4>
                    <small>Catat penjualan ke pengepul untuk kurangi stok</small>
                </div>
                <div class="card-body p-4">
                    <?= $pesan ?>

                    <?php if($id_transaksi_baru != "") { ?>
                        <div class="text-center mb-4">
                            <a href="cetak_struk_jual.php?id=<?= $id_transaksi_baru ?>" target="_blank" class="btn btn-warning btn-lg font-weight-bold shadow pulse-button"><i class="fas fa-print mr-2"></i> CETAK NOTA PENJUALAN</a>
                            <br><br>
                            <a href="input_penjualan.php" class="text-muted small"><u>Input Transaksi Baru</u></a>
                        </div>
                    <?php } else { ?>

                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Jenis Transaksi</label>
                            <select name="sumber" class="form-control" required>
                                <option value="Jual Sampah ke Pengepul">Jual Sampah ke Pengepul</option>
                                <option value="Jual Produk Daur Ulang">Jual Produk Daur Ulang</option>
                                <option value="Donasi / Lainnya">Donasi / Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-danger">Berat Dijual (Kg)</label>
                            <div class="input-group">
                                <input type="number" name="berat_keluar" class="form-control font-weight-bold" placeholder="0" required>
                                <div class="input-group-append"><span class="input-group-text">Kg</span></div>
                            </div>
                            <small class="text-muted">Masukkan 0 jika bukan penjualan sampah.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary">Keterangan / Nama Pengepul</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Pak Budi (Kardus & Botol)" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-success">Total Uang Diterima (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text bg-success text-white font-weight-bold">Rp</span></div>
                                <input type="number" name="nominal" class="form-control form-control-lg font-weight-bold text-success" placeholder="0" required>
                            </div>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success btn-block font-weight-bold py-3 mt-4">SIMPAN DATA</button>
                        <a href="dashboard.php" class="btn btn-secondary btn-block mt-2">Kembali</a>
                    </form>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>