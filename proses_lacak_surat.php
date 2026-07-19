<?php
include 'koneksi.php';

if (isset($_GET['nik'])) {
    $nik = mysqli_real_escape_string($conn, $_GET['nik']);
    
    $query = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE nik_warga = '$nik' ORDER BY id_pengajuan DESC");
    
    if (mysqli_num_rows($query) > 0) {
        echo '<div class="table-responsive mt-3">';
        echo '<table class="table table-bordered table-hover align-middle small">';
        echo '<thead class="table-dark text-center">';
        echo '<tr>';
        echo '<th>Jenis Surat</th>';
        echo '<th>Tanggal Ajuan</th>';
        echo '<th>No. Surat Resmi</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($data = mysqli_fetch_array($query)) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($data['jenis_surat']) . '</strong><br><small class="text-muted">Keperluan: ' . htmlspecialchars($data['keperluan']) . '</small></td>';
            echo '<td class="text-center">' . date('d-m-Y', strtotime($data['tanggal_ajuan'])) . '</td>';
            
            if (!empty($data['no_surat'])) {
                echo '<td class="text-center text-primary fw-bold">' . htmlspecialchars($data['no_surat']) . '</td>';
            } else {
                echo '<td class="text-center text-muted"><em>Belum Terbit</em></td>';
            }
            
            echo '<td class="text-center">';
            if ($data['status'] == 'Selesai') {
                echo '<span class="badge bg-success px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i>Selesai</span>';
            } elseif ($data['status'] == 'Diproses') {
                echo '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fas fa-spinner fa-spin me-1"></i>Diproses</span>';
            } else {
                echo '<span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="fas fa-clock me-1"></i>Menunggu</span>';
            }
            echo '</td>';
            
            
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning text-center mt-3" role="alert">';
        echo '<i class="fas fa-exclamation-triangle me-2"></i> NIK Anda ditemukan di data warga, tetapi Anda <strong>belum pernah mengajukan surat apapun</strong>.';
        echo '</div>';
    }
} else {
    echo '<p class="text-danger text-center small">Akses ditolak.</p>';
}
?>