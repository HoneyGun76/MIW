<?php
/**
 * Black Box Testing Implementation for MIW Travel System
 * This script performs comprehensive black box testing and logs results
 * 
 * IMPORTANT: This script creates test data and should NOT be deployed to Railway
 * It's for local testing and documentation purposes only
 */

// Prevent this from running in production
if (getenv('RAILWAY_ENVIRONMENT') || isset($_ENV['RAILWAY_ENVIRONMENT'])) {
    die("This testing script should not run in production Railway environment!");
}

// Include configuration
require_once 'config.php';

// Test Results Storage
$testResults = [
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => 'local_development',
    'categories' => []
];

// Helper function to log test results
function logTestResult($category, $testName, $description, $input, $expectedResult, $actualResult, $status) {
    global $testResults;
    
    if (!isset($testResults['categories'][$category])) {
        $testResults['categories'][$category] = [];
    }
    
    $testResults['categories'][$category][] = [
        'test_name' => $testName,
        'description' => $description,
        'input' => $input,
        'expected_result' => $expectedResult,
        'actual_result' => $actualResult,
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}

// Helper function to simulate HTTP requests
function simulateRequest($url, $method = 'GET', $data = null) {
    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $data ? http_build_query($data) : null
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    return $result !== false ? $result : 'ERROR_CONNECTION_FAILED';
}

// Test Category A: Konektivitas dan Deployment (Local simulation)
echo "=== BLACK BOX TESTING IMPLEMENTATION ===\n";
echo "Starting Local Black Box Testing...\n\n";

// A1: Database Connection Test
echo "A1: Testing Database Connection...\n";
try {
    $stmt = $conn->query("SELECT 1 as test");
    $result = $stmt->fetch();
    if ($result['test'] == 1) {
        logTestResult('konektivitas', 'database_connection', 'Test koneksi database', 'SELECT 1', 'Result: 1', 'Connected successfully', 'PASS');
        echo "✓ Database connection: PASS\n";
    }
} catch (Exception $e) {
    logTestResult('konektivitas', 'database_connection', 'Test koneksi database', 'SELECT 1', 'Connection success', $e->getMessage(), 'FAIL');
    echo "✗ Database connection: FAIL - " . $e->getMessage() . "\n";
}

// A2: Environment Variables Test
echo "A2: Testing Environment Variables...\n";
$requiredVars = ['DB_HOST', 'DB_NAME', 'DB_USER'];
$envStatus = 'PASS';
$envResults = [];

foreach ($requiredVars as $var) {
    if (defined($var) || getenv($var)) {
        $envResults[] = "$var: SET";
    } else {
        $envResults[] = "$var: MISSING";
        $envStatus = 'FAIL';
    }
}

logTestResult('konektivitas', 'environment_variables', 'Test variabel lingkungan', 'Check required vars', 'All variables set', implode(', ', $envResults), $envStatus);
echo ($envStatus == 'PASS' ? "✓" : "✗") . " Environment variables: $envStatus\n";

// Test Category B: Pendaftaran Jamaah
echo "\nB: Testing Pendaftaran Jamaah...\n";

// B1: Test Insert Jamaah Umroh
echo "B1: Testing Insert Jamaah Umroh...\n";
$testData = [
    'nama_lengkap' => 'Test Jamaah BlackBox',
    'nik' => '1234567890123456',
    'tempat_lahir' => 'Jakarta',
    'tanggal_lahir' => '1990-01-01',
    'jenis_kelamin' => 'L',
    'alamat' => 'Jl. Test No. 123',
    'no_telepon' => '08123456789',
    'email' => 'testblackbox@example.com',
    'paspor' => 'TEST123456',
    'paket_id' => 1,
    'jenis_perjalanan' => 'umroh'
];

try {
    $sql = "INSERT INTO jamaah (nama_lengkap, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_telepon, email, paspor, paket_id, jenis_perjalanan, tanggal_daftar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $testData['nama_lengkap'], $testData['nik'], $testData['tempat_lahir'],
        $testData['tanggal_lahir'], $testData['jenis_kelamin'], $testData['alamat'],
        $testData['no_telepon'], $testData['email'], $testData['paspor'],
        $testData['paket_id'], $testData['jenis_perjalanan']
    ]);
    
    if ($result) {
        $insertId = $conn->lastInsertId();
        logTestResult('pendaftaran', 'insert_jamaah_umroh', 'Test pendaftaran jamaah umroh', json_encode($testData), 'Data tersimpan dengan ID baru', "Inserted with ID: $insertId", 'PASS');
        echo "✓ Insert Jamaah Umroh: PASS (ID: $insertId)\n";
        
        // Store for cleanup
        $testInsertId = $insertId;
    }
} catch (Exception $e) {
    logTestResult('pendaftaran', 'insert_jamaah_umroh', 'Test pendaftaran jamaah umroh', json_encode($testData), 'Data tersimpan', $e->getMessage(), 'FAIL');
    echo "✗ Insert Jamaah Umroh: FAIL - " . $e->getMessage() . "\n";
}

