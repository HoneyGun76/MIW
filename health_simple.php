<?php
/**
 * Simple Health Check for Railway Deployment
 * This provides a basic health check that doesn't fail due to database issues
 */

// Set headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Basic check - if we get here, PHP is working
    $status = [
        'status' => 'healthy',
        'timestamp' => date('c'),
        'php_version' => PHP_VERSION,
        'memory_usage' => memory_get_usage(true),
        'uptime' => time(),
        'environment' => 'unknown'
    ];
    
    // Environment detection
    if (isset($_ENV['RAILWAY_ENVIRONMENT']) || getenv('RAILWAY_ENVIRONMENT')) {
        $status['environment'] = 'railway';
    } else {
        $status['environment'] = 'local';
    }
    
    // Optional database check - don't fail if database is down
    if (file_exists('config.php')) {
        try {
            // Temporarily disable all error output for health check
            $old_error_reporting = error_reporting(0);
            $old_display_errors = ini_get('display_errors');
            ini_set('display_errors', 0);
            
            // Capture any output that might occur
            ob_start();
            
            // Create isolated scope for config
            $config_result = (function() {
                try {
                    include 'config.php';
                    return [
                        'loaded' => true, 
                        'conn' => isset($conn) ? $conn : null,
                        'pdo' => isset($pdo) ? $pdo : null
                    ];
                } catch (Exception $e) {
                    return ['loaded' => false, 'error' => $e->getMessage()];
                }
            })();
            
            // Clean any output
            ob_end_clean();
            
            // Restore error reporting
            error_reporting($old_error_reporting);
            ini_set('display_errors', $old_display_errors);
            
            if ($config_result['loaded'] && $config_result['conn']) {
                try {
                    $stmt = $config_result['conn']->query("SELECT 1");
                    $status['database'] = 'connected';
                } catch (Exception $e) {
                    $status['database'] = 'connection_error';
                }
            } else {
                $status['database'] = 'no_connection';
            }
        } catch (Exception $e) {
            $status['database'] = 'config_error';
        }
    } else {
        $status['database'] = 'config_not_found';
    }
    
    // Always return 200 OK for basic health check
    http_response_code(200);
    echo json_encode($status, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    // Even if there's an error, return 200 for basic health
    http_response_code(200);
    echo json_encode([
        'status' => 'basic_healthy',
        'error' => substr($e->getMessage(), 0, 100),
        'timestamp' => date('c'),
        'message' => 'PHP is running but with errors'
    ], JSON_PRETTY_PRINT);
}
?>
