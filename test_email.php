<?php
// Test script to check email functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Email Configuration Test</h1>";

// Minimal configuration check without database
$isRailway = isset($_ENV['RAILWAY_ENVIRONMENT']) || 
             isset($_ENV['RAILWAY_PROJECT_ID']) || 
             getenv('RAILWAY_ENVIRONMENT') ||
             isset($_ENV['DB_HOST']);

if ($isRailway) {
    define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com');
    define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME') ?? '');
    define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?? '');
    define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587);
    define('SMTP_ENCRYPTION', $_ENV['SMTP_ENCRYPTION'] ?? getenv('SMTP_ENCRYPTION') ?? 'tls');
    $environment = 'railway';
} else {
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_USERNAME', 'drakestates@gmail.com');
    define('SMTP_PASSWORD', 'lqqj vnug vrau dkfa');
    define('SMTP_PORT', 587);
    define('SMTP_ENCRYPTION', 'tls');
    $environment = 'local';
}

echo "<h2>Current Environment: $environment</h2>";

echo "<h3>Email Configuration:</h3>";
echo "SMTP_HOST: " . SMTP_HOST . "<br>";
echo "SMTP_PORT: " . SMTP_PORT . "<br>";
echo "SMTP_USERNAME: " . SMTP_USERNAME . "<br>";
echo "Environment: $environment<br>";

// Test PHP mail() function
echo "<h3>Testing PHP mail() function:</h3>";
$to = 'drakestates@gmail.com';
$subject = 'Test from PHP mail() - ' . date('Y-m-d H:i:s');
$message = 'This is a test email from PHP mail() function';
$headers = "From: MIW Travel <drakestates@gmail.com>\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

$result = mail($to, $subject, $message, $headers);
echo "mail() function result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";

// Test WSL Postfix connection
echo "<h3>Testing WSL Postfix connection:</h3>";
$wsl_ip = '172.26.140.121';
$smtp_port = 25;

$socket = @fsockopen($wsl_ip, $smtp_port, $errno, $errstr, 10);
if ($socket) {
    echo "Connection to WSL Postfix ($wsl_ip:$smtp_port): SUCCESS<br>";
    $response = fgets($socket);
    echo "SMTP Response: " . htmlspecialchars($response) . "<br>";
    fclose($socket);
} else {
    echo "Connection to WSL Postfix ($wsl_ip:$smtp_port): FAILED<br>";
    echo "Error: $errno - $errstr<br>";
}

// Test current SMTP configuration
echo "<h3>Testing current SMTP configuration:</h3>";
$smtp_socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 10);
if ($smtp_socket) {
    echo "Connection to " . SMTP_HOST . ":" . SMTP_PORT . ": SUCCESS<br>";
    $response = fgets($smtp_socket);
    echo "SMTP Response: " . htmlspecialchars($response) . "<br>";
    fclose($smtp_socket);
} else {
    echo "Connection to " . SMTP_HOST . ":" . SMTP_PORT . ": FAILED<br>";
    echo "Error: $errno - $errstr<br>";
}
?>
