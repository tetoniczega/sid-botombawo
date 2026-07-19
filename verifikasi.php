<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
if(strtolower($_SESSION['role']) != "admin"){ header("location:index.php"); exit; } 
include 'koneksi.php'; 

$nama_desa = "Botombawo"; 

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Data Warga - Desa <?= htmlspecialchars($nama_desa); ?></title>
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
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 2px solid #e0eafc; cursor: pointer; transition: 0.3s; }
        .img-preview:hover { transform: scale(1.1); }
    </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold mb-0 text-dark">Verifikasi Data Mandiri Warga</h2>
            <p class="text-muted">Review dan validasi pengajuan data kependudukan mandiri di Desa <strong><?= htmlspecialchars($nama_desa); ?></strong>.</p>
        </div>

        <div class="card card-premium p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Foto KTP/Wajah</th>
                            <th width="20%">NIK</th>
                            <th width="25%">Nama Lengkap</th>
                            <th width="15%">Tanggal Pengajuan</th>
                            <th class="text-center" width="20%">Aksi (Validasi)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM pengajuan_warga WHERE status_pengajuan='Menunggu' ORDER BY id ASC");
                        $no = 1;
                        $cek = mysqli_num_rows($query);
                        
                        if($cek == 0){
                            echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Tidak ada pengajuan warga baru yang menunggu verifikasi.</td></tr>";
                        }
                        
                        while ($row = mysqli_fetch_array($query)) {
                            $path_foto = ($row['foto'] != "" && file_exists('foto/'.$row['foto'])) ? 'foto/'.$row['foto'] : "https://ui-avatars.com/api/?name=".urlencode($row['nama_lengkap']);
                            
                            echo "<tr>
                                    <td class='text-muted fw-bold'>".$no++."</td>
                                    <td><a href='$path_foto' target='_blank'><img src='$path_foto' class='img-preview' title='Klik untuk perbesar'></a></td>
                                    <td><span class='badge bg-light text-dark border'>".htmlspecialchars($row['nik'])."</span></td>
                                    <td class='fw-bold text-dark'>".htmlspecialchars($row['nama_lengkap'])."</td>
                                    <td class='text-muted small'>".htmlspecialchars($row['tgl_pengajuan'])."</td>
                                    <td class='text-center'>
                                        <a href='proses_verifikasi.php?id=".$row['id']."&aksi=terima' class='btn btn-sm btn-success fw-bold px-3' style='background:#059669; border:none; border-radius:8px;' onclick=\"return confirm('ACC dan masukkan ke Data Warga Utama?')\"><i class='fas fa-check me-1'></i> Terima</a>
                                        <a href='proses_verifikasi.php?id=".$row['id']."&aksi=tolak' class='btn btn-sm btn-danger fw-bold px-3' style='border-radius:8px;' onclick=\"return confirm('Tolak dan hapus pengajuan ini?')\"><i class='fas fa-times me-1'></i> Tolak</a>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>