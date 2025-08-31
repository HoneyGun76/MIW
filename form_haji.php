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
                           WHERE jenis_paket = 'Haji'");
    $stmt->execute();
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($packages)) {
        $errors[] = "Tidak ada paket haji tersedia saat ini.";
    }
} catch (PDOException $e) {
    error_log("Error fetching packages: " . $e->getMessage());
    $errors[] = "Error mengambil data paket. Silakan coba lagi nanti.";
}

// Prepare biaya_paket and terbilang values
$biaya_paket_value = isset($inputData['harga_paket']) ? preg_replace('/[^0-9]/', '', $inputData['harga_paket']) : '';
$terbilang = '';
if ($biaya_paket_value !== '') {
    $terbilang = terbilang($biaya_paket_value, 'USD');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Haji</title>
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
                alert("Pendaftaran berhasil! Kami akan mengirimkan konfirmasi via email.");
            }
        };

        function updateBiaya() {
            const paketSelect = document.getElementById("pak_id");
            const roomTypeSelect = document.getElementById("type_room_pilihan");
            const biayaTerbilangInput = document.getElementById("biaya_terbilang");
            const tanggalKeberangkatanInput = document.getElementById("tanggal_keberangkatan");
            const programPilihanInput = document.getElementById("program_pilihan");

            if (!paketSelect || !roomTypeSelect || !biayaTerbilangInput || !tanggalKeberangkatanInput) {
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
                    option.textContent = `${room.type} - $ ${room.price.toLocaleString('id-ID')}`;
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
            const biayaTerbilangInput = document.getElementById("biaya_terbilang");
            const biayaPaketInput = document.getElementById("harga_paket");
            const selectedRoom = roomTypeSelect?.options[roomTypeSelect.selectedIndex];
            
            if (!selectedRoom) return;
            
            let totalPrice = parseFloat(selectedRoom.dataset.price);
            biayaTerbilangInput.value = formatTerbilang(totalPrice);
            biayaPaketInput.value = totalPrice;
        }

        function formatTerbilang(amount) {
            amount = Math.floor(amount);
            if (amount === 0) return 'Nol Dolar';
            
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
            
            const result = convert(amount).replace(/\s+/g, ' ').trim() + ' Dolar';
            return result === ' Dolar' ? 'Nol Dolar' : result;
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
            // Add validation logic here
            return true;
        }

        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }

        function updateAge() {
            const tanggalLahirInput = document.getElementById("tanggal_lahir");
            const umurInput = document.getElementById("umur");
            
            if (tanggalLahirInput.value) {
                const age = calculateAge(tanggalLahirInput.value);
                umurInput.value = age;
            } else {
                umurInput.value = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("hajiForm");
            const tanggalLahirInput = document.getElementById("tanggal_lahir");
            
            if (form) {
                form.addEventListener("submit", function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                    }
                });
            }
            
            if (tanggalLahirInput) {
                tanggalLahirInput.addEventListener("change", updateAge);
                // Calculate initial age if date is already set
                if (tanggalLahirInput.value) {
                    updateAge();
                }
            }
            
            toggleMarketingFields();
            
            if (document.getElementById("marketing_type").value) {
                toggleMarketingFields();
            }
        });
    </script>
