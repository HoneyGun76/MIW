<?php
// Ensure all variables are properly set
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use data from passed array
$data = $kwitansiData ?? [];

// Basic information
$nomor_kwitansi = $data['invoice_id'] ?? '';
$nama = $data['nama'] ?? '';
$alamat = $data['alamat'] ?? '';
$no_telp = $data['no_telp'] ?? '';
$keterangan = $data['keterangan'] ?? '';  // Can be customized if needed
$deskripsi = sprintf("Pembayaran %s %s program %s",
    $data['payment_type'] ?? 'DP',
    $data['type_room_pilihan'] ?? '',
    $data['program_pilihan'] ?? ''
);

// Package details
$program_pilihan = $data['program_pilihan'] ?? '';
$type_room_pilihan = $data['type_room_pilihan'] ?? '';
$harga_paket = floatval($data['package_price'] ?? 0);

// Payment information
$payment_type = $data['payment_type'] ?? 'DP';
$payment_method = $data['payment_method'] ?? '';
$metode_pembayaran = $payment_method; // Map to the variable used in template
$uang_masuk = floatval($data['payment_total'] ?? 0);
$diskon = floatval($data['diskon'] ?? 0);
$total_uang_masuk = $uang_masuk;
$sisa_pembayaran = $harga_paket - $total_uang_masuk;

// Get currency from the passed data
$currency = $data['currency'] ?? 'IDR';
$currencySymbol = $currency === 'USD' ? '$' : 'Rp';

// Include the terbilang helper
require_once 'terbilang.php';
$terbilang = terbilang($total_uang_masuk, $currency);

function formatCurrency($value, $curr = 'IDR') {
    // If already numeric, format directly
    if (is_numeric($value)) {
        return ($curr === 'USD' ? '$ ' : 'Rp ') . number_format($value ?? 0, 0, ',', '.');
    }

    // Remove common formatting if string input
    $cleaned = preg_replace('/[^0-9]/', '', $value);
    return ($curr === 'USD' ? '$ ' : 'Rp ') . number_format((float)($cleaned ?? 0), 0, ',', '.');
}


// Format the currency values for display
$uang_masuk_formatted = !empty($uang_masuk) ? formatCurrency($uang_masuk, $currency) : ($currency === 'USD' ? '$ 0' : 'Rp 0');
$total_harga_formatted = !empty($total_harga) ? formatCurrency($total_harga, $currency) : ($currency === 'USD' ? '$ 0' : 'Rp 0');
$diskon_formatted = !empty($diskon) ? formatCurrency($diskon, $currency) : ($currency === 'USD' ? '$ 0' : 'Rp 0');
$total_uang_masuk_formatted = !empty($total_uang_masuk) ? formatCurrency($total_uang_masuk, $currency) : ($currency === 'USD' ? '$ 0' : 'Rp 0');
$sisa_pembayaran_formatted = !empty($sisa_pembayaran) ? formatCurrency($sisa_pembayaran, $currency) : ($currency === 'USD' ? '$ 0' : 'Rp 0');

// Function to check if online resource is accessible
function isOnlineResourceAccessible($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $response_code == 200;
}

// Function to get image source with better error handling
function getImageSource($online_url, $local_path) {
    // First try local path with absolute path
    $absolute_local_path = __DIR__ . '/' . $local_path;
    if (file_exists($absolute_local_path)) {
        $image_type = pathinfo($absolute_local_path, PATHINFO_EXTENSION);
        $image_data = file_get_contents($absolute_local_path);
        if ($image_data !== false) {
            return 'data:image/' . $image_type . ';base64,' . base64_encode($image_data);
        }
    }
    
    // Then try online URL if local fails
    if (filter_var($online_url, FILTER_VALIDATE_URL)) {
        $image_data = @file_get_contents($online_url);
        if ($image_data !== false) {
            return $online_url;
        }
    }
    
    return ''; // Return empty if neither source works
}

// Logo handling
$logo_src = getImageSource(
    'https://madinahimanwisata.co.id/wp-content/uploads/2022/07/Logo.svg',
    'miw_logo.png'
);

// Icons handling
$wa_icon_src = getImageSource(
    'https://www.citypng.com/public/uploads/preview/whatsapp-black-logo-icon-transparent-png-701751695033911ohce7u0egy.png',
    'wa_icon.png'
);

$ig_icon_src = getImageSource(
    'https://clipground.com/images/black-instagram-logo-clipart-2.jpg',
    'ig_icon.jpg'
);

