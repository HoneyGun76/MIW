<?php
require_once 'config.php';
require_once 'email_functions.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['nik'])) {
        throw new Exception('NIK is required');
    }

    // Get jamaah data
    $stmt = $conn->prepare("SELECT * FROM data_jamaah WHERE nik = ?");
    $stmt->execute([$_POST['nik']]);
    $jamaah = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jamaah) {
        throw new Exception('Jamaah not found');
    }

    // Check if all required files are present
    $requiredFiles = [
        'bk_kuning',
        'foto',
        'fc_ktp',
        'fc_ijazah',
        'fc_kk',
        'fc_bk_nikah',
        'fc_akta_lahir'
    ];

    $files = [];
    foreach ($requiredFiles as $file) {
        if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('All documents must be uploaded');
        }
        $files[$file] = $_FILES[$file];
    }

    require_once 'upload_handler.php';
    $uploader = new UploadHandler('documents');
    $uploadedPaths = [];
    
    foreach ($files as $fileField => $file) {
        $result = $uploader->handleUpload(
            $file,
            $jamaah['nik'] . '_' . $fileField . '_'
        );
        
        if (!$result['success']) {
            throw new Exception("Failed to upload $fileField: " . $result['message']);
        }
        $uploadedPaths[$fileField] = $result['path'];
    }

    // Update document timestamps
    $currentTime = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("
        UPDATE data_jamaah SET
            bk_kuning = ?,
            foto = ?,
            fc_ktp_path = ?,
            fc_ijazah_path = ?,
            fc_kk_path = ?,
            fc_bk_nikah_path = ?,
            fc_akta_lahir_path = ?
        WHERE nik = ?
    ");

    $stmt->execute([
        $currentTime,
        $currentTime,
        $currentTime,
        $currentTime,
        $currentTime,
        $currentTime,
        $currentTime,
        $_POST['nik']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Documents uploaded successfully',
        'timestamps' => [
            'bk_kuning' => $currentTime,
            'foto' => $currentTime,
            'fc_ktp_path' => $currentTime,
            'fc_ijazah_path' => $currentTime,
            'fc_kk_path' => $currentTime,
            'fc_bk_nikah_path' => $currentTime,
            'fc_akta_lahir_path' => $currentTime
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
