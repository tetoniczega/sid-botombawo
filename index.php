<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php'; 
include 'config.php';

$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM warga");
$d_total = mysqli_fetch_assoc($q_total);
$total_warga = $d_total['total'];

$q_laki = mysqli_query($conn, "SELECT COUNT(*) as total_l FROM warga WHERE jenis_kelamin='Laki-laki'");
$d_laki = mysqli_fetch_assoc($q_laki);
$total_laki = $d_laki['total_l'];

$q_perempuan = mysqli_query($conn, "SELECT COUNT(*) as total_p FROM warga WHERE jenis_kelamin='Perempuan'");
$d_perempuan = mysqli_fetch_assoc($q_perempuan);
$total_perempuan = $d_perempuan['total_p'];

$q_kerja = mysqli_query($conn, "SELECT pekerjaan, COUNT(*) as jumlah FROM warga GROUP BY pekerjaan ORDER BY jumlah DESC");

$q_pengumuman = mysqli_query($conn, "
    SELECT * FROM pengumuman 
    WHERE status = 'Aktif' 
      AND (tanggal_akhir >= CURDATE() OR tanggal_akhir IS NULL) 
    ORDER BY id DESC 
    LIMIT 6
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Desa Yawaniha</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f4f7f6; color: #2c3e50; }
        .sidebar { height: 100vh; position: fixed; top: 0; left: 0; width: 260px; background: #ffffff; padding-top: 20px; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.03); }
        .sidebar-brand { font-size: 1.4rem; font-weight: 800; text-align: center; color: #059669; text-decoration: none; display: flex; align-items: center; justify-content: center; margin-bottom: 30px; letter-spacing: 0.5px;}
        .sidebar-brand i { font-size: 1.8rem; margin-right: 12px; color: #059669; }
        .nav-link { color: #6c757d; font-weight: 600; padding: 12px 25px; margin: 5px 15px; border-radius: 10px; transition: 0.3s; text-decoration: none; }
        .nav-link:hover { background-color: #f8f9fa; color: #059669; transform: translateX(5px); }
        .nav-link.active { background: linear-gradient(135deg, #059669, #047857); color: white; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }
        .main-content { margin-left: 260px; padding: 40px; }
        .card-premium { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .stat-card { border-radius: 15px; border: none; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white; }
        .badge-nik { background: #ecfdf5; color: #059669; padding: 6px 12px; border-radius: 8px; font-weight: 700; letter-spacing: 1px; }
        .btn-action { width: 35px; height: 35px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; margin: 0 3px; }
        .btn-action:hover { transform: translateY(-3px); box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .img-warga { width: 45px; height: 45px; object-fit: cover; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border: 2px solid white; }
        
        /* Gaya Khusus untuk Box Pengumuman Multi-Card */
        .announcement-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 15px; padding: 18px; height: 100%; transition: 0.3s; }
        .announcement-box:hover { background: #f1f5f9; transform: translateY(-2px); }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Dashboard Kependudukan</h2>
                <p class="text-muted">Halo, <strong><?php echo $_SESSION['nama_pengguna']; ?></strong> (<?php echo $_SESSION['role']; ?>)</p>
            </div>
            <div class="d-flex gap-2">
                <a href="export_excel.php" class="btn btn-success shadow-sm fw-bold" style="border-radius:10px; background: #10b981; border:none;">
                    <i class="fas fa-file-excel me-2"></i> Export Excel
                </a>
                <?php if(isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'){ ?>
                <a href="tambah_warga.php" class="btn btn-primary shadow-sm fw-bold" style="background: #059669; border:none; border-radius:10px;">
                    <i class="fas fa-plus me-2"></i> Tambah Warga
                </a>
                <?php } ?>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon shadow-sm me-3" style="background: linear-gradient(135deg, #059669, #047857) !important;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Total Penduduk</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo $total_warga; ?> <span class="fs-6 text-muted fw-normal">Jiwa</span></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon shadow-sm me-3" style="background: linear-gradient(135deg, #0ea5e9, #0284c7) !important;">
                            <i class="fas fa-male"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Laki-laki</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo $total_laki; ?> <span class="fs-6 text-muted fw-normal">Jiwa</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon shadow-sm me-3" style="background: linear-gradient(135deg, #f43f5e, #e11d48) !important;">
                            <i class="fas fa-female"></i>
                        </div>
                        <div>
                            <h6 class="text-muted fw-bold mb-1">Perempuan</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo $total_perempuan; ?> <span class="fs-6 text-muted fw-normal">Jiwa</span></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card shadow-sm h-100 p-2">
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="height: 100px; width: 100%;">
                            <canvas id="genderChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-7">
                <div class="card card-premium h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon me-3 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669) !important; width:45px; height:45px; font-size:1.2rem;">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Klasifikasi Jenis Pekerjaan</h5>
                                <small class="text-muted">Akurasi data real-time pemetaan distribusi bantuan sosial</small>
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-uppercase text-muted" style="position: sticky; top: 0; z-index: 1; font-size: 0.75rem;">
                                    <tr>
                                        <th class="text-center" width="10%">No</th>
                                        <th>Nama Pekerjaan</th>
                                        <th class="text-center" width="25%">Kuantitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no_k = 1;
                                    if(mysqli_num_rows($q_kerja) > 0) {
                                        while($r_kerja = mysqli_fetch_assoc($q_kerja)){
                                            $nama_kerja = !empty($r_kerja['pekerjaan']) ? htmlspecialchars($r_kerja['pekerjaan']) : 'Belum / Tidak Bekerja';
                                    ?>
                                    <tr>
                                        <td class="text-center text-muted small"><?= $no_k++; ?></td>
                                        <td class="fw-bold text-dark text-uppercase" style="font-size: 0.9rem;"><?= $nama_kerja; ?></td>
                                        <td class="text-center">
                                            <span class="badge font-weight-bold" style="background: #ecfdf5; color: #059669; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
                                                <?= $r_kerja['jumlah']; ?> Jiwa
                                            </span>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center text-muted py-3'>Belum ada data pekerjaan.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-premium h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stat-icon me-3 shadow-sm" style="background: linear-gradient(135deg, #ef4444, #b91c1c) !important; width:45px; height:45px; font-size:1.2rem;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Log Aktivitas Terbaru</h5>
                                <small class="text-muted">Rekam jejak digital (Audit Trail Keamanan)</small>
                            </div>
                        </div>
                        <div class="list-group list-group-flush" style="max-height: 260px; overflow-y: auto;">
                            <?php 
                            $q_log = @mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 5");
                            if ($q_log && mysqli_num_rows($q_log) > 0) {
                                while($r_log = mysqli_fetch_assoc($q_log)){
                            ?>
                                <div class="list-group-item px-0 py-2 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-secondary font-weight-bold" style="font-size: 0.7rem;"><?= htmlspecialchars($r_log['username']); ?></span>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= date('d/m H:i', strtotime($r_log['waktu'])); ?></small>
                                    </div>
                                    <p class="mb-0 text-dark small" style="line-height: 1.3; font-size: 0.85rem;"><?= htmlspecialchars($r_log['tindakan']); ?></p>
                                </div>
                            <?php 
                                }
                            } else {
                            ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle mb-2 fs-4 text-warning"></i>
                                    <p class="mb-0 small px-2">Belum ada aktivitas terekam. Pastikan tabel `log_aktivitas` sudah dieksekusi di database.</p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-premium mb-5">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="stat-icon me-3 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706) !important; width:45px; height:45px; font-size:1.2rem;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Papan Informasi Berita Desa</h5>
                        <small class="text-muted">Daftar seluruh pengumuman resmi yang diterbitkan oleh pemerintah desa</small>
                    </div>
                </div>
                
                <div class="row g-3">
                    <?php 
                    if($q_pengumuman && mysqli_num_rows($q_pengumuman) > 0) {
                        while($r_info = mysqli_fetch_assoc($q_pengumuman)) {
                            $warna_badge = "bg-secondary";
                            if($r_info['kategori'] == "Bantuan Sosial") $warna_badge = "bg-success text-white";
                            elseif($r_info['kategori'] == "Kesehatan") $warna_badge = "bg-info text-dark";
                            elseif($r_info['kategori'] == "Kegiatan Desa") $warna_badge = "bg-primary text-white";
                    ?>
                    <div class="col-md-4">
                        <div class="announcement-box">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge <?= $warna_badge; ?> fw-bold" style="font-size:0.7rem; padding: 5px 10px; border-radius:6px;"><?= htmlspecialchars($r_info['kategori']); ?></span>
                                <small class="text-muted" style="font-size:0.7rem;"><i class="far fa-calendar-alt me-1"></i><?= date('d M Y', strtotime($r_info['tanggal'])); ?></small>
                            </div>
                            <h6 class="fw-bold text-dark mb-2 text-uppercase" style="font-size:0.9rem; letter-spacing:0.3px;"><?= htmlspecialchars($r_info['judul']); ?></h6>
                            <p class="text-muted small mb-0" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= nl2br(htmlspecialchars($r_info['isi_pengumuman'])); ?>
                            </p>
                        </div>
                    </div>
                    <?php 
                        }
                    } else {
                    ?>
                    <div class="col-12 text-center text-muted py-3">
                        <i class="fas fa-info-circle me-1 text-warning"></i> Belum ada data informasi pengumuman yang aktif atau relevan di papan utama.
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="card card-premium">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tabelData" class="table table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="5%">Foto</th> 
                                <th width="15%">NIK</th>
                                <th>Nama Lengkap</th>
                                <th>L/P</th>
                                <th>Alamat Lengkap</th>
                                <th>No. WhatsApp</th>
                                <?php if(isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'){ ?>
                                <th class="text-center" width="15%">Aksi</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM warga ORDER BY id DESC");
                            $no = 1;
                            while ($row = mysqli_fetch_array($query)) {
                                $jk = ($row['jenis_kelamin'] == 'Laki-laki') ? 'L' : 'P';
                          
                                if($row['foto'] != "" && file_exists('foto/'.$row['foto'])){
                                    $path_foto = 'foto/'.$row['foto'];
                                } else {
                                    $path_foto = "https://ui-avatars.com/api/?name=".urlencode($row['nama_lengkap'])."&background=random&color=fff";
                                }

                                // Format link WhatsApp interaktif
                                $no_hp_tampil = !empty($row['no_hp']) ? "<a href='https://wa.me/".$row['no_hp']."' target='_blank' class='text-success fw-bold text-decoration-none'><i class='fab fa-whatsapp me-1'></i>".$row['no_hp']."</a>" : "<span class='text-muted small'>-</span>";

                                echo "<tr>
                                        <td class='text-center text-muted'>".$no++."</td>
                                        <td><img src='$path_foto' class='img-warga' alt='Foto'></td>
                                        <td><span class='badge-nik'>".$row['nik']."</span></td>
                                        <td class='fw-bold text-dark'>".$row['nama_lengkap']."</td>
                                        <td class='fw-bold text-muted'>".$jk."</td>
                                        <td class='text-muted small'>".$row['alamat']."</td>
                                        <td>".$no_hp_tampil."</td>";
                                        
                                if(isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'){
                                    echo "<td class='text-center'>
                                            <a href='edit.php?id=".$row['id']."' class='btn-action btn btn-light text-warning' title='Edit'><i class='fas fa-pen'></i></a>
                                            <a href='#' class='btn-action btn btn-light text-danger' title='Hapus' onclick=\"konfirmasiHapus(event, 'hapus.php?id=".$row['id']."')\"><i class='fas fa-trash'></i></a>
                                            <a href='pilih_surat.php?id=".$row['id']."' class='btn-action btn btn-light text-primary' title='Cetak Surat'><i class='fas fa-print'></i></a>
                                          </td>";
                                }

                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#tabelData').DataTable({
                "language": {
                    "search": "Cari Warga:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ warga",
                    "paginate": { "first": "Awal", "last": "Akhir", "next": "Maju", "previous": "Mundur" }
                }
            });
        });

        function konfirmasiHapus(event, url) {
            event.preventDefault(); 
            Swal.fire({
                title: 'Hapus Data Warga?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        }

        const ctx = document.getElementById('genderChart').getContext('2d');
        const genderChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [<?php echo $total_laki; ?>, <?php echo $total_perempuan; ?>],
                    backgroundColor: ['#0ea5e9', '#e11d48'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { family: 'Nunito', size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>