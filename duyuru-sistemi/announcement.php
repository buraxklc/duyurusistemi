<?php
// announcement.php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == 0) {
    header('Location: index.php');
    exit;
}

// Duyuruyu çek
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$duyuru = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$duyuru) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($duyuru['title']) ?> - Perfect Duyuru v1.0</title>
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
            max-width: 900px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #1A5490;
        }
        
        .nav-bar {
            background: #F0F0F0;
            border-bottom: 1px solid #CCCCCC;
            padding: 5px;
            font-size: 11px;
        }
        
        .nav-bar a {
            color: #0066CC;
            text-decoration: none;
            margin-right: 15px;
        }
        
        .nav-bar a:hover {
            text-decoration: underline;
        }
        
        .content {
            padding: 15px;
        }
        
        .duyuru-detail {
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            margin-bottom: 15px;
        }
        
        .duyuru-header {
            background: linear-gradient(to bottom, #F5F5F5, #E8E8E8);
            border-bottom: 1px solid #CCCCCC;
            padding: 12px;
        }
        
        .duyuru-title {
            color: #0066CC;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .duyuru-meta {
            color: #888888;
            font-size: 10px;
        }
        
        .kategori-tag {
            background: #FFFFE0;
            border: 1px solid #D4C83F;
            padding: 2px 6px;
            font-size: 9px;
            color: #666666;
            margin-right: 10px;
        }
        
        .duyuru-content {
            padding: 20px;
            line-height: 1.5;
            color: #333333;
            font-size: 12px;
        }
        
        .duyuru-image {
            text-align: center;
            margin: 20px 0;
        }
        
        .duyuru-image img {
            border: 1px solid #CCCCCC;
            max-width: 100%;
            box-shadow: 2px 2px 4px #CCCCCC;
        }
        
        .back-button {
            background: linear-gradient(to bottom, #F0F0F0, #D8D8D8);
            border: 1px solid #999999;
            padding: 6px 12px;
            font-size: 11px;
            color: #333333;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .back-button:hover {
            background: linear-gradient(to bottom, #E8E8E8, #D0D0D0);
            text-decoration: none;
            color: #333333;
        }
        
        .footer {
            background: #F0F0F0;
            border-top: 1px solid #CCCCCC;
            padding: 8px;
            font-size: 10px;
            color: #666666;
            text-align: center;
        }
        
        .info-box {
            background: #F9F9F9;
            border: 1px solid #DDDDDD;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .info-box .label {
            color: #666666;
            font-weight: bold;
        }
        
        .info-box .value {
            color: #333333;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <div class="header">
        Perfect Duyuru Panel v1.0 - Duyuru Detayı
    </div>
    
    <!-- Navigation -->
    <div class="nav-bar">
        <a href="index.php">« Ana Sayfa</a>
        <a href="index.php#duyurular">Duyurular</a>
        <a href="admin/">Yönetim Paneli</a>
    </div>
    
    <!-- Content -->
    <div class="content">
        <a href="index.php" class="back-button">« Geri Dön</a>
        
        <!-- Duyuru Bilgileri -->
        <div class="info-box">
            <table width="100%" cellpadding="3" cellspacing="0">
                <tr>
                    <td class="label" width="120">Duyuru ID:</td>
                    <td class="value">#<?= $duyuru['id'] ?></td>
                    <td class="label" width="120">Kategori:</td>
                    <td class="value"><span class="kategori-tag"><?= strtoupper($duyuru['category']) ?></span></td>
                </tr>
                <tr>
                    <td class="label">Yayın Tarihi:</td>
                    <td class="value"><?= date('d.m.Y H:i:s', strtotime($duyuru['created_at'])) ?></td>
                    <td class="label">Öncelik:</td>
                    <td class="value"><?= strtoupper($duyuru['priority']) ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Duyuru İçeriği -->
        <div class="duyuru-detail">
            <div class="duyuru-header">
                <div class="duyuru-title"><?= htmlspecialchars($duyuru['title']) ?></div>
                <div class="duyuru-meta">
                    Yayınlanma: <?= date('d.m.Y H:i', strtotime($duyuru['created_at'])) ?> | 
                    Durum: AKTİF | 
                    ID: #<?= $duyuru['id'] ?>
                </div>
            </div>
            <div class="duyuru-content">
                <?php if($duyuru['image']): ?>
                    <div class="duyuru-image">
                        <img src="assets/uploads/<?= $duyuru['image'] ?>" alt="Duyuru Resmi">
                    </div>
                <?php endif; ?>
                
                <?= nl2br(htmlspecialchars($duyuru['content'])) ?>
                
                <?php if($duyuru['end_date']): ?>
                    <div style="margin-top: 20px; padding: 10px; background: #FFF8DC; border: 1px solid #DDD;">
                        <strong>Not:</strong> Bu duyuru <?= date('d.m.Y H:i', strtotime($duyuru['end_date'])) ?> tarihine kadar geçerlidir.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <a href="index.php" class="back-button">« Duyuru Listesine Dön</a>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Perfect Duyuru Panel v1.0 | Powered by PHP & MySQL | © 2024 Tüm hakları saklıdır.
    </div>
</div>

</body>
</html>