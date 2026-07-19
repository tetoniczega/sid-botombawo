<?php 
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php'; 

$nama_desa = "Botombawo"; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengaduan - Desa <?= htmlspecialchars($nama_desa); ?></title>
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
        .img-laporan { width: 90px; height: 90px; object-fit: cover; border-radius: 10px; cursor: pointer; border: 2px solid #f1f1f1;}
        .img-laporan:hover { opacity: 0.8; }
        .exif-badge { font-size: 0.7rem; line-height: 1.3; margin-top: 8px; padding: 5px; border-radius: 6px; text-align: left; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="mb-4">
            <h2 class="fw-bold mb-0 text-dark">Data Pengaduan Masyarakat</h2>
            <p class="text-muted">Pantau dan tindak lanjuti laporan dari warga Desa <strong><?= htmlspecialchars($nama_desa); ?></strong>.</p>
        </div>

        <div class="card card-premium p-4">
            <div class="table-responsive">
                <table id="tabelLaporan" class="table table-hover align-middle" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="15%">Bukti Foto</th>
                            <th width="20%">Pelapor</th>
                            <th width="25%">Laporan & Lokasi</th>
                            <th width="10%">Tanggal</th>
                            <th width="10%">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT pengaduan.*, warga.nama_lengkap 
                                                      FROM pengaduan 
                                                      JOIN warga ON pengaduan.nik_warga = warga.nik 
                                                      ORDER BY pengaduan.id DESC");
                        $no = 1;
                        while ($row = mysqli_fetch_array($query)) {
                      
                            $badge_color = "bg-warning text-dark";
                            if($row['status_laporan'] == 'Diproses') $badge_color = "bg-primary";
                            if($row['status_laporan'] == 'Selesai') $badge_color = "bg-success";

                            $path_foto = 'foto/'.$row['foto_lampiran'];
                            
                            $btn_maps = "";
                            if($row['latitude'] != "" && $row['longitude'] != "") {
                                $link_maps = "https://www.google.com/maps?q=".$row['latitude'].",".$row['longitude'];
                                $btn_maps = "<a href='$link_maps' target='_blank' class='btn btn-sm btn-outline-danger mt-1' style='font-size:0.75rem;'><i class='fas fa-map-marker-alt me-1'></i> Cek Maps</a>";
                            }

                            $exif_html = "";
                            if (!empty($row['foto_lampiran']) && file_exists($path_foto) && function_exists('exif_read_data')) {
                                $exif_data = @exif_read_data($path_foto);
                                
                                if ($exif_data !== false && isset($exif_data['DateTimeOriginal'])) {
                                    $kamera = ($exif_data['Make'] ?? '') . " " . ($exif_data['Model'] ?? '');
                                    $exif_html = "<div class='exif-badge bg-success bg-opacity-10 border border-success text-success'>
                                                    <i class='fas fa-check-circle'></i> <b>FOTO ASLI</b><br>
                                                    <span style='font-size:0.65rem;'>".$exif_data['DateTimeOriginal']."<br>".$kamera."</span>
                                                  </div>";
                                } else {
                                    $exif_html = "<div class='exif-badge bg-warning bg-opacity-10 border border-warning text-danger'>
                                                    <i class='fas fa-exclamation-triangle'></i> <b>POTENSI HOAX</b><br>
                                                    <span style='font-size:0.65rem;'>Metadata hilang. Verifikasi lapangan!</span>
                                                  </div>";
                                }
                            } else {
                                $exif_html = "<div class='exif-badge bg-secondary bg-opacity-10 border border-secondary text-muted'>
                                                <i class='fas fa-info-circle'></i> <span style='font-size:0.65rem;'>Data EXIF tidak tersedia</span>
                                              </div>";
                            }

                            echo "<tr>
                                    <td class='text-muted fw-bold text-center'>".$no++."</td>
                                    <td>
                                        <a href='$path_foto' target='_blank'><img src='$path_foto' class='img-laporan'></a>
                                        $exif_html
                                    </td>
                                    <td>
                                        <div class='fw-bold'>".$row['nama_lengkap']."</div>
                                        <small class='text-muted'>NIK: ".$row['nik_warga']."</small>
                                    </td>
                                    <td>
                                        <div class='fw-bold text-danger'>".$row['judul_laporan']."</div>
                                        <small class='text-muted d-block text-truncate mb-1' style='max-width: 200px;' title='".$row['isi_laporan']."'>".$row['isi_laporan']."</small>
                                        $btn_maps
                                    </td>
                                    <td class='small'>".date('d M Y H:i', strtotime($row['tgl_lapor']))."</td>
                                    <td><span class='badge $badge_color'>".$row['status_laporan']."</span></td>
                                    <td class='text-center'>
                                        <button type='button' class='btn btn-sm btn-success me-1' data-bs-toggle='modal' data-bs-target='#modalTindakLanjut".$row['id']."'>
                                            <i class='fas fa-edit me-1'></i> Tanggapi
                                        </button>
                                        <a href='update_status_lapor.php?id=".$row['id']."&status=Hapus' class='btn btn-sm btn-outline-danger' onclick=\"return confirm('Hapus laporan ini?')\">
                                            <i class='fas fa-trash'></i>
                                        </a>

                                        <div class='modal fade' id='modalTindakLanjut".$row['id']."' tabindex='-1' aria-hidden='true'>
                                            <div class='modal-dialog text-start'>
                                                <div class='modal-content' style='border-radius:15px;'>
                                                    <div class='modal-header bg-light'>
                                                        <h5 class='modal-title fw-bold text-dark'><i class='fas fa-comment-dots text-success me-2'></i>Tindak Lanjut Laporan</h5>
                                                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                    </div>
                                                    <form action='update_status_lapor.php' method='POST'>
                                                        <div class='modal-body'>
                                                            <input type='hidden' name='id' value='".$row['id']."'>
                                                            
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold text-secondary small'>Nama Pelapor</label>
                                                                <input type='text' class='form-control bg-light' value='".htmlspecialchars($row['nama_lengkap'])."' readonly>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold text-secondary small'>Isi Pengaduan</label>
                                                                <textarea class='form-control bg-light' rows='2' readonly>".htmlspecialchars($row['isi_laporan'])."</textarea>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold text-dark small'>Ubah Status Laporan</label>
                                                                <select name='status_laporan' class='form-select' required>
                                                                    <option value='Menunggu' ".($row['status_laporan'] == 'Menunggu' ? 'selected' : '').">Menunggu</option>
                                                                    <option value='Diproses' ".($row['status_laporan'] == 'Diproses' ? 'selected' : '').">Diproses</option>
                                                                    <option value='Selesai' ".($row['status_laporan'] == 'Selesai' ? 'selected' : '').">Selesai</option>
                                                                </select>
                                                            </div>
                                                            <div class='mb-3'>
                                                                <label class='form-label fw-bold text-dark small'>Tanggapan Admin (Balasan ke Warga)</label>
                                                                <textarea name='tanggap_admin' class='form-control' rows='4' placeholder='Ketikkan solusi atau tanggapan resmi di sini...' required>".$row['tanggap_admin']."</textarea>
                                                            </div>
                                                        </div>
                                                        <div class='modal-footer bg-light'>
                                                            <button type='button' class='btn btn-sm btn-secondary fw-bold' data-bs-dismiss='modal'>Batal</button>
                                                            <button type='submit' class='btn btn-sm btn-success fw-bold'>Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelLaporan').DataTable({
                "language": {
                    "search": "Cari Laporan:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ laporan",
                    "paginate": { "first": "Awal", "last": "Akhir", "next": "Maju", "previous": "Mundur" }
                }
            });
        });
    </script>
</body>
</html>