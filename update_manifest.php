<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Debug log
    error_log("POST data received: " . print_r($_POST, true));
    
    // Validate all required fields upfront
    $required_fields = ['nik', 'pak_id', 'room_prefix', 'medinah_number', 'mekkah_number'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
    }
    
    // Begin transaction
    $conn->beginTransaction();
    
    // Check if jamaah exists
    $stmt = $conn->prepare("SELECT * FROM data_jamaah WHERE nik = ?");
    $stmt->execute([$_POST['nik']]);
    $jamaah = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$jamaah) {
        throw new Exception("Jamaah with NIK {$_POST['nik']} not found");
    }
    
    // Prepare data with trimmed values
    $data = [
        'room_prefix' => trim($_POST['room_prefix']),
        'medinah_room_number' => trim($_POST['medinah_number']),
        'mekkah_room_number' => trim($_POST['mekkah_number']),
        'nik' => trim($_POST['nik'])
    ];

    // Only include relation/hubungan_mahram if it's provided and not empty
    if (isset($_POST['relation']) && trim($_POST['relation']) !== '') {
        $data['room_relation'] = trim($_POST['relation']);
    }
    
    // Double-check that required values are not empty
    foreach ($data as $key => $value) {
        // Skip validation for optional fields and the NIK which is used in WHERE clause
        if ($key === 'room_relation' || $key === 'nik') continue;
        
        if ($value === '') {
            throw new Exception("Field '$key' cannot be empty");
        }
    }
    
    // Update jamaah record with room information
    $sql = "UPDATE data_jamaah SET 
            room_prefix = :room_prefix,
            medinah_room_number = :medinah_room_number,
            mekkah_room_number = :mekkah_room_number";
    
    if (isset($data['room_relation'])) {
        $sql .= ", room_relation = :room_relation";
    }
    
    $sql .= ", updated_at = NOW() WHERE nik = :nik";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
    
    // Commit transaction
    $conn->commit();
    
    $response = [
        'success' => true, 
        'message' => 'Roomlist updated successfully'
    ];

} catch (Exception $e) {
    // Only rollback if we're in a transaction
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $response = [
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ];
}

echo json_encode($response);