<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $query_hapus = mysqli_query($conn, "DELETE FROM pengajuan_surat WHERE id_pengajuan = '$id'");

    if ($query_hapus) {
        echo "<script>alert('Data arsip surat berhasil dihapus, Boss!'); window.location='arsip.php';</script>";
    } else {
        $error_msg = mysqli_error($conn);
        echo "<script>alert('Gagal menghapus data! Error: " . addslashes($error_msg) . "'); window.location='arsip.php';</script>";
    }
} else {
    header("Location: arsip.php");
    exit();
}
?>