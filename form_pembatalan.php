<?php
require_once "config.php";

// Check if this is payment mode (admin-initiated cancellation)
$paymentMode = isset($_GET['mode']) && $_GET['mode'] === 'payment';
$pembatalan_id = isset($_GET['pembatalan_id']) ? $_GET['pembatalan_id'] : null;
$nik = isset($_GET['nik']) ? $_GET['nik'] : null;

// Get pembatalan data if in payment mode
$pembatalanData = null;
$jamaahData = null;
$dendaInfo = null;

if ($paymentMode && $pembatalan_id && $nik) {
    try {
        // Get pembatalan data
        $stmt = $pdo->prepare("SELECT * FROM data_pembatalan WHERE id = ? AND nik = ?");
        $stmt->execute([$pembatalan_id, $nik]);
        $pembatalanData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pembatalanData) {
            // Parse alasan to get status info
            if (strpos($pembatalanData['alasan'], 'ADMIN_INITIATED|') === 0) {
                $statusJson = substr($pembatalanData['alasan'], 16); // Remove "ADMIN_INITIATED|"
                $statusInfo = json_decode($statusJson, true);
                $dendaInfo = $statusInfo['calculation_details'] ?? null;
            }
            
            // Get jamaah data
            $stmt = $pdo->prepare("
                SELECT j.*, p.program_pilihan, p.jenis_paket 
                FROM data_jamaah j 
                LEFT JOIN data_paket p ON j.pak_id = p.pak_id 
                WHERE j.nik = ?
            ");
            $stmt->execute([$nik]);
            $jamaahData = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        error_log("Error loading pembatalan data: " . $e->getMessage());
    }
}

// Retrieve errors and input data from URL if any
$errors = isset($_GET['errors']) ? $_GET['errors'] : null;
$inputData = isset($_GET['input']) ? json_decode(urldecode($_GET['input']), true) : [];
$success = isset($_GET['success']) ? $_GET['success'] : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $paymentMode ? 'Pembayaran Denda Pembatalan' : 'Form Pembatalan Keikutsertaan'; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-mode-header {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .denda-info {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .payment-form {
            border: 2px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .hidden-form-section {
            display: none;
        }
    </style>
    <script>
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            const errors = urlParams.get('errors');
            const success = urlParams.get('success');

            if (errors) {
                const decodedErrors = decodeURIComponent(errors).replace(/\\n/g, '\n');
                alert(decodedErrors);
            }
            
            if (success) {
                const isPaymentMode = <?php echo $paymentMode ? 'true' : 'false'; ?>;
                if (isPaymentMode) {
                    alert("Pembayaran denda berhasil disubmit! Kami akan memverifikasi pembayaran Anda dan memproses pengembalian dana.");
                } else {
                    alert("Permohonan pembatalan berhasil diajukan! Kami akan menghubungi Anda via email/telepon.");
                }
            }
        };

        function validateForm() {
            const isPaymentMode = <?php echo $paymentMode ? 'true' : 'false'; ?>;
            
            if (isPaymentMode) {
                // Payment mode validation
                const dendaPayment = document.getElementById('denda_payment');
                if (!dendaPayment.files[0]) {
                    alert('Bukti pembayaran denda harus diunggah');
                    return false;
                }
                
                const file = dendaPayment.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Hanya file JPG, PNG, atau PDF yang diperbolehkan');
                    return false;
                }
                
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('Ukuran file tidak boleh melebihi 2MB');
                    return false;
                }
                
                return true;
            }
            
            // Original form validation for normal mode
            // NIK validation (16 digits)
            const nik = document.getElementById('nik').value;
            if (!/^\d{16}$/.test(nik)) {
                alert('NIK harus 16 digit angka');
                return false;
            }

            // Email validation
            const email = document.getElementById('email').value;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Format email tidak valid');
                return false;
            }

            // File validation
            const kwitansiFile = document.getElementById('kwitansi_path').files[0];
            if (!kwitansiFile) {
                alert('Harap upload kwitansi pembayaran');
                return false;
            }

            const proofFile = document.getElementById('proof_path').files[0];
            if (!proofFile) {
                alert('Harap upload bukti pembayaran');
                return false;
            }

            // Check file types and sizes
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            
            if (!allowedTypes.includes(kwitansiFile.type)) {
                alert('File kwitansi harus berupa JPG, PNG, atau PDF');
                return false;
            }
            
            if (!allowedTypes.includes(proofFile.type)) {
                alert('File bukti pembayaran harus berupa JPG, PNG, atau PDF');
                return false;
            }
            
            if (kwitansiFile.size > 2 * 1024 * 1024) { // 2MB limit
                alert('Ukuran file kwitansi tidak boleh melebihi 2MB');
                return false;
            }
            
            if (proofFile.size > 2 * 1024 * 1024) { // 2MB limit
                alert('Ukuran file bukti pembayaran tidak boleh melebihi 2MB');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <header>
        <h1><?php echo $paymentMode ? 'Pembayaran Denda Pembatalan' : 'Form Pembatalan Keikutsertaan'; ?></h1>
    </header>
    <main>
        <?php if ($paymentMode): ?>
            <!-- Payment Mode - Admin Initiated Cancellation -->
            <div class="payment-mode-header">
                <h2>⚠️ Pembatalan Program Telah Diinisiasi Admin</h2>
                <p>Program Anda telah dibatalkan oleh admin kami. Untuk menyelesaikan proses pembatalan, silakan lakukan pembayaran denda sesuai ketentuan di bawah ini.</p>
            </div>
            
            <?php if ($jamaahData && $dendaInfo): ?>
                <div class="denda-info">
                    <h3>Informasi Pembatalan</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Nama</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($jamaahData['nama']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>NIK</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($jamaahData['nik']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Program</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($jamaahData['program_pilihan']); ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Tanggal Keberangkatan</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo date('d/m/Y', strtotime($dendaInfo['departure_date'])); ?></td>
                        </tr>
                    </table>
                    
                    <h3 style="margin-top: 20px;">Rincian Biaya Pembatalan</h3>
                    <?php 
                    $currencySymbol = $dendaInfo['currency'] === 'USD' ? '$' : 'Rp';
                    $dendaFormatted = number_format($dendaInfo['denda_amount'], 0, ',', '.');
                    $totalFormatted = number_format($dendaInfo['total_package_price'], 0, ',', '.');
                    $refundFormatted = number_format($dendaInfo['refund_amount'], 0, ',', '.');
                    ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Total Biaya Paket</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $currencySymbol . ' ' . $totalFormatted; ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Denda Pembatalan (<?php echo $dendaInfo['denda_percentage']; ?>%)</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd; color: red;"><strong><?php echo $currencySymbol . ' ' . $dendaFormatted; ?></strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9;"><strong>Dana yang Dikembalikan</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd; color: green;"><strong><?php echo $currencySymbol . ' ' . $refundFormatted; ?></strong></td>
                        </tr>
                    </table>
                </div>
                
                <?php if ($dendaInfo['denda_amount'] > 0): ?>
                    <div class="payment-form">
                        <h3>Formulir Pembayaran Denda</h3>
                        <p><strong>Jumlah yang harus dibayar: <?php echo $currencySymbol . ' ' . $dendaFormatted; ?></strong></p>
                        <p>Silakan transfer ke rekening berikut dan upload bukti pembayaran:</p>
                        <div style="background-color: #e9ecef; padding: 15px; margin: 10px 0; border-radius: 5px;">
                            <strong>Informasi Rekening:</strong><br>
                            Bank: BNI<br>
                            No. Rekening: 1234567890<br>
                            Atas Nama: PT Madinah Iman Wisata<br>
                            <br>
                            <em>Pastikan nominal transfer sesuai dengan jumlah denda yang tertera.</em>
                        </div>
                        
                        <form action="submit_pembatalan.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                            <input type="hidden" name="payment_mode" value="1">
                            <input type="hidden" name="pembatalan_id" value="<?php echo $pembatalan_id; ?>">
                            <input type="hidden" name="nik" value="<?php echo htmlspecialchars($nik); ?>">
                            
                            <label for="denda_payment">Upload Bukti Pembayaran Denda (max 2MB):</label>
                            <input type="file" id="denda_payment" name="denda_payment" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small style="color: #666;">Format: JPG, PNG, atau PDF. Maksimal 2MB.</small>
                            
                            <button type="submit" style="background-color: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 5px; margin-top: 15px;">Kirim Bukti Pembayaran</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="payment-form">
                        <h3>Tidak Ada Denda</h3>
                        <p>Tidak ada denda yang dikenakan untuk pembatalan ini. Proses pengembalian dana akan segera diproses oleh tim kami.</p>
                        <p>Anda akan menerima konfirmasi email setelah proses selesai.</p>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="payment-mode-header">
                    <h3>Data tidak ditemukan</h3>
                    <p>Maaf, data pembatalan tidak ditemukan. Silakan hubungi customer service kami.</p>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Normal Mode - Regular Cancellation Form -->
            <form action="submit_pembatalan.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
            <div class="cancellation-policy">
                <h2>Kebijakan Pembatalan Madinah Iman Wisata</h2>
                
                <div class="policy-section">
                    <h3>1. Jenis Pembatalan</h3>
                    <div class="policy-item">
                        <h4>A. Pembatalan oleh Jamaah</h4>
                        <p>Jika Anda memutuskan membatalkan perjalanan:</p>
                        <div class="policy-detail">
                            <h5>Umroh</h5>
                            <ul>
                                <li><strong>DP (Uang Muka):</strong> Minimal Rp 5.000.000 tidak dapat dikembalikan (kecuali meninggal dunia)</li>
                                <li><strong>Setelah Pelunasan:</strong>
                                    <ul>
                                        <li>3-6 bulan sebelum berangkat: Denda 30% dari total biaya</li>
                                        <li>2-3 bulan sebelum berangkat: Denda 50% dari total biaya</li>
                                        <li>Kurang dari 1 bulan: Tidak ada pengembalian dana</li>
                                    </ul>
                                </li>
                            </ul>
                            
                            <h5>Haji Khusus</h5>
                            <ul>
                                <li><strong>DP (Uang Muka):</strong> Dikenakan biaya administrasi USD 500</li>
                                <li><strong>Setelah Pelunasan:</strong>
                                    <ul>
                                        <li>Lebih dari 6 bulan: Denda 10% dari total biaya</li>
                                        <li>3-6 bulan: Denda 40% dari total biaya</li>
                                        <li>2-3 bulan: Denda 70% dari total biaya</li>
                                        <li>Kurang dari 1 bulan: Denda 90% dari total biaya</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="policy-item">
                        <h4>B. Pembatalan oleh Madinah Iman Wisata</h4>
                        <p>Hanya berlaku untuk Umroh:</p>
                        <ul>
                            <li>Dana akan dikembalikan penuh atau ditawarkan jadwal baru</li>
                            <li>Dipotong biaya yang sudah dikeluarkan (visa, hotel, tiket)</li>
                            <li>Alasan pembatalan: kuota tidak terpenuhi, visa ditolak, bencana alam, atau hal di luar kendali</li>
                        </ul>
                    </div>
                </div>
                
                <div class="policy-section">
                    <h3>2. Cara Menghitung Denda</h3>
                    <div class="calculation-example">
                        <p>Rumus yang kami gunakan:</p>
                        <div class="formula">
                            <p><strong>Denda = (Total Biaya Paket) × (Persentase Denda)</strong></p>
                            <p>Persentase denda ditentukan oleh:</p>
                            <ol>
                                <li>Jenis program (Umroh/Haji)</li>
                                <li>Tahap pembayaran (DP/Lunas)</li>
                                <li>Waktu pembatalan</li>
                            </ol>
                        </div>
                        
                        <div class="example">
                            <p><strong>Contoh Perhitungan:</strong></p>
                            <p>Paket Haji Khusus @ Rp 75.000.000, dibatalkan 4 bulan setelah pelunasan:</p>
                            <p>Total Denda = Rp 75.000.000 × 40% = <strong>Rp 30.000.000</strong></p>
                            <p>Dana yang dikembalikan = Rp 75.000.000 - Rp 30.000.000 = <strong>Rp 45.000.000</strong></p>
                        </div>
                    </div>
                </div>
                
                <div class="policy-section">
                    <h3>3. Prosedur Pembatalan</h3>
                    <ol class="procedure-steps">
                        <li>Isi formulir pembatalan ini secara lengkap</li>
                        <li>Lampirkan bukti pembayaran terakhir</li>
                        <li>Tim kami akan verifikasi dalam 3 hari kerja</li>
                        <li>Anda akan menerima email berisi:
                            <ul>
                                <li>Rincian denda (jika ada)</li>
                                <li>Nomor referensi pembatalan</li>
                                <li>Prosedur pengembalian dana</li>
                            </ul>
                        </li>
                        <li>Pengembalian dana diproses dalam 14 hari kerja setelah verifikasi</li>
                    </ol>
                </div>
                
                <div class="policy-note">
                    <h3>Catatan Penting:</h3>
                    <ul>
                        <li>Semua denda dihitung berdasarkan <strong>total biaya paket</strong>, bukan DP</li>
                        <li>Waktu pembatalan dihitung dari tanggal keberangkatan di itinerary</li>
                        <li>Untuk pembatalan karena meninggal dunia, harap lampirkan surat keterangan dokter</li>
                        <li>Pengembalian dana akan dikreditkan ke rekening asal pembayaran</li>
                    </ul>
                </div>
            </div>

            <h3>Data Jamaah</h3>
            <label for="nik">NIK:</label>
            <input type="text" id="nik" name="nik" value="<?php echo isset($inputData['nik']) ? $inputData['nik'] : ''; ?>" required maxlength="16">

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" value="<?php echo isset($inputData['nama']) ? $inputData['nama'] : ''; ?>" required>

            <label for="no_telp">No. Telepon/HP:</label>
            <input type="text" id="no_telp" name="no_telp" value="<?php echo isset($inputData['no_telp']) ? $inputData['no_telp'] : ''; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo isset($inputData['email']) ? $inputData['email'] : ''; ?>" required>

            <label for="alasan">Alasan Pembatalan:</label>
            <textarea id="alasan" name="alasan"><?php echo isset($inputData['alasan']) ? $inputData['alasan'] : ''; ?></textarea>

            <h3>Upload Dokumen</h3>
            <label for="kwitansi_path">Kwitansi Pembayaran (max 2MB):</label>
            <input type="file" id="kwitansi_path" name="kwitansi_path" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="proof_path">Bukti Pembayaran (max 2MB):</label>
            <input type="file" id="proof_path" name="proof_path" accept=".pdf,.jpg,.jpeg,.png" required>

            <button type="submit">Ajukan Pembatalan</button>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>