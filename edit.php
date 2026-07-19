<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php'; 

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM warga WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if(!$data){
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Data Warga - SmartDesa</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f7f6; }
        .main-content { padding: 40px; }
        .card-premium { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); }
        .card-header-custom { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border-radius: 20px 20px 0 0 !important; padding: 20px 30px; border: none; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; }
    </style>
</head>
<body>

    <div class="container main-content">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-premium mt-3">
                    <div class="card-header card-header-custom d-flex align-items-center">
                        <i class="fas fa-user-edit fs-3 me-3"></i>
                        <h4 class="mb-0 fw-bold">Edit Data Penduduk</h4>
                    </div>
                    <div class="card-body p-5">
                        <form action="proses_edit.php" method="POST">
                            <input type="hidden" name="id" value="<?= $data['id']; ?>">
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Nomor Induk Kependudukan (NIK)</label>
                                <input type="text" class="form-control" name="nik" value="<?= $data['nik']; ?>" required maxlength="16">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" value="<?= $data['nama_lengkap']; ?>" required>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin" required>
                                        <option value="Laki-laki" <?= ($data['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="Perempuan" <?= ($data['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" value="<?= $data['tanggal_lahir']; ?>" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pekerjaan</label>
                                    <input type="text" class="form-control" name="pekerjaan" value="<?= $data['pekerjaan']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status Perkawinan</label>
                                    <select class="form-select" name="status_kawin">
                                        <option value="Belum Kawin" <?= ($data['status_kawin'] == 'Belum Kawin') ? 'selected' : ''; ?>>Belum Kawin</option>
                                        <option value="Kawin" <?= ($data['status_kawin'] == 'Kawin') ? 'selected' : ''; ?>>Kawin</option>
                                        <option value="Cerai Hidup" <?= ($data['status_kawin'] == 'Cerai Hidup') ? 'selected' : ''; ?>>Cerai Hidup</option>
                                        <option value="Cerai Mati" <?= ($data['status_kawin'] == 'Cerai Mati') ? 'selected' : ''; ?>>Cerai Mati</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">No. WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-success fw-bold" style="border-radius: 12px 0 0 12px;"><i class="fab fa-whatsapp fs-5"></i></span>
                                    <input type="text" class="form-control" name="no_hp" value="<?= $data['no_hp']; ?>" placeholder="Contoh: 628123456789" style="border-radius: 0 12px 12px 0;">
                                </div>
                                <small class="text-muted d-block mt-1">Gunakan kode negara tanpa tanda + (contoh: <strong>628xxxxxxxx</strong>)</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat" rows="3" required><?= $data['alamat']; ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-light fw-bold px-4" style="border-radius:10px;">Kembali</a>
                                <button type="submit" class="btn btn-warning fw-bold px-4 text-white" style="border-radius:10px;">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>