<?php
require_once "config.php";
require_once "paket_functions.php";

// Handle CRUD operations
handlePackageOperations($conn);

// Get all packages
$paketData = getAllPackages($conn);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dasbor Admin - Manajemen Paket</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 admin-header">
                <h2>MIW Travel Admin Dashboard</h2>
            </div>
        </div>

        <?php include 'admin_nav.php'; ?>

        <div class="row mt-3">
            <div class="col-12">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="table-container">
                    <div class="table-title">Paket Management</div>
                    <div class="d-flex justify-content-between mb-3">
                        <div class="btn-group">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Filter berdasarkan Tipe Paket
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?type=all">All Packages</a></li>
                                <li><a class="dropdown-item" href="?type=Umroh">Umroh</a></li>
                                <li><a class="dropdown-item" href="?type=Haji">Haji</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                            Add New Package
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis Paket</th>
                                    <th>Program</th>
                                    <th>Tanggal</th>
                                    <th>Base Prices</th>
                                    <th>Rooms</th>
                                    <th>Hotels</th>
                                    <th>HCN</th>
                                    <th class="action-btns">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paketData as $package): 
                                    $roomNumbersRaw = $package['room_numbers'] ?? '';
                                    $roomNumbers = json_decode($roomNumbersRaw, true);
                                    if (!is_array($roomNumbers)) {
                                        // Fallback: treat as comma-separated string
                                        $roomNumbers = array_filter(array_map('trim', explode(',', $roomNumbersRaw)));
                                    }
                                    $medinahRooms = json_decode($package['hotel_medinah_rooms'] ?? '{}', true);
                                    $makkahRooms = json_decode($package['hotel_makkah_rooms'] ?? '{}', true);
                                    $additionalHotels = json_decode($package['additional_hotels'] ?? '[]', true);
                                    $additionalHotelsRooms = json_decode($package['additional_hotels_rooms'] ?? '{}', true);
                                ?>
                                <tr>
                                    <td><?= $package['pak_id'] ?></td>
                                    <td><?= $package['jenis_paket'] ?></td>
                                    <td><?= $package['program_pilihan'] ?></td>
                                    <td><?= $package['tanggal_keberangkatan'] ?></td>
                                    <td>
                                        <strong>Quad:</strong> <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_quad'] ?? 0, 0, ',', '.') ?><br>
                                        <strong>Triple:</strong> <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_triple'] ?? 0, 0, ',', '.') ?><br>
                                        <strong>Double:</strong> <?= $package['currency'] == 'USD' ? '$' : 'Rp ' ?><?= number_format($package['base_price_double'] ?? 0, 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Calculate available rooms
                                        $quadAvailable = min(
                                            count($medinahRooms['quad'] ?? []),
                                            count($makkahRooms['quad'] ?? [])
                                        );
                                        $tripleAvailable = min(
                                            count($medinahRooms['triple'] ?? []),
                                            count($makkahRooms['triple'] ?? [])
                                        );
                                        $doubleAvailable = min(
                                            count($medinahRooms['double'] ?? []),
                                            count($makkahRooms['double'] ?? [])
                                        );
                                        $totalAvailable = $quadAvailable + $tripleAvailable + $doubleAvailable;
                                        ?>
                                        
                                        <strong>Available:</strong> <?= $totalAvailable ?><br>
                                        <strong>Quad:</strong> <?= $quadAvailable ?><br>
                                        <strong>Triple:</strong> <?= $tripleAvailable ?><br>
                                        <strong>Double:</strong> <?= $doubleAvailable ?><br>
                                        
                                        <div class="room-numbering">
                                            <?php foreach ($roomNumbers as $room): ?>
                                                <span class="room-badge"><?= $room ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>Medinah:</strong> <?= $package['hotel_medinah'] ?><br>
                                        <strong>Makkah:</strong> <?= $package['hotel_makkah'] ?>
                                        <?php if (!empty($additionalHotels)): ?>
                                            <br><strong>Additional Hotels:</strong> <?= implode(', ', $additionalHotels) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $package['hcn'] ?></td>
                                    <td class="action-btns">
                                        <form method="post" action="admin_paket.php" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $package['pak_id'] ?>">
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="loadEditData(<?= $package['pak_id'] ?>)">
                                                Edit
                                            </button>
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-labelledby="addPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPackageModalLabel">Add New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Paket</label>
                                <select class="form-select" name="jenis_paket" id="add_jenis_paket" required>
                                    <option value="Umroh">Umroh</option>
                                    <option value="Haji">Haji</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" name="currency" id="add_currency" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Pilihan</label>
                                <input type="text" class="form-control" name="program_pilihan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Keberangkatan</label>
                                <input type="date" class="form-control" name="tanggal_keberangkatan" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Quad)</label>
                                <input type="number" class="form-control" name="base_price_quad" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Triple)</label>
                                <input type="number" class="form-control" name="base_price_triple" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Double)</label>
                                <input type="number" class="form-control" name="base_price_double" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Hotel Medinah</label>
                                <input type="text" class="form-control" name="hotel_medinah">
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Quad Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_quad_rooms" placeholder="e.g., 101,102,103">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Triple Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_triple_rooms" placeholder="e.g., 201,202,203">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Double Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_double_rooms" placeholder="e.g., 301,302,303">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hotel Makkah</label>
                                <input type="text" class="form-control" name="hotel_makkah">
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Quad Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_quad_rooms" placeholder="e.g., 101,102,103">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Triple Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_triple_rooms" placeholder="e.g., 201,202,203">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Double Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_double_rooms" placeholder="e.g., 301,302,303">
                                </div>
                            </div>
                        </div>

                        <div class="hotel-container mb-3">
                            <h5>Hotel Contract Numbers (HCN)</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Medinah HCN</label>
                                    <input type="text" class="form-control" name="hcn_medinah" placeholder="e.g., MAD-HJ2026-7421">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Makkah HCN</label>
                                    <input type="text" class="form-control" name="hcn_makkah" placeholder="e.g., MAK-HJ2026-9156">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">HCN Issued Date</label>
                                    <input type="date" class="form-control" name="hcn_issued_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HCN Expiry Date</label>
                                    <input type="date" class="form-control" name="hcn_expiry_date">
                                </div>
                            </div>
                        </div>

                        <div class="hotel-container mb-3">
                            <h5>Additional Hotels</h5>
                            <div id="hotelsContainer">
                            </div>
                            <button type="button" id="addHotel" class="btn btn-sm btn-secondary mt-2">Add Hotel</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add" class="btn btn-primary">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Package Modal -->
    <div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPackageModalLabel">Edit Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="admin_paket.php">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_package_id">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jenis Paket</label>
                                <select class="form-select" name="jenis_paket" id="edit_jenis_paket" required>
                                    <option value="Umroh">Umroh</option>
                                    <option value="Haji">Haji</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" name="currency" id="edit_currency" readonly>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Program Pilihan</label>
                                <input type="text" class="form-control" name="program_pilihan" id="edit_program_pilihan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Keberangkatan</label>
                                <input type="date" class="form-control" name="tanggal_keberangkatan" id="edit_tanggal_keberangkatan" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Quad)</label>
                                <input type="number" class="form-control" name="base_price_quad" id="edit_base_price_quad" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Triple)</label>
                                <input type="number" class="form-control" name="base_price_triple" id="edit_base_price_triple" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Base Price (Double)</label>
                                <input type="number" class="form-control" name="base_price_double" id="edit_base_price_double" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Hotel Medinah</label>
                                <input type="text" class="form-control" name="hotel_medinah" id="edit_hotel_medinah">
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Quad Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_quad_rooms" id="edit_medinah_quad_rooms" placeholder="e.g., 101,102,103">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Triple Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_triple_rooms" id="edit_medinah_triple_rooms" placeholder="e.g., 201,202,203">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Medinah Double Room Numbers</label>
                                    <input type="text" class="form-control" name="medinah_double_rooms" id="edit_medinah_double_rooms" placeholder="e.g., 301,302,303">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hotel Makkah</label>
                                <input type="text" class="form-control" name="hotel_makkah" id="edit_hotel_makkah">
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Quad Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_quad_rooms" id="edit_makkah_quad_rooms" placeholder="e.g., 101,102,103">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Triple Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_triple_rooms" id="edit_makkah_triple_rooms" placeholder="e.g., 201,202,203">
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Makkah Double Room Numbers</label>
                                    <input type="text" class="form-control" name="makkah_double_rooms" id="edit_makkah_double_rooms" placeholder="e.g., 301,302,303">
                                </div>
                            </div>
                        </div>

                        <div class="hotel-container mb-3">
                            <h5>Hotel Contract Numbers (HCN)</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Medinah HCN</label>
                                    <input type="text" class="form-control" name="hcn_medinah" id="edit_hcn_medinah" placeholder="e.g., MAD-HJ2026-7421">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Makkah HCN</label>
                                    <input type="text" class="form-control" name="hcn_makkah" id="edit_hcn_makkah" placeholder="e.g., MAK-HJ2026-9156">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">HCN Issued Date</label>
                                    <input type="date" class="form-control" name="hcn_issued_date" id="edit_hcn_issued_date">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">HCN Expiry Date</label>
                                    <input type="date" class="form-control" name="hcn_expiry_date" id="edit_hcn_expiry_date">
                                </div>
                            </div>
                        </div>

                        <div class="hotel-container mb-3">
                            <h5>Additional Hotels</h5>
                            <div id="editHotelsContainer">
                            </div>
                            <button type="button" id="addEditHotel" class="btn btn-sm btn-secondary mt-2">Add Hotel</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="update" class="btn btn-primary">Update Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="paket_scripts.js"></script>
</body>
</html>