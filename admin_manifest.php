<?php
require_once 'config.php';

// Get all packages
$stmt = $conn->prepare("SELECT * FROM data_paket ORDER BY tanggal_keberangkatan DESC");
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manifest Export - MIW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 admin-header d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-file-earmark-excel"></i> Manifest Export</h2>
            </div>
        </div>

        <?php include 'admin_nav.php'; ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Export Package Data</h5>
                        <p class="text-muted small">Export manifest data with roomlist and kelengkapan in a single Excel file</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="packageTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Jenis Paket</th>
                                        <th>Program</th>
                                        <th>Tanggal Keberangkatan</th>
                                        <th>Hotel Medinah</th>
                                        <th>Hotel Makkah</th>
                                        <th>Jumlah Jamaah</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packages as $package): 
                                        // Count jamaah for this package
                                        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM data_jamaah WHERE pak_id = ?");
                                        $stmt->execute([$package['pak_id']]);
                                        $jamaahCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($package['pak_id']) ?></td>
                                        <td><?= htmlspecialchars($package['jenis_paket']) ?></td>
                                        <td><?= htmlspecialchars($package['program_pilihan']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($package['tanggal_keberangkatan'])) ?></td>
                                        <td><?= htmlspecialchars($package['hotel_medinah']) ?></td>
                                        <td><?= htmlspecialchars($package['hotel_makkah']) ?></td>
                                        <td><?= $jamaahCount ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary export-manifest" 
                                                        data-pakid="<?= $package['pak_id'] ?>"
                                                        data-bs-toggle="tooltip"
                                                        title="Export Manifest with Roomlist and Kelengkapan">
                                                    <i class="bi bi-file-earmark-excel"></i> Export Manifest
                                                </button>
                                            </div>
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

        <!-- Export Status Modal -->
        <div class="modal fade" id="exportStatusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="exportStatus" class="alert alert-info">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Processing export...
                        </div>
                        <div id="downloadLink" class="d-none text-center">
                            <p>Export completed successfully!</p>
                            <a href="#" class="btn btn-success" id="downloadBtn">
                                <i class="bi bi-download"></i> Download File
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="manifest_scripts.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

            // Initialize DataTable
            $('#packageTable').DataTable({
                responsive: true,
                order: [[3, 'asc']], // Sort by departure date
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang ditampilkan",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Note: Export handlers are managed by manifest_scripts.js
        });
    </script>
</body>
</html>