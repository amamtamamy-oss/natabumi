<?php
session_start();
if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

// Inisialisasi Keranjang
if(!isset($_SESSION['keranjang'])){ $_SESSION['keranjang'] = []; }

$pesan = "";
$id_cetak = "";
$link_wa = "";

// --- 1. TAMBAH ITEM KE KERANJANG ---
if(isset($_POST['tambah_item'])){
    $id_produk = $_POST['id_produk'];
    $qty       = $_POST['qty'];
    
    // Ambil Data Produk
    $qP = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk='$id_produk'");
    $dP = mysqli_fetch_array($qP);
    
    if($dP['stok'] >= $qty){
        // Cek jika barang sudah ada di keranjang, tinggal update qty
        $found = false;
        foreach($_SESSION['keranjang'] as $key => $item){
            if($item['id'] == $id_produk){
                $_SESSION['keranjang'][$key]['qty'] += $qty;
                $_SESSION['keranjang'][$key]['subtotal'] = $_SESSION['keranjang'][$key]['harga'] * $_SESSION['keranjang'][$key]['qty'];
                $found = true;
                break;
            }
        }

        if(!$found){
            $item = [
                'id' => $dP['id_produk'],
                'nama' => $dP['nama_produk'],
                'harga' => $dP['harga_poin'],
                'qty' => $qty,
                'subtotal' => $dP['harga_poin'] * $qty
            ];
            $_SESSION['keranjang'][] = $item;
        }
    } else {
        $pesan = "<div class='alert alert-danger'>❌ Stok tidak cukup! (Sisa: $dP[stok])</div>";
    }
}

// --- 2. HAPUS ITEM / RESET ---
if(isset($_GET['hapus'])){
    array_splice($_SESSION['keranjang'], $_GET['hapus'], 1); // Hapus & Re-index
    header("location:tukar_hadiah.php");
}
if(isset($_POST['reset'])){
    unset($_SESSION['keranjang']);
    header("location:tukar_hadiah.php");
}

