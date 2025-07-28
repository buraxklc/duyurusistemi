<?php
// admin/index.php - ÇALIŞAN VERSİYON
session_start();
require_once '../includes/config.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Test dosyasında çalışan aynı kod
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Hatalı kullanıcı adı veya şifre!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Giriş - Perfect Duyuru Admin Panel v1.0</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            font-size: 11px;
            background: #E5E5E5;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: #FFFFFF;
            border: 1px solid #999999;
            width: 400px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
        }
        
        .login-header {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #1A5490;
            text-align: center;
        }
        
        .login-content {
            padding: 30px;
        }
        
        .welcome-text {
            text-align: center;
            margin-bottom: 25px;
            color: #333333;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .welcome-text h3 {
            color: #0066CC;
            font-size: 16px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333333;
            font-size: 11px;
        }
        
        .form-input {
            width: 100%;
            border: 1px solid #CCCCCC;
            padding: 8px;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 11px;
            box-sizing: border-box;
            background: #FAFAFA;
        }
        
        .form-input:focus {
            border-color: #4A90E2;
            background: #FFFFFF;
            outline: none;
        }
        
        .login-btn {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            border: 1px solid #1A5490;
            color: white;
            padding: 10px 20px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .login-btn:hover {
            background: linear-gradient(to bottom, #3A80D2, #1A61A5);
        }
        
        .alert-error {
            background: #F2DEDE;
            border: 1px solid #EBCCD1;
            color: #A94442;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 11px;
            border-radius: 3px;
        }
        
        .login-footer {
            background: #F0F0F0;
            border-top: 1px solid #CCCCCC;
            padding: 10px;
            font-size: 10px;
            color: #666666;
            text-align: center;
        }
        
        .back-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-link a {
            color: #0066CC;
            text-decoration: none;
            font-size: 11px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Header -->
    <div class="login-header">
        Perfect Duyuru Admin Panel v1.0
    </div>
    
    <!-- Content -->
    <div class="login-content">
        <div class="welcome-text">
            <h3>Yönetici Girişi</h3>
            Duyuru yönetim sistemine hoşgeldiniz.<br>
            Devam etmek için giriş yapınız.
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert-error">
                <strong>Giriş Hatası!</strong> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Kullanıcı Adı</label>
                <input type="text" name="username" class="form-input" placeholder="Kullanıcı adınızı girin..." value="admin" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-input" placeholder="Şifrenizi girin..." value="123456" required>
            </div>
            
            <button type="submit" name="login" class="login-btn">
                Giriş Yap
            </button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">« Ana Sayfaya Dön</a>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="login-footer">
        Perfect Duyuru Admin Panel v1.0 | Powered by PHP & MySQL | © 2024 Tüm hakları saklıdır.
    </div>
</div>

</body>
</html>
