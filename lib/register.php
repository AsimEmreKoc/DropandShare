<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
require 'db.php';

$error = ""; 
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tüm alanları doldurunuz.";
    } elseif (strlen($password) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $error = "Bu e-posta adresi zaten kayıtlı.";
        } else {
            $token = bin2hex(random_bytes(16));

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashedPassword, $token])) {
                
                $verificationLink = "http://localhost:8000/verify.php?token=" . $token;

                
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';      
                    $mail->SMTPAuth = true;
                    $mail->Username = 'emrekoc2834@gmail.com';  
                    $mail->Password = '*****';                 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Alıcı ve gönderici
                    $mail->setFrom('no-reply@DropandShare.com', 'Drop&Share');
                    $mail->addAddress($email, $username);

                    // İçerik
                    $mail->isHTML(true);
                    $mail->Subject = 'Drop&Share Hesap Dogrulama';
                    $mail->Body    = "
                        <p>Merhaba <b>{$username}</b>,</p>
                        <p>Kayıt işlemini tamamlamak için aşağıdaki bağlantıya tıklayın:</p>
                        <p><a href='{$verificationLink}'>{$verificationLink}</a></p>
                        <p>Teşekkürler!</p>
                    ";
                    $mail->AltBody = "Merhaba {$username},\nKayıt işlemini tamamlamak için aşağıdaki bağlantıya tıklayın:\n{$verificationLink}\nTeşekkürler!";

                    $mail->send();

                    $success = "Kayıt başarılı! Lütfen e-posta adresinizi doğrulayın.";
                    

                } catch (Exception $e) {
                    $error = "E-posta gönderilirken hata oluştu: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Kayıt sırasında bir hata oluştu.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Kayıt Ol - Drop&Share</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
            margin: 0; 
            padding: 0;
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
        .form-container {
            background: #fafafa;
            width: 380px;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            color: #333;
            transition: box-shadow 0.3s ease;
        }
        .form-container:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }
        h2 {
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
        input[type="email"],
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
        input::placeholder {
            color: #aaa;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
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
        .link {
            text-align: center;
            margin-top: 18px;
            font-size: 15px;
            color: #ccc;
        }
        .link a {
            color: #333;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .link a:hover {
            color: #555;
            text-decoration: underline;
        }
        @media (max-width: 420px) {
            .form-container {
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
    <div class="form-container">
        <h2>Kayıt Ol</h2>
        <?php if (!empty($success)): ?>
    <div style="color: green; text-align: center; margin-bottom: 15px;">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>
        <?php if (!empty($error)): ?>
        <div style="color: red; text-align: center; margin-bottom: 15px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" placeholder="Kullanıcı adınızı girin" required autocomplete="username" />
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email adresinizi girin" required autocomplete="email" />
            
            <label for="password">Şifre:</label>
            <input type="password" id="password" name="password" placeholder="Şifrenizi girin" required autocomplete="new-password" />
            
            <button type="submit">Kayıt Ol</button>
        </form>
        <p class="link">Zaten hesabın var mı? <a href="login.php">Giriş yap</a></p>
    </div>
</body>
</html>

