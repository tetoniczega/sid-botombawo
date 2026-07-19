<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id =$_POST['id'];
    $status =$_POST['status_laporan'];
    $tanggapan =$_POST['tanggap_admin'];

    $query = "UPDATE pengaduan SET status_laporan='$status', tanggap_admin='$tanggapan' WHERE id='$id'";
    mysqli_query($conn,$query);
    
    header("Location: kelola_laporan.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id =$_GET['id'];
    $status =$_GET['status'];

    if ($status == 'Hapus') {
        $query = mysqli_query($conn, "SELECT foto_lampiran FROM pengaduan WHERE id='$id'");
        $data = mysqli_fetch_assoc($query);
        if ($data['foto_lampiran'] != "" && file_exists('foto/'.$data['foto_lampiran'])) {
            unlink('foto/'.$data['foto_lampiran']);
        }
        mysqli_query($conn, "DELETE FROM pengaduan WHERE id='$id'");
    }
    header("Location: kelola_laporan.php");
    exit;
}
?>