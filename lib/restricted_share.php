<?php
require 'db.php';
session_start();

$fileId = $_POST['file_id'] ?? null;
$email = $_POST['email'] ?? null;
$new_mode = 'restricted';

if (!$fileId || !$email) {
    echo json_encode(['success' => false, 'message' => 'Eksik parametre.']);
    exit;
}

try {
    // Veritabanı işlemlerini bir bütün (transaction) olarak başlat
    $conn->beginTransaction();

    // shared_access tablosuna kayıt
    $stmt = $conn->prepare("INSERT INTO shared_access (file_id, email) VALUES (?, ?)");
    $stmt->execute([$fileId, $email]);

    // files tablosunda share_mode'u güncelle
    $stmt2 = $conn->prepare("UPDATE files SET share_mode = ? WHERE id = ?");
    $stmt2->execute([$new_mode, $fileId]);

    $conn->commit(); // başarılıysa işlemleri onayla

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $conn->rollBack(); // hata olursa işlemleri geri al

    // Eğer aynı e-posta zaten eklenmişse (örneğin UNIQUE kısıtlaması varsa)
    if ($e->getCode() == '23000') {
        echo json_encode(['success' => false, 'message' => 'Bu e-posta zaten yetkilendirilmiş.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
    }
}
