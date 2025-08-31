<?php
/**
 * Basic Health Check for Railway Deployment
 * This provides a simple health check endpoint for Railway
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
        'uptime' => time()
    ];
    
    // Try to include config for database check (optional)
    if (file_exists('config.php')) {
        try {
            require_once 'config.php';
            
            // Quick database connectivity test
            if (isset($conn)) {
                $stmt = $conn->query("SELECT 1");
                $status['database'] = 'connected';
            } else {
                $status['database'] = 'config_loaded_no_connection';
            }
        } catch (Exception $e) {
            $status['database'] = 'connection_failed';
            $status['db_error'] = substr($e->getMessage(), 0, 100); // Limit error message
        }
    } else {
        $status['database'] = 'config_not_found';
    }
    
    http_response_code(200);
    echo json_encode($status, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'unhealthy',
        'error' => substr($e->getMessage(), 0, 100),
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT);
}
?>
