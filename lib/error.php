<?php
$message = $_GET['msg'] ?? 'Bilinmeyen bir hata oluştu.';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hata</title>
    <style>
        body {
            background: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .error-box {
            background: #fff;
            border-left: 6px solid #dc3545;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            border-radius: 12px;
            max-width: 600px;
            text-align: center;
        }

        .error-box h1 {
            font-size: 36px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-box p {
            font-size: 18px;
            color: #333;
        }

        .back-btn {
            margin-top: 25px;
            display: inline-block;
            background: #dc3545;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>❌ Hata</h1>
        <p><?= htmlspecialchars($message) ?></p>
        <a href="index.php" class="back-btn">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
