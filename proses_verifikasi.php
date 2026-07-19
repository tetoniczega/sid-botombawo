<?php
session_start(); 
include 'koneksi.php';

if(!isset($_SESSION['status']) || $_SESSION['status'] != "login"){ 
    header("location:login.php"); 
    exit; 
}
if(!isset($_SESSION['role']) || strtolower($_SESSION['role']) != "admin"){ 
    header("location:index.php"); 
    exit; 
} 

if(!isset($_GET['id']) || !isset($_GET['aksi'])){
    header("Location: verifikasi.php");
    exit;
}

$id   = (int)$_GET['id']; 
$aksi = mysqli_real_escape_string($conn, $_GET['aksi']); 


$query = mysqli_query($conn, "SELECT * FROM pengajuan_warga WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if(!$data){
    echo "<script>alert('Data pengajuan tidak ditemukan!'); window.location='verifikasi.php';</script>";
    exit;
}

$admin_user = isset($_SESSION['nama_pengguna']) ? $_SESSION['nama_pengguna'] : 'Admin';
$waktu_log  = date('Y-m-d H:i:s');

if($aksi == 'terima'){
    $nik = $data['nik'];
    $nama = $data['nama_lengkap'];
    $jk = $data['jenis_kelamin'];
    $tgl = $data['tanggal_lahir'];
    $kerja = $data['pekerjaan'];
    $status_k = $data['status_kawin'];
    $no_hp = $data['no_hp']; 
    $alamat = $data['alamat'];
    $foto = $data['foto'];
    $nama_aman = mysqli_real_escape_string($conn, $nama);
    $alamat_aman = mysqli_real_escape_string($conn, $alamat);
    $no_hp_aman = mysqli_real_escape_string($conn, $no_hp);

    $insert = mysqli_query($conn, "INSERT INTO warga (nik, nama_lengkap, jenis_kelamin, tanggal_lahir, pekerjaan, status_kawin, no_hp, alamat, foto) 
                                   VALUES ('$nik', '$nama_aman', '$jk', '$tgl', '$kerja', '$status_k', '$no_hp_aman', '$alamat_aman', '$foto')");

    if($insert){
        $isi_log = "Menyetujui pendaftaran warga mandiri baru atas nama: " . $nama . " (NIK: " . $nik . ")";
        $isi_log_aman = mysqli_real_escape_string($conn, $isi_log); 
        
        $log = mysqli_query($conn, "INSERT INTO log_aktivitas (username, tindakan) VALUES ('$admin_user', '$isi_log_aman')");
        
        if(!$log){
            echo "<script>alert('Warga berhasil masuk, TAPI LOG GAGAL: " . mysqli_error($conn) . "');</script>";
        }

        mysqli_query($conn, "DELETE FROM pengajuan_warga WHERE id='$id'");
        echo "<script>alert('VERIFIKASI BERHASIL! Data resmi masuk ke sistem dan tercatat di log.'); window.location='verifikasi.php';</script>";
    } else {
        echo "<h4>Gagal memindahkan data!</h4>";
        echo "Penyebab: " . mysqli_error($conn);
        echo "<br><br><a href='verifikasi.php'>Kembali ke Halaman Verifikasi</a>";
    }

} else if($aksi == 'tolak'){
    $nik_tolak  = $data['nik'];
    $nama_tolak = $data['nama_lengkap'];

    if($data['foto'] != "" && file_exists('foto/'.$data['foto'])){
        unlink('foto/'.$data['foto']);
    }
    
    $hapus = mysqli_query($conn, "DELETE FROM pengajuan_warga WHERE id='$id'");
    
    if($hapus){
        $isi_log_tolak = "Menolak pendaftaran warga mandiri atas nama: " . $nama_tolak . " (NIK: " . $nik_tolak . ")";
        $isi_log_tolak_aman = mysqli_real_escape_string($conn, $isi_log_tolak);
        
        $log_tolak = mysqli_query($conn, "INSERT INTO log_aktivitas (username, tindakan) VALUES ('$admin_user', '$isi_log_tolak_aman')");
        
        if(!$log_tolak){
            echo "<script>alert('Pengajuan dihapus, TAPI LOG GAGAL: " . mysqli_error($conn) . "');</script>";
        }

        echo "<script>alert('PENGAJUAN DITOLAK! Data telah dihapus dan tindakan terekam di log.'); window.location='verifikasi.php';</script>";
    } else {
        echo "Gagal menghapus pengajuan: " . mysqli_error($conn);
    }
}
?>