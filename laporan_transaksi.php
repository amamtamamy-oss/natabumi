<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// --- LOGIKA FILTER TANGGAL ---
// Default: Tampilkan data bulan ini
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

// Query Data: Gabungkan Riwayat Belanja dengan Data Siswa
$query = "SELECT r.*, s.nama_lengkap, s.kelas 
          FROM riwayat_penukaran r 
          JOIN siswa s ON r.id_siswa = s.id_siswa 
          WHERE DATE(r.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir' 
          ORDER BY r.tanggal DESC";
$result = mysqli_query($koneksi, $query);

// Hitung Total Otomatis
$total_poin = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Koperasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        /* ATURAN SAAT DICETAK (PRINT) */
        @media print {
            .no-print { display: none !important; } /* Sembunyikan tombol/navbar */
            body { background-color: white; }
            .card { border: none !important; shadow: none !important; }
            .table th { background-color: #ddd !important; color: black !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4 no-print">
  <div class="container">
    <a class="navbar-brand font-weight-bold" href="dashboard.php">📊 Jurnal Transaksi Koperasi</a>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
  </div>
</nav>

<div class="container">
    
    <div class="card shadow mb-4 no-print">
        <div class="card-body py-3 bg-white rounded">
            <form method="GET" class="form-inline justify-content-center">
                <label class="mr-2 font-weight-bold">Periode:</label>
                <input type="date" name="tgl_awal" class="form-control mr-2" value="<?= $tgl_awal ?>">
                <span class="mr-2 font-weight-bold">-</span>
                <input type="date" name="tgl_akhir" class="form-control mr-2" value="<?= $tgl_akhir ?>">
                
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-filter"></i> Tampilkan</button>
                <button type="button" onclick="window.print()" class="btn btn-warning ml-2 font-weight-bold shadow-sm"><i class="fas fa-print"></i> Cetak Laporan</button>
            </form>
        </div>
    </div>

    <div class="text-center mb-4">
        <h4 class="font-weight-bold mb-0 text-uppercase">Laporan Penjualan Koperasi</h4>
        <h5 class="mb-2">MA ALMUSLIMUN ECO-SCHOOL</h5>
        <p class="text-muted small mb-0">
            Periode: <b><?= date('d/m/Y', strtotime($tgl_awal)) ?></b> s/d <b><?= date('d/m/Y', strtotime($tgl_akhir)) ?></b>
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="bg-info text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Waktu</th>
                            <th width="25%">Nama Siswa</th>
                            <th>Detail Barang / Keterangan</th>
                            <th width="15%">Total (Poin)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($result) > 0){
                            while($d = mysqli_fetch_array($result)){ 
                                $total_poin += $d['poin_keluar'];
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="small text-center"><?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?></td>
                            <td>
                                <b><?= $d['nama_lengkap'] ?></b><br>
                                <span class="badge badge-light border"><?= $d['kelas'] ?></span>
                            </td>
                            <td class="small"><?= $d['keterangan'] ?></td>
                            <td class="text-right font-weight-bold text-danger">
                                Rp <?= number_format($d['poin_keluar']) ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 text-muted font-italic'>Belum ada transaksi pada periode ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="font-weight-bold" style="font-size: 1.1em;">
                            <td colspan="4" class="text-right text-uppercase">Total Penjualan Periode Ini:</td>
                            <td class="text-right text-danger">Rp <?= number_format($total_poin) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row mt-5 mb-5">
        <div class="col-4 offset-8 text-center">
            <p class="mb-5">
                Kawistolegi, <?= date('d F Y') ?><br>
                Kepala Koperasi,
            </p>
            <br>
            <p class="font-weight-bold border-bottom d-inline-block pb-1">
                ( <?= $_SESSION['nama_petugas'] ?> )
            </p>
        </div>
    </div>

</div>

</body>
</html>