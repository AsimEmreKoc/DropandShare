<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Oturum yok']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $user_id = $_SESSION['user_id'];
    $file_id = $_POST['file_id'];

    // Dosyayı kontrol et
    $stmt = $conn->prepare("SELECT unique_name FROM files WHERE id = ? AND user_id = ? AND is_shared = 1");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $sharedFilePath = __DIR__ . "/shared/" . $file['unique_name'];

        // Veritabanını güncelle
        $update = $conn->prepare("UPDATE files SET is_shared = 0, unique_name = NULL WHERE id = ? AND user_id = ?");
        $result = $update->execute([$file_id, $user_id]);

        if ($result) {
            // shared_access tablosundaki erişim izinlerini kaldır
            $deleteAccess = $conn->prepare("DELETE FROM shared_access WHERE file_id = ?");
            $deleteAccess->execute([$file_id]);

            // Dosya klasörden silinsin
            if (file_exists($sharedFilePath)) {
                unlink($sharedFilePath);
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Veritabanı güncellenemedi']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dosya bulunamadı veya paylaşılmamış']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
}
