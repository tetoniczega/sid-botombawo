<?php
include 'koneksi.php';
include 'config.php'; 

date_default_timezone_set('Asia/Jakarta');
function kirimWA($target, $pesan) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $pesan,
        'countryCode' => '62', 
      ),
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . TOKEN_FONNTE 
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

if (isset($_POST['kirim_surat'])) {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);
    $tanggal_sekarang = date('Y-m-d'); 

    $cek_warga = mysqli_query($conn, "SELECT * FROM warga WHERE nik = '$nik'");

    if (mysqli_num_rows($cek_warga) == 0) {
        echo "<script>alert('Gagal! NIK Anda belum terdaftar sebagai warga desa ini. Silakan lakukan pendaftaran mandiri terlebih dahulu.'); window.location='portal.php';</script>";
    } else {
        $data_warga_asli = mysqli_fetch_assoc($cek_warga);
        $nama_asli_database = $data_warga_asli['nama_lengkap'] ?? $data_warga_asli['nama'];
        
        $no_hp_warga = $data_warga_asli['no_hp'] ?? ''; 

        $query_surat = mysqli_query($conn, "INSERT INTO pengajuan_surat 
            (id_pengajuan, nik_warga, nama_warga, jenis_surat, no_surat, keperluan, tanggal_ajuan, status, file_surat, catatan_admin) 
            VALUES 
            (NULL, '$nik', '$nama_asli_database', '$jenis_surat', '', '$keperluan', '$tanggal_sekarang', 'Menunggu', '', '')");

        if ($query_surat) {
            if (!empty($no_hp_warga)) {
                $pesan_notifikasi = "Halo *" . $nama_asli_database . "*,\n\nPengajuan surat *" . $jenis_surat . "* Anda telah sukses masuk ke sistem desa.\n\nDetail:\n- Keperluan: " . $keperluan . "\n- Tanggal: " . date('d-m-Y') . "\n- Status: *Menunggu Verifikasi Admin*\n\nSilakan lakukan tracking berkala langsung di website desa menggunakan NIK Anda. Terima kasih.";
                kirimWA($no_hp_warga, $pesan_notifikasi);
            }

            echo "<script>alert('Pengajuan surat berhasil dikirim! Silakan tunggu verifikasi dari admin desa.'); window.location='portal.php';</script>";
        } else {
            $error_msg = mysqli_error($conn);
            echo "<script>alert('Gagal menyimpan ke database! Error: " . addslashes($error_msg) . "'); window.location='portal.php';</script>";
        }
    }
}

$total_warga = 0; $total_laki = 0; $total_perempuan = 0;
$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM warga");
if($q_total) { $data_total = mysqli_fetch_assoc($q_total); if($data_total['total'] != "") { $total_warga = $data_total['total']; } }
$q_laki = mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jenis_kelamin='Laki-laki'");
if($q_laki) { $data_laki = mysqli_fetch_assoc($q_laki); if($data_laki['total'] != "") { $total_laki = $data_laki['total']; } }
$q_perempuan = mysqli_query($conn, "SELECT COUNT(*) as total FROM warga WHERE jenis_kelamin='Perempuan'");
if($q_perempuan) { $data_perempuan = mysqli_fetch_assoc($q_perempuan); if($data_perempuan['total'] != "") { $total_perempuan = $data_perempuan['total']; } }

$q_apbdes = mysqli_query($conn, "SELECT * FROM apbdes WHERE id=1");
$apbdes = mysqli_fetch_assoc($q_apbdes);

$infra = $apbdes['infrastruktur'] ?? 0;
$pemberdayaan = $apbdes['pemberdayaan'] ?? 0;
$kesehatan = $apbdes['kesehatan'] ?? 0;
$pemerintahan = $apbdes['pemerintahan'] ?? 0;
$tahun_apbdes = $apbdes['tahun'] ?? date('Y');

$total_apbdes = $infra + $pemberdayaan + $kesehatan + $pemerintahan;

