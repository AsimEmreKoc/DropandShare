<?php
require 'db.php';

$message = "";
$redirect = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() === 1) {
        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $update->execute([$token]);
        $message = "✅ Hesabınız başarıyla doğrulandı. Giriş sayfasına yönlendiriliyorsunuz...";
        $redirect = true;
    } else {
        $message = "❌ Geçersiz doğrulama bağlantısı.";
    }
} else {
    $message = "❗ Doğrulama kodu bulunamadı.";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Doğrulama</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .message-box {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .message-box h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .message-box a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .message-box a:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2><?= htmlspecialchars($message) ?></h2>
        <a href="login.php">Giriş Sayfasına Geri Dön</a>
    </div>

    <?php if ($redirect): ?>
    <script>
        setTimeout(() => {
            window.location.href = "login.php";
        }, 4000); // 4 saniye sonra yönlendir
    </script>
    <?php endif; ?>
</body>
</html>
