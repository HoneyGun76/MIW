<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'data_miw');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    $pdo = $conn; // Ensure compatibility with files using $pdo
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Connection failed. Please try again later.");
}

// Email configuration for development
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'drakestates@gmail.com');
define('SMTP_PASSWORD', 'lqqj vnug vrau dkfa');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_SECURE', 'tls');

// Other configurations
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

// Email settings
define('EMAIL_FROM', 'drakestates@gmail.com');
define('EMAIL_FROM_NAME', 'MIW Travel');
define('EMAIL_SUBJECT', 'Pendaftaran Umroh/Haji Anda');
define('ADMIN_EMAIL', 'drakestates@gmail.com'); // Add admin email
define('EMAIL_ENABLED', true); // Add email enabled flag

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    $pdo = $conn; // Ensure compatibility with files using $pdo
} catch (PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    die("Could not connect to the database. Please try again later.");
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

?>