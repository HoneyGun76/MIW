<?php
session_start();

// Validate the cancellation success session data
$isValidCancellation = (
    isset($_SESSION['cancellation_success']) &&
    is_array($_SESSION['cancellation_success']) &&
    !empty($_SESSION['cancellation_success']['status']) &&
    $_SESSION['cancellation_success']['status'] === true &&
    !empty($_SESSION['cancellation_success']['timestamp']) &&
    (time() - $_SESSION['cancellation_success']['timestamp']) < 300 // 5 minute window
);

if (!$isValidCancellation) {
    // Clear invalid session data
    unset($_SESSION['cancellation_success']);
    header('Location: index.php');
    exit;
}

// Clear the session data immediately after use
unset($_SESSION['cancellation_success']);

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembatalan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        h1 {
            color: #2196F3;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .reference-number {
            font-weight: bold;
            color: #333;
            background-color: #f0f0f0;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 0;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #2196F3;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: 0 auto 30px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .contact-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loader"></div>
        <h1>Pengajuan Pembatalan Diterima</h1>
        <p>Permohonan pembatalan Anda telah berhasil kami terima.</p>
        
        <?php if (isset($_SESSION['cancellation_ref'])): ?>
            <p>Nomor Referensi: <span class="reference-number"><?= htmlspecialchars($_SESSION['cancellation_ref']) ?></span></p>
        <?php endif; ?>
        
        <p>Tim kami akan memproses permohonan pembatalan ini dan menghubungi Anda dalam waktu 1x24 jam untuk konfirmasi lebih lanjut.</p>
        
        <div class="contact-info">
            <p>Untuk pertanyaan lebih lanjut, silakan hubungi:</p>
            <p><strong>Email:</strong> cs@madinahimanwisata.com<br>
            <strong>Telepon:</strong> 021-29044541</p>
        </div>
        
        <p>Halaman ini akan otomatis tertutup dalam <span id="countdown">10</span> detik...</p>
    </div>

    <script>
        // Countdown timer
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(function() {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = 'https://hajikhusus.web.id';
            }
        }, 1000);

        // Prevent form resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>