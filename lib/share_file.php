<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['share_file_id'])) {
    $fileId = $_POST['share_file_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$fileId, $userId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $uniqueName = uniqid() . "_" . basename($file['file_name']);
        $sharedDir = __DIR__ . "/shared/";
        if (!file_exists($sharedDir)) {
            mkdir($sharedDir, 0777, true);
        }

        $sourcePath = __DIR__ . "/" . $file['file_path'];
        $destPath = $sharedDir . $uniqueName;

        if (file_exists($sourcePath)) {
            if (copy($sourcePath, $destPath)) {
$update = $conn->prepare("UPDATE files SET is_shared = 1, unique_name = ?, share_mode = 'public' WHERE id = ?");
$update->execute([$uniqueName, $fileId]);

            
            }
        }
    }

    // Hata durumunda geri gönder
    header("Location: dashboard.php");
    exit();
}
?>