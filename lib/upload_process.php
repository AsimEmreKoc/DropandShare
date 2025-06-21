<?php
session_start();
require 'db.php';




if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$messages = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $maxSize = 8 * 1024 * 1024;
    foreach ($_FILES['file']['size'] as $key => $size) {
    if ($size > $maxSize) {
        die("Hata: Yüklemeye çalıştığınız dosya 8MB'den büyük.");
    }
}
    $userId = $_SESSION['user_id'];
    $isShared = isset($_POST['is_shared']) ? 1 : 0;
    $username = $_SESSION['username'];
    $uploadDir = "uploads/" . $username . "/";

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $files = $_FILES["file"];
    $successCount = 0;

    for ($i = 0; $i < count($files["name"]); $i++) {
        $fileName = basename($files["name"][$i]);
        $fileType = $files["type"][$i];
        $fileSize = $files["size"][$i];
        $tmpName  = $files["tmp_name"][$i];
        
        $timestampedName = time() . "_" . $fileName;
        $filePath = $uploadDir . $timestampedName;
        $uniqueName = uniqid() . "_" . $fileName;

        if (move_uploaded_file($tmpName, $filePath)) {
            $stmt = $conn->prepare("INSERT INTO files (user_id, file_name, file_path, file_type, file_size, is_shared, unique_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $fileName, $filePath, $fileType, $fileSize, $isShared, $uniqueName]);
            $successCount++;
        }
    }

    if ($successCount > 0) {
        $success = true;
        $message = "$successCount dosya başarıyla yüklendi.";
    } else {
        $message = "Hiçbir dosya yüklenemedi.";
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yükleme Sonucu | Drop&Share</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .message-box {
            background-color: #fff;
            padding: 40px 60px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .message-box h2 {
            color: <?= $success ? '#4CAF50' : '#F44336' ?>;
            margin-bottom: 10px;
        }
        .message-box ul {
            list-style-type: none;
            padding: 0;
            color: #555;
        }
        .message-box ul li {
            margin: 5px 0;
        }
        .message-box a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            background-color: #0066ff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            transition: 0.3s ease;
        }
        .message-box a:hover {
            background-color: #004ec2;
        }
    </style>
</head>
<body>

<div class="message-box">
    <h2><?= $success ? 'Yükleme Tamamlandı' : '❌ Hatalar Oluştu' ?></h2>
    <ul>
        <?php foreach ($messages as $msg): ?>
            <li><?= htmlspecialchars($msg) ?></li>
        <?php endforeach; ?>
    </ul>
    <a href="dashboard.php">Panele Dön</a>
</div>

</body>
</html>
