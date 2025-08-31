<?php
require_once 'config.php';
require_once 'email_functions.php';

echo "<h1>PHP mail() Test for Railway</h1>";
echo "<hr>";

// Test 1: Direct PHP mail() function
echo "<h2>Test 1: Direct PHP mail() function</h2>";
$result1 = mail(
    'admin@miw-travel.com',
    'Direct PHP mail() Test - ' . date('Y-m-d H:i:s'),
    '<h1>Direct PHP mail() Test</h1><p>This email was sent using PHP mail() function directly on Railway at ' . date('Y-m-d H:i:s') . '</p>',
    "From: MIW Travel <noreply@miw-travel.com>\r\n" .
    "Reply-To: noreply@miw-travel.com\r\n" .
    "MIME-Version: 1.0\r\n" .
    "Content-type: text/html; charset=UTF-8\r\n"
);

if ($result1) {
    echo "<p style='color: green;'>✓ Direct PHP mail() returned TRUE</p>";
} else {
    echo "<p style='color: red;'>✗ Direct PHP mail() returned FALSE</p>";
}

echo "<hr>";

// Test 2: sendPHPMail function
echo "<h2>Test 2: sendPHPMail function</h2>";
$result2 = sendPHPMail(
    'admin@miw-travel.com',
    'sendPHPMail Test - ' . date('Y-m-d H:i:s'),
    '<h1>sendPHPMail Test</h1><p>This email was sent using sendPHPMail function on Railway at ' . date('Y-m-d H:i:s') . '</p>'
);

if ($result2) {
    echo "<p style='color: green;'>✓ sendPHPMail function returned TRUE</p>";
} else {
    echo "<p style='color: red;'>✗ sendPHPMail function returned FALSE</p>";
}

echo "<hr>";

// Test 3: PHPMailer with PHP mail() mode
echo "<h2>Test 3: PHPMailer with PHP mail() mode</h2>";
try {
    $mail = configurePHPMailer();
    $mail->addAddress('admin@miw-travel.com', 'Admin Test');
    $mail->Subject = 'PHPMailer PHP mail() Test - ' . date('Y-m-d H:i:s');
    $mail->Body = '<h1>PHPMailer PHP mail() Test</h1><p>This email was sent using PHPMailer in PHP mail() mode on Railway at ' . date('Y-m-d H:i:s') . '</p>';
    $mail->isHTML(true);
    
    $result3 = $mail->send();
    
    if ($result3) {
        echo "<p style='color: green;'>✓ PHPMailer send() returned TRUE</p>";
    } else {
        echo "<p style='color: red;'>✗ PHPMailer send() returned FALSE</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ PHPMailer Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";

// Configuration info
echo "<h2>Configuration Status</h2>";
echo "<p><strong>USE_PHP_MAIL:</strong> " . (defined('USE_PHP_MAIL') ? (USE_PHP_MAIL ? 'TRUE' : 'FALSE') : 'NOT DEFINED') . "</p>";
echo "<p><strong>USE_SMTP:</strong> " . (defined('USE_SMTP') ? (USE_SMTP ? 'TRUE' : 'FALSE') : 'NOT DEFINED') . "</p>";
echo "<p><strong>EMAIL_ENABLED:</strong> " . (defined('EMAIL_ENABLED') ? (EMAIL_ENABLED ? 'TRUE' : 'FALSE') : 'NOT DEFINED') . "</p>";

echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
