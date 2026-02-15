<?php
session_start();
include 'koneksi.php';

// Logika Filter: Bisa per Kelas ATAU per ID Siswa
$where = "";
$judul = "";

if(isset($_GET['kelas'])){
    $kelas = $_GET['kelas'];
    $where = "WHERE kelas='$kelas'";
    $judul = "Kelas " . $kelas;
} elseif(isset($_GET['id'])){
    $id = $_GET['id'];
    $where = "WHERE id_siswa='$id'";
    $judul = "Siswa ID " . $id;
} else {
    header("location:kelola_siswa.php"); // Kalau kosong, tendang balik
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu - <?= $judul ?></title>
    <style>
        body { font-family: Arial, sans-serif; -webkit-print-color-adjust: exact; background: #f0f0f0; padding: 20px; }
        .container { width: 100%; display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; }
        
        .card-member {
            width: 340px; 
            height: 215px;
            border: 1px solid #999;
            position: relative;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
            color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            page-break-inside: avoid;
            margin-bottom: 10px;
        }
        
        .header { 
            padding: 8px 10px; 
            border-bottom: 1px solid rgba(255,255,255,0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.1); 
        }

        .logo-img { height: 35px; width: auto; background: white; padding: 2px; border-radius: 4px; }
        .header-text { font-weight: bold; font-size: 12px; text-align: center; text-transform: uppercase; flex-grow: 1; margin: 0 5px; line-height: 1.2; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); }
        
        .content { padding: 15px; display: flex; align-items: center; }
        .qr-code { background: white; padding: 5px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .info { margin-left: 15px; }
        .info h3 { margin: 0; font-size: 18px; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); }
        .info p { margin: 4px 0 0; font-size: 13px; font-weight: 500; }
        
        .footer { position: absolute; bottom: 0; width: 100%; padding: 6px; font-size: 10px; text-align: center; background: rgba(0,0,0,0.2); font-style: italic; }

        .btn-print { position: fixed; bottom: 30px; right: 30px; background: #007bff; color: white; font-weight: bold; padding: 15px 30px; border: none; border-radius: 50px; cursor: pointer; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 99; }
        
        @media print { 
            body { background: none; padding: 0; margin: 0; }
            .btn-print { display: none; } 
            .card-member { margin: 5px; border: 1px solid #ccc; float: left; }
            .container { display: block; }
        }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    <div class="container">
        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM siswa $where ORDER BY nama_lengkap ASC");
        while($d = mysqli_fetch_array($query)){
        ?>
        <div class="card-member">
            <div class="header">
                <img src="logo_ma.png" alt="MA" class="logo-img">
                <div class="header-text">KARTU NASABAH<br>ECO-HEALTH SCHOOL</div>
                <img src="logo_nata.png" alt="Nata" class="logo-img">
            </div>
            <div class="content">
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=<?= $d['nis'] ?>&margin=0" width="85">
                </div>
                <div class="info">
                    <h3><?= substr($d['nama_lengkap'], 0, 15) ?><?= (strlen($d['nama_lengkap']) > 15) ? '..' : '' ?></h3>
                    <p>NIS: <strong><?= $d['nis'] ?></strong></p>
                    <p>Kelas: <?= $d['kelas'] ?></p>
                </div>
            </div>
            <div class="footer">Mitra Resmi: MA Almuslimun & Nata Bumi Foundation</div>
        </div>
        <?php } ?>
    </div>
</body>
</html>