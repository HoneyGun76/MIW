<?php
require_once 'config.php';

// Helper function to calculate document completion status
function calculateCompletionStatus($jamaah) {
    $requiredDocs = [
        'bk_kuning_path', 'foto_path', 'fc_ktp_path', 'fc_ijazah_path', 
        'fc_kk_path', 'fc_bk_nikah_path', 'fc_akta_lahir_path'
    ];
    
    $completedCount = 0;
    foreach ($requiredDocs as $doc) {
        if (!empty($jamaah[$doc])) {
            $completedCount++;
        }
    }
    
    $percentage = round(($completedCount / count($requiredDocs)) * 100);
    
    if ($percentage == 100) return 'Complete';
    if ($percentage >= 80) return 'Almost Complete';
    if ($percentage >= 50) return 'In Progress';
    return 'Incomplete';
}

// Set proper headers for JSON response
header('Content-Type: application/json');

if (!isset($_POST['pak_id']) || !isset($_POST['export_type'])) {http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing parameters: pak_id or export_type',
        'error' => true
    ]);
    exit;
}

$pakId = trim($_POST['pak_id']);
$exportType = trim($_POST['export_type']);

if (empty($pakId) || empty($exportType)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid parameters: pak_id or export_type cannot be empty',
        'error' => true
    ]);
    exit;
}

