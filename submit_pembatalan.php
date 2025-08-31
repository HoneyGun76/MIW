<?php
require_once 'config.php';
require_once 'email_functions.php';
require_once 'upload_handler.php';

// Check if this is payment mode submission
$paymentMode = isset($_POST['payment_mode']) && $_POST['payment_mode'] == '1';
$pembatalan_id = $_POST['pembatalan_id'] ?? null;

if ($paymentMode) {
    // Handle payment mode submission
    handlePaymentSubmission($pembatalan_id);
    exit;
}

function handlePaymentSubmission($pembatalan_id) {
    global $pdo;
    
    $nik = $_POST['nik'] ?? '';
    
    // Validate inputs
    if (empty($pembatalan_id) || empty($nik)) {
        redirectWithError('Data tidak valid');
        return;
    }
    
    // Validate file upload
    if (empty($_FILES['denda_payment']['name'])) {
        redirectWithError('Bukti pembayaran denda harus diupload');
        return;
    }
    
    $file = $_FILES['denda_payment'];
    $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!in_array($file['type'], $allowedTypes)) {
        redirectWithError('Hanya file JPG, PNG, atau PDF yang diperbolehkan');
        return;
    }
    
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        redirectWithError('Ukuran file tidak boleh melebihi 2MB');
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Verify pembatalan record exists and belongs to the NIK
        $stmt = $pdo->prepare("SELECT * FROM data_pembatalan WHERE id = ? AND nik = ?");
        $stmt->execute([$pembatalan_id, $nik]);
        $pembatalanData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pembatalanData) {
            throw new Exception('Data pembatalan tidak ditemukan');
        }
        
        // Upload payment proof
        $uploadDir = 'uploads/cancellations/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = 'denda_payment_' . $pembatalan_id . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Gagal menyimpan file');
        }
        
        // Update pembatalan record with payment proof and change status
        $statusJson = substr($pembatalanData['alasan'], 16); // Remove "ADMIN_INITIATED|"
        $statusInfo = json_decode($statusJson, true);
        $statusInfo['status'] = 'payment_submitted';
        $statusInfo['payment_proof'] = $uploadPath;
        $statusInfo['payment_submitted_at'] = date('Y-m-d H:i:s');
        
        $newAlasan = "ADMIN_INITIATED|" . json_encode($statusInfo);
        
        $stmt = $pdo->prepare("UPDATE data_pembatalan SET alasan = ?, proof_path = ? WHERE id = ?");
        $stmt->execute([$newAlasan, $uploadPath, $pembatalan_id]);
        
        // Get jamaah data for email
        $stmt = $pdo->prepare("
            SELECT j.*, p.program_pilihan 
            FROM data_jamaah j 
            LEFT JOIN data_paket p ON j.pak_id = p.pak_id 
            WHERE j.nik = ?
        ");
        $stmt->execute([$nik]);
        $jamaahData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Send notification email to admin about payment submission
        // In a real implementation, you might want to send this to admin
        
        $pdo->commit();
        
        redirectWithSuccess('Bukti pembayaran berhasil disubmit! Kami akan memverifikasi pembayaran Anda dan memproses pengembalian dana.', true);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Payment submission error: " . $e->getMessage());
        redirectWithError('Terjadi kesalahan: ' . $e->getMessage());
    }
}

function redirectWithError($message, $isPaymentMode = false) {
    $baseUrl = $isPaymentMode ? 'form_pembatalan.php?mode=payment' : 'form_pembatalan.php';
    header("Location: $baseUrl&errors=" . urlencode($message));
    exit;
}

function redirectWithSuccess($message, $isPaymentMode = false) {
    $baseUrl = $isPaymentMode ? 'form_pembatalan.php?mode=payment' : 'form_pembatalan.php';
    header("Location: $baseUrl&success=" . urlencode($message));
    exit;
}

function insertCancellationData($inputData, $kwitansiPath, $proofPath) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO data_pembatalan 
            (nik, nama, no_telp, email, alasan, kwitansi_path, proof_path) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $inputData['nik'],
            $inputData['nama'],
            $inputData['no_telp'],
            $inputData['email'],
            $inputData['alasan'],
            $kwitansiPath,
            $proofPath
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
    $stmt = $pdo->prepare("SELECT nik FROM data_jamaah WHERE nik = ?");
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