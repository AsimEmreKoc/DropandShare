<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_id'])) {
    $user_id = $_SESSION['user_id'];
    $file_id = $_POST['file_id'];

    
    $stmt = $conn->prepare("SELECT * FROM deleted_files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

  
        $shared_path = __DIR__ . "/shared/" . $file['unique_name'];
        if (file_exists($shared_path)) {
            unlink($shared_path);
        }


        $del = $conn->prepare("DELETE FROM deleted_files WHERE id = ? AND user_id = ?");
        $del->execute([$file_id, $user_id]);
    }
}

header("Location: deleted_files.php");
exit();
