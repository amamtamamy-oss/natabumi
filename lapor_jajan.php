<?php
include 'koneksi.php';

// --- PROSES SIMPAN LAPORAN ---
if(isset($_POST['lapor'])){
    
    // 1. LOGIKA CERDAS: MENENTUKAN SIAPA SISWANYA
    $input_mentah = $_POST['siswa_input']; 
    $id_siswa = "";

    // A. Jika inputan ANGKA SAJA (Berarti hasil Scan NIS dari Kartu)
    if(is_numeric($input_mentah)){
        $cekNis = mysqli_query($koneksi, "SELECT id_siswa FROM siswa WHERE nis='$input_mentah'");
        if(mysqli_num_rows($cekNis) > 0){
            $dNis = mysqli_fetch_array($cekNis);
            $id_siswa = $dNis['id_siswa'];
        } else {
            echo "<script>alert('NIS $input_mentah tidak terdaftar!'); window.history.back();</script>"; exit;
        }
    } 
    // B. Jika inputan Format Text (Berarti pilih dari ketikan Nama)
    else {
        // Format di Datalist: "Nama Siswa | ID:123"
        // Kita ambil angka setelah "ID:"
        $pecah = explode("ID:", $input_mentah);
        if(isset($pecah[1])){
            $id_siswa = trim($pecah[1]);
        } else {
            echo "<script>alert('Nama tidak dikenali! Harap pilih dari daftar yang muncul saat mengetik.'); window.history.back();</script>"; exit;
        }
    }

    $id_menu  = $_POST['id_menu'];
    $tanggal  = date('Y-m-d H:i:s');

    // 2. Ambil Data Menu (Level Sehat)
    $qMenu = mysqli_query($koneksi, "SELECT * FROM menu_sehat WHERE id_menu='$id_menu'");
    $dMenu = mysqli_fetch_array($qMenu);
    
    $nama_makanan = $dMenu['nama_makanan'];
    $level_sehat  = $dMenu['level_sehat']; 

    // 3. Simpan ke Database
    $simpan = mysqli_query($koneksi, "INSERT INTO jurnal_jajan (id_siswa, nama_makanan, level_sehat, tanggal) VALUES ('$id_siswa', '$nama_makanan', '$level_sehat', '$tanggal')");

    if($simpan){
        echo "<script>alert('✅ Laporan Berhasil! Terima kasih sudah jujur.'); window.location='lapor_jajan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurnal Jajanku - Scan & Lapor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body { background: #28a745; min-height: 100vh; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-jurnal { max-width: 500px; margin: 0 auto; border-radius: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.3); overflow: hidden; }
        .header-bg { background: linear-gradient(135deg, #a8ff78 0%, #78ffd6 100%); padding: 30px 20px; text-align: center; border-bottom: 5px solid #fff; }
        .btn-scan { border-radius: 50px; font-weight: bold; letter-spacing: 1px; transition: 0.3s; }
        .btn-scan:hover { transform: scale(1.05); }
        #reader { width: 100%; border-radius: 10px; border: 2px dashed #28a745; display: none; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div class="card card-jurnal bg-white">
        
        <div class="header-bg">
            <h1 style="font-size: 4rem;">🥗</h1>
            <h4 class="font-weight-bold text-dark mt-2">Jurnal Jajanku</h4>
            <p class="text-secondary font-weight-bold mb-0">Scan Kartu atau Ketik Nama</p>
        </div>

        <div class="card-body p-4">
            
            <div id="reader"></div>

            <form method="POST">
                
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-outline-success btn-scan px-4 py-2" onclick="startCamera()">
                        <i class="fas fa-camera"></i> Buka Kamera Scan
                    </button>
                    <button type="button" class="btn btn-danger btn-scan px-4 py-2" onclick="stopCamera()" id="stopBtn" style="display:none;">
                        ⏹ Stop
                    </button>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-success">Siapa Namamu?</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-user text-success"></i></span>
                        </div>
                        <input list="data_siswa" name="siswa_input" id="inputScan" class="form-control border-left-0" placeholder="Ketik Nama atau Scan Kartu..." autocomplete="off" required>
                    </div>
                    <datalist id="data_siswa">
                        <?php
                        $qS = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama_lengkap ASC");
                        while($s = mysqli_fetch_array($qS)){
                            // Format Value: Nama | ID:123
                            echo "<option value='$s[nama_lengkap] | ID:$s[id_siswa]'>";
                        }
                        ?>
                    </datalist>
                    <small class="text-muted">Tip: Arahkan kamera ke QR Code kartumu agar otomatis terisi.</small>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-success">Jajan Apa Hari Ini?</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-utensils text-success"></i></span>
                        </div>
                        <select name="id_menu" class="form-control border-left-0" required>
                            <option value="">-- Pilih Makanan --</option>
                            <?php
                            $qM = mysqli_query($koneksi, "SELECT * FROM menu_sehat ORDER BY nama_makanan ASC");
                            while($m = mysqli_fetch_array($qM)){
                                // Simbol Bintang
                                $bintang = str_repeat("⭐", $m['level_sehat']);
                                echo "<option value='$m[id_menu]'>$m[nama_makanan] ($bintang)</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <hr>
                <button type="submit" name="lapor" class="btn btn-success btn-block btn-lg shadow rounded-pill font-weight-bold">
                    📝 KIRIM LAPORAN
                </button>

            </form>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-secondary small" style="text-decoration: underline;">Kembali ke Dashboard Admin</a>
            </div>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;

        function startCamera() {
            document.getElementById('reader').style.display = 'block';
            document.getElementById('stopBtn').style.display = 'inline-block';
            
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Pakai kamera belakang
                { fps: 10, qrbox: 250 },
                (decodedText, decodedResult) => {
                    // SUKSES SCAN:
                    // Masukkan hasil scan (NIS) ke kotak input
                    document.getElementById('inputScan').value = decodedText;
                    
                    // Matikan kamera
                    stopCamera();
                    
                    // Beri notifikasi kecil
                    alert("Kartu Terdeteksi! NIS: " + decodedText);
                },
                (errorMessage) => {
                    // Error biarkan saja (karena scanning terus menerus)
                }
            ).catch(err => {
                alert("Gagal membuka kamera. Pastikan izin browser diberikan!");
            });
        }

        function stopCamera() {
            if(html5QrcodeScanner){
                html5QrcodeScanner.stop().then((ignore) => {
                    document.getElementById('reader').style.display = 'none';
                    document.getElementById('stopBtn').style.display = 'none';
                }).catch((err) => {
                    console.log("Stop failed");
                });
            }
        }
    </script>

</body>
</html>