// --- 3. PROSES BAYAR (CHECKOUT) ---
if(isset($_POST['bayar'])){
    if(empty($_SESSION['keranjang'])){
        $pesan = "<div class='alert alert-warning'>⚠️ Keranjang masih kosong!</div>";
    } else {
        $id_siswa = $_POST['id_siswa'];
        $tanggal  = date('Y-m-d H:i:s');
        
        // Hitung Total
        $total_belanja = 0;
        $detail_barang = "";
        
        foreach($_SESSION['keranjang'] as $k){
            $total_belanja += $k['subtotal'];
            $detail_barang .= $k['nama'] . "(" . $k['qty'] . "), ";
            // Kurangi Stok
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $k[qty] WHERE id_produk='$k[id]'");
        }
        $detail_barang = rtrim($detail_barang, ", ");

        // Cek Saldo Siswa
        $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
        $dSiswa = mysqli_fetch_array($cek);
        
        if($dSiswa['saldo_poin'] >= $total_belanja){
            // Potong Saldo
            $sisa = $dSiswa['saldo_poin'] - $total_belanja;
            mysqli_query($koneksi, "UPDATE siswa SET saldo_poin = $sisa WHERE id_siswa='$id_siswa'");
            
            // Simpan Transaksi
            mysqli_query($koneksi, "INSERT INTO riwayat_penukaran (id_siswa, poin_keluar, keterangan, tanggal) VALUES ('$id_siswa', '$total_belanja', '$detail_barang', '$tanggal')");
            $id_cetak = mysqli_insert_id($koneksi);
            
            // WA Link
            $hp = preg_replace('/[^0-9]/', '', $dSiswa['no_hp']);
            if(substr($hp, 0, 1) == '0') $hp = '62'.substr($hp, 1);
            $text_wa = "Halo $dSiswa[nama_lengkap].\nBelanja BERHASIL.\n\n🛒 Item: $detail_barang\n💰 Total: -$total_belanja Poin\n💳 Sisa: Rp ".number_format($sisa);
            $link_wa = "https://wa.me/$hp?text=".urlencode($text_wa);
            
            unset($_SESSION['keranjang']); // Kosongkan Keranjang
            $pesan = "<div class='alert alert-success'>✅ <b>Transaksi Sukses!</b> Total: Rp ".number_format($total_belanja)."</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>❌ Saldo Tidak Cukup! (Sisa: Rp ".number_format($dSiswa['saldo_poin']).")</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Koperasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .header-bg { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); padding-bottom: 80px; border-radius: 0 0 30px 30px; color: white; }
        .card-custom { border:none; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.05); }
        .select2-container .select2-selection--single { height: 45px; display: flex; align-items: center; }
        #reader { width: 100%; border-radius: 10px; display: none; border: 3px solid #38ef7d; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="header-bg pt-4 text-center">
    <a href="dashboard.php" class="btn btn-light btn-sm rounded-circle position-absolute" style="left:20px;"><i class="fas fa-arrow-left"></i></a>
    <h2 class="font-weight-bold">🛒 Kasir Koperasi</h2>
    <p>Belanja Banyak Item Sekaligus</p>
</div>

<div class="container" style="margin-top: -50px;">
    <?= $pesan ?>
    
    <?php if($id_cetak != ""){ ?>
        <div class="alert alert-warning text-center shadow mb-4">
            <h5 class="font-weight-bold">🖨️ Transaksi Selesai!</h5>
            <a href="cetak_struk_koperasi.php?id=<?= $id_cetak ?>" target="_blank" class="btn btn-dark btn-sm font-weight-bold mr-2"><i class="fas fa-print"></i> CETAK STRUK</a>
            <a href="<?= $link_wa ?>" target="_blank" class="btn btn-success btn-sm font-weight-bold"><i class="fab fa-whatsapp"></i> KIRIM WA</a>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-5 mb-3">
            <div class="card card-custom p-4 bg-white h-100">
                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-box-open mr-2"></i>1. Pilih Barang</h6>
                <form method="POST">
                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">NAMA PRODUK</label>
                        <select name="id_produk" id="pilihProduk" class="form-control select2-cari" required>
                            <option value="">-- Cari Barang --</option>
                            <?php
                            $qP = mysqli_query($koneksi, "SELECT * FROM produk WHERE stok > 0 ORDER BY nama_produk ASC");
                            while($p = mysqli_fetch_array($qP)){
                                echo "<option value='$p[id_produk]' data-harga='$p[harga_poin]'>$p[nama_produk] (Stok: $p[stok])</option>";
                            } ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">HARGA</label>
                                <input type="text" id="hargaSatuan" class="form-control font-weight-bold bg-light" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">QTY</label>
                                <input type="number" name="qty" class="form-control font-weight-bold text-primary" value="1" min="1" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="tambah_item" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fas fa-cart-plus mr-1"></i> MASUKKAN KERANJANG
                    </button>
                    <div class="text-center mt-2">
                        <a href="kelola_produk.php" class="small text-muted"><u>+ Tambah Produk Baru</u></a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-7 mb-3">
            
            <div class="card card-custom p-4 bg-white mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="font-weight-bold text-success mb-0"><i class="fas fa-shopping-basket mr-2"></i>2. Keranjang Belanja</h6>
                    <?php if(!empty($_SESSION['keranjang'])) { ?>
                        <form method="POST"><button type="submit" name="reset" class="btn btn-xs btn-outline-danger py-0 small">Reset</button></form>
                    <?php } ?>
                </div>

                <div class="table-responsive bg-light rounded p-2 mb-2" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-borderless mb-0 small">
                        <?php 
                        $grand_total = 0;
                        if(empty($_SESSION['keranjang'])){
                            echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Keranjang Masih Kosong...</td></tr>";
                        } else {
                            foreach($_SESSION['keranjang'] as $key => $item){ 
                                $grand_total += $item['subtotal'];
                        ?>
                        <tr class="border-bottom">
                            <td><?= $item['nama'] ?> <span class="text-muted">(@<?= $item['harga'] ?>)</span></td>
                            <td class="text-center">x<?= $item['qty'] ?></td>
                            <td class="text-right font-weight-bold"><?= number_format($item['subtotal']) ?></td>
                            <td class="text-right"><a href="?hapus=<?= $key ?>" class="text-danger"><i class="fas fa-times"></i></a></td>
                        </tr>
                        <?php } } ?>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold">TOTAL:</span>
                    <h5 class="font-weight-bold text-danger">Rp <?= number_format($grand_total) ?></h5>
                </div>
            </div>

            <div class="card card-custom p-4 bg-white border-left-warning" style="border-left: 5px solid #f0ad4e;">
                <h6 class="font-weight-bold text-warning mb-3"><i class="fas fa-qrcode mr-2"></i>3. Scan & Bayar</h6>
                
                <div id="reader"></div> <form method="POST">
                    <div class="form-group">
                        <div class="input-group">
                            <select name="id_siswa" id="pilihSiswa" class="form-control select2-cari" required>
                                <option value="">-- Cari Nama / Scan Kartu --</option>
                                <?php
                                $qS = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama_lengkap ASC");
                                while($s = mysqli_fetch_array($qS)){
                                    echo "<option value='$s[id_siswa]' data-nis='$s[nis]'>$s[nama_lengkap] (Saldo: ".number_format($s['saldo_poin']).")</option>";
                                } ?>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-warning text-white" onclick="toggleKamera()"><i class="fas fa-camera"></i></button>
                            </div>
                        </div>
                    </div>

                    <?php if(empty($_SESSION['keranjang'])) { ?>
                        <button type="button" class="btn btn-secondary btn-block disabled" disabled>Masukkan Barang Dulu</button>
                    <?php } else { ?>
                        <button type="submit" name="bayar" class="btn btn-success btn-block font-weight-bold py-2 shadow" onclick="return confirm('Proses Pembayaran?')">
                            <i class="fas fa-check-circle mr-2"></i> BAYAR SEKARANG
                        </button>
                    <?php } ?>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $('.select2-cari').select2();
        $('#pilihProduk').change(function(){
            $('#hargaSatuan').val($(this).find(':selected').data('harga'));
        });
    });

    // SCANNER LOGIC
    var html5QrcodeScanner = null;
    function toggleKamera() {
        var x = document.getElementById("reader");
        if (x.style.display === "none") { x.style.display = "block"; startScan(); } 
        else { x.style.display = "none"; if(html5QrcodeScanner) html5QrcodeScanner.stop(); }
    }
    function startScan() {
        html5QrcodeScanner = new Html5Qrcode("reader");
        html5QrcodeScanner.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                html5QrcodeScanner.stop(); document.getElementById("reader").style.display = "none";
                var found = false;
                $('#pilihSiswa option').each(function() {
                    if ($(this).data('nis') == decodedText) {
                        $('#pilihSiswa').val($(this).val()).trigger('change');
                        found = true; alert("✅ Kartu Terbaca: " + $(this).text()); return false; 
                    }
                });
                if(!found) alert("❌ Kartu Tidak Terdaftar!");
            }, (err) => {}
        ).catch((err) => {});
    }
    
    // Auto Print
    <?php if($id_cetak != "") { ?>
        // window.open('cetak_struk_koperasi.php?id=<?= $id_cetak ?>', '_blank');
    <?php } ?>
</script>

</body>
</html>