// B2: Test Duplicate NIK Validation
echo "B2: Testing Duplicate NIK Validation...\n";
try {
    $duplicateData = $testData;
    $duplicateData['nama_lengkap'] = 'Test Duplicate NIK';
    
    $sql = "INSERT INTO jamaah (nama_lengkap, nik, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, no_telepon, email, paspor, paket_id, jenis_perjalanan, tanggal_daftar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $duplicateData['nama_lengkap'], $duplicateData['nik'], $duplicateData['tempat_lahir'],
        $duplicateData['tanggal_lahir'], $duplicateData['jenis_kelamin'], $duplicateData['alamat'],
        $duplicateData['no_telepon'], $duplicateData['email'], $duplicateData['paspor'],
        $duplicateData['paket_id'], $duplicateData['jenis_perjalanan']
    ]);
    
    // If it succeeds, that's actually a failure for this test
    logTestResult('pendaftaran', 'duplicate_nik_validation', 'Test validasi NIK duplikat', 'NIK yang sudah ada', 'Error duplicate entry', 'Insertion succeeded (validation failed)', 'FAIL');
    echo "✗ Duplicate NIK Validation: FAIL - Should have been rejected\n";
    
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate') !== false || strpos($e->getMessage(), 'UNIQUE') !== false) {
        logTestResult('pendaftaran', 'duplicate_nik_validation', 'Test validasi NIK duplikat', 'NIK yang sudah ada', 'Error duplicate entry', 'Duplicate constraint triggered', 'PASS');
        echo "✓ Duplicate NIK Validation: PASS\n";
    } else {
        logTestResult('pendaftaran', 'duplicate_nik_validation', 'Test validasi NIK duplikat', 'NIK yang sudah ada', 'Error duplicate entry', $e->getMessage(), 'UNCERTAIN');
        echo "? Duplicate NIK Validation: UNCERTAIN - " . $e->getMessage() . "\n";
    }
}

// Test Category C: Manajemen Paket
echo "\nC: Testing Manajemen Paket...\n";

// C1: Test Retrieve Paket List
echo "C1: Testing Retrieve Paket List...\n";
try {
    $sql = "SELECT id, nama_paket, harga, tanggal_keberangkatan FROM paket WHERE status = 'aktif' LIMIT 5";
    $stmt = $conn->query($sql);
    $pakets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($pakets) > 0) {
        logTestResult('manajemen_paket', 'retrieve_paket_list', 'Test pengambilan daftar paket', 'SELECT paket aktif', 'Data paket ditemukan', count($pakets) . ' paket ditemukan', 'PASS');
        echo "✓ Retrieve Paket List: PASS (" . count($pakets) . " pakets found)\n";
    } else {
        logTestResult('manajemen_paket', 'retrieve_paket_list', 'Test pengambilan daftar paket', 'SELECT paket aktif', 'Data paket ditemukan', 'No paket found', 'UNCERTAIN');
        echo "? Retrieve Paket List: UNCERTAIN - No active packages found\n";
    }
} catch (Exception $e) {
    logTestResult('manajemen_paket', 'retrieve_paket_list', 'Test pengambilan daftar paket', 'SELECT paket aktif', 'Data paket ditemukan', $e->getMessage(), 'FAIL');
    echo "✗ Retrieve Paket List: FAIL - " . $e->getMessage() . "\n";
}

// C2: Test Insert New Paket
echo "C2: Testing Insert New Paket...\n";
$paketTestData = [
    'nama_paket' => 'Test Paket BlackBox',
    'deskripsi' => 'Paket untuk testing black box',
    'harga' => 25000000,
    'tanggal_keberangkatan' => '2025-12-01',
    'durasi' => 14,
    'kapasitas' => 45,
    'jenis_perjalanan' => 'umroh',
    'status' => 'aktif'
];

