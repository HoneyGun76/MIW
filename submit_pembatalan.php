<?php
require_once 'config.php';
require_once 'email_functions.php';
require_once 'upload_handler.php';

function insertCancellationData($inputData, $kwitansiPath, $proofPath) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO data_pembatalan 
            (nik, nama, no_telp, email, alasan, kwitansi_path, proof_path) 
            VALUES 
            (:nik, :nama, :no_telp, :email, :alasan, :kwitansi_path, :proof_path)
        ");

        return $stmt->execute([
            ':nik' => $inputData['nik'],
            ':nama' => $inputData['nama'],
            ':no_telp' => $inputData['no_telp'],
            ':email' => $inputData['email'],
            ':alasan' => $inputData['alasan'],
            ':kwitansi_path' => $kwitansiPath,
            ':proof_path' => $proofPath
        ]);
        
    } catch(PDOException $e) {
        error_log("Database error in insertCancellationData: " . $e->getMessage());
        return false;
    }
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => [],
    'input' => []
];

// Collect form data
$inputData = [
    'nik' => trim($_POST['nik'] ?? ''),
    'nama' => trim($_POST['nama'] ?? ''),
    'no_telp' => trim($_POST['no_telp'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'alasan' => trim($_POST['alasan'] ?? '')
];

// Validate required fields and data
if (empty($inputData['nik'])) {
    $response['errors'][] = 'NIK harus diisi';
} elseif (!preg_match('/^\d{16}$/', $inputData['nik'])) {
    $response['errors'][] = 'Format NIK tidak valid (harus 16 digit)';
} else {
    // Verify NIK exists in data_jamaah
    $stmt = $conn->prepare("SELECT nik FROM data_jamaah WHERE nik = ?");
    $stmt->execute([$inputData['nik']]);
    if (!$stmt->fetch()) {
        $response['errors'][] = 'NIK tidak terdaftar dalam sistem';
    }
}

if (empty($inputData['nama'])) {
    $response['errors'][] = 'Nama lengkap harus diisi';
}

if (empty($inputData['no_telp'])) {
    $response['errors'][] = 'Nomor telepon harus diisi';
} elseif (!preg_match('/^[0-9+()-]{10,15}$/', str_replace(' ', '', $inputData['no_telp']))) {
    $response['errors'][] = 'Format nomor telepon tidak valid';
}

if (empty($inputData['email'])) {
    $response['errors'][] = 'Email harus diisi';
} elseif (!filter_var($inputData['email'], FILTER_VALIDATE_EMAIL)) {
    $response['errors'][] = 'Format email tidak valid';
}

if (empty($inputData['alasan'])) {
    $response['errors'][] = 'Alasan pembatalan harus diisi';
}

// Validate file uploads
$files = [];
if (empty($_FILES['kwitansi_path']['name'])) {
    $response['errors'][] = 'Kwitansi pembayaran harus diupload';
} else {
    $files[] = $_FILES['kwitansi_path'];
}

if (empty($_FILES['proof_path']['name'])) {
    $response['errors'][] = 'Bukti pembayaran harus diupload';
} else {
    $files[] = $_FILES['proof_path'];
}

// Check file types and sizes
foreach ($files as $file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        $response['errors'][] = "File {$file['name']} harus berupa JPG, PNG, atau PDF";
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        $response['errors'][] = "Ukuran file {$file['name']} tidak boleh melebihi 2MB";
    }
}

// If there are errors, redirect back
if (!empty($response['errors'])) {
    $response['input'] = $inputData;
    $encodedErrors = urlencode(implode("\n", $response['errors']));
    $encodedInput = urlencode(json_encode($inputData));
    header("Location: form_pembatalan.php?errors={$encodedErrors}&input={$encodedInput}");
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();
    
    // Initialize upload handler
    $uploadHandler = new UploadHandler();
    
    // Process kwitansi upload
    $kwitansiPath = $uploadHandler->generateCustomFilename($inputData['nik'], 'kwitansi');
    $kwitansiUpload = $uploadHandler->handleUpload(
        $_FILES['kwitansi_path'],
        'cancellations',
        $kwitansiPath
    );
    
    if (!$kwitansiUpload || isset($kwitansiUpload['error'])) {
        throw new Exception('Gagal mengupload kwitansi: ' . 
            (isset($kwitansiUpload['error']) ? $kwitansiUpload['error'] : 'Unknown error'));
    }
    
    // Process proof upload
    $proofPath = $uploadHandler->generateCustomFilename($inputData['nik'], 'bukti');
    $proofUpload = $uploadHandler->handleUpload(
        $_FILES['proof_path'],
        'cancellations',
        $proofPath
    );
    
    if (!$proofUpload || isset($proofUpload['error'])) {
        // Clean up kwitansi if proof upload fails
        if (file_exists($kwitansiUpload['path'])) {
            unlink($kwitansiUpload['path']);
        }
        throw new Exception('Gagal mengupload bukti pembayaran: ' . 
            (isset($proofUpload['error']) ? $proofUpload['error'] : 'Unknown error'));
    }

    // Insert to database with file paths
    $dbSuccess = insertCancellationData(
        $inputData,
        $kwitansiUpload['path'],
        $proofUpload['path']
    );
    
    if (!$dbSuccess) {
        // Clean up uploaded files if database insert fails
        if (file_exists($kwitansiUpload['path'])) {
            unlink($kwitansiUpload['path']);
        }
        if (file_exists($proofUpload['path'])) {
            unlink($proofUpload['path']);
        }
        throw new Exception('Gagal menyimpan data pembatalan');
    }

    // Commit transaction
    $conn->commit();

    // Send email notification to admin
    $emailData = array_merge($inputData, [
        'kwitansi_path' => $kwitansiUpload['path'],
        'proof_path' => $proofUpload['path']
    ]);
    
    try {
        $emailSuccess = buildCancellationContent($emailData);
        if (!$emailSuccess) {
            error_log("Warning: Email notification failed but data was saved successfully");
        }
    } catch (Exception $e) {
        error_log("Warning: Email error: " . $e->getMessage());
    }

    // Store success in session
    $_SESSION['cancellation_success'] = [
        'status' => true,
        'message' => 'Pembatalan berhasil diajukan',
        'timestamp' => time()
    ];
    
    // Redirect to success page
    header('Location: closing_page_pembatalan.php');
    exit;

} catch (Exception $e) {
    // Rollback transaction if it was started
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log detailed error for debugging
    error_log("Cancellation processing error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Provide user-friendly error message
    if (strpos($e->getMessage(), 'upload') !== false) {
        $response['errors'][] = 'Gagal mengunggah file. Pastikan ukuran file tidak melebihi 2MB dan format file sesuai ketentuan.';
    } else if (strpos($e->getMessage(), 'database') !== false) {
        $response['errors'][] = 'Gagal menyimpan data pembatalan. Silakan coba beberapa saat lagi.';
    } else {
        $response['errors'][] = 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.';
    }
    
    // Save failed attempt details in session for support reference
    $_SESSION['last_error'] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => $e->getMessage(),
        'input' => $inputData
    ];
    
    // Redirect back with error information
    $encodedErrors = urlencode(implode("\n", $response['errors']));
    $encodedInput = urlencode(json_encode($inputData));
    header("Location: form_pembatalan.php?errors={$encodedErrors}&input={$encodedInput}");
    exit;
}