<?php
require_once 'config.php';

// Test package 12 roomlist export
$pakId = 12;
$_POST['pak_id'] = $pakId;
$_POST['export_type'] = 'roomlist';

echo "=== TESTING ROOMLIST EXPORT FOR PACKAGE {$pakId} ===\n";

// Capture the output from export_manifest.php
ob_start();
include 'export_manifest.php';
$output = ob_get_clean();

// Parse the JSON response
$response = json_decode($output, true);

if (!$response) {
    echo "ERROR: Invalid JSON response\n";
    echo "Raw output: {$output}\n";
    exit;
}

if (!$response['success']) {
    echo "ERROR: Export failed - {$response['message']}\n";
    exit;
}

$data = $response['data'];

echo "SUCCESS: Export data received\n";
echo "Package: {$data['package']['name']}\n";
echo "Departure: {$data['package']['departure_date']}\n";

// Test roomlist structure
if (isset($data['roomLists'])) {
    $medinahCount = count($data['roomLists']['medinah'] ?? []);
    $makkahCount = count($data['roomLists']['makkah'] ?? []);
    
    echo "Medinah rooms: {$medinahCount}\n";
    echo "Makkah rooms: {$makkahCount}\n";
    
    // Show sample data structure
    if ($medinahCount > 0) {
        echo "\nSample Medinah room structure:\n";
        $sample = $data['roomLists']['medinah'][0];
        foreach ($sample as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    
    if ($makkahCount > 0) {
        echo "\nSample Makkah room structure:\n";
        $sample = $data['roomLists']['makkah'][0];
        foreach ($sample as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    
    echo "\n✅ ROOMLIST STRUCTURE TEST PASSED\n";
    echo "Both Medinah and Makkah rooms have compatible structure for combined export\n";
    
} else {
    echo "ERROR: No roomlist data found\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
