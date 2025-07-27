<?php
// index.php
require_once 'includes/config.php';

// Aktif duyuruları çek
$stmt = $pdo->query("SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Perfect Duyuru v1.0</title>
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
        
        .stats-bar {
            background: #F9F9F9;
            border: 1px solid #DDDDDD;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 11px;
            color: #666666;
        }
        
        .duyuru-item {
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .duyuru-header {
            background: linear-gradient(to bottom, #F5F5F5, #E8E8E8);
            border-bottom: 1px solid #CCCCCC;
            padding: 6px 10px;
            font-weight: bold;
            color: #333333;
        }
        
        .duyuru-title {
            color: #0066CC;
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .duyuru-meta {
            color: #888888;
            font-size: 10px;
        }
        
        .kategori-tag {
            background: #FFFFE0;
            border: 1px solid #D4C83F;
            padding: 1px 4px;
            font-size: 9px;
            color: #666666;
            margin-right: 8px;
        }
        
        .duyuru-content {
            padding: 12px;
            line-height: 1.4;
            color: #333333;
        }
        
        .duyuru-image {
            text-align: center;
            margin-top: 10px;
        }
        
        .duyuru-image img {
            border: 1px solid #CCCCCC;
            max-width: 300px;
        }
        
        .footer {
            background: #F0F0F0;
            border-top: 1px solid #CCCCCC;
            padding: 8px;
            font-size: 10px;
            color: #666666;
            text-align: center;
        }
        
        .admin-button {
            position: fixed;
            bottom: 15px;
            right: 15px;
            background: linear-gradient(to bottom, #F0F0F0, #D8D8D8);
            border: 1px solid #999999;
            padding: 6px 12px;
            font-size: 11px;
            color: #333333;
            text-decoration: none;
            cursor: pointer;
        }
        
        .admin-button:hover {
            background: linear-gradient(to bottom, #E8E8E8, #D0D0D0);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999999;
            font-style: italic;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .info-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #EEEEEE;
        }
        
        .info-table .label {
            color: #666666;
            width: 120px;
        }
        
        .info-table .value {
            color: #333333;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <div class="header">
        Perfect Duyuru Panel v1.0 - Duyuru Yönetim Sistemi
    </div>
    
    <!-- Navigation -->
    <div class="nav-bar">
        <a href="#">Ana Sayfa</a>
        <a href="#duyurular">Duyurular</a>  
    </div>
    
    <!-- Content -->
    <div class="content">
        <!-- Stats Bar -->
        <div class="stats-bar">
            <table class="info-table">
                <tr>
                    <td class="label">Toplam Duyuru:</td>
                    <td class="value"><?= count($announcements) ?></td>
                    <td class="label">Son Güncelleme:</td>
                    <td class="value"><?= date('d.m.Y H:i:s') ?></td>
                </tr>
                <tr>
                    <td class="label">Sistem Durumu:</td>
                    <td class="value" style="color: green;">AKTİF</td>
                    <td class="label">Versiyon:</td>
                    <td class="value">v1.0</td>
                </tr>
            </table>
        </div>
        
        <!-- Duyurular -->
        <div id="duyurular">
            <?php if(empty($announcements)): ?>
                <div class="empty-state">
                    <strong>Henüz hiç duyuru eklenmemiş.</strong><br>
                    Yönetim panelinden yeni duyuru ekleyebilirsiniz.
                </div>
            <?php else: ?>
                <?php foreach($announcements as $duyuru): ?>
                <div class="duyuru-item">
                    <div class="duyuru-header">
                        <div class="duyuru-title"><?= htmlspecialchars($duyuru['title']) ?></div>
                        <div class="duyuru-meta">
                            <span class="kategori-tag"><?= strtoupper($duyuru['category']) ?></span>
                            Tarih: <?= date('d.m.Y H:i', strtotime($duyuru['created_at'])) ?> | 
                            ID: #<?= $duyuru['id'] ?> | 
                            Durum: AKTİF
                        </div>
                    </div>
                    <div class="duyuru-content">
                        <?= nl2br(htmlspecialchars($duyuru['content'])) ?>
                        
                        <?php if($duyuru['image']): ?>
                            <div class="duyuru-image">
                                <img src="assets/uploads/<?= $duyuru['image'] ?>" alt="Duyuru Resmi">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Perfect Duyuru Panel v1.0 | Powered by burak | © 2025 Tüm hakları saklıdır.
    </div>
</div>


</body>
</html>