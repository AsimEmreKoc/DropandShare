<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share_file_id'])) {
    $file_id = $_POST['share_file_id'];

 
    $stmt = $conn->prepare("UPDATE files SET is_shared = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$file_id, $user_id]);
}


$stmt = $conn->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC");
$stmt->execute([$user_id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Cloud Drive</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo">☁️ Drop&Share</div>
        <nav>
            <a href="dashboard.php" class="active">🏠 Ana Sayfa</a>
            <a href="shared.php">📁 Paylaşılan</a>
            <a href="deleted_files.php">🗑️ Silinenler</a>
            <a href="logout.php" class="logout">🚪 Çıkış</a>
        </nav>
    </div>

    <div class="main">
        <div class="topbar">
            <h2>Merhaba, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
            <div class="buttons">
                <a href="upload.php"><button>📤 Dosya Yükle</button></a>
            </div>
        </div>

        <div class="files">
            <h3>📄 Dosyalarım</h3>

            <div class="search-container">
    <input type="text" id="searchInput" placeholder="Dosya ara...">
</div>

            <?php if (count($files) > 0): ?>
<table class="file-table">
    <thead>
        <tr>
            <th>Dosya Adı</th>
            <th>Yüklenme Tarihi</th>
            <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($files as $file): ?>
            <tr>
                <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                <td><?php echo $file['uploaded_at']; ?></td>
                <td>
                    <form method="get" action="preview.php" target="_blank" style="display:inline;">
    <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
    <button type="submit" class="open-btn">📂 Aç</button>
</form>

    <form action="delete_file.php" method="POST" style="display:inline;">
        <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
        <button type="submit" class="delete-btn">🗑️ Sil</button>
    </form>

<?php if (!$file['is_shared']): ?>
<form method="post" action="share_file.php" style="display:inline;">
    <input type="hidden" name="share_file_id" value="<?php echo $file['id']; ?>">
    <button type="submit" class="share-btn">🔗 Paylaş</button>
</form>

<?php else: ?>
    <a href="#" 
   class="view-link" 
   data-file-id="<?php echo $file['id']; ?>" 
   data-file-url="shared/<?php echo $file['unique_name']; ?>">
   🔗 Paylaşım Ayarları 
</a>
<?php endif; ?>



                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

            <?php else: ?>
                <p>Henüz dosya yüklemediniz.</p>
            <?php endif; ?>
        </div>
    </div>


<!-- Paylaşım linki popup'ı -->
<div id="sharePopup" class="popup-overlay" style="display:none;">
  <div class="popup-content">
    <h3>Paylaşılan Dosya Linki</h3>
    <input type="text" id="shareLink" readonly>
    <button id="copyBtn">Kopyala</button>
    <button id="unshareBtn">Paylaşımı Kaldır</button>
    <button id="restrictedBtn">Kısıtlı Paylaşım</button>
    <button id="closePopup">Kapat</button>

    <div id="restrictedShareForm" style="display:none; margin-top:15px;">
      <input type="email" id="restrictedEmail" placeholder="E-posta adresi giriniz">
      <button id="confirmRestrictedShare">Onayla</button>
      <button id="cancelRestrictedShare">İptal</button>
    </div>
  </div>
</div>

<script src="search.js"></script>
<script src="pop-up.js"></script>



</body>
</html>
