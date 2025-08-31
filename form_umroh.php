<?php
// Database configuration
require_once "config.php";
require_once 'terbilang.php';

// Initialize variables
$errors = [];
$inputData = [];
$success = false;

// Check for errors/success from previous submission
if (isset($_GET['errors'])) {
    $errors = explode("\n", urldecode($_GET['errors']));
}

if (isset($_GET['success'])) {
    $success = true;
}

if (isset($_GET['input'])) {
    $inputData = json_decode(urldecode($_GET['input']), true);
}

// Fetch Umroh packages with all room types and features
try {
    $stmt = $conn->prepare("SELECT pak_id, program_pilihan, tanggal_keberangkatan, 
                           base_price_quad, base_price_triple, base_price_double
                           FROM data_paket 
                           WHERE jenis_paket = 'Umroh'");
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($packages)) {
        $errors[] = "Tidak ada paket umroh tersedia saat ini.";
    }
} catch (PDOException $e) {
    error_log("Error fetching packages: " . $e->getMessage());
    $errors[] = "Error mengambil data paket. Silakan coba lagi nanti.";
}

// Prepare biaya_paket and terbilang values
$biaya_paket_value = isset($inputData['harga_paket']) ? preg_replace('/[^0-9]/', '', $inputData['harga_paket']) : '';
$terbilang = '';
if ($biaya_paket_value !== '') {
    $terbilang = terbilang($biaya_paket_value, 'IDR');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Umroh</title>
    <link rel="stylesheet" href="styles.css">
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
                alert("Pendaftaran berhasil! Kami akan mengirimkan konfirmasi melalui email.");
            }
        };

        function updateBiaya() {
            const paketSelect = document.getElementById("pak_id");
            const roomTypeSelect = document.getElementById("type_room_pilihan");
            const biayaInput = document.getElementById("harga_paket");
            const tanggalKeberangkatanInput = document.getElementById("tanggal_keberangkatan");
            const programPilihanInput = document.getElementById("program_pilihan");

            if (!paketSelect || !roomTypeSelect || !biayaInput || !tanggalKeberangkatanInput) {
                return;
            }

            const selectedOption = paketSelect.options[paketSelect.selectedIndex];
            if (!selectedOption) return;
            
            try {
                const packageData = JSON.parse(selectedOption.getAttribute("data-package"));
                if (!packageData) return;
                
                tanggalKeberangkatanInput.value = packageData.tanggal_keberangkatan;
                programPilihanInput.value = packageData.program_pilihan;
                
                roomTypeSelect.innerHTML = '';
                const roomTypes = [
                    { type: 'Quad', price: packageData.base_price_quad },
                    { type: 'Triple', price: packageData.base_price_triple },
                    { type: 'Double', price: packageData.base_price_double }
                ];
                
                roomTypes.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.type;
                    option.textContent = `${room.type} - Rp ${room.price.toLocaleString('id-ID')}`;
                    option.dataset.price = room.price;
                    roomTypeSelect.appendChild(option);
                });
                
                updateTotalPrice();
            } catch (e) {
                console.error("Error parsing package data:", e);
            }
        }

        function updateTotalPrice() {
            const roomTypeSelect = document.getElementById("type_room_pilihan");
            const biayaInput = document.getElementById("harga_paket");
            const terbilangInput = document.getElementById("biaya_terbilang");
            const selectedRoom = roomTypeSelect?.options[roomTypeSelect.selectedIndex];
            
            if (!selectedRoom) return;
            
            let totalPrice = parseFloat(selectedRoom.dataset.price);
            biayaInput.value = totalPrice;
            terbilangInput.value = formatTerbilang(totalPrice);
        }

        function formatTerbilang(amount) {
            amount = Math.floor(amount);
            if (amount === 0) return 'Nol Rupiah';
            
            const units = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
            const teens = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 
                        'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
            const tens = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 
                        'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
            
            function convertLessThanOneThousand(num) {
                if (num === 0) return '';
                if (num < 10) return units[num];
                if (num < 20) return teens[num - 10];
                if (num < 100) {
                    return tens[Math.floor(num / 10)] + 
                        (num % 10 !== 0 ? ' ' + units[num % 10] : '');
                }
                if (num < 200) return 'Seratus ' + convertLessThanOneThousand(num - 100);
                return units[Math.floor(num / 100)] + ' Ratus ' + convertLessThanOneThousand(num % 100);
            }
            
            function convert(num) {
                if (num === 0) return '';
                if (num < 1000) return convertLessThanOneThousand(num);
                if (num < 2000) return 'Seribu ' + convertLessThanOneThousand(num - 1000);
                if (num < 1000000) {
                    const thousands = Math.floor(num / 1000);
                    return convertLessThanOneThousand(thousands) + ' Ribu ' + convertLessThanOneThousand(num % 1000);
                }
                if (num < 1000000000) {
                    const millions = Math.floor(num / 1000000);
                    return convertLessThanOneThousand(millions) + ' Juta ' + convert(num % 1000000);
                }
                return 'Jumlah terlalu besar';
            }
            
            const result = convert(amount).replace(/\s+/g, ' ').trim() + ' Rupiah';
            return result === ' Rupiah' ? 'Nol Rupiah' : result;
        }

        function toggleMarketingFields() {
            const marketingType = document.getElementById("marketing_type");
            const marketingFields = document.getElementById("marketing_fields");
            const marketingNama = document.getElementById("marketing_nama");
            const marketingHp = document.getElementById("marketing_hp");
            const nama = document.getElementById("nama");
            const noTelp = document.getElementById("no_telp");

            if (!marketingType || !marketingFields || !marketingNama || !marketingHp || !nama || !noTelp) {
                return;
            }

            if (marketingType.value === "mandiri") {
                marketingFields.style.display = "block";
                marketingNama.value = nama.value;
                marketingHp.value = noTelp.value;
                marketingNama.readOnly = true;
                marketingHp.readOnly = true;
            } else if (marketingType.value === "orang_lain") {
                marketingFields.style.display = "block";
                marketingNama.value = "";
                marketingHp.value = "";
                marketingNama.readOnly = false;
                marketingHp.readOnly = false;
            } else {
                marketingFields.style.display = "none";
            }
        }

        function validateForm() {
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

            // File validations
            const kkFile = document.getElementById('kk_path').files[0];
            const ktpFile = document.getElementById('ktp_path').files[0];
            
            if (!kkFile || !ktpFile) {
                alert('Harap upload KK dan KTP');
                return false;
            }

            // Check file types
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            const files = [kkFile, ktpFile];
            
            for (let file of files) {
                if (file && !allowedTypes.includes(file.type)) {
                    alert('Hanya file JPG, PNG, atau PDF yang diperbolehkan');
                    return false;
                }
                
                if (file && file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('Ukuran file tidak boleh melebihi 2MB');
                    return false;
                }
            }

            return true;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("umrohForm");
            if (form) {
                form.addEventListener("submit", function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                    }
                });
            }
            toggleMarketingFields();
        });
    </script>
