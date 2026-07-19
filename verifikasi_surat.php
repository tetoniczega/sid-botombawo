<?php
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
if(strtolower($_SESSION['role']) != 'admin'){ header("location:index.php"); exit; }

include 'koneksi.php'; 

$nama_desa = "Botombawo"; 

if (isset($_POST['proses_aksi'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_pengajuan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status_baru']);

    $token_fonnte = "pBNzvoQG3kvesbB51aW6";

    $q_warga = mysqli_query($conn, "SELECT p.*, w.no_hp FROM pengajuan_surat p LEFT JOIN warga w ON p.nik_warga = w.nik WHERE p.id_pengajuan='$id'");
    $d_warga = mysqli_fetch_assoc($q_warga);
    
    $nama_penerima = $d_warga['nama_warga'];
    $jenis_surat   = $d_warga['jenis_surat'];
    $no_hp_target  = $d_warga['no_hp'];

    if ($status_baru == 'Selesai') {
        $no_surat = mysqli_real_escape_string($conn, $_POST['no_surat']);
        $update = mysqli_query($conn, "UPDATE pengajuan_surat SET status='Selesai', no_surat='$no_surat' WHERE id_pengajuan='$id'");
        
        if ($update) {
            if (!empty($no_hp_target)) {
                $pesan = "Halo *{$nama_penerima}*,\n\nPengajuan dokumen *{$jenis_surat}* Anda telah disetujui dan selesai diproses dengan Nomor Surat: *{$no_surat}*.\n\nSilakan datang langsung ke *Kantor Balai Desa Botombawo* pada jam kerja untuk pengambilan surat fisik resmi (sudah dicap & ditandatangani) dengan membawa KTP asli Anda.\n\nTerima kasih.";
                
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
                        'target' => $no_hp_target,
                        'message' => $pesan,
                    ),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token_fonnte"
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
            }

            echo "<script>alert('Surat berhasil diverifikasi, nomor disimpan, dan notifikasi WhatsApp telah dikirim!'); window.location='verifikasi_surat.php';</script>";
        } else {
            echo "<script>alert('Gagal memverifikasi surat: " . mysqli_error($conn) . "'); window.location='verifikasi_surat.php';</script>";
        }
    } elseif ($status_baru == 'Ditolak') {
        $catatan_admin = mysqli_real_escape_string($conn, $_POST['catatan_admin']);
        $update = mysqli_query($conn, "UPDATE pengajuan_surat SET status='Ditolak', catatan_admin='$catatan_admin' WHERE id_pengajuan='$id'");
        
        if ($update) {
            if (!empty($no_hp_target)) {
                $pesan = "Halo *{$nama_penerima}*,\n\nMohon maaf, permohonan pengajuan dokumen *{$jenis_surat}* Anda belum dapat disetujui oleh Pemerintah Desa Botombawo dengan alasan:\n\n\"*{$catatan_admin}*\"\n\nSilakan lengkapi kembali berkas persyaratan Anda atau hubungi perangkat desa di Kantor Balai Desa untuk info selengkapnya.\n\nTerima kasih.";
                
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
                        'target' => $no_hp_target,
                        'message' => $pesan,
                    ),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $token_fonnte"
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
            }

            echo "<script>alert('Status surat ditolak dan notifikasi alasan penolakan telah dikirim lewat WhatsApp!'); window.location='verifikasi_surat.php';</script>";
        } else {
            echo "<script>alert('Gagal menolak surat: " . mysqli_error($conn) . "'); window.location='verifikasi_surat.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Surat Masuk - Desa <?= htmlspecialchars($nama_desa); ?></title>
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
        .badge-waiting { background-color: #fffbeb; color: #d97706; padding: 6px 12px; border-radius: 8px; font-weight: 700; }
        .badge-success { background-color: #ecfdf5; color: #059669; padding: 6px 12px; border-radius: 8px; font-weight: 700; }
        .badge-danger { background-color: #fef2f2; color: #dc2626; padding: 6px 12px; border-radius: 8px; font-weight: 700; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Verifikasi Surat Mandiri Warga</h2>
                <p class="text-muted">Halo, <strong><?php echo htmlspecialchars($_SESSION['nama_pengguna']); ?></strong>. Periksa dan setujui permohonan surat masuk di Desa <strong><?= htmlspecialchars($nama_desa); ?></strong>.</p>
            </div>
        </div>

        <div class="card card-premium">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="tabelVerifikasi" class="table table-hover align-middle" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th width="20%">Warga Pemohon</th>
                                <th width="20%">Jenis Surat</th>
                                <th width="25%">Keperluan</th>
                                <th class="text-center" width="15%">Status</th>
                                <th class="text-center" width="20%">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tampil = mysqli_query($conn, "SELECT * FROM pengajuan_surat ORDER BY status ASC, id_pengajuan DESC");
                            while ($data = mysqli_fetch_array($tampil)) {
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= htmlspecialchars($data['nama_warga']); ?></span>
                                    <small class="text-muted">Username/NIK: <?= htmlspecialchars($data['nik_warga']); ?></small>
                                </td>
                                <td class="fw-bold text-success small"><?= htmlspecialchars($data['jenis_surat']); ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($data['keperluan']); ?></td>
                                <td class="text-center">
                                    <?php if($data['status'] == 'Menunggu') { ?>
                                        <span class="badge-waiting">Menunggu</span>
                                    <?php } elseif($data['status'] == 'Selesai') { ?>
                                        <span class="badge-success">Selesai</span>
                                    <?php } else { ?>
                                        <span class="badge-danger">Ditolak</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($data['status'] == 'Menunggu') { ?>
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <button type="button" class="btn btn-success btn-sm fw-bold shadow-sm btn-setuju-modal" 
                                                    data-id="<?= $data['id_pengajuan']; ?>" 
                                                    data-nama="<?= htmlspecialchars($data['nama_warga']); ?>" 
                                                    data-surat="<?= htmlspecialchars($data['jenis_surat']); ?>"
                                                    style="background:#059669; border:none; border-radius:8px;">
                                                <i class="fas fa-check"></i> Setuju
                                            </button>

                                            <form action="" method="POST" class="d-flex m-0 align-items-center">
                                                <input type="hidden" name="id_pengajuan" value="<?= $data['id_pengajuan']; ?>">
                                                <input type="hidden" name="status_baru" value="Ditolak">
                                                <input type="text" name="catatan_admin" class="form-control form-control-sm me-1" placeholder="Alasan..." required style="width: 100px; border-radius: 6px;">
                                                <button type="submit" name="proses_aksi" class="btn btn-danger btn-sm fw-bold shadow-sm" style="border-radius: 6px;">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php } else { ?>
                                        <span class="text-muted small bg-light px-2 py-1 rounded">Terverifikasi</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNomorSurat" tabindex="-1" aria-labelledby="modalNomorSuratLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success" id="modalNomorSuratLabel"><i class="fas fa-file-invoice me-2"></i> Validasi & Penomoran Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body px-4 pb-4">
                        <input type="hidden" name="id_pengajuan" id="modal_id_pengajuan">
                        <input type="hidden" name="status_baru" value="Selesai">
                        
                        <div class="mb-3 p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Warga Pemohon:</small>
                            <strong id="modal_nama_warga" class="text-dark d-block mb-2"></strong>
                            <small class="text-muted d-block">Jenis Surat:</small>
                            <span id="modal_jenis_surat" class="badge bg-success text-white fw-bold"></span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">Masukkan Nomor Surat Resmi</label>
                            <input type="text" class="form-control" name="no_surat" placeholder="Contoh: 140/025/BTW/<?= date('Y'); ?>" required style="border-radius:10px; padding: 10px;">
                            <small class="text-muted" style="font-size: 0.75rem;">Nomor ini akan langsung terintegrasi otomatis ke format berkas cetak.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius:8px;">Batal</button>
                        <button type="submit" name="proses_aksi" class="btn btn-success fw-bold" style="background:#059669; border:none; border-radius:8px;">Setujui & Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#tabelVerifikasi').DataTable({
                "language": {
                    "search": "Cari Pengajuan:",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ permohonan",
                    "paginate": { "first": "Awal", "last": "Akhir", "next": "Maju", "previous": "Mundur" }
                }
            });

            $('#tabelVerifikasi').on('click', '.btn-setuju-modal', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var surat = $(this).data('surat');
                
                $('#modal_id_pengajuan').val(id);
                $('#modal_nama_warga').text(nama);
                $('#modal_jenis_surat').text(surat);
                
                var myModal = new bootstrap.Modal(document.getElementById('modalNomorSurat'));
                myModal.show();
            });
        });
    </script>
</body>
</html>