$web_icon_src = getImageSource(
    'https://www.freepnglogos.com/uploads/logo-website-png/logo-website-file-globe-icon-svg-wikimedia-commons-21.png',
    'web_icon.png'
);

$himpuh_icon_src = getImageSource(
    'https://static.himpuh.or.id/img/logo-origin.png',
    'himpuh_icon.jpg'
);

$iata_icon_src = getImageSource(
    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/IATAlogo.svg/512px-IATAlogo.svg.png?20170225002728',
    'iata_icon.png'
);

$kan_icon_src = getImageSource(
    'https://global-resource.co.id/wp-content/uploads/2016/11/KAN.png',
    'kan_icon.jpg'
);

// Debugging: Check which images are loading
error_log("Logo loaded: " . (!empty($logo_src) ? 'Yes' : 'No'));
error_log("WA icon loaded: " . (!empty($wa_icon_src) ? 'Yes' : 'No'));
error_log("IG icon loaded: " . (!empty($ig_icon_src) ? 'Yes' : 'No'));
error_log("Web icon loaded: " . (!empty($web_icon_src) ? 'Yes' : 'No'));
error_log("Himpuh icon loaded: " . (!empty($himpuh_icon_src) ? 'Yes' : 'No'));
error_log("IATA icon loaded: " . (!empty($iata_icon_src) ? 'Yes' : 'No'));
error_log("KAN icon loaded: " . (!empty($kan_icon_src) ? 'Yes' : 'No'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KWITANSI PEMBAYARAN</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 2mm;
            font-size: 10px;
            line-height: 1.2;
        }

        /* Container */
        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .header {
            margin-bottom: 5px;
        }

        .header-black-bar {
            background-color: #000;
            color: white;
            padding: 5px 8px;
            min-height: auto;
            display: inline-block;
            width: 100%;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-column {
            width: 15%;
            vertical-align: top;
        }

        .address-column {
            width: 30%;
            vertical-align: top;
            padding-right: 5px;
            font-size: 8px;
            line-height: 1.2;
        }

        .title-column {
            width: 25%;
            vertical-align: top;
            text-align: right;
        }

        .logo {
            height: 50px;
            margin-top: 3px;
            margin-left: 4px;
        }

        .kwitansi-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .receipt-label {
            font-size: 10px;
        }

        /* Content Tables */
        .content {
            margin-bottom: 5px;
        }

        /* First Table (Main Fields) */
        .main-fields {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 9px;
        }

        .main-fields th {
            border: none !important;
            background: none !important;
            padding: 3px;
            text-align: left;
            font-weight: bold;
            width: 20%;
        }

        .main-fields td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
            background: transparent;
            width: 30%;
        }

        /* Add this new rule to remove border for specific cells */
        .main-fields tr:first-child td:first-child {
            border: none;
            background: transparent;
        }


        /* Second Table (Items Table) */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            font-size: 8px;
        }

        .items-table thead {
            border-bottom: 1px solid #000;
        }

        .items-table th,
        .items-table td {
            padding: 3px;
            text-align: left;
            border: none;
        }

        .items-table th {
            font-weight: bold;
            background: none;
        }

        /* Payment Summary Table */
        .payment-summary {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .payment-column {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .payment-left,
        .payment-right {
            width: 100%;
            border-collapse: collapse;
            border: none;
            font-size: 9px;
        }

        .payment-left th,
        .payment-left td,
        .payment-right th,
        .payment-right td {
            padding: 3px;
        }

        .payment-left th {
            border: none !important;
            background: none !important;
            padding: 3px;
            padding-left: 0;
            text-align: left;
            font-weight: bold;
            width: auto;
        }
        
        .payment-left td {
            border: 1px solid #000;
            padding: 3px;
            padding-right: 2px;
            text-align: right;
            background: transparent;
            width: 60%;
        }

        .payment-right th {
            text-align: left;
            padding-left: 3px;
        }

        .payment-right td {
            text-align: right;
        }

        /* Payment Method */
        .payment-method {
            margin-top: 5px;
            padding: 3px 0;
            text-align: left;
            width: 100%;
            box-sizing: border-box;
            font-weight: bold;
            font-size: 9px;
        }

        /* Signature Table Styles */
        .signature-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
            font-size: 9px;
        }

        .signature-table td[colspan="3"] table {
            width: 100%;
            margin: 0 auto;
        }

        .signature-table td[colspan="3"] table tr td {
            text-align: center; /* Center numbers under icons */
            padding: 0 2px;
        }

        .signature-label {
            padding-top: 20px;
        }

        .signature-referral-label {
            text-align: right;
            padding-top: 20px;
            padding-right: 0px;
        }

        .signature-name {
            padding-top: 0px;
        }

        .signature-empty {
            height: 50px;
            text-align: center;
            vertical-align: bottom;
        }

        .icon-container {
            width: 200px;
            height: 30px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 5px;
            border: 1px solid #000;
        }

        .icon-placeholder {
            height: 20px;
        }

        .contact-numbers {
            display: flex;
            justify-content: space-around;
            font-size: 8px;
            margin-top: 5px;
        }

        .contact-info {
            width: 20px;
            height: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .output-data {
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-black-bar">
                <table class="header-table">
                    <tr>
                        <!-- Logo Column -->
                        <td class="logo-column">
                            <?php if (!empty($logo_src)): ?>
                                <img src="<?= $logo_src ?>" class="logo" alt="miw_logo.png">
                            <?php else: ?>
                                <!-- Fallback text if no logo can be loaded -->
                                <div style="height:50px;display:flex;align-items:center;font-weight:bold;font-size:12px;">
                                    MADINAH IMAN WISATA
                                </div>
                            <?php endif; ?>
                        </td>
                        
                        <!-- Address Column 1 -->
                        <td class="address-column">
                            <div><b>Alamat</b></div>
                            <div>Kantor Bandung</div>
                            <div>Jl. R.A.A. Marta Negara No.16 Kota Bandung</div>
                            <div>40264</div>
                            <div>Email : miwtours@gmail.com</div>
                        </td>
                        
                        <!-- Address Column 2 -->
                        <td class="address-column">
                            <div>&nbsp;</div>
                            <div>Kantor Pusat</div>
                            <div>Ruko Harvest Bintaro No.1-2</div>
                            <div>Jl. Merpati Raya, Ciputat</div>
                            <div>Tangerang Selatan 15413</div>
                        </td>
                        
                        <!-- Title Column -->
                        <td class="title-column">
                            <div class="kwitansi-title">KWITANSI</div>
                            <div class="receipt-label">Receipt</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="content">
            <!-- First Table (Header Information) -->
            <table class="main-fields">
                <tr>
                    <th>Nomor</th>
                    <td style="border: none !important;"><?= htmlspecialchars($nomor_kwitansi) ?></td>
                </tr>
                <tr>
                    <th>Sudah Diterima Dari</th>
                    <td><?= htmlspecialchars($nama) ?></td>
                    <th>Alamat</th>
                    <td><?= htmlspecialchars($alamat) ?></td>
                </tr>
                <tr>
                    <th>No. HP/WA</th>
                    <td><?= htmlspecialchars($no_telp) ?></td>
                    <th rowspan="2">Untuk Pembayaran</th>
                    <td rowspan="2"><?= htmlspecialchars($deskripsi) ?></td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td><?= htmlspecialchars($keterangan) ?></td>
                    <td colspan="2" style="border: none !important;"></td>
                </tr>
            </table>

            <!-- Second Table (Items Table) -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="5%">No.</th>
                        <th width="35%">Deskripsi</th>
                        <th width="15%">Kamar</th>
                        <th width="10%">Qty</th>
                        <th width="17%">Harga</th>
                        <th width="18%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- First Data Row -->
                    <tr>
                        <td>1</td>
                        <td><?= !empty($data_paket) ? htmlspecialchars($data_paket) : '&nbsp;' ?></td>
                        <td><?= !empty($kamar) ? htmlspecialchars($kamar) : '&nbsp;' ?></td>
                        <td>1</td>
                        <td><?= !empty($harga_paket) ? formatCurrency(str_replace(['Rp', '.', ' '], '', $harga_paket)) : '&nbsp;' ?></td>
                        <td><?= !empty($total_harga) ? formatCurrency(str_replace(['Rp', '.', ' '], '', $total_harga)) : '&nbsp;' ?></td>
                    </tr>
                    <!-- Second Empty Row -->
                    <tr>
                        <td>2</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <!-- Third Empty Row -->
                    <tr>
                        <td>3</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>

            <!-- Third Table (Payment Summary) -->
            <table class="payment-summary">
                <tr>
                    <!-- Left Column -->
                    <td class="payment-column">
                        <table class="payment-left">
                            <tr>
                                <th>Uang Masuk</th>
                                <td><?= $uang_masuk_formatted ?></td>
                            </tr>
                            <tr>
                                <th>Terbilang</th>
                                <td><?= htmlspecialchars($terbilang) ?></td>
                            </tr>
                        </table>
                    </td>
                    <!-- Right Column -->
                    <td class="payment-column">
                        <table class="payment-right">
                            <tr>
                                <th>Total Harga</th>
                                <td><?= $total_harga_formatted ?></td>
                            </tr>
                            <tr>
                                <th>Diskon</th>
                                <td><?= $diskon_formatted ?></td>
                            </tr>
                            <tr>
                                <th>Total Uang Masuk</th>
                                <td><?= $total_uang_masuk_formatted ?></td>
                            </tr>
                            <tr>
                                <th>Sisa Pembayaran</th>
                                <td><?= $sisa_pembayaran_formatted ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Clean Payment Method Field -->
            <div class="payment-method">
                <span class="method-label">Metode Pembayaran:</span>
                <span class="method-value"><?= htmlspecialchars($metode_pembayaran) ?></span>
            </div>

            <!-- Signature Table (4 columns) -->
            <table class="signature-table">
                <tr>
                    <td class="signature-label">Penyetor</td>
                    <td class="signature-label" style="text-align: center;">Penerima</td>
                    <td class="signature-referral-label"><b>Referral :</b></td>
                    <td class="output-data">
                        <?php 
                        $referral = '';
                        if (isset($row['marketing_type'])) {
                            if ($row['marketing_type'] == 'pribadi') {
                                $referral = 'ELI RAHMALIA';
                            } elseif ($row['marketing_type'] == 'orang_lain' && !empty($row['marketing_nama'])) {
                                $referral = $row['marketing_nama'];
                            }
                        }
                        echo htmlspecialchars($referral);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="signature-empty"></td>
                    <td class="signature-empty" style="text-align: left;"></td>
                    <td class="signature-empty" style="text-align: right;">
                        <!-- Soft-edge rectangle for icons -->
                        <div class="icon-container">
                            <!-- Three icon placeholders with fallback text -->
                            <?php if (!empty($himpuh_icon_src)): ?>
                                <img class="icon-placeholder" src="<?= $himpuh_icon_src ?>" alt="Himpuh" style="height:20px;">
                            <?php else: ?>
                                <span>Himpuh</span>
                            <?php endif; ?>
                            
                            <?php if (!empty($iata_icon_src)): ?>
                                <img class="icon-placeholder" src="<?= $iata_icon_src ?>" alt="IATA" style="height:20px;">
                            <?php else: ?>
                                <span>IATA</span>
                            <?php endif; ?>
                            
                            <?php if (!empty($kan_icon_src)): ?>
                                <img class="icon-placeholder" src="<?= $kan_icon_src ?>" alt="KAN" style="height:20px;">
                            <?php else: ?>
                                <span>KAN</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="signature-empty"></td>
                </tr>
                <tr>
                    <td class="signature-name"><?= htmlspecialchars($nama) ?></td>
                    <td class="signature-name" style="text-align: center;">Anggi</td>
                    <td>
                        <!-- Contact numbers below icons -->
                        <table class="contact-info">
                            <tr>
                                <?php if (!empty($wa_icon_src)): ?>
                                    <th><img class="contact-info" src="<?= $wa_icon_src ?>" alt="WhatsApp" style="width:20px;height:20px;"></th>
                                <?php else: ?>
                                    <th>WA</th>
                                <?php endif; ?>
                                <td style="padding-right: 10px;">08112041100</td>
                                
                                <?php if (!empty($ig_icon_src)): ?>
                                    <th><img class="contact-info" src="<?= $ig_icon_src ?>" alt="Instagram" style="width:20px;height:20px;"></th>
                                <?php else: ?>
                                    <th>IG</th>
                                <?php endif; ?>
                                <td>miwtravel.bandung</td>
                                
                                <?php if (!empty($web_icon_src)): ?>
                                    <th style="padding-left: 10px;"><img class="contact-info" src="<?= $web_icon_src ?>" alt="Website" style="width:20px;height:20px;"></th>
                                <?php else: ?>
                                    <th>Web</th>
                                <?php endif; ?>
                                <td>www.miw.co.id</td>
                            </tr>
                        </table>
                    </td>
                    <td class="output-data"></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>