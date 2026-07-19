<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php'; 

$nama_desa = "Botombawo"; 

if(isset($_POST['update_apbdes'])){
    $infra          = mysqli_real_escape_string($conn, $_POST['infrastruktur']);
    $pemberdayaan   = mysqli_real_escape_string($conn, $_POST['pemberdayaan']);
    $kesehatan      = mysqli_real_escape_string($conn, $_POST['kesehatan']);
    $pemerintahan   = mysqli_real_escape_string($conn, $_POST['pemerintahan']);

    $query_update = "UPDATE apbdes SET 
                    infrastruktur = '$infra', 
                    pemberdayaan = '$pemberdayaan', 
                    kesehatan = '$kesehatan', 
                    pemerintahan = '$pemerintahan' 
                    WHERE id = 1";
                    
    if(mysqli_query($conn, $query_update)){
        echo "<script>alert('BERHASIL! Data Anggaran APBDes telah diperbarui secara Real-Time.'); window.location='kelola_apbdes.php';</script>";
    } else {
        echo "<script>alert('GAGAL memperbarui database: " . mysqli_error($conn) . "');</script>";
    }
}

$query_data = mysqli_query($conn, "SELECT * FROM apbdes WHERE id=1");
$data_apbdes = mysqli_fetch_assoc($query_data);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola APBDes - SmartDesa <?= htmlspecialchars($nama_desa); ?></title>
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
    </style>
</head>
<body>

   <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold mb-0 text-dark">Kelola Data APBDes</h2>
            <p class="text-muted">Perbarui alokasi Dana Desa yang akan tampil di Portal Publik Desa <strong><?= htmlspecialchars($nama_desa); ?></strong> secara instan.</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-premium p-4">
                    <div class="alert alert-info border-0 shadow-sm rounded-3">
                        <i class="fas fa-info-circle me-2"></i> <b>Petunjuk:</b> Masukkan nominal angka saja tanpa titik atau koma (Contoh: <b>450000000</b>).
                    </div>
                    
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary"><i class="fas fa-hard-hat me-1"></i> Pembangunan Infrastruktur</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" class="form-control form-control-lg" name="infrastruktur" value="<?= isset($data_apbdes['infrastruktur']) ? $data_apbdes['infrastruktur'] : 0; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success"><i class="fas fa-seedling me-1"></i> Pemberdayaan Masyarakat</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" class="form-control form-control-lg" name="pemberdayaan" value="<?= isset($data_apbdes['pemberdayaan']) ? $data_apbdes['pemberdayaan'] : 0; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-danger"><i class="fas fa-heartbeat me-1"></i> Kesehatan & Stunting</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" class="form-control form-control-lg" name="kesehatan" value="<?= isset($data_apbdes['kesehatan']) ? $data_apbdes['kesehatan'] : 0; ?>" required>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label fw-bold text-warning"><i class="fas fa-user-shield me-1"></i> Penyelenggaraan Pemerintah</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" class="form-control form-control-lg" name="pemerintahan" value="<?= isset($data_apbdes['pemerintahan']) ? $data_apbdes['pemerintahan'] : 0; ?>" required>
                            </div>
                        </div>

                        <button type="submit" name="update_apbdes" class="btn btn-success btn-lg w-100 fw-bold shadow-sm" style="background: linear-gradient(135deg, #059669, #047857); border:none;">
                            <i class="fas fa-save me-2"></i> Simpan Pembaruan Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>