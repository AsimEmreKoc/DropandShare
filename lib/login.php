<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    if ($user['is_verified'] == 0) {
        $error = "Lütfen e-posta adresinizi doğrulayın.";
    } else {
        // Giriş başarılı
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        header("Location: dashboard.php");
        exit();
    }
}
else {
        $error = "E-posta veya şifre hatalı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Giriş Yap - Drop&Share</title>
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
            flex-direction: column;
            color: #eee;
        }
        .site-title {
            font-size: 48px;
            font-weight: 900;
            color: #eee;
            margin-bottom: 30px;
            user-select: none;
            letter-spacing: 2px;
        }
        .login-container {
            background: #fafafa;
            width: 380px;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            color: #333;
            transition: box-shadow 0.3s ease;
        }
        .login-container:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        input[type="text"],
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
        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #aaa;
        }
        input[type="text"]:focus,
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
        .register-link {
            text-align: center;
            margin-top: 18px;
            font-size: 15px;
            color: #ccc;
        }
        .register-link a {
            color: #333;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .register-link a:hover {
            color: #555;
            text-decoration: underline;
        }
        @media (max-width: 420px) {
            .login-container {
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
    <div class="login-container">
        <h2>Giriş Yap</h2>
            <?php if (!empty($error)): ?>
        <div style="color: red; text-align: center; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
            <?php endif; ?>
         
<form action="login.php" method="POST">
    <label for="email">E-posta:</label>
    <input type="text" id="email" name="email" placeholder="E-posta adresinizi girin" required autocomplete="email" />
    
    <label for="password">Şifre:</label>
    <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required autocomplete="current-password" />
    
    <button type="submit">Giriş Yap</button>
</form>
<p class="forgot-password" style="text-align: center; margin-top: 12px;">
    <a href="forgot_password.php" style="color: #333; font-weight: 600; text-decoration: none;">Şifremi unuttum?</a>
</p>

        <p class="register-link">Hesabın yok mu? <a href="register.php">Kayıt ol</a></p>
    </div>
</body>
</html>