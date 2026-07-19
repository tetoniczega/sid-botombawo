<?php
include 'koneksi.php';

$nik           = mysqli_real_escape_string($conn, $_POST['nik']);
$nama_lengkap  = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
$jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
$tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
$pekerjaan     = mysqli_real_escape_string($conn, $_POST['pekerjaan']); 
$status_kawin  = mysqli_real_escape_string($conn, $_POST['status_kawin']); 
$no_hp         = mysqli_real_escape_string($conn, $_POST['no_hp']); 
$alamat        = mysqli_real_escape_string($conn, $_POST['alamat']);

$foto = "";
if(isset($_FILES['foto_warga']['name']) && $_FILES['foto_warga']['name'] != ""){
    $nama_file = $_FILES['foto_warga']['name'];
    $tmp_file = $_FILES['foto_warga']['tmp_name'];
    
    $x = explode('.', $nama_file);
    $ekstensi = strtolower(end($x));
    
    $nama_foto_baru = "PENDING_" . $nik . "_" . time() . "." . $ekstensi;

    if(move_uploaded_file($tmp_file, 'foto/'.$nama_foto_baru)){
        $foto = $nama_foto_baru;
    }
}

$query = "INSERT INTO pengajuan_warga (nik, nama_lengkap, jenis_kelamin, tanggal_lahir, pekerjaan, status_kawin, no_hp, alamat, foto, status_pengajuan) 
          VALUES ('$nik', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$pekerjaan', '$status_kawin', '$no_hp', '$alamat', '$foto', 'Menunggu')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('BERHASIL! Data Anda sedang diantrekan. Mohon tunggu verifikasi dari Admin Balai Desa.'); window.location='portal.php';</script>";
} else {
    echo "Error Database: " . mysqli_error($conn);
}
?>