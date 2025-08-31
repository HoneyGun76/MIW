<?php
require_once 'config.php'; // Database connection

// Get all packages
try {
    $stmt = $conn->prepare("SELECT * FROM data_paket ORDER BY jenis_paket, program_pilihan");
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching packages: " . $e->getMessage());
    $packages = [];
}

// Count packages by type
$umrohCount = array_reduce($packages, function($carry, $item) {
    return $carry + ($item['jenis_paket'] === 'Umroh' ? 1 : 0);
}, 0);

$hajiCount = array_reduce($packages, function($carry, $item) {
    return $carry + ($item['jenis_paket'] === 'Haji' ? 1 : 0);
}, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roomlist Management - MIW</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 admin-header d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-building"></i> Roomlist Management</h2>
            </div>
        </div>

        <?php include 'admin_nav.php'; ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body p-0">
                        <ul class="nav nav-tabs nav-fill" id="roomlistTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active d-flex align-items-center justify-content-center" 
                                        id="umroh-manifest-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#umroh-manifest" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-people-fill me-2"></i>
                                    Roomlist Umroh
                                    <span class="badge bg-primary ms-2"><?= $umrohCount ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link d-flex align-items-center justify-content-center" 
                                        id="haji-manifest-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#haji-manifest" 
                                        type="button" 
                                        role="tab">
                                    <i class="bi bi-building me-2"></i>
                                    Roomlist Haji
                                    <span class="badge bg-success ms-2"><?= $hajiCount ?></span>
                                </button>
                            </li>

                        </ul>
                    </div>
                </div>
                
                <div class="tab-content roomlist-tab-content" id="roomlistTabsContent">
                    <div class="tab-pane fade show active" id="umroh-manifest" role="tabpanel">
                        <div class="roomlist-container">
                            <?php include 'manifest_umroh.php'; ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="haji-manifest" role="tabpanel">
                        <div class="roomlist-container">
                            <?php include 'manifest_haji.php'; ?>
                        </div>
                    </div>

                </div>

                <style>
                    .manifest-container {
                        padding: 20px;
                    }
                    
                    .package-header {
                        background-color: #f8f9fa;
                        border-left: 5px solid #f6b127;
                        padding: 15px;
                        margin-bottom: 20px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        border-radius: 4px;
                    }

                    .package-header + .room-data {
                        margin-top: 20px;
                    }

                    .room-data {
                        margin-bottom: 30px;
                        background-color: #fff;
                        padding: 15px;
                        border-radius: 4px;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                    }

                    .package-table {
                        margin-bottom: 40px;
                        border: 1px solid #dee2e6;
                        border-radius: 4px;
                        overflow: hidden;
                    }

                    .package-table thead th {
                        background-color: #343a40;
                        color: white;
                        border-bottom: 2px solid #dee2e6;
                        padding: 12px 8px;
                    }

                    .manifest-divider {
                        height: 2px;
                        background: linear-gradient(to right, transparent, #dee2e6, transparent);
                        margin: 40px 0;
                    }

                    .package-header:not(:first-child) {
                        margin-top: 40px;
                    }

                    .room-list {
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                    }

                    .room-list p {
                        margin-bottom: 0;
                        padding: 8px;
                        background-color: #f8f9fa;
                        border-radius: 4px;
                    }

                    .badge {
                        font-size: 0.85em;
                        padding: 5px 10px;
                    }
                </style>
            </div>
        </div>
    </div>

    <!-- jQuery must be loaded first -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap Bundle includes Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables with Bootstrap 5 -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom scripts for roomlist functionality -->
    <script src="roomlist_scripts.js"></script>
    
    <script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Handle tab changes
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize first tab
        var firstTab = document.querySelector('#roomlistTabs .nav-link');
        var firstPane = document.querySelector('#roomlistTabsContent .tab-pane');
        if (firstTab && firstPane) {
            firstTab.classList.add('active');
            firstPane.classList.add('show', 'active');
        }

        // Reinitialize DataTables when switching tabs
        const tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const newTabPane = document.querySelector(event.target.getAttribute('data-bs-target'));
                if (newTabPane) {
                    const tables = newTabPane.querySelectorAll('.package-table');
                    tables.forEach(table => {
                        if ($.fn.DataTable.isDataTable(table)) {
                            $(table).DataTable().destroy();
                        }
                        $(table).DataTable({
                            destroy: true,
                            responsive: true,
                            columnDefs: [
                                {
                                    targets: [1],
                                    width: '120px'
                                }
                            ],
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
                    });
                }
            });
        });
    });</script>
</body>
</html>
