<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['username'])) {
    header("location: login.php?pesan=belum_login");
    exit;
}
$username_warga = $_SESSION['username'];
$nama_warga_login = isset($_SESSION['nama']) ? $_SESSION['nama'] : $_SESSION['username']; 

if (isset($_POST['ajukan_surat'])) {
    $jenis_surat = mysqli_real_escape_string($conn, $_POST['jenis_surat']);
    $keperluan = mysqli_real_escape_string($conn, $_POST['keperluan']);

    $query = "INSERT INTO pengajuan_surat (nik_warga, nama_warga, jenis_surat, keperluan) 
              VALUES ('$username_warga', '$nama_warga_login', '$jenis_surat', '$keperluan')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Surat berhasil diajukan! Silakan pantau status tracking.'); window.location='warga.php';</script>";
    } else {
        echo "<script>alert('Gagal mengajukan surat: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layanan Surat - SmartDesa</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f6f9; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Layanan Surat Mandiri</h2>
            <p class="text-muted">Selamat Datang, <strong class="text-primary"><?= $nama_warga_login; ?></strong></p>
        </div>
        <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold" style="border-radius: 8px;">
            <i class="fas fa-sign-out-alt me-1"></i> Keluar
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-paper-plane me-2 text-primary"></i>Buat Pengajuan</h5>
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pilih Jenis Surat</label>
                        <select name="jenis_surat" class="form-select" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Surat --</option>
                            <option value="Surat Keterangan Domisili">Surat Keterangan Domisili</option>
                            <option value="Surat Keterangan Tidak Mampu">Surat Keterangan Tidak Mampu (SKTM)</option>
                            <option value="Surat Keterangan Usaha">Surat Keterangan Usaha (SKU)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keperluan / Alasan</label>
                        <textarea name="keperluan" class="form-control" rows="4" style="border-radius: 8px;" placeholder="Contoh: Syarat mendaftarkan beasiswa anak" required></textarea>
                    </div>
                    <button type="submit" name="ajukan_surat" class="btn btn-primary w-100 fw-bold" style="border-radius: 8px;">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-custom p-4 bg-white">
                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-history me-2 text-primary"></i>Riwayat & Status Tracking</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small fw-bold">Tanggal</th>
                                <th class="small fw-bold">Jenis Surat</th>
                                <th class="small fw-bold">Keperluan</th>
                                <th class="small fw-bold text-center">Status Tracking</th>
                                <th class="small fw-bold">Aksi / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tampil = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE nik_warga='$username_warga' ORDER BY id_pengajuan DESC");
                            if (mysqli_num_rows($tampil) == 0) {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4 small'>Belum ada riwayat pengajuan surat.</td></tr>";
                            }
                            while ($data = mysqli_fetch_array($tampil)) {
                                if ($data['status'] == 'Menunggu') {
                                    $badge = "<span class='badge bg-warning text-dark px-3 py-2'>Menunggu</span>";
                                    $aksi = "<span class='text-muted small'><i class='fas fa-clock me-1'></i>Sedang diperiksa</span>";
                                } elseif ($data['status'] == 'Selesai') {
                                    $badge = "<span class='badge bg-success px-3 py-2'>Selesai</span>";
                                    $aksi = "<a href='download.php?file=".$data['file_surat']."' class='btn btn-success btn-sm fw-bold' style='border-radius:6px;' target='_blank'><i class='fas fa-download me-1'></i>Unduh PDF</a>";
                                } else {
                                    $badge = "<span class='badge bg-danger px-3 py-2'>Ditolak</span>";
                                    $aksi = "<small class='text-danger d-block fw-bold'>Alasan:</small><span class='small text-muted'>" . htmlspecialchars($data['catatan_admin']) . "</span>";
                                }
                            ?>
                            <tr>
                                <td class="small"><?= date('d/m/Y H:i', strtotime($data['tanggal_ajuan'])); ?></td>
                                <td class="fw-bold small text-dark"><?= $data['jenis_surat']; ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($data['keperluan']); ?></td>
                                <td class="text-center"><?= $badge; ?></td>
                                <td><?= $aksi; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>