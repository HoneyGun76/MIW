<?php
/**
 * Script untuk mengekspor tabel pengujian dari pengujian.txt ke format Excel
 * MIW Travel - Railway Deployment Testing Export
 */

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

function exportPengujianToExcel() {
    // Baca file pengujian.txt
    $pengujianFile = __DIR__ . '/diagrams/pengujian.txt';
    if (!file_exists($pengujianFile)) {
        die("File pengujian.txt tidak ditemukan!\n");
    }
    
    $content = file_get_contents($pengujianFile);
    
    // Create new spreadsheet
    $spreadsheet = new Spreadsheet();
    
    // Data untuk berbagai sheet
    $sheets = [
        'Rencana Pengujian' => [
            'title' => 'Tabel 4.1 Rencana Pengujian Sistem MIW Travel',
            'headers' => ['No.', 'Modul', 'Requirement yang diuji', 'Butir Uji', 'Jenis Pengujian'],
            'data' => []
        ],
        'Hasil Keseluruhan' => [
            'title' => 'Tabel 4.2 Hasil Pengujian Sistem MIW Travel',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil yang diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Konektivitas' => [
            'title' => 'Tabel 4.3 Hasil Pengujian Konektivitas dan Deployment',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Pendaftaran Jamaah' => [
            'title' => 'Tabel 4.4 Hasil Pengujian Pendaftaran Jamaah',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Manajemen Paket' => [
            'title' => 'Tabel 4.5 Hasil Pengujian Manajemen Paket',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Pembatalan' => [
            'title' => 'Tabel 4.6 Hasil Pengujian Pembatalan',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Administratif' => [
            'title' => 'Tabel 4.7 Hasil Pengujian Administratif',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Upload File' => [
            'title' => 'Tabel 4.8 Hasil Pengujian Upload dan Pengelolaan File',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ],
        'Keamanan' => [
            'title' => 'Tabel 4.9 Hasil Pengujian Keamanan',
            'headers' => ['No', 'Pengujian', 'Test Case', 'Hasil Yang Diharapkan', 'Kesimpulan'],
            'data' => []
        ]
    ];
    
    // Parse data untuk Rencana Pengujian (Tabel 4.1)
    if (preg_match('/\*\*Tabel 4\.1 Rencana Pengujian Sistem MIW Travel\*\*(.*?)\n\n/s', $content, $matches)) {
        $tableContent = $matches[1];
        $lines = explode("\n", $tableContent);
        foreach ($lines as $line) {
            if (preg_match('/^\|\s*(\d+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|/', $line, $rowMatch)) {
                $sheets['Rencana Pengujian']['data'][] = [
                    trim($rowMatch[1]),
                    trim($rowMatch[2]),
                    trim($rowMatch[3]),
                    trim($rowMatch[4]),
                    trim($rowMatch[5])
                ];
            }
        }
    }
    
    // Parse data untuk Hasil Keseluruhan (Tabel 4.2)
    if (preg_match('/\*\*Tabel 4\.2 Hasil Pengujian Sistem MIW Travel\*\*(.*?)4\.5\.3/s', $content, $matches)) {
        $tableContent = $matches[1];
        $lines = explode("\n", $tableContent);
        foreach ($lines as $line) {
            if (preg_match('/^\|\s*(\d+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|/', $line, $rowMatch)) {
                $sheets['Hasil Keseluruhan']['data'][] = [
                    trim($rowMatch[1]),
                    trim($rowMatch[2]),
                    trim($rowMatch[3]),
                    trim($rowMatch[4]),
                    trim($rowMatch[5])
                ];
            }
        }
    }
    
    // Parse data untuk setiap kategori detail (Tabel 4.3 - 4.9)
    $categoryMappings = [
        'Tabel 4\.3' => 'Konektivitas',
        'Tabel 4\.4' => 'Pendaftaran Jamaah',
        'Tabel 4\.5' => 'Manajemen Paket',
        'Tabel 4\.6' => 'Pembatalan',
        'Tabel 4\.7' => 'Administratif',
        'Tabel 4\.8' => 'Upload File',
        'Tabel 4\.9' => 'Keamanan'
    ];
    
    foreach ($categoryMappings as $tablePattern => $sheetName) {
        if (preg_match('/\*\*' . $tablePattern . '.*?\*\*(.*?)\*\*Analisis:\*\*/s', $content, $matches)) {
            $tableContent = $matches[1];
            $lines = explode("\n", $tableContent);
            foreach ($lines as $line) {
                if (preg_match('/^\|\s*(\d+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|\s*([^|]+)\s*\|/', $line, $rowMatch)) {
                    $sheets[$sheetName]['data'][] = [
                        trim($rowMatch[1]),
                        trim($rowMatch[2]),
                        trim($rowMatch[3]),
                        trim($rowMatch[4]),
                        trim($rowMatch[5])
                    ];
                }
            }
        }
    }
    
    // Create sheets and populate data
    $sheetIndex = 0;
    foreach ($sheets as $sheetName => $sheetData) {
        if ($sheetIndex > 0) {
            $spreadsheet->createSheet();
        }
        $worksheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
        $worksheet->setTitle($sheetName);
        
        // Add title
        $worksheet->setCellValue('A1', $sheetData['title']);
        $worksheet->mergeCells('A1:E1');
        $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E6E6FA');
        
        // Add headers
        $row = 3;
        $col = 'A';
        foreach ($sheetData['headers'] as $header) {
            $worksheet->setCellValue($col . $row, $header);
            $worksheet->getStyle($col . $row)->getFont()->setBold(true);
            $worksheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
            $col++;
        }
        
        // Add data
        $row = 4;
        foreach ($sheetData['data'] as $rowData) {
            $col = 'A';
            foreach ($rowData as $cellData) {
                $worksheet->setCellValue($col . $row, $cellData);
                
                // Color coding for results
                if ($col == 'E' && in_array(trim($cellData), ['Valid', 'Tidak Valid'])) {
                    if (trim($cellData) == 'Valid') {
                        $worksheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
                    } else {
                        $worksheet->getStyle($col . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFB6C1');
                    }
                }
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $columnID) {
            $worksheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Add borders
        if (!empty($sheetData['data'])) {
            $lastRow = $row - 1;
            $worksheet->getStyle('A3:E' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        $sheetIndex++;
    }
    
    // Set active sheet to first sheet
    $spreadsheet->setActiveSheetIndex(0);
    
    // Create output directory if it doesn't exist
    $outputDir = __DIR__ . '/diagrams';
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }
    
    // Save Excel file
    $writer = new Xlsx($spreadsheet);
    $filename = $outputDir . '/Pengujian_MIW_Travel_' . date('Y-m-d_H-i-s') . '.xlsx';
    $writer->save($filename);
    
    echo "File Excel berhasil dibuat: " . $filename . "\n";
    echo "Total sheets: " . count($sheets) . "\n";
    
    // Summary
    $totalTests = count($sheets['Hasil Keseluruhan']['data']);
    $validTests = 0;
    foreach ($sheets['Hasil Keseluruhan']['data'] as $test) {
        if (trim($test[4]) == 'Valid') {
            $validTests++;
        }
    }
    
    echo "Total pengujian: " . $totalTests . "\n";
    echo "Pengujian valid: " . $validTests . "\n";
    echo "Tingkat keberhasilan: " . round(($validTests / $totalTests) * 100, 1) . "%\n";
    
    return $filename;
}

// Jalankan export
try {
    $filename = exportPengujianToExcel();
    echo "\nEkspor berhasil! File tersimpan di: " . basename($filename) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
