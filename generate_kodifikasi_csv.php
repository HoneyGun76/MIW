<?php
/**
 * CSV Generator for PERANCANGAN KODIFIKASI SISTEM
 * Creates CSV file with all kodifikasi tables from the MIW Travel system
 */

// Define all kodifikasi data
$kodifikasiData = [
    [
        'title' => 'Tabel 1.1 - Kodifikasi Status Jamaah',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'pending', 'Jamaah telah mendaftar namun belum melakukan verifikasi dokumen dan pembayaran'],
            ['2', 'verified', 'Jamaah telah diverifikasi dokumen dan data personalnya oleh admin'],
            ['3', 'paid', 'Jamaah telah melakukan pembayaran dan pembayaran telah dikonfirmasi'],
            ['4', 'departed', 'Jamaah telah berangkat melaksanakan ibadah haji/umroh sesuai jadwal']
        ]
    ],
    [
        'title' => 'Tabel 1.2 - Kodifikasi Status Paket',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'aktif', 'Paket tersedia untuk pendaftaran dan dapat dipilih oleh jamaah'],
            ['2', 'nonaktif', 'Paket tidak tersedia untuk pendaftaran (sudah penuh/expired)']
        ]
    ],
    [
        'title' => 'Tabel 1.3 - Kodifikasi Status Pembatalan',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'pending', 'Permintaan pembatalan sedang diproses dan menunggu persetujuan'],
            ['2', 'approved', 'Permintaan pembatalan disetujui dan proses refund dapat dilakukan'],
            ['3', 'rejected', 'Permintaan pembatalan ditolak berdasarkan kebijakan perusahaan']
        ]
    ],
    [
        'title' => 'Tabel 1.4 - Kodifikasi Status Pembayaran',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'pending', 'Bukti pembayaran telah diupload dan menunggu verifikasi admin'],
            ['2', 'verified', 'Pembayaran telah diverifikasi dan dikonfirmasi oleh admin'],
            ['3', 'rejected', 'Pembayaran ditolak karena bukti tidak valid atau tidak sesuai']
        ]
    ],
    [
        'title' => 'Tabel 1.5 - Kodifikasi Status Perkawinan',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'Belum Kawin', 'Jamaah belum menikah (status lajang)'],
            ['2', 'Kawin', 'Jamaah sudah menikah'],
            ['3', 'Cerai Hidup', 'Jamaah bercerai dan mantan pasangan masih hidup'],
            ['4', 'Cerai Mati', 'Jamaah bercerai karena pasangan meninggal dunia (janda/duda)']
        ]
    ],
    [
        'title' => 'Tabel 1.6 - Kodifikasi Status Kelengkapan Dokumen',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'Complete', 'Semua dokumen (100%) telah diupload dan lengkap'],
            ['2', 'Almost Complete', 'Dokumen hampir lengkap (80-99%) tinggal beberapa dokumen'],
            ['3', 'In Progress', 'Dokumen dalam proses pengumpulan (50-79%) sebagian telah diupload'],
            ['4', 'Incomplete', 'Dokumen belum lengkap (<50%) masih banyak yang harus diupload']
        ]
    ],
    [
        'title' => 'Tabel 1.7 - Kodifikasi Jenis Kelamin',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'L', 'Laki-laki'],
            ['2', 'P', 'Perempuan']
        ]
    ],
    [
        'title' => 'Tabel 1.8 - Kodifikasi Jenis Perjalanan',
        'data' => [
            ['No', 'Nilai Kodifikasi', 'Keterangan'],
            ['1', 'umroh', 'Perjalanan ibadah umroh'],
            ['2', 'haji', 'Perjalanan ibadah haji']
        ]
    ]
];

// Generate filename with timestamp
$filename = 'Perancangan_Kodifikasi_Sistem_MIW_' . date('Y-m-d_H-i-s') . '.csv';
$filepath = __DIR__ . '/diagrams/' . $filename;

// Open file for writing
$file = fopen($filepath, 'w');

if (!$file) {
    die("Error: Cannot create CSV file at {$filepath}\n");
}

// Add UTF-8 BOM for proper Excel compatibility
fwrite($file, "\xEF\xBB\xBF");

// Add main title
fputcsv($file, ['PERANCANGAN KODIFIKASI SISTEM - MIW TRAVEL']);
fputcsv($file, ['Sistem Manajemen Perjalanan Haji dan Umroh']);
fputcsv($file, []); // Empty row

// Add each kodifikasi table
foreach ($kodifikasiData as $table) {
    // Add table title
    fputcsv($file, [$table['title']]);
    fputcsv($file, []); // Empty row
    
    // Add data rows
    foreach ($table['data'] as $row) {
        fputcsv($file, $row);
    }
    
    // Add spacing between tables
    fputcsv($file, []); // Empty row
    fputcsv($file, []); // Empty row
}

// Add creation info
fputcsv($file, ['Generated on: ' . date('Y-m-d H:i:s')]);
fputcsv($file, ['MIW Travel Management System']);

// Close file
fclose($file);

echo "CSV file created successfully: {$filename}\n";
echo "File location: {$filepath}\n";
echo "File size: " . number_format(filesize($filepath) / 1024, 2) . " KB\n";
echo "\nCSV generation completed!\n";

?>