</head>
<body>
    <header>
        <h1>Form Pendaftaran Haji</h1>
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
        <form action="submit_haji.php" method="POST" enctype="multipart/form-data" id="hajiForm">
            <input type="hidden" name="currency" value="USD">
            <input type="hidden" name="jenis_paket" value="Haji">
            <input type="hidden" id="program_pilihan" name="program_pilihan" value="">
            <input type="hidden" id="harga_paket" name="harga_paket" value="0">

            <h3>Informasi Pribadi</h3>
            <label for="nik">NIK (16 digit):</label>
            <input type="text" id="nik" name="nik" required maxlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka">

            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="nama_ayah">Nama Ayah Kandung:</label>
            <input type="text" id="nama_ayah" name="nama_ayah" required>

            <label for="tempat_lahir">Tempat Lahir:</label>
            <input type="text" id="tempat_lahir" name="tempat_lahir" required>

            <label for="tanggal_lahir">Tanggal Lahir:</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

            <label for="umur">Umur:</label>
            <input type="number" id="umur" name="umur" min="12" max="100" readonly>

            <label for="jenis_kelamin">Jenis Kelamin:</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>

            <label for="tinggi_badan">Tinggi Badan (cm):</label>
            <input type="number" id="tinggi_badan" name="tinggi_badan" min="100" max="250">

            <label for="berat_badan">Berat Badan (kg):</label>
            <input type="number" id="berat_badan" name="berat_badan" min="30" max="200">

            <label for="alamat">Alamat Lengkap:</label>
            <textarea id="alamat" name="alamat" required></textarea>

            <label for="desa_kelurahan">Desa/Kelurahan:</label>
            <input type="text" id="desa_kelurahan" name="desa_kelurahan">

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" id="kecamatan" name="kecamatan">

            <label for="kabupaten_kota">Kabupaten/Kota:</label>
            <input type="text" id="kabupaten_kota" name="kabupaten_kota">

            <label for="provinsi">Provinsi:</label>
            <input type="text" id="provinsi" name="provinsi">

            <label for="kode_pos">Kode Pos:</label>
            <input type="text" id="kode_pos" name="kode_pos" maxlength="5">

            <label for="kewarganegaraan">Kewarganegaraan:</label>
            <select id="kewarganegaraan" name="kewarganegaraan">
                <option value="Indonesia">Indonesia</option>
                <option value="Asing">Asing</option>
            </select>

            <label for="no_telp">No. Telp/HP:</label>
            <input type="text" id="no_telp" name="no_telp" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <h3>Informasi Pendidikan & Pekerjaan</h3>
            <label for="pendidikan">Pendidikan Terakhir:</label>
            <select id="pendidikan" name="pendidikan">
                <option value="">-- Pilih Pendidikan --</option>
                <option value="SD">SD</option>
                <option value="SLTP">SLTP</option>
                <option value="SLTA">SLTA</option>
                <option value="D1/D2/D3/SM">D1/D2/D3/SM</option>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
            </select>

            <label for="pekerjaan">Pekerjaan:</label>
            <select id="pekerjaan" name="pekerjaan">
                <option value="">-- Pilih Pekerjaan --</option>
                <option value="Pegawai Negeri Sipil">Pegawai Negeri Sipil</option>
                <option value="TNI/POLRI">TNI/POLRI</option>
                <option value="Dagang">Dagang</option>
                <option value="Tani/Nelayan">Tani/Nelayan</option>
                <option value="Swasta">Swasta</option>
                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                <option value="Pelajar/Mahasiswa">Pelajar/Mahasiswa</option>
                <option value="BUMN/BUMD">BUMN/BUMD</option>
                <option value="Pensiunan">Pensiunan</option>
            </select>

            <h3>Informasi Kesehatan</h3>
            <label for="golongan_darah">Golongan Darah:</label>
            <select id="golongan_darah" name="golongan_darah">
                <option value="">-- Pilih Golongan Darah --</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>

            <label for="status_perkawinan">Status Perkawinan:</label>
            <select id="status_perkawinan" name="status_perkawinan">
                <option value="">-- Pilih Status --</option>
                <option value="Belum Menikah">Belum Menikah</option>
                <option value="Menikah">Menikah</option>
                <option value="Janda/Duda">Janda/Duda</option>
            </select>

            <h3>Ciri-ciri Fisik</h3>
            <label for="ciri_rambut">Ciri Rambut:</label>
            <input type="text" id="ciri_rambut" name="ciri_rambut">

            <label for="ciri_alis">Ciri Alis:</label>
            <input type="text" id="ciri_alis" name="ciri_alis">

            <label for="ciri_hidung">Ciri Hidung:</label>
            <input type="text" id="ciri_hidung" name="ciri_hidung">

            <label for="ciri_muka">Ciri Muka:</label>
            <input type="text" id="ciri_muka" name="ciri_muka">

            <h3>Informasi Mahram</h3>
            <label for="nama_mahram">Nama Mahram:</label>
            <input type="text" id="nama_mahram" name="nama_mahram">

            <label for="hubungan_mahram">Hubungan Mahram:</label>
            <select id="hubungan_mahram" name="hubungan_mahram">
                <option value="">-- Pilih Hubungan --</option>
                <option value="Orang Tua">Orang Tua</option>
                <option value="Anak">Anak</option>
                <option value="Suami/Istri">Suami/Istri</option>
                <option value="Mertua">Mertua</option>
                <option value="Saudara Kandung">Saudara Kandung</option>
            </select>

            <label for="nomor_mahram">Nomor Mahram:</label>
            <input type="text" id="nomor_mahram" name="nomor_mahram">

            <label for="pengalaman_haji">Pengalaman Haji:</label>
            <select id="pengalaman_haji" name="pengalaman_haji">
                <option value="Belum">Belum</option>
                <option value="Pernah">Pernah</option>
            </select>

            <h3>Paket Haji</h3>
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
            </select>

            <label for="tanggal_keberangkatan">Tanggal Keberangkatan:</label>
            <input type="date" id="tanggal_keberangkatan" name="tanggal_keberangkatan" readonly>

            <label for="type_room_pilihan">Tipe Kamar:</label>
            <select id="type_room_pilihan" name="type_room_pilihan" required onchange="updateTotalPrice()">
                <option value="">-- Pilih Tipe Kamar --</option>
            </select>

            <label for="biaya_terbilang">Biaya Paket (Terbilang):</label>
            <input type="text" id="biaya_terbilang" name="biaya_terbilang" readonly>

            <label for="payment_type">Keterangan Pembayaran:</label>
            <select id="payment_type" name="payment_type" required>
                <option value="">-- Pilih Keterangan --</option>
                <option value="DP" <?php echo (isset($inputData['payment_type']) && $inputData['payment_type'] === 'DP') ? 'selected' : ''; ?>>DP (Down Payment)</option>
                <option value="Pelunasan" <?php echo (isset($inputData['payment_type']) && $inputData['payment_type'] === 'Pelunasan') ? 'selected' : ''; ?>>Lunas (Full Payment)</option>
            </select>

            <input type="hidden" id="payment_method" name="payment_method" value="BSI">

            <h3>Dokumen Pendukung</h3>
            <label for="kk_path">Upload Kartu Keluarga (max 2MB):</label>
            <input type="file" id="kk_path" name="kk_path" accept=".pdf,.jpg,.jpeg,.png" required>

            <label for="ktp_path">Upload KTP (max 2MB):</label>
            <input type="file" id="ktp_path" name="ktp_path" accept=".pdf,.jpg,.jpeg,.png" required>

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