<?php 
session_start();
include 'koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$login = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($login);

if($cek > 0){
    $data = mysqli_fetch_assoc($login);

    $_SESSION['username'] = $username;
    $_SESSION['nama_pengguna'] = $data['nama_lengkap'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['status'] = "login";

    header("location:index.php");
} else {
    header("location:login.php?pesan=gagal");
}
?>