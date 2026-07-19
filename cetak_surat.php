<?php
session_start();
include 'koneksi.php';

$id_param     = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
$type_param   = mysqli_real_escape_string($conn, $_GET['type'] ?? ''); 
$no_param     = mysqli_real_escape_string($conn, $_GET['no'] ?? '');   

if (empty($id_param)) { 
    die("<h3 style='color:red; text-align:center; font-family:sans-serif;'>Error: Parameter ID Surat Tidak Ditemukan!</h3>"); 
}

$data_warga     = null;
$data_p         = null;
$data           = null;

$nama_tampil    = 'Warga Desa';
$nik_pemohon    = '-';
$jenis_kelamin  = '-';
$tanggal_lahir  = '-';
$pekerjaan      = '-';
$alamat         = 'Desa Botombawo';
$tempat_lahir   = 'Botombawo'; 
$nomor_surat    = !empty($no_param) ? $no_param : '...../...../YW/' . date('Y');
$jenis_surat    = $type_param;
$keperluan_teks = '-';
$status_surat   = '';

if (!empty($type_param)) {
    if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") { 
        header("location:login.php"); 
        exit; 
    }

    $query_warga = mysqli_query($conn, "SELECT * FROM warga WHERE id = '$id_param'");
    $data_warga  = mysqli_fetch_assoc($query_warga);

    if (!$data_warga) { 
        die("<h3 style='text-align:center;'>Error: Data profil warga dengan ID [".$id_param."] tidak ditemukan!</h3>"); 
    }

    $query_pengajuan = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE (id_pengajuan = '$id_param') OR (id = '$id_param') OR (id_surat = '$id_param') LIMIT 1");
    if (!$query_pengajuan) {
        $check_columns = mysqli_query($conn, "SHOW COLUMNS FROM pengajuan_surat");
        $primary_key = 'id';
        while ($col = mysqli_fetch_assoc($check_columns)) {
            if ($col['Key'] == 'PRI' || strpos($col['Field'], 'id') !== false) {
                $primary_key = $col['Field'];
                break;
            }
        }
        $query_pengajuan = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE $primary_key = '$id_param'");
    }
    $data = mysqli_fetch_assoc($query_pengajuan);

    $nama_tampil    = $data_warga['nama_lengkap'] ?? 'Warga Desa';
    $nik_pemohon    = $data_warga['nik'] ?? '-';
    $jenis_kelamin  = $data_warga['jenis_kelamin'] ?? '-';
    $tanggal_lahir  = $data_warga['tanggal_lahir'] ?? '-';
    $pekerjaan      = $data_warga['pekerjaan'] ?? '-';
    $alamat         = $data_warga['alamat'] ?? 'Desa Botombawo';
    
    $tempat_db      = $data_warga['tempat_lahir'] ?? '';
    $tempat_lahir   = !empty($tempat_db) && $tempat_db != '-' ? $tempat_db : 'Botombawo';

    $jenis_surat    = $type_param;
    $keperluan_teks = $data['keperluan'] ?? '-';
    $status_surat   = $data['status'] ?? 'Selesai';

} else {
    $query_pengajuan = mysqli_query($conn, "SELECT * FROM pengajuan_surat WHERE id_pengajuan = '$id_param'");
    $data_p          = mysqli_fetch_assoc($query_pengajuan);

    if (!$data_p) { 
        die("<h3 style='text-align:center; font-family:sans-serif;'>Data Pengajuan Surat Tidak Ditemukan!</h3>"); 
    }

    $status_surat = $data_p['status'] ?? '';

    if ($status_surat != 'Selesai' && (!isset($_SESSION['status']) || $_SESSION['status'] != "login")) {
        die("<h3 style='color:red; text-align:center; font-family:sans-serif; margin-top:50px;'>Akses Ditolak!</h3>");
    }

    $nik_cari     = $data_p['nik_warga'] ?? '';
    $query_detail = mysqli_query($conn, "SELECT * FROM warga WHERE nik = '$nik_cari' LIMIT 1");
    $w            = mysqli_fetch_assoc($query_detail);

    $nama_tampil    = $data_p['nama_warga'] ?? ($w['nama_lengkap'] ?? 'Warga Desa');
    $nik_pemohon    = $data_p['nik_warga'] ?? '-';
    $jenis_kelamin  = $w['jenis_kelamin'] ?? '-';
    $tanggal_lahir  = $w['tanggal_lahir'] ?? '-';
    $pekerjaan      = $w['pekerjaan'] ?? '-';
    $alamat         = $w['alamat'] ?? ($data_p['alamat'] ?? 'Desa Botombawo');
    
    // Proteksi pencarian jalur B
    $tempat_db      = $w['tempat_lahir'] ?? ($data_p['tempat_lahir'] ?? '');
    $tempat_lahir   = !empty($tempat_db) && $tempat_db != '-' ? $tempat_db : 'Botombawo';
    
    $jenis_surat    = $data_p['jenis_surat'] ?? '';
    $keperluan_teks = $data_p['keperluan'] ?? '-';
}

