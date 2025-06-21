<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Drop&Share | Dosya Yükle</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;600;900&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #333;
            color: #eee;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .upload-hero {
            text-align: center;
            padding: 80px 20px 40px;
            width: 100%;
            max-width: 600px;
        }

        .upload-hero h1 {
            font-weight: 900;
            font-size: 3rem;
            margin-bottom: 10px;
            letter-spacing: 3px;
            user-select: none;
        }

        .upload-hero p {
            font-weight: 300;
            font-size: 1.2rem;
            color: #ccc;
            margin: 0;
        }

        .upload-container {
            background-color: #222;
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.6);
            width: 100%;
            max-width: 480px;
            user-select: none;
        }

        .upload-container h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #eee;
            text-align: center;
        }

        form.upload-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: 600;
            font-size: 1rem;
            color: #ddd;
            user-select: none;
        }

        input[type="file"] {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #555;
            background-color: #444;
            color: #eee;
            cursor: pointer;
            font-size: 1rem;
        }

        input[type="file"]::-webkit-file-upload-button {
            cursor: pointer;
            background-color: #555;
            border: none;
            padding: 8px 16px;
            color: #eee;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            font-weight: 600;
        }
        input[type="file"]::-webkit-file-upload-button:hover {
            background-color: #666;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            cursor: pointer;
            user-select: none;
            color: #ccc;
            font-size: 0.95rem;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #eee;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #eee;
            color: #333;
            font-weight: 700;
            padding: 15px 0;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        button[type="submit"]:hover {
            background-color: #555;
            color: #eee;
            box-shadow: 0 8px 20px rgba(0,0,0,0.5);
        }

        .back-link {
            margin-top: 25px;
            text-align: center;
        }

        .back-link a {
            color: #eee;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
            user-select: none;
        }

        .back-link a:hover {
            color: #bbb;
        }

        @media (max-width: 500px) {
            .upload-hero h1 {
                font-size: 2.4rem;
            }
            .upload-container {
                padding: 25px 15px;
            }
            button[type="submit"] {
                font-size: 1rem;
                padding: 12px 0;
            }
        }
    </style>
</head>
<body>
    <div class="upload-hero">
        <h1>Drop&amp;Share</h1>
        <p>Dosyalarınızı güvenli bir şekilde yükleyin ve paylaşın.</p>
    </div>

    <div class="upload-container">
        <h2>Dosya Yükleme Paneli</h2>
        <form action="upload_process.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="file">📁 Dosya Seç</label>
            <input type="file" name="file[]" id="file" multiple required>

            <label class="checkbox-label" for="is_shared">
                <input type="checkbox" name="is_shared" id="is_shared" value="1">
                Dosyayı paylaşıma aç
            </label>

            <button type="submit" name="submit">🚀 Yükle</button>
        </form>

        <p class="back-link"><a href="dashboard.php">← Kontrol Paneline Dön</a></p>
    </div>
    <script>
document.querySelector('.upload-form').addEventListener('submit', function(e) {
    const files = document.getElementById('file').files;
    const maxSize = 8 * 1024 * 1024; // 8 MB

    for (let i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
            alert(`"${files[i].name}" dosyası 8 MB sınırını aşıyor.`);
            e.preventDefault(); // Form gönderimini engelle
            return;
        }
    }
});
</script>

</body>
</html>



