<?php
// email_functions.php

require_once 'config.php';
require_once 'vendor/autoload.php';

// Include email queue functions for Railway
if ($isRailway) {
    require_once 'email_queue_functions.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Configure PHPMailer with default settings
 */
function configurePHPMailer() {
    $mail = new PHPMailer(true);
    
    // Check if we should use PHP mail() instead of SMTP
    if (defined('USE_PHP_MAIL') && USE_PHP_MAIL) {
        $mail->isMail(); // Use PHP mail() function
    } else {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        
        // Set longer timeout for Railway
        $mail->Timeout = 30;
    }
    
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
    
    return $mail;
}

/**
 * Simple PHP mail() function for Railway
 */
function sendPHPMail($to, $subject, $body, $from = null) {
    if (!$from) {
        $from = EMAIL_FROM;
    }
    
    $headers = "From: " . EMAIL_FROM_NAME . " <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Legacy function for backward compatibility
 */
function sendFallbackEmail($to, $subject, $body, $from = null) {
    return sendPHPMail($to, $subject, $body, $from);
}

/**
 * Build HTML email template with styling
 */
function buildEmailTemplate($title, $content) {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f6b127; color: #000; padding: 15px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background-color: #f6b127; color: #000; text-align: left; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .footer { margin-top: 20px; font-size: 0.9em; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>$title</h2>
            <p>MIW Travel</p>
        </div>
        <div class='content'>
            $content
        </div>
        <div class='footer'>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>
HTML;
}

/**
 * Build registration details table for admin email
 */
function buildRegistrationDetails($registrationData, $registrationType) {
    $currencySymbol = ($registrationData['currency'] ?? 'IDR') === 'USD' ? '$' : 'Rp';
    $amount = number_format($registrationData['harga_paket'] ?? 0, 0, ',', '.');
    
    $content = "<p>Berikut detail pendaftaran baru yang perlu diproses:</p>";
    
    $content .= "<h3>Informasi Jamaah</h3>
    <table>
        <tr><th width='30%'>Nama Lengkap</th><td>" . htmlspecialchars($registrationData['nama']) . "</td></tr>
        <tr><th>NIK</th><td>" . htmlspecialchars($registrationData['nik']) . "</td></tr>
        <tr><th>Tanggal Lahir</th><td>" . htmlspecialchars($registrationData['tanggal_lahir']) . "</td></tr>
        <tr><th>Jenis Kelamin</th><td>" . htmlspecialchars($registrationData['jenis_kelamin']) . "</td></tr>
        <tr><th>Alamat</th><td>" . htmlspecialchars($registrationData['alamat']) . "</td></tr>
        <tr><th>No. Telepon</th><td>" . htmlspecialchars($registrationData['no_telp']) . "</td></tr>
        <tr><th>Email</th><td>" . htmlspecialchars($registrationData['email']) . "</td></tr>
    </table>";
    
    if ($registrationType === 'Umroh' || $registrationType === 'Haji') {
        $content .= "<h3>Detail Paket</h3>
        <table>
            <tr><th width='30%'>Program</th><td>" . htmlspecialchars($registrationData['program_pilihan'] ?? '') . "</td></tr>
            <tr><th>Tipe Kamar</th><td>" . htmlspecialchars($registrationData['type_room_pilihan'] ?? '') . "</td></tr>
            <tr><th>Biaya</th><td>$currencySymbol $amount</td></tr>
        </table>";
    }
    
    $content .= "<p>Silakan cek dokumen pendukung di sistem administrasi.</p>";
    
    return $content;
}

/**
 * Build confirmation content for registrant
 */
function buildConfirmationContent($registrationData, $registrationType) {
    $content = "<p>Halo " . htmlspecialchars($registrationData['nama']) . ",</p>";
    $content .= "<p>Terima kasih telah mendaftar $registrationType dengan detail berikut:</p>";
    
    $content .= "<table>
        <tr><th width='30%'>NIK</th><td>" . htmlspecialchars($registrationData['nik']) . "</td></tr>
        <tr><th>Nama Lengkap</th><td>" . htmlspecialchars($registrationData['nama']) . "</td></tr>";
    
    if (isset($registrationData['program_pilihan'])) {
        $content .= "<tr><th>Program</th><td>" . htmlspecialchars($registrationData['program_pilihan']) . "</td></tr>";
    }
    
    $content .= "</table>";
    
    $content .= "<p>Tim kami akan segera menghubungi Anda untuk proses selanjutnya.</p>";
    $content .= "<p>Terima kasih atas kepercayaan Anda menggunakan layanan MIW Travel.</p>";
    
    return $content;
}

/**
 * Build payment confirmation details for admin email
 */
function buildPaymentConfirmationDetails($paymentData) {
    $content = "<p>Berikut detail konfirmasi pembayaran yang perlu diverifikasi:</p>";
    
    $content .= "<h3>Informasi Pembayaran</h3>
    <table>
        <tr><th width='30%'>Nama Lengkap</th><td>" . htmlspecialchars($paymentData['nama']) . "</td></tr>
        <tr><th>NIK</th><td>" . htmlspecialchars($paymentData['nik']) . "</td></tr>
        <tr><th>Program</th><td>" . htmlspecialchars($paymentData['program_pilihan']) . "</td></tr>
        <tr><th>Tipe Kamar</th><td>" . htmlspecialchars($paymentData['type_room_pilihan']) . "</td></tr>
        <tr><th>Atas Nama Transfer</th><td>" . htmlspecialchars($paymentData['transfer_account_name']) . "</td></tr>
        <tr><th>Jenis Pembayaran</th><td>" . htmlspecialchars($paymentData['payment_type']) . "</td></tr>
        <tr><th>Metode Pembayaran</th><td>" . htmlspecialchars($paymentData['payment_method']) . "</td></tr>
        <tr><th>Tanggal Pembayaran</th><td>" . htmlspecialchars($paymentData['payment_date']) . "</td></tr>
        <tr><th>Waktu Pembayaran</th><td>" . htmlspecialchars($paymentData['payment_time']) . "</td></tr>
    </table>";
    
    $content .= "<p>Bukti pembayaran telah diunggah ke sistem. Silakan cek melalui sistem administrasi.</p>";
    
    return $content;
}

/**
 * Send registration email to admin with attachments
 */
function sendRegistrationEmail($registrationData, $files, $registrationType = 'Umroh') {
    if (!EMAIL_ENABLED) {
        error_log("Email sending is disabled in config");
        return false;
    }

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL);
        
        // Add reply-to if needed
        if (!empty($registrationData['email'])) {
            $mail->addReplyTo($registrationData['email'], $registrationData['nama']);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Pendaftaran $registrationType Baru - " . $registrationData['nama'];
        
        // Build email content
        $emailContent = buildRegistrationDetails($registrationData, $registrationType);
        $mail->Body = buildEmailTemplate("Notifikasi Pendaftaran $registrationType", $emailContent);
        $mail->AltBody = strip_tags($emailContent);

        // Attachments
        foreach ($files as $fileType => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $mail->addAttachment(
                    $file['tmp_name'],
                    $file['name']
                );
            }
        }

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send confirmation email to registrant
 */
function sendConfirmationEmail($registrationData, $registrationType = 'Umroh') {
    if (!EMAIL_ENABLED || empty($registrationData['email'])) {
        return false;
    }

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($registrationData['email'], $registrationData['nama']);
        $mail->addReplyTo(EMAIL_FROM, EMAIL_FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = EMAIL_SUBJECT;
        
        // Build email content
        $emailContent = buildConfirmationContent($registrationData, $registrationType);
        $mail->Body = buildEmailTemplate("Konfirmasi Pendaftaran $registrationType", $emailContent);
        $mail->AltBody = strip_tags($emailContent);

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Confirmation email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send payment confirmation email to admin and registrant
 */
function sendPaymentConfirmationEmail($paymentData, $files = [], $registrationType = 'Umroh') {
    if (!EMAIL_ENABLED) {
        error_log("Email sending is disabled in config");
        return [
            'success' => false,
            'message' => "Email sending is disabled in config"
        ];
    }

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress(ADMIN_EMAIL); // Admin
        if (!empty($paymentData['email'])) {
            $mail->addAddress($paymentData['email'], $paymentData['nama']); // Registrant
        }
        
        // Add reply-to 
        $mail->addReplyTo(EMAIL_FROM, EMAIL_FROM_NAME);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Konfirmasi Pembayaran $registrationType - " . $paymentData['nama'];
        
        // Build email content
        $emailContent = buildPaymentConfirmationDetails($paymentData);
        $mail->Body = buildEmailTemplate("Konfirmasi Pembayaran $registrationType", $emailContent);
        $mail->AltBody = strip_tags($emailContent);

        $success = $mail->send();
        
        if ($success) {
            error_log("Payment confirmation email sent successfully to {$paymentData['email']} for {$paymentData['nama']}");
            return [
                'success' => true,
                'message' => !empty($paymentData['email']) ? 
                    "Email konfirmasi pembayaran telah dikirim ke {$paymentData['email']}" :
                    "Email konfirmasi pembayaran telah dikirim ke admin"
            ];
        }
        
        return [
            'success' => false,
            'message' => "Gagal mengirim email konfirmasi pembayaran"
        ];
    } catch (Exception $e) {
        error_log("Payment confirmation email sending failed: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'message' => "Error: " . $e->getMessage()
        ];
    }
}

/**
 * Build payment verification email content
 */
function buildPaymentVerificationContent($emailData) {
    $content = "<p>Halo " . htmlspecialchars($emailData['nama']) . ",</p>";
    $content .= "<p>Terima kasih. Pembayaran Anda untuk program " . htmlspecialchars($emailData['program_pilihan']) . " telah diverifikasi dengan detail berikut:</p>";
    
    $content .= "<table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>";
    $content .= "<tr><th style='text-align: left; padding: 8px; width: 30%; border: 1px solid #ddd;'>NIK</th><td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($emailData['nik']) . "</td></tr>";
    $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Jenis Program</th><td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($emailData['program_pilihan']) . "</td></tr>";
    $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Total Pembayaran</th><td style='padding: 8px; border: 1px solid #ddd;'>" . $emailData['currency'] . " " . number_format($emailData['payment_total'] ?? 0, 0, ',', '.') . "</td></tr>";
    
    if ($emailData['payment_remaining'] > 0) {
        $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Sisa Pembayaran</th><td style='padding: 8px; border: 1px solid #ddd;'>" . $emailData['currency'] . " " . number_format($emailData['payment_remaining'] ?? 0, 0, ',', '.') . "</td></tr>";
    }
    
    $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Status</th><td style='padding: 8px; border: 1px solid #ddd;'>Terverifikasi</td></tr>";
    $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Tanggal</th><td style='padding: 8px; border: 1px solid #ddd;'>" . $emailData['payment_date'] . "</td></tr>";
    if (isset($emailData['payment_time'])) {
        $content .= "<tr><th style='text-align: left; padding: 8px; border: 1px solid #ddd;'>Waktu</th><td style='padding: 8px; border: 1px solid #ddd;'>" . $emailData['payment_time'] . "</td></tr>";
    }
    $content .= "</table>";
    
    $content .= "<p>Kwitansi pembayaran terlampir dalam email ini. Mohon simpan sebagai bukti pembayaran yang sah.</p>";
    if ($emailData['payment_remaining'] > 0) {
        $content .= "<p>Mohon segera melakukan pelunasan sesuai dengan jadwal yang telah ditentukan.</p>";
    } else {
        $content .= "<p>Pembayaran Anda telah lunas. Terima kasih atas kepercayaan Anda.</p>";
    }
    
    return $content;
}



/**
 * Send rejection email to user
 */
function sendPaymentRejectionEmail($paymentData) {
    if (!EMAIL_ENABLED) {
        error_log("Email sending is disabled in config");
        return false;
    }

    try {
        $mail = configurePHPMailer();
        
        // Add recipients
        $mail->addAddress($paymentData['email'], $paymentData['nama']);
        $mail->addBCC(ADMIN_EMAIL);

        // Set content
        $mail->isHTML(true);
        $mail->Subject = 'Pembayaran Tidak Dapat Diverifikasi - MIW Travel';
        
        $content = buildPaymentRejectionContent($paymentData);
        $mail->Body = buildEmailTemplate('Pembayaran Tidak Dapat Diverifikasi', $content);
        $mail->AltBody = strip_tags($content);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Payment rejection email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send payment verification email to registrant with attachments
 */
function sendPaymentVerificationEmail($emailData, $attachments = []) {
    if (!EMAIL_ENABLED) {
        error_log("Email sending is disabled in config");
        return true;
    }

    try {
        $mailer = configurePHPMailer();
        
        $mailer->addAddress($emailData['email'], $emailData['nama']);
        $mailer->isHTML(true);
        $mailer->Subject = 'Konfirmasi Pembayaran - MIW Travel';
        
        // Build email content using the content builder
        $content = buildPaymentVerificationContent($emailData);
        $mailer->Body = buildEmailTemplate('Konfirmasi Pembayaran', $content);
        
        // Add attachments if any
        if (!empty($attachments)) {
            foreach ($attachments as $type => $file) {
                if (isset($file['tmp_name'], $file['name'])) {
                    $mailer->addAttachment($file['tmp_name'], $file['name']);
                }
            }
        }
        
        return $mailer->send();
    } catch (Exception $e) {
        error_log("Error sending payment verification email: " . $e->getMessage());
        return false;
    }
}

/**
 * Build payment rejection content
 */
function buildPaymentRejectionContent($paymentData) {
    $content = "<p>Kepada Yth. " . htmlspecialchars($paymentData['nama']) . ",</p>";
    
    $content .= "<p>Mohon maaf, pembayaran Anda untuk program " . 
                htmlspecialchars($paymentData['program_pilihan']) . 
                " dengan tanggal keberangkatan " . 
                date('d/m/Y', strtotime($paymentData['tanggal_keberangkatan'])) . 
                " tidak dapat diverifikasi karena alasan tertentu.</p>";
    
    $content .= "<p>Anda dapat melakukan pembayaran ulang atau menghubungi kami untuk informasi lebih lanjut.</p>";
    
    return $content;
}

function buildCancellationContent($cancellationData) {
    $content = "
        <h3>Detail Pembatalan</h3>
        <table border='1' cellpadding='5' cellspacing='0'>
            <tr><th width='30%'>NIK</th><td>" . htmlspecialchars($cancellationData['nik']) . "</td></tr>
            <tr><th>Nama Lengkap</th><td>" . htmlspecialchars($cancellationData['nama']) . "</td></tr>
            <tr><th>No. Telepon</th><td>" . htmlspecialchars($cancellationData['no_telp']) . "</td></tr>
            <tr><th>Email</th><td>" . htmlspecialchars($cancellationData['email']) . "</td></tr>
            <tr><th>Alasan Pembatalan</th><td>" . htmlspecialchars($cancellationData['alasan']) . "</td></tr>
            <tr><th>Tanggal Pengajuan</th><td>" . date('d/m/Y H:i:s') . "</td></tr>
        </table>
    ";
    
    return $content;
}

/**
 * Send cancellation notification email
 */
function sendCancellationEmail($cancellationData) {
    if (!EMAIL_ENABLED) {
        error_log("Email sending is disabled in config");
        return false;
    }

    try {
        $mail = configurePHPMailer();
        
        // Recipients
        $mail->addAddress(ADMIN_EMAIL);
        if (!empty($cancellationData['email'])) {
            $mail->addReplyTo($cancellationData['email'], $cancellationData['nama']);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Pengajuan Pembatalan - ' . $cancellationData['nama'];
        
        $emailContent = buildCancellationContent($cancellationData) . 
                       '<p>Dokumen pendukung telah diunggah ke sistem.</p>';
        
        $mail->Body = buildEmailTemplate('Pengajuan Pembatalan', $emailContent);
        $mail->AltBody = strip_tags($emailContent);

        return $mail->send();
    } catch (Exception $e) {
        error_log("Cancellation email sending failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send document upload notification email to admin
 */
function sendDocumentUploadEmail($jamaahData, $files) {
    try {
        $mail = configurePHPMailer();
        $mail->addAddress(ADMIN_EMAIL);
        
        // Set email subject
        $mail->Subject = 'Document Uploads - ' . $jamaahData['nama'] . ' (' . $jamaahData['nik'] . ')';
        
        // Build email content
        $content = "<h3>Document Uploads for Jamaah</h3>";
        $content .= "<p><strong>Name:</strong> " . htmlspecialchars($jamaahData['nama']) . "</p>";
        $content .= "<p><strong>NIK:</strong> " . htmlspecialchars($jamaahData['nik']) . "</p>";
        $content .= "<p><strong>Documents Uploaded:</strong></p><ul>";
        
        // Add files as attachments
        foreach ($files as $docType => $fileData) {
            if (!empty($fileData['tmp_name']) && !empty($fileData['name'])) {
                $filename = $jamaahData['nik'] . '_' . $docType . '_' . date('Ymd_His') . '_' . $fileData['name'];
                $mail->addAttachment($fileData['tmp_name'], $filename);
                $content .= "<li>" . ucwords(str_replace('_', ' ', $docType)) . "</li>";
            }
        }
        $content .= "</ul>";
        
        // Set email content
        $mail->isHTML(true);
        $mail->Body = buildEmailTemplate('Document Uploads', $content);
        
        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email send failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send pembatalan notification email to jamaah
 */
function sendPembatalanNotification($jamaahData, $dendaResult, $pembatalan_id) {
    try {
        $mail = configurePHPMailer();
        
        // Format currency
        $currencySymbol = $dendaResult['currency'] === 'USD' ? '$' : 'Rp';
        $dendaFormatted = number_format($dendaResult['denda_amount'], 0, ',', '.');
        $totalPriceFormatted = number_format($dendaResult['total_package_price'], 0, ',', '.');
        $refundFormatted = number_format($dendaResult['refund_amount'], 0, ',', '.');
        
        // Build email content
        $content = "<p>Yth. " . htmlspecialchars($jamaahData['nama']) . ",</p>";
        $content .= "<p>Kami informasikan bahwa program " . htmlspecialchars($jamaahData['program_pilihan']) . " Anda telah dibatalkan oleh admin kami.</p>";
        
        $content .= "<h3>Detail Pembatalan</h3>
        <table>
            <tr><th width='30%'>NIK</th><td>" . htmlspecialchars($jamaahData['nik']) . "</td></tr>
            <tr><th>Nama</th><td>" . htmlspecialchars($jamaahData['nama']) . "</td></tr>
            <tr><th>Program</th><td>" . htmlspecialchars($jamaahData['program_pilihan']) . "</td></tr>
            <tr><th>Tanggal Keberangkatan</th><td>" . date('d/m/Y', strtotime($dendaResult['departure_date'])) . "</td></tr>
            <tr><th>Tipe Kamar</th><td>" . htmlspecialchars($jamaahData['type_room_pilihan']) . "</td></tr>
        </table>";
        
        $content .= "<h3>Rincian Biaya Pembatalan</h3>
        <table>
            <tr><th width='30%'>Total Biaya Paket</th><td>$currencySymbol $totalPriceFormatted</td></tr>
            <tr><th>Denda Pembatalan (" . $dendaResult['denda_percentage'] . "%)</th><td style='color: red;'>$currencySymbol $dendaFormatted</td></tr>
            <tr><th>Dana yang Dikembalikan</th><td style='color: green;'>$currencySymbol $refundFormatted</td></tr>
        </table>";
        
        if ($dendaResult['denda_amount'] > 0) {
            $content .= "<h3>Instruksi Pembayaran Denda</h3>";
            $content .= "<p>Untuk menyelesaikan proses pembatalan, Anda diwajibkan membayar denda sebesar <strong>$currencySymbol $dendaFormatted</strong>.</p>";
            $content .= "<p>Silakan klik link berikut untuk melakukan pembayaran denda:</p>";
            
            // Create payment link
            $baseUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
            $paymentLink = $baseUrl . "/miw/form_pembatalan.php?mode=payment&pembatalan_id=" . $pembatalan_id . "&nik=" . urlencode($jamaahData['nik']);
            
            $content .= "<p><a href='$paymentLink' style='background-color: #f6b127; color: #000; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Bayar Denda Pembatalan</a></p>";
            
            $content .= "<p><strong>Catatan Penting:</strong></p>
            <ul>
                <li>Pembayaran denda harus diselesaikan dalam 7 hari kerja</li>
                <li>Setelah pembayaran diverifikasi, dana akan dikembalikan dalam 14 hari kerja</li>
                <li>Dana akan ditransfer ke rekening asal pembayaran</li>
            </ul>";
        } else {
            $content .= "<p>Tidak ada denda yang dikenakan untuk pembatalan ini. Proses pengembalian dana akan segera diproses.</p>";
        }
        
        $content .= "<p>Jika ada pertanyaan, silakan hubungi customer service kami.</p>";
        $content .= "<p>Terima kasih atas pengertian Anda.</p>";
        
        $title = "Pemberitahuan Pembatalan Program " . $jamaahData['program_pilihan'];
        $emailBody = buildEmailTemplate($title, $content);
        
        $mail->addAddress($jamaahData['email'], $jamaahData['nama']);
        $mail->Subject = $title;
        $mail->Body = $emailBody;
        $mail->isHTML(true);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Pembatalan email send failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send pembatalan completion email to jamaah
 */
function sendPembatalanCompletion($jamaahData, $dendaInfo, $isApproved = true) {
    try {
        $mail = configurePHPMailer();
        
        $currencySymbol = $dendaInfo['currency'] === 'USD' ? '$' : 'Rp';
        $refundFormatted = number_format($dendaInfo['refund_amount'], 0, ',', '.');
        
        if ($isApproved) {
            $title = "Pembatalan Disetujui - " . $jamaahData['program_pilihan'];
            $content = "<p>Yth. " . htmlspecialchars($jamaahData['nama']) . ",</p>";
            $content .= "<p style='color: green;'><strong>Pembatalan program Anda telah DISETUJUI dan diproses.</strong></p>";
            
            $content .= "<h3>Detail Pengembalian Dana</h3>
            <table>
                <tr><th width='30%'>Jumlah Dikembalikan</th><td style='color: green;'>$currencySymbol $refundFormatted</td></tr>
                <tr><th>Metode Pengembalian</th><td>Transfer ke rekening asal pembayaran</td></tr>
                <tr><th>Estimasi Waktu</th><td>7-14 hari kerja</td></tr>
            </table>";
            
            $content .= "<p>Dana akan ditransfer ke rekening yang sama dengan pembayaran terakhir Anda.</p>";
            $content .= "<p>Anda akan menerima kwitansi pembatalan sebagai lampiran email ini.</p>";
        } else {
            $title = "Pembatalan Ditolak - " . $jamaahData['program_pilihan'];
            $content = "<p>Yth. " . htmlspecialchars($jamaahData['nama']) . ",</p>";
            $content .= "<p style='color: red;'><strong>Pembatalan program Anda telah DITOLAK.</strong></p>";
            $content .= "<p>Silakan hubungi customer service untuk informasi lebih lanjut.</p>";
        }
        
        $content .= "<p>Terima kasih atas pengertian Anda.</p>";
        
        $emailBody = buildEmailTemplate($title, $content);
        
        $mail->addAddress($jamaahData['email'], $jamaahData['nama']);
        $mail->Subject = $title;
        $mail->Body = $emailBody;
        $mail->isHTML(true);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Pembatalan completion email send failed: " . $e->getMessage());
        return false;
    }
}