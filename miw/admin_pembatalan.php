<?php
require_once 'config.php';

// Set default sorting
$sort = $_GET['sort'] ?? 'kwitansi_path';
$order = $_GET['order'] ?? 'desc';
$validSortColumns = ['nik', 'nama', 'no_telp', 'email', 'kwitansi_path', 'proof_path'];
$sort = in_array($sort, $validSortColumns) ? $sort : 'kwitansi_path';
$order = $order === 'desc' ? 'desc' : 'asc';

// Pagination setup
$recordsPerPage = $_GET['per_page'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $recordsPerPage;

// Get total records count
$countStmt = $conn->query("SELECT COUNT(*) FROM data_pembatalan p JOIN data_jamaah j ON p.nik = j.nik");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get records with sorting and pagination
$query = "SELECT p.*, j.nama, j.pak_id 
          FROM data_pembatalan p
          JOIN data_jamaah j ON p.nik = j.nik 
          ORDER BY p.$sort $order 
          LIMIT :offset, :per_page";
$stmt = $conn->prepare($query);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':per_page', $recordsPerPage, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th class="sortable <?= $sort === 'no_telp' ? 'sorted' : '' ?>" 
                                onclick="sortTable('no_telp')">No. Telepon
                                <?= $sort === 'no_telp' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th class="sortable <?= $sort === 'email' ? 'sorted' : '' ?>" 
                                onclick="sortTable('email')">Email
                                <?= $sort === 'email' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th>Alasan</th>
                            <th class="sortable <?= $sort === 'kwitansi_path' ? 'sorted' : '' ?>" 
                                onclick="sortTable('kwitansi_path')">Kwitansi
                                <?= $sort === 'kwitansi_path' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
                            </th>
                            <th class="sortable <?= $sort === 'proof_path' ? 'sorted' : '' ?>" 
                                onclick="sortTable('proof_path')">Bukti
                                <?= $sort === 'proof_path' ? ($order === 'asc' ? '↑' : '↓') : '' ?>
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
                                    <td><?= htmlspecialchars($record['no_telp']) ?></td>
                                    <td><?= htmlspecialchars($record['email']) ?></td>
                                    <td><?= htmlspecialchars($record['alasan']) ?></td>
                                    <td>
                                        <?php if ($record['kwitansi_path']): ?>
                                            <div class="file-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $record['kwitansi_path'] ?>', 'cancellations', 'preview')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $record['kwitansi_path'] ?>', 'cancellations', 'download')">
                                                    <i class="bi bi-download"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($record['proof_path']): ?>
                                            <div class="file-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="handleFile('<?= $record['proof_path'] ?>', 'cancellations', 'preview')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="handleFile('<?= $record['proof_path'] ?>', 'cancellations', 'download')">
                                                    <i class="bi bi-download"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewDetails('<?= $record['nik'] ?>')"
                                                title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>
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
                    <button type="button" class="btn btn-primary" onclick="verifyCancellation()" id="verifyBtn">
                        <i class="bi bi-check-circle"></i> Verify Cancellation
                    </button>
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
        
        function viewDetails(nik) {
            currentNik = nik; // Store the NIK for verification
            fetch(`get_pembatalan_details.php?nik=${nik}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('detailContent').innerHTML = data;
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();
                });
        }
        
        function confirmDelete(nik) {
            if (confirm(`Apakah Anda yakin ingin menghapus data pembatalan untuk NIK ${nik}?`)) {
                window.location.href = `delete_pembatalan.php?nik=${nik}`;
            }
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

            if (!confirm('Apakah Anda yakin ingin memverifikasi pembatalan ini? Email konfirmasi akan dikirim ke jamaah dan data akan dihapus.')) {
                return;
            }

            const verifyBtn = document.getElementById('verifyBtn');
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

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
                    window.location.reload(); // Refresh the page to update the table
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan saat memproses pembatalan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembatalan');
            })
            .finally(() => {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="bi bi-check-circle"></i> Verify Cancellation';
            });
        }
    </script>

    <?php include 'includes/file_preview_modal.php'; ?>
    <script src="js/file_handlers.js"></script>
</body>
</html>