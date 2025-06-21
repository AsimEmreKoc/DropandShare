<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM deleted_files WHERE user_id = ? ORDER BY deleted_at DESC");
$stmt->execute([$user_id]);
$deleted_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Silinen Dosyalar - Cloud Drive</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">☁️</div>
        <nav>
            <a href="dashboard.php">🏠 Ana Sayfa</a>
            <a href="shared.php">📁 Paylaşılan</a>
            <a href="deleted_files.php" class="active">🗑️ Silinenler</a>
            <a href="logout.php" class="logout">🚪 Çıkış</a>
        </nav>
    </div>

    <div class="main">
        <div class="topbar">
            <h2>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
        </div>

        <div class="files">
            <h3>🗑️ Silinen Dosyalar</h3>

            <?php if (count($deleted_files) > 0): ?>
            <table class="file-table">
                <thead>
                    <tr>
                        <th>Dosya Adı</th>
                        <th>Silinme Tarihi</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deleted_files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                            <td><?php echo $file['deleted_at']; ?></td>
                            <td>
                                <form action="restore_file.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                    <button type="submit" class="restore-btn">Geri Yükle</button>
                                </form>
                                <form action="permanent_delete.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                    <button type="submit" class="delete-permanent-btn">Kalıcı Sil</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else: ?>
                <p>Silinen dosya bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
