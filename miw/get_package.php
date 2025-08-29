<?php
require_once "config.php";
require_once "paket_functions.php";

if (isset($_GET['id'])) {
    $package = getPackageById($conn, $_GET['id']);
    header('Content-Type: application/json');
    echo json_encode($package);
    exit();
}