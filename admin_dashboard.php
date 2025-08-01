<?php
require_once 'config.php';

// Function to delete jamaah and related data
function deleteJamaahAndRelatedData($nik, $pdo) {
    try {
        $pdo->beginTransaction();
        
        // First get file paths that need to be deleted
        $stmt = $pdo->prepare("
            SELECT 
                kk_path, ktp_path, paspor_path, payment_path,
                bk_kuning_path, foto_path, fc_ktp_path, fc_ijazah_path,
                fc_kk_path, fc_bk_nikah_path, fc_akta_lahir_path
            FROM data_jamaah 
            WHERE nik = ?
        ");
        $stmt->execute([$nik]);
        $files = $stmt->fetch(PDO::FETCH_ASSOC);

        // The data_manifest table is no longer used - roomlist data is stored in data_jamaah
        // No need to delete from data_manifest anymore
        
        // Delete from invoice if exists
        $stmt = $pdo->prepare("DELETE FROM data_invoice WHERE nik = ?");
        $stmt->execute([$nik]);

        // Delete from jamaah
        $stmt = $pdo->prepare("DELETE FROM data_jamaah WHERE nik = ?");
        $stmt->execute([$nik]);

        // Delete physical files after successful database deletion
        foreach ($files as $path) {
            if ($path && file_exists($_SERVER['DOCUMENT_ROOT'] . '/MIW/' . $path)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/MIW/' . $path);
            }
        }

        $pdo->commit();
        return ['success' => true, 'message' => 'Data jamaah berhasil dihapus'];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error deleting jamaah data: " . $e->getMessage());
        return ['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_jamaah'])) {
    $nik = $_POST['nik'];
    $result = deleteJamaahAndRelatedData($nik, $pdo);
    
    if ($result['success']) {
        $_SESSION['message'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Handle AJAX request for jamaah details
if (isset($_GET['nik']) && !empty($_GET['nik']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            p.program_pilihan,
            p.jenis_paket,
            p.tanggal_keberangkatan,
            p.base_price_quad,
            p.base_price_triple,
            p.base_price_double,
            p.currency,
            i.invoice_id,
            i.payment_type as invoice_payment_type,
            j.payment_path,  -- Explicitly include payment_path
            COALESCE(j.payment_total, i.payment_amount) as payment_total,
            COALESCE(j.payment_remaining, i.sisa_pembayaran) as payment_remaining,
            i.total_uang_masuk,
            i.diskon
        FROM data_jamaah j
        LEFT JOIN data_paket p ON j.pak_id = p.pak_id
        LEFT JOIN data_invoice i ON j.nik = i.nik
        WHERE j.nik = ?
    ");
    $stmt->execute([$_GET['nik']]);
    $jamaah_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($jamaah_details);
    exit;
}

// Get total count of jamaah
$stmt = $pdo->query("SELECT COUNT(*) as total FROM data_jamaah");
$total_jamaah = $stmt->fetch()['total'];

// Get verified payments
$stmt = $pdo->query("
    SELECT 
        j.*,
        p.program_pilihan,
        p.jenis_paket,
        p.tanggal_keberangkatan,
        p.base_price_quad,
        p.base_price_triple,
        p.base_price_double,
        i.invoice_id,
        i.payment_type as invoice_payment_type,
        i.payment_amount,
        i.total_uang_masuk,
        i.sisa_pembayaran
    FROM data_jamaah j
    LEFT JOIN data_paket p ON j.pak_id = p.pak_id
    LEFT JOIN data_invoice i ON j.nik = i.nik
    WHERE j.payment_status = 'verified'
    ORDER BY j.payment_verified_at DESC
");
$verified_payments = $stmt->fetchAll();

// Get pending payments
$stmt = $pdo->query("
    SELECT 
        j.*,
        p.program_pilihan,
        p.jenis_paket,
        p.tanggal_keberangkatan,
        p.base_price_quad,
        p.base_price_triple,
        p.base_price_double,
        i.invoice_id,
        i.payment_type as invoice_payment_type,
        i.payment_amount,
        i.total_uang_masuk,
        i.sisa_pembayaran
    FROM data_jamaah j
    LEFT JOIN data_paket p ON j.pak_id = p.pak_id
    LEFT JOIN data_invoice i ON j.nik = i.nik
    WHERE j.payment_status = 'pending'
    ORDER BY j.payment_path DESC
");
$pending_payments = $stmt->fetchAll();

// Get jamaah data with package and payment info
$query = "
    SELECT 
        j.*,
        p.program_pilihan,
        p.jenis_paket,
        p.tanggal_keberangkatan,
        p.base_price_quad,
        p.base_price_triple,
        p.base_price_double,
        i.invoice_id,
        i.payment_type as invoice_payment_type,
        i.payment_amount,
        i.total_uang_masuk,
        i.sisa_pembayaran
    FROM data_jamaah j
    LEFT JOIN data_paket p ON j.pak_id = p.pak_id
    LEFT JOIN data_invoice i ON j.nik = i.nik
    ORDER BY j.created_at DESC
";
$stmt = $pdo->query($query);
$jamaah_list = $stmt->fetchAll();

// Get details for a specific jamaah if NIK is provided
$jamaah_details = null;
if (isset($_GET['nik'])) {
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            p.program_pilihan,
            p.jenis_paket,
            p.tanggal_keberangkatan,
            p.base_price_quad,
            p.base_price_triple,
            p.base_price_double,
            i.invoice_id,
            i.payment_type as invoice_payment_type,
            i.payment_amount,
            i.total_uang_masuk,
            i.sisa_pembayaran,
            i.diskon
        FROM data_jamaah j
        LEFT JOIN data_paket p ON j.pak_id = p.pak_id
        LEFT JOIN data_invoice i ON j.nik = i.nik
        WHERE j.nik = ?
    ");
    $stmt->execute([$_GET['nik']]);
    $jamaah_details = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Data Jamaah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .file-actions {
            display: inline-flex;
            gap: 0.5rem;
        }
        .preview-container {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <header class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-people-fill"></i> Data Jamaah</h2>
                <div>
                    <span class="badge bg-primary">Total Jamaah: <?= $total_jamaah ?></span>
                </div>
            </div>
        </header>

        <?php include 'admin_nav.php'; ?>

        <!-- Records Per Page Selector -->
        <div class="records-per-page d-flex justify-content-end mb-3">
            <label for="recordsPerPage">Records per page:</label>
            <select class="form-select form-select-sm" id="recordsPerPage" style="width: auto;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- All Jamaah Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="table-container">
                    <div class="table-title">
                        <h5>All Registered Jamaah</h5>
                    </div>
                    <div class="table-responsive scrollable-table" style="--records-per-page: 10;">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>NIK</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Type</th>
                                    <th>Departure</th>
                                    <th>Payment Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jamaah_list as $index => $jamaah): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($jamaah['nik']) ?></td>
                                    <td><?= htmlspecialchars($jamaah['nama']) ?></td>
                                    <td><?= htmlspecialchars($jamaah['program_pilihan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($jamaah['jenis_paket'] ?? '-') ?></td>
                                    <td><?= $jamaah['tanggal_keberangkatan'] ? date('d M Y', strtotime($jamaah['tanggal_keberangkatan'])) : '-' ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'verified' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger'
                                        ];
                                        $status = $jamaah['payment_status'] ?? 'pending';
                                        ?>
                                        <span class="badge bg-<?= $status_class[$status] ?? 'secondary' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-details" 
                                                data-nik="<?= $jamaah['nik'] ?>" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#jamaahDetailsModal">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Verified Payments Tab -->
            <div class="tab-pane fade" id="verified" role="tabpanel" aria-labelledby="verified-tab">
                <div class="table-container">
                    <div class="table-title">
                        <h5>Verified Payments</h5>
                    </div>
                    <div class="table-responsive scrollable-table">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Total Paid</th>
                                    <th>Remaining</th>
                                    <th>Verified At</th>
                                    <th>Verified By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($verified_payments as $i => $jamaah): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($jamaah['nama']) ?></td>
                                    <td><?= htmlspecialchars($jamaah['program_pilihan']) ?></td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($jamaah['payment_type']) ?></span></td>
                                    <td><?= number_format($jamaah['payment_total'] ?? 0, 2) ?></td>
                                    <td><?= number_format($jamaah['total_uang_masuk'] ?? 0, 2) ?></td>
                                    <td><?= number_format($jamaah['payment_remaining'] ?? 0, 2) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($jamaah['payment_verified_at'])) ?></td>
                                    <td><?= htmlspecialchars($jamaah['payment_verified_by']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewDetails('<?= $jamaah['nik'] ?>')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <a href="invoice.php?nik=<?= $jamaah['nik'] ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pending Payments Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-container">
                    <div class="table-title">
                        <h5>Pending Payments</h5>
                    </div>
                    <div class="table-responsive scrollable-table">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Program</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Bank</th>
                                    <th>Account Name</th>
                                    <th>Payment Date</th>
                                    <th>Uploaded At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_payments as $i => $jamaah): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($jamaah['nama']) ?></td>
                                    <td><?= htmlspecialchars($jamaah['program_pilihan']) ?></td>
                                    <td><span class="badge bg-warning"><?= htmlspecialchars($jamaah['payment_type']) ?></span></td>
                                    <td><?= number_format($jamaah['payment_total'] ?? 0, 2) ?></td>
                                    <td><?= htmlspecialchars($jamaah['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($jamaah['transfer_account_name']) ?></td>
                                    <td><?= date('d M Y', strtotime($jamaah['payment_date'])) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($jamaah['payment_path'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewDetails('<?= $jamaah['nik'] ?>')">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="verifyPayment('<?= $jamaah['nik'] ?>')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rejectPayment('<?= $jamaah['nik'] ?>')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
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

    <!-- Jamaah Details Modal -->
    <div class="modal fade" id="jamaahDetailsModal" tabindex="-1" aria-labelledby="jamaahDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jamaahDetailsModalLabel">Jamaah Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="registration-details">
                        <h2>Personal Information</h2>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Basic Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>NIK</th>
                                        <td class="jamaah-nik"></td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td class="jamaah-nama"></td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td class="jamaah-gender"></td>
                                    </tr>
                                    <tr>
                                        <th>Birth Place</th>
                                        <td class="jamaah-birthplace"></td>
                                    </tr>
                                    <tr>
                                        <th>Birth Date</th>
                                        <td class="jamaah-birthdate"></td>
                                    </tr>
                                    <tr>
                                        <th>Age</th>
                                        <td class="jamaah-age"></td>
                                    </tr>
                                    <tr>
                                        <th>Nationality</th>
                                        <td class="jamaah-nationality"></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h3>Contact Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Email</th>
                                        <td class="jamaah-email"></td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td class="jamaah-phone"></td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td class="jamaah-address"></td>
                                    </tr>
                                    <tr>
                                        <th>Postal Code</th>
                                        <td class="jamaah-postal"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h3>Family Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Father's Name</th>
                                        <td class="jamaah-father"></td>
                                    </tr>
                                    <tr>
                                        <th>Mother's Name</th>
                                        <td class="jamaah-mother"></td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status</th>
                                        <td class="jamaah-marital"></td>
                                    </tr>
                                    <tr>
                                        <th>Mahram Name</th>
                                        <td class="jamaah-mahram-name"></td>
                                    </tr>
                                    <tr>
                                        <th>Mahram Relation</th>
                                        <td class="jamaah-mahram-relation"></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h3>Physical Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Height</th>
                                        <td class="jamaah-height"></td>
                                    </tr>
                                    <tr>
                                        <th>Weight</th>
                                        <td class="jamaah-weight"></td>
                                    </tr>
                                    <tr>
                                        <th>Blood Type</th>
                                        <td class="jamaah-blood"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <h2 class="mt-5">Travel Information</h2>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Package Details</h3>
                                <table class="table">
                                    <tr>
                                        <th>Package Type</th>
                                        <td class="jamaah-package-type"></td>
                                    </tr>
                                    <tr>
                                        <th>Program</th>
                                        <td class="jamaah-program"></td>
                                    </tr>
                                    <tr>
                                        <th>Room Type</th>
                                        <td class="jamaah-room-type"></td>
                                    </tr>
                                    <tr>
                                        <th>Departure Date</th>
                                        <td class="jamaah-departure"></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h3>Payment Information</h3>
                                <table class="table">
                                    <tr>
                                        <th>Payment Status</th>
                                        <td class="jamaah-payment-status"></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Type</th>
                                        <td class="jamaah-payment-type"></td>
                                    </tr>
                                    <tr>
                                        <th>Amount Paid</th>
                                        <td class="jamaah-payment-amount"></td>
                                    </tr>
                                    <tr>
                                        <th>Remaining Balance</th>
                                        <td class="jamaah-payment-remaining"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <h3 class="mt-4">Document Status</h3>
                        <table class="table">
                            <tr>
                                <th>Passport Name</th>
                                <td class="jamaah-passport-name"></td>
                            </tr>
                            <tr>
                                <th>Passport Number</th>
                                <td class="jamaah-passport-number"></td>
                            </tr>
                            <tr>
                                <th>Passport Issue Place</th>
                                <td class="jamaah-passport-issue-place"></td>
                            </tr>
                        </table>

                        <h4 class="mt-4">Documents</h4>
                        <table class="table">
                            <tr>
                                <th>KTP</th>
                                <td class="jamaah-ktp-actions file-actions"></td>
                            </tr>
                            <tr>
                                <th>KK</th>
                                <td class="jamaah-kk-actions file-actions"></td>
                            </tr>
                            <tr>
                                <th>Passport</th>
                                <td class="jamaah-passport-actions file-actions"></td>
                            </tr>
                            <tr>
                                <th>Payment Proof</th>
                                <td class="jamaah-payment-actions file-actions"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <form method="POST" onsubmit="return confirmDelete()" class="me-2">
                        <input type="hidden" name="nik" id="deleteNikInput">
                        <button type="submit" name="delete_jamaah" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Hapus Data Jamaah
                        </button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/file_preview_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/file_handlers.js"></script>
    <script>
        function confirmDelete() {
            return confirm('Apakah Anda yakin ingin menghapus data jamaah ini? Semua data terkait termasuk manifest, invoice, dan berkas yang diunggah akan dihapus secara permanen.');
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // View details button click handler
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                var nik = this.getAttribute('data-nik');
                
                // Fetch jamaah details via AJAX
                fetch(`admin_dashboard.php?nik=${nik}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(jamaah => {
                        // Basic Information
                        document.querySelector('.jamaah-nik').textContent = jamaah.nik;
                        document.querySelector('.jamaah-nama').textContent = jamaah.nama;
                        document.querySelector('.jamaah-gender').textContent = jamaah.jenis_kelamin;
                        document.querySelector('.jamaah-birthplace').textContent = jamaah.tempat_lahir;
                        document.querySelector('.jamaah-birthdate').textContent = new Date(jamaah.tanggal_lahir).toLocaleDateString();
                        document.querySelector('.jamaah-age').textContent = jamaah.umur;
                        document.querySelector('.jamaah-nationality').textContent = jamaah.kewarganegaraan;
                        
                        // Contact Information
                        document.querySelector('.jamaah-email').textContent = jamaah.email;
                        document.querySelector('.jamaah-phone').textContent = jamaah.no_telp;
                        document.querySelector('.jamaah-address').textContent = jamaah.alamat;
                        document.querySelector('.jamaah-postal').textContent = jamaah.kode_pos;
                        
                        // Family Information
                        document.querySelector('.jamaah-father').textContent = jamaah.nama_ayah;
                        document.querySelector('.jamaah-mother').textContent = jamaah.nama_ibu;
                        document.querySelector('.jamaah-marital').textContent = jamaah.status_perkawinan;
                        document.querySelector('.jamaah-mahram-name').textContent = jamaah.nama_mahram;
                        document.querySelector('.jamaah-mahram-relation').textContent = jamaah.hubungan_mahram;
                        
                        // Physical Information
                        document.querySelector('.jamaah-height').textContent = jamaah.tinggi_badan + ' cm';
                        
                        // File Actions
                        const createFileActions = (path, type) => {
                            if (!path) return 'No file uploaded';
                            return `
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('${path}', '${type}', 'preview')">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('${path}', '${type}', 'download')">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            `;
                        };

                        // Update document action buttons
                        document.querySelector('.jamaah-ktp-actions').innerHTML = createFileActions(jamaah.ktp_path, 'documents');
                        document.querySelector('.jamaah-kk-actions').innerHTML = createFileActions(jamaah.kk_path, 'documents');
                        document.querySelector('.jamaah-passport-actions').innerHTML = createFileActions(jamaah.paspor_path, 'documents');
                        document.querySelector('.jamaah-payment-actions').innerHTML = createFileActions(jamaah.payment_path, 'payments');
                        document.querySelector('.jamaah-weight').textContent = jamaah.berat_badan + ' kg';
                        document.querySelector('.jamaah-blood').textContent = jamaah.golongan_darah;
                        
                        // Package Details
                        document.querySelector('.jamaah-package-type').textContent = jamaah.jenis_paket;
                        document.querySelector('.jamaah-program').textContent = jamaah.program_pilihan;
                        document.querySelector('.jamaah-room-type').textContent = jamaah.type_room_pilihan;
                        document.querySelector('.jamaah-departure').textContent = new Date(jamaah.tanggal_keberangkatan).toLocaleDateString();
                        
                        // Set NIK for delete form
                        document.getElementById('deleteNikInput').value = jamaah.nik;
                        
                        // Payment Information
                        document.querySelector('.jamaah-payment-status').textContent = jamaah.payment_status;
                        document.querySelector('.jamaah-payment-type').textContent = jamaah.invoice_payment_type || jamaah.payment_type;
                        document.querySelector('.jamaah-payment-amount').textContent = jamaah.payment_total ? 
                            new Intl.NumberFormat('id-ID', { style: 'currency', currency: jamaah.jenis_paket === 'Haji' ? 'USD' : 'IDR' }).format(jamaah.payment_total) : '-';
                        document.querySelector('.jamaah-payment-remaining').textContent = jamaah.payment_remaining ?
                            new Intl.NumberFormat('id-ID', { style: 'currency', currency: jamaah.jenis_paket === 'Haji' ? 'USD' : 'IDR' }).format(jamaah.payment_remaining) : '-';
                        
                        // Document Information
                        document.querySelector('.jamaah-passport-name').textContent = jamaah.nama_paspor;
                        document.querySelector('.jamaah-passport-number').textContent = jamaah.no_paspor;
                        document.querySelector('.jamaah-passport-issue-place').textContent = jamaah.tempat_pembuatan_paspor;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Records per page change handler
        document.getElementById('recordsPerPage').addEventListener('change', function() {
            var value = this.value;
            document.querySelectorAll('.scrollable-table').forEach(table => {
                table.style.setProperty('--records-per-page', value);
            });
        });
    </script>
</body>
</html>