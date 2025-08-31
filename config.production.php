<?php
// Production database configuration
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$port = getenv('DB_PORT');

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

// Email configuration
define('SMTP_HOST', getenv('MAIL_HOST'));
define('SMTP_USERNAME', getenv('MAIL_USERNAME'));
define('SMTP_PASSWORD', getenv('MAIL_PASSWORD'));
define('SMTP_PORT', getenv('MAIL_PORT'));
define('SMTP_ENCRYPTION', getenv('MAIL_ENCRYPTION'));

// Other configurations
ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');
