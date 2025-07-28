<?php
// index.php - Mevcut tasarım + arama özelliği
require_once 'includes/config.php';

// Arama parametreleri
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Temel sorgu
$where_conditions = ["status = 'active'"];
$params = [];

// Arama koşulları
if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $where_conditions[] = "category = ?";
    $params[] = $category;
}

if (!empty($priority)) {
    $where_conditions[] = "priority = ?";
    $params[] = $priority;
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
}

// SQL sorgusu
$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT * FROM announcements WHERE $where_clause ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Toplam sayı
$total_stmt = $pdo->query("SELECT COUNT(*) FROM announcements WHERE status = 'active'");
$total_count = $total_stmt->fetchColumn();
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
        
        /* ARAMA BÖLÜMÜ - ESKİ TASARIMLA UYUMLU */
        .search-box {
            background: #F9F9F9;
            border: 1px solid #DDDDDD;
            padding: 12px;
            margin-bottom: 15px;
        }
        
        .search-header {
            background: linear-gradient(to bottom, #F5F5F5, #E8E8E8);
            border-bottom: 1px solid #CCCCCC;
            padding: 6px 10px;
            font-weight: bold;
            color: #333333;
            font-size: 11px;
            margin: -12px -12px 10px -12px;
        }
        
        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: end;
        }
        
        .search-item {
            flex: 1;
            min-width: 120px;
        }
        
        .search-label {
            display: block;
            font-weight: bold;
            margin-bottom: 2px;
            color: #333333;
            font-size: 10px;
        }
        
        .search-input, .search-select {
            width: 100%;
            border: 1px solid #CCCCCC;
            padding: 3px;
            font-family: Tahoma, Arial, sans-serif;
            font-size: 10px;
            box-sizing: border-box;
        }
        
        .search-btn {
            background: linear-gradient(to bottom, #4A90E2, #2171B5);
            border: 1px solid #1A5490;
            color: white;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: bold;
            cursor: pointer;
            height: 22px;
        }
        
        .search-btn:hover {
            background: linear-gradient(to bottom, #3A80D2, #1A61A5);
        }
        
        .clear-btn {
            background: linear-gradient(to bottom, #F0F0F0, #D8D8D8);
            border: 1px solid #999999;
            padding: 3px 6px;
            font-size: 10px;
            color: #333333;
            text-decoration: none;
            height: 22px;
            line-height: 16px;
            display: inline-block;
        }
        
        .quick-links {
            margin-top: 8px;
            font-size: 10px;
        }
        
        .quick-links a {
            color: #0066CC;
            text-decoration: none;
            margin-right: 10px;
            padding: 2px 4px;
            border-radius: 2px;
        }
        
        .quick-links a:hover {
            background: #0066CC;
            color: white;
            text-decoration: none;
        }
        
        /* SONUÇ BİLGİSİ */
        .search-results {
            background: #E7F3FF;
            border: 1px solid #B8DAFF;
            padding: 6px 10px;
            margin-bottom: 15px;
            font-size: 10px;
            color: #004085;
        }
        
        /* ESKİ STATS BAR */
        .stats-bar {
            background: #F9F9F9;
            border: 1px solid #DDDDDD;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 11px;
            color: #666666;
        }
        
        /* ESKİ DUYURU TASARIMI */
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
        
        /* RESPONSİVE */
        @media (max-width: 768px) {
            .main-container {
                margin: 5px;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-item {
                min-width: auto;
            }
            
            body {
                padding: 5px;
            }
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
        <a href="#duyurular">Duyurular</a>  
    </div>
    
    <!-- Content -->
    <div class="content">
        
        <!-- ARAMA KUTUSU -->
        <div class="search-box">
            <div class="search-header">Duyuru Arama ve Filtreleme</div>
            <form method="GET" class="search-form">
                <div class="search-item">
                    <label class="search-label">Arama</label>
                    <input type="text" name="search" class="search-input" placeholder="Başlık veya içerik..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="search-item">
                    <label class="search-label">Kategori</label>
                    <select name="category" class="search-select">
                        <option value="">Tümü</option>
                        <option value="genel" <?= $category == 'genel' ? 'selected' : '' ?>>Genel</option>
                        <option value="önemli" <?= $category == 'önemli' ? 'selected' : '' ?>>Önemli</option>
                        <option value="etkinlik" <?= $category == 'etkinlik' ? 'selected' : '' ?>>Etkinlik</option>
                        <option value="duyuru" <?= $category == 'duyuru' ? 'selected' : '' ?>>Duyuru</option>
                        <option value="haber" <?= $category == 'haber' ? 'selected' : '' ?>>Haber</option>
                    </select>
                </div>
                <div class="search-item">
                    <label class="search-label">Öncelik</label>
                    <select name="priority" class="search-select">
                        <option value="">Tümü</option>
                        <option value="high" <?= $priority == 'high' ? 'selected' : '' ?>>Yüksek</option>
                        <option value="medium" <?= $priority == 'medium' ? 'selected' : '' ?>>Orta</option>
                        <option value="low" <?= $priority == 'low' ? 'selected' : '' ?>>Düşük</option>
                    </select>
                </div>
                <div class="search-item">
                    <label class="search-label">Başlangıç</label>
                    <input type="date" name="date_from" class="search-input" value="<?= $date_from ?>">
                </div>
                <div class="search-item">
                    <label class="search-label">Bitiş</label>
                    <input type="date" name="date_to" class="search-input" value="<?= $date_to ?>">
                </div>
                <div style="display: flex; gap: 4px;">
                    <button type="submit" class="search-btn">Ara</button>
                    <a href="index.php" class="clear-btn">Temizle</a>
                </div>
            </form>
            
            <div class="quick-links">
                <strong>Hızlı:</strong>
                <a href="?date_from=<?= date('Y-m-d', strtotime('-7 days')) ?>">Son 7 Gün</a>
                <a href="?date_from=<?= date('Y-m-d', strtotime('-30 days')) ?>">Son Ay</a>
                <a href="?priority=high">Önemli</a>
                <a href="?category=önemli">Acil</a>
            </div>
        </div>
        
        <!-- SONUÇ BİLGİSİ -->
        <?php if(!empty($search) || !empty($category) || !empty($priority) || !empty($date_from) || !empty($date_to)): ?>
        <div class="search-results">
            <strong><?= count($announcements) ?></strong> sonuç bulundu
            <?php if(!empty($search)): ?>
                "<strong><?= htmlspecialchars($search) ?></strong>" için
            <?php endif; ?>
            (toplam <?= $total_count ?> duyuru)
        </div>
        <?php endif; ?>
        
        <!-- Stats Bar -->
        <div class="stats-bar">
            <table class="info-table">
                <tr>
                    <td class="label">Gösterilen:</td>
                    <td class="value"><?= count($announcements) ?></td>
                    <td class="label">Toplam Duyuru:</td>
                    <td class="value"><?= $total_count ?></td>
                </tr>
                <tr>
                    <td class="label">Sistem Durumu:</td>
                    <td class="value" style="color: green;">AKTİF</td>
                    <td class="label">Son Güncelleme:</td>
                    <td class="value"><?= date('d.m.Y H:i:s') ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Duyurular -->
        <div id="duyurular">
            <?php if(empty($announcements)): ?>
                <div class="empty-state">
                    <?php if(!empty($search) || !empty($category) || !empty($priority) || !empty($date_from) || !empty($date_to)): ?>
                        <strong>Arama kriterlerinize uygun duyuru bulunamadı.</strong><br>
                        Filtreleri temizleyerek tekrar deneyin.
                    <?php else: ?>
                        <strong>Henüz hiç duyuru eklenmemiş.</strong><br>
                        Yönetim panelinden yeni duyuru ekleyebilirsiniz.
                    <?php endif; ?>
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
                            Öncelik: <?= strtoupper($duyuru['priority']) ?> |
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