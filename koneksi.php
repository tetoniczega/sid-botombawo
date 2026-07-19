<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "sid_desa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (!function_exists('catatLog')) {
    function catatLog($conn, $username, $tindakan) {
        $username_aman = mysqli_real_escape_string($conn, $username);
        $tindakan_aman = mysqli_real_escape_string($conn, $tindakan);
        mysqli_query($conn, "INSERT INTO log_aktivitas (username, tindakan) VALUES ('$username_aman', '$tindakan_aman')");
    }
}
?>