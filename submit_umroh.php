<?php
// submit_umroh.php

require_once 'config.php';
require_once 'email_functions.php';

// Initialize response variables
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $requiredFields = [
            'nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 
            'alamat', 'no_telp', 'email', 'nama_ayah', 'nama_ibu',
            'pak_id', 'type_room_pilihan'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Field $field harus diisi.";
            }
        }
        
        // Validate NIK (16 digits and uniqueness)
        if (!preg_match('/^\d{16}$/', $_POST['nik'])) {
            $errors[] = "NIK harus 16 digit angka.";
        } else {
            // Check if NIK already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM data_jamaah WHERE nik = ?");
            $stmt->execute([$_POST['nik']]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Mohon maaf, pendaftaran tidak dapat dilanjutkan.";
            }
        }
        
        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid.";
        }
        
        // Upload and validate files
        require_once 'upload_handler.php';
        $uploadHandler = new UploadHandler();
        $uploadedFiles = [];
        $requiredFiles = ['kk_path', 'ktp_path'];
        
        foreach ($requiredFiles as $fileField) {
            if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "File $fileField harus diupload.";
                continue;
            }
            
            $documentType = str_replace('_path', '', $fileField);
            $customName = $uploadHandler->generateCustomFilename($_POST['nik'], $documentType, $_POST['pak_id']);
            $uploadResult = $uploadHandler->handleUpload($_FILES[$fileField], 'documents', $customName);
            
            if (!$uploadResult) {
                $errors = array_merge($errors, $uploadHandler->getErrors());
                continue;
            }
            
            $uploadedFiles[$fileField] = $uploadResult['path'];
        }
        
        // Process optional paspor file
        if (isset($_FILES['paspor_path']) && $_FILES['paspor_path']['error'] === UPLOAD_ERR_OK) {
            $customName = $uploadHandler->generateCustomFilename($_POST['nik'], 'paspor', $_POST['pak_id']);
            $uploadResult = $uploadHandler->handleUpload($_FILES['paspor_path'], 'documents', $customName);
            
            if ($uploadResult) {
                $uploadedFiles['paspor_path'] = $uploadResult['path'];
            }
        }
        
        // If no errors, proceed with database insertion (without saving files)
        if (empty($errors)) {
            $conn->beginTransaction();
            
            // Prepare data for database
            $currentDateTime = date('Y-m-d H:i:s');
            $data = [
                'nik' => $_POST['nik'],
                'nama' => $_POST['nama'],
                'tempat_lahir' => $_POST['tempat_lahir'],
                'tanggal_lahir' => $_POST['tanggal_lahir'],
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'alamat' => $_POST['alamat'],
                'kode_pos' => $_POST['kode_pos'] ?? null,
                'email' => $_POST['email'],
                'no_telp' => $_POST['no_telp'],
                'tinggi_badan' => $_POST['tinggi_badan'] ?? null,
                'berat_badan' => $_POST['berat_badan'] ?? null,
                'nama_ayah' => $_POST['nama_ayah'],
                'nama_ibu' => $_POST['nama_ibu'],
                'emergency_nama' => $_POST['emergency_nama'] ?? null,
                'emergency_hp' => $_POST['emergency_hp'] ?? null,
                'nama_paspor' => $_POST['nama_paspor'] ?? null,
                'no_paspor' => $_POST['no_paspor'] ?? null,
                'tempat_pembuatan_paspor' => $_POST['tempat_pembuatan_paspor'] ?? null,
                'tanggal_pengeluaran_paspor' => $_POST['tanggal_pengeluaran_paspor'] ?? null,
                'tanggal_habis_berlaku' => $_POST['tanggal_habis_berlaku'] ?? null,
                'jenis_vaksin_1' => $_POST['jenis_vaksin_1'] ?? null,
                'jenis_vaksin_2' => $_POST['jenis_vaksin_2'] ?? null,
                'jenis_vaksin_3' => $_POST['jenis_vaksin_3'] ?? null,
                'tanggal_vaksin_1' => $_POST['tanggal_vaksin_1'] ?? null,
                'tanggal_vaksin_2' => $_POST['tanggal_vaksin_2'] ?? null,
                'tanggal_vaksin_3' => $_POST['tanggal_vaksin_3'] ?? null,
                'marketing_nama' => $_POST['marketing_nama'] ?? null,
                'marketing_hp' => $_POST['marketing_hp'] ?? null,
                'marketing_type' => $_POST['marketing_type'] ?? null,
                'request_khusus' => $_POST['request_khusus'] ?? null,
                'pak_id' => $_POST['pak_id'],
                'type_room_pilihan' => $_POST['type_room_pilihan'],
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
                // Set upload timestamps for files (even though we're not saving them to server)
                'kk_path' => isset($_FILES['kk_path']) ? $currentDateTime : null,
                'ktp_path' => isset($_FILES['ktp_path']) ? $currentDateTime : null,
                'paspor_path' => isset($uploadedFiles['paspor_path']) ? $currentDateTime : null,
                'kk_path' => $uploadedFiles['kk_path'] ?? null,
                'ktp_path' => $uploadedFiles['ktp_path'] ?? null,
                'paspor_path' => $uploadedFiles['paspor_path'] ?? null,
                'payment_type' => $_POST['payment_type'] ?? null,
                'payment_method' => $_POST['payment_method'] ?? null,
                // Note: payment_path is not set here as it's for payment verification
            ];
            
            // Prepare SQL query (exclude file path fields)
            $columns = implode(', ', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO data_jamaah ($columns) VALUES ($placeholders)";
            $stmt = $conn->prepare($sql);
            
            // Bind parameters
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            // Execute query
            $stmt->execute();
            
            // Prepare email data
            $emailData = [
                'nik' => $_POST['nik'],
                'nama' => $_POST['nama'],
                'tanggal_lahir' => $_POST['tanggal_lahir'],
                'jenis_kelamin' => $_POST['jenis_kelamin'],
                'alamat' => $_POST['alamat'],
                'no_telp' => $_POST['no_telp'],
                'email' => $_POST['email'],
                'program_pilihan' => $_POST['program_pilihan'] ?? '',
                'type_room_pilihan' => $_POST['type_room_pilihan'] ?? '',
                'harga_paket' => $_POST['harga_paket'] ?? 0,
                'currency' => $_POST['currency'] ?? 'IDR'
            ];

            // All file uploads and database operations successful
            $conn->commit();
            $success = true;
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    } catch (Exception $e) {
        $conn->rollBack();
        $errors[] = $e->getMessage();
        error_log("Error: " . $e->getMessage());
    }
}

