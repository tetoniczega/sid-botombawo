<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$nama_desa = "Botombawo"; 

$q_badge = mysqli_query($conn, "SELECT COUNT(*) as total_antrean FROM pengajuan_warga WHERE status_pengajuan='Menunggu'");
$r_badge = mysqli_fetch_assoc($q_badge);
$total_antrean = $r_badge['total_antrean'];
$q_surat = mysqli_query($conn, "SELECT COUNT(*) as total_surat FROM pengajuan_surat WHERE status='Menunggu'");
$r_surat = mysqli_fetch_assoc($q_surat);
$total_surat = $r_surat['total_surat'];

$q_lapor = mysqli_query($conn, "SELECT COUNT(*) as total_lapor FROM pengaduan WHERE status_laporan='Menunggu'");
$r_lapor = mysqli_fetch_assoc($q_lapor);
$total_lapor = $r_lapor['total_lapor'];

$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <a href="index.php" class="sidebar-brand">
        <i class="fas fa-leaf"></i>
        <div><?= htmlspecialchars($nama_desa); ?></div>
    </a>
    <h6 class="px-4 text-muted font-weight-bold text-uppercase mb-1" style="font-size:0.75rem; letter-spacing:1px;">Menu Utama</h6>
    <nav class="nav flex-column mb-auto">
        
        <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-users w-20 me-2"></i> Dashboard Data
        </a>
        
        <?php if(isset($_SESSION['role']) && strtolower($_SESSION['role']) == 'admin'){ ?>
            
            <a href="verifikasi.php" class="nav-link <?= ($current_page == 'verifikasi.php') ? 'active' : ''; ?> d-flex justify-content-between align-items-center">
                <span><i class="fas fa-user-check w-20 me-2"></i> Verifikasi Pengajuan</span>
                <?php if($total_antrean > 0): ?>
                    <span class="badge bg-danger rounded-pill shadow-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                        <?= $total_antrean; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="verifikasi_surat.php" class="nav-link <?= ($current_page == 'verifikasi_surat.php') ? 'active' : ''; ?> d-flex justify-content-between align-items-center">
                <span><i class="fas fa-envelope-open-text w-20 me-2"></i> Verifikasi Surat Masuk</span>
                <?php if($total_surat > 0): ?>
                    <span class="badge bg-danger rounded-pill shadow-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                        <?= $total_surat; ?>
                    </span>
                <?php endif; ?>
            </a>
            
            <a href="tambah_informasi.php" class="nav-link <?= ($current_page == 'tambah_informasi.php') ? 'active' : ''; ?>">
                <i class="fas fa-bullhorn w-20 me-2"></i> Buat Pengumuman
            </a>
            
            <a href="broadcast.php" class="nav-link <?= ($current_page == 'broadcast.php') ? 'active' : ''; ?>">
                <i class="fab fa-whatsapp w-20 me-2"></i> Broadcast WhatsApp
            </a>
            
        <?php } ?>
        
        <a href="arsip.php" class="nav-link <?= ($current_page == 'arsip.php') ? 'active' : ''; ?>">
            <i class="fas fa-book w-20 me-2"></i> Buku Arsip Surat
        </a>
        
        <a href="kelola_laporan.php" class="nav-link <?= ($current_page == 'kelola_laporan.php') ? 'active' : ''; ?> d-flex justify-content-between align-items-center">
            <span><i class="fas fa-bullhorn w-20 me-2"></i> Data E-Lapor</span>
            <?php if($total_lapor > 0): ?>
                <span class="badge bg-danger rounded-pill shadow-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                    <?= $total_lapor; ?>
                </span>
            <?php endif; ?>
        </a>
        
        <a href="kelola_apbdes.php" class="nav-link <?= ($current_page == 'kelola_apbdes.php') ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie w-20 me-2"></i> Transparansi APBDes
        </a>
        
    </nav>
    <div class="position-absolute bottom-0 w-100 p-4">
         <a href="logout.php" class="btn btn-light text-danger w-100 fw-bold shadow-sm" onclick="return confirm('Keluar dari sistem?')">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
         </a>
    </div>
</div>