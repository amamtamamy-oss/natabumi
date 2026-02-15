<?php
include 'koneksi.php';

// --- BAGIAN 1: Cek Input Siswa ---
if (isset($_POST['id_siswa_input'])) {
    $input_mentah = $_POST['id_siswa_input']; 
    $pecah = explode("ID:", $input_mentah);
    if (isset($pecah[1])) {
        $id_siswa = trim($pecah[1]); 
    } else {
        echo "<script>alert('ERROR: Pilih nama siswa dari daftar!'); window.history.back();</script>";
        exit;
    }
} else {
    echo "<script>alert('Data siswa kosong!'); window.history.back();</script>";
    exit;
}

// --- BAGIAN 2: Tangkap Data ---
$id_kategori = $_POST['id_kategori'];
$berat_gram  = $_POST['berat']; // Satuan Gram
$kebersihan  = $_POST['kebersihan']; // kotor, sedang, atau bersih

// --- BAGIAN 3: Ambil Harga PENGEPUL dari Database ---
// Kita butuh harga asli pengepul untuk dihitung persentasenya
$queryHarga = mysqli_query($koneksi, "SELECT harga_pengepul FROM kategori_sampah WHERE id_kategori = '$id_kategori'");
$dataHarga  = mysqli_fetch_array($queryHarga);
$hargaAsli  = $dataHarga['harga_pengepul']; 

// Jika harga pengepul belum diisi admin, default ke 0 biar gak error
if($hargaAsli <= 0) {
    echo "<script>alert('Admin belum set harga pengepul untuk sampah ini!'); window.history.back();</script>";
    exit;
}

// --- BAGIAN 4: RUMUS BAGI HASIL (Sesuai Permintaan Bapak) ---
$persen_siswa = 0; // Wadah persentase

if ($kebersihan == "kotor") {
    // Sekolah untung 60%, Siswa dapat 40%
    $persen_siswa = 0.40; 
} elseif ($kebersihan == "sedang") {
    // Sekolah untung 50%, Siswa dapat 50%
    $persen_siswa = 0.50;
} elseif ($kebersihan == "bersih") {
    // Sekolah untung 40%, Siswa dapat 60%
    $persen_siswa = 0.60;
}

// HITUNG FINAL
// Rumus: (Berat Gram / 1000) * Harga Asli Pengepul * Persentase Jatah Siswa
$berat_kg = $berat_gram / 1000;
$totalPoin = round($berat_kg * $hargaAsli * $persen_siswa);

// --- BAGIAN 5: Simpan ke Database ---
// Simpan juga status kebersihan agar tercatat di laporan
$querySimpan = "INSERT INTO transaksi (id_siswa, id_kategori, berat_jumlah, tingkat_kebersihan, poin_total) 
                VALUES ('$id_siswa', '$id_kategori', '$berat_gram', '$kebersihan', '$totalPoin')";

if (mysqli_query($koneksi, $querySimpan)) {
    $id_transaksi_baru = mysqli_insert_id($koneksi);
    
    // Update Saldo Siswa
    mysqli_query($koneksi, "UPDATE siswa SET saldo_poin = saldo_poin + $totalPoin WHERE id_siswa = '$id_siswa'");
    
    header("location:cetak_struk.php?id=$id_transaksi_baru");

} else {
    echo "Gagal menyimpan: " . mysqli_error($koneksi);
}
?>