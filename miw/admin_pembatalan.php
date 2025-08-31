<?php
require_once 'config.php';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $nik = $_POST['nik'] ?? '';
        $pembatalan_id = $_POST['pembatalan_id'] ?? '';
        
        if ($action === 'approve_denda' && $nik && $pembatalan_id) {
            try {
                $pdo->beginTransaction();
                
                // Get pembatalan and jamaah data
                $stmt = $pdo->prepare("
                    SELECT p.*, j.*, pk.program_pilihan 
                    FROM data_pembatalan p 
                    JOIN data_jamaah j ON p.nik = j.nik 
                    LEFT JOIN data_paket pk ON j.pak_id = pk.pak_id 
                    WHERE p.id = ? AND p.nik = ?
                ");
                $stmt->execute([$pembatalan_id, $nik]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($data && strpos($data['alasan'], 'ADMIN_INITIATED|') === 0) {
                    // Parse status info
                    $statusJson = substr($data['alasan'], 16);
                    $statusInfo = json_decode($statusJson, true);
                    $statusInfo['status'] = 'approved';
                    $statusInfo['approved_at'] = date('Y-m-d H:i:s');
                    $statusInfo['approved_by'] = 'Admin';
                    
                    // Update pembatalan status
                    $newAlasan = "ADMIN_INITIATED|" . json_encode($statusInfo);
                    $stmt = $pdo->prepare("UPDATE data_pembatalan SET alasan = ? WHERE id = ?");
                    $stmt->execute([$newAlasan, $pembatalan_id]);
                    
                    // Send completion email
                    require_once 'email_functions.php';
                    $dendaInfo = $statusInfo['calculation_details'];
                    sendPembatalanCompletion($data, $dendaInfo, true);
                    
                    $pdo->commit();
                    $_SESSION['message'] = 'Pembatalan berhasil disetujui dan email konfirmasi telah dikirim';
                } else {
                    throw new Exception('Data pembatalan tidak valid');
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = 'Gagal menyetujui pembatalan: ' . $e->getMessage();
            }
        } elseif ($action === 'reject_denda' && $nik && $pembatalan_id) {
            try {
                $pdo->beginTransaction();
                
                // Get pembatalan and jamaah data
                $stmt = $pdo->prepare("
                    SELECT p.*, j.*, pk.program_pilihan 
                    FROM data_pembatalan p 
                    JOIN data_jamaah j ON p.nik = j.nik 
                    LEFT JOIN data_paket pk ON j.pak_id = pk.pak_id 
                    WHERE p.id = ? AND p.nik = ?
                ");
                $stmt->execute([$pembatalan_id, $nik]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($data && strpos($data['alasan'], 'ADMIN_INITIATED|') === 0) {
                    // Parse status info
                    $statusJson = substr($data['alasan'], 16);
                    $statusInfo = json_decode($statusJson, true);
                    $statusInfo['status'] = 'rejected';
                    $statusInfo['rejected_at'] = date('Y-m-d H:i:s');
                    $statusInfo['rejected_by'] = 'Admin';
                    
                    // Update pembatalan status
                    $newAlasan = "ADMIN_INITIATED|" . json_encode($statusInfo);
                    $stmt = $pdo->prepare("UPDATE data_pembatalan SET alasan = ? WHERE id = ?");
                    $stmt->execute([$newAlasan, $pembatalan_id]);
                    
                    // Send rejection email
                    require_once 'email_functions.php';
                    $dendaInfo = $statusInfo['calculation_details'];
                    sendPembatalanCompletion($data, $dendaInfo, false);
                    
                    $pdo->commit();
                    $_SESSION['message'] = 'Pembatalan ditolak dan email notifikasi telah dikirim';
                } else {
                    throw new Exception('Data pembatalan tidak valid');
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = 'Gagal menolak pembatalan: ' . $e->getMessage();
            }
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Set default sorting
$sort = $_GET['sort'] ?? 'created_at';
$order = $_GET['order'] ?? 'desc';
$validSortColumns = ['nik', 'nama', 'no_telp', 'email', 'created_at', 'alasan'];
$sort = in_array($sort, $validSortColumns) ? $sort : 'created_at';
$order = $order === 'desc' ? 'desc' : 'asc';

// Pagination setup
$recordsPerPage = $_GET['per_page'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $recordsPerPage;

// Get total records count
$countStmt = $pdo->query("SELECT COUNT(*) FROM data_pembatalan p JOIN data_jamaah j ON p.nik = j.nik");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get records with sorting and pagination
$query = "SELECT p.*, j.nama, j.pak_id, pk.program_pilihan,
                 CASE 
                     WHEN p.alasan LIKE 'ADMIN_INITIATED|%' THEN 'admin_initiated'
                     ELSE 'regular'
                 END as cancellation_type
          FROM data_pembatalan p
          JOIN data_jamaah j ON p.nik = j.nik 
          LEFT JOIN data_paket pk ON j.pak_id = pk.pak_id
          ORDER BY p.$sort $order 
          LIMIT :offset, :per_page";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $recordsPerPage, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Parse admin-initiated status for each record
foreach ($records as &$record) {
    if ($record['cancellation_type'] === 'admin_initiated') {
        $statusJson = substr($record['alasan'], 16);
        $statusInfo = json_decode($statusJson, true);
        $record['admin_status'] = $statusInfo['status'] ?? 'unknown';
        $record['denda_amount'] = $statusInfo['denda_amount'] ?? 0;
        $record['currency'] = $statusInfo['currency'] ?? 'IDR';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Pembatalan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
    <title>Admin - Data Pembatalan | MIW Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="admin-header">
            <h2><i class="bi bi-x-circle-fill"></i> Data Pembatalan</h2>
        </div>

        <?php include 'admin_nav.php'; ?>

        <!-- Session Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="records-per-page">
                    <label for="per_page">Tampilkan:</label>
                    <select id="per_page" class="form-select form-select-sm" onchange="updateRecordsPerPage(this.value)">
                        <option value="10" <?= $recordsPerPage == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $recordsPerPage == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $recordsPerPage == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $recordsPerPage == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
            </div>

            <div class="scrollable-table" style="--records-per-page: <?= $recordsPerPage ?>">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="sortable <?= $sort === 'nik' ? 'sorted' : '' ?>" 
                                onclick="sortTable('nik')">NIK
                                <?= $sort === 'nik' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th class="sortable <?= $sort === 'nama' ? 'sorted' : '' ?>" 
                                onclick="sortTable('nama')">Nama
                                <?= $sort === 'nama' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th>Program</th>
                            <th>Jenis Pembatalan</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th class="sortable <?= $sort === 'created_at' ? 'sorted' : '' ?>" 
                                onclick="sortTable('created_at')">Tanggal
                                <?= $sort === 'created_at' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pembatalan</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['nik']) ?></td>
                                    <td><?= htmlspecialchars($record['nama']) ?></td>
                                    <td><?= htmlspecialchars($record['program_pilihan'] ?? 'N/A') ?></td>
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
                                    <td>
                                        <?php if ($record['cancellation_type'] === 'admin_initiated'): ?>
                                            <?php
                                            $statusClass = match($record['admin_status']) {
                                                'pending_payment' => 'bg-warning text-dark',
                                                'payment_submitted' => 'bg-primary',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            $statusText = match($record['admin_status']) {
                                                'pending_payment' => 'Menunggu Pembayaran',
                                                'payment_submitted' => 'Pembayaran Disubmit',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                default => 'Unknown'
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pending Review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($record['cancellation_type'] === 'admin_initiated' && isset($record['denda_amount'])): ?>
                                            <?php 
                                            $currencySymbol = $record['currency'] === 'USD' ? '$' : 'Rp';
                                            $dendaFormatted = number_format($record['denda_amount'], 0, ',', '.');
                                            ?>
                                            <span class="text-danger fw-bold"><?= $currencySymbol ?> <?= $dendaFormatted ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($record['created_at'])) ?></td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewDetails('<?= $record['nik'] ?>', '<?= $record['id'] ?>')"
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if ($record['cancellation_type'] === 'admin_initiated' && $record['admin_status'] === 'payment_submitted'): ?>
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="approveDenda('<?= $record['nik'] ?>', '<?= $record['id'] ?>')"
                                                    title="Setujui Pembatalan">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="rejectDenda('<?= $record['nik'] ?>', '<?= $record['id'] ?>')"
                                                    title="Tolak Pembatalan">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?= $record['nik'] ?>')"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&per_page=<?= $recordsPerPage ?>">Sebelumnya</a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&per_page=<?= $recordsPerPage ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&per_page=<?= $recordsPerPage ?>">Selanjutnya</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pembatalan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <div id="modalActions">
                        <!-- Actions will be populated based on cancellation type and status -->
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        function sortTable(column) {
            const currentSort = '<?= $sort ?>';
            const currentOrder = '<?= $order ?>';
            let newOrder = 'asc';
            
            if (column === currentSort) {
                newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            }
            
            window.location.href = `?sort=${column}&order=${newOrder}&per_page=<?= $recordsPerPage ?>`;
        }
        
        function updateRecordsPerPage(value) {
            window.location.href = `?sort=<?= $sort ?>&order=<?= $order ?>&per_page=${value}`;
        }
        
        let currentNik = '';
        let currentPembatalanId = '';
        
        function viewDetails(nik, pembatalanId = '') {
            currentNik = nik;
            currentPembatalanId = pembatalanId;
            
            fetch(`get_pembatalan_details.php?nik=${nik}&pembatalan_id=${pembatalanId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = data;
                    
                    // Update modal actions based on the cancellation type and status
                    updateModalActions();
                    
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();
                });
        }
        
        function updateModalActions() {
            const modalActions = document.getElementById('modalActions');
            
            // Get cancellation type and status from the detail content
            const detailContent = document.getElementById('detailContent');
            const isAdminInitiated = detailContent.innerHTML.includes('Admin Initiated');
            const isPaymentSubmitted = detailContent.innerHTML.includes('payment_submitted');
            
            let actionsHtml = '';
            
            if (isAdminInitiated && isPaymentSubmitted) {
                actionsHtml = `
                    <button type="button" class="btn btn-success me-2" onclick="approveDenda()">
                        <i class="bi bi-check-circle"></i> Setujui Pembatalan
                    </button>
                    <button type="button" class="btn btn-danger me-2" onclick="rejectDenda()">
                        <i class="bi bi-x-circle"></i> Tolak Pembatalan
                    </button>
                `;
            } else if (!isAdminInitiated) {
                actionsHtml = `
                    <button type="button" class="btn btn-primary me-2" onclick="verifyCancellation()">
                        <i class="bi bi-check-circle"></i> Verifikasi Pembatalan
                    </button>
                `;
            }
            
            modalActions.innerHTML = actionsHtml;
        }
        
        function confirmDelete(nik) {
            if (confirm(`Apakah Anda yakin ingin menghapus data pembatalan untuk NIK ${nik}?`)) {
                window.location.href = `delete_pembatalan.php?nik=${nik}`;
            }
        }
        
        function approveDenda(nik = null, pembatalanId = null) {
            const targetNik = nik || currentNik;
            const targetId = pembatalanId || currentPembatalanId;
            
            if (!targetNik || !targetId) {
                alert('Error: Data tidak valid');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menyetujui pembatalan ini? Dana akan dikembalikan ke jamaah sesuai perhitungan denda.')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="approve_denda">
                <input type="hidden" name="nik" value="${targetNik}">
                <input type="hidden" name="pembatalan_id" value="${targetId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function rejectDenda(nik = null, pembatalanId = null) {
            const targetNik = nik || currentNik;
            const targetId = pembatalanId || currentPembatalanId;
            
            if (!targetNik || !targetId) {
                alert('Error: Data tidak valid');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menolak pembatalan ini? Jamaah akan diberitahu bahwa pembayaran denda ditolak.')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="reject_denda">
                <input type="hidden" name="nik" value="${targetNik}">
                <input type="hidden" name="pembatalan_id" value="${targetId}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        function exportToExcel() {
            // Create a new workbook
            const wb = XLSX.utils.book_new();
            
            // Get table data
            const table = document.querySelector('table');
            const ws = XLSX.utils.table_to_sheet(table);
            
            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, "Data Pembatalan");
            
            // Export the workbook
            XLSX.writeFile(wb, `Data_Pembatalan_MIW_${new Date().toISOString().slice(0,10)}.xlsx`);
        }

        function verifyCancellation() {
            if (!currentNik) {
                alert('Error: NIK tidak valid');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin memverifikasi pembatalan ini? Email konfirmasi akan dikirim ke jamaah dan data akan diproses.')) {
                return;
            }

            const verifyBtn = document.querySelector('[onclick="verifyCancellation()"]');
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            }

            fetch('verify_cancellation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `nik=${encodeURIComponent(currentNik)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pembatalan berhasil diverifikasi dan email konfirmasi telah dikirim');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan saat memproses pembatalan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembatalan');
            })
            .finally(() => {
                if (verifyBtn) {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Verifikasi Pembatalan';
                }
            });
        }

        // Display session messages
        // Messages are now handled by the PHP session display above
    </script>

    <?php include 'includes/file_preview_modal.php'; ?>
    <script src="js/file_handlers.js"></script>
</body>
</html>