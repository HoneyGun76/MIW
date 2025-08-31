<?php
/**
 * Export Testing Tables to Excel
 * This script exports the Black Box Testing documentation tables to Excel format
 * 
 * IMPORTANT: This script is for documentation purposes and should NOT be deployed to Railway
 */

// Prevent this from running in production
if (getenv('RAILWAY_ENVIRONMENT') || isset($_ENV['RAILWAY_ENVIRONMENT'])) {
    die("This export script should not run in production Railway environment!");
}

// Include the XLSX library if available
$xlsxAvailable = false;
if (file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
    $xlsxAvailable = true;
} else {
    // Use simple CSV export as fallback
    $xlsxAvailable = false;
}

// Testing data extracted from pengujian.txt
$rencana_pengujian_data = [
    ['No.', 'Modul', 'Requirement yang diuji', 'Butir Uji', 'Jenis Pengujian'],
    [1, 'Konektivitas', 'Akses halaman utama website', 'Ketersediaan file index.php', 'Black Box'],
    [2, 'Konektivitas', 'Health check endpoint', 'Ketersediaan file health.php', 'Black Box'],
    [3, 'Konektivitas', 'Konfigurasi sistem', 'Ketersediaan file config.php', 'Black Box'],
    [4, 'Konektivitas', 'Deteksi environment Railway', 'Validasi environment variables', 'Black Box'],
    [5, 'Pendaftaran Jamaah', 'Form pendaftaran umroh/haji', 'Ketersediaan file form', 'Black Box'],
    [6, 'Pendaftaran Jamaah', 'Validasi input form', 'Validasi NIK, email, telepon, tanggal', 'Black Box'],
    [7, 'Pendaftaran Jamaah', 'Upload dokumen jamaah', 'Kemampuan upload file', 'Black Box'],
    [8, 'Manajemen Paket', 'Pengelolaan paket perjalanan', 'Ketersediaan file admin paket', 'Black Box'],
    [9, 'Manajemen Paket', 'Validasi data paket', 'Struktur data paket', 'Black Box'],
    [10, 'Pembatalan', 'Form pengajuan pembatalan', 'Ketersediaan file pembatalan', 'Black Box'],
    [11, 'Pembatalan', 'Verifikasi pembatalan', 'Proses verifikasi admin', 'Black Box'],
    [12, 'Administratif', 'Panel administrasi', 'Akses file admin dashboard', 'Black Box'],
    [13, 'Administratif', 'Export manifest jamaah', 'Fungsi export data', 'Black Box'],
    [14, 'Administratif', 'Manajemen roomlist', 'Pengaturan kamar jamaah', 'Black Box'],
    [15, 'File Upload', 'Handler upload file', 'Sistem upload dokumen', 'Black Box'],
    [16, 'File Upload', 'Permission file system', 'Hak akses file dan direktori', 'Black Box'],
    [17, 'File Upload', 'Validasi ukuran file', 'Batas maksimum ukuran file', 'Black Box'],
    [18, 'Keamanan', 'Sanitasi input', 'Pencegahan XSS dan SQL injection', 'Black Box'],
    [19, 'Keamanan', 'Autentikasi admin', 'Sistem login admin', 'Black Box'],
    [20, 'Keamanan', 'Validasi tipe file', 'Filter ekstensi file yang diizinkan', 'Black Box']
];