$q_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman WHERE tgl_selesai >= CURDATE() OR tgl_selesai IS NULL ORDER BY tanggal");
function formatSingkat($angka) {
    if ($angka >= 1000000000) {
        return round($angka / 1000000000, 2) . ' M';
    } elseif ($angka >= 1000000) {
        return round($angka / 1000000, 1) . ' Jt';
    }
    return number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Resmi Desa Botombawo</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f7f6; overflow-x: hidden; color: #1e293b; position: relative; }
        body::before { content: ''; position: absolute; width: 400px; height: 400px; background: rgba(16, 185, 129, 0.04); border-radius: 50%; top: 15%; right: -100px; z-index: -1; filter: blur(50px); }
        body::after { content: ''; position: absolute; width: 350px; height: 350px; background: rgba(245, 158, 11, 0.03); border-radius: 50%; top: 50%; left: -100px; z-index: -1; filter: blur(60px); }

        .navbar { background: rgba(255, 255, 255, 0.75) !important; backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%); transition: all 0.4s ease; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03); border-bottom: 1px solid rgba(255, 255, 255, 0.5); padding: 16px 0; }
        .navbar.navbar-scrolled { padding: 10px 0; background: rgba(255, 255, 255, 0.92) !important; box-shadow: 0 10px 30px rgba(6, 78, 59, 0.06); }
        .navbar-brand { font-weight: 900; color: #064e3b !important; font-size: 1.45rem; letter-spacing: -0.5px; }
        .nav-link { font-weight: 800; color: #475569 !important; transition: 0.3s; margin: 0 3px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-link:hover { color: #059669 !important; transform: translateY(-1px); }
        
        .hero-section { background: linear-gradient(135deg, rgba(2, 44, 34, 0.88), rgba(6, 78, 59, 0.82)), url('https://images.unsplash.com/photo-1596404368525-4ebdc18a287b?auto=format&fit=crop&w=1920&q=80') center/cover fixed; color: white; padding: 170px 0 200px 0; text-align: center; position: relative; clip-path: polygon(0 0, 100% 0, 100% 92%, 0 100%); }
        .hero-title { font-weight: 900; font-size: 4rem; line-height: 1.15; background: linear-gradient(to right, #ffffff 30%, #fef08a 70%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 15px 30px rgba(0,0,0,0.15); }
        .hero-subtitle { color: #fbbf24; font-weight: 800; font-size: 1.15rem; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 20px; display: inline-block; }
        
        .btn-hero { font-weight: 800; padding: 14px 32px; border-radius: 14px; text-transform: uppercase; letter-spacing: 1px; transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); border: none; font-size: 0.8rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .btn-hero:hover { transform: translateY(-4px); box-shadow: 0 12px 25px rgba(0,0,0,0.25); }
        .btn-hero.btn-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .btn-hero.btn-success:hover { box-shadow: 0 12px 25px rgba(16, 185, 129, 0.4); }
        .btn-hero.btn-info { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; }
        .btn-hero.btn-info:hover { box-shadow: 0 12px 25px rgba(14, 165, 233, 0.4); }
        .btn-hero.btn-warning { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; animation: pulseGlow 2s infinite; }
        .btn-hero.btn-warning:hover { box-shadow: 0 12px 25px rgba(245, 158, 11, 0.4); }
        .btn-hero.btn-danger { background: linear-gradient(135deg, #e11d48, #be123c); color: white; }
        .btn-hero.btn-danger:hover { box-shadow: 0 12px 25px rgba(225, 29, 72, 0.4); }
        .btn-hero.btn-light { background: rgba(255, 255, 255, 0.2); color: white; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .btn-hero.btn-light:hover { background: white; color: #064e3b; }

        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
            70% { box-shadow: 0 0 0 12px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        .stat-box { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border-radius: 24px; padding: 40px 24px; text-align: center; box-shadow: 0 20px 40px rgba(6, 78, 59, 0.04); transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); margin-top: -110px; position: relative; z-index: 10; border: 1px solid rgba(255, 255, 255, 0.6); }
        .stat-box:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 30px 60px rgba(6, 78, 59, 0.12); }
        .stat-box::after { content: ''; position: absolute; bottom: 0; left: 10%; width: 80%; height: 5px; background: linear-gradient(90deg, transparent, #059669, transparent); opacity: 0; transition: 0.4s; }
        .stat-box:hover::after { opacity: 1; }
        
        .icon-wrapper { width: 80px; height: 80px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: center; border-radius: 20px; font-size: 2.2rem; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.03); transition: 0.4s;}
        .stat-box:hover .icon-wrapper { transform: rotate(8deg); }

        .card-pengumuman { border-left: 5px solid #059669; border-radius: 20px !important; background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); border-top: 1px solid rgba(255,255,255,0.6); border-right: 1px solid rgba(255,255,255,0.3); border-bottom: 1px solid rgba(255,255,255,0.3); }
        .card-pengumuman:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(5, 150, 105, 0.08); border-left-color: #fbbf24; }
        
        .service-card { border: 1px solid rgba(255,255,255,0.7); border-radius: 24px; background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); padding: 40px 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.02); transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); position: relative; overflow: hidden; }
        .service-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px rgba(5, 150, 105, 0.08); background: white; border-color: rgba(5, 150, 105, 0.2); }
        .service-card::before { content: ''; position: absolute; top: 0; left: 0; width: 0; height: 4px; background: linear-gradient(90deg, #059669, #fbbf24); transition: 0.4s ease; }
        .service-card:hover::before { width: 100%; }
        
        .img-kades { width: 100%; height: 400px; object-fit: cover; border-radius: 28px; box-shadow: 0 20px 45px rgba(0,0,0,0.12); border: 10px solid white; transition: 0.4s; }
        .img-kades:hover { transform: scale(1.02); }
        
        .modal-content { border-radius: 24px !important; border: 1px solid rgba(255, 255, 255, 0.4) !important; background: rgba(255, 255, 255, 0.98) !important; backdrop-filter: blur(25px); box-shadow: 0 30px 70px rgba(0,0,0,0.18) !important; overflow: hidden; }
        .form-control, .form-select { border-radius: 12px; padding: 14px; border: 1px solid #e2e8f0; background-color: #f8fafc; font-size: 0.9rem; transition: all 0.3s; }
        .form-control:focus, .form-select:focus { background-color: white; border-color: #059669; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1); }

        .floating-action-group { position: fixed; bottom: 30px; right: 30px; z-index: 1050; display: flex; flex-direction: column; gap: 12px; }
        .btn-floating { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: white !important; box-shadow: 0 6px 16px rgba(0,0,0,0.16); transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); border: none; text-decoration: none; position: relative; }
        .btn-floating:hover { transform: scale(1.1) translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.24); }
        .btn-floating-bell { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .btn-floating-wa { background: linear-gradient(135deg, #25d366, #128c7e); }
        .badge-floating-count { position: absolute; top: -3px; right: -3px; background-color: #e11d48; color: white; border-radius: 50%; padding: 3px 7px; font-size: 0.65rem; font-weight: 800; border: 2px solid white; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-leaf text-success me-2"></i>Botombawo</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><i class="fas fa-bars fs-4 text-success"></i></button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item"><a class="nav-link" href="#">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalPengumuman">Pengumuman</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil">Profil</a></li>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="#transparansi"><i class="fas fa-chart-pie me-1"></i> APBDes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#layanan">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link text-success fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalAjukanSurat">Ajukan Surat</a></li>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalDaftarWarga">Daftar Warga</a></li>
                    <li class="nav-item"><a class="nav-link text-danger fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalELapor">E-Lapor</a></li>
                    <li class="nav-item"><a class="nav-link text-info fw-bold" href="#" data-bs-toggle="modal" data-bs-target="#modalCekStatus">Cek Status</a></li>
                    <li class="nav-item ms-lg-2 mt-3 mt-lg-0">
                        <a class="btn btn-success btn-sm rounded-pill py-2 px-4 shadow-sm fw-bold" href="login.php" style="background: #059669; border:none; font-size:0.8rem;"><i class="fas fa-user-shield me-1"></i> Login Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container" data-aos="zoom-in" data-aos-duration="1000">
            <span class="badge bg-white text-success px-3 py-2 rounded-pill mb-3 fw-bold shadow-sm" style="letter-spacing: 1.5px; font-size: 0.75rem;"><i class="fas fa-star text-warning me-1"></i> SMART GOVERNANCE</span>
            <h1 class="hero-title mb-2">Desa Botombawo</h1>
            <p class="hero-subtitle mb-3">"Data Tuwu Ono Niha, YA'AHOWU"</p>
            <p class="lead mb-5 fw-light mx-auto text-white-50" style="max-width: 680px; font-size: 1.15rem; line-height: 1.6;">Mewujudkan pelayanan publik administrasi kependudukan terpadu yang cepat, transparan, dan terdigitalisasi secara instan.</p>
            
            <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                <button type="button" class="btn btn-success btn-hero" data-bs-toggle="modal" data-bs-target="#modalAjukanSurat">
                    <i class="fas fa-envelope-open-text fs-6"></i>Ajukan Surat
                </button>
                <button type="button" class="btn btn-info btn-hero" data-bs-toggle="modal" data-bs-target="#modalCekStatus">
                    <i class="fas fa-search-location fs-6"></i>Cek Status
                </button>
                <a href="#layanan" class="btn btn-light btn-hero"><i class="fas fa-concierge-bell fs-6"></i>Layanan Desa</a>
                <button type="button" class="btn btn-warning btn-hero" data-bs-toggle="modal" data-bs-target="#modalDaftarWarga">
                    <i class="fas fa-user-plus fs-6"></i>Daftar Warga
                </button>
                <button type="button" class="btn btn-danger btn-hero" data-bs-toggle="modal" data-bs-target="#modalELapor">
                    <i class="fas fa-bullhorn fs-6"></i>E-Lapor
                </button>
                <button type="button" class="btn btn-warning fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLacak" style="border-radius: 10px; padding: 12px 24px;">
                    <i class="fas fa-search-location me-2"></i> Cek Status Laporan Warga
                </button>
            </div>
        </div>
    </section>

    <section id="statistik" class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-box">
                    <div class="icon-wrapper text-success"><i class="fas fa-users"></i></div>
                    <h1 class="fw-bold text-dark display-5 mb-1"><span class="counter" data-val="<?= $total_warga; ?>">0</span></h1>
                    <p class="text-muted mb-0 fw-bold text-uppercase" style="letter-spacing: 1.5px; font-size: 0.75rem;">Total Penduduk</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-box">
                    <div class="icon-wrapper" style="color: #0ea5e9;"><i class="fas fa-male"></i></div>
                    <h1 class="fw-bold text-dark display-5 mb-1"><span class="counter" data-val="<?= $total_laki; ?>">0</span></h1>
                    <p class="text-muted mb-0 fw-bold text-uppercase" style="letter-spacing: 1.5px; font-size: 0.75rem;">Penduduk Laki-laki</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-box">
                    <div class="icon-wrapper" style="color: #e11d48;"><i class="fas fa-female"></i></div>
                    <h1 class="fw-bold text-dark display-5 mb-1"><span class="counter" data-val="<?= $total_perempuan; ?>">0</span></h1>
                    <p class="text-muted mb-0 fw-bold text-uppercase" style="letter-spacing: 1.5px; font-size: 0.75rem;">Penduduk Perempuan</p>
                </div>
            </div>
        </div>
    </section>

    <section id="transparansi" class="container" style="margin-top: 120px;">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(245, 158, 11, 0.08); color: #d97706; font-weight: 800; letter-spacing: 1px; font-size:0.75rem;">TRANSPARANSI PUBLIK</span>
            <h2 class="fw-bold text-dark mb-2" style="font-size: 2.3rem;">Alokasi Grafik Anggaran Dana Desa</h2>
            <p class="text-muted mx-auto" style="max-width: 550px;">Wujud keterbukaan informasi publik and akuntabilitas keuangan pada Tahun Anggaran <?= $tahun_apbdes; ?>.</p>
        </div>

        <div class="row align-items-center bg-white rounded-4 shadow-sm p-4 border" data-aos="fade-up" data-aos-delay="100" style="border-radius: 24px !important;">
            <div class="col-lg-5 mb-4 mb-lg-0 text-center">
                <div style="height: 280px; position: relative;">
                    <canvas id="apbdesChart"></canvas>
                    <div style="position: absolute; top: 52%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <span class="text-muted fw-bold d-block text-uppercase" style="font-size: 0.7rem; letter-spacing:1px;">Total Dana</span>
                        <span class="text-success fw-bold" style="font-size: 1.6rem; letter-spacing:-0.5px;">Rp <?= formatSingkat($total_apbdes); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 ps-lg-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3" style="font-size:1.05rem;"><i class="fas fa-wallet text-success me-2"></i>Rincian Realisasi Sektor</h5>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 rounded-3" style="background: #f8fafc; border-left: 4px solid #3b82f6;">
                    <div><i class="fas fa-hard-hat text-primary me-2"></i> <span class="fw-bold text-secondary" style="font-size:0.85rem;">Infrastruktur & Pembangunan</span></div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.9rem;">Rp <?= formatSingkat($infra); ?></h6>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 rounded-3" style="background: #f8fafc; border-left: 4px solid #10b981;">
                    <div><i class="fas fa-seedling text-success me-2"></i> <span class="fw-bold text-secondary" style="font-size:0.85rem;">Pemberdayaan Masyarakat</span></div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.9rem;">Rp <?= formatSingkat($pemberdayaan); ?></h6>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2 p-3 rounded-3" style="background: #f8fafc; border-left: 4px solid #ef4444;">
                    <div><i class="fas fa-heartbeat text-danger me-2"></i> <span class="fw-bold text-secondary" style="font-size:0.85rem;">Kesehatan & PMT Stunting</span></div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.9rem;">Rp <?= formatSingkat($kesehatan); ?></h6>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #f8fafc; border-left: 4px solid #f59e0b;">
                    <div><i class="fas fa-user-shield text-warning me-2"></i> <span class="fw-bold text-secondary" style="font-size:0.85rem;">Penyelenggaraan Pemerintahan</span></div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.9rem;">Rp <?= formatSingkat($pemerintahan); ?></h6>
                </div>
            </div>
        </div>
    </section>

    <section id="profil" class="container" style="margin-top: 120px; margin-bottom: 80px;">
        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="position-relative">
                    <img src="kades.PNG" alt="Kepala Desa" class="img-kades">
                    <div class="position-absolute bottom-0 start-0 text-white p-3 shadow" style="background: linear-gradient(135deg, #064e3b, #059669); border-radius: 0 16px 0 16px; transform: translateY(10px); left: 10px !important;">
                        <h6 class="fw-bold mb-0" style="font-size:0.95rem;">Bapak Kepala Desa</h6>
                        <small class="text-warning" style="font-size:0.75rem;">Kepala Desa Botombawo</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="badge px-3 py-2 rounded-pill mb-3" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-weight: 800; letter-spacing: 1px; font-size:0.75rem;">PROFIL PIMPINAN</span>
                <h2 class="fw-bold text-dark mb-3" style="font-size: 2.3rem; line-height: 1.25;">Membangun Botombawo yang Mandiri, Inovatif & Sejahtera</h2>
                <p class="text-muted mb-4" style="line-height: 1.75; font-size:0.95rem;">Slogan <b class="text-success">"Data Tuwu Ono Niha, YA'AHOWU"</b> adalah representasi integritas tinggi dalam melayani masyarakat. Melalui implementasi platform Smart Desa terintegrasi, seluruh urusan birokrasi surat-menyurat didesain pangkas waktu dan terverifikasi aman.</p>
                <div class="row g-3">
                    <div class="col-sm-6"><div class="d-flex align-items-center p-3 rounded-3 border bg-white shadow-sm" style="border-radius:16px !important;"><div class="text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="background: #059669; width: 45px; height: 45px; border-radius:12px !important;"><i class="fas fa-bolt"></i></div><div><h6 class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">Item Birokrasi Instan</h6><small class="text-muted" style="font-size:0.75rem;">Birokrasi digital mandiri</small></div></div></div>
                    <div class="col-sm-6"><div class="d-flex align-items-center p-3 rounded-3 border bg-white shadow-sm" style="border-radius:16px !important;"><div class="text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="background: #f59e0b; width: 45px; height: 45px; border-radius:12px !important;"><i class="fas fa-shield-alt"></i></div><div><h6 class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">Enkripsi Data Aman</h6><small class="text-muted" style="font-size:0.75rem;">Keamanan data KTP privat</small></div></div></div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="container mt-5 pt-4 mb-5 pb-5">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="badge px-3 py-2 rounded-pill mb-2" style="background: rgba(5, 150, 105, 0.08); color: #059669; font-weight: 800; letter-spacing: 1px; font-size: 0.75rem;">PERSYARATAN DOKUMEN</span>
            <h2 class="fw-bold text-dark" style="font-size: 2.3rem;">Persyaratan Administrasi Berkas</h2>
            <p class="text-muted mt-2">Mohon baca dan siapkan kelengkapan file berikut sebelum mengisi formulir digital.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100"><div class="service-card h-100 text-center"><div class="mb-4 d-inline-block p-3 rounded-4" style="background: #ecfdf5; color: #059669; border-radius:18px !important;"><i class="fas fa-map-marked-alt fs-2"></i></div><h4 class="fw-bold mb-2 text-dark" style="font-size:1.25rem;">Ket. Domisili</h4><p class="text-muted fs-6 mb-3" style="font-size: 0.85rem;">Surat resmi bukti kepindahan/tempat tinggal berkala warga.</p><ul class="text-muted text-start ps-3 mb-0 mx-auto" style="font-size: 0.85rem; max-width: 180px;"><li>KTP Asli Scan</li><li>Kartu Keluarga</li><li>Pengantar Dusun</li></ul></div></div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200"><div class="service-card h-100 text-center"><div class="mb-4 d-inline-block p-3 rounded-4" style="background: #fffbeb; color: #d97706; border-radius:18px !important;"><i class="fas fa-hand-holding-heart fs-2"></i></div><h4 class="fw-bold mb-2 text-dark" style="font-size:1.25rem;">Ket. Tidak Mampu</h4><p class="text-muted mb-3" style="font-size: 0.85rem;">Digunakan klaim KIP kuliah, jaminan kesehatan, atau bansos.</p><ul class="text-muted text-start ps-3 mb-0 mx-auto" style="font-size: 0.85rem; max-width: 180px;"><li>KTP & KK Aktif</li><li>Pernyataan RT/RW</li><li>Foto Rumah</li></ul></div></div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300"><div class="service-card h-100 text-center"><div class="mb-4 d-inline-block p-3 rounded-4" style="background: #f0fdf4; color: #16a34a; border-radius:18px !important;"><i class="fas fa-store fs-2"></i></div><h4 class="fw-bold mb-2 text-dark" style="font-size:1.25rem;">Ket. Usaha (SKU)</h4><p class="text-muted mb-3" style="font-size: 0.85rem;">Legalitas krusial pengajuan suntikan modal UMKM KUR Bank.</p><ul class="text-muted text-start ps-3 mb-0 mx-auto" style="font-size: 0.85rem; max-width: 180px;"><li>KTP Pemohon</li><li>Surat Pengantar</li><li>Foto Lokasi Usaha</li></ul></div></div>
        </div>
    </section>

    <div class="modal fade" id="modalPengumuman" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4" style="background: linear-gradient(135deg, #059669, #10b981); color: white;">
                    <h4 class="modal-title fw-bold" style="font-size:1.25rem;"><i class="fas fa-bullhorn me-2"></i> Papan Pengumuman Digital</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group rounded-0 border-0">
                        <?php 
                        if ($q_pengumuman && mysqli_num_rows($q_pengumuman) > 0) {
                            while ($row_pengumuman = mysqli_fetch_assoc($q_pengumuman)) { 
                                $hari_tgl = date('d', strtotime($row_pengumuman['tgl_selesai']));
                                $bulan_thn = date('M Y', strtotime($row_pengumuman['tgl_selesai']));
                        ?>
                            <div class="list-group-item list-group-item-action p-4 border-start-0 border-end-0 border-top-0 position-relative" style="transition: all 0.3s ease;">
                                <div class="row align-items-center">
                                    <div class="col-auto text-center border-end pe-4 d-none d-sm-block" style="min-width: 90px;">
                                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.8rem; line-height: 1;"><?php echo $hari_tgl; ?></h3>
                                        <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;"><?php echo $bulan_thn; ?></small>
                                    </div>
                                    <div class="col ps-sm-4">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            <small class="text-muted d-sm-none fw-medium"><i class="far fa-calendar-alt me-1"></i> <?php echo $hari_tgl . ' ' . $bulan_thn; ?></small>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 fw-bold" style="font-size: 0.7rem;"><?php echo htmlspecialchars($row_pengumuman['kategori']); ?></span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem; line-height: 1.4;"><?php echo htmlspecialchars($row_pengumuman['judul']); ?></h5>
                                        <p class="text-secondary small mb-0 text-truncate" style="max-width: 500px;">
                                            <?php echo strip_tags($row_pengumuman['isi_pengumuman']); ?>
                                        </p>
                                    </div>
                                    <div class="col-auto text-end mt-3 mt-md-0">
                                        <a href="detail_pengumuman.php?id=<?php echo $row_pengumuman['id']; ?>" class="btn btn-sm btn-light text-success fw-bold px-3 py-2 border border-success border-opacity-10" style="border-radius: 10px;">
                                            Buka <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            } 
                        } else { 
                            if (!$q_pengumuman) {
                                echo "<div class='p-3 text-center bg-danger bg-opacity-10 text-danger small'><i class='fas fa-exclamation-triangle me-1'></i> Sistem Log: " . mysqli_error($conn) . "</div>";
                            }
                        ?>
                            <div class="p-5 text-center bg-white">
                                <div class="text-muted mb-2" style="font-size: 2rem;"><i class="fas fa-bullhorn opacity-25"></i></div>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Pengumuman Terbaru</h6>
                                <p class="text-muted small mb-0">Informasi resmi dari pemerintah desa akan tertera di sini setelah dirilis.</p>
                            </div>
                        <?php 
                        } 
                        ?>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm fw-bold px-3" data-bs-dismiss="modal" style="border-radius:10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAjukanSurat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-success" style="font-size:1.25rem;"><i class="fas fa-file-signature me-2"></i> Formulir Pengajuan Surat Mandiri</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <p class="text-muted small mb-4">Pastikan data Anda sudah terverifikasi di sistem penduduk desa sebelum mengirim formulir.</p>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">NIK (Nomor Induk Kependudukan)</label>
                            <input type="text" class="form-control" name="nik" maxlength="16" placeholder="Masukkan 16 digit NIK Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap sesuai KTP" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Jenis Surat Yang Diajukan</label>
                            <select class="form-select" name="jenis_surat" required>
                                <option value="">-- Pilih Jenis Surat --</option>
                                <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                                <option value="Surat Pengantar Domisili">Surat Pengantar Domisili</option>
                                <option value="Surat Keterangan Usaha (SKU)">Surat Keterangan Usaha (SKU)</option>
                                <option value="Surat Pengantar Kelakuan Baik">Surat Pengantar Kelakuan Baik</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small">Keperluan / Alasan Pengajuan</label>
                            <textarea class="form-control" name="keperluan" rows="3" placeholder="Contoh: Keperluan kelengkapan syarat pencairan beasiswa." required></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius:12px; font-size:0.85rem;">Batal</button>
                            <button type="submit" name="kirim_surat" class="btn btn-success fw-bold px-4 text-white" style="border-radius:12px; background: #059669; border:none; font-size:0.85rem;">Kirim Permohonan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDaftarWarga" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4" style="background: linear-gradient(135deg, #059669, #10b981); color: white;">
                    <h4 class="modal-title fw-bold" style="font-size:1.25rem;"><i class="fas fa-user-edit me-2"></i> Registrasi Pendataan Warga Mandiri</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-4">
                    <p class="text-muted small mb-4">Silakan ketik data diri Anda secara lengkap dan valid sesuai KTP fisik milik Anda.</p>
                    <form action="proses_daftar_mandiri.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-id-card text-success me-1"></i> NIK (16 Digit)</label>
                                <input type="text" class="form-control" name="nik" placeholder="Contoh: 3201..." required maxlength="16">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-font text-success me-1"></i> Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" placeholder="Sesuai KTP" required>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-venus-mars text-success me-1"></i> Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin" required>
                                    <option value="" selected disabled>Pilih...</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-calendar-alt text-success me-1"></i> Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tanggal_lahir" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-briefcase text-success me-1"></i> Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" placeholder="Sesuai KTP" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small"><i class="fas fa-ring text-success me-1"></i> Status Perkawinan</label>
                                <select class="form-select" name="status_kawin" required>
                                    <option value="" selected disabled>Pilih...</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <span style="color: #0f8b57; margin-right: 5px;">📞</span> No. HP / WhatsApp
                            </label>
                            <input type="text" class="form-control" name="no_hp" placeholder="Contoh: 081234567xxx" required style="border-radius: 8px; padding: 10px;">
                        </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fas fa-map-marker-alt text-success me-1"></i> Alamat Lengkap Rumah</label>
                            <textarea class="form-control" name="alamat" rows="2" placeholder="Nama Jalan, RT/RW, Dusun" required></textarea>
                        </div>
                        
                        <div class="mb-4 p-3" style="background: #f8fafc; border: 2px dashed #10b981; border-radius: 14px;">
                            <label class="form-label fw-bold text-success small"><i class="fas fa-camera me-1"></i> Upload Foto KTP / Pas Foto Wajah</label>
                            <input type="file" class="form-control border-success" name="foto_warga" accept="image/png, image/jpeg, image/jpg" required>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">Maksimal file berkas berkuran 2MB format berkas (JPG/PNG).</small>
                        </div>
                        
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius:12px; font-size:0.85rem;">Batal</button>
                            <button type="submit" class="btn btn-success fw-bold px-4 text-white" style="border-radius:12px; background: #059669; border:none; font-size:0.85rem;">
                                <i class="fas fa-paper-plane me-2"></i>Ajukan Validasi Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalELapor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-danger" style="font-size:1.25rem;"><i class="fas fa-bullhorn me-2"></i> Pusat Pengaduan & Aspirasi Masyarakat</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <p class="text-muted small mb-4">Laporkan kerusakan fasilitas umum, kendala pelayanan, atau aduan lingkungan secara realtime.</p>
                    <form action="proses_lapor.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger small"><i class="fas fa-id-card me-1"></i> NIK Pelapor Valid</label>
                            <input type="text" class="form-control" name="nik" placeholder="Masukkan 16 digit NIK Anda" required maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fas fa-heading text-danger me-1"></i> Judul Pokok Pengaduan</label>
                            <input type="text" class="form-control" name="judul_laporan" placeholder="Contoh: Tiang Lampu Jalan Roboh" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small"><i class="fas fa-edit text-danger me-1"></i> Deskripsi Kronologi Laporan</label>
                            <textarea class="form-control" name="isi_laporan" rows="4" placeholder="Tulis rincian keluhan secara transparan di sini..." required></textarea>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label fw-bold text-secondary small"><i class="fas fa-map-marker-alt text-danger me-1"></i> Koordinat Lokasi Fisik (Otomatis GPS)</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control bg-white" name="latitude" id="lat" placeholder="Latitude" readonly required>
                                <input type="text" class="form-control bg-white" name="longitude" id="long" placeholder="Longitude" readonly required>
                                <button type="button" class="btn btn-dark fw-bold btn-sm px-3" onclick="getLocation()" style="border-radius:10px !important;"><i class="fas fa-crosshairs me-1"></i> Ambil GPS</button>
                            </div>
                            <small class="text-muted d-block" id="geo-msg" style="font-size: 0.75rem;">Klik tombol <b>Ambil GPS</b> untuk akurasi peta verifikasi tim lapangan.</small>
                        </div>
                        <div class="mb-4 p-3 rounded-3" style="background: #fff5f5; border: 2px dashed #e11d48;">
                            <label class="form-label fw-bold text-danger small"><i class="fas fa-camera me-1"></i> Lampiran Bukti Dokumentasi Lapangan</label>
                            <input type="file" class="form-control border-danger" name="foto_lampiran" accept="image/*" required>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius:12px; font-size:0.85rem;">Batal</button>
                            <button type="submit" class="btn btn-danger fw-bold px-4 text-white" style="border-radius:12px; background: #e11d48; border:none; font-size:0.85rem;"><i class="fas fa-paper-plane me-2"></i>Kirim Aduan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCekStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold text-info" style="font-size:1.25rem;"><i class="fas fa-tasks me-2"></i> Tracking Status Berkas Permohonan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">Ketik Nomor NIK Warga Pemohon</label>
                        <div class="input-group">
                            <input type="text" id="nik_lacak" class="form-control" maxlength="16" placeholder="Ketik 16 digit NIK">
                            <button class="btn btn-info text-white fw-bold px-4" type="button" id="btn_cari_status" style="border-radius:0 12px 12px 0;"><i class="fas fa-search me-1"></i> Lacak</button>
                        </div>
                    </div>
                    <div id="hasil_lacak" class="mt-3">
                        <p class="text-muted text-center small">Masukkan data NIK lalu klik lacak untuk melihat status mutakhir, Boss.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="floating-action-group">
        <button type="button" class="btn-floating btn-floating-bell" data-bs-toggle="modal" data-bs-target="#modalPengumuman" title="Papan Pengumuman">
            <i class="fas fa-bell"></i>
            <?php if ($q_pengumuman && mysqli_num_rows($q_pengumuman) > 0): ?>
                <span class="badge-floating-count"><?php echo mysqli_num_rows($q_pengumuman); ?></span>
            <?php endif; ?>
        </button>
        
        <a href="https://wa.me/6285864948803" target="_blank" class="btn-floating btn-floating-wa" title="Hubungi via WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <footer class="py-5 text-white" style="background-color: #021e17; font-family: 'Nunito', sans-serif; position: relative; z-index: 20; border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-md-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-leaf text-success fs-3 me-2" style="color: #10b981 !important;"></i>
                        <h4 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px;">Desa Botombawo</h4>
                    </div>
                    <p class="text-white-50 small mb-0" style="font-size: 0.85rem; line-height: 1.6;">
                        Sistem Informasi Administrasi Layanan Digital Terpadu.<br>
                        Desa Botombawo, Kecamatan Sitolu Ori,<br>
                        Kabupaten Nias Utara, Sumatera Utara.
                    </p>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <h6 class="text-uppercase fw-bold mb-3" style="color: #10b981 !important; letter-spacing: 1.5px; font-size: 0.75rem;">Hubungi Kami</h6>
                    <ul class="list-unstyled text-white-50 small mb-3" style="font-size: 0.85rem;">
                        <li class="mb-2 d-flex align-items-center gap-2"><i class="fas fa-map-marker-alt text-success" style="color: #10b981 !important; width: 15px;"></i><span>Jl. Utama Desa Botombawo, Kec. Sitolu Ori</span></li>
                        <li class="mb-2 d-flex align-items-center gap-2"><i class="fas fa-envelope text-success" style="color: #10b981 !important; width: 15px;"></i><span>info@desabotombawo.go.id</span></li>
                        <li class="mb-2 d-flex align-items-center gap-2"><i class="fab fa-whatsapp text-success" style="color: #10b981 !important; width: 15px;"></i><span>+62 858-6494-8803</span></li>
                    </ul>
                    <a href="https://wa.me/6285864948803" target="_blank" class="btn btn-sm btn-success fw-bold px-3 py-2 text-white" style="border-radius: 10px; background-color: #059669; border: none; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);"><i class="fab fa-whatsapp fs-6"></i> Hubungi Layanan Desa</a>
                </div>
                <div class="col-md-3 text-md-end text-start align-self-end" data-aos="fade-up" data-aos-delay="300">
                    <p class="text-white-50 small mb-0" style="font-size: 0.8rem;">&copy; 2026 Desa Botombawo.<br>All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        AOS.init({ once: true, offset: 40, duration: 800 });
        
        $(window).scroll(function() { 
            if ($(document).scrollTop() > 40) { 
                $('.navbar').addClass('navbar-scrolled'); 
            } else { 
                $('.navbar').removeClass('navbar-scrolled'); 
            } 
        });

        $(document).ready(function() {
            $('.counter').each(function () {
                var $this = $(this);
                var countTo = parseInt($this.attr('data-val')) || 0;
                if(countTo === 0) { $this.text('0'); return; }
                $({ countNum: 0 }).animate({ countNum: countTo }, {
                    duration: 1500, easing: 'linear',
                    step: function() { $this.text(Math.floor(this.countNum)); },
                    complete: function() { $this.text(this.countNum); }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('apbdesChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Infrastruktur', 'Pemberdayaan', 'Kesehatan', 'Pemerintahan'],
                    datasets: [{
                        data: [<?= $infra; ?>, <?= $pemberdayaan; ?>, <?= $kesehatan; ?>, <?= $pemerintahan; ?>],
                        backgroundColor: ['#3b82f6', '#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 4, borderColor: '#ffffff', hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 15, font: { family: 'Nunito', weight: 'bold', size: 11 } } } },
                    cutout: '78%', animation: { animateScale: true, animateRotate: true }
                }
            });
        });

        document.getElementById('btn_cari_status').addEventListener('click', function() {
            var nik = document.getElementById('nik_lacak').value;
            if(nik == "") { alert("NIK wajib diisi, Boss!"); return; }
            var hasilDiv = document.getElementById('hasil_lacak');
            hasilDiv.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-info" role="status"></div><p class="text-muted small mt-2">Membaca database...</p></div>';
            fetch('proses_lacak_surat.php?nik=' + nik)
                .then(response => response.text())
                .then(data => { hasilDiv.innerHTML = data; })
                .catch(error => {
                    console.error('Error:', error);
                    hasilDiv.innerHTML = '<p class="text-danger text-center small">Gagal terkoneksi ke tracking database.</p>';
                });
        });

        function getLocation() {
            if (navigator.geolocation) {
                document.getElementById("geo-msg").innerHTML = "<span class='text-primary fw-bold'><i class='fas fa-spinner fa-spin'></i> Menghubungi satelit GPS... Izinkan di browser Anda, Boss.</span>";
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else { 
                document.getElementById("geo-msg").innerHTML = "<span class='text-danger'>Akses lokasi ditolak browser.</span>";
            }
        }

        function showPosition(position) {
            document.getElementById("lat").value = position.coords.latitude;
            document.getElementById("long").value = position.coords.longitude;
            document.getElementById("geo-msg").innerHTML = "<span class='text-success fw-bold'><i class='fas fa-check-circle'></i> Titik koordinat GPS terkunci sempurna!</span>";
        }

        function showError(error) {
            document.getElementById("geo-msg").innerHTML = "<span class='text-danger fw-bold'><i class='fas fa-times-circle'></i> GPS gagal melacak. Aktifkan setelan lokasi perangkat Anda.</span>";
        }
    </script>
<div class="modal fade" id="modalLacak" tabindex="-1" aria-labelledby="modalLacakLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
            <!-- Header Pop-up -->
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #198754, #146c43); padding: 20px;">
                <h5 class="modal-title fw-bold" id="modalLacakLabel">
                    <i class="fas fa-file-invoice me-2"></i> E-Lapor: Lacak Status Pengaduan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Silakan masukkan 16 digit NIK Anda untuk melacak progres tindak lanjut aduan secara real-time.</p>
                
                <form action="" method="POST" class="row g-2 mb-4">
                    <div class="col-sm-9">
                        <input type="text" name="nik_cari" class="form-control" placeholder="Ketik 16 Digit NIK Anda..." style="border-radius:10px; padding: 10px;" required maxlength="16" autocomplete="off" value="<?= isset($_POST['nik_cari']) ? htmlspecialchars($_POST['nik_cari']) : ''; ?>">
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" name="btn_lacak" class="btn btn-success w-100 fw-bold" style="border-radius:10px; padding: 10px;">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </form>
                <?php 
                if (isset($_POST['btn_lacak'])) {
                    $nik_cari = mysqli_real_escape_string($conn, $_POST['nik_cari']);
                    
                    $q_lacak = mysqli_query($conn, "SELECT * FROM pengaduan WHERE nik_warga = '$nik_cari' ORDER BY id DESC");

                    if (mysqli_num_rows($q_lacak) > 0) {
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-hover align-middle">';
                        echo '<thead>
                                <tr>
                                    <th>No / Tanggal</th>
                                    <th>Judul Laporan</th>
                                    <th>Status</th>
                                    <th>Balasan Admin</th> </tr>
                               </thead>
                              <tbody>';
                        
                        while ($r_lacak = mysqli_fetch_assoc($q_lacak)) {
                            $status = !empty($r_lacak['status_laporan']) ? $r_lacak['status_laporan'] : 'Menunggu';

                            if ($status == "Menunggu") {
                                $badge_warna = "bg-warning text-dark";
                            } elseif ($status == "Diproses") {
                                $badge_warna = "bg-primary text-white";
                            } elseif ($status == "Selesai") {
                                $badge_warna = "bg-success text-white";
                            } else {
                                $badge_warna = "bg-secondary text-white";
                            }
                            $balasan_admin = !empty($r_lacak['tanggap_admin']) ? htmlspecialchars($r_lacak['tanggap_admin']) : "<span class='text-muted small'><i>Belum ditanggapi admin</i></span>";
                        echo "<tr>
                            <td>" . $r_lacak['tgl_lapor'] . "</td> 
                            <td>" . htmlspecialchars($r_lacak['judul_laporan']) . "</td>
                            <td><span class='badge $badge_warna fw-bold' style='padding: 6px 12px; border-radius:5px;'>" . $status . "</span></td>
                            <td>" . $balasan_admin . "</td> </tr>";
                        }
                        echo '</tbody></table></div>';
                    } else {
                        echo '<div class="alert alert-danger text-center mb-0" style="border-radius:10px;">
                                <i class="fas fa-exclamation-triangle me-2"></i> NIK <strong>' . htmlspecialchars($nik_cari) . '</strong> tidak terdaftar dalam sistem pengaduan.
                              </div>';
                    }
                }
                ?>
            </div>
            
            <div class="modal-footer bg-light" style="padding: 15px;">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php if (isset($_POST['btn_lacak'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modalLacakE = new bootstrap.Modal(document.getElementById('modalLacak'));
        modalLacakE.show();
    });
</script>
<?php endif; ?>
</body>
</html>