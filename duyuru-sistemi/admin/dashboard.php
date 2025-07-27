<?php
// admin/dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_once '../includes/functions.php';
checkAdmin();

// İstatistikler
$stmt = $pdo->query("SELECT COUNT(*) FROM announcements");
$total_announcements = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM announcements WHERE status = 'active'");
$active_announcements = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM announcements WHERE DATE(created_at) = CURDATE()");
$today_announcements = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Perfect Duyuru Admin Panel v1.0</title>
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
        
        .welcome-box {
            background: #F9F9F9;
            border: 1px solid #DDDDDD;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .stats-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .stat-box {
            flex: 1;
            background: #FAFAFA;
            border: 1px solid #DDDDDD;
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #0066CC;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #666666;
        }
        
        .recent-box {
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
            padding: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        th {
            background: #F0F0F0;
            border: 1px solid #CCCCCC;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            color: #333333;
        }
        
        td {
            border: 1px solid #DDDDDD;
            padding: 6px;
            color: #333333;
        }
        
        tr:nth-child(even) {
            background: #F9F9F9;
        }
        
        .status-active {
            color: green;
            font-weight: bold;
        }
        
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        
        .priority-high { color: #CC0000; font-weight: bold; }
        .priority-medium { color: #FF6600; }
        .priority-low { color: #006600; }
        
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
        
        .logout-btn:hover {
            background: linear-gradient(to bottom, #E8E8E8, #D0D0D0);
            text-decoration: none;
            color: #333333;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Header -->
    <div class="header">
        Perfect Duyuru Admin Panel v1.0 - Yönetim Paneli
        <a href="logout.php" class="logout-btn">Çıkış Yap</a>
    </div>
    
    <!-- Navigation -->
    <div class="nav-bar">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="add-announcement.php">Duyuru Ekle</a>
        <a href="manage-announcements.php">Duyuru Yönetimi</a>
        <a href="../index.php" target="_blank">Siteyi Görüntüle</a>
    </div>
    
    <!-- Content -->
    <div class="content">
        <!-- Welcome Box -->
        <div class="welcome-box">
            <strong>Hoşgeldiniz, <?= $_SESSION['admin_username'] ?>!</strong><br>
            Son giriş: <?= date('d.m.Y H:i:s') ?> | 
            Sistem durumu: <span style="color: green; font-weight: bold;">AKTİF</span> | 
            Panel versiyonu: v1.0
        </div>
        
        <!-- İstatistikler -->
        <div class="stats-container">
            <div class="stat-box">
                <div class="stat-number"><?= $total_announcements ?></div>
                <div class="stat-label">Toplam Duyuru</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $active_announcements ?></div>
                <div class="stat-label">Aktif Duyuru</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $today_announcements ?></div>
                <div class="stat-label">Bugün Eklenen</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $total_announcements - $active_announcements ?></div>
                <div class="stat-label">Pasif Duyuru</div>
            </div>
        </div>
        
        <!-- Son Duyurular -->
        <div class="recent-box">
            <div class="box-header">Son Eklenen Duyurular</div>
            <div class="box-content">
                <?php
                $stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 8");
                $recent_announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <table>
                    <tr>
                        <th width="40">ID</th>
                        <th>Başlık</th>
                        <th width="80">Kategori</th>
                        <th width="60">Öncelik</th>
                        <th width="60">Durum</th>
                        <th width="120">Tarih</th>
                        <th width="80">İşlemler</th>
                    </tr>
                    <?php foreach($recent_announcements as $announcement): ?>
                    <tr>
                        <td>#<?= $announcement['id'] ?></td>
                        <td><?= htmlspecialchars(substr($announcement['title'], 0, 40)) ?><?= strlen($announcement['title']) > 40 ? '...' : '' ?></td>
                        <td><?= strtoupper($announcement['category']) ?></td>
                        <td class="priority-<?= $announcement['priority'] ?>"><?= strtoupper($announcement['priority']) ?></td>
                        <td class="status-<?= $announcement['status'] ?>"><?= strtoupper($announcement['status']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($announcement['created_at'])) ?></td>
                        <td>
                            <a href="edit-announcement.php?id=<?= $announcement['id'] ?>" style="color: #0066CC; font-size: 10px;">Düzenle</a> |
                            <a href="manage-announcements.php?delete=<?= $announcement['id'] ?>" style="color: #CC0000; font-size: 10px;" onclick="return confirm('Emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        
        <!-- Sistem Bilgileri -->
        <div class="recent-box">
            <div class="box-header">Sistem Bilgileri</div>
            <div class="box-content">
                <table>
                    <tr>
                        <td width="150"><strong>PHP Versiyonu:</strong></td>
                        <td><?= phpversion() ?></td>
                        <td width="150"><strong>Sunucu Zamanı:</strong></td>
                        <td><?= date('d.m.Y H:i:s') ?></td>
                    </tr>
                    <tr>
                        <td><strong>MySQL Versiyonu:</strong></td>
                        <td><?= $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) ?></td>
                        <td><strong>Panel Versiyonu:</strong></td>
                        <td>Perfect Duyuru v1.0</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        Perfect Duyuru Admin Panel v1.0 | Powered by PHP & MySQL | © 2024 Tüm hakları saklıdır.
    </div>
</div>

</body>
</html>