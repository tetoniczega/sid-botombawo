<?php
session_start();
if($_SESSION['status'] != "login"){ header("location:login.php"); exit; }
include 'koneksi.php';

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Arsip_Surat_Desa.xls");
?>

<center>
    <h3>LAPORAN ARSIP PENCETAKAN SURAT KETERANGAN</h3>
    <h4>DESA BOTOMBAWO</h4>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <th>No</th>
            <th>Tanggal Cetak</th>
            <th>Nama Warga</th>
            <th>Jenis Surat</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = mysqli_query($conn, "SELECT * FROM arsip_surat ORDER BY id DESC");
        $no = 1;
        while ($row = mysqli_fetch_array($query)) {
        ?>
            <tr>
                <td align="center"><?php echo $no++; ?></td>
                <td><?php echo date('d-m-Y', strtotime($row['tanggal_cetak'])); ?></td>
                <td><?php echo $row['nama_warga']; ?></td>
                <td><?php echo $row['jenis_surat']; ?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>