try {
    // Validate connection
    if (!$conn instanceof PDO) {
        throw new Exception("Database connection error");
    }

    // Get package details
    $stmt = $conn->prepare("SELECT * FROM data_paket WHERE pak_id = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare package query");
    }
    
    $stmt->execute([$pakId]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$package) {
        throw new Exception("Package not found: " . $pakId);
    }
    
    // Get jamaah data directly from data_jamaah table
    $jamaahs = [];
    $stmt = $conn->prepare("
        SELECT j.*
        FROM data_jamaah j
        WHERE j.pak_id = ?
        ORDER BY j.nama
    ");
    $stmt->execute([$pakId]);
    $jamaahs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $manifestData = [];
    $roomListsData = [
        'medinah' => [],
        'makkah' => []
    ];
    $kelengkapanData = [];
    
    if ($exportType === 'manifest') {
        // Prepare manifest export data
        $counter = 1;
        foreach ($jamaahs as $jamaah) {
            // Calculate age if needed
            $age = $jamaah['umur'] ?? '';
            if (empty($age) && !empty($jamaah['tanggal_lahir'])) {
                $age = date_diff(date_create($jamaah['tanggal_lahir']), date_create('today'))->y;
            }
            
            // Determine marketing name
            $marketingName = $jamaah['marketing_nama'] ?? '';
            if ($marketingName === $jamaah['nama']) {
                $marketingName = 'Eli Rahmalia';
            }
            
            // Strictly format data according to manifest template
            $manifestData[] = [
                'No' => $counter++,
                'Sex' => $jamaah['jenis_kelamin'] === 'Laki-laki' ? 'MR' : 'MRS',
                'Name of Passport' => strtoupper($jamaah['nama_paspor'] ?? $jamaah['nama']),
                'Marketing' => strtoupper($marketingName),
                'Nama Ayah' => strtoupper($jamaah['nama_ayah'] ?? ''),
                'Birth: Date' => $jamaah['tanggal_lahir'] ? date('d/m/Y', strtotime($jamaah['tanggal_lahir'])) : '',
                'Birth: City' => strtoupper($jamaah['tempat_lahir'] ?? ''),
                'Passport: No.Passport' => strtoupper($jamaah['no_paspor'] ?? ''),
                'Passport: Issuing Office' => strtoupper($jamaah['tempat_pembuatan_paspor'] ?? ''),
                'Passport: Date of Issue' => $jamaah['tanggal_pengeluaran_paspor'] ? date('d/m/Y', strtotime($jamaah['tanggal_pengeluaran_paspor'])) : '',
                'Passport: Date of Expiry' => $jamaah['tanggal_habis_berlaku'] ? date('d/m/Y', strtotime($jamaah['tanggal_habis_berlaku'])) : '',
                'Relation' => strtoupper($jamaah['room_relation'] ?? $jamaah['hubungan_mahram'] ?? ''),
                'Age' => $age,
                'Cabang' => 'Bandung',
                'Roomlist' => $jamaah['type_room_pilihan'] ?? '',
                'NIK' => $jamaah['nik'],
                'Alamat' => strtoupper($jamaah['alamat'] ?? ''),
                'Keterangan' => $jamaah['request_khusus'] ?? ''
            ];
        }
    }
    
    // Always prepare kelengkapan data for all jamaah
    $counter = 1;
    foreach ($jamaahs as $jamaah) {
        // Check document completion status
        $kelengkapanData[] = [
            'No' => $counter++,
            'NIK' => $jamaah['nik'],
            'Name' => $jamaah['nama'],
            'Sex' => $jamaah['jenis_kelamin'],
            'Buku Kuning' => !empty($jamaah['bk_kuning_path']) ? '✓' : '✗',
            'Foto' => !empty($jamaah['foto_path']) ? '✓' : '✗',
            'FC KTP' => !empty($jamaah['fc_ktp_path']) ? '✓' : '✗',
            'FC Ijazah' => !empty($jamaah['fc_ijazah_path']) ? '✓' : '✗',
            'FC KK' => !empty($jamaah['fc_kk_path']) ? '✓' : '✗',
            'FC Buku Nikah' => !empty($jamaah['fc_bk_nikah_path']) ? '✓' : '✗',
            'FC Akta Lahir' => !empty($jamaah['fc_akta_lahir_path']) ? '✓' : '✗',
            'Upload Date BK' => !empty($jamaah['bk_kuning_path']) ? date('d/m/Y', strtotime($jamaah['bk_kuning_path'])) : '',
            'Upload Date Foto' => !empty($jamaah['foto_path']) ? date('d/m/Y', strtotime($jamaah['foto_path'])) : '',
            'Completion Status' => calculateCompletionStatus($jamaah)
        ];
    }
    
    // Always prepare room lists data for both manifest and roomlist exports
    foreach ($jamaahs as $jamaah) {
        $roomPrefix = $jamaah['room_prefix'] ?? '';
        if (!empty($roomPrefix)) {
            $roomType = substr($roomPrefix, 0, 1); // Q, T, or D
            $roomTypeLabel = $roomType === 'Q' ? 'Quad' : ($roomType === 'T' ? 'Triple' : 'Double');
            $roomNumber = $jamaah['medinah_room_number'] ?? '';
            $makkahNumber = $jamaah['mekkah_room_number'] ?? '';
            
            // Add to Medinah rooms
            if (!empty($roomNumber)) {
                if (!isset($roomListsData['medinah'][$roomNumber])) {
                    $roomListsData['medinah'][$roomNumber] = [
                        'Room Number' => $roomNumber,
                        'Type' => $roomTypeLabel,
                        'Occupants' => []
                    ];
                }
                $roomListsData['medinah'][$roomNumber]['Occupants'][] = [
                    'name' => $jamaah['nama'],
                    'nik' => $jamaah['nik'],
                    'relation' => $jamaah['room_relation'] ?? '',
                    'age' => $jamaah['umur'] ?? '',
                    'sex' => $jamaah['jenis_kelamin'] === 'Laki-laki' ? 'MR' : 'MRS'
                ];
            }
            
            // Add to Makkah rooms
            if (!empty($makkahNumber)) {
                if (!isset($roomListsData['makkah'][$makkahNumber])) {
                    $roomListsData['makkah'][$makkahNumber] = [
                        'Room Number' => $makkahNumber,
                        'Type' => $roomTypeLabel,
                        'Occupants' => []
                    ];
                }
                $roomListsData['makkah'][$makkahNumber]['Occupants'][] = [
                    'name' => $jamaah['nama'],
                    'nik' => $jamaah['nik'],
                    'relation' => $jamaah['room_relation'] ?? '',
                    'age' => $jamaah['umur'] ?? '',
                    'sex' => $jamaah['jenis_kelamin'] === 'Laki-laki' ? 'MR' : 'MRS'
                ];
            }
        }
    }
    
    // Convert room lists to flat arrays for export
    $medinahRoomList = [];
    $makkahRoomList = [];

    foreach ($roomListsData['medinah'] as $room) {
        // Create detailed room entries for roomlist export
        foreach ($room['Occupants'] as $index => $occupant) {
            $medinahRoomList[] = [
                'Room Number' => $room['Room Number'],
                'Type' => $room['Type'],
                'Guest' => ($index + 1),
                'Name' => $occupant['name'],
                'NIK' => $occupant['nik'],
                'Sex' => $occupant['sex'],
                'Age' => $occupant['age'],
                'Relation' => $occupant['relation']
            ];
        }
    }

    foreach ($roomListsData['makkah'] as $room) {
        // Create detailed room entries for roomlist export
        foreach ($room['Occupants'] as $index => $occupant) {
            $makkahRoomList[] = [
                'Room Number' => $room['Room Number'],
                'Type' => $room['Type'],
                'Guest' => ($index + 1),
                'Name' => $occupant['name'],
                'NIK' => $occupant['nik'],
                'Sex' => $occupant['sex'],
                'Age' => $occupant['age'],
                'Relation' => $occupant['relation']
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => [
            'manifest' => $manifestData,
            'roomLists' => [
                'medinah' => $medinahRoomList,
                'makkah' => $makkahRoomList
            ],
            'kelengkapan' => $kelengkapanData,
            'package' => [
                'name' => $package['program_pilihan'],
                'type' => $package['jenis_paket'],
                'departure_date' => date('d/m/Y', strtotime($package['tanggal_keberangkatan']))
                // Hotel information removed to provide more space for manifest table
            ]
        ],
        'type' => $exportType
    ]);
    
} catch (Exception $e) {
    error_log("Export manifest error: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}