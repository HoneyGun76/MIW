<?php
require_once 'config.php';

if (!isset($_GET['nik'])) {
    die('NIK tidak valid');
}

$nik = $_GET['nik'];
$stmt = $conn->prepare("SELECT * FROM data_pembatalan WHERE nik = ?");
$stmt->execute([$nik]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die('Data tidak ditemukan');
}
?>

<div class="registration-details">
    <h3>Informasi Jamaah</h3>
    <table>
        <tr>
            <td width="30%"><strong>NIK</strong></td>
            <td><?= htmlspecialchars($record['nik']) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Lengkap</strong></td>
            <td><?= htmlspecialchars($record['nama']) ?></td>
        </tr>
        <tr>
            <td><strong>No. Telepon</strong></td>
            <td><?= htmlspecialchars($record['no_telp']) ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><?= htmlspecialchars($record['email']) ?></td>
        </tr>
    </table>

    <h3>Detail Pembatalan</h3>
    <table>
        <tr>
            <td width="30%"><strong>Alasan Pembatalan</strong></td>
            <td><?= htmlspecialchars($record['alasan']) ?></td>
        </tr>
        <tr>
            <td><strong>Waktu Pengajuan</strong></td>
            <td><?= $record['created_at'] ? date('d/m/Y H:i', strtotime($record['created_at'])) : 'Belum diupload' ?></td>
        </tr>
        <tr>
            <td><strong>Waktu Pembaruan</strong></td>
            <td><?= $record['updated_at'] ? date('d/m/Y H:i', strtotime($record['updated_at'])) : 'Belum diupload' ?></td>
        </tr>
    </table>
</div>