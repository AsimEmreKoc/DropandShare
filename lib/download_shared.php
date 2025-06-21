<?php
$uniqueName = $_GET['file'] ?? '';
$sharedPath = "shared/" . $uniqueName;

if ($uniqueName && file_exists($sharedPath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($sharedPath) . '"');
    readfile($sharedPath);
    exit;
} else {
    echo "Paylaşılan dosya bulunamadı.";
}
?>
