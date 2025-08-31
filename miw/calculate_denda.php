<?php
/**
 * Calculate pembatalan denda (penalty) based on package type and time until departure
 * Based on the rules defined in form_pembatalan.php
 */

require_once 'config.php';

function calculateDenda($jamaahData, $paketData) {
    $tanggalKeberangkatan = new DateTime($paketData['tanggal_keberangkatan']);
    $currentDate = new DateTime();
    
    // If departure date has passed, no cancellation allowed
    if ($currentDate > $tanggalKeberangkatan) {
        return ['error' => 'Tanggal keberangkatan sudah terlewat'];
    }
    
    $interval = $currentDate->diff($tanggalKeberangkatan);
    $monthsUntilDeparture = $interval->m + ($interval->y * 12);
    
    $jenisPaket = $paketData['jenis_paket'];
    $roomType = strtolower($jamaahData['type_room_pilihan']);
    $hargaPaket = $paketData['base_price_' . $roomType];
    $currency = $paketData['currency'];
    
    $dendaPercentage = 0;
    $dendaAmount = 0;
    
    if ($jenisPaket === 'Umroh') {
        // Umroh cancellation rules
        if ($monthsUntilDeparture >= 3 && $monthsUntilDeparture <= 6) {
            $dendaPercentage = 30; // 30%
        } elseif ($monthsUntilDeparture >= 2 && $monthsUntilDeparture < 3) {
            $dendaPercentage = 50; // 50%
        } elseif ($monthsUntilDeparture < 1) {
            $dendaPercentage = 100; // 100% - no refund
        } else {
            // More than 6 months - only DP not refundable (minimum Rp 5.000.000)
            $dendaAmount = ($currency === 'IDR') ? 5000000 : 500; // USD 500 equivalent
            $dendaPercentage = 0;
        }
    } elseif ($jenisPaket === 'Haji') {
        // Haji cancellation rules
        if ($monthsUntilDeparture > 6) {
            $dendaPercentage = 10; // 10%
        } elseif ($monthsUntilDeparture >= 3 && $monthsUntilDeparture <= 6) {
            $dendaPercentage = 40; // 40%
        } elseif ($monthsUntilDeparture >= 2 && $monthsUntilDeparture < 3) {
            $dendaPercentage = 70; // 70%
        } elseif ($monthsUntilDeparture < 1) {
            $dendaPercentage = 90; // 90%
        } else {
            // Fallback for edge cases
            $dendaAmount = ($currency === 'USD') ? 500 : 5000000; // USD 500 admin fee
            $dendaPercentage = 0;
        }
    }
    
    // Calculate final denda amount
    if ($dendaPercentage > 0) {
        $dendaAmount = $hargaPaket * ($dendaPercentage / 100);
    }
    
    $refundAmount = $hargaPaket - $dendaAmount;
    
    return [
        'success' => true,
        'denda_amount' => $dendaAmount,
        'denda_percentage' => $dendaPercentage,
        'refund_amount' => $refundAmount,
        'total_package_price' => $hargaPaket,
        'currency' => $currency,
        'months_until_departure' => $monthsUntilDeparture,
        'package_type' => $jenisPaket,
        'departure_date' => $tanggalKeberangkatan->format('Y-m-d')
    ];
}

// Handle AJAX request for denda calculation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_denda'])) {
    $nik = $_POST['nik'] ?? '';
    
    if (empty($nik)) {
        echo json_encode(['error' => 'NIK tidak valid']);
        exit;
    }
    
    try {
        // Get jamaah data
        $stmt = $pdo->prepare("
            SELECT j.*, p.* 
            FROM data_jamaah j 
            JOIN data_paket p ON j.pak_id = p.pak_id 
            WHERE j.nik = ?
        ");
        $stmt->execute([$nik]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            echo json_encode(['error' => 'Data jamaah tidak ditemukan']);
            exit;
        }
        
        $result = calculateDenda($data, $data);
        echo json_encode($result);
        
    } catch (Exception $e) {
        echo json_encode(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
    exit;
}

// Handle admin cancellation initiation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initiate_cancellation'])) {
    $nik = $_POST['nik'] ?? '';
    $admin_name = $_POST['admin_name'] ?? 'Admin';
    
    if (empty($nik)) {
        echo json_encode(['error' => 'NIK tidak valid']);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get jamaah and package data
        $stmt = $pdo->prepare("
            SELECT j.*, p.* 
            FROM data_jamaah j 
            JOIN data_paket p ON j.pak_id = p.pak_id 
            WHERE j.nik = ?
        ");
        $stmt->execute([$nik]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            throw new Exception('Data jamaah tidak ditemukan');
        }
        
        // Calculate denda
        $dendaResult = calculateDenda($data, $data);
        if (isset($dendaResult['error'])) {
            throw new Exception($dendaResult['error']);
        }
        
        // Create alasan with status info
        $statusInfo = [
            'type' => 'ADMIN_INITIATED',
            'denda_amount' => $dendaResult['denda_amount'],
            'currency' => $dendaResult['currency'],
            'status' => 'pending_payment',
            'admin_name' => $admin_name,
            'calculation_details' => $dendaResult
        ];
        $alasanText = "ADMIN_INITIATED|" . json_encode($statusInfo);
        
        // Insert pembatalan record
        $stmt = $pdo->prepare("
            INSERT INTO data_pembatalan (nik, nama, no_telp, email, alasan, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['nik'],
            $data['nama'],
            $data['no_telp'],
            $data['email'],
            $alasanText
        ]);
        
        $pembatalan_id = $pdo->lastInsertId();
        
        // Send email notification
        require_once 'email_functions.php';
        $emailResult = sendPembatalanNotification($data, $dendaResult, $pembatalan_id);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Pembatalan berhasil diinisiasi dan email notifikasi telah dikirim',
            'pembatalan_id' => $pembatalan_id,
            'denda_info' => $dendaResult,
            'email_sent' => $emailResult
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Gagal menginisiasi pembatalan: ' . $e->getMessage()]);
    }
    exit;
}
?>
