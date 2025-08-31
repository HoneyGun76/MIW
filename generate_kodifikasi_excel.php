<?php
/**
 * Excel Generator for PERANCANGAN KODIFIKASI SISTEM
 * Creates Excel file with all kodifikasi tables from the MIW Travel system
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('MIW Travel System')
    ->setTitle('Perancangan Kodifikasi Sistem')
    ->setSubject('System Codification Design')
    ->setDescription('Kodifikasi status untuk sistem manajemen MIW Travel')
    ->setKeywords('kodifikasi status jamaah paket pembayaran')
    ->setCategory('Documentation');

// Set sheet title
$sheet->setTitle('Kodifikasi Sistem');

// Define all kodifikasi data
$kodifikasiData = [
    [
        'title' => 'Tabel 1.1 Perancangan Kodifikasi Pada Tabel Jamaah',
        'subtitle' => 'Kodifikasi Status Jamaah',
        'data' => [
            ['1', 'pending', 'Jamaah telah mendaftar namun belum melakukan verifikasi dokumen dan pembayaran'],
            ['2', 'verified', 'Jamaah telah diverifikasi dokumen dan data personalnya oleh admin'],
            ['3', 'paid', 'Jamaah telah melakukan pembayaran dan pembayaran telah dikonfirmasi'],
            ['4', 'departed', 'Jamaah telah berangkat melaksanakan ibadah haji/umroh sesuai jadwal']
        ]
    ],
    [
        'title' => 'Tabel 1.2 Perancangan Kodifikasi Pada Tabel Paket',
        'subtitle' => 'Kodifikasi Status Paket',
        'data' => [
            ['1', 'aktif', 'Paket tersedia untuk pendaftaran dan dapat dipilih oleh jamaah'],
            ['2', 'nonaktif', 'Paket tidak tersedia untuk pendaftaran (sudah penuh/expired)']
        ]
    ],
    [
        'title' => 'Tabel 1.3 Perancangan Kodifikasi Pada Tabel Pembatalan',
        'subtitle' => 'Kodifikasi Status Pembatalan',
        'data' => [
            ['1', 'pending', 'Permintaan pembatalan sedang diproses dan menunggu persetujuan'],
            ['2', 'approved', 'Permintaan pembatalan disetujui dan proses refund dapat dilakukan'],
            ['3', 'rejected', 'Permintaan pembatalan ditolak berdasarkan kebijakan perusahaan']
        ]
    ],
    [
        'title' => 'Tabel 1.4 Perancangan Kodifikasi Pada Tabel Payment',
        'subtitle' => 'Kodifikasi Status Pembayaran',
        'data' => [
            ['1', 'pending', 'Bukti pembayaran telah diupload dan menunggu verifikasi admin'],
            ['2', 'verified', 'Pembayaran telah diverifikasi dan dikonfirmasi oleh admin'],
            ['3', 'rejected', 'Pembayaran ditolak karena bukti tidak valid atau tidak sesuai']
        ]
    ],
    [
        'title' => 'Tabel 1.5 Perancangan Kodifikasi Pada Tabel Status Perkawinan',
        'subtitle' => 'Kodifikasi Status Perkawinan',
        'data' => [
            ['1', 'Belum Kawin', 'Jamaah belum menikah (status lajang)'],
            ['2', 'Kawin', 'Jamaah sudah menikah'],
            ['3', 'Cerai Hidup', 'Jamaah bercerai dan mantan pasangan masih hidup'],
            ['4', 'Cerai Mati', 'Jamaah bercerai karena pasangan meninggal dunia (janda/duda)']
        ]
    ],
    [
        'title' => 'Tabel 1.6 Perancangan Kodifikasi Pada Status Kelengkapan Dokumen',
        'subtitle' => 'Kodifikasi Status Kelengkapan Dokumen',
        'data' => [
            ['1', 'Complete', 'Semua dokumen (100%) telah diupload dan lengkap'],
            ['2', 'Almost Complete', 'Dokumen hampir lengkap (80-99%) tinggal beberapa dokumen'],
            ['3', 'In Progress', 'Dokumen dalam proses pengumpulan (50-79%) sebagian telah diupload'],
            ['4', 'Incomplete', 'Dokumen belum lengkap (<50%) masih banyak yang harus diupload']
        ]
    ],
    [
        'title' => 'Tabel 1.7 Perancangan Kodifikasi Pada Jenis Kelamin',
        'subtitle' => 'Kodifikasi Jenis Kelamin',
        'data' => [
            ['1', 'L', 'Laki-laki'],
            ['2', 'P', 'Perempuan']
        ]
    ],
    [
        'title' => 'Tabel 1.8 Perancangan Kodifikasi Pada Jenis Perjalanan',
        'subtitle' => 'Kodifikasi Jenis Perjalanan',
        'data' => [
            ['1', 'umroh', 'Perjalanan ibadah umroh'],
            ['2', 'haji', 'Perjalanan ibadah haji']
        ]
    ]
];

// Style definitions
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '366092']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THICK,
            'color' => ['rgb' => '000000']
        ]
    ]
];

$titleStyle = [
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => '000000']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E6F2FF']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['rgb' => '000000']
        ]
    ]
];

$dataStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

$numberStyle = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
];

// Add main title
$sheet->setCellValue('A1', 'PERANCANGAN KODIFIKASI SISTEM - MIW TRAVEL');
$sheet->mergeCells('A1:C1');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '1F4E79']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

$sheet->setCellValue('A2', 'Sistem Manajemen Perjalanan Haji dan Umroh');
$sheet->mergeCells('A2:C2');
$sheet->getStyle('A2')->applyFromArray([
    'font' => [
        'italic' => true,
        'size' => 10,
        'color' => ['rgb' => '666666']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
]);

// Set row height for title
$sheet->getRowDimension(1)->setRowHeight(25);
$sheet->getRowDimension(2)->setRowHeight(20);

$currentRow = 4;

// Add each kodifikasi table
foreach ($kodifikasiData as $table) {
    // Add table title
    $sheet->setCellValue("A{$currentRow}", $table['title']);
    $sheet->mergeCells("A{$currentRow}:C{$currentRow}");
    $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray($titleStyle);
    $sheet->getRowDimension($currentRow)->setRowHeight(20);
    $currentRow++;
    
    // Add table header
    $sheet->setCellValue("A{$currentRow}", 'No');
    $sheet->setCellValue("B{$currentRow}", 'Nilai Kodifikasi');
    $sheet->setCellValue("C{$currentRow}", 'Keterangan');
    $sheet->getStyle("A{$currentRow}:C{$currentRow}")->applyFromArray($headerStyle);
    $sheet->getRowDimension($currentRow)->setRowHeight(18);
    $currentRow++;
    
    // Add data rows
    foreach ($table['data'] as $row) {
        $sheet->setCellValue("A{$currentRow}", $row[0]);
        $sheet->setCellValue("B{$currentRow}", $row[1]);
        $sheet->setCellValue("C{$currentRow}", $row[2]);
        
        $sheet->getStyle("A{$currentRow}")->applyFromArray($numberStyle);
        $sheet->getStyle("B{$currentRow}:C{$currentRow}")->applyFromArray($dataStyle);
        $sheet->getRowDimension($currentRow)->setRowHeight(30);
        $currentRow++;
    }
    
    // Add spacing between tables
    $currentRow += 2;
}

// Set column widths
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(60);

// Auto-fit row heights for better text visibility
for ($i = 1; $i <= $currentRow; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
}

// Add creation info at the bottom
$infoRow = $currentRow + 2;
$sheet->setCellValue("A{$infoRow}", 'Generated on: ' . date('Y-m-d H:i:s'));
$sheet->setCellValue("A" . ($infoRow + 1), 'MIW Travel Management System');
$sheet->getStyle("A{$infoRow}:A" . ($infoRow + 1))->applyFromArray([
    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '888888']]
]);

// Set page setup for printing
$sheet->getPageSetup()
    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
    ->setFitToPage(true)
    ->setFitToWidth(1)
    ->setFitToHeight(0);

// Set print margins
$sheet->getPageMargins()
    ->setTop(0.75)
    ->setRight(0.25)
    ->setLeft(0.25)
    ->setBottom(0.75)
    ->setHeader(0.3)
    ->setFooter(0.3);

// Generate filename with timestamp
$filename = 'Perancangan_Kodifikasi_Sistem_MIW_' . date('Y-m-d_H-i-s') . '.xlsx';
$filepath = __DIR__ . '/diagrams/' . $filename;

// Create writer and save file
$writer = new Xlsx($spreadsheet);

try {
    $writer->save($filepath);
    echo "Excel file created successfully: {$filename}\n";
    echo "File location: {$filepath}\n";
    echo "File size: " . number_format(filesize($filepath) / 1024, 2) . " KB\n";
} catch (Exception $e) {
    echo "Error creating Excel file: " . $e->getMessage() . "\n";
}

// Clean up memory
$spreadsheet->disconnectWorksheets();
unset($spreadsheet);

echo "\nExcel generation completed!\n";
?>
