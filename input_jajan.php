<?php
session_start();
if(!isset($_SESSION['status_siswa']) || $_SESSION['status_siswa'] != "login"){
    header("location:login_siswa.php");
    exit;
}
include 'koneksi.php';

$id_siswa = $_SESSION['id_siswa'];
$pesan = "";

// --- PROSES SIMPAN ---
if(isset($_POST['simpan'])){
    $mode_pedagang = $_POST['mode_pedagang']; // Mode Pedagang (Database/Manual)
    $mode_input    = $_POST['mode_input'];    // Mode Makanan (Database/Manual)
    $tanggal       = date('Y-m-d H:i:s');
    
    // 1. URUS PEDAGANG DULU
    $id_pedagang_final = 0;

    if($mode_pedagang == 'database'){
        $id_pedagang_final = $_POST['id_pedagang'];
    } 
    else if($mode_pedagang == 'manual'){
        $nama_pedagang_baru = htmlspecialchars($_POST['nama_pedagang_manual']);
        if(!empty($nama_pedagang_baru)){
            // Cek dulu, jangan-jangan sudah ada tapi siswa gak lihat
            $cekP = mysqli_query($koneksi, "SELECT id_pedagang FROM pedagang WHERE nama_pedagang LIKE '%$nama_pedagang_baru%'");
            if(mysqli_num_rows($cekP) > 0){
                $dP = mysqli_fetch_array($cekP);
                $id_pedagang_final = $dP['id_pedagang'];
            } else {
                // Masukkan sebagai Pedagang PENDING
                mysqli_query($koneksi, "INSERT INTO pedagang (nama_pedagang, nama_kios, status) VALUES ('$nama_pedagang_baru', 'Kios Baru (Siswa)', 'pending')");
                $id_pedagang_final = mysqli_insert_id($koneksi); // Ambil ID yang baru dibuat
            }
        }
    }

    // 2. URUS MAKANAN
    $nama_final = "";
    $level_final = 0;

    if($mode_input == 'database'){
        $id_menu = $_POST['id_menu'];
        if(!empty($id_menu)){
            $qM = mysqli_query($koneksi, "SELECT * FROM menu_sehat WHERE id_menu='$id_menu'");
            $dM = mysqli_fetch_array($qM);
            $nama_final  = $dM['nama_makanan'];
            $level_final = $dM['level_sehat'];
            
            // Jika menu database punya pedagang spesifik, timpa pilihan siswa (opsional)
             if($dM['id_pedagang'] != 0){
                 $id_pedagang_final = $dM['id_pedagang']; 
             }
        }
    } 
    else if($mode_input == 'manual'){
        $nama_manual = htmlspecialchars($_POST['nama_manual']); 
        if(!empty($nama_manual)){
            $nama_final  = $nama_manual . " (Baru)"; 
            $level_final = 0; 
        }
    }

    // 3. EKSEKUSI FINAL
    if($nama_final != "" && !empty($id_pedagang_final)){
        $simpan = mysqli_query($koneksi, "INSERT INTO jurnal_jajan (id_siswa, nama_makanan, level_sehat, tanggal, id_pedagang) VALUES ('$id_siswa', '$nama_final', '$level_final', '$tanggal', '$id_pedagang_final')");
        
        if($simpan){
            $pesan = "✅ Laporan Berhasil! Terima kasih kontribusinya.";
            echo "<meta http-equiv='refresh' content='2;url=dashboard_siswa.php'>"; 
        } else {
            $pesan = "❌ Gagal menyimpan database.";
        }
    } else {
        $pesan = "⚠️ Data belum lengkap (Pedagang/Makanan kosong).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lapor Jajan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%); padding-bottom: 50px; border-radius: 0 0 30px 30px; color: white; }
        .card-form { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: -30px; }
        .select2-container .select2-selection--single { height: 50px !important; border-radius: 50px !important; display: flex; align-items: center; padding-left: 10px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px !important; right: 15px !important; }
        .btn-toggle { cursor: pointer; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="header-bg pt-4 pb-5 text-center">
        <h3 class="font-weight-bold">📝 Jurnal Jajanku</h3>
        <p>Bantu data sekolah kita jadi lebih lengkap!</p>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-form p-4">
                    <?php if($pesan){ echo "<div class='alert alert-success'>$pesan</div>"; } ?>
                    
                    <form method="POST">
                        
                        <div class="form-group bg-light p-3 rounded border">
                            <label class="font-weight-bold text-dark"><i class="fas fa-store mr-1 text-warning"></i> Beli di Mana?</label>
                            <input type="hidden" name="mode_pedagang" id="mode_pedagang" value="database">
                            
                            <div id="pedagang-db">
                                <select name="id_pedagang" class="form-control select2-cari" style="width: 100%;">
                                    <option value="">-- Pilih Kantin/Warung --</option>
                                    <?php
                                    // Tampilkan semua pedagang (termasuk yang pending, biar gak dobel input)
                                    $qP = mysqli_query($koneksi, "SELECT * FROM pedagang ORDER BY nama_pedagang ASC");
                                    while($p = mysqli_fetch_array($qP)){
                                        echo "<option value='$p[id_pedagang]'>$p[nama_pedagang] ($p[nama_kios])</option>";
                                    }
                                    ?>
                                </select>
                                <div class="text-right mt-2">
                                    <span class="text-danger btn-toggle font-weight-bold" onclick="togglePedagang('manual')"><i class="fas fa-plus-circle"></i> Pedagang Baru?</span>
                                </div>
                            </div>

                            <div id="pedagang-manual" style="display: none;">
                                <input type="text" name="nama_pedagang_manual" class="form-control rounded-pill" placeholder="Nama Penjual (Cth: Pak Somad)">
                                <div class="text-right mt-2">
                                    <span class="text-primary btn-toggle font-weight-bold" onclick="togglePedagang('database')"><i class="fas fa-list"></i> Kembali ke List</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group p-3">
                            <label class="font-weight-bold text-secondary"><i class="fas fa-utensils mr-1 text-success"></i> Makan Apa?</label>
                            <input type="hidden" name="mode_input" id="mode_input" value="database">

                            <div id="menu-db">
                                <select name="id_menu" class="form-control select2-cari" style="width: 100%;">
                                    <option value="">-- Cari Makanan... --</option>
                                    <?php
                                    $qM = mysqli_query($koneksi, "SELECT * FROM menu_sehat WHERE level_sehat > 0 ORDER BY nama_makanan ASC");
                                    while($m = mysqli_fetch_array($qM)){
                                        $star = str_repeat("⭐", $m['level_sehat']);
                                        echo "<option value='$m[id_menu]'>$m[nama_makanan] ($star)</option>";
                                    }
                                    ?>
                                </select>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="toggleMenu('manual')">
                                        <i class="fas fa-times-circle"></i> Tidak ketemu? Input Manual
                                    </button>
                                </div>
                            </div>

                            <div id="menu-manual" style="display: none;">
                                <div class="alert alert-info small py-1 mb-2">Guru akan cek gizinya nanti.</div>
                                <input type="text" name="nama_manual" class="form-control rounded-pill" placeholder="Nama Makanan (Cth: Cimol)">
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="toggleMenu('database')">
                                        <i class="fas fa-search"></i> Kembali ke Pencarian
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success btn-block rounded-pill font-weight-bold py-3 shadow">
                            KIRIM LAPORAN <i class="fas fa-paper-plane ml-1"></i>
                        </button>
                        <a href="dashboard_siswa.php" class="btn btn-outline-secondary btn-block rounded-pill mt-3">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-cari').select2({ placeholder: "🔍 Ketik pencarian...", allowClear: true });
        });

        function togglePedagang(mode){
            $('#mode_pedagang').val(mode);
            if(mode == 'manual'){ $('#pedagang-db').hide(); $('#pedagang-manual').show(); }
            else { $('#pedagang-manual').hide(); $('#pedagang-db').show(); }
        }

        function toggleMenu(mode){
            $('#mode_input').val(mode);
            if(mode == 'manual'){ $('#menu-db').hide(); $('#menu-manual').show(); }
            else { $('#menu-manual').hide(); $('#menu-db').show(); }
        }
    </script>
</body>
</html>