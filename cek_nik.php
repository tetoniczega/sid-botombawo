<?php
include 'koneksi.php';

$pesan = "";
$status = "";

if(isset($_POST['cek_nik'])){
    $nik = $_POST['nik'];
    
    $query = mysqli_query($conn, "SELECT nama_lengkap FROM warga WHERE nik='$nik'");
    $cek = mysqli_num_rows($query);
    
    if($cek > 0){
        $data = mysqli_fetch_assoc($query);
        $status = "sukses";
        $pesan = "Halo <strong>" . $data['nama_lengkap'] . "</strong>, NIK Anda telah resmi terdaftar dalam Sistem Informasi Desa Botombawo.";
    } else {
        $status = "gagal";
        $pesan = "Maaf, NIK <strong>" . $nik . "</strong> belum terdaftar. Silakan hubungi RT/RW atau perangkat desa untuk pendataan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Desa Sukamakmur</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            font-family: 'Nunito', sans-serif; 
            background: linear-gradient(135deg, #e0eafc, #cfdef3); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            margin: 0;
        }
        .portal-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .portal-icon {
            font-size: 4rem;
            color: #4361ee;
            margin-bottom: 20px;
        }
        .btn-cek {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-cek:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        .login-link {
            font-size: 0.9rem;
            color: #6c757d;
            text-decoration: none;
            margin-top: 20px;
            display: inline-block;
        }
        .login-link:hover { color: #4361ee; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="portal-card mx-auto">
                    <i class="fas fa-search-location portal-icon"></i>
                    <h3 class="fw-bold text-dark">Portal Cek NIK Warga</h3>
                    <p class="text-muted mb-4">Desa Sukamakmur, Kec. Makmur Jaya</p>

                    <form action="" method="POST">
                        <div class="mb-4 text-start">
                            <label class="form-label fw-bold">Masukkan Nomor Induk Kependudukan (NIK)</label>
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="fas fa-id-card"></i></span>
                                <input type="number" name="nik" class="form-control border-start-0" placeholder="Ketik 16 Digit NIK..." required autofocus>
                            </div>
                        </div>
                        <button type="submit" name="cek_nik" class="btn btn-primary text-white w-100 btn-cek fs-5">
                            <i class="fas fa-search me-2"></i> Cek Status Terdaftar
                        </button>
                    </form>

                    <?php if($status == "sukses"){ ?>
                        <div class="alert alert-success mt-4 border-0 shadow-sm" style="border-radius: 15px;">
                            <i class="fas fa-check-circle fs-3 mb-2 d-block text-success"></i>
                            <?php echo $pesan; ?>
                        </div>
                    <?php } else if($status == "gagal"){ ?>
                        <div class="alert alert-danger mt-4 border-0 shadow-sm" style="border-radius: 15px;">
                            <i class="fas fa-times-circle fs-3 mb-2 d-block text-danger"></i>
                            <?php echo $pesan; ?>
                        </div>
                    <?php } ?>

                    <a href="login.php" class="login-link"><i class="fas fa-lock me-1"></i> Akses Perangkat Desa</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>