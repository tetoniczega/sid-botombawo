<?php 
session_start();
date_default_timezone_set('Asia/Jakarta');
if($_SESSION['status'] != "login" || strtolower($_SESSION['role']) != 'admin'){ 
    header("location:login.php"); 
    exit; 
}
include 'koneksi.php'; 

$nama_desa = "Botombawo"; 

$hari_ini = date('Y-m-d');
mysqli_query($conn, "DELETE FROM pengumuman WHERE tgl_selesai IS NOT NULL AND tgl_selesai < '$hari_ini'");

if(isset($_GET['aksi']) && $_GET['aksi'] == 'hapus'){
    $id_hapus = mysqli_real_escape_string($conn, $_GET['id']);
    mysqli_query($conn, "DELETE FROM pengumuman WHERE id='$id_hapus'");
    echo "<script>alert('Pengumuman berhasil dihapus!'); window.location='tambah_informasi.php';</script>";
}

if(isset($_POST['simpan_pengumuman'])){
    $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $isi         = mysqli_real_escape_string($conn, $_POST['isi']);
    $tgl_selesai = mysqli_real_escape_string($conn, $_POST['tgl_selesai']);
    $username    = $_SESSION['nama_pengguna'];
    $waktu       = date('Y-m-d H:i:s');

    $foto        = ""; 
    $tanggal     = $hari_ini; 
    $penulis     = $username; 

    $query = "INSERT INTO pengumuman (judul, isi_pengumuman, kategori, foto_banner, tanggal, tgl_selesai, tanggal_akhir, penulis, status) 
              VALUES ('$judul', '$isi', '$kategori', '$foto', '$tanggal', '$tgl_selesai', '$tgl_selesai', '$penulis', 'Aktif')";
    
    $eksekusi = mysqli_query($conn, $query);

    if($eksekusi){
        @mysqli_query($conn, "INSERT INTO log_aktivitas (username, tindakan, waktu) VALUES ('$username', 'Membuat pengumuman baru: $judul', '$waktu')");
        
        echo "<script>
                alert('Pengumuman berhasil diterbitkan dan diatur auto-delete!'); 
                window.location='tambah_informasi.php';
              </script>";
    } else {
        echo "<script>alert('Gagal menerbitkan pengumuman. Eror: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Pengumuman - Desa <?= htmlspecialchars($nama_desa); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f7f6; color: #2c3e50; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; width: 260px; background: #ffffff; padding-top: 20px; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.03); }
        .sidebar-brand { font-size: 1.4rem; font-weight: 800; text-align: center; color: #059669; text-decoration: none; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; letter-spacing: 0.5px;}
        .sidebar-brand i { font-size: 1.8rem; margin-right: 12px; color: #059669; }
        .nav-link { color: #6c757d; font-weight: 600; padding: 12px 25px; margin: 5px 15px; border-radius: 10px; transition: 0.3s; text-decoration: none; }
        .nav-link:hover { background-color: #f8f9fa; color: #059669; transform: translateX(5px); }
        .nav-link.active { background: linear-gradient(135deg, #059669, #047857); color: white; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }
        .main-content { margin-left: 260px; padding: 40px; }
        .card-premium { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .btn-success-premium { background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; font-weight: 700; border-radius: 10px; padding: 12px 25px; }
        .btn-success-premium:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 10px rgba(16, 185, 129, 0.2); }
        .info-box { background-color: #f0fdf4; border-left: 4px solid #10b981; color: #14532d; padding: 15px; border-radius: 8px; font-size: 0.9rem; }
        
        .table-custom th { text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; color: #2c3e50; font-weight: 700; }
    </style>
</head>
<body>

   <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-bullhorn text-success me-2"></i>Manajemen Pengumuman Desa</h2>
            <p class="text-muted">Buat informasi atau berita desa baru dengan sistem kedaluwarsa otomatis di Desa <strong><?= htmlspecialchars($nama_desa); ?></strong></p>
        </div>

        <!-- FORM INPUT ORIGINAL -->
        <div class="card card-premium p-4 mb-5">
            <div class="info-box mb-4">
                <i class="fas fa-clock me-2"></i> 
                <strong>Sistem Otomatis Aktif:</strong> Pengumuman akan otomatis terhapus dari sistem dan hilang dari portal warga jika melewati tanggal selesai berlaku.
            </div>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul Pengumuman / Berita</label>
                    <input type="text" name="judul" class="form-control" style="border-radius:10px;" placeholder="Contoh: Jadwal Pembagian Bansos PKH Tahap 2" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kategori Informasi</label>
                        <select name="kategori" class="form-select" style="border-radius:10px;" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="Berita">Berita Desa</option>
                            <option value="Kegiatan">Kegiatan Warga</option>
                            <option value="Pemberitahuan">Pemberitahuan Penting</option>
                            <option value="Bansos">Bantuan Sosial</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Pengumuman Ini Berlaku Sampai Tanggal:</label>
                        <input type="date" name="tgl_selesai" class="form-control" style="border-radius:10px;" min="<?= date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Isi Lengkap Informasi</label>
                    <textarea name="isi" class="form-control" rows="6" style="border-radius:10px;" placeholder="Tuliskan detail pengumuman di sini..." required></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="simpan_pengumuman" class="btn btn-success-premium shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Terbitkan Pengumuman
                    </button>
                    <a href="index.php" class="btn btn-light fw-bold" style="border-radius:10px; padding: 12px 20px; border: 1px solid #ddd;">Batal</a>
                </div>
            </form>
        </div>

        <div class="mb-3">
            <h4 class="fw-bold text-dark">Daftar Pengumuman Aktif saat ini</h4>
            <p class="text-muted small">Menampilkan data tanggal rilis dan batas kedaluwarsa secara riil untuk pembuktian sistem.</p>
        </div>

        <div class="card card-premium p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-custom" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">NO</th>
                            <th width="15%">KATEGORI</th>
                            <th width="45%">JUDUL PENGUMUMAN</th>
                            <th width="15%">TGL DIBUAT</th>
                            <th width="15%">BERLAKU SAMPAI</th>
                            <th width="5%" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        $ambil_data = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY id DESC");
                        while($row = mysqli_fetch_array($ambil_data)){
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge bg-secondary" style="border-radius:5px; padding: 6px 10px; font-size: 0.8rem;"><?= $row['kategori']; ?></span></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['judul']); ?></td>
                            <td class="text-success fw-bold"><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td class="text-danger fw-bold"><?= date('d M Y', strtotime($row['tgl_selesai'])); ?></td>
                            <td class="text-center">
                                <a href="tambah_informasi.php?id=<?= $row['id']; ?>&aksi=hapus" class="text-danger" onclick="return confirm('Hapus pengumuman ini secara manual?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>