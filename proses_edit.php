<?php
include 'koneksi.php';

$id            = $_POST['id'];
$nik           = $_POST['nik'];
$nama_lengkap  = $_POST['nama_lengkap'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$pekerjaan     = $_POST['pekerjaan'];
$status_kawin  = $_POST['status_kawin'];
$no_hp         = $_POST['no_hp']; 
$alamat        = $_POST['alamat'];

$query = "UPDATE warga SET 
            nik='$nik', 
            nama_lengkap='$nama_lengkap', 
            jenis_kelamin='$jenis_kelamin', 
            tanggal_lahir='$tanggal_lahir', 
            pekerjaan='$pekerjaan', 
            status_kawin='$status_kawin', 
            no_hp='$no_hp', 
            alamat='$alamat' 
          WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    header("Location: index.php");
} else {
    echo "Gagal mengupdate data: " . mysqli_error($conn);
}
?>