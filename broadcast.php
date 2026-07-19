<?php 
session_start();
if($_SESSION['status'] != "login" || strtolower($_SESSION['role']) != 'admin'){ 
    header("location:login.php"); 
    exit; 
}
include 'koneksi.php'; 
include 'config.php'; 

$nama_desa = "Botombawo"; 

if(isset($_POST['kirim_broadcast'])){
    $pesan_utama = $_POST['pesan'];
    $username = $_SESSION['nama_pengguna'];
    $waktu = date('Y-m-d H:i:s');
    $url_file_kirim = "";
    if(isset($_FILES['lampiran_file']) && $_FILES['lampiran_file']['error'] == 0){
        $nama_original = $_FILES['lampiran_file']['name'];
        $nama_bersih   = str_replace(' ', '_', $nama_original);
        $nama_file     = time() . '_' . $nama_bersih;
        $tmp_file      = $_FILES['lampiran_file']['tmp_name'];
        $folder_tujuan = "uploads/dokumen/";

        if (!is_dir($folder_tujuan)) {
            mkdir($folder_tujuan, 0777, true);
        }

        if(move_uploaded_file($tmp_file, $folder_tujuan . $nama_file)){
            $protokol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
            $domain   = $_SERVER['HTTP_HOST'];
            $path_project = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            
            $url_file_kirim = $protokol . "://" . $domain . $path_project . "/" . $folder_tujuan . $nama_file;
        }
    }

    $query_warga = mysqli_query($conn, "SELECT nama_lengkap, no_hp FROM warga WHERE no_hp IS NOT NULL AND no_hp != ''");
    
    $berhasil = 0;
    $gagal = 0;
    $pesan_error = ""; 

    while($warga = mysqli_fetch_assoc($query_warga)){
        $no_tujuan = $warga['no_hp'];
        $nama_warga = $warga['nama_lengkap'];

        $pesan_personal = str_replace("{nama}", $nama_warga, $pesan_utama);

        $payload = array(
            'target' => $no_tujuan,
            'message' => $pesan_personal,
            'countryCode' => '62',
        );

        if(!empty($url_file_kirim)){
            $payload['url'] = $url_file_kirim;
        }

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
          CURLOPT_POSTFIELDS => $payload,
          CURLOPT_HTTPHEADER => array(
            "Authorization: " . TOKEN_FONNTE 
          ),
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_SSL_VERIFYHOST => 0,
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        if(isset($result['status']) && $result['status'] == true){
            $berhasil++;
        } else {
            $gagal++;
            $pesan_error = isset($result['reason']) ? $result['reason'] : 'Koneksi ke API Gagal/Token Salah';
        }
    }

    @mysqli_query($conn, "INSERT INTO log_aktivitas (username, tindakan, waktu) VALUES ('$username', 'Melakukan Broadcast WA dengan lampiran ke $berhasil warga', '$waktu')");

    if ($gagal > 0 && $berhasil == 0) {
        echo "<script>
                alert('Gagal Mengirim! Alasan dari Fonnte: $pesan_error');
                window.location='broadcast.php';
              </script>";
    } else {
        echo "<script>
                alert('Proses Selesai! Sukses Terkirim: $berhasil Warga. Gagal: $gagal Warga.');
                window.location='index.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Broadcast WA - Desa <?= htmlspecialchars($nama_desa); ?></title>
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
        .btn-wa { background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; font-weight: 700; border-radius: 10px; padding: 12px 25px; }
        .btn-wa:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 10px rgba(16, 185, 129, 0.2); }
        .info-box { background-color: #eff6ff; border-left: 4px solid #3b82f6; color: #1e3a8a; padding: 15px; border-radius: 8px; font-size: 0.9rem; }
        .warning-box { background-color: #fff7ed; border-left: 4px solid #f97316; color: #7c2d12; padding: 15px; border-radius: 8px; font-size: 0.9rem; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold text-dark mb-0"><i class="fab fa-whatsapp text-success me-2"></i>Pusat Broadcast WhatsApp Desa</h2>
            <p class="text-muted">Kirimkan info pengumuman secara massal disertai lampiran file dokumen/gambar di Desa <strong><?= htmlspecialchars($nama_desa); ?></strong></p>
        </div>

        <div class="card card-premium p-4">
            <?php if($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1'): ?>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Apakah isi pengumuman sudah benar? Sistem akan menyebarkan pesan dan lampiran ini ke semua kontak warga.')">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Isi Pesan Siaran Massal</label>
                    <textarea name="pesan" class="form-control" rows="6" placeholder="Contoh: Assalamualaikum, Halo {nama}. Diberitahukan bahwa besok pagi pukul 08.00 WIB akan diadakan kerja bakti..." style="border-radius:10px;" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold"><i class="fas fa-paperclip text-secondary me-1"></i> Sisipkan File Lampiran (Opsional)</label>
                    <input type="file" name="lampiran_file" class="form-control" style="border-radius:10px;">
                    <div class="form-text text-muted">Format didukung: PDF, JPG, PNG, DOCX (Maksimal 2MB). Kosongkan jika ingin kirim teks biasa.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="kirim_broadcast" class="btn btn-wa shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i> Mulai Broadcast Sekarang
                    </button>
                    <a href="index.php" class="btn btn-light fw-bold" style="border-radius:10px; padding: 12px 20px;">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>