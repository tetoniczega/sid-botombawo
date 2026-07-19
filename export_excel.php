<?php
session_start();
if($_SESSION['status'] != "login"){
    header("location:login.php");
    exit;
}
include 'koneksi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Data_Penduduk.xls");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Export Data Warga</title>
</head>
<body>
    <center>
        <h2>Laporan Data Penduduk Desa Botombawo</h2>
        <p>Tanggal Cetak: <?php echo date('d-m-Y'); ?></p>
    </center>

    <table border="1" cellpadding="5">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>NIK</th>
                <th>Nama Lengkap</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Pekerjaan</th>
                <th>Status</th>
                <th>Alamat Lengkap</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conn, "SELECT * FROM warga ORDER BY id DESC");
            $no = 1;
            while ($row = mysqli_fetch_array($query)) {
                echo "<tr>
                        <td>".$no++."</td>
                        <td>'".$row['nik']."</td>
                        <td>".$row['nama_lengkap']."</td>
                        <td>".$row['jenis_kelamin']."</td>
                        <td>".date('d-m-Y', strtotime($row['tanggal_lahir']))."</td>
                        <td>".$row['pekerjaan']."</td>
                        <td>".$row['status_kawin']."</td>
                        <td>".$row['alamat']."</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>