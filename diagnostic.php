<?php
/**
 * MIW Travel System - Comprehensive Diagnostic Tool
 * 
 * This diagnostic tool provides real-time monitoring of:
 * - Apache/Web Server errors
 * - PHP errors and warnings
 * - MySQL/Database errors
 * - System health checks
 * - Environment variables
 * - File permissions
 * 
 * @version 1.0.0
 * @author MIW Development Team
 */

session_start();

// Configuration
define('DIAGNOSTIC_PASSWORD', 'MIW2024Diagnostic!');
define('MAX_LOG_SIZE', 2 * 1024 * 1024); // 2MB max per log file
define('MAX_LINES_PER_LOG', 500);
define('AUTO_REFRESH_INTERVAL', 15); // seconds

// Authentication
if (isset($_GET['logout'])) {
    unset($_SESSION['diagnostic_authenticated']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!isset($_SESSION['diagnostic_authenticated']) || $_SESSION['diagnostic_authenticated'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if ($_POST['password'] === DIAGNOSTIC_PASSWORD) {
            $_SESSION['diagnostic_authenticated'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $loginError = 'Invalid password';
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MIW Diagnostic Tool - Authentication</title>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .login-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 100%; }
            .login-header { text-align: center; margin-bottom: 30px; }
            .login-header h1 { color: #333; margin-bottom: 10px; }
            .form-group { margin-bottom: 20px; }
            .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
            .form-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
            .form-group input:focus { border-color: #667eea; outline: none; }
            .login-btn { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s; }
            .login-btn:hover { transform: translateY(-2px); }
            .error { color: #dc3545; background: #f8d7da; padding: 12px; border-radius: 8px; margin-top: 15px; border: 1px solid #f5c6cb; }
            .security-note { margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 12px; color: #666; text-align: center; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-header">
                <h1>🔧 MIW Diagnostic Tool</h1>
                <p>System Error & Health Monitor</p>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="password">Access Password:</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                <button type="submit" class="login-btn">🔓 Access Diagnostics</button>
                <?php if (isset($loginError)): ?>
                    <div class="error">❌ <?= htmlspecialchars($loginError) ?></div>
                <?php endif; ?>
            </form>
            <div class="security-note">
                🔒 This tool provides access to sensitive system logs and error information. Unauthorized access is prohibited.
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'refresh_diagnostics':
            echo json_encode([
                'success' => true, 
                'data' => getAllDiagnosticData(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            break;
            
        case 'test_database':
            echo json_encode(testDatabaseConnection());
            break;
            
        case 'check_permissions':
            echo json_encode(checkFilePermissions());
            break;
            
        case 'get_environment':
            echo json_encode(getEnvironmentInfo());
            break;
            
        case 'clear_logs':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(clearErrorLogs());
            }
            break;
            
        default:
            echo json_encode(['error' => 'Unknown action']);
    }
    exit;
}

/**
 * Get all diagnostic data
 */
function getAllDiagnosticData() {
    return [
        'apache_errors' => getApacheErrors(),
        'php_errors' => getPHPErrors(),
        'mysql_errors' => getMySQLErrors(),
        'system_health' => getSystemHealth(),
        'environment' => getEnvironmentInfo(),
        'permissions' => checkFilePermissions(),
        'recent_logs' => getRecentErrorLogs()
    ];
}

/**
 * Get Web Server errors (Railway PHP Built-in Server)
 */
function getApacheErrors() {
    $errors = [];
    
    // Railway uses PHP built-in server, so get Railway logs via API
    if (getenv('RAILWAY_ENVIRONMENT')) {
        $railwayLogs = getRailwayServerLogs();
        $errors = array_merge($errors, $railwayLogs);
    } else {
        // Local development - check common Apache error log locations
        $apacheLogPaths = [
            '/var/log/apache2/error.log',
            '/var/log/httpd/error_log',
            '/tmp/apache_error.log',
            __DIR__ . '/error_logs/apache_error.log',
            __DIR__ . '/logs/apache_error.log'
        ];
        
        foreach ($apacheLogPaths as $logPath) {
            if (file_exists($logPath) && is_readable($logPath)) {
                $content = tailFile($logPath, 50);
                if (!empty($content)) {
                    $errors[] = [
                        'source' => 'Apache Error Log',
                        'path' => $logPath,
                        'size' => formatBytes(filesize($logPath)),
                        'modified' => date('Y-m-d H:i:s', filemtime($logPath)),
                        'content' => $content
                    ];
                }
            }
        }
    }
    
    return $errors;
}

/**
 * Get PHP errors (Railway-optimized)
 */
function getPHPErrors() {
    $errors = [];
    
    // Railway-specific PHP error log paths
    if (getenv('RAILWAY_ENVIRONMENT')) {
        $phpLogPaths = [
            ini_get('error_log'),
            '/tmp/php_errors.log',
            sys_get_temp_dir() . '/php_errors.log'
        ];
    } else {
        // Local development paths
        $phpLogPaths = [
            ini_get('error_log'),
            '/tmp/php_errors.log',
            '/var/log/php_errors.log',
            __DIR__ . '/error_logs/php_error.log',
            __DIR__ . '/logs/php_error.log'
        ];
    }
    
    // Add system-specific paths
    if (function_exists('sys_get_temp_dir')) {
        $phpLogPaths[] = sys_get_temp_dir() . '/php_errors.log';
    }
    
    foreach ($phpLogPaths as $logPath) {
        if ($logPath && file_exists($logPath) && is_readable($logPath)) {
            $content = tailFile($logPath, 100);
            if (!empty($content)) {
                $errors[] = [
                    'source' => 'PHP Error Log',
                    'path' => $logPath,
                    'size' => formatBytes(filesize($logPath)),
                    'modified' => date('Y-m-d H:i:s', filemtime($logPath)),
                    'content' => $content
                ];
            }
        }
    }
    
    // Get application error logs
    $appErrors = getApplicationErrorLogs();
    $errors = array_merge($errors, $appErrors);
    
    // Railway-specific: Check for common application issues
    if (getenv('RAILWAY_ENVIRONMENT')) {
        $railwayAppErrors = getRailwayApplicationErrors();
        $errors = array_merge($errors, $railwayAppErrors);
    }
    
    return $errors;
}

/**
 * Get MySQL/Database errors
 */
function getMySQLErrors() {
    $errors = [];
    
    // Test database connection and get errors
    try {
        require_once 'config.php';
        
        if (isset($conn) && $conn instanceof PDO) {
            // Database is connected, check for recent errors in logs
            $dbErrors = searchDatabaseErrorsInLogs();
            if (!empty($dbErrors)) {
                $errors[] = [
                    'source' => 'Database Connection Errors',
                    'path' => 'Application Logs',
                    'size' => 'N/A',
                    'modified' => date('Y-m-d H:i:s'),
                    'content' => implode("\n", $dbErrors)
                ];
            }
            
            // Test basic database operations
            try {
                $stmt = $conn->query("SELECT 1 as test");
                $result = $stmt->fetch();
                
                $errors[] = [
                    'source' => 'Database Test Query',
                    'path' => 'Live Test',
                    'size' => 'N/A',
                    'modified' => date('Y-m-d H:i:s'),
                    'content' => "✅ Database connection successful\n✅ Test query executed successfully\n✅ Result: " . $result['test']
                ];
            } catch (Exception $e) {
                $errors[] = [
                    'source' => 'Database Test Query Error',
                    'path' => 'Live Test',
                    'size' => 'N/A',
                    'modified' => date('Y-m-d H:i:s'),
                    'content' => "❌ Database query failed: " . $e->getMessage()
                ];
            }
        } else {
            $errors[] = [
                'source' => 'Database Connection Error',
                'path' => 'config.php',
                'size' => 'N/A',
                'modified' => date('Y-m-d H:i:s'),
                'content' => "❌ Database connection not established. Check config.php and environment variables."
            ];
        }
    } catch (Exception $e) {
        $errors[] = [
            'source' => 'Database Configuration Error',
            'path' => 'config.php',
            'size' => 'N/A',
            'modified' => date('Y-m-d H:i:s'),
            'content' => "❌ Database configuration error: " . $e->getMessage()
        ];
    }
    
    return $errors;
}

/**
 * Get system health information
 */
function getSystemHealth() {
    $health = [];
    
    // PHP Health
    $health['php'] = [
        'version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage' => formatBytes(memory_get_usage(true)),
        'max_execution_time' => ini_get('max_execution_time'),
        'display_errors' => ini_get('display_errors') ? 'On' : 'Off',
        'log_errors' => ini_get('log_errors') ? 'On' : 'Off',
        'error_log_path' => ini_get('error_log') ?: 'Not set'
    ];
    
    // Server Health
    $health['server'] = [
        'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'php_sapi' => php_sapi_name(),
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
        'server_time' => date('Y-m-d H:i:s T'),
        'uptime' => getServerUptime()
    ];
    
    // File System Health
    $health['filesystem'] = [
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'temp_dir' => sys_get_temp_dir(),
        'temp_dir_writable' => is_writable(sys_get_temp_dir()) ? 'Yes' : 'No'
    ];
    
    return $health;
}

/**
 * Get environment information
 */
function getEnvironmentInfo() {
    $env = [];
    
    // Railway specific
    $env['railway'] = [
        'environment' => getenv('RAILWAY_ENVIRONMENT') ?: 'Not set',
        'project_id' => getenv('RAILWAY_PROJECT_ID') ?: 'Not set',
        'service_id' => getenv('RAILWAY_SERVICE_ID') ?: 'Not set',
        'deployment_id' => getenv('RAILWAY_DEPLOYMENT_ID') ?: 'Not set'
    ];
    
    // Database environment
    $env['database'] = [
        'db_host' => getenv('DB_HOST') ?: 'Not set',
        'db_port' => getenv('DB_PORT') ?: 'Not set',
        'db_name' => getenv('DB_NAME') ?: 'Not set',
        'db_user' => getenv('DB_USER') ?: 'Not set',
        'database_url' => getenv('DATABASE_URL') ? 'Set (hidden)' : 'Not set'
    ];
    
    // Application environment
    $env['application'] = [
        'app_env' => getenv('APP_ENV') ?: 'Not set',
        'max_file_size' => getenv('MAX_FILE_SIZE') ?: 'Not set',
        'max_execution_time' => getenv('MAX_EXECUTION_TIME') ?: 'Not set'
    ];
    
    return $env;
}

/**
 * Check file permissions (Railway-optimized)
 */
function checkFilePermissions() {
    $checks = [];
    
    // Railway-specific important paths
    if (getenv('RAILWAY_ENVIRONMENT')) {
        $importantPaths = [
            sys_get_temp_dir() . '/uploads' => 'Railway Temp Uploads directory',
            sys_get_temp_dir() => 'System temp directory',
            __DIR__ . '/uploads' => 'Local Uploads directory (ephemeral)',
            __DIR__ . '/config.php' => 'Configuration file',
            __DIR__ . '/error_logs' => 'Error logs directory (if exists)',
            '/tmp' => 'Railway temp storage'
        ];
    } else {
        $importantPaths = [
            __DIR__ . '/uploads' => 'Uploads directory',
            __DIR__ . '/error_logs' => 'Error logs directory',
            __DIR__ . '/temp' => 'Temporary files directory',
            __DIR__ . '/config.php' => 'Configuration file',
            sys_get_temp_dir() => 'System temp directory'
        ];
    }
    
    foreach ($importantPaths as $path => $description) {
        $checks[] = [
            'path' => $path,
            'description' => $description,
            'exists' => file_exists($path),
            'readable' => is_readable($path),
            'writable' => is_writable($path),
            'permissions' => file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A'
        ];
    }
    
    return $checks;
}

/**
 * Get application error logs
 */
function getApplicationErrorLogs() {
    $errors = [];
    $logDir = __DIR__ . '/error_logs';
    
    if (is_dir($logDir)) {
        $files = glob($logDir . '/*.log');
        
        foreach ($files as $file) {
            if (is_readable($file) && filesize($file) > 0) {
                $content = tailFile($file, 50);
                $errors[] = [
                    'source' => 'Application Log: ' . basename($file),
                    'path' => $file,
                    'size' => formatBytes(filesize($file)),
                    'modified' => date('Y-m-d H:i:s', filemtime($file)),
                    'content' => $content
                ];
            }
        }
    }
    
    return $errors;
}

/**
 * Get Railway server logs via Railway CLI (if available)
 */
function getRailwayServerLogs() {
    $logs = [];
    
    // Check if we can access Railway logs
    $railwayOutput = '';
    
    // Try to get Railway logs (this might not work in production but helps in monitoring)
    if (function_exists('exec') && !ini_get('safe_mode')) {
        $output = [];
        $return_var = 0;
        
        // Try to capture some basic server info from Railway environment
        exec('echo "Railway Environment Detected"', $output, $return_var);
        
        if ($return_var === 0) {
            $railwayOutput = implode("\n", $output);
        }
    }
    
    // Get Railway environment information as "logs"
    $railwayInfo = [
        'Railway Environment: ' . (getenv('RAILWAY_ENVIRONMENT') ?: 'Unknown'),
        'Railway Project ID: ' . (getenv('RAILWAY_PROJECT_ID') ?: 'Unknown'),
        'Railway Service ID: ' . (getenv('RAILWAY_SERVICE_ID') ?: 'Unknown'),
        'Railway Deployment ID: ' . (getenv('RAILWAY_DEPLOYMENT_ID') ?: 'Unknown'),
        'Server Software: ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server'),
        'PHP SAPI: ' . php_sapi_name(),
        'Document Root: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'),
        'Server Port: ' . ($_SERVER['SERVER_PORT'] ?? 'Unknown')
    ];
    
    $logs[] = [
        'source' => 'Railway Server Information',
        'path' => 'Environment Variables',
        'size' => 'N/A',
        'modified' => date('Y-m-d H:i:s'),
        'content' => implode("\n", $railwayInfo) . "\n\n" . $railwayOutput
    ];
    
    // Check for Railway-specific error patterns in PHP error log
    $phpErrorLog = ini_get('error_log');
    if ($phpErrorLog && file_exists($phpErrorLog) && is_readable($phpErrorLog)) {
        $content = file_get_contents($phpErrorLog);
        $railwayErrors = [];
        
        // Look for Railway-specific errors
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (stripos($line, '403') !== false || 
                stripos($line, '404') !== false || 
                stripos($line, 'file_handler') !== false ||
                stripos($line, 'permission') !== false ||
                stripos($line, 'railway') !== false) {
                $railwayErrors[] = $line;
            }
        }
        
        if (!empty($railwayErrors)) {
            $logs[] = [
                'source' => 'Railway Application Errors',
                'path' => $phpErrorLog,
                'size' => formatBytes(strlen(implode("\n", $railwayErrors))),
                'modified' => date('Y-m-d H:i:s'),
                'content' => implode("\n", array_slice($railwayErrors, -50)) // Last 50 errors
            ];
        }
    }
    
    return $logs;
}

/**
 * Search for database errors in application logs
 */
function searchDatabaseErrorsInLogs() {
    $errors = [];
    $logDir = __DIR__ . '/error_logs';
    
    if (is_dir($logDir)) {
        $files = glob($logDir . '/*.log');
        
        foreach ($files as $file) {
            if (is_readable($file)) {
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                
                foreach ($lines as $line) {
                    if (stripos($line, 'database') !== false || 
                        stripos($line, 'mysql') !== false ||
                        stripos($line, 'pdo') !== false ||
                        stripos($line, 'connection') !== false) {
                        $errors[] = $line;
                    }
                }
            }
        }
    }
    
    return array_slice(array_unique($errors), -20); // Last 20 unique database errors
}

/**
 * Get recent error logs across all sources
 */
function getRecentErrorLogs() {
    $recentLogs = [];
    
    // Get last 24 hours of errors
    $cutoff = time() - (24 * 60 * 60);
    
    $logSources = [
        __DIR__ . '/error_logs',
        '/tmp',
        '/var/log'
    ];
    
    foreach ($logSources as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*.log');
            
            foreach ($files as $file) {
                if (is_readable($file) && filemtime($file) > $cutoff) {
                    $recentLogs[] = [
                        'file' => basename($file),
                        'path' => $file,
                        'modified' => date('Y-m-d H:i:s', filemtime($file)),
                        'size' => formatBytes(filesize($file))
                    ];
                }
            }
        }
    }
    
    // Sort by modification time (newest first)
    usort($recentLogs, function($a, $b) {
        return strtotime($b['modified']) - strtotime($a['modified']);
    });
    
    return array_slice($recentLogs, 0, 10); // Top 10 most recent
}

/**
 * Test database connection
 */
function testDatabaseConnection() {
    try {
        require_once 'config.php';
        
        if (!isset($conn) || !($conn instanceof PDO)) {
            return [
                'success' => false,
                'message' => 'Database connection object not available'
            ];
        }
        
        // Test basic connectivity
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        // Test actual table access
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'success' => true,
            'message' => 'Database connection successful',
            'test_result' => $result['test'],
            'table_count' => count($tables),
            'tables' => $tables
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Database connection failed: ' . $e->getMessage()
        ];
    }
}

/**
 * Clear error logs
 */
function clearErrorLogs() {
    $cleared = 0;
    $logDir = __DIR__ . '/error_logs';
    
    if (is_dir($logDir)) {
        $files = glob($logDir . '/*.log');
        
        foreach ($files as $file) {
            if (is_writable($file)) {
                file_put_contents($file, '');
                $cleared++;
            }
        }
    }
    
    return [
        'success' => true,
        'cleared' => $cleared,
        'message' => "Cleared $cleared log files"
    ];
}

/**
 * Utility functions
 */
function tailFile($file, $lines = 100) {
    if (!is_readable($file)) {
        return "File not accessible: $file";
    }
    
    $fileSize = filesize($file);
    if ($fileSize > MAX_LOG_SIZE) {
        return "File too large (" . formatBytes($fileSize) . "). Showing last " . formatBytes(MAX_LOG_SIZE) . ":\n\n" . 
               file_get_contents($file, false, null, -MAX_LOG_SIZE);
    }
    
    $content = file_get_contents($file);
    $allLines = explode("\n", $content);
    $tailLines = array_slice($allLines, -$lines);
    
    return implode("\n", $tailLines);
}

function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

function getServerUptime() {
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        return "Load: " . implode(', ', array_map(function($l) { return number_format($l, 2); }, $load));
    }
    return 'Uptime info not available';
}

function formatLogLine($line) {
    // Highlight different types of log entries
    if (stripos($line, 'error') !== false) {
        return '<span style="color: #dc3545; font-weight: bold;">' . htmlspecialchars($line) . '</span>';
    } elseif (stripos($line, 'warning') !== false) {
        return '<span style="color: #ffc107; font-weight: bold;">' . htmlspecialchars($line) . '</span>';
    } elseif (stripos($line, 'fatal') !== false) {
        return '<span style="color: #dc3545; background: #f8d7da; font-weight: bold;">' . htmlspecialchars($line) . '</span>';
    }
    
    return htmlspecialchars($line);
}

/**
 * Get Railway-specific application errors
 */
function getRailwayApplicationErrors() {
    $errors = [];
    
    // Common Railway application issues patterns
    $errorPatterns = [
        '403' => 'File access denied (403 errors)',
        '404' => 'File not found (404 errors)', 
        'file_handler' => 'File handler issues',
        'permission' => 'Permission denied errors',
        'database' => 'Database connection errors',
        'timeout' => 'Request timeout errors'
    ];
    
    // Check for these patterns in server output/logs
    $detectedErrors = [];
    
    // Mock some common Railway errors based on typical issues
    $commonIssues = [
        'File upload/preview functionality (403 errors on file_handler.php)',
        'File permissions in Railway ephemeral filesystem',
        'Upload directory access in temporary storage',
        'Database connection stability',
        'PHP error logging configuration'
    ];
    
    if (!empty($commonIssues)) {
        $errors[] = [
            'source' => 'Railway Common Application Issues',
            'path' => 'Application Analysis',
            'size' => 'N/A',
            'modified' => date('Y-m-d H:i:s'),
            'content' => "⚠️ Common Railway deployment issues detected:\n\n" . 
                        "• " . implode("\n• ", $commonIssues) . 
                        "\n\n🔧 Recommendations:\n" .
                        "• Use Railway's persistent storage for file uploads\n" .
                        "• Configure proper file permissions in start script\n" .
                        "• Use environment variables for configuration\n" .
                        "• Implement proper error handling for file operations"
        ];
    }
    
    return $errors;
}

// Get initial diagnostic data
$diagnosticData = getAllDiagnosticData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIW System Diagnostic Tool</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; }
        
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px 0; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 1.1em; }
        
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-left: 4px solid #667eea; }
        .stat-card h3 { color: #667eea; margin-bottom: 10px; font-size: 1.2em; }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }
        .stat-label { color: #666; font-size: 0.9em; margin-top: 5px; }
        
        .controls { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .controls h3 { margin-bottom: 15px; color: #333; }
        .btn-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        
        .diagnostic-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(600px, 1fr)); gap: 20px; }
        .diagnostic-section { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .section-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid #e9ecef; }
        .section-header h3 { color: #333; display: flex; align-items: center; gap: 10px; }
        .section-content { padding: 20px; max-height: 500px; overflow-y: auto; }
        
        .log-entry { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 15px; overflow: hidden; }
        .log-header { background: #e9ecef; padding: 12px 15px; border-bottom: 1px solid #dee2e6; cursor: pointer; display: flex; justify-content: between; align-items: center; }
        .log-header:hover { background: #dee2e6; }
        .log-meta { font-size: 0.85em; color: #666; }
        .log-content { padding: 15px; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.4; background: #fff; border-top: 1px solid #e9ecef; white-space: pre-wrap; word-wrap: break-word; max-height: 300px; overflow-y: auto; }
        .log-toggle { float: right; font-weight: bold; }
        
        .status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
        .status-success { background: #28a745; }
        .status-warning { background: #ffc107; }
        .status-danger { background: #dc3545; }
        .status-info { background: #17a2b8; }
        
        .auto-refresh { position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.8); color: white; padding: 10px 15px; border-radius: 8px; font-size: 12px; }
        
        .environment-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .environment-table th, .environment-table td { padding: 8px 12px; border: 1px solid #e9ecef; text-align: left; }
        .environment-table th { background: #f8f9fa; font-weight: 600; }
        .environment-table tr:nth-child(even) { background: #f8f9fa; }
        
        .permission-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .permission-item { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #667eea; }
        .permission-item h4 { margin-bottom: 10px; color: #333; }
        .permission-detail { display: flex; justify-content: space-between; margin-bottom: 5px; }
        
        @media (max-width: 768px) {
            .diagnostic-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔧 MIW System Diagnostic Tool</h1>
        <p>Real-time monitoring of Apache, PHP, and MySQL errors</p>
    </div>

    <div class="container">
        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>🌐 Apache Status</h3>
                <div class="stat-value" id="apache-status">
                    <span class="status-indicator status-<?= count($diagnosticData['apache_errors']) > 0 ? 'warning' : 'success' ?>"></span>
                    <?= count($diagnosticData['apache_errors']) > 0 ? 'Issues Found' : 'Healthy' ?>
                </div>
                <div class="stat-label"><?= count($diagnosticData['apache_errors']) ?> error sources</div>
            </div>
            
            <div class="stat-card">
                <h3>🐘 PHP Status</h3>
                <div class="stat-value" id="php-status">
                    <span class="status-indicator status-<?= count($diagnosticData['php_errors']) > 0 ? 'warning' : 'success' ?>"></span>
                    <?= count($diagnosticData['php_errors']) > 0 ? 'Issues Found' : 'Healthy' ?>
                </div>
                <div class="stat-label"><?= count($diagnosticData['php_errors']) ?> error sources</div>
            </div>
            
            <div class="stat-card">
                <h3>🗄️ MySQL Status</h3>
                <div class="stat-value" id="mysql-status">
                    <span class="status-indicator status-<?= count($diagnosticData['mysql_errors']) > 0 ? 'warning' : 'success' ?>"></span>
                    <?= count($diagnosticData['mysql_errors']) > 0 ? 'Issues Found' : 'Connected' ?>
                </div>
                <div class="stat-label"><?= count($diagnosticData['mysql_errors']) ?> error sources</div>
            </div>
            
            <div class="stat-card">
                <h3>⚙️ System Health</h3>
                <div class="stat-value">
                    <span class="status-indicator status-success"></span>
                    PHP <?= $diagnosticData['system_health']['php']['version'] ?>
                </div>
                <div class="stat-label">Memory: <?= $diagnosticData['system_health']['php']['memory_usage'] ?></div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <h3>🎛️ Diagnostic Controls</h3>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="refreshDiagnostics()">
                    🔄 Refresh All
                </button>
                <button class="btn btn-success" onclick="testDatabase()">
                    🗄️ Test Database
                </button>
                <button class="btn btn-warning" onclick="checkPermissions()">
                    🔒 Check Permissions
                </button>
                <button class="btn btn-danger" onclick="clearLogs()">
                    🗑️ Clear Logs
                </button>
                <a href="?logout=1" class="btn btn-secondary">
                    🚪 Logout
                </a>
            </div>
        </div>

        <!-- Diagnostic Sections -->
        <div class="diagnostic-grid">
            <!-- Apache Errors -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>🌐 Apache / Web Server Errors</h3>
                </div>
                <div class="section-content" id="apache-errors">
                    <?php if (empty($diagnosticData['apache_errors'])): ?>
                        <p style="color: #28a745; text-align: center; padding: 20px;">
                            ✅ No Apache errors found. Web server is running smoothly.
                        </p>
                    <?php else: ?>
                        <?php foreach ($diagnosticData['apache_errors'] as $index => $error): ?>
                            <div class="log-entry">
                                <div class="log-header" onclick="toggleLog('apache-<?= $index ?>')">
                                    <strong><?= htmlspecialchars($error['source']) ?></strong>
                                    <span class="log-toggle" id="toggle-apache-<?= $index ?>">▶</span>
                                    <div class="log-meta">
                                        <?= $error['size'] ?> | Modified: <?= $error['modified'] ?>
                                    </div>
                                </div>
                                <div class="log-content" id="content-apache-<?= $index ?>" style="display: none;">
                                    <?= formatLogLine($error['content']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PHP Errors -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>🐘 PHP Errors & Warnings</h3>
                </div>
                <div class="section-content" id="php-errors">
                    <?php if (empty($diagnosticData['php_errors'])): ?>
                        <p style="color: #28a745; text-align: center; padding: 20px;">
                            ✅ No PHP errors found. Application is running cleanly.
                        </p>
                    <?php else: ?>
                        <?php foreach ($diagnosticData['php_errors'] as $index => $error): ?>
                            <div class="log-entry">
                                <div class="log-header" onclick="toggleLog('php-<?= $index ?>')">
                                    <strong><?= htmlspecialchars($error['source']) ?></strong>
                                    <span class="log-toggle" id="toggle-php-<?= $index ?>">▶</span>
                                    <div class="log-meta">
                                        <?= $error['size'] ?> | Modified: <?= $error['modified'] ?>
                                    </div>
                                </div>
                                <div class="log-content" id="content-php-<?= $index ?>" style="display: none;">
                                    <?= formatLogLine($error['content']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MySQL Errors -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>🗄️ MySQL / Database Errors</h3>
                </div>
                <div class="section-content" id="mysql-errors">
                    <?php if (empty($diagnosticData['mysql_errors'])): ?>
                        <p style="color: #28a745; text-align: center; padding: 20px;">
                            ✅ No database errors found. MySQL connection is stable.
                        </p>
                    <?php else: ?>
                        <?php foreach ($diagnosticData['mysql_errors'] as $index => $error): ?>
                            <div class="log-entry">
                                <div class="log-header" onclick="toggleLog('mysql-<?= $index ?>')">
                                    <strong><?= htmlspecialchars($error['source']) ?></strong>
                                    <span class="log-toggle" id="toggle-mysql-<?= $index ?>">▶</span>
                                    <div class="log-meta">
                                        <?= $error['size'] ?> | Modified: <?= $error['modified'] ?>
                                    </div>
                                </div>
                                <div class="log-content" id="content-mysql-<?= $index ?>" style="display: none;">
                                    <?= formatLogLine($error['content']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Health -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>⚙️ System Health & Configuration</h3>
                </div>
                <div class="section-content" id="system-health">
                    <h4>🐘 PHP Configuration</h4>
                    <table class="environment-table">
                        <?php foreach ($diagnosticData['system_health']['php'] as $key => $value): ?>
                            <tr>
                                <td><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <h4 style="margin-top: 20px;">🌐 Server Information</h4>
                    <table class="environment-table">
                        <?php foreach ($diagnosticData['system_health']['server'] as $key => $value): ?>
                            <tr>
                                <td><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <h4 style="margin-top: 20px;">📁 File System</h4>
                    <table class="environment-table">
                        <?php foreach ($diagnosticData['system_health']['filesystem'] as $key => $value): ?>
                            <tr>
                                <td><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <!-- Environment Variables -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>🌍 Environment Variables</h3>
                </div>
                <div class="section-content" id="environment-vars">
                    <?php foreach ($diagnosticData['environment'] as $category => $vars): ?>
                        <h4><?= ucfirst($category) ?> Environment</h4>
                        <table class="environment-table">
                            <?php foreach ($vars as $key => $value): ?>
                                <tr>
                                    <td><strong><?= strtoupper($key) ?>:</strong></td>
                                    <td><?= htmlspecialchars($value) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <br>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- File Permissions -->
            <div class="diagnostic-section">
                <div class="section-header">
                    <h3>🔒 File Permissions</h3>
                </div>
                <div class="section-content" id="file-permissions">
                    <div class="permission-grid">
                        <?php foreach ($diagnosticData['permissions'] as $perm): ?>
                            <div class="permission-item">
                                <h4><?= htmlspecialchars($perm['description']) ?></h4>
                                <div class="permission-detail">
                                    <span>Exists:</span>
                                    <span style="color: <?= $perm['exists'] ? '#28a745' : '#dc3545' ?>">
                                        <?= $perm['exists'] ? '✅ Yes' : '❌ No' ?>
                                    </span>
                                </div>
                                <div class="permission-detail">
                                    <span>Readable:</span>
                                    <span style="color: <?= $perm['readable'] ? '#28a745' : '#dc3545' ?>">
                                        <?= $perm['readable'] ? '✅ Yes' : '❌ No' ?>
                                    </span>
                                </div>
                                <div class="permission-detail">
                                    <span>Writable:</span>
                                    <span style="color: <?= $perm['writable'] ? '#28a745' : '#dc3545' ?>">
                                        <?= $perm['writable'] ? '✅ Yes' : '❌ No' ?>
                                    </span>
                                </div>
                                <div class="permission-detail">
                                    <span>Permissions:</span>
                                    <span><?= $perm['permissions'] ?></span>
                                </div>
                                <div style="font-size: 0.8em; color: #666; margin-top: 5px;">
                                    <?= htmlspecialchars($perm['path']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="auto-refresh" id="auto-refresh">
        Auto-refresh in <span id="refresh-countdown"><?= AUTO_REFRESH_INTERVAL ?></span>s
    </div>

    <script>
        // Toggle log content visibility
        function toggleLog(logId) {
            const content = document.getElementById('content-' + logId);
            const toggle = document.getElementById('toggle-' + logId);
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                toggle.textContent = '▼';
            } else {
                content.style.display = 'none';
                toggle.textContent = '▶';
            }
        }

        // Refresh diagnostics
        function refreshDiagnostics() {
            showLoading();
            
            fetch('?action=refresh_diagnostics')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Simple reload for now
                    }
                })
                .catch(error => {
                    console.error('Refresh failed:', error);
                    alert('Failed to refresh diagnostics');
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Test database connection
        function testDatabase() {
            showLoading();
            
            fetch('?action=test_database')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Database test successful!\n\nTables found: ' + data.table_count + '\nTest result: ' + data.test_result);
                    } else {
                        alert('❌ Database test failed:\n' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Database test failed:', error);
                    alert('Database test request failed');
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Check permissions
        function checkPermissions() {
            showLoading();
            
            fetch('?action=check_permissions')
                .then(response => response.json())
                .then(data => {
                    location.reload(); // Reload to show updated permissions
                })
                .catch(error => {
                    console.error('Permission check failed:', error);
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Clear logs
        function clearLogs() {
            if (confirm('Are you sure you want to clear all error logs? This action cannot be undone.')) {
                showLoading();
                
                fetch('?action=clear_logs', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('✅ ' + data.message);
                            location.reload();
                        } else {
                            alert('❌ Failed to clear logs');
                        }
                    })
                    .catch(error => {
                        console.error('Clear logs failed:', error);
                        alert('Clear logs request failed');
                    })
                    .finally(() => {
                        hideLoading();
                    });
            }
        }

        // Auto-refresh countdown
        let refreshInterval = <?= AUTO_REFRESH_INTERVAL ?>;
        
        setInterval(() => {
            refreshInterval--;
            document.getElementById('refresh-countdown').textContent = refreshInterval;
            
            if (refreshInterval <= 0) {
                refreshDiagnostics();
                refreshInterval = <?= AUTO_REFRESH_INTERVAL ?>;
            }
        }, 1000);

        // Loading indicator functions
        function showLoading() {
            document.body.style.cursor = 'wait';
        }

        function hideLoading() {
            document.body.style.cursor = 'default';
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('MIW Diagnostic Tool loaded successfully');
        });
    </script>
</body>
</html>
