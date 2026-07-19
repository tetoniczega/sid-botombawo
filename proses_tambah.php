<?php 
session_start(); 
include 'koneksi.php';

$nik           = $_POST['nik'];
$nama_lengkap  = $_POST['nama_lengkap'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$pekerjaan     = $_POST['pekerjaan'];
$status_kawin  = $_POST['status_kawin'];
$no_hp         = $_POST['no_hp']; 
$alamat        = $_POST['alamat'];

$foto = ""; 

if(isset($_FILES['foto_warga']['name']) && $_FILES['foto_warga']['name'] != ""){
    $nama_file = $_FILES['foto_warga']['name'];
    $ukuran_file = $_FILES['foto_warga']['size'];
    $tmp_file = $_FILES['foto_warga']['tmp_name'];
    
    $ekstensi_diperbolehkan = array('png','jpg','jpeg');
    $x = explode('.', $nama_file);
    $ekstensi = strtolower(end($x));
    
    $nama_foto_baru = $nik . "_" . time() . "." . $ekstensi;

    if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
        if($ukuran_file < 2048000){ 
            move_uploaded_file($tmp_file, 'foto/'.$nama_foto_baru);
            $foto = $nama_foto_baru;
        } else {
            echo "<script>alert('GAGAL! Ukuran file foto terlalu besar (Maks 2MB).'); window.location='tambah_warga.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('GAGAL! Ekstensi file hanya boleh JPG, JPEG, atau PNG.'); window.location='tambah_warga.php';</script>";
        exit;
    }
}

$query = "INSERT INTO warga (nik, nama_lengkap, jenis_kelamin, tanggal_lahir, pekerjaan, status_kawin, no_hp, alamat, foto) 
          VALUES ('$nik', '$nama_lengkap', '$jenis_kelamin', '$tanggal_lahir', '$pekerjaan', '$status_kawin', '$no_hp', '$alamat', '$foto')";

if (mysqli_query($conn, $query)) {
    
    $username_aktif = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
    
    catatLog($conn, $username_aktif, "Menambahkan warga baru bernama: " . $nama_lengkap . " (NIK: " . $nik . ")");

    echo "<script>alert('Data Warga Berhasil Disimpan!'); window.location='index.php';</script>";
} else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
}
?>