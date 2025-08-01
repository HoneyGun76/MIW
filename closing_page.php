<?php
session_start();

// Validate the payment success session data
$isValidPayment = (
    isset($_SESSION['payment_success']) &&
    is_array($_SESSION['payment_success']) &&
    !empty($_SESSION['payment_success']['status']) &&
    $_SESSION['payment_success']['status'] === true &&
    !empty($_SESSION['payment_success']['timestamp']) &&
    (time() - $_SESSION['payment_success']['timestamp']) < 300 // 5 minute window
);

if (!$isValidPayment) {
    // Clear invalid session data
    unset($_SESSION['payment_success']);
    header('Location: index.php');
    exit;
}

// Clear the session data immediately after use
unset($_SESSION['payment_success']);

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
    <title>Konfirmasi Pembayaran</title>
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
            color: #4CAF50;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .invoice-number {
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
            border-top: 5px solid #4CAF50;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="loader"></div>
        <h1>Terima Kasih</h1>
        <p>Pembayaran Anda telah berhasil dikonfirmasi.</p>
        <?php if (isset($_SESSION['payment_success']['email_status'])): ?>
        <p><?php echo htmlspecialchars($_SESSION['payment_success']['email_status']); ?></p>
        <?php endif; ?>
        
        <p>Tim kami akan melakukan verifikasi pembayaran dan menghubungi Anda dalam waktu 1x24 jam.</p>
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