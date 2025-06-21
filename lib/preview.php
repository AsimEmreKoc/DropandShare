<?php
require 'db.php';
session_start();

$fileId = $_GET['id'] ?? null;

if (!$fileId) {
    header("Location: error.php?msg=" . urlencode("Dosya ID belirtilmedi."));
    exit;
}

// Dosyayı çek
$stmt = $conn->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    header("Location: error.php?msg=" . urlencode("Dosya bulunamadı."));
    exit;
}

if ($file['is_shared'] != 1) {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $file['user_id']) {
        header("Location: error.php?msg=" . urlencode("Bu dosyayı görüntüleme izniniz yok."));
        exit;
    }
}

$filePath = $file['file_path'];
if (!file_exists($filePath)) {
    header("Location: error.php?msg=" . urlencode("Dosya sunucuda bulunamadı."));
    exit;
}
$fileUrl = htmlspecialchars($filePath);
$fileName = htmlspecialchars($file['file_name']);
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$fileSize = htmlspecialchars($file['file_size']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dosya Önizleme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #333;
            text-align: center;
            padding: 50px;
        }
        .preview-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            display: inline-block;
            max-width: 800px;
        }
        img, iframe {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 20px;
        }
        .download-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }
        .file-list {
            text-align: left;
            margin-top: 20px;
            background: #f1f1f1;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <h2>Dosya Önizleme</h2>
<p><strong>Dosya:</strong> <?= $fileName ?> (<?= round($fileSize / (1024 * 1024), 2) ?> MB)</p>

        <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
            <img src="<?= $fileUrl ?>" alt="Görsel Dosya">

        <?php elseif ($fileExtension === 'pdf'): ?>
            <iframe src="<?= $fileUrl ?>" width="100%" height="600px"></iframe>

        <?php elseif (in_array($fileExtension, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'])): ?>
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode("http://localhost:8000/" . $filePath) ?>" width="100%" height="600px" frameborder="0"></iframe>

        <?php elseif ($fileExtension === 'zip'): ?>
            <div class="file-list">
                <h3>ZIP İçeriği:</h3>
                <ul>
                    <?php
                    $zip = new ZipArchive;
                    if ($zip->open($filePath) === TRUE) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $filename = $zip->getNameIndex($i);
                            echo "<li>" . htmlspecialchars($filename) . "</li>";
                        }
                        $zip->close();
                    } else {
                        echo "<li>ZIP dosyası açılamadı.</li>";
                    }
                    ?>
                </ul>
            </div>

        <?php elseif ($fileExtension === 'rar' && class_exists('RarArchive')): ?>
            <div class="file-list">
                <h3>RAR İçeriği:</h3>
                <ul>
                    <?php
                    $rar = RarArchive::open($filePath);
                    if ($rar) {
                        $entries = $rar->getEntries();
                        foreach ($entries as $entry) {
                            echo "<li>" . htmlspecialchars($entry->getName()) . "</li>";
                        }
                        $rar->close();
                    } else {
                        echo "<li>RAR dosyası açılamadı.</li>";
                    }
                    ?>
                </ul>
            </div>

        <?php else: ?>
            <p>Bu dosya türü önizlemeyi desteklemiyor.</p>
        <?php endif; ?>

        <a class="download-btn" href="download.php?id=<?= $fileId ?>">Dosyayı İndir</a>
    </div>
</body>
</html>
