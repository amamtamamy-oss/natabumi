<?php
include 'koneksi.php';

// Cek ID Transaksi
if(!isset($_GET['id'])){ die("ID Transaksi tidak ditemukan."); }
$id = $_GET['id'];

// Ambil Data Transaksi & Data Siswa
$q = mysqli_query($koneksi, "SELECT r.*, s.nama_lengkap, s.nis, s.kelas, s.saldo_poin 
                             FROM riwayat_penukaran r 
                             JOIN siswa s ON r.id_siswa=s.id_siswa 
                             WHERE r.id_penukaran='$id'");
$d = mysqli_fetch_array($q);

if(!$d){ die("Data transaksi tidak ditemukan di database."); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Belanja #<?= $id ?></title>
    <style>
        /* Desain Struk Standar 58mm / 80mm */
        body { 
            font-family: 'Courier New', Courier, monospace; 
            background: #eee; 
            margin: 0; 
            padding: 20px;
        }
        .struk-wrapper {
            width: 300px; /* Ukuran kertas struk thermal */
            background: #fff;
            padding: 15px;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .small { font-size: 11px; }
        
        .garis { 
            border-bottom: 1px dashed #000; 
            margin: 10px 0; 
        }
        
        table { width: 100%; font-size: 12px; }
        td { vertical-align: top; padding: 2px 0; }

        /* Mode Print: Hilangkan background dan margin */
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .struk-wrapper { width: 100%; box-shadow: none; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()"> <div class="struk-wrapper">
    
    <div class="text-center">
        <h3 style="margin:0;">KOPERASI ECO-SCHOOL</h3>
        <span class="small">MA ALMUSLIMUN KAWISTOLEGI</span>
    </div>
    
    <div class="garis"></div>
    
    <table class="small">
        <tr>
            <td>Tgl</td>
            <td class="text-right"><?= date('d/m/Y H:i', strtotime($d['tanggal'])) ?></td>
        </tr>
        <tr>
            <td>No. Nota</td>
            <td class="text-right">#TRX-<?= $d['id_penukaran'] ?></td>
        </tr>
        <tr>
            <td>Siswa</td>
            <td class="text-right"><?= substr($d['nama_lengkap'], 0, 15) ?></td>
        </tr>
    </table>
    
    <div class="garis"></div>
    
    <table>
        <tr>
            <td colspan="2" class="bold" style="padding-bottom: 5px;">Item Belanja:</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-left: 10px;">
                <?= $d['keterangan'] ?>
            </td>
        </tr>
    </table>
    
    <div class="garis"></div>
    
    <table>
        <tr style="font-size: 14px;">
            <td class="bold">TOTAL BAYAR</td>
            <td class="text-right bold">Rp <?= number_format($d['poin_keluar']) ?></td>
        </tr>
        <tr>
            <td colspan="2" class="garis"></td>
        </tr>
        <tr>
            <td>Sisa Saldo Poin</td>
            <td class="text-right">Rp <?= number_format($d['saldo_poin']) ?></td>
        </tr>
    </table>
    
    <div class="garis"></div>
    
    <div class="text-center small">
        Terima Kasih!<br>
        <i>"Sampahmu, Tabunganmu"</i>
    </div>

</div>

<div class="text-center no-print" style="margin-top: 20px;">
    <button onclick="window.print()" style="padding: 10px 20px; font-weight: bold; cursor: pointer;">🖨️ Cetak Lagi</button>
    <br><br>
    <a href="tukar_hadiah.php" style="text-decoration: none; color: blue;">[ Kembali ke Kasir ]</a>
</div>

</body>
</html>