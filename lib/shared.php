<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Kullanıcının e-postasını al
$stmtEmail = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmtEmail->execute([$user_id]);
$userEmail = $stmtEmail->fetchColumn();

// Paylaşılan dosyaları getir (kendi paylaştıkları + kendisiyle paylaşılanlar)
$stmt = $conn->prepare("
    SELECT f.*, u.email AS owner_email FROM files f
    LEFT JOIN users u ON f.user_id = u.id
    WHERE (f.user_id = ? AND f.is_shared = 1)
    OR (f.id IN (
    SELECT file_id FROM shared_access WHERE email = ?
) AND f.is_shared = 1)
");
$stmt->execute([$user_id, $userEmail]);
$sharedFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Paylaşılan Dosyalar</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="sidebar">
    <div class="logo">☁️</div>
    <nav>
        <a href="dashboard.php">🏠 Ana Sayfa</a>
        <a href="shared.php" class="active">📁 Paylaşılan</a>
        <a href="deleted_files.php">🗑️ Silinenler</a>
        <a href="logout.php" class="logout">🚪 Çıkış</a>
    </nav>
</div>

<div class="main">
    <div class="topbar">
        <h2>🔗 Paylaşılan Dosyalar</h2>
        <div class="buttons">
            <a href="upload.php"><button>📤 Dosya Yükle</button></a>
        </div>
    </div>

    <div class="files">
        <div class="search-container">
    <input type="text" id="searchInput" placeholder="Dosya ara...">
</div>
        <?php if (count($sharedFiles) > 0): ?>
            <table class="file-table">
                <thead>
                <tr>
                    <th>Dosya Adı</th>
                    <th>Yüklenme Tarihi</th>
                    <th>Paylaşan</th> 
                    <th>Paylaşım Linki</th>
                    <th>İşlem</th>
                </tr>
                </thead>
<tbody>
<?php foreach ($sharedFiles as $file): ?>
    <tr>
        <td><?php echo htmlspecialchars($file['file_name']); ?></td>
        <td><?php echo $file['uploaded_at']; ?></td>
        <td>
    <?php
    if ($file['user_id'] == $user_id) {
        echo "Sen";
    } else {
        echo htmlspecialchars($file['owner_email']);
    }
    ?>
</td>
        <td>
            <a href="preview_shared.php?id=<?php echo $file['id']; ?>" target="_blank">
                <?php echo $_SERVER['HTTP_HOST'] . '/preview_shared.php?id=' . $file['id']; ?>
            </a>
        </td>
        <td>
            <form method="get" action="preview_shared.php" target="_blank" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
                <button type="submit" class="open-btn">📂 Aç</button>
            </form>
            |
<a href="<?php echo $file['file_path']; ?>" download="<?php echo htmlspecialchars($file['file_name']); ?>">
    ⬇️ İndir
</a>

<?php if ($file['user_id'] == $user_id): ?>
    <a href="#" 
       class="view-link" 
       data-file-id="<?php echo $file['id']; ?>" 
       data-file-url="shared/<?php echo $file['unique_name']; ?>">
       🔗 Paylaşım Ayarları 
    </a>
<?php else: ?>
    
<?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

            </table>
        <?php else: ?>
            <p>Henüz paylaşılmış dosyanız yok.</p>
        <?php endif; ?>
    </div>
</div>
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
