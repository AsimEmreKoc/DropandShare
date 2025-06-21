<?php
require 'db.php';
session_start();

$fileId = $_GET['id'] ?? null;

if (!$fileId) {
    die("Dosya ID belirtilmedi.");
}

$stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    die("Dosya bulunamadı.");
}

// Paylaşılan mı kontrolü
if ($file['is_shared'] != 1) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $file['user_id']) {
        die("Bu dosyayı indirme izniniz yok.");
    }
}

$filePath = $file['file_path'];
$fileName = basename($file['file_name']);

if (!file_exists($filePath)) {
    die("Dosya sunucuda bulunamadı.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