</head>
<body>
    <header>
        <h1>Form Pendaftaran Umroh</h1>
    </header>
    <main>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <h3>Terjadi kesalahan:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <p>Pendaftaran berhasil! Kami akan mengirimkan konfirmasi via email.</p>
            </div>
        <?php else: ?>

        <form action="submit_umroh.php" method="POST" enctype="multipart/form-data" id="umrohForm">
            <input type="hidden" name="currency" value="IDR">
            <input type="hidden" name="jenis_paket" value="Umroh">
            <input type="hidden" id="program_pilihan" name="program_pilihan" value="">
            <input type="hidden" id="harga_paket" name="harga_paket" value="0">

            <h3>Informasi Pribadi</h3>
            <label for="nik">NIK (16 digit):</label>
            <input type="text" id="nik" name="nik" required maxlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka">

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="tempat_lahir">Tempat Lahir:</label>
            <input type="text" id="tempat_lahir" name="tempat_lahir" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label for="alamat">Alamat Lengkap:</label>
            <textarea id="alamat" name="alamat" required></textarea>

            <label for="kode_pos">Kode Pos:</label>
            <input type="text" id="kode_pos" name="kode_pos" maxlength="5">

            <label for="no_telp">Nomor Telepon/HP:</label>
            <input type="text" id="no_telp" name="no_telp" required>

            <label for="email">Alamat Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="tinggi_badan">Tinggi Badan (cm):</label>
            <input type="number" id="tinggi_badan" name="tinggi_badan" min="100" max="250">

            <label for="berat_badan">Berat Badan (kg):</label>
            <input type="number" id="berat_badan" name="berat_badan" min="30" max="200">

            <label for="nama_ayah">Nama Ayah:</label>
            <input type="text" id="nama_ayah" name="nama_ayah" required>

            <label for="nama_ibu">Nama Ibu:</label>
            <input type="text" id="nama_ibu" name="nama_ibu" required>

            <h3>Kontak Darurat</h3>
            <label for="emergency_nama">Nama Kontak Darurat:</label>
            <input type="text" id="emergency_nama" name="emergency_nama">

            <label for="emergency_hp">Nomor HP Darurat:</label>
            <input type="text" id="emergency_hp" name="emergency_hp">

            <h3>Data Paspor</h3>
            <label for="nama_paspor">Nama Lengkap di Paspor:</label>
            <input type="text" id="nama_paspor" name="nama_paspor">

            <label for="no_paspor">Nomor Paspor:</label>
            <input type="text" id="no_paspor" name="no_paspor">

            <label for="tempat_pembuatan_paspor">Tempat Dikeluarkan:</label>
            <input type="text" id="tempat_pembuatan_paspor" name="tempat_pembuatan_paspor">

            <label for="tanggal_pengeluaran_paspor">Tanggal Dikeluarkan:</label>
            <input type="date" id="tanggal_pengeluaran_paspor" name="tanggal_pengeluaran_paspor">

            <label for="tanggal_habis_berlaku">Tanggal Masa Berlaku:</label>
            <input type="date" id="tanggal_habis_berlaku" name="tanggal_habis_berlaku">

            <h3>Informasi Vaksinasi (Tidak Wajib Diisi)</h3>
            <label for="jenis_vaksin_1">Jenis Vaksin Dosis 1 (Tidak Wajib Diisi):</label>
            <input type="text" id="jenis_vaksin_1" name="jenis_vaksin_1">

            <label for="tanggal_vaksin_1">Tanggal Vaksin Dosis 1 (Tidak Wajib Diisi):</label>
            <input type="date" id="tanggal_vaksin_1" name="tanggal_vaksin_1">

            <label for="jenis_vaksin_2">Jenis Vaksin Dosis 2 (Tidak Wajib Diisi):</label>
            <input type="text" id="jenis_vaksin_2" name="jenis_vaksin_2">

            <label for="tanggal_vaksin_2">Tanggal Vaksin Dosis 2 (Tidak Wajib Diisi):</label>
            <input type="date" id="tanggal_vaksin_2" name="tanggal_vaksin_2">

            <label for="jenis_vaksin_3">Jenis Vaksin Dosis 3 (Tidak Wajib Diisi):</label>
            <input type="text" id="jenis_vaksin_3" name="jenis_vaksin_3">

            <label for="tanggal_vaksin_3">Tanggal Vaksin Dosis 3 (Tidak Wajib Diisi):</label>
            <input type="date" id="tanggal_vaksin_3" name="tanggal_vaksin_3">

            <h3>Paket Umroh</h3>
            <label for="pak_id">Program Pilihan:</label>
            <select id="pak_id" name="pak_id" required onchange="updateBiaya()">
                <option value="">-- Pilih Program --</option>
                <?php foreach ($packages as $package): ?>
                        <?php
                        $packageData = [
                            'program_pilihan' => $package['program_pilihan'],
                            'tanggal_keberangkatan' => $package['tanggal_keberangkatan'],
                            'base_price_quad' => $package['base_price_quad'],
                            'base_price_triple' => $package['base_price_triple'],
                            'base_price_double' => $package['base_price_double']
                        ];
                        
                        $selected = isset($inputData['pak_id']) && $inputData['pak_id'] == $package['pak_id'] ? 'selected' : '';
                        ?>
                        <option value="<?php echo $package['pak_id']; ?>" 
                                data-package='<?php echo json_encode($packageData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>' 
                                <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($package['program_pilihan']); ?>
                        </option>
                    <?php endforeach; ?></select>

            <label for="tanggal_keberangkatan">Tanggal Keberangkatan:</label>
            <input type="date" id="tanggal_keberangkatan" name="tanggal_keberangkatan" readonly>

            <label for="type_room_pilihan">Tipe Kamar:</label>
            <select id="type_room_pilihan" name="type_room_pilihan" required onchange="updateTotalPrice()">
                <option value="">-- Pilih Tipe Kamar --</option>
            </select>

            <label for="biaya_terbilang">Biaya Paket (Terbilang):</label>
            <input type="text" id="biaya_terbilang" name="biaya_terbilang" readonly>

            <h3>Informasi Pembayaran</h3>
            <label for="payment_method">Metode Pembayaran:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">-- Pilih Metode --</option>
                <option value="BNI" <?php echo (isset($inputData['payment_method']) && $inputData['payment_method'] === 'BNI') ? 'selected' : ''; ?>>BNI</option>
                <option value="Mandiri" <?php echo (isset($inputData['payment_method']) && $inputData['payment_method'] === 'Mandiri') ? 'selected' : ''; ?>>Mandiri</option>
            </select>

            <label for="payment_type">Keterangan Pembayaran:</label>
            <select id="payment_type" name="payment_type" required>
                <option value="">-- Pilih Keterangan --</option>
                <option value="DP" <?php echo (isset($inputData['payment_type']) && $inputData['payment_type'] === 'DP') ? 'selected' : ''; ?>>DP (Down Payment)</option>
                <option value="Pelunasan" <?php echo (isset($inputData['payment_type']) && $inputData['payment_type'] === 'Pelunasan') ? 'selected' : ''; ?>>Lunas (Full Payment)</option>
            </select>

            <h3>Dokumen Pendukung</h3>
            <label for="kk_path">Upload Kartu Keluarga (max 2MB):</label>
            <input type="file" id="kk_path" name="kk_path" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="ktp_path">Upload KTP (max 2MB):</label>
            <input type="file" id="ktp_path" name="ktp_path" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="paspor_path">Upload Paspor (jika ada, max 2MB):</label>
            <input type="file" id="paspor_path" name="paspor_path" accept=".pdf,.jpg,.jpeg,.png">

            <h3>Informasi Pendaftaran</h3>
            <label for="marketing_type">Tipe Pendaftaran:</label>
            <select id="marketing_type" name="marketing_type" required onchange="toggleMarketingFields()">
                <option value="">-- Pilih Tipe Pendaftaran --</option>
                <option value="mandiri">Saya mendaftar sendiri/Mandiri</option>
                <option value="orang_lain">Saya mendaftarkan orang lain</option>
            </select>

            <div id="marketing_fields" style="display: none;">
                <label for="marketing_nama">Nama Pendaftar:</label>
                <input type="text" id="marketing_nama" name="marketing_nama">

                <label for="marketing_hp">No. HP Pendaftar:</label>
                <input type="text" id="marketing_hp" name="marketing_hp">
            </div>

            <h3>Request Khusus</h3>
            <textarea id="request_khusus" name="request_khusus" placeholder="Masukkan request khusus jika ada"></textarea>

            <button type="submit">Daftar</button>
        </form>
        <?php endif; ?>
    </main>
</body>
</html>