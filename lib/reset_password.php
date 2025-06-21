<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"] ?? '';
    $newPassword = $_POST["new_password"] ?? '';

    if (strlen($newPassword) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);

        if ($stmt->rowCount() === 1) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
            $update->execute([$hashedPassword, $token]);
            $success = "Şifreniz başarıyla güncellendi. <a href='login.php'>Giriş Yap</a>";
        } else {
            $error = "Geçersiz veya süresi dolmuş bağlantı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Yeni Şifre - Drop&Share</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0; padding: 0;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #eee;
            flex-direction: column;
            padding: 20px;
        }
        .site-title {
            font-size: 48px;
            font-weight: 900;
            color: #eee;
            margin-bottom: 30px;
            user-select: none;
            letter-spacing: 2px;
        }
        .container {
            background: #fafafa;
            width: 380px;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            color: #333;
            transition: box-shadow 0.3s ease;
        }
        .container:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1.8px solid #ccc;
            font-size: 16px;
            transition: 0.3s;
            color: #333;
        }
        input[type="password"]::placeholder {
            color: #aaa;
        }
        input[type="password"]:focus {
            border-color: #333;
            outline: none;
            box-shadow: 0 0 8px #33399980;
        }
        button {
            width: 100%;
            padding: 14px 0;
            background: #333;
            color: white;
            border: none;
            font-weight: 600;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #555;
        }
        .message {
            margin-top: 15px;
            text-align: center;
        }
        .error {
            color: #e74c3c;
        }
        .success {
            color: #27ae60;
        }
        a {
            color: #333;
            font-weight: 600;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @media (max-width: 420px) {
            .container {
                width: 90%;
                padding: 30px 20px;
            }
            .site-title {
                font-size: 36px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="site-title">Drop&amp;Share</div>
    <div class="container">
        <h2>Yeni Şifre Belirle</h2>

        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" novalidate>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
            <input type="password" name="new_password" placeholder="Yeni şifrenizi girin" required minlength="6" />
            <button type="submit">Şifreyi Güncelle</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
