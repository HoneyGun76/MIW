<?php
/**
 * MIW Travel Management System - Railway-Optimized Configuration
 * 
 * This config automatically detects Railway environment and uses appropriate database settings
 * - Railway Production: Uses Railway MySQL service with environment variables
 * - Local Development: Uses localhost MySQL
 * 
 * @version 3.0.0 - Railway Optimized
 */

// Set error handling and time limits
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors in production
ini_set('log_errors', 1);
set_time_limit(30);
ini_set('max_execution_time', 30);

// Initialize global variables
$db_config = [];
$pdo = null;
$conn = null;

/**
 * ENVIRONMENT DETECTION
 */
// Railway environment detection
$isRailway = isset($_ENV['RAILWAY_ENVIRONMENT']) || 
             isset($_ENV['RAILWAY_PROJECT_ID']) || 
             getenv('RAILWAY_ENVIRONMENT') ||
             isset($_ENV['DB_HOST']); // Railway provides these automatically

if ($isRailway) {
    // RAILWAY PRODUCTION ENVIRONMENT
    $db_config = [
        'type' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 3306,
        'database' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'railway',
        'username' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '',
        'environment' => 'railway'
    ];
    
    // Railway production settings
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    
} else {
    // LOCAL DEVELOPMENT ENVIRONMENT
    $db_config = [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'data_miw',
        'username' => 'root',
        'password' => '',
        'environment' => 'local'
    ];
    
    // Local development settings
    ini_set('display_errors', 1);
}

/**
 * DATABASE CONNECTION
 */
try {
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    ];
    
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $options);
    $conn = $pdo; // Alias for compatibility
    
    // Set timezone
    $pdo->exec("SET time_zone = '+07:00'");
    
    // Log successful connection (development only)
    if ($db_config['environment'] === 'local') {
        error_log("MIW: Connected to {$db_config['environment']} database successfully");
    }
    
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    if ($db_config['environment'] === 'local') {
        die("Connection failed: " . $e->getMessage());
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Legacy compatibility constants
define('DB_HOST', $db_config['host']);
define('DB_PORT', $db_config['port']);
define('DB_NAME', $db_config['database']);
define('DB_USER', $db_config['username']);
define('DB_PASS', $db_config['password']);

/**
 * EMAIL CONFIGURATION
 */
// Environment-based email configuration
if ($isRailway) {
    // Railway production email settings
    define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com');
    define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME') ?? '');
    define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD') ?? '');
    define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587);
    define('SMTP_ENCRYPTION', $_ENV['SMTP_ENCRYPTION'] ?? getenv('SMTP_ENCRYPTION') ?? 'tls');
    define('EMAIL_ENABLED', true);
} else {
    // Local development email settings
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_USERNAME', 'drakestates@gmail.com');
    define('SMTP_PASSWORD', 'lqqj vnug vrau dkfa');
    define('SMTP_PORT', 587);
    define('SMTP_ENCRYPTION', 'tls');
    define('EMAIL_ENABLED', true);
}

// Common email settings
define('SMTP_SECURE', SMTP_ENCRYPTION);
define('EMAIL_FROM', SMTP_USERNAME ?: 'noreply@miw-travel.com');
define('EMAIL_FROM_NAME', 'MIW Travel');
define('EMAIL_SUBJECT', 'Pendaftaran Umroh/Haji Anda');
define('ADMIN_EMAIL', SMTP_USERNAME ?: 'admin@miw-travel.com');

/**
 * APPLICATION CONFIGURATION
 */
// File upload settings
define('MAX_FILE_SIZE', $_ENV['MAX_FILE_SIZE'] ?? '10M');
define('MAX_EXECUTION_TIME', $_ENV['MAX_EXECUTION_TIME'] ?? 300);

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Production session security
    if ($isRailway) {
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
    session_start();
}

/**
 * UTILITY FUNCTIONS
 */
function isProduction() {
    global $db_config;
    return isset($db_config['environment']) && $db_config['environment'] === 'railway';
}

function getCurrentEnvironment() {
    global $db_config;
    return $db_config['environment'] ?? 'unknown';
}

function getUploadDirectory() {
    if (isProduction()) {
        // Railway: use mounted volume for persistent uploads
        return '/app/uploads';
    } else {
        // Local: use uploads directory
        return __DIR__ . '/uploads';
    }
}

function ensureUploadDirectory() {
    $upload_dir = getUploadDirectory();
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    return $upload_dir;
}

// Environment logging (development only)
if (!isProduction()) {
    error_log("MIW Config: Environment = " . getCurrentEnvironment() . ", Database = MySQL");
}

?>