try {
    $sql = "INSERT INTO paket (nama_paket, deskripsi, harga, tanggal_keberangkatan, durasi, kapasitas, jenis_perjalanan, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        $paketTestData['nama_paket'], $paketTestData['deskripsi'], $paketTestData['harga'],
        $paketTestData['tanggal_keberangkatan'], $paketTestData['durasi'], $paketTestData['kapasitas'],
        $paketTestData['jenis_perjalanan'], $paketTestData['status']
    ]);
    
    if ($result) {
        $paketInsertId = $conn->lastInsertId();
        logTestResult('manajemen_paket', 'insert_new_paket', 'Test penambahan paket baru', json_encode($paketTestData), 'Paket berhasil ditambahkan', "Inserted with ID: $paketInsertId", 'PASS');
        echo "✓ Insert New Paket: PASS (ID: $paketInsertId)\n";
    }
} catch (Exception $e) {
    logTestResult('manajemen_paket', 'insert_new_paket', 'Test penambahan paket baru', json_encode($paketTestData), 'Paket berhasil ditambahkan', $e->getMessage(), 'FAIL');
    echo "✗ Insert New Paket: FAIL - " . $e->getMessage() . "\n";
}

// Test Category D: Pembatalan
echo "\nD: Testing Pembatalan...\n";

// D1: Test Insert Pembatalan
echo "D1: Testing Insert Pembatalan...\n";
if (isset($testInsertId)) {
    $pembatalanData = [
        'jamaah_id' => $testInsertId,
        'alasan' => 'Testing black box pembatalan',
        'tanggal_pengajuan' => date('Y-m-d'),
        'status' => 'pending'
    ];
    
    try {
        $sql = "INSERT INTO pembatalan (jamaah_id, alasan, tanggal_pengajuan, status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            $pembatalanData['jamaah_id'], $pembatalanData['alasan'],
            $pembatalanData['tanggal_pengajuan'], $pembatalanData['status']
        ]);
        
        if ($result) {
            $pembatalanId = $conn->lastInsertId();
            logTestResult('pembatalan', 'insert_pembatalan', 'Test pengajuan pembatalan', json_encode($pembatalanData), 'Pembatalan berhasil diajukan', "Inserted with ID: $pembatalanId", 'PASS');
            echo "✓ Insert Pembatalan: PASS (ID: $pembatalanId)\n";
        }
    } catch (Exception $e) {
        logTestResult('pembatalan', 'insert_pembatalan', 'Test pengajuan pembatalan', json_encode($pembatalanData), 'Pembatalan berhasil diajukan', $e->getMessage(), 'FAIL');
        echo "✗ Insert Pembatalan: FAIL - " . $e->getMessage() . "\n";
    }
} else {
    logTestResult('pembatalan', 'insert_pembatalan', 'Test pengajuan pembatalan', 'Data jamaah tidak tersedia', 'Pembatalan berhasil diajukan', 'Cannot test - no jamaah ID', 'SKIP');
    echo "⚠ Insert Pembatalan: SKIPPED - No jamaah ID available\n";
}

// Test Category E: File Upload Simulation
echo "\nE: Testing File Upload Functionality...\n";

// E1: Test File Directory Access
echo "E1: Testing File Directory Access...\n";
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (is_dir($uploadDir) && is_writable($uploadDir)) {
    logTestResult('file_upload', 'directory_access', 'Test akses direktori upload', 'Check uploads/ directory', 'Directory accessible and writable', 'Directory exists and writable', 'PASS');
    echo "✓ File Directory Access: PASS\n";
} else {
    logTestResult('file_upload', 'directory_access', 'Test akses direktori upload', 'Check uploads/ directory', 'Directory accessible and writable', 'Directory not accessible', 'FAIL');
    echo "✗ File Directory Access: FAIL\n";
}

// E2: Test File Creation
echo "E2: Testing File Creation...\n";
$testFileName = $uploadDir . 'test_blackbox_' . time() . '.txt';
$testContent = 'This is a black box test file created at ' . date('Y-m-d H:i:s');

try {
    $result = file_put_contents($testFileName, $testContent);
    if ($result !== false) {
        logTestResult('file_upload', 'file_creation', 'Test pembuatan file', 'Create test file', 'File created successfully', "File created: $testFileName", 'PASS');
        echo "✓ File Creation: PASS\n";
        
        // Clean up immediately
        if (file_exists($testFileName)) {
            unlink($testFileName);
        }
    }
} catch (Exception $e) {
    logTestResult('file_upload', 'file_creation', 'Test pembuatan file', 'Create test file', 'File created successfully', $e->getMessage(), 'FAIL');
    echo "✗ File Creation: FAIL - " . $e->getMessage() . "\n";
}

// Test Category F: Data Validation
echo "\nF: Testing Data Validation...\n";

