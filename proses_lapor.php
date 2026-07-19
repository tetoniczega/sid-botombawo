<?php
include 'koneksi.php';

$nik = $_POST['nik'];
$judul_laporan = $_POST['judul_laporan'];
$isi_laporan = $_POST['isi_laporan'];

$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$cek_warga = mysqli_query($conn, "SELECT id FROM warga WHERE nik='$nik'");
if(mysqli_num_rows($cek_warga) == 0){
    echo "<script>alert('GAGAL! NIK Anda tidak ditemukan di database. Silakan daftar mandiri terlebih dahulu.'); window.location='lapor.php';</script>";
    exit;
}

$foto = "";
if(isset($_FILES['foto_lampiran']['name']) && $_FILES['foto_lampiran']['name'] != ""){
    $nama_file = $_FILES['foto_lampiran']['name'];
    $tmp_file = $_FILES['foto_lampiran']['tmp_name'];
    
    $x = explode('.', $nama_file);
    $ekstensi = strtolower(end($x));
    $nama_foto_baru = "LAPORAN_" . time() . "." . $ekstensi;

    if(move_uploaded_file($tmp_file, 'foto/'.$nama_foto_baru)){
        $foto = $nama_foto_baru;
    }
}

$query = "INSERT INTO pengaduan (nik_warga, judul_laporan, isi_laporan, latitude, longitude, foto_lampiran, status_laporan) 
          VALUES ('$nik', '$judul_laporan', '$isi_laporan', '$latitude', '$longitude', '$foto', 'Menunggu')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('BERHASIL! Laporan Anda beserta titik kordinat telah diterima.'); window.location='portal.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>