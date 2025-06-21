<?php
include 'db.php';

$stmt = $conn->prepare("DELETE FROM deleted_files WHERE deleted_at < NOW() - INTERVAL 30 DAY");
$stmt->execute();
?>
