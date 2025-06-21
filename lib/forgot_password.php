<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$error = "";
$success = "";

 use PHPMailer\PHPMailer\PHPMailer;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';
   

    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 30 MINUTE) WHERE email = ?")
            ->execute([$token, $email]);

        $resetLink = "http://localhost:8000/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'emrekoc2834@gmail.com';
            $mail->Password = 'pzdu nzbi bjmc klry';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@dropandshare.com', 'Drop&Share');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Sifre Sifirlama Talebi';
            $mail->Body    = "
                <p>Merhaba,</p>
                <p>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>Bu bağlantı 30 dakika içinde geçersiz olacaktır.</p>";

            $mail->send();
            $success = "Sıfırlama bağlantısı e-posta adresinize gönderildi.";
        } catch (Exception $e) {
            $error = "E-posta gönderilemedi: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Bu e-posta adresi sistemde kayıtlı değil.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Şifremi Unuttum - Drop&Share</title>
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
    }
    .container {
      background: #fafafa;
      width: 380px;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      color: #333;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-weight: 700;
      font-size: 24px;
    }
    input[type="email"] {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      border: 1.8px solid #ccc;
      font-size: 16px;
      color: #333;
      transition: 0.3s;
    }
    input[type="email"]:focus {
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
      font-size: 17px;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #555;
    }
    .back-btn {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
      background-color: #e0e0e0;
      padding: 10px;
      border-radius: 8px;
      transition: 0.2s ease;
    }
    .back-btn:hover {
      background-color: #d0d0d0;
    }
    .message {
      text-align: center;
      margin-top: 15px;
      color: green;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Şifremi Unuttum</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Kayıtlı e-posta adresiniz" required />
      <button type="submit">Sıfırlama Bağlantısı Gönder</button>
    </form>
    <a href="login.php" class="back-btn">← Girişe Geri Dön</a>
    <?php if (!empty($success)): ?>
      <div class="success" style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
  </div>
</body>
</html>

