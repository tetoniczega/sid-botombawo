<?php
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
if($_SESSION['role'] != "admin"){ header("location:index.php"); exit; }

include 'koneksi.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT nama_lengkap, foto FROM warga WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

$nama_terhapus = isset($data['nama_lengkap']) ? $data['nama_lengkap'] : 'Tidak Diketahui';

$hapus = mysqli_query($conn, "DELETE FROM warga WHERE id='$id'");

if($hapus){
    $username_aktif = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
    catatLog($conn, $username_aktif, "Menghapus data warga bernama: " . $nama_terhapus . " (ID: " . $id . ")");

    if($data['foto'] != "" && file_exists('foto/'.$data['foto'])){
        unlink('foto/'.$data['foto']);
    }
    header("location:index.php?pesan=hapus");
} else {
    echo "Gagal menghapus data: " . mysqli_error($conn);
}
?>