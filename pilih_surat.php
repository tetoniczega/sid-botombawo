<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php'; 

$id_warga = $_GET['id'] ?? '';

if(empty($id_warga)) {
    echo "<script>alert('ID Warga tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
$query = mysqli_query($conn, "SELECT * FROM warga WHERE id='$id_warga'");
$warga = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Jenis Surat - SmartDesa</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f7f6; padding-top: 50px; }
        .card-surat { border: none; border-radius: 20px; transition: all 0.3s ease; cursor: pointer; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.03); border: 2px solid transparent;}
        .card-surat:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(5, 150, 105, 0.15); border-color: #059669;}
        .icon-box { width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 1.8rem; }
    </style>
</head>
<body>

<div class="container text-center mb-4">
    <a href="index.php" class="btn btn-light rounded-pill shadow-sm mb-4 px-4"><i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard</a>
    <h2 class="fw-bold text-dark">Cetak Dokumen Administrasi</h2>
    <p class="text-muted mb-2">Pilih jenis surat dinas yang ingin diterbitkan untuk warga berikut:</p>
    <div class="badge bg-success px-4 py-2 rounded-pill fs-6 shadow-sm mb-4">
        <i class="fas fa-user me-2"></i><?= $warga['nama_lengkap'] ?? 'Nama Tidak Diketahui'; ?> (NIK: <?= $warga['nik'] ?? '-'; ?>)
    </div>
</div>

<div class="container">
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body bg-white rounded-3 p-4 border-start border-success border-5">
                    <label class="fw-bold mb-2 text-dark"><i class="fas fa-edit text-success me-2"></i>Masukkan Nomor Surat Dinas (Manual):</label>
                    <input type="text" id="nomor_surat" class="form-control form-control-lg text-center fw-bold" placeholder="Contoh: 470/ 015 /YW/2026" required>
                    <small class="text-danger mt-2 fw-bold" id="warning_nomor" style="display:none;"><i class="fas fa-exclamation-circle me-1"></i> Wajib diisi sebelum mencetak surat!</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4 col-sm-6">
            <div class="card card-surat p-4 text-center h-100" onclick="prosesCetak('domisili')">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="fas fa-map-marker-alt"></i></div>
                <h4 class="fw-bold text-dark">Ket. Domisili</h4>
                <p class="text-muted small">Surat keterangan pernyataan tempat tinggal resmi di wilayah desa.</p>
                <span class="btn btn-outline-primary rounded-pill btn-sm mt-2 px-3">Cetak Surat <i class="fas fa-print ms-1"></i></span>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card card-surat p-4 text-center h-100" onclick="prosesCetak('sktm')">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="fas fa-hand-holding-heart"></i></div>
                <h4 class="fw-bold text-dark">SKTM</h4>
                <p class="text-muted small">Surat keterangan tidak mampu untuk jaminan sosial/pendidikan.</p>
                <span class="btn btn-outline-warning rounded-pill btn-sm mt-2 px-3">Cetak Surat <i class="fas fa-print ms-1"></i></span>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card card-surat p-4 text-center h-100" onclick="prosesCetak('sku')">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="fas fa-store"></i></div>
                <h4 class="fw-bold text-dark">Ket. Usaha (SKU)</h4>
                <p class="text-muted small">Surat legalitas kepemilikan unit usaha/UMKM mikro.</p>
                <span class="btn btn-outline-success rounded-pill btn-sm mt-2 px-3">Cetak Surat <i class="fas fa-print ms-1"></i></span>
            </div>
        </div>
    </div>
</div>

<script>
    function prosesCetak(jenis_surat) {
        var inputNomor = document.getElementById('nomor_surat').value;
        var peringatan = document.getElementById('warning_nomor');
        
        if(inputNomor.trim() === '') {
            peringatan.style.display = 'block';
            document.getElementById('nomor_surat').focus();
        } else {
            peringatan.style.display = 'none';
            var url = 'cetak_surat.php?id=<?= $id_warga; ?>&type=' + jenis_surat + '&no=' + encodeURIComponent(inputNomor);
            window.open(url, '_blank');
        }
    }
</script>

</body>
</html>