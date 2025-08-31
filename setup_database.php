<?php
// Database setup for Black Box Testing
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->exec('CREATE DATABASE IF NOT EXISTS data_miw');
    echo "Database data_miw created or already exists\n";
    
    // Now connect to the database
    $pdo = new PDO('mysql:host=localhost;dbname=data_miw;charset=utf8mb4', 'root', '');
    
    // Create basic tables if they don't exist
    $createTables = [
        "CREATE TABLE IF NOT EXISTS jamaah (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_lengkap VARCHAR(255) NOT NULL,
            nik VARCHAR(16) UNIQUE NOT NULL,
            tempat_lahir VARCHAR(100),
            tanggal_lahir DATE,
            jenis_kelamin ENUM('L', 'P'),
            alamat TEXT,
            no_telepon VARCHAR(20),
            email VARCHAR(255),
            paspor VARCHAR(20),
            paket_id INT,
            jenis_perjalanan ENUM('umroh', 'haji'),
            tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'verified', 'paid', 'departed') DEFAULT 'pending'
        )",
        
        "CREATE TABLE IF NOT EXISTS paket (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_paket VARCHAR(255) NOT NULL,
            deskripsi TEXT,
            harga DECIMAL(15,2),
            tanggal_keberangkatan DATE,
            durasi INT,
            kapasitas INT,
            jenis_perjalanan ENUM('umroh', 'haji'),
            status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )",
        
        "        "CREATE TABLE IF NOT EXISTS pembatalan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            jamaah_id INT,
            alasan TEXT,
            tanggal_pengajuan DATE,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            tanggal_processed DATETIME,
            notes TEXT,
            FOREIGN KEY (jamaah_id) REFERENCES jamaah(id) ON DELETE CASCADE
        )",
        
        "CREATE TABLE IF NOT EXISTS email_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient_email VARCHAR(255) NOT NULL,
            recipient_name VARCHAR(255),
            subject VARCHAR(500) NOT NULL,
            body TEXT NOT NULL,
            email_type VARCHAR(50) DEFAULT 'general',
            attachments JSON,
            status ENUM('pending', 'processing', 'sent', 'failed') DEFAULT 'pending',
            priority INT DEFAULT 5,
            attempts INT DEFAULT 0,
            max_attempts INT DEFAULT 3,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            processed_at TIMESTAMP NULL,
            error_message TEXT NULL,
            
            INDEX idx_status_priority (status, priority),
            INDEX idx_created_at (created_at)
        )""
    ];
    
    foreach ($createTables as $sql) {
        $pdo->exec($sql);
    }
    
    // Insert sample paket if empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM paket");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        $samplePaket = "INSERT INTO paket (nama_paket, deskripsi, harga, tanggal_keberangkatan, durasi, kapasitas, jenis_perjalanan) 
                       VALUES ('Paket Umroh Reguler', 'Paket umroh 14 hari dengan fasilitas lengkap', 25000000, '2025-12-01', 14, 45, 'umroh')";
        $pdo->exec($samplePaket);
        echo "Sample paket inserted\n";
    }
    
    echo "Database setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
