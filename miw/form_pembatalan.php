<?php
require_once "config.php";

// Retrieve errors and input data from URL if any
$errors = isset($_GET['errors']) ? $_GET['errors'] : null;
$inputData = isset($_GET['input']) ? json_decode(urldecode($_GET['input']), true) : [];
$success = isset($_GET['success']) ? $_GET['success'] : null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pembatalan Keikutsertaan</title>
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
                alert("Permohonan pembatalan berhasil diajukan! Kami akan menghubungi Anda via email/telepon.");
            }
        };

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

            // No Paspor validation
            const noPaspor = document.getElementById('no_paspor').value;
            if (!noPaspor) {
                alert('Nomor paspor harus diisi');
                return false;
            }

            // File validation
            const kwitansiFile = document.getElementById('kwitansi_path').files[0];
            if (!kwitansiFile) {
                alert('Harap upload kwitansi pembayaran');
                return false;
            }

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(kwitansiFile.type)) {
                alert('Hanya file JPG, PNG, atau PDF yang diperbolehkan');
                return false;
            }
            
            if (kwitansiFile.size > 2 * 1024 * 1024) { // 2MB limit
                alert('Ukuran file tidak boleh melebihi 2MB');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <header>
        <h1>Form Pembatalan Keikutsertaan</h1>
    </header>
    <main>
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
    </main>
</body>
</html>