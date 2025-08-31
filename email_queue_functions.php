<?php
/**
 * Email Queue Functions for Railway → Local Email Processing
 * This file handles queuing emails instead of sending them directly
 */

require_once 'config.php';

/**
 * Queue an email for processing by local worker
 */
function queueEmail($recipient, $subject, $body, $type = 'general', $attachments = null, $priority = 5) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO email_queue (recipient_email, recipient_name, subject, body, email_type, attachments, priority) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $recipient['email'], 
            $recipient['name'] ?? '', 
            $subject, 
            $body, 
            $type,
            $attachments ? json_encode($attachments) : null,
            $priority
        ]);
        
        if ($result) {
            error_log("Email queued successfully: {$recipient['email']} - $subject");
            return $pdo->lastInsertId();
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Failed to queue email: " . $e->getMessage());
        return false;
    }
}

/**
 * Queue registration email for admin
 */
function queueRegistrationEmail($registrationData, $files, $registrationType = 'Umroh') {
    $subject = "Pendaftaran $registrationType Baru - " . $registrationData['nama'];
    $emailContent = buildRegistrationDetails($registrationData, $registrationType);
    $body = buildEmailTemplate("Notifikasi Pendaftaran $registrationType", $emailContent);
    
    // Process attachments for queue
    $attachmentData = [];
    if ($files) {
        foreach ($files as $fileType => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $attachmentData[] = [
                    'type' => $fileType,
                    'name' => $file['name'],
                    'path' => $file['tmp_name'], // Note: This might not persist
                    'size' => $file['size']
                ];
            }
        }
    }
    
    return queueEmail([
        'email' => ADMIN_EMAIL,
        'name' => 'Admin MIW Travel'
    ], $subject, $body, 'registration', $attachmentData, 8); // High priority
}

/**
 * Queue confirmation email for registrant
 */
function queueConfirmationEmail($registrationData, $registrationType = 'Umroh') {
    if (empty($registrationData['email'])) {
        return false;
    }
    
    $subject = EMAIL_SUBJECT;
    $emailContent = buildConfirmationContent($registrationData, $registrationType);
    $body = buildEmailTemplate("Konfirmasi Pendaftaran $registrationType", $emailContent);
    
    return queueEmail([
        'email' => $registrationData['email'],
        'name' => $registrationData['nama']
    ], $subject, $body, 'confirmation', null, 7); // Medium-high priority
}

/**
 * Queue payment confirmation email
 */
function queuePaymentConfirmationEmail($paymentData, $files = [], $registrationType = 'Umroh') {
    $subject = "Konfirmasi Pembayaran $registrationType - " . $paymentData['nama'];
    $emailContent = buildPaymentConfirmationDetails($paymentData);
    $body = buildEmailTemplate("Konfirmasi Pembayaran $registrationType", $emailContent);
    
    // Queue for admin
    $adminResult = queueEmail([
        'email' => ADMIN_EMAIL,
        'name' => 'Admin MIW Travel'
    ], $subject, $body, 'payment_confirmation', null, 7);
    
    // Queue for registrant if email provided
    $userResult = true;
    if (!empty($paymentData['email'])) {
        $userResult = queueEmail([
            'email' => $paymentData['email'],
            'name' => $paymentData['nama']
        ], $subject, $body, 'payment_confirmation', null, 6);
    }
    
    return [
        'success' => $adminResult && $userResult,
        'message' => $adminResult && $userResult ? 
            "Email konfirmasi pembayaran telah dijadwalkan untuk dikirim" :
            "Gagal menjadwalkan email konfirmasi pembayaran"
    ];
}

/**
 * Queue cancellation email
 */
function queueCancellationEmail($cancellationData) {
    $subject = 'Pengajuan Pembatalan - ' . $cancellationData['nama'];
    $emailContent = buildCancellationContent($cancellationData) .
                   '<p>Dokumen pendukung telah diunggah ke sistem.</p>';
    $body = buildEmailTemplate('Pengajuan Pembatalan', $emailContent);
    
    return queueEmail([
        'email' => ADMIN_EMAIL,
        'name' => 'Admin MIW Travel'
    ], $subject, $body, 'cancellation', null, 7);
}

/**
 * Queue document upload notification
 */
function queueDocumentUploadEmail($jamaahData, $files) {
    $subject = 'Document Uploads - ' . $jamaahData['nama'] . ' (' . $jamaahData['nik'] . ')';
    
    $content = "<h3>Document Uploads for Jamaah</h3>";
    $content .= "<p><strong>Name:</strong> " . htmlspecialchars($jamaahData['nama']) . "</p>";
    $content .= "<p><strong>NIK:</strong> " . htmlspecialchars($jamaahData['nik']) . "</p>";
    $content .= "<p><strong>Documents Uploaded:</strong></p><ul>";
    
    foreach ($files as $docType => $fileData) {
        if (!empty($fileData['tmp_name']) && !empty($fileData['name'])) {
            $content .= "<li>" . ucwords(str_replace('_', ' ', $docType)) . "</li>";
        }
    }
    $content .= "</ul>";
    
    $body = buildEmailTemplate('Document Uploads', $content);
    
    return queueEmail([
        'email' => ADMIN_EMAIL,
        'name' => 'Admin MIW Travel'
    ], $subject, $body, 'document_upload', null, 5);
}

/**
 * Get queue statistics for monitoring
 */
function getEmailQueueStats() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT 
                status,
                COUNT(*) as count,
                MIN(created_at) as oldest,
                MAX(created_at) as newest
            FROM email_queue 
            GROUP BY status
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Failed to get email queue stats: " . $e->getMessage());
        return [];
    }
}

/**
 * Legacy compatibility - these functions now queue emails instead of sending
 */
function sendRegistrationEmail($registrationData, $files, $registrationType = 'Umroh') {
    return queueRegistrationEmail($registrationData, $files, $registrationType);
}

function sendConfirmationEmail($registrationData, $registrationType = 'Umroh') {
    return queueConfirmationEmail($registrationData, $registrationType);
}

function sendPaymentConfirmationEmail($paymentData, $files = [], $registrationType = 'Umroh') {
    return queuePaymentConfirmationEmail($paymentData, $files, $registrationType);
}

function sendCancellationEmail($cancellationData) {
    return queueCancellationEmail($cancellationData);
}

function sendDocumentUploadEmail($jamaahData, $files) {
    return queueDocumentUploadEmail($jamaahData, $files);
}
?>
