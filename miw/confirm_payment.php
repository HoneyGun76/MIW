<?php
require_once 'config.php';
require_once 'email_functions.php';

// Ensure error logs directory exists
if (!file_exists(__DIR__ . '/error_logs')) {
    mkdir(__DIR__ . '/error_logs', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Start transaction
        $conn->beginTransaction();

        // Validate required fields
        $requiredFields = ['nik', 'transfer_account_name', 'nama', 'program_pilihan'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Validate file upload
        if (!isset($_FILES['payment_path']) || $_FILES['payment_path']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Payment proof file is required");
        }

        // Get current timestamp for payment records
        $currentDateTime = new DateTime();
        $currentDate = $currentDateTime->format('Y-m-d');
        $currentTime = $currentDateTime->format('H:i:s');

        // Validate file upload
        $file = $_FILES['payment_path'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid file type. Allowed types: JPG, PNG, PDF");
        }

        // Validate file size (2MB limit)
        $maxSize = 2 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception("File size exceeds 2MB limit");
        }

        // Handle file upload
        require_once 'upload_handler.php';
        $uploadHandler = new UploadHandler();
        $customName = $uploadHandler->generateCustomFilename($_POST['nik'], 'payment', null);
        $uploadResult = $uploadHandler->handleUpload($_FILES['payment_path'], 'payments', $customName);
        
        if (!$uploadResult) {
            throw new Exception('Failed to upload payment proof: ' . implode(', ', $uploadHandler->getErrors()));
        }

        // Update jamaah record with payment details
        $stmt = $conn->prepare("UPDATE data_jamaah SET 
            transfer_account_name = :transfer_account_name,
            payment_time = :payment_time,
            payment_date = :payment_date,
            payment_status = 'pending',
            payment_path = :payment_path
            WHERE nik = :nik");

        $stmt->execute([
            'transfer_account_name' => $_POST['transfer_account_name'],
            'payment_time' => $currentTime,
            'payment_date' => $currentDate,
            'payment_path' => $uploadResult['path'],
            'nik' => $_POST['nik']
        ]);

        // Fetch package details for email
        $stmt = $conn->prepare("SELECT j.*, p.program_pilihan, p.tanggal_keberangkatan,
            CASE j.type_room_pilihan
                WHEN 'Quad' THEN p.base_price_quad
                WHEN 'Triple' THEN p.base_price_triple
                WHEN 'Double' THEN p.base_price_double
            END as biaya_paket,
            p.currency
            FROM data_jamaah j
            JOIN data_paket p ON j.pak_id = p.pak_id
            WHERE j.nik = :nik");
        $stmt->execute(['nik' => $_POST['nik']]);
        $jamaahData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Prepare email data with complete details
        $paymentData = [
            'nama' => $_POST['nama'],
            'nik' => $_POST['nik'],
            'no_telp' => $jamaahData['no_telp'],
            'email' => $jamaahData['email'],
            'program_pilihan' => $jamaahData['program_pilihan'],
            'tanggal_keberangkatan' => $jamaahData['tanggal_keberangkatan'],
            'biaya_paket' => $jamaahData['biaya_paket'],
            'type_room_pilihan' => $_POST['type_room_pilihan'],
            'transfer_account_name' => $_POST['transfer_account_name'],
            'payment_time' => $currentTime,
            'payment_date' => $currentDate,
            'payment_type' => $_POST['payment_type'] ?? '',
            'payment_method' => $_POST['payment_method'] ?? '',
            'currency' => $jamaahData['currency']
        ];

        // Determine registration type from program_pilihan
        $registrationType = (stripos($_POST['program_pilihan'], 'haji') !== false) ? 'Haji' : 'Umroh';

        // Send email notification without attachments
        $emailResult = sendPaymentConfirmationEmail($paymentData, [], $registrationType);

        // Log email result but don't stop transaction
        if (!$emailResult['success']) {
            error_log("Payment confirmation email issue: " . $emailResult['message']);
        }

        // Commit transaction
        $conn->commit();

        // Set success session data for closing_page.php
        $_SESSION['payment_success'] = [
            'status' => true,
            'timestamp' => time(),
            'message' => 'Payment confirmation submitted successfully',
            'email_status' => $emailResult['success'] ? $emailResult['message'] : null
        ];

        // Redirect to closing page
        header('Location: closing_page.php');
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Payment confirmation error: " . $e->getMessage());
        
        // Store error in session and redirect back to invoice
        $_SESSION['payment_error'] = $e->getMessage();
        header('Location: invoice.php');
        exit();
    }
}

// If we get here without POST data, redirect to homepage
header('Location: https://hajikhusus.web.id');
exit();
