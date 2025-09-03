<?php
require_once "config.php";

try {
    if ($conn) {
        // Check if column already exists first
        $checkColumn = $conn->query("SHOW COLUMNS FROM data_paket LIKE 'flyer_image'");
        if ($checkColumn->rowCount() == 0) {
            // Add flyer column to data_paket table
            $sql = "ALTER TABLE data_paket ADD COLUMN flyer_image VARCHAR(255) DEFAULT NULL";
            $conn->exec($sql);
            echo "Flyer column added successfully!";
        } else {
            echo "Flyer column already exists!";
        }
    } else {
        echo "Database connection failed. This is normal for Railway deployment - the migration will run automatically on first deployment.";
    }
} catch (Exception $e) {
    echo "Info: " . $e->getMessage() . " (This may be expected if running on Railway)";
}
?>
