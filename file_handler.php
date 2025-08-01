<?php
require_once 'config.php';

// Function to get file MIME type
function getMimeType($filepath) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filepath);
    finfo_close($finfo);
    return $mimeType;
}

// Function to output file for preview/download
function outputFile($filepath, $filename, $download = false) {
    if (!file_exists($filepath)) {
        header('HTTP/1.0 404 Not Found');
        exit('File not found.');
    }

    $mimeType = getMimeType($filepath);
    
    // Set headers
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($filepath));
    
    if ($download) {
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    } else {
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
    }
    
    // Output file
    readfile($filepath);
    exit;
}

// Validate and process request
if (isset($_GET['file']) && isset($_GET['type'])) {
    $file = urldecode($_GET['file']);
    $type = $_GET['type'];
    $action = isset($_GET['action']) ? $_GET['action'] : 'preview';
    
    // Map type to subdirectory
    $validTypes = [
        'documents' => 'documents',
        'payments' => 'payments',
        'cancellations' => 'cancellations'
    ];
    
    if (!isset($validTypes[$type])) {
        header('HTTP/1.0 400 Bad Request');
        exit('Invalid file type.');
    }
    
    // Setup paths
    $uploadsDir = __DIR__ . '/uploads/';
    $typePath = $uploadsDir . $validTypes[$type] . '/';
    
    // Clean and validate the file path
    $cleanFile = str_replace('..', '', $file); // Remove potential directory traversal
    $filepath = realpath($typePath . basename($cleanFile));
    
    // Final security check
    $realPath = realpath($filepath);
    $realTypePath = realpath($typePath);
    
    if ($realPath === false || strpos($realPath, $realTypePath) !== 0) {
        header('HTTP/1.0 403 Forbidden');
        exit('Access denied.');
    }
    outputFile($filepath, basename($file), $action === 'download');
}
?>
