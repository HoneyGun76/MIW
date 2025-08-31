<?php
require_once 'config.php';

if (!isset($_GET['nik'])) {
    die('NIK tidak valid');
}

$nik = $_GET['nik'];
$pembatalan_id = $_GET['pembatalan_id'] ?? null;

// Get pembatalan data with jamaah and package info
$query = "SELECT p.*, j.nama as jamaah_nama, j.pak_id, pk.program_pilihan, pk.jenis_paket, pk.tanggal_keberangkatan,
                 CASE 
                     WHEN p.alasan LIKE 'ADMIN_INITIATED|%' THEN 'admin_initiated'
                     ELSE 'regular'
                 END as cancellation_type
          FROM data_pembatalan p
          JOIN data_jamaah j ON p.nik = j.nik 
          LEFT JOIN data_paket pk ON j.pak_id = pk.pak_id
          WHERE p.nik = ?";

$params = [$nik];
if ($pembatalan_id) {
    $query .= " AND p.id = ?";
    $params[] = $pembatalan_id;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die('Data tidak ditemukan');
}

// Parse admin-initiated data if applicable
$statusInfo = null;
$dendaInfo = null;
if ($record['cancellation_type'] === 'admin_initiated') {
    $statusJson = substr($record['alasan'], 16); // Remove "ADMIN_INITIATED|"
    $statusInfo = json_decode($statusJson, true);
    $dendaInfo = $statusInfo['calculation_details'] ?? null;
}
?>

<div class="registration-details">
    <h3>Informasi Jamaah</h3>
    <table class="table table-borderless">
        <tr>
            <td width="30%"><strong>NIK</strong></td>
            <td><?= htmlspecialchars($record['nik']) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Lengkap</strong></td>
            <td><?= htmlspecialchars($record['jamaah_nama']) ?></td>
        </tr>
        <tr>
            <td><strong>No. Telepon</strong></td>
            <td><?= htmlspecialchars($record['no_telp']) ?></td>
        </tr>
        <tr>
            <td><strong>Email</strong></td>
            <td><?= htmlspecialchars($record['email']) ?></td>
        </tr>
        <tr>
            <td><strong>Program</strong></td>
            <td><?= htmlspecialchars($record['program_pilihan'] ?? 'N/A') ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Keberangkatan</strong></td>
            <td><?= $record['tanggal_keberangkatan'] ? date('d/m/Y', strtotime($record['tanggal_keberangkatan'])) : 'N/A' ?></td>
        </tr>
    </table>

    <h3>Detail Pembatalan</h3>
    <table class="table table-borderless">
        <tr>
            <td width="30%"><strong>Jenis Pembatalan</strong></td>
            <td>
                <?php if ($record['cancellation_type'] === 'admin_initiated'): ?>
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-person-gear"></i> Admin Initiated
                    </span>
                <?php else: ?>
                    <span class="badge bg-info">
                        <i class="bi bi-person"></i> User Request
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        
        <?php if ($record['cancellation_type'] === 'admin_initiated' && $statusInfo): ?>
            <tr>
                <td><strong>Status</strong></td>
                <td>
                    <?php
                    $statusClass = match($statusInfo['status']) {
                        'pending_payment' => 'bg-warning text-dark',
                        'payment_submitted' => 'bg-primary',
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $statusText = match($statusInfo['status']) {
                        'pending_payment' => 'Menunggu Pembayaran Denda',
                        'payment_submitted' => 'Pembayaran Denda Disubmit',
                        'approved' => 'Pembatalan Disetujui',
                        'rejected' => 'Pembatalan Ditolak',
                        default => 'Unknown'
                    };
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                    
                    <!-- Hidden status for JavaScript -->
                    <span class="d-none" id="cancellation-status"><?= $statusInfo['status'] ?></span>
                </td>
            </tr>
            
            <tr>
                <td><strong>Admin yang Memproses</strong></td>
                <td><?= htmlspecialchars($statusInfo['admin_name'] ?? 'N/A') ?></td>
            </tr>
            
            <?php if ($dendaInfo): ?>
                <tr>
                    <td><strong>Perhitungan Denda</strong></td>
                    <td>
                        <?php 
                        $currencySymbol = $dendaInfo['currency'] === 'USD' ? '$' : 'Rp';
                        $dendaFormatted = number_format($dendaInfo['denda_amount'], 0, ',', '.');
                        $totalFormatted = number_format($dendaInfo['total_package_price'], 0, ',', '.');
                        $refundFormatted = number_format($dendaInfo['refund_amount'], 0, ',', '.');
                        ?>
                        <div class="alert alert-info">
                            <strong>Total Biaya Paket:</strong> <?= $currencySymbol ?> <?= $totalFormatted ?><br>
                            <strong>Denda (<?= $dendaInfo['denda_percentage'] ?>%):</strong> <span class="text-danger"><?= $currencySymbol ?> <?= $dendaFormatted ?></span><br>
                            <strong>Dana Dikembalikan:</strong> <span class="text-success"><?= $currencySymbol ?> <?= $refundFormatted ?></span><br>
                            <small class="text-muted">Berdasarkan <?= $dendaInfo['months_until_departure'] ?> bulan sebelum keberangkatan</small>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            
            <?php if (isset($statusInfo['payment_submitted_at'])): ?>
                <tr>
                    <td><strong>Waktu Submit Pembayaran</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($statusInfo['payment_submitted_at'])) ?></td>
                </tr>
            <?php endif; ?>
            
            <?php if (isset($statusInfo['approved_at'])): ?>
                <tr>
                    <td><strong>Waktu Disetujui</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($statusInfo['approved_at'])) ?></td>
                </tr>
            <?php endif; ?>
            
            <?php if (isset($statusInfo['rejected_at'])): ?>
                <tr>
                    <td><strong>Waktu Ditolak</strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($statusInfo['rejected_at'])) ?></td>
                </tr>
            <?php endif; ?>
            
        <?php else: ?>
            <tr>
                <td><strong>Alasan Pembatalan</strong></td>
                <td><?= nl2br(htmlspecialchars($record['alasan'])) ?></td>
            </tr>
        <?php endif; ?>
        
        <tr>
            <td><strong>Waktu Pengajuan</strong></td>
            <td><?= $record['created_at'] ? date('d/m/Y H:i', strtotime($record['created_at'])) : 'N/A' ?></td>
        </tr>
        
        <?php if ($record['updated_at']): ?>
            <tr>
                <td><strong>Waktu Pembaruan</strong></td>
                <td><?= date('d/m/Y H:i', strtotime($record['updated_at'])) ?></td>
            </tr>
        <?php endif; ?>
    </table>
    
    <!-- File attachments section -->
    <?php if ($record['kwitansi_path'] || $record['proof_path']): ?>
        <h3>Dokumen Terlampir</h3>
        <div class="row">
            <?php if ($record['kwitansi_path']): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>Kwitansi Pembayaran</strong>
                        </div>
                        <div class="card-body text-center">
                            <div class="file-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $record['kwitansi_path'] ?>', 'cancellations', 'preview')">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $record['kwitansi_path'] ?>', 'cancellations', 'download')">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($record['proof_path']): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <strong>
                                <?= $record['cancellation_type'] === 'admin_initiated' ? 'Bukti Pembayaran Denda' : 'Bukti Pembayaran' ?>
                            </strong>
                        </div>
                        <div class="card-body text-center">
                            <div class="file-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $record['proof_path'] ?>', 'cancellations', 'preview')">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $record['proof_path'] ?>', 'cancellations', 'download')">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>