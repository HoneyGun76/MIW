<?php
require_once 'config.php';
require_once 'email_functions.php';
require_once 'terbilang.php';

// Ensure error logs directory exists
if (!file_exists(__DIR__ . '/error_logs')) {
    mkdir(__DIR__ . '/error_logs', 0755, true);
}

// Process payment verification/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        if (!isset($_POST['nik'])) {
            throw new Exception('NIK is required');
        }

        $nik = $_POST['nik'];
        $currentDateTime = new DateTime();

        // Get jamaah and package details
        $stmt = $conn->prepare("
            SELECT j.*, p.currency, p.program_pilihan,
                   CASE j.type_room_pilihan
                       WHEN 'Quad' THEN p.base_price_quad
                       WHEN 'Triple' THEN p.base_price_triple
                       WHEN 'Double' THEN p.base_price_double
                   END as package_price
            FROM data_jamaah j
            JOIN data_paket p ON j.pak_id = p.pak_id
            WHERE j.nik = ?
        ");
        $stmt->execute([$nik]);
        $jamaah = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$jamaah) {
            throw new Exception('Jamaah not found');
        }

        if (isset($_POST['verify_payment'])) {
            if (!isset($_POST['paid_amount']) || empty($_POST['paid_amount'])) {
                throw new Exception('Payment amount is required');
            }

            $paymentTotal = floatval($_POST['paid_amount']);
            $packagePrice = floatval($jamaah['package_price']);
            $paymentRemaining = $packagePrice - $paymentTotal;

            // Generate invoice_id (YYYY + 4-digit sequence)
            $year = date('Y');
            $stmt = $conn->prepare("
                SELECT COALESCE(MAX(SUBSTRING(invoice_id, 5)), 0) as last_sequence 
                FROM data_invoice 
                WHERE invoice_id LIKE ?
            ");
            $stmt->execute([$year . '%']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $sequence = str_pad(intval($result['last_sequence']) + 1, 4, '0', STR_PAD_LEFT);
            $invoiceId = $year . $sequence;

            // Insert into data_invoice
            $stmt = $conn->prepare("
                INSERT INTO data_invoice (
                    invoice_id, pak_id, nik, nama, alamat, no_telp,
                    keterangan, payment_type, program_pilihan, type_room_pilihan,
                    harga_paket, payment_amount, total_uang_masuk, sisa_pembayaran
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $invoiceId,
                $jamaah['pak_id'],
                $nik,
                $jamaah['nama'],
                $jamaah['alamat'],
                $jamaah['no_telp'],
                'Pembayaran ' . ($paymentRemaining > 0 ? 'DP' : 'LUNAS'),
                $paymentRemaining > 0 ? 'DP' : 'LUNAS',
                $jamaah['program_pilihan'],
                $jamaah['type_room_pilihan'],
                $packagePrice,
                $paymentTotal,
                $paymentTotal,
                $paymentRemaining
            ]);

            // Update jamaah record
            $stmt = $conn->prepare("
                UPDATE data_jamaah 
                SET payment_status = 'verified',
                    payment_total = ?,
                    payment_remaining = ?,
                    payment_verified_at = ?,
                    payment_rejected_at = NULL,
                    payment_verified_by = 'Admin'
                WHERE nik = ?
            ");
            $stmt->execute([
                $paymentTotal,
                $paymentRemaining,
                $currentDateTime->format('Y-m-d H:i:s'),
                $nik
            ]);

            // Generate kwitansi using dompdf
            require_once 'vendor/autoload.php';
            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true
            ]);

            // Prepare data for kwitansi
            $kwitansiData = [
                'invoice_id' => $invoiceId,
                'nama' => $jamaah['nama'],
                'nik' => $jamaah['nik'],
                'alamat' => $jamaah['alamat'],
                'no_telp' => $jamaah['no_telp'],
                'program_pilihan' => $jamaah['program_pilihan'],
                'type_room_pilihan' => $jamaah['type_room_pilihan'],
                'package_price' => $packagePrice,
                'payment_type' => $paymentRemaining > 0 ? 'DP' : 'LUNAS',
                'payment_method' => $jamaah['payment_method'],
                'payment_total' => $paymentTotal,
                'payment_remaining' => $paymentRemaining,
                'currency' => $jamaah['currency'], // Add currency from jamaah data
                'diskon' => 0, // Add discount handling if needed
                'keterangan' => 'Pembayaran ' . ($paymentRemaining > 0 ? 'DP' : 'LUNAS')
            ];

            // Generate PDF
            ob_start();
            require 'kwitansi_template.php';
            $html = ob_get_clean();

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A5', 'landscape');
            $dompdf->render();

            // Save to temporary file
            $pdfContent = $dompdf->output();
            $tempFile = tempnam(sys_get_temp_dir(), 'kwitansi_');
            file_put_contents($tempFile, $pdfContent);

            // Send verification email
            $emailData = [
                'nama' => $jamaah['nama'],
                'nik' => $jamaah['nik'],
                'program_pilihan' => $jamaah['program_pilihan'],
                'payment_total' => $paymentTotal,
                'payment_remaining' => $paymentRemaining,
                'currency' => $jamaah['currency'],
                'payment_status' => 'verified',
                'payment_date' => $currentDateTime->format('Y-m-d'),
                'payment_time' => $currentDateTime->format('H:i:s'),
                'email' => $jamaah['email']
            ];

            $attachments = [
                'kwitansi' => [
                    'tmp_name' => $tempFile,
                    'name' => 'Kwitansi_' . $invoiceId . '.pdf',
                    'error' => UPLOAD_ERR_OK
                ]
            ];

            if (!sendPaymentVerificationEmail($emailData, $attachments)) {
                unlink($tempFile);
                throw new Exception('Failed to send verification email');
            }

            unlink($tempFile); // Clean up temporary file

        } elseif (isset($_POST['reject_payment'])) {
            // Update jamaah record for rejection
            $stmt = $conn->prepare("
                UPDATE data_jamaah 
                SET payment_status = 'rejected',
                    payment_total = NULL,
                    payment_remaining = NULL,
                    payment_verified_at = NULL,
                    payment_rejected_at = ?,
                    payment_verified_by = NULL
                WHERE nik = ?
            ");
            $stmt->execute([
                $currentDateTime->format('Y-m-d H:i:s'),
                $nik
            ]);

            // Send rejection email
            $emailData = [
                'nama' => $jamaah['nama'],
                'nik' => $jamaah['nik'],
                'program_pilihan' => $jamaah['program_pilihan'],
                'payment_status' => 'rejected',
                'email' => $jamaah['email'],
                'payment_date' => $jamaah['payment_date'],
                'payment_time' => $jamaah['payment_time'],
                'tanggal_keberangkatan' => $jamaah['tanggal_keberangkatan']  // Add departure date
            ];

            if (!sendPaymentRejectionEmail($emailData)) {
                throw new Exception('Failed to send rejection email');
            }
        }

        $conn->commit();
        $_SESSION['message'] = isset($_POST['verify_payment']) ? 
            'Payment has been verified successfully.' : 
            'Payment has been rejected.';
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        $errorDate = date('Y-m-d');
        $logFile = __DIR__ . "/error_logs/payment_errors_{$errorDate}.log";
        error_log("[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . "\n", 3, $logFile);
        $_SESSION['error'] = $e->getMessage();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch pending registrations
try {
    $stmt = $conn->prepare("
        SELECT 
            j.*,
            p.currency, 
            p.program_pilihan,
            p.tanggal_keberangkatan,
            CASE j.type_room_pilihan
                WHEN 'Quad' THEN p.base_price_quad
                WHEN 'Triple' THEN p.base_price_triple
                WHEN 'Double' THEN p.base_price_double
            END as package_price,
            DATE(j.created_at) as tanggal_daftar
        FROM data_jamaah j
        JOIN data_paket p ON j.pak_id = p.pak_id
        WHERE j.payment_status = 'pending'
        ORDER BY j.created_at DESC
    ");
    $stmt->execute();
    $pendingRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching pending registrations: " . $e->getMessage());
    $pendingRegistrations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Menunggu - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="admin_styles.css" rel="stylesheet">
    <style>
        .file-actions {
            display: inline-flex;
            gap: 0.5rem;
        }
        .preview-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .modal-xl {
            max-width: 1200px;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .card-header {
            padding: 0.75rem 1.25rem;
        }
        .card-body {
            padding: 1.25rem;
        }
        .table-bordered {
            margin-bottom: 0;
        }
        .table-bordered th {
            background-color: rgba(0,0,0,0.03);
        }
        .document-download {
            display: inline-block;
            margin: 0.25rem;
        }
        .badge {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            margin: 0.25rem;
        }
        .btn-outline-primary:hover, .btn-outline-success:hover {
            color: #fff;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
        .status-verified {
            color: #28a745;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-rejected {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 bg-dark text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>MIW Travel Admin Dashboard</h2>
                </div>
            </div>
        </div>

        <?php include 'admin_nav.php'; ?>

        <div class="row mt-3">
            <div class="col-12">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                        <?php unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Pending Registrations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="pendingTable">
                                <thead>
                                    <tr>
                                        <th>Registration Date</th>
                                        <th>Name</th>
                                        <th>NIK</th>
                                        <th>Program</th>
                                        <th>Room Type</th>
                                        <th>Total Package</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRegistrations as $registration): ?>
                                        <tr>
                                            <td><?= date('Y-m-d H:i', strtotime($registration['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($registration['nama']) ?></td>
                                            <td><?= htmlspecialchars($registration['nik']) ?></td>
                                            <td><?= htmlspecialchars($registration['program_pilihan']) ?></td>
                                            <td><?= htmlspecialchars($registration['type_room_pilihan']) ?></td>
                                            <td>
                                                <?= $registration['currency'] ?> 
                                                <?= number_format($registration['package_price'] ?? 0, 2) ?>
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal<?= $registration['nik'] ?>">
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
            </div>
        </div>
    </div>

    <!-- Modals for each registration -->
    <?php foreach ($pendingRegistrations as $registration): ?>
        <div class="modal fade" id="detailModal<?= $registration['nik'] ?>" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Detail Registrasi - <?= htmlspecialchars($registration['nama']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-4">
                                <!-- Personal Information -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="card-title mb-0">Informasi Pribadi</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">NIK</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['nik']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Nama</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['nama']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Email</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['email']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">No. Telepon</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['no_telp']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Jenis Kelamin</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['jenis_kelamin'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Tempat/Tanggal Lahir</div>
                                                <div class="col-sm-8">
                                                    <?= htmlspecialchars($registration['tempat_lahir'] ?? 'N/A') ?> / 
                                                    <?= htmlspecialchars($registration['tanggal_lahir'] ? date('d/m/Y', strtotime($registration['tanggal_lahir'])) : 'N/A') ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Tinggi/Berat Badan</div>
                                                <div class="col-sm-8">
                                                    <?= isset($registration['tinggi_badan'], $registration['berat_badan']) 
                                                        ? htmlspecialchars($registration['tinggi_badan']) . ' cm / ' . htmlspecialchars($registration['berat_badan']) . ' kg'
                                                        : 'N/A' ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Alamat</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['alamat'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Kode Pos</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['kode_pos'] ?? 'N/A') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package and Document Information -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="card-title mb-0">Informasi Paket & Pembayaran</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Program</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['program_pilihan']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Tipe Kamar</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['type_room_pilihan']) ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Biaya Paket</div>
                                                <div class="col-sm-8">
                                                    <?= htmlspecialchars($registration['currency']) ?> 
                                                    <?= number_format($registration['package_price'] ?? 0, 0, ',', '.') ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Tanggal Keberangkatan</div>
                                                <div class="col-sm-8">
                                                    <?= $registration['tanggal_keberangkatan'] ? date('d/m/Y', strtotime($registration['tanggal_keberangkatan'])) : 'N/A' ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Request Khusus</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['request_khusus'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Metode Pembayaran</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['payment_method'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Tipe Pembayaran</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['payment_type'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Nama Rekening</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['transfer_account_name'] ?? 'N/A') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact and Document Status -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="card-title mb-0">Kontak Darurat & Status Dokumen</h6>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3">Kontak Darurat</h6>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Nama Kontak</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['emergency_nama'] ?? 'N/A') ?></div>
                                            </div>
                                            <div class="row mb-4">
                                                <div class="col-sm-4 fw-bold">No. HP</div>
                                                <div class="col-sm-8"><?= htmlspecialchars($registration['emergency_hp'] ?? 'N/A') ?></div>
                                            </div>

                                            <h6 class="fw-bold mb-3">Status Dokumen</h6>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Kartu Keluarga (KK)</div>
                                                <div class="col-sm-4">
                                                    <?= $registration['kk_path'] ? basename($registration['kk_path']) : '-' ?>
                                                </div>
                                                <div class="col-sm-4 file-actions">
                                                    <?php if ($registration['kk_path']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $registration['kk_path'] ?>', 'documents', 'preview')">
                                                            <i class="bi bi-eye"></i> Preview
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $registration['kk_path'] ?>', 'documents', 'download')">
                                                            <i class="bi bi-download"></i> Download
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">KTP</div>
                                                <div class="col-sm-4">
                                                    <?= $registration['ktp_path'] ? basename($registration['ktp_path']) : '-' ?>
                                                </div>
                                                <div class="col-sm-4 file-actions">
                                                    <?php if ($registration['ktp_path']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $registration['ktp_path'] ?>', 'documents', 'preview')">
                                                            <i class="bi bi-eye"></i> Preview
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $registration['ktp_path'] ?>', 'documents', 'download')">
                                                            <i class="bi bi-download"></i> Download
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Paspor</div>
                                                <div class="col-sm-4">
                                                    <?= $registration['paspor_path'] ? basename($registration['paspor_path']) : '-' ?>
                                                </div>
                                                <div class="col-sm-4 file-actions">
                                                    <?php if ($registration['paspor_path']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $registration['paspor_path'] ?>', 'documents', 'preview')">
                                                            <i class="bi bi-eye"></i> Preview
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $registration['paspor_path'] ?>', 'documents', 'download')">
                                                            <i class="bi bi-download"></i> Download
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-sm-4 fw-bold">Bukti Pembayaran</div>
                                                <div class="col-sm-4">
                                                    <?= $registration['payment_path'] ? basename($registration['payment_path']) : '-' ?>
                                                </div>
                                                <div class="col-sm-4 file-actions">
                                                    <?php if ($registration['payment_path']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $registration['payment_path'] ?>', 'payments', 'preview')">
                                                            <i class="bi bi-eye"></i> Preview
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $registration['payment_path'] ?>', 'payments', 'download')">
                                                            <i class="bi bi-download"></i> Download
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details Section -->
                                <div class="col-lg-6">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="card-title mb-0">Detail Pembayaran</h6>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="nik" value="<?= $registration['nik'] ?>">
                                            <input type="hidden" name="payment_status" value="pending">
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Total Biaya Paket</div>
                                                <div class="col-sm-8">
                                                    <?= $registration['currency'] ?> <?= number_format($registration['package_price'] ?? 0, 0, ',', '.') ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Status Pembayaran</div>
                                                <div class="col-sm-8">
                                                    <span class="badge bg-warning">Pending</span>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold">Bukti Transfer</div>
                                                <div class="col-sm-8">
                                                    <?= isset($registration['payment_path']) ? date('d/m/Y H:i', strtotime($registration['payment_path'])) : 'Belum diupload' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center gap-3">
                                    <label for="paid_amount<?= $registration['nik'] ?>" class="form-label mb-0 fw-bold">
                                        Jumlah Pembayaran (<?= $registration['currency'] ?>)
                                    </label>
                                    <div class="input-group" style="width: 200px;">
                                        <span class="input-group-text">
                                            <?= $registration['currency'] === 'USD' ? '$' : 'Rp' ?>
                                        </span>
                                        <input type="number" 
                                            class="form-control payment-amount" 
                                            id="paid_amount<?= $registration['nik'] ?>"
                                            name="paid_amount"
                                            step="0.01"
                                            value="">
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="submit" name="verify_payment" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Verify Payment
                                    </button>
                                    <button type="submit" name="reject_payment" class="btn btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?');">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php include 'includes/file_preview_modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="js/file_handlers.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#pendingTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Tidak ada data yang ditampilkan",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
            
            // Initialize payment input without restrictions
            $('.payment-amount').on('input', function() {
                const input = $(this);
                input.removeClass('is-invalid');
            });
            
            // Modal event handlers
            $('.modal').on('show.bs.modal', function() {
                const modal = $(this);
                const paymentInput = modal.find('.payment-amount');
                const verifyBtn = modal.find('button[name="verify_payment"]');
                
                // Enable payment input and verify button if this is a pending payment
                const paymentStatus = modal.find('[name="payment_status"]').val();
                if (paymentStatus === 'pending') {
                    paymentInput.prop('disabled', false).prop('required', true);
                    verifyBtn.prop('disabled', false);
                } else {
                    paymentInput.prop('disabled', true).prop('required', false);
                    verifyBtn.prop('disabled', true);
                }
            });
            
            // Confirmation for reject
            $('button[name="reject_payment"]').on('click', function(e) {
                if (!confirm('Are you sure you want to reject this payment?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>