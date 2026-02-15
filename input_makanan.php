<?php include 'koneksi.php'; ?>
<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php?pesan=belum_login");
}
include 'koneksi.php'; 
// ... kode selanjutnya biarkan seperti biasa
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Makanan - MA Almuslimun</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Eco-Health School</a>
    <div class="navbar-nav"><div class="navbar-nav ml-auto">
  <a class="nav-link" href="index.php">Input Sampah</a>
  <a class="nav-link" href="input_makanan.php">Input Makanan</a>
  <a class="nav-link" href="laporan.php">Lihat Laporan</a>
  <a class="nav-link btn btn-danger text-white ml-2" href="logout.php">Keluar</a>
</div>
      <a class="nav-link" href="index.php">Bank Sampah</a>
      <a class="nav-link active" href="input_makanan.php">Monitoring Makanan</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h4>Form Cek Kesehatan Makanan</h4>
        </div>
        <div class="card-body">
            <form action="proses_makanan.php" method="POST">
                
                <div class="form-group">
                    <label>Nama Pedagang / Kantin</label>
                    <select name="id_pedagang" class="form-control" required>
                        <option value="">-- Pilih Pedagang --</option>
                        <?php
                        $q = mysqli_query($koneksi, "SELECT * FROM pedagang");
                        while ($d = mysqli_fetch_array($q)) {
                            echo "<option value='$d[id_pedagang]'>$d[nama_pedagang] ($d[lokasi])</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nama Makanan/Minuman</label>
                    <input type="text" name="nama_makanan" class="form-control" placeholder="Contoh: Es Teh Plastik, Nasi Soto" required>
                </div>

                <div class="form-group">
                    <label>Level Kesehatan (Skor)</label>
                    <div class="border p-3 rounded">
                        
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lvl5" name="level_sehat" value="5" class="custom-control-input" required>
                            <label class="custom-control-label text-success font-weight-bold" for="lvl5">
                                ⭐ Level 5: Sangat Sehat (Hijau Tua)
                            </label>
                            <small class="d-block text-muted">- Alami, Buah/Sayur utuh, Tanpa Pengawet.</small>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lvl4" name="level_sehat" value="4" class="custom-control-input">
                            <label class="custom-control-label text-info font-weight-bold" for="lvl4">
                                ⭐ Level 4: Sehat (Biru)
                            </label>
                            <small class="d-block text-muted">- Masakan rumahan, direbus/kukus, higienis.</small>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lvl3" name="level_sehat" value="3" class="custom-control-input">
                            <label class="custom-control-label text-primary font-weight-bold" for="lvl3">
                                ⭐ Level 3: Netral (Kuning)
                            </label>
                            <small class="d-block text-muted">- Roti kemasan, biskuit, gorengan minyak bersih.</small>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="lvl2" name="level_sehat" value="2" class="custom-control-input">
                            <label class="custom-control-label text-warning font-weight-bold" for="lvl2">
                                ⚠️ Level 2: Kurangi (Oranye)
                            </label>
                            <small class="d-block text-muted">- Terlalu manis, tinggi garam, mie instan.</small>
                        </div>

                        <div class="custom-control custom-radio">
                            <input type="radio" id="lvl1" name="level_sehat" value="1" class="custom-control-input">
                            <label class="custom-control-label text-danger font-weight-bold" for="lvl1">
                                ⛔ Level 1: Hindari/Bahaya (Merah)
                            </label>
                            <small class="d-block text-muted">- Saus curah aneh, pewarna tekstil, kotor/basi.</small>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan Temuan</label>
                    <textarea name="catatan" class="form-control" placeholder="Contoh: Saus terlihat terlalu merah menyala..."></textarea>
                </div>

                <button type="submit" class="btn btn-info btn-block">Simpan Data Makanan</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>