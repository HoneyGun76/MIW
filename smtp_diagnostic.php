<?php
/**
 * SMTP Diagnostic Script for Railway
 * Tests SMTP connectivity and email sending capabilities
 */

require_once 'config.php';
require_once 'email_functions.php';

// Only allow access in Railway environment or with specific parameter
if (!$isRailway && !isset($_GET['test'])) {
    die("Access denied. This diagnostic is for Railway environment only.");
}

echo "<h1>MIW Travel - SMTP Diagnostic</h1>";
echo "<p>Environment: " . ($isRailway ? 'Railway Production' : 'Local Development') . "</p>";
echo "<hr>";

// Test 1: Configuration Check
echo "<h2>1. SMTP Configuration</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><td><strong>SMTP_HOST</strong></td><td>" . SMTP_HOST . "</td></tr>";
echo "<tr><td><strong>SMTP_PORT</strong></td><td>" . SMTP_PORT . "</td></tr>";
echo "<tr><td><strong>SMTP_USERNAME</strong></td><td>" . (SMTP_USERNAME ? SMTP_USERNAME : '<em>Not Set</em>') . "</td></tr>";
echo "<tr><td><strong>SMTP_PASSWORD</strong></td><td>" . (SMTP_PASSWORD ? '***SET***' : '<em>Not Set</em>') . "</td></tr>";
echo "<tr><td><strong>SMTP_SECURE</strong></td><td>" . SMTP_SECURE . "</td></tr>";
echo "<tr><td><strong>EMAIL_FROM</strong></td><td>" . EMAIL_FROM . "</td></tr>";
echo "</table>";

// Test 2: Network Connectivity
echo "<h2>2. Network Connectivity Test</h2>";
$host = SMTP_HOST;
$port = SMTP_PORT;

echo "<p>Testing connection to $host:$port...</p>";

$connection = @fsockopen($host, $port, $errno, $errstr, 10);
if ($connection) {
    echo "<p style='color: green;'>✓ Network connection to SMTP server successful</p>";
    fclose($connection);
} else {
    echo "<p style='color: red;'>✗ Network connection failed: $errstr ($errno)</p>";
}

// Test 3: PHPMailer Test
echo "<h2>3. PHPMailer SMTP Test</h2>";

if (isset($_GET['send_test']) && $_GET['send_test'] === '1') {
    try {
        $mail = configurePHPMailer();
        $mail->addAddress('admin@miw-travel.com', 'Admin Test'); // Test recipient
        $mail->Subject = 'SMTP Test from Railway - ' . date('Y-m-d H:i:s');
        $mail->Body = '<h1>SMTP Test</h1><p>This is a test email sent from Railway at ' . date('Y-m-d H:i:s') . '</p>';
        $mail->isHTML(true);
        
        if (sendEmailWithFallback($mail)) {
            echo "<p style='color: green;'>✓ Test email sent successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Test email failed to send</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ PHPMailer Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p><a href='?send_test=1'>Click here to send test email</a></p>";
}

// Test 4: Fallback Method Test
echo "<h2>4. PHP mail() Function Test</h2>";

if (isset($_GET['test_fallback']) && $_GET['test_fallback'] === '1') {
    $result = sendFallbackEmail(
        'admin@miw-travel.com',
        'Fallback Email Test - ' . date('Y-m-d H:i:s'),
        '<h1>Fallback Test</h1><p>This is a fallback email test from Railway at ' . date('Y-m-d H:i:s') . '</p>'
    );
    
    if ($result) {
        echo "<p style='color: green;'>✓ Fallback email method successful</p>";
    } else {
        echo "<p style='color: red;'>✗ Fallback email method failed</p>";
    }
} else {
    echo "<p><a href='?test_fallback=1'>Click here to test fallback email</a></p>";
}

// Test 5: Environment Variables
echo "<h2>5. Environment Variables</h2>";
echo "<table border='1' style='border-collapse: collapse;'>";
$envVars = ['SMTP_HOST', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'SMTP_PORT', 'SMTP_ENCRYPTION'];
foreach ($envVars as $var) {
    $value = getenv($var) ?: $_ENV[$var] ?? 'Not Set';
    if ($var === 'SMTP_PASSWORD' && $value !== 'Not Set') {
        $value = '***SET***';
    }
    echo "<tr><td><strong>$var</strong></td><td>$value</td></tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><em>Diagnostic completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