if (!empty($data_p['no_surat'])) {
    $nomor_surat = $data_p['no_surat'];
} elseif (!empty($data['no_surat'])) {
    $nomor_surat = $data['no_surat'];
}

$judul_surat = "";
$isi_surat   = "";
$check_type  = strtolower($jenis_surat);

if (strpos($check_type, 'domisili') !== false) {
    $judul_surat = "SURAT KETERANGAN DOMISILI";
    $isi_surat   = "Bahwa nama tersebut di bawah ini adalah benar-benar penduduk yang berdomisili dan menetap di wilayah Desa Botombawo, Kecamatan Sitolu Ori, Kabupaten Nias Utara.";
} 
elseif (strpos($check_type, 'mampu') !== false || strpos($check_type, 'sktm') !== false) {
    $judul_surat = "SURAT KETERANGAN TIDAK MAMPU (SKTM)";
    $isi_surat   = "Bahwa nama tersebut di bawah ini adalah benar-benar warga Desa Botombawo yang tergolong dalam keluarga kurang mampu/ekonomi lemah. Surat keterangan ini diberikan untuk keperluan: " . htmlspecialchars($keperluan_teks) . ".";
} 
elseif (strpos($check_type, 'usaha') !== false || strpos($check_type, 'sku') !== false) {
    $judul_surat = "SURAT KETERANGAN USAHA (SKU)";
    $isi_surat   = "Bahwa nama tersebut di bawah ini benar memiliki usaha produktif aktif (UMKM) yang beroperasional di wilayah hukum otoritas administrasi Desa Botombawo. Surat keterangan ini diterbitkan untuk memenuhi keperluan: " . htmlspecialchars($keperluan_teks) . ".";
} 
else {
    $judul_surat = !empty($jenis_surat) ? strtoupper($jenis_surat) : "SURAT KETERANGAN KEPALA DESA";
    $isi_surat   = "Bahwa nama tersebut di bawah ini adalah benar-benar warga Desa Botombawo, Kecamatan Sitolu Ori, Kabupaten Nias Utara. Surat keterangan ini diberikan untuk memenuhi keperluan: " . htmlspecialchars($keperluan_teks) . ".";
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$link_validasi = $protocol . $_SERVER['HTTP_HOST'] . "/desa/cek_status.php?id=" . $id_param;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak - <?= htmlspecialchars($judul_surat); ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: "Times New Roman", Times, serif; background-color: #fff; padding: 0; margin: 0; color: #000; line-height: 1.4; }
        .kertas-surat { width: 210mm; max-height: 297mm; padding: 15mm 20mm; margin: auto; background: white; }
        
        .judul-surat { text-align: center; margin-top: 15px; }
        .judul-surat h4 { text-decoration: underline; margin-bottom: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; }
        .nomor-surat { text-align: center; margin-top: 4px; margin-bottom: 20px; font-weight: bold; font-size: 11pt; }
        .paragraf { text-indent: 40px; text-align: justify; margin-bottom: 12px; font-size: 11pt; }
        
        .tabel-identitas { margin: 12px 40px; border-collapse: collapse; font-size: 11pt; width: 85%; }
        .tabel-identitas td { padding: 3px 8px; vertical-align: top; }
        
        .ttd-table { width: 100%; margin-top: 25px; border-collapse: collapse; page-break-inside: avoid; }
        .ttd-wrapper { position: relative; width: 230px; margin: 0 auto; text-align: center; }
        
        .file-ttd {
            width: 140px;
            height: auto;
            margin: 5px auto;
            display: block;
        }

        .barcode-validasi-box {
            border: 1px dashed #666;
            padding: 4px;
            text-align: center;
            display: inline-block;
            background: #fff;
        }
        .qr-mini-image {
            width: 65px;
            height: 65px;
            display: block;
        }
        .text-mini-tte {
            font-size: 6pt;
            font-family: Arial, sans-serif;
            color: #555;
            margin-top: 2px;
        }
        
        .nama-kades-garis { 
            font-weight: bold; 
            text-decoration: underline; 
            text-transform: uppercase; 
            font-size: 11pt; 
            margin-top: 5px;
        }
        
        @media print {
            body { padding: 0; background-color: #fff; }
            .btn-cetak { display: none; }
            .kertas-surat { padding: 10mm 15mm; margin: 0; width: 100%; max-height: 100%; }
            @page { size: A4; margin: 0; }
        }
        .btn-cetak { position: fixed; bottom: 20px; right: 20px; padding: 12px 25px; background: #059669; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 9999; }
    </style>
</head>
<body>
    <button class="btn-cetak" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    
    <div class="kertas-surat">
        
        <table border="0" width="100%" style="border-bottom: 4px double #000; padding-bottom: 8px; margin-bottom: 15px;">
            <tr>
                <td width="15%" align="center" valign="middle">
                    <img src="Logo.png" alt="Logo Kabupaten Nias" style="width: 85px; height: auto; display: block;">
                </td>
                <td width="85%" align="center" valign="middle" style="padding-right: 8%;">
                    <h3 style="margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; line-height: 1.2;">PEMERINTAH KABUPATEN NIAS UTARA</h3>
                    <h3 style="margin: 0; font-size: 13pt; text-transform: uppercase; font-weight: bold; line-height: 1.2;">KECAMATAN SITOLU ORI</h3>
                    <h2 style="margin: 3px 0 0 0; font-size: 17pt; text-transform: uppercase; font-weight: bold; line-height: 1.2;">KEPALA DESA BOTOMBAWO</h2>
                    <p style="margin: 3px 0 0 0; font-size: 10pt; font-style: italic;">Alamat: Jl. Botombawo No. 01, Kode Pos 22852</p>
                </td>
            </tr>
        </table>

        <div class="judul-surat">
            <h4><?= htmlspecialchars($judul_surat); ?></h4>
        </div>
        <div class="nomor-surat">Nomor: <?= htmlspecialchars($nomor_surat); ?></div>
        
        <div class="isi-surat">
            <p class="paragraf">Yang bertanda tangan di bawah ini, Kepala Desa Botombawo, Kecamatan Sitolu Ori, Kabupaten Nias Utara, dengan ini menerangkan bahwa:</p>
            
            <table class="tabel-identitas">
                <tr>
                    <td width="150">Nama Lengkap</td>
                    <td width="10">:</td>
                    <td><strong><?= strtoupper(htmlspecialchars($nama_tampil)); ?></strong></td>
                </tr>
                <tr>
                    <td>NIK</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($nik_pemohon); ?></td>
                </tr>
                <tr>
                    <td>Tempat, Tgl Lahir</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($tempat_lahir); ?>, <?= ($tanggal_lahir != '-') ? date('d-m-Y', strtotime($tanggal_lahir)) : '-'; ?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($jenis_kelamin); ?></td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($pekerjaan); ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($alamat); ?></td>
                </tr>
            </table>
            
            <p class="paragraf"><?= $isi_surat; ?></p>
            <p class="paragraf">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        <table class="ttd-table" border="0">
            <tr>
                <td width="50%" align="left" valign="bottom" style="padding-left: 40px;">
                    <?php if ($status_surat == 'Selesai') { ?>
                        <div class="barcode-validasi-box">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($link_validasi); ?>" class="qr-mini-image" alt="QR Verifikasi">
                            <div class="text-mini-tte">SCAN VALIDASI<br>DOKUMEN ASLI</div>
                        </div>
                    <?php } ?>
                </td>
                
                <td width="50%" align="center" valign="top">
                    <div class="ttd-wrapper">
                        <p style="margin: 0 0 2px 0; font-size: 11pt;">Botombawo, <?= date('d-m-Y'); ?></p>
                        <p style="margin: 0 0 5px 0; font-size: 11pt; font-weight: bold;">Kepala Desa Botombawo,</p>
                        
                        <?php if ($status_surat == 'Selesai') { ?>
                            <img src="TTD.png" class="file-ttd" alt="Tanda Tangan Kades">
                        <?php } else { ?>
                            <div style="height: 75px;"></div>
                        <?php } ?>
                        
                        <div class="nama-kades-garis">BAPAK KEPALA DESA</div>
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>