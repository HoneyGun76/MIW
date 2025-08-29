<?php
require_once "config.php";
require_once "terbilang.php";

// Ensure error log directory exists
if (!file_exists(__DIR__ . '/error_logs')) {
    mkdir(__DIR__ . '/error_logs', 0755, true);
}

ob_start();

try {
    // Required parameters validation
    $requiredParams = [
        'nama' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'no_telp' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'alamat' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'pak_id' => FILTER_SANITIZE_NUMBER_INT,
        'type_room_pilihan' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'payment_method' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'payment_type' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'nik' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ];
    
    $filteredInput = [];
    foreach ($requiredParams as $param => $filter) {
        if (!isset($_GET[$param])) {
            throw new InvalidArgumentException("Missing required parameter: $param");
        }
        $filteredInput[$param] = filter_input(INPUT_GET, $param, $filter);
        
        if (empty($filteredInput[$param])) {
            throw new InvalidArgumentException("Parameter $param cannot be empty");
        }
    }
    
    // Optional parameters
    $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);

    // Fetch package details from database
    $stmt = $conn->prepare("SELECT * FROM data_paket WHERE pak_id = :pak_id");
    $stmt->execute(['pak_id' => $filteredInput['pak_id']]);
    $paket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paket) {
        throw new RuntimeException("Paket tidak ditemukan untuk ID: " . $filteredInput['pak_id']);
    }

    // Determine price based on room type
    $priceColumn = 'base_price_' . strtolower($filteredInput['type_room_pilihan']);
    if (!isset($paket[$priceColumn])) {
        throw new RuntimeException("Harga tidak valid untuk tipe kamar ini");
    }
    $payment_total = $paket[$priceColumn];

    // Determine program type and currency
    $currency = $paket['currency'];

    // Format dates
    $tanggal_keberangkatan_formatted = date('d F Y', strtotime($paket['tanggal_keberangkatan']));

    // Bank account information
    $bankAccounts = [
        'Umroh' => [
            'BNI' => ['number' => '1234567890', 'name' => 'MIW Travel Bandung'],
            'Mandiri' => ['number' => '1234567890123', 'name' => 'MIW Travel Bandung']
        ],
        'Haji' => [
            'BSI' => ['number' => '0987654321', 'name' => 'MIW Travel Bandung']
        ]
    ];

    $selectedBank = $bankAccounts[$paket['jenis_paket']][$filteredInput['payment_method']];

    // Prepare view data
    $viewData = [
        'nama' => $filteredInput['nama'],
        'no_telp' => $filteredInput['no_telp'],
        'alamat' => $filteredInput['alamat'],
        'email' => $email,
        'nik' => $filteredInput['nik'],
        'program_pilihan' => $paket['program_pilihan'],
        'tanggal_keberangkatan' => $tanggal_keberangkatan_formatted,
        'type_room_pilihan' => $filteredInput['type_room_pilihan'],
        'payment_method' => $filteredInput['payment_method'],
        'payment_type' => $filteredInput['payment_type'],
        'currency' => $currency,
        'payment_total' => $payment_total,
        'terbilang' => terbilang($payment_total, $currency),
        'bank_number' => $selectedBank['number'],
        'bank_name' => $selectedBank['name']
    ];

} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; color: #721c24; background-color: #f8d7da; }
            .error-container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; background: white; }
            h1 { color: #721c24; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>Terjadi Kesalahan</h1>
            <p>Maaf, terjadi masalah saat memproses invoice Anda.</p>
            <p><strong>Detail:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
            <p>Silakan hubungi customer service kami untuk bantuan lebih lanjut.</p>
        </div>
    </body>
    </html>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran <?= $viewData['program_pilihan'] ?></title>
    <link rel="stylesheet" href="invoice_styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MIW Travel</h1>
            <p>Invoice Pembayaran <?= $viewData['program_pilihan'] ?></p>
        </div>

        <div class="section">
            <div class="section-title">Informasi Pendaftar</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Calon Jamaah</div>
                    <div class="info-value"><?= $viewData['nama'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">No.HP/WA</div>
                    <div class="info-value"><?= $viewData['no_telp'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alamat</div>
                    <div class="info-value"><?= $viewData['alamat'] ?></div>
                </div>
                <?php if (!empty($viewData['email'])): ?>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= $viewData['email'] ?></div>
                </div>
                <?php endif; ?>
                <div class="info-item">
                    <div class="info-label">NIK</div>
                    <div class="info-value"><?= $viewData['nik'] ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Detail Paket</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Program Pilihan</div>
                    <div class="info-value"><?= $viewData['program_pilihan'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Berangkat</div>
                    <div class="info-value"><?= $viewData['tanggal_keberangkatan'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipe Kamar</div>
                    <div class="info-value"><?= $viewData['type_room_pilihan'] ?></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Informasi Pembayaran</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Keterangan Pembayaran</div>
                    <div class="info-value"><?= $viewData['payment_type'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Harga</div>
                    <div class="info-value">
                        <?= $viewData['currency'] === 'USD' ? '$' : 'Rp ' ?>
                        <?= number_format($viewData['payment_total'] ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Terbilang</div>
                    <div class="info-value"><?= $viewData['terbilang'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value"><?= $viewData['payment_method'] ?></div>
                </div>
            </div>
        </div>

        <div class="bank-info">
            <h3>Transfer ke rekening:</h3>
            <p>Bank: <?= $viewData['payment_method'] ?></p>
            <p>Nomor Rekening: <?= $viewData['bank_number'] ?></p>
            <p>Atas Nama: <?= $viewData['bank_name'] ?></p>
            
            <?php if ($viewData['payment_type'] === 'DP'): ?>
                <div class="payment-note">
                    <?php if ($paket['jenis_paket'] === 'Haji'): ?>
                        <h3>Minimal DP (Down Payment) sebesar $5,000 (Lima Ribu US Dollar)</h3>
                    <?php else: ?>
                        <h3>Minimal DP (Down Payment) sebesar Rp.5.000.000,- (Lima Juta Rupiah)</h3>
                    <?php endif; ?>
                    <p>Pembayaran di bawah jumlah minimum mungkin tidak akan diproses oleh admin.</p>
                </div>
            <?php endif; ?>
        </div>

        <form method="post" id="paymentConfirmationForm" enctype="multipart/form-data" action="confirm_payment.php">
            <input type="hidden" name="nik" value="<?= $viewData['nik'] ?>">
            <input type="hidden" name="nama" value="<?= $viewData['nama'] ?>">
            <input type="hidden" name="no_telp" value="<?= $viewData['no_telp'] ?>">
            <input type="hidden" name="program_pilihan" value="<?= $viewData['program_pilihan'] ?>">
            <input type="hidden" name="payment_total" value="<?= $viewData['payment_total'] ?>">
            <input type="hidden" name="payment_method" value="<?= $viewData['payment_method'] ?>">
            <input type="hidden" name="payment_type" value="<?= $viewData['payment_type'] ?>">
            <input type="hidden" name="email" value="<?= $viewData['email'] ?>">
            <input type="hidden" name="type_room_pilihan" value="<?= $viewData['type_room_pilihan'] ?>">
            <input type="hidden" name="tanggal_keberangkatan" value="<?= $viewData['tanggal_keberangkatan'] ?>">
            
            <div class="section">
                <div class="section-title">Informasi Transfer</div>
                <div class="info-item">
                    <div class="info-label">Atas Nama (a.n.) Transfer</div>
                    <div class="info-value">
                        <input type="text" name="transfer_account_name" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Bukti Pembayaran</div>
                <div class="info-item">
                    <div class="info-label">Upload Bukti Transfer</div>
                    <div class="info-value">
                        <input type="file" name="payment_path" accept="image/*,.pdf" required>
                    </div>
                </div>
            </div>

            <button type="submit" id="confirmButton" class="confirmation-button" disabled>Konfirmasi Pembayaran (7)</button>
            
            <script>
                // Add countdown for button activation
                document.addEventListener('DOMContentLoaded', function() {
                    const button = document.getElementById('confirmButton');
                    let countdown = 7; // 7 seconds countdown
                    
                    button.disabled = true;
                    
                    const timer = setInterval(() => {
                        countdown--;
                        button.textContent = `Konfirmasi Pembayaran (${countdown})`;
                        
                        if (countdown <= 0) {
                            clearInterval(timer);
                            button.disabled = false;
                            button.textContent = 'Konfirmasi Pembayaran';
                        }
                    }, 1000);

                    // Prevent double submission
                    const form = document.getElementById('paymentConfirmationForm');
                    form.addEventListener('submit', function(e) {
                        button.disabled = true;
                        button.textContent = 'Memproses...';
                    });
                });
            </script>
        </form>

        <div class="footer">
            <p>Terima kasih telah memilih MIW Travel</p>
        </div>
    </div>
</body>
</html>
<script>
        document.getElementById('paymentConfirmationForm').addEventListener('submit', function(e) {
            // Get the submit button
            var submitButton = this.querySelector('button[type="submit"]');
            
            // Disable the button and change text
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = 'Memproses...';
                
                // Re-enable the button after 10 seconds (in case of errors)
                setTimeout(function() {
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Konfirmasi Pembayaran';
                }, 10000);
            }
        });

        // Show error message if any
        <?php if (isset($_SESSION['payment_error'])): ?>
            alert('<?php echo htmlspecialchars($_SESSION['payment_error']); ?>');
            <?php unset($_SESSION['payment_error']); ?>
        <?php endif; ?>
    </script>
<?php ob_end_flush(); ?>