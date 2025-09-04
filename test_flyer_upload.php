<?php
/**
 * Test Flyer Upload Diagnostic Page
 * Use this to test flyer upload functionality in Railway
 */

require_once 'config.php';
require_once 'paket_functions.php';

header('Content-Type: application/json');

$response = [
    'status' => 'ok',
    'environment' => [],
    'directories' => [],
    'upload_test' => []
];

// Environment info
$response['environment'] = [
    'railway_env' => getenv('RAILWAY_ENVIRONMENT') ?: 'not set',
    'railway_project' => getenv('RAILWAY_PROJECT_ID') ?: 'not set',
    'is_railway_detected' => function_exists('getUploadsPath'),
    'uploads_path' => function_exists('getUploadsPath') ? getUploadsPath() : 'function not found'
];

// Directory checks
$uploadsPath = getUploadsPath();
$flyersPath = $uploadsPath . '/flyers';

$response['directories'] = [
    'uploads_exists' => is_dir($uploadsPath),
    'uploads_writable' => is_writable($uploadsPath),
    'flyers_exists' => is_dir($flyersPath),
    'flyers_writable' => is_writable($flyersPath),
    'uploads_path' => $uploadsPath,
    'flyers_path' => $flyersPath
];

// Test file creation
try {
    $testFile = $flyersPath . '/test_file.txt';
    if (file_put_contents($testFile, 'test content')) {
        $response['upload_test']['file_creation'] = 'success';
        $response['upload_test']['file_path'] = $testFile;
        
        // Clean up test file
        if (unlink($testFile)) {
            $response['upload_test']['file_cleanup'] = 'success';
        } else {
            $response['upload_test']['file_cleanup'] = 'failed';
        }
    } else {
        $response['upload_test']['file_creation'] = 'failed';
    }
} catch (Exception $e) {
    $response['upload_test']['error'] = $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
