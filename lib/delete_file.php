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

 
    $stmt = $conn->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        $deleted_at = date('Y-m-d H:i:s');

        
        $insert = $conn->prepare("
            INSERT INTO deleted_files 
            (original_id, user_id, file_name, file_path, file_size, file_type, is_shared, uploaded_at, deleted_at,unique_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $insert->execute([
            $file['id'],        
            $file['user_id'],    
            $file['file_name'],  
            $file['file_path'],  
            $file['file_size'],  
            $file['file_type'],  
            $file['is_shared'],  
            $file['uploaded_at'],
            $deleted_at,         
            $file['unique_name']
        ]);

    
        $del = $conn->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
        $del->execute([$file_id, $user_id]);
    }
}

header("Location: dashboard.php");
exit();
