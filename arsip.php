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
    <title>Buku Arsip Surat - Desa <?= htmlspecialchars($nama_desa); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
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
        .dt-buttons .btn-success { background-color: #059669 !important; border-color: #059669 !important; font-weight: 700; border-radius: 8px; padding: 8px 16px; }
        .dt-buttons .btn-success:hover { background-color: #047857 !important; border-color: #047857 !important; }
    </style>
</head>
<body>

   <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Buku Arsip Surat Desa</h2>
                <p class="text-muted">Daftar seluruh surat warga yang telah divalidasi dan diberikan nomor surat resmi di Desa <strong><?= htmlspecialchars($nama_desa); ?></strong>.</p>
            </div>
        </div>

        <div class="card card-premium">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tabelArsip" class="table table-hover align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">No. Surat Resmi</th>
                                <th width="20%">Warga Pemohon</th>
                                <th width="20%">Jenis Surat</th>
                                <th width="25%">Keperluan</th>
                                <th class="text-center" width="15%">Aksi Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tampil_arsip = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE status='Selesai' ORDER BY id_pengajuan DESC");
                            while ($data = mysqli_fetch_array($tampil_arsip)) {
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary"><?= htmlspecialchars($data['no_surat']); ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= htmlspecialchars($data['nama_warga']); ?></span>
                                    <small class="text-muted">NIK: <?= htmlspecialchars($data['nik_warga']); ?></small>
                                </td>
                                <td class="fw-bold text-success small"><?= htmlspecialchars($data['jenis_surat']); ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($data['keperluan']); ?></td>
                                <td class="text-center">
                                    <a href="hapus_arsip.php?id=<?= $data['id_pengajuan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini, Boss?')">
                                        <i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabelArsip').DataTable({
                "dom": "<'row mb-3'<'col-md-6'B><'col-md-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel me-2"></i> Export Excel',
                        className: 'btn btn-success shadow-sm',
                        title: 'Data Buku Arsip Surat Desa <?= htmlspecialchars($nama_desa); ?>',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    }
                ],
                "language": {
                    "search": "Cari Arsip:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ arsip surat",
                    "paginate": { "first": "Awal", "last": "Akhir", "next": "Maju", "previous": "Mundur" }
                }
            });
        });
    </script>
</body>
</html>