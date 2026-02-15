<?php
include 'koneksi.php';
$id = $_GET['id'];

// Ambil data detail transaksi
$query = "SELECT t.*, s.nama_lengkap, s.kelas, k.nama_jenis 
          FROM transaksi t 
          JOIN siswa s ON t.id_siswa = s.id_siswa 
          JOIN kategori_sampah k ON t.id_kategori = k.id_kategori 
          WHERE t.id_transaksi = '$id'";
$data = mysqli_fetch_array(mysqli_query($koneksi, $query));

// --- PERSIAPAN PESAN WA ---
// Format: Halo [Nama], transaksi [Jenis] [Berat]kg berhasil. Poin: [Jml].
$textWA = "Halo *" . $data['nama_lengkap'] . "* (" . $data['kelas'] . "),\n";
$textWA .= "Terima kasih sudah menyetor sampah:\n";
$textWA .= "Jenis: " . $data['nama_jenis'] . "\n";
$textWA .= "Berat: " . $data['berat_jumlah'] . " Kg\n";
$textWA .= "Poin Didapat: *" . $data['poin_total'] . " Poin*\n";
$textWA .= "Tetap semangat jaga lingkungan! 🌱\n";
$textWA .= "- *Bank Sampah MA Almuslimun*";

// Encode agar bisa jadi link (spasi jadi %20, dst)
$linkWA = "https://wa.me/?text=" . urlencode($textWA);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Struk #<?= $id ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Courier New', monospace; font-size: 14px; max-width: 350px; margin: 20px auto; background: #f4f4f4; }
        .kertas { background: white; padding: 15px; border: 1px solid #ddd; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px; }
        .footer { text-align: center; border-top: 2px dashed #000; padding-top: 10px; margin-top: 10px; font-size: 12px; }
        table { width: 100%; }
        
        /* Tombol-tombol Aksi */
        .btn-group { margin-top: 20px; text-align: center; }
        .btn { display: block; width: 100%; padding: 12px; text-decoration: none; margin-bottom: 10px; border-radius: 5px; font-family: sans-serif; font-weight: bold; }
        .btn-print { background: #333; color: white; }
        .btn-wa { background: #25D366; color: white; } /* Warna Hijau WA */
        .btn-back { background: #ddd; color: #333; }
        
        @media print { 
            body { background: white; margin: 0; }
            .kertas { box-shadow: none; border: none; width: 100%; }
            .btn-group, .btn { display: none !important; } /* Sembunyikan tombol saat diprint */
        }
    </style>
</head>
<body>

    <div class="kertas">
        <div class="header">
            <strong>BANK SAMPAH<br>MA ALMUSLIMUN</strong><br>
            <small>Jl. Raya Kampus No. 123</small>
        </div>

        <table>
            <tr><td>Tgl</td> <td align="right"><?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?></td></tr>
            <tr><td>Siswa</td> <td align="right"><?= $data['nama_lengkap'] ?></td></tr>
            <tr><td>Kelas</td> <td align="right"><?= $data['kelas'] ?></td></tr>
        </table>
        
        <hr style="border-top: 1px solid #000;">
        
        <table>
            <tr>
                <td colspan="2"><strong><?= $data['nama_jenis'] ?></strong></td>
            </tr>
            <tr>
                <td><?= $data['berat_jumlah'] ?> Kg x (Grade: <?= $data['tingkat_kebersihan'] ?>)</td>
                <td align="right"><b>+ <?= $data['poin_total'] ?></b></td>
            </tr>
        </table>

        <div class="footer">
            Simpan struk ini sebagai bukti.<br>
            Admin: Eco-Health
        </div>
    </div>

    <div class="btn-group">
        <a href="#" onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</a>
        
        <a href="<?= $linkWA ?>" target="_blank" class="btn btn-wa">📱 Kirim Struk via WA</a>
        
        <a href="index.php" class="btn btn-back">Kembali ke Menu Utama</a>
    </div>

</body>
</html>