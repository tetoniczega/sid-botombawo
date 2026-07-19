<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }

$nama_desa = "Botombawo"; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Data Warga - Desa <?= htmlspecialchars($nama_desa); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f7f6; color: #2c3e50; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; width: 260px; background: #ffffff; padding-top: 20px; box-shadow: 4px 0 10px rgba(0,0,0,0.03); z-index: 1000;}
        .sidebar-brand { font-size: 1.4rem; font-weight: 800; text-align: center; color: #059669; text-decoration: none; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; letter-spacing: 0.5px;}
        .sidebar-brand i { font-size: 1.8rem; margin-right: 12px; color: #059669; }
        .nav-link { color: #6c757d; font-weight: 600; padding: 12px 25px; margin: 5px 15px; border-radius: 10px; transition: 0.3s; text-decoration: none; }
        .nav-link:hover { background-color: #f8f9fa; color: #059669; transform: translateX(5px); }
        .nav-link.active { background: linear-gradient(135deg, #059669, #047857); color: white; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }
        
        .main-content { margin-left: 260px; padding: 40px; }
        .card-premium { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; }
        .card-header-custom { background: linear-gradient(135deg, #059669, #047857); color: white; border-radius: 20px 20px 0 0 !important; padding: 20px 30px; border: none; }
        
        .form-floating>.form-control, .form-floating>.form-select { border-radius: 12px; border: 1px solid #e0e0e0; box-shadow: none; }
        .form-floating>.form-control:focus, .form-floating>.form-select:focus { border-color: #059669; box-shadow: 0 0 0 0.25rem rgba(5, 150, 105, 0.15); }
        
        .btn-custom { background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; border-radius: 12px; padding: 12px 25px; font-weight: 700; letter-spacing: 0.5px; transition: 0.3s; }
        .btn-custom:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3); }
        
        .form-control-file-custom { border: 1px dashed #059669; background-color: #f0fdf4; border-radius: 12px; padding: 12px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="index.php" class="sidebar-brand">
            <i class="fas fa-leaf"></i>
            <div><?= htmlspecialchars($nama_desa); ?></div>
        </a>
        <h6 class="px-4 text-muted font-weight-bold text-uppercase mb-1" style="font-size:0.75rem; letter-spacing:1px;">Menu Utama</h6>
        <nav class="nav flex-column mb-auto">
            <a href="index.php" class="nav-link active"><i class="fas fa-users w-20 me-2"></i> Dashboard Data</a>
            
            <?php if(isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'){ ?>
                <a href="verifikasi.php" class="nav-link"><i class="fas fa-user-check w-20 me-2"></i> Verifikasi Pengajuan</a>
                <a href="verifikasi_surat.php" class="nav-link"><i class="fas fa-envelope-open-text w-20 me-2"></i> Verifikasi Surat Masuk</a>
                <a href="tambah_informasi.php" class="nav-link"><i class="fas fa-bullhorn w-20 me-2"></i> Buat Pengumuman</a>
                <a href="broadcast.php" class="nav-link"><i class="fab fa-whatsapp w-20 me-2"></i> Broadcast WhatsApp</a>
            <?php } ?>
            
            <a href="arsip.php" class="nav-link"><i class="fas fa-book w-20 me-2"></i> Buku Arsip Surat</a>
            <a href="kelola_laporan.php" class="nav-link"><i class="fas fa-bullhorn w-20 me-2"></i> Data E-Lapor</a>
            <a href="kelola_apbdes.php" class="nav-link"><i class="fas fa-chart-pie w-20 me-2"></i> Transparansi APBDes</a>
        </nav>
        <div class="position-absolute bottom-0 w-100 p-4">
             <a href="logout.php" class="btn btn-light text-danger w-100 fw-bold shadow-sm" onclick="return confirm('Keluar dari sistem?')">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
             </a>
        </div>
    </div>

    <div class="main-content">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-premium mt-3">
                    <div class="card-header card-header-custom d-flex align-items-center">
                        <i class="fas fa-user-plus fs-3 me-3"></i>
                        <h4 class="mb-0 fw-bold">Tambah Data Penduduk Baru</h4>
                    </div>
                    <div class="card-body p-5">
                        
                        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nik" name="nik" placeholder="NIK" required maxlength="16">
                                        <label for="nik"><i class="fas fa-id-card me-1"></i> Nomor Induk Kependudukan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nama" name="nama_lengkap" placeholder="Nama Lengkap" required>
                                        <label for="nama"><i class="fas fa-font me-1"></i> Nama Sesuai KTP</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="jk" name="jenis_kelamin" required>
                                            <option value="" selected disabled>Pilih...</option>
                                            <option value="Laki-laki">Laki-laki</option>
                                            <option value="Perempuan">Perempuan</option>
                                        </select>
                                        <label for="jk"><i class="fas fa-venus-mars me-1"></i> Jenis Kelamin</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="tgl" name="tanggal_lahir" required>
                                        <label for="tgl"><i class="fas fa-calendar-alt me-1"></i> Tanggal Lahir</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="kerja" name="pekerjaan" placeholder="Pekerjaan">
                                        <label for="kerja"><i class="fas fa-briefcase me-1"></i> Pekerjaan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="status" name="status_kawin">
                                            <option value="Belum Kawin">Belum Kawin</option>
                                            <option value="Kawin">Kawin</option>
                                            <option value="Cerai Hidup">Cerai Hidup</option>
                                            <option value="Cerai Mati">Cerai Mati</option>
                                        </select>
                                        <label for="status"><i class="fas fa-ring me-1"></i> Status Perkawinan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="Nomor WhatsApp" required>
                                <label for="no_hp"><i class="fab fa-whatsapp me-1 text-success fw-bold"></i> Nomor WhatsApp Aktif (Contoh: 08123456789)</label>
                            </div>

                            <div class="form-floating mb-4">
                                <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat" style="height: 100px" required></textarea>
                                <label for="alamat"><i class="fas fa-map-marker-alt me-1"></i> Alamat Lengkap (Jl, RT/RW, Dusun)</label>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold text-muted mb-2"><i class="fas fa-camera me-1"></i> Upload Pas Foto / KTP Warga (Opsional)</label>
                                <input type="file" class="form-control form-control-lg form-control-file-custom" name="foto_warga" accept="image/png, image/jpeg, image/jpg">
                                <small class="text-muted mt-1 d-block">Format yang diizinkan: JPG, JPEG, PNG. Maksimal ukuran 2MB.</small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="index.php" class="text-muted text-decoration-none fw-bold"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                                <button type="submit" class="btn btn-custom">
                                    <i class="fas fa-paper-plane me-2"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>