// F1: Test Date Validation
echo "F1: Testing Date Validation...\n";
$invalidDate = '2024-02-30'; // Invalid date
try {
    $date = DateTime::createFromFormat('Y-m-d', $invalidDate);
    if ($date && $date->format('Y-m-d') === $invalidDate) {
        logTestResult('validation', 'date_validation', 'Test validasi tanggal tidak valid', $invalidDate, 'Tanggal ditolak', 'Date accepted (validation failed)', 'FAIL');
        echo "✗ Date Validation: FAIL - Invalid date accepted\n";
    } else {
        logTestResult('validation', 'date_validation', 'Test validasi tanggal tidak valid', $invalidDate, 'Tanggal ditolak', 'Date rejected correctly', 'PASS');
        echo "✓ Date Validation: PASS\n";
    }
} catch (Exception $e) {
    logTestResult('validation', 'date_validation', 'Test validasi tanggal tidak valid', $invalidDate, 'Tanggal ditolak', 'Exception thrown: ' . $e->getMessage(), 'PASS');
    echo "✓ Date Validation: PASS (Exception handling)\n";
}

// F2: Test Email Validation
echo "F2: Testing Email Validation...\n";
$invalidEmail = 'invalid-email-format';
if (filter_var($invalidEmail, FILTER_VALIDATE_EMAIL)) {
    logTestResult('validation', 'email_validation', 'Test validasi email tidak valid', $invalidEmail, 'Email ditolak', 'Email accepted (validation failed)', 'FAIL');
    echo "✗ Email Validation: FAIL - Invalid email accepted\n";
} else {
    logTestResult('validation', 'email_validation', 'Test validasi email tidak valid', $invalidEmail, 'Email ditolak', 'Email rejected correctly', 'PASS');
    echo "✓ Email Validation: PASS\n";
}

// Cleanup Test Data
echo "\nCleaning up test data...\n";
try {
    if (isset($testInsertId)) {
        // Delete pembatalan first (foreign key constraint)
        $conn->prepare("DELETE FROM pembatalan WHERE jamaah_id = ?")->execute([$testInsertId]);
        // Delete jamaah
        $conn->prepare("DELETE FROM jamaah WHERE id = ?")->execute([$testInsertId]);
        echo "✓ Cleaned up test jamaah (ID: $testInsertId)\n";
    }
    
    if (isset($paketInsertId)) {
        $conn->prepare("DELETE FROM paket WHERE id = ?")->execute([$paketInsertId]);
        echo "✓ Cleaned up test paket (ID: $paketInsertId)\n";
    }
} catch (Exception $e) {
    echo "⚠ Cleanup warning: " . $e->getMessage() . "\n";
}

// Generate Test Report
echo "\n=== GENERATING TEST REPORT ===\n";

$reportContent = "BLACK BOX TESTING RESULTS\n";
$reportContent .= "========================\n\n";
$reportContent .= "Test Execution Date: " . $testResults['timestamp'] . "\n";
$reportContent .= "Environment: " . $testResults['environment'] . "\n\n";

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$skippedTests = 0;

foreach ($testResults['categories'] as $category => $tests) {
    $reportContent .= strtoupper($category) . "\n";
    $reportContent .= str_repeat("-", strlen($category)) . "\n";
    
    foreach ($tests as $test) {
        $totalTests++;
        $status = $test['status'];
        
        if ($status === 'PASS') $passedTests++;
        elseif ($status === 'FAIL') $failedTests++;
        elseif ($status === 'SKIP') $skippedTests++;
        
        $reportContent .= "\n" . $test['test_name'] . " - " . $status . "\n";
        $reportContent .= "Description: " . $test['description'] . "\n";
        $reportContent .= "Input: " . $test['input'] . "\n";
        $reportContent .= "Expected: " . $test['expected_result'] . "\n";
        $reportContent .= "Actual: " . $test['actual_result'] . "\n";
    }
    $reportContent .= "\n";
}

$reportContent .= "\nSUMMARY\n";
$reportContent .= "=======\n";
$reportContent .= "Total Tests: $totalTests\n";
$reportContent .= "Passed: $passedTests\n";
$reportContent .= "Failed: $failedTests\n";
$reportContent .= "Skipped: $skippedTests\n";
$reportContent .= "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";

// Save test report
$reportFile = 'diagrams/black_box_test_results_' . date('Y-m-d_H-i-s') . '.txt';
file_put_contents($reportFile, $reportContent);

echo "Test report saved to: $reportFile\n";
echo "\nTEST SUMMARY:\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passedTests\n";
echo "Failed: $failedTests\n";
echo "Skipped: $skippedTests\n";
echo "Success Rate: " . round(($passedTests / $totalTests) * 100, 2) . "%\n";

echo "\n=== BLACK BOX TESTING COMPLETED ===\n";
?>
