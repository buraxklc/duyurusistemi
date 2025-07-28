<?php
// admin/add-announcement.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_once '../includes/functions.php';
checkAdmin();

$success = '';
$error = '';

if (isset($_POST['add_announcement'])) {
    $title = cleanInput($_POST['title']);
    $content = cleanInput($_POST['content']);
    $category = cleanInput($_POST['category']);
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
        if (!$image) {
            $error = "Resim yüklenirken hata oluştu!";
        }
    }
    
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, category, priority, status, start_date, end_date, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $category, $priority, $status, $start_date, $end_date, $image]);
            $success = "Duyuru başarıyla eklendi! ID: #" . $pdo->lastInsertId();
        } catch (PDOException $e) {
            $error = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Duyuru Ekle - Perfect Duyuru Admin Panel v1.0</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            font-size: 11px;
            background: #E5E5E5;
            margin: 0;
            padding: 10px;
        }
        
        .main-container {
            background: #FFFFFF;
            border: 1px solid #999999;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            color: white;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #1A5490;
        }
        
        .nav-bar {
            background: #F0F0F0;
            border-bottom: 1px solid #CCCCCC;
            padding: 5px 15px;
            font-size: 11px;
        }
        
        .nav-bar a {
            color: #0066CC;
            text-decoration: none;
            margin-right: 15px;
        }
        
        .nav-bar a:hover, .nav-bar a.active {
            text-decoration: underline;
            font-weight: bold;
        }
        
        .content {
            padding: 15px;
        }
        
        .form-container {
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            margin-bottom: 15px;
        }
        
        .box-header {
            background: linear-gradient(to bottom, #F5F5F5, #E8E8E8);
            border-bottom: 1px solid #CCCCCC;
            padding: 8px 12px;
            font-weight: bold;
            color: #333333;
            font-size: 12px;
        }
        
        .box-content {
            padding: 15px;
        }
        
        .form-row {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            font-weight: bold;
            margin-bottom: 3px;
            color: #333333;
            font-size: 11px;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            border: 1px solid #CCCCCC;
            padding: 4px;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 11px;
            box-sizing: border-box;
        }
        
        .form-textarea {
            height: 120px;
            resize: vertical;
        }
        
        .form-columns {
            display: flex;
            gap: 20px;
        }
        
        .form-column {
            flex: 1;
        }
        
        .form-column-small {
            flex: 0 0 200px;
        }
        
        .submit-btn {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            border: 1px solid #1A5490;
            color: white;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .submit-btn:hover {
            background: linear-gradient(to bottom, #3A80D2, #1A61A5);
        }
        
        .back-btn {
            background: linear-gradient(to bottom, #F0F0F0, #D8D8D8);
            border: 1px solid #999999;
            padding: 6px 12px;
            font-size: 11px;
            color: #333333;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .back-btn:hover {
            background: linear-gradient(to bottom, #E8E8E8, #D0D0D0);
            text-decoration: none;
            color: #333333;
        }
        
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid;
            font-size: 11px;
        }
        
        .alert-success {
            background: #DFF0D8;
            border-color: #D6E9C6;
            color: #3C763D;
        }
        
        .alert-error {
            background: #F2DEDE;
            border-color: #EBCCD1;
            color: #A94442;
        }
        
        .footer {
            background: #F0F0F0;
            border-top: 1px solid #CCCCCC;
            padding: 8px;
            font-size: 10px;
            color: #666666;
            text-align: center;
        }
        
        .logout-btn {
            float: right;
            background: linear-gradient(to bottom, #F0F0F0, #D8D8D8);
            border: 1px solid #999999;
            padding: 4px 8px;
            font-size: 10px;
            color: #333333;
            text-decoration: none;
        }
        
        .form-help {
            font-size: 10px;
            color: #666666;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <div class="header">
        Perfect Duyuru Admin Panel v1.0 - Duyuru Ekleme
        <a href="logout.php" class="logout-btn">Çıkış Yap</a>
    </div>
    
    <!-- Navigation -->
    <div class="nav-bar">
        <a href="dashboard.php">Dashboard</a>
        <a href="add-announcement.php" class="active">Duyuru Ekle</a>
        <a href="manage-announcements.php">Duyuru Yönetimi</a>
        <a href="../index.php" target="_blank">Siteyi Görüntüle</a>
    </div>
    
    <!-- Content -->
    <div class="content">
        <a href="dashboard.php" class="back-btn">« Dashboard'a Dön</a>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Başarılı!</strong> <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Hata!</strong> <?= $error ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <div class="box-header">Yeni Duyuru Ekleme Formu</div>
            <div class="box-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-columns">
                        <div class="form-column">
                            <div class="form-row">
                                <label class="form-label">Duyuru Başlığı *</label>
                                <input type="text" name="title" class="form-input" placeholder="Duyuru başlığını girin..." required>
                                <div class="form-help">Duyuru başlığı en fazla 255 karakter olabilir.</div>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Duyuru İçeriği *</label>
                                <textarea name="content" class="form-textarea" placeholder="Duyuru içeriğini detaylı olarak yazın..." required></textarea>
                                <div class="form-help">Duyuru içeriğinde HTML etiketleri kullanmayın.</div>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Duyuru Resmi</label>
                                <input type="file" name="image" class="form-input" accept="image/*">
                                <div class="form-help">Desteklenen formatlar: JPG, PNG, GIF. Maksimum boyut: 2MB.</div>
                            </div>
                        </div>

                        <div class="form-column-small">
                            <div class="form-row">
                                <label class="form-label">Kategori</label>
                                <select name="category" class="form-select">
                                    <option value="genel">Genel</option>
                                    <option value="önemli">Önemli</option>
                                    <option value="etkinlik">Etkinlik</option>
                                    <option value="duyuru">Duyuru</option>
                                    <option value="haber">Haber</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Öncelik Seviyesi</label>
                                <select name="priority" class="form-select">
                                    <option value="low">Düşük</option>
                                    <option value="medium" selected>Orta</option>
                                    <option value="high">Yüksek</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-select">
                                    <option value="active" selected>Aktif</option>
                                    <option value="inactive">Pasif</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Başlangıç Tarihi</label>
                                <input type="datetime-local" name="start_date" class="form-input" value="<?= date('Y-m-d\TH:i') ?>">
                                <div class="form-help">Duyurunun yayına başlayacağı tarih.</div>
                            </div>

                            <div class="form-row">
                                <label class="form-label">Bitiş Tarihi</label>
                                <input type="datetime-local" name="end_date" class="form-input">
                                <div class="form-help">Boş bırakırsanız süresiz olur.</div>
                            </div>
                        </div>
                    </div>

                    <hr style="border: 1px solid #DDDDDD; margin: 20px 0;">
                    
                    <div style="text-align: right;">
                        <button type="submit" name="add_announcement" class="submit-btn">Duyuru Ekle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Perfect Duyuru Admin Panel v1.0 | Powered by burak | © 2024 Tüm hakları saklıdır.
    </div>
</div>

</body>
</html>