$hasil_pengujian_data = [
    ['No', 'Pengujian', 'Test Case', 'Hasil yang diharapkan', 'Kesimpulan'],
    [1, 'Akses Website', 'Pemeriksaan file index.php', 'File tersedia dan dapat diakses', 'Valid'],
    [2, 'Health Check Endpoint', 'Pemeriksaan file health.php', 'File health check tersedia', 'Valid'],
    [3, 'File Konfigurasi', 'Pemeriksaan file config.php', 'File konfigurasi tersedia', 'Valid'],
    [4, 'Deteksi Environment', 'Pemeriksaan variabel environment', 'Environment terdeteksi dengan benar', 'Valid'],
    [5, 'File Form Pendaftaran', 'Pemeriksaan file form umroh/haji', 'Semua file form tersedia', 'Valid'],
    [6, 'Validasi Input Form', 'Testing format NIK, email, telepon', 'Validasi berfungsi dengan benar', 'Valid'],
    [7, 'Kemampuan Upload File', 'Pemeriksaan direktori uploads', 'Direktori dapat diakses dan ditulis', 'Valid'],
    [8, 'File Manajemen Paket', 'Pemeriksaan file admin paket', 'File manajemen paket tersedia', 'Valid'],
    [9, 'Struktur Data Paket', 'Validasi data paket lengkap', 'Data paket sesuai standar', 'Valid'],
    [10, 'File Form Pembatalan', 'Pemeriksaan file pembatalan', 'File form pembatalan tersedia', 'Valid'],
    [11, 'Proses Verifikasi', 'Pemeriksaan file verify_cancellation', 'File verifikasi tersedia', 'Valid'],
    [12, 'Akses Panel Admin', 'Pemeriksaan file admin dashboard', 'Panel admin dapat diakses', 'Tidak Valid'],
    [13, 'Export Manifest', 'Pemeriksaan file export manifest', 'File export tersedia', 'Valid'],
    [14, 'Manajemen Kamar', 'Pemeriksaan file roomlist', 'File manajemen kamar tersedia', 'Valid'],
    [15, 'Handler Upload', 'Pemeriksaan handler upload file', 'Handler upload tersedia', 'Valid'],
    [16, 'Permission File', 'Testing buat/hapus file test', 'File dapat dibuat dan dihapus', 'Valid'],
    [17, 'Validasi Ukuran File', 'Simulasi file 2MB vs batas 5MB', 'File dalam batas diterima', 'Valid'],
    [18, 'Sanitasi Input', 'Testing input XSS/SQL injection', 'Input disanitasi dengan benar', 'Tidak Valid'],
    [19, 'Autentikasi Admin', 'Pemeriksaan file admin_auth', 'Sistem autentikasi tersedia', 'Tidak Valid'],
    [20, 'Validasi Tipe File', 'Testing ekstensi file beragam', 'Hanya file diizinkan diterima', 'Valid']
];

// Function to export to CSV (fallback)
function exportToCSV($data, $filename) {
    $file = fopen($filename, 'w');
    
    foreach ($data as $row) {
        fputcsv($file, $row);
    }
    
    fclose($file);
    return $filename;
}

// Function to create Excel using HTML table method (simple fallback)
function exportToHTMLExcel($rencana_data, $hasil_data, $filename) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tabel Pengujian MIW Travel</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; }
        .valid { background-color: #d4edda; }
        .tidak-valid { background-color: #f8d7da; }
    </style>
</head>
<body>
    <h1>TABEL PENGUJIAN SISTEM MIW TRAVEL</h1>
    
    <div class="title">Tabel 4.1 Rencana Pengujian Sistem MIW Travel</div>
    <table>
';

    // Add Rencana Pengujian table
    foreach ($rencana_data as $index => $row) {
        if ($index === 0) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<th>' . htmlspecialchars($cell) . '</th>';
            }
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
    }
    
    $html .= '</table>
    
    <div class="title">Tabel 4.2 Hasil Pengujian Sistem MIW Travel</div>
    <table>
';

    // Add Hasil Pengujian table
    foreach ($hasil_data as $index => $row) {
        if ($index === 0) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<th>' . htmlspecialchars($cell) . '</th>';
            }
            $html .= '</tr>';
        } else {
            $html .= '<tr>';
            foreach ($row as $cellIndex => $cell) {
                $class = '';
                if ($cellIndex === 4) { // Kesimpulan column
                    $class = ($cell === 'Valid') ? 'valid' : 'tidak-valid';
                }
                $html .= '<td class="' . $class . '">' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
        }
    }
    
    $html .= '</table>

    <div class="title">Ringkasan Hasil Pengujian</div>
    <table>
        <tr><th>Metrik</th><th>Nilai</th></tr>
        <tr><td>Total Pengujian</td><td>20</td></tr>
        <tr><td>Valid</td><td>17 (85%)</td></tr>
        <tr><td>Tidak Valid</td><td>3 (15%)</td></tr>
        <tr><td>Tingkat Keberhasilan</td><td>85%</td></tr>
        <tr><td>Tanggal Pengujian</td><td>5 Agustus 2025</td></tr>
        <tr><td>Environment</td><td>Local Simulation</td></tr>
    </table>
    
