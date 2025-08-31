<?php
require_once 'config.php';

if (!isset($_GET['nik'])) {
    header('Location: admin_pembatalan.php?error=invalid_nik');
    exit;
}

$nik = $_GET['nik'];

try {
    $stmt = $conn->prepare("DELETE FROM data_pembatalan WHERE nik = ?");
    $stmt->execute([$nik]);
    header('Location: admin_pembatalan.php?success=deleted');
} catch (PDOException $e) {
    error_log("Delete error: " . $e->getMessage());
    header('Location: admin_pembatalan.php?error=delete_failed');
}