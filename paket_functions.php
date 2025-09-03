<?php
require_once "config.php";

function handlePackageOperations($conn) {
    // Add new package
    if (isset($_POST['add'])) {
        $jenis_paket = $_POST['jenis_paket'];
        $currency = $_POST['currency'];
        $program_pilihan = $_POST['program_pilihan'];
        $tanggal_keberangkatan = $_POST['tanggal_keberangkatan'];
        $base_price_quad = $_POST['base_price_quad'];
        $base_price_triple = $_POST['base_price_triple'];
        $base_price_double = $_POST['base_price_double'];
        $hotel_medinah = $_POST['hotel_medinah'] ?? '';
        $hotel_makkah = $_POST['hotel_makkah'] ?? '';

        // Handle flyer image upload
        $flyer_image = null;
        if (isset($_FILES['flyer_image']) && $_FILES['flyer_image']['error'] === 0) {
            $flyer_image = handleFlyerUpload($_FILES['flyer_image']);
        }

        // Process HCN data
        $hcnData = [
            'medinah' => $_POST['hcn_medinah'] ?? '',
            'makkah' => $_POST['hcn_makkah'] ?? '',
            'additional' => [],
            'issued_date' => $_POST['hcn_issued_date'] ?? '',
            'expiry_date' => $_POST['hcn_expiry_date'] ?? ''
        ];

        // Process additional HCN codes if they exist
        if (isset($_POST['additional_hotels'])) {
            foreach ($_POST['additional_hotels'] as $index => $hotel) {
                if (!empty($hotel['hcn'])) {
                    $hcnData['additional'][] = $hotel['hcn'];
                }
            }
        }

        // Process room numbers
        $medinahRooms = [
            'quad' => !empty($_POST['medinah_quad_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_quad_rooms'])) : [],
            'triple' => !empty($_POST['medinah_triple_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_triple_rooms'])) : [],
            'double' => !empty($_POST['medinah_double_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_double_rooms'])) : []
        ];

        $makkahRooms = [
            'quad' => !empty($_POST['makkah_quad_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_quad_rooms'])) : [],
            'triple' => !empty($_POST['makkah_triple_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_triple_rooms'])) : [],
            'double' => !empty($_POST['makkah_double_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_double_rooms'])) : []
        ];

        // Process additional hotels
        $additionalHotels = [];
        $additionalHotelsRooms = [];
        if (isset($_POST['additional_hotels'])) {
            foreach ($_POST['additional_hotels'] as $hotel) {
                if (!empty($hotel['name'])) {
                    $additionalHotels[] = $hotel['name'];
                    $additionalHotelsRooms[] = [
                        'quad' => !empty($hotel['quad_rooms']) ? explode(',', str_replace(' ', '', $hotel['quad_rooms'])) : [],
                        'triple' => !empty($hotel['triple_rooms']) ? explode(',', str_replace(' ', '', $hotel['triple_rooms'])) : [],
                        'double' => !empty($hotel['double_rooms']) ? explode(',', str_replace(' ', '', $hotel['double_rooms'])) : []
                    ];
                }
            }
        }

        // Calculate available rooms and generate room prefixes
        $quadAvailable = min(
            count($medinahRooms['quad']),
            count($makkahRooms['quad']),
            ...array_map(fn($hotel) => count($hotel['quad']), $additionalHotelsRooms)
        );
        $tripleAvailable = min(
            count($medinahRooms['triple']),
            count($makkahRooms['triple']),
            ...array_map(fn($hotel) => count($hotel['triple']), $additionalHotelsRooms)
        );
        $doubleAvailable = min(
            count($medinahRooms['double']),
            count($makkahRooms['double']),
            ...array_map(fn($hotel) => count($hotel['double']), $additionalHotelsRooms)
        );

        $roomPrefixes = [];
        for ($i = 1; $i <= $quadAvailable; $i++) {
            $roomPrefixes[] = 'Q' . $i;
        }
        for ($i = 1; $i <= $tripleAvailable; $i++) {
            $roomPrefixes[] = 'T' . $i;
        }
        for ($i = 1; $i <= $doubleAvailable; $i++) {
            $roomPrefixes[] = 'D' . $i;
        }

        // Try to insert with flyer column first, fallback if column doesn't exist
        try {
            $stmt = $conn->prepare("INSERT INTO data_paket (
                jenis_paket, currency, program_pilihan, tanggal_keberangkatan,
                base_price_quad, base_price_triple, base_price_double,
                hotel_medinah, hotel_makkah, additional_hotels,
                hotel_medinah_rooms, hotel_makkah_rooms, additional_hotels_rooms,
                room_numbers, hcn, flyer_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $jenis_paket,
                $currency,
                $program_pilihan,
                $tanggal_keberangkatan,
                $base_price_quad,
                $base_price_triple,
                $base_price_double,
                $hotel_medinah,
                $hotel_makkah,
                json_encode($additionalHotels),
                json_encode($medinahRooms),
                json_encode($makkahRooms),
                json_encode($additionalHotelsRooms),
                implode(',', $roomPrefixes),
                json_encode($hcnData),
                $flyer_image
            ]);
        } catch (PDOException $e) {
            // Fallback: Insert without flyer column if it doesn't exist
            if (strpos($e->getMessage(), 'flyer_image') !== false) {
                $stmt = $conn->prepare("INSERT INTO data_paket (
                    jenis_paket, currency, program_pilihan, tanggal_keberangkatan,
                    base_price_quad, base_price_triple, base_price_double,
                    hotel_medinah, hotel_makkah, additional_hotels,
                    hotel_medinah_rooms, hotel_makkah_rooms, additional_hotels_rooms,
                    room_numbers, hcn
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->execute([
                    $jenis_paket,
                    $currency,
                    $program_pilihan,
                    $tanggal_keberangkatan,
                    $base_price_quad,
                    $base_price_triple,
                    $base_price_double,
                    $hotel_medinah,
                    $hotel_makkah,
                    json_encode($additionalHotels),
                    json_encode($medinahRooms),
                    json_encode($makkahRooms),
                    json_encode($additionalHotelsRooms),
                    implode(',', $roomPrefixes),
                    json_encode($hcnData)
                ]);
            } else {
                throw $e; // Re-throw if it's a different error
            }
        }

        $_SESSION['message'] = "Package added successfully!";
        header("Location: admin_paket.php");
        exit();
    }

    // Update package
    if (isset($_POST['update'])) {
        $pak_id = $_POST['id'];
        $jenis_paket = $_POST['jenis_paket'];
        $currency = $_POST['currency'];
        $program_pilihan = $_POST['program_pilihan'];
        $tanggal_keberangkatan = $_POST['tanggal_keberangkatan'];
        $base_price_quad = $_POST['base_price_quad'];
        $base_price_triple = $_POST['base_price_triple'];
        $base_price_double = $_POST['base_price_double'];
        $hotel_medinah = $_POST['hotel_medinah'] ?? '';
        $hotel_makkah = $_POST['hotel_makkah'] ?? '';

        // Handle flyer image upload for update
        $flyer_image = $_POST['current_flyer'] ?? null; // Keep existing flyer by default
        if (isset($_FILES['flyer_image']) && $_FILES['flyer_image']['error'] === 0) {
            // Delete old flyer if exists
            if ($flyer_image && file_exists($flyer_image)) {
                unlink($flyer_image);
            }
            $flyer_image = handleFlyerUpload($_FILES['flyer_image']);
        }

        // Process HCN data
        $hcnData = [
            'medinah' => $_POST['hcn_medinah'] ?? '',
            'makkah' => $_POST['hcn_makkah'] ?? '',
            'additional' => [],
            'issued_date' => $_POST['hcn_issued_date'] ?? '',
            'expiry_date' => $_POST['hcn_expiry_date'] ?? ''
        ];

        // Process additional HCN codes if they exist
        if (isset($_POST['additional_hotels'])) {
            foreach ($_POST['additional_hotels'] as $index => $hotel) {
                if (!empty($hotel['hcn'])) {
                    $hcnData['additional'][] = $hotel['hcn'];
                }
            }
        }

        // Process room numbers
        $medinahRooms = [
            'quad' => !empty($_POST['medinah_quad_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_quad_rooms'])) : [],
            'triple' => !empty($_POST['medinah_triple_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_triple_rooms'])) : [],
            'double' => !empty($_POST['medinah_double_rooms']) ? explode(',', str_replace(' ', '', $_POST['medinah_double_rooms'])) : []
        ];

        $makkahRooms = [
            'quad' => !empty($_POST['makkah_quad_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_quad_rooms'])) : [],
            'triple' => !empty($_POST['makkah_triple_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_triple_rooms'])) : [],
            'double' => !empty($_POST['makkah_double_rooms']) ? explode(',', str_replace(' ', '', $_POST['makkah_double_rooms'])) : []
        ];

        // Process additional hotels
        $additionalHotels = [];
        $additionalHotelsRooms = [];
        if (isset($_POST['additional_hotels'])) {
            foreach ($_POST['additional_hotels'] as $hotel) {
                if (!empty($hotel['name'])) {
                    $additionalHotels[] = $hotel['name'];
                    $additionalHotelsRooms[] = [
                        'quad' => !empty($hotel['quad_rooms']) ? explode(',', str_replace(' ', '', $hotel['quad_rooms'])) : [],
                        'triple' => !empty($hotel['triple_rooms']) ? explode(',', str_replace(' ', '', $hotel['triple_rooms'])) : [],
                        'double' => !empty($hotel['double_rooms']) ? explode(',', str_replace(' ', '', $hotel['double_rooms'])) : []
                    ];
                }
            }
        }

        // Calculate available rooms and generate room prefixes
        $quadAvailable = min(
            count($medinahRooms['quad']),
            count($makkahRooms['quad']),
            ...array_map(fn($hotel) => count($hotel['quad']), $additionalHotelsRooms)
        );
        $tripleAvailable = min(
            count($medinahRooms['triple']),
            count($makkahRooms['triple']),
            ...array_map(fn($hotel) => count($hotel['triple']), $additionalHotelsRooms)
        );
        $doubleAvailable = min(
            count($medinahRooms['double']),
            count($makkahRooms['double']),
            ...array_map(fn($hotel) => count($hotel['double']), $additionalHotelsRooms)
        );

        $roomPrefixes = [];
        for ($i = 1; $i <= $quadAvailable; $i++) {
            $roomPrefixes[] = 'Q' . $i;
        }
        for ($i = 1; $i <= $tripleAvailable; $i++) {
            $roomPrefixes[] = 'T' . $i;
        }
        for ($i = 1; $i <= $doubleAvailable; $i++) {
            $roomPrefixes[] = 'D' . $i;
        }

        // Try to update with flyer column first, fallback if column doesn't exist
        try {
            $stmt = $conn->prepare("UPDATE data_paket SET 
                jenis_paket = ?, currency = ?, program_pilihan = ?, tanggal_keberangkatan = ?,
                base_price_quad = ?, base_price_triple = ?, base_price_double = ?,
                hotel_medinah = ?, hotel_makkah = ?, additional_hotels = ?,
                hotel_medinah_rooms = ?, hotel_makkah_rooms = ?, additional_hotels_rooms = ?,
                room_numbers = ?, hcn = ?, flyer_image = ?
                WHERE pak_id = ?");

            $stmt->execute([
                $jenis_paket,
                $currency,
                $program_pilihan,
                $tanggal_keberangkatan,
                $base_price_quad,
                $base_price_triple,
                $base_price_double,
                $hotel_medinah,
                $hotel_makkah,
                json_encode($additionalHotels),
                json_encode($medinahRooms),
                json_encode($makkahRooms),
                json_encode($additionalHotelsRooms),
                implode(',', $roomPrefixes),
                json_encode($hcnData),
                $flyer_image,
                $pak_id
            ]);
        } catch (PDOException $e) {
            // Fallback: Update without flyer column if it doesn't exist
            if (strpos($e->getMessage(), 'flyer_image') !== false) {
                $stmt = $conn->prepare("UPDATE data_paket SET 
                    jenis_paket = ?, currency = ?, program_pilihan = ?, tanggal_keberangkatan = ?,
                    base_price_quad = ?, base_price_triple = ?, base_price_double = ?,
                    hotel_medinah = ?, hotel_makkah = ?, additional_hotels = ?,
                    hotel_medinah_rooms = ?, hotel_makkah_rooms = ?, additional_hotels_rooms = ?,
                    room_numbers = ?, hcn = ?
                    WHERE pak_id = ?");

                $stmt->execute([
                    $jenis_paket,
                    $currency,
                    $program_pilihan,
                    $tanggal_keberangkatan,
                    $base_price_quad,
                    $base_price_triple,
                    $base_price_double,
                    $hotel_medinah,
                    $hotel_makkah,
                    json_encode($additionalHotels),
                    json_encode($medinahRooms),
                    json_encode($makkahRooms),
                    json_encode($additionalHotelsRooms),
                    implode(',', $roomPrefixes),
                    json_encode($hcnData),
                    $pak_id
                ]);
            } else {
                throw $e; // Re-throw if it's a different error
            }
        }

        $_SESSION['message'] = "Package updated successfully!";
        header("Location: admin_paket.php");
        exit();
    }

    // Delete package
    if (isset($_POST['delete'])) {
        $pak_id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM data_paket WHERE pak_id = ?");
        $stmt->execute([$pak_id]);

        $_SESSION['message'] = "Package deleted successfully!";
        header("Location: admin_paket.php");
        exit();
    }
}

function getAllPackages($conn) {
    $stmt = $conn->query("SELECT * FROM data_paket ORDER BY tanggal_keberangkatan DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPackageById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM data_paket WHERE pak_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function handleFlyerUpload($file) {
    $targetDir = "uploads/flyers/";
    
    // Create directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed.');
    }
    
    // Validate file size (2MB max)
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum 2MB allowed.');
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'flyer_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $targetPath = $targetDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    } else {
        throw new Exception('Failed to upload file.');
    }
}