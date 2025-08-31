<?php
require_once 'config.php';
require_once 'email_functions.php';

header('Content-Type: application/json');

if (!isset($_POST['nik']) || empty($_POST['nik'])) {
    echo json_encode(['success' => false, 'message' => 'NIK tidak valid']);
    exit;
}

try {
    // Get cancellation details
    $stmt = $conn->prepare("
        SELECT p.*, j.nama, j.email 
        FROM data_pembatalan p
        JOIN data_jamaah j ON p.nik = j.nik
        WHERE p.nik = ?
    ");
    $stmt->execute([$_POST['nik']]);
    $cancellation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cancellation) {
        echo json_encode(['success' => false, 'message' => 'Data pembatalan tidak ditemukan']);
        exit;
    }

    // Send confirmation email
    $mail = configurePHPMailer();
    $mail->addAddress($cancellation['email'], $cancellation['nama']);
    $mail->Subject = 'Konfirmasi Pembatalan Program - MIW Travel';

    // Build email content
    $emailContent = "
        <h2>Konfirmasi Pembatalan Program</h2>
        <p>Kepada Yth. {$cancellation['nama']},</p>
        <p>Pembatalan program Anda telah diverifikasi dan disetujui. Detail pembatalan:</p>
        <table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd;'><strong>NIK</strong></td>
                <td style='padding: 8px; border: 1px solid #ddd;'>{$cancellation['nik']}</td>
            </tr>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd;'><strong>Nama</strong></td>
                <td style='padding: 8px; border: 1px solid #ddd;'>{$cancellation['nama']}</td>
            </tr>
            <tr>
                <td style='padding: 8px; border: 1px solid #ddd;'><strong>Alasan Pembatalan</strong></td>
                <td style='padding: 8px; border: 1px solid #ddd;'>{$cancellation['alasan']}</td>
            </tr>
        </table>
        <p>Proses pembatalan telah selesai. Jika Anda memiliki pertanyaan lebih lanjut, silakan hubungi kami.</p>
        <p>Terima kasih atas kepercayaan Anda kepada MIW Travel.</p>
    ";

    $mail->Body = buildEmailTemplate('Konfirmasi Pembatalan Program', $emailContent);
    $mail->AltBody = strip_tags($emailContent);

    // Send email
    $mail->send();

    // Delete the cancellation record
    $stmt = $conn->prepare("DELETE FROM data_pembatalan WHERE nik = ?");
    $stmt->execute([$_POST['nik']]);

    // Also delete from data_jamaah table
    $stmt = $conn->prepare("DELETE FROM data_jamaah WHERE nik = ?");
    $stmt->execute([$_POST['nik']]);

    echo json_encode(['success' => true, 'message' => 'Pembatalan berhasil diverifikasi dan email konfirmasi telah dikirim']);
} catch (Exception $e) {
    error_log("Error in verify_cancellation.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat memproses pembatalan']);
}
