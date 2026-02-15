<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

$pesan = "";

// --- PROSES SIMPAN DATA ---
if(isset($_POST['simpan'])){
    $id_siswa   = $_POST['id_siswa'];
    $id_sampah  = $_POST['id_sampah'];
    $berat      = $_POST['berat']; 
    $kualitas   = $_POST['kualitas']; // Bersih / Sedang / Kotor
    $tanggal    = date('Y-m-d H:i:s');

    // 1. Ambil Harga Dasar Sampah dari Database
    // (Harga ini yang Bapak edit di menu 'Kelola Sampah')
    $qS = mysqli_query($koneksi, "SELECT * FROM kategori_sampah WHERE id_kategori='$id_sampah'");
    $dS = mysqli_fetch_array($qS);
    
    // Pastikan kita ambil 'harga_poin_dasar' (Harga Beli ke Siswa)
    $harga_dasar = $dS['harga_poin_dasar']; 

    // 2. Hitung Poin Awal (Berat dalam Kg * Harga Dasar)
    // Rumus: (Berat Gram / 1000) * Harga Per Kg
    $poin_kotor = ($berat / 1000) * $harga_dasar;

    // 3. LOGIKA LEVEL KEBERSIHAN (INI YANG BAPAK MINTA) ✅
    if($kualitas == 'Bersih'){
        $poin_bersih = $poin_kotor * 1.0; // 100% (Utuh)
    } 
    elseif($kualitas == 'Sedang'){
        $poin_bersih = $poin_kotor * 0.8; // 80% (Dipotong 20%)
    } 
    elseif($kualitas == 'Kotor'){
        $poin_bersih = $poin_kotor * 0.5; // 50% (Dipotong Separuh)
    }

    // Bulatkan ke bawah agar tidak ada koma
    $poin_fix = floor($poin_bersih);

    // 4. Simpan ke Tabel Transaksi
    // PERBAIKAN DI SINI: Mengubah 'poin_dapat' menjadi 'poin_total' sesuai database Bapak
    $simpan = mysqli_query($koneksi, "INSERT INTO transaksi (id_siswa, id_kategori, berat_jumlah, poin_total, tanggal, kualitas) VALUES ('$id_siswa', '$id_sampah', '$berat', '$poin_fix', '$tanggal', '$kualitas')");

    // 5. Update Saldo Siswa
    $update = mysqli_query($koneksi, "UPDATE siswa SET saldo_poin = saldo_poin + $poin_fix WHERE id_siswa='$id_siswa'");

    if($simpan && $update){
        $pesan = "✅ <b>Sukses!</b> Poin: $poin_fix (Kualitas: $kualitas - Berat: $berat gr)";
    } else {
        $pesan = "❌ Gagal menyimpan. Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Timbang Sampah</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding-bottom: 120px; border-radius: 0 0 40px 40px; color: white; text-align: center; }
        .btn-back { position: absolute; top: 20px; left: 20px; z-index: 10; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        #reader { width: 100%; border-radius: 15px; display: none; margin-bottom: 15px; border: 4px solid #38ef7d; }
        .select2-container .select2-selection--single { height: 50px !important; display: flex; align-items: center; border-radius: 10px !important; border: 1px solid #ced4da; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 50px !important; }
    </style>
</head>
<body>

    <div class="header-bg pt-5">
        <a href="dashboard.php" class="btn btn-light btn-back rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left text-success"></i>
        </a>
        <h2 class="font-weight-bold mb-1">⚖️ Timbang Sampah</h2>
        <p class="mb-0 text-white-50">Sistem Poin Berbasis Kualitas</p>
    </div>

    <div class="container" style="margin-top: -80px;">
        <div class="row justify-content-center">
            
            <div class="col-md-5 mb-4">
                <div class="card card-custom p-4 bg-white">
                    
                    <?php if($pesan){ 
                        $alert = (strpos($pesan, 'Sukses') !== false) ? 'alert-success' : 'alert-danger';
                        echo "<div class='alert $alert shadow-sm rounded-lg'>$pesan</div>"; 
                    } ?>
                    
                    <div id="reader"></div>

                    <form method="POST">
                        
                        <div class="form-group">
                            <label class="font-weight-bold text-secondary small">SISWA / SCAN QR</label>
                            <div class="input-group">
                                <select name="id_siswa" id="pilihSiswa" class="form-control select2-cari" style="width: 80%;" required>
                                    <option value="">-- Cari Nama / Scan --</option>
                                    <?php
                                    $qS = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama_lengkap ASC");
                                    while($s = mysqli_fetch_array($qS)){
                                        echo "<option value='$s[id_siswa]' data-nis='$s[nis]'>$s[nama_lengkap] ($s[kelas])</option>";
                                    }
                                    ?>
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-warning text-white" onclick="toggleKamera()" title="Buka Kamera">
                                        <i class="fas fa-qrcode"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary small">JENIS SAMPAH</label>
                            <select name="id_sampah" class="form-control h-auto py-2" style="border-radius: 10px;" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                $qJ = mysqli_query($koneksi, "SELECT * FROM kategori_sampah ORDER BY nama_jenis ASC");
                                while($j = mysqli_fetch_array($qJ)){
                                    echo "<option value='$j[id_kategori]'>$j[nama_jenis] (Rp $j[harga_poin_dasar]/kg)</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary small">KUALITAS / KEBERSIHAN</label>
                            <select name="kualitas" class="form-control h-auto py-2" style="border-radius: 10px;" required>
                                <option value="Bersih">✨ Bersih (Harga Normal 100%)</option>
                                <option value="Sedang">⚠️ Sedang (Potongan 20%)</option>
                                <option value="Kotor">❌ Kotor (Potongan 50%)</option>
                            </select>
                            <small class="text-muted">Kondisi sampah menentukan poin yang didapat.</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-secondary small">BERAT (GRAM)</label>
                            <div class="input-group">
                                <input type="number" name="berat" class="form-control font-weight-bold text-primary" placeholder="Contoh: 500" style="height: 50px; border-radius: 10px 0 0 10px;" required>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light font-weight-bold" style="border-radius: 0 10px 10px 0;">GR</span>
                                </div>
                            </div>
                            <small class="text-muted">1000 gram = 1 Kg</small>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success btn-block rounded-pill font-weight-bold py-3 shadow mt-4">
                            SIMPAN & HITUNG POIN <i class="fas fa-calculator ml-1"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card card-custom p-4 bg-white">
                    <h6 class="font-weight-bold text-secondary mb-3">Riwayat Input Hari Ini</h6>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th>Siswa</th>
                                    <th>Jenis</th>
                                    <th>Kualitas</th>
                                    <th class="text-right">Poin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tgl_ini = date('Y-m-d');
                                // Join tabel transaksi, siswa, dan kategori_sampah
                                $qR = mysqli_query($koneksi, "SELECT t.*, s.nama_lengkap, k.nama_jenis 
                                                              FROM transaksi t 
                                                              JOIN siswa s ON t.id_siswa=s.id_siswa 
                                                              JOIN kategori_sampah k ON t.id_kategori=k.id_kategori 
                                                              WHERE DATE(t.tanggal)='$tgl_ini' 
                                                              ORDER BY t.tanggal DESC LIMIT 5");
                                
                                if(mysqli_num_rows($qR) > 0){
                                    while($r = mysqli_fetch_array($qR)){
                                        // Warna badge berdasarkan kualitas
                                        $bg = 'badge-secondary';
                                        if($r['kualitas']=='Bersih') $bg = 'badge-success';
                                        if($r['kualitas']=='Sedang') $bg = 'badge-warning';
                                        if($r['kualitas']=='Kotor') $bg = 'badge-danger';
                                ?>
                                <tr>
                                    <td class="font-weight-bold align-middle"><?= substr($r['nama_lengkap'], 0, 10) ?>..</td>
                                    <td class="small align-middle">
                                        <?= $r['nama_jenis'] ?> <br>
                                        <span class="text-muted"><?= $r['berat_jumlah'] ?> gr</span>
                                    </td>
                                    <td class="align-middle"><span class="badge <?= $bg ?>"><?= $r['kualitas'] ?></span></td>
                                    <td class="text-right font-weight-bold text-success align-middle">+<?= number_format($r['poin_total']) ?></td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Belum ada setoran hari ini.</td></tr>";
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
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        $(document).ready(function() { $('.select2-cari').select2(); });

        var html5QrcodeScanner = null;
        function toggleKamera() {
            var x = document.getElementById("reader");
            if (x.style.display === "none" || x.style.display === "") {
                x.style.display = "block"; startScan();
            } else {
                x.style.display = "none"; if(html5QrcodeScanner) html5QrcodeScanner.stop();
            }
        }
        function startScan() {
            html5QrcodeScanner = new Html5Qrcode("reader");
            html5QrcodeScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => {
                    html5QrcodeScanner.stop();
                    document.getElementById("reader").style.display = "none";
                    var found = false;
                    $('#pilihSiswa option').each(function() {
                        if ($(this).data('nis') == decodedText) {
                            $('#pilihSiswa').val($(this).val()).trigger('change');
                            found = true; alert("✅ Siswa Ditemukan: " + $(this).text());
                            return false;
                        }
                    });
                    if(!found) alert("❌ Kartu Tidak Terdaftar di Database!");
                }, (errorMessage) => {}
            ).catch((err) => {});
        }
    </script>
</body>
</html>