</body>
</html>';

    file_put_contents($filename, $html);
    return $filename;
}

echo "=== EXPORT TABEL PENGUJIAN KE EXCEL ===\n";
echo "Starting export process...\n\n";

$timestamp = date('Y-m-d_H-i-s');
$files_created = [];

try {
    // Export to CSV files
    echo "1. Exporting Rencana Pengujian to CSV...\n";
    $rencana_csv = "diagrams/tabel_rencana_pengujian_$timestamp.csv";
    exportToCSV($rencana_pengujian_data, $rencana_csv);
    $files_created[] = $rencana_csv;
    echo "   ✓ Created: $rencana_csv\n";

    echo "2. Exporting Hasil Pengujian to CSV...\n";
    $hasil_csv = "diagrams/tabel_hasil_pengujian_$timestamp.csv";
    exportToCSV($hasil_pengujian_data, $hasil_csv);
    $files_created[] = $hasil_csv;
    echo "   ✓ Created: $hasil_csv\n";

    // Export to HTML (can be opened in Excel)
    echo "3. Exporting Combined Tables to HTML/Excel format...\n";
    $html_excel = "diagrams/tabel_pengujian_combined_$timestamp.xls";
    exportToHTMLExcel($rencana_pengujian_data, $hasil_pengujian_data, $html_excel);
    $files_created[] = $html_excel;
    echo "   ✓ Created: $html_excel\n";

    // Try to use JavaScript XLSX method if available
    echo "4. Creating JavaScript-based Excel export...\n";
    $js_export_script = "diagrams/export_excel_$timestamp.html";
    
    $js_content = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export Tabel Pengujian ke Excel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        button { padding: 10px 20px; margin: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Export Tabel Pengujian MIW Travel ke Excel</h1>
    
    <div class="info">
        <strong>Instruksi:</strong><br>
        1. Klik tombol "Export ke Excel" di bawah<br>
        2. File Excel akan otomatis terdownload<br>
        3. File berisi 2 sheet: Rencana Pengujian dan Hasil Pengujian
    </div>
    
    <button onclick="exportToExcel()">Export ke Excel</button>
    
    <script>
    function exportToExcel() {
        // Create workbook
        const wb = XLSX.utils.book_new();
        
        // Rencana Pengujian data
        const rencanaData = ' . json_encode($rencana_pengujian_data) . ';
        
        // Hasil Pengujian data  
        const hasilData = ' . json_encode($hasil_pengujian_data) . ';
        
        // Create Rencana Pengujian worksheet
        const wsRencana = XLSX.utils.aoa_to_sheet(rencanaData);
        
        // Style header row for Rencana
        const headerCellsRencana = ["A1", "B1", "C1", "D1", "E1"];
        headerCellsRencana.forEach(cellRef => {
            if (!wsRencana[cellRef]) wsRencana[cellRef] = {v: "", t: "s"};
            wsRencana[cellRef].s = {
                font: {bold: true, sz: 11, color: {rgb: "000000"}},
                alignment: {horizontal: "center", vertical: "center"},
                fill: {patternType: "solid", fgColor: {rgb: "D9D9D9"}},
                border: {
                    top: {style: "medium", color: {rgb: "000000"}},
                    bottom: {style: "medium", color: {rgb: "000000"}},
                    left: {style: "medium", color: {rgb: "000000"}},
                    right: {style: "medium", color: {rgb: "000000"}}
                }
            };
        });
        
        // Set column widths for Rencana
        wsRencana["!cols"] = [
            {wch: 5},   // No
            {wch: 20},  // Modul
            {wch: 35},  // Requirement
            {wch: 40},  // Butir Uji
            {wch: 15}   // Jenis Pengujian
        ];
        
        // Create Hasil Pengujian worksheet
        const wsHasil = XLSX.utils.aoa_to_sheet(hasilData);
        
        // Style header row for Hasil
        const headerCellsHasil = ["A1", "B1", "C1", "D1", "E1"];
        headerCellsHasil.forEach(cellRef => {
            if (!wsHasil[cellRef]) wsHasil[cellRef] = {v: "", t: "s"};
            wsHasil[cellRef].s = {
                font: {bold: true, sz: 11, color: {rgb: "000000"}},
                alignment: {horizontal: "center", vertical: "center"},
                fill: {patternType: "solid", fgColor: {rgb: "D9D9D9"}},
                border: {
                    top: {style: "medium", color: {rgb: "000000"}},
                    bottom: {style: "medium", color: {rgb: "000000"}},
                    left: {style: "medium", color: {rgb: "000000"}},
                    right: {style: "medium", color: {rgb: "000000"}}
                }
            };
        });
        
        // Style Kesimpulan column (column E) based on value
        for (let i = 2; i <= hasilData.length; i++) {
            const cellRef = "E" + i;
            if (wsHasil[cellRef]) {
                const value = wsHasil[cellRef].v;
                if (value === "Valid") {
                    wsHasil[cellRef].s = {
                        font: {bold: true, color: {rgb: "006600"}},
                        fill: {patternType: "solid", fgColor: {rgb: "D4F6D4"}},
                        border: {
                            top: {style: "thin", color: {rgb: "000000"}},
                            bottom: {style: "thin", color: {rgb: "000000"}},
                            left: {style: "thin", color: {rgb: "000000"}},
                            right: {style: "thin", color: {rgb: "000000"}}
                        }
                    };
                } else if (value === "Tidak Valid") {
                    wsHasil[cellRef].s = {
                        font: {bold: true, color: {rgb: "CC0000"}},
                        fill: {patternType: "solid", fgColor: {rgb: "F6D4D4"}},
                        border: {
                            top: {style: "thin", color: {rgb: "000000"}},
                            bottom: {style: "thin", color: {rgb: "000000"}},
                            left: {style: "thin", color: {rgb: "000000"}},
                            right: {style: "thin", color: {rgb: "000000"}}
                        }
                    };
                }
            }
        }
        
        // Set column widths for Hasil
        wsHasil["!cols"] = [
            {wch: 5},   // No
            {wch: 25},  // Pengujian
            {wch: 35},  // Test Case
            {wch: 40},  // Hasil yang diharapkan
            {wch: 15}   // Kesimpulan
        ];
        
        // Add worksheets to workbook
        XLSX.utils.book_append_sheet(wb, wsRencana, "Rencana Pengujian");
        XLSX.utils.book_append_sheet(wb, wsHasil, "Hasil Pengujian");
        
        // Generate filename
        const timestamp = new Date().toISOString().slice(0,19).replace(/[:\-]/g, "").replace("T", "_");
        const filename = `Tabel_Pengujian_MIW_Travel_${timestamp}.xlsx`;
        
        // Save file
        XLSX.writeFile(wb, filename);
        
        alert(`Excel file exported successfully!
        
Filename: ${filename}
Sheets: Rencana Pengujian, Hasil Pengujian
Total Records: 20 test cases
Valid Tests: 17 (85%)
Invalid Tests: 3 (15%)

The file has been downloaded to your Downloads folder.`);
    }
    </script>
</body>
</html>';

    file_put_contents($js_export_script, $js_content);
    $files_created[] = $js_export_script;
    echo "   ✓ Created: $js_export_script\n";

    echo "\n=== EXPORT COMPLETED SUCCESSFULLY ===\n";
    echo "Files created:\n";
    foreach ($files_created as $file) {
        echo "  - $file\n";
    }
    
    echo "\nUsage Instructions:\n";
    echo "1. CSV files: Can be opened directly in Excel\n";
    echo "2. XLS file: HTML-based Excel format, can be opened in Excel\n";
    echo "3. HTML file: Open in browser and click Export button for full Excel functionality\n";
    
    echo "\nRecommended: Use the HTML file (export_excel_*.html) for best Excel export experience.\n";
    echo "It will generate a proper .xlsx file with formatting and multiple sheets.\n";

} catch (Exception $e) {
    echo "Error during export: " . $e->getMessage() . "\n";
}

echo "\n=== EXPORT PROCESS COMPLETED ===\n";
?>
