<?php
/**
 * Local Email Worker for MIW Travel
 * This script runs on your local machine and processes emails from Railway database
 * 
 * SETUP INSTRUCTIONS:
 * 1. Update database connection details below
 * 2. Update SMTP settings for your local email
 * 3. Run: php local_email_worker.php
 * 4. Keep running in background
 */

// ===========================================
// CONFIGURATION - UPDATE THESE VALUES
// ===========================================

// Railway Database Connection
$RAILWAY_DB_HOST = 'junction.proxy.rlwy.net'; // Get from Railway dashboard
$RAILWAY_DB_PORT = 'XXXXX'; // Get from Railway dashboard  
$RAILWAY_DB_NAME = 'railway';
$RAILWAY_DB_USER = 'root';
$RAILWAY_DB_PASS = 'UQfZRKdpYpgTjWirALSSSisnrrBDXrRO'; // From Railway

// Local SMTP Settings (use your preferred email service)
$LOCAL_SMTP_HOST = 'smtp.gmail.com';
$LOCAL_SMTP_PORT = 587;
$LOCAL_SMTP_USERNAME = 'drakestates@gmail.com'; // Your email
$LOCAL_SMTP_PASSWORD = 'lqqj vnug vrau dkfa'; // Your app password
$LOCAL_SMTP_ENCRYPTION = 'tls';
$LOCAL_EMAIL_FROM = 'drakestates@gmail.com';
$LOCAL_EMAIL_FROM_NAME = 'MIW Travel';

// Worker Settings
$WORKER_SLEEP_SECONDS = 30; // Check every 30 seconds
$MAX_EMAILS_PER_BATCH = 10;
$MAX_PROCESSING_TIME = 300; // 5 minutes

// ===========================================
// DO NOT EDIT BELOW THIS LINE
// ===========================================

require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "=== MIW Travel Email Worker Started ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "Checking Railway database every {$WORKER_SLEEP_SECONDS} seconds\n";
echo "Press Ctrl+C to stop\n\n";

// Database connection
try {
    $pdo = new PDO(
        "mysql:host={$RAILWAY_DB_HOST};port={$RAILWAY_DB_PORT};dbname={$RAILWAY_DB_NAME}",
        $RAILWAY_DB_USER,
        $RAILWAY_DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
    echo "✓ Connected to Railway database\n";
} catch (Exception $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Test SMTP connection
try {
    $testMail = new PHPMailer(true);
    $testMail->isSMTP();
    $testMail->Host = $LOCAL_SMTP_HOST;
    $testMail->SMTPAuth = true;
    $testMail->Username = $LOCAL_SMTP_USERNAME;
    $testMail->Password = $LOCAL_SMTP_PASSWORD;
    $testMail->SMTPSecure = $LOCAL_SMTP_ENCRYPTION;
    $testMail->Port = $LOCAL_SMTP_PORT;
    
    // Test connection without sending
    $testMail->SMTPConnect();
    $testMail->SMTPClose();
    echo "✓ SMTP connection successful\n\n";
} catch (Exception $e) {
    echo "⚠ SMTP connection warning: " . $e->getMessage() . "\n";
    echo "Worker will continue but emails may fail\n\n";
}

/**
 * Get pending emails from queue
 */
function getQueuedEmails($pdo, $limit) {
    $stmt = $pdo->prepare("
        SELECT * FROM email_queue 
        WHERE status = 'pending' AND attempts < max_attempts 
        ORDER BY priority DESC, created_at ASC 
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Update email status
 */
function updateEmailStatus($pdo, $id, $status, $errorMessage = null) {
    $stmt = $pdo->prepare("
        UPDATE email_queue 
        SET status = ?, processed_at = NOW(), error_message = ? 
        WHERE id = ?
    ");
    return $stmt->execute([$status, $errorMessage, $id]);
}

/**
 * Increment attempt count
 */
function incrementAttempts($pdo, $id) {
    $stmt = $pdo->prepare("
        UPDATE email_queue 
        SET attempts = attempts + 1, 
            status = CASE 
                WHEN attempts + 1 >= max_attempts THEN 'failed'
                ELSE 'pending'
            END
        WHERE id = ?
    ");
    return $stmt->execute([$id]);
}

/**
 * Send email via local SMTP
 */
function sendEmailViaSMTP($emailData) {
    global $LOCAL_SMTP_HOST, $LOCAL_SMTP_PORT, $LOCAL_SMTP_USERNAME, $LOCAL_SMTP_PASSWORD;
    global $LOCAL_SMTP_ENCRYPTION, $LOCAL_EMAIL_FROM, $LOCAL_EMAIL_FROM_NAME;
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = $LOCAL_SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $LOCAL_SMTP_USERNAME;
        $mail->Password = $LOCAL_SMTP_PASSWORD;
        $mail->SMTPSecure = $LOCAL_SMTP_ENCRYPTION;
        $mail->Port = $LOCAL_SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Email content
        $mail->setFrom($LOCAL_EMAIL_FROM, $LOCAL_EMAIL_FROM_NAME);
        $mail->addAddress($emailData['recipient_email'], $emailData['recipient_name']);
        $mail->isHTML(true);
        $mail->Subject = $emailData['subject'];
        $mail->Body = $emailData['body'];
        
        // Handle attachments if any
        if (!empty($emailData['attachments'])) {
            $attachments = json_decode($emailData['attachments'], true);
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    $mail->addAttachment($attachment['path'], $attachment['name']);
                }
            }
        }
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        echo "Email send failed: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Process a single email
 */
function processEmail($pdo, $email) {
    echo "[" . date('H:i:s') . "] Processing email #{$email['id']} to {$email['recipient_email']}\n";
    
    // Mark as processing
    updateEmailStatus($pdo, $email['id'], 'processing');
    
    try {
        $success = sendEmailViaSMTP($email);
        
        if ($success) {
            updateEmailStatus($pdo, $email['id'], 'sent');
            echo "✓ Email #{$email['id']} sent successfully\n";
            return true;
        } else {
            incrementAttempts($pdo, $email['id']);
            echo "✗ Email #{$email['id']} failed, attempt {$email['attempts']}\n";
            return false;
        }
        
    } catch (Exception $e) {
        updateEmailStatus($pdo, $email['id'], 'pending', $e->getMessage());
        incrementAttempts($pdo, $email['id']);
        echo "✗ Email #{$email['id']} error: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Main worker loop
 */
$startTime = time();
$emailsProcessed = 0;

while (true) {
    try {
        // Get pending emails
        $emails = getQueuedEmails($pdo, $MAX_EMAILS_PER_BATCH);
        
        if (empty($emails)) {
            echo "[" . date('H:i:s') . "] No pending emails, sleeping...\n";
        } else {
            echo "[" . date('H:i:s') . "] Found " . count($emails) . " pending emails\n";
            
            foreach ($emails as $email) {
                processEmail($pdo, $email);
                $emailsProcessed++;
                
                // Prevent overwhelming the SMTP server
                sleep(1);
            }
        }
        
        // Check if we should restart
        if (time() - $startTime > $MAX_PROCESSING_TIME) {
            echo "\n=== Worker restart after {$MAX_PROCESSING_TIME} seconds ===\n";
            echo "Emails processed: $emailsProcessed\n\n";
            $startTime = time();
            $emailsProcessed = 0;
        }
        
        sleep($WORKER_SLEEP_SECONDS);
        
    } catch (Exception $e) {
        echo "Worker error: " . $e->getMessage() . "\n";
        echo "Retrying in 60 seconds...\n";
        sleep(60);
    }
}
?>
