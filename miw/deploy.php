<?php
/**
 * Railway Deployment Test Script
 * This script tests the deployment environment and database connectivity
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🚂 Railway Deployment Test</h1>";

// Test 1: Environment Detection
echo "<h2>1. Environment Detection</h2>";
echo "RAILWAY_ENVIRONMENT: " . ($_ENV['RAILWAY_ENVIRONMENT'] ?? 'Not set') . "<br>";
echo "RAILWAY_PROJECT_ID: " . ($_ENV['RAILWAY_PROJECT_ID'] ?? 'Not set') . "<br>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "<br>";

// Test 2: Config Loading
echo "<h2>2. Config Loading Test</h2>";
try {
    require_once 'config.php';
    echo "✅ Config loaded successfully<br>";
    echo "Environment: " . getCurrentEnvironment() . "<br>";
    echo "Database Type: " . $db_config['type'] . "<br>";
} catch (Exception $e) {
    echo "❌ Config error: " . $e->getMessage() . "<br>";
}

// Test 3: Database Connection
echo "<h2>3. Database Connection Test</h2>";
try {
    if (isset($conn) && $conn instanceof PDO) {
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "✅ Database connected successfully<br>";
        echo "Test query result: " . $result['test'] . "<br>";
    } else {
        echo "❌ Database connection not available<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: PHP Environment
echo "<h2>4. PHP Environment</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";

// Test 5: File Permissions
echo "<h2>5. File System Test</h2>";
$upload_dir = getUploadDirectory();
echo "Upload Directory: " . $upload_dir . "<br>";
echo "Upload Dir Exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "<br>";
echo "Upload Dir Writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";

echo "<h2>✅ Deployment Test Complete</h2>";
echo "<p><a href='index.php'>← Back to Main Application</a></p>";
?>
