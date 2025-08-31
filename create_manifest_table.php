<?php
require_once 'config.php';

// Set the content type to HTML
header('Content-Type: text/html; charset=utf-8');

$messages = [];
$success = true;

// This file is deprecated - the data_manifest table has been removed
// All room data is now stored directly in data_jamaah

$messages[] = "DEPRECATED: The separate data_manifest table is no longer used.";
$messages[] = "All room data is now stored directly in the data_jamaah table.";
$messages[] = "This provides a simpler database structure and easier management.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEPRECATED - Manifest Table Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h3>DEPRECATED - Manifest Table Setup</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h4>This Feature is Deprecated</h4>
                            <?php foreach ($messages as $message): ?>
                                <p><?= htmlspecialchars($message) ?></p>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <h4>Room Data Fields in data_jamaah</h4>
                            <ul>
                                <li><code>room_prefix</code> - Room code (e.g. Q1, T2, D3)</li>
                                <li><code>medinah_room_number</code> - Room number in Medinah</li>
                                <li><code>mekkah_room_number</code> - Room number in Mekkah</li>
                                <li><code>room_relation</code> - Relation/mahram information</li>
                            </ul>
                        </div>
                        
                        <p>You can continue using the manifest and roomlist features as normal.</p>
                        <div class="mt-4">
                            <a href="admin_manifest.php" class="btn btn-primary">Go to Manifest Management</a>
                            <a href="admin_roomlist.php" class="btn btn-secondary">Go to Roomlist Management</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