if ($success) {
    // Get package details for pricing
    $paketId = $_POST['pak_id'];
    $stmt = $conn->prepare("SELECT program_pilihan, tanggal_keberangkatan, 
                           base_price_quad, base_price_triple, base_price_double, currency 
                           FROM data_paket WHERE pak_id = ?");
    $stmt->execute([$paketId]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    // Calculate total based on room type
    $roomType = $_POST['type_room_pilihan'];
    $paymentTotal = 0;
    switch($roomType) {
        case 'Quad': $paymentTotal = $package['base_price_quad']; break;
        case 'Triple': $paymentTotal = $package['base_price_triple']; break;
        case 'Double': $paymentTotal = $package['base_price_double']; break;
    }

    // Prepare invoice parameters
    $invoiceParams = [
        'nama' => $_POST['nama'],
        'no_telp' => $_POST['no_telp'],
        'alamat' => $_POST['alamat'],
        'email' => $_POST['email'] ?? '',
        'nik' => $_POST['nik'],
        'program_pilihan' => $package['program_pilihan'],
        'tanggal_keberangkatan' => $package['tanggal_keberangkatan'],
        'type_room_pilihan' => $_POST['type_room_pilihan'],
        'payment_method' => $_POST['payment_method'],
        'payment_type' => $_POST['payment_type'],
        'currency' => $package['currency'],
        'payment_total' => $paymentTotal,
        'request_khusus' => $_POST['request_khusus'] ?? '',
        'pak_id' => $_POST['pak_id']
    ];

    $queryString = http_build_query($invoiceParams);
    header("Location: invoice.php?" . $queryString);
} else {
    $errorMessages = implode("\\n", $errors);
    header("Location: invoice.php?errors=" . urlencode($errorMessages));
}
exit();