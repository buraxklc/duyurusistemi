<?php
// admin/manage-announcements.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_once '../includes/functions.php';
checkAdmin();

$success = '';
$error = '';

// Silme işlemi
if (isset($_GET['delete'])) {
   $id = (int)$_GET['delete'];
   try {
       // Önce resmi sil
       $stmt = $pdo->prepare("SELECT image FROM announcements WHERE id = ?");
       $stmt->execute([$id]);
       $announcement = $stmt->fetch();
       
       if ($announcement && $announcement['image']) {
           $image_path = "../assets/uploads/" . $announcement['image'];
           if (file_exists($image_path)) {
               unlink($image_path);
           }
       }
       
       $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
       $stmt->execute([$id]);
       $success = "Duyuru başarıyla silindi!";
   } catch (PDOException $e) {
       $error = "Silme hatası: " . $e->getMessage();
   }
}

// Durum değiştirme
if (isset($_GET['toggle_status'])) {
   $id = (int)$_GET['toggle_status'];
   try {
       $stmt = $pdo->prepare("UPDATE announcements SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
       $stmt->execute([$id]);
       $success = "Duyuru durumu güncellendi!";
   } catch (PDOException $e) {
       $error = "Güncelleme hatası: " . $e->getMessage();
   }
}

// Duyuruları çek
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
   $where_conditions[] = "(title LIKE ? OR content LIKE ?)";
   $params[] = "%$search%";
   $params[] = "%$search%";
}

if (!empty($category)) {
   $where_conditions[] = "category = ?";
   $params[] = $category;
}

if (!empty($status)) {
   $where_conditions[] = "status = ?";
   $params[] = $status;
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

$stmt = $pdo->prepare("SELECT * FROM announcements $where_clause ORDER BY created_at DESC");
$stmt->execute($params);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
   <meta charset="UTF-8">
   <title>Duyuru Yönetimi - Perfect Duyuru Admin Panel v1.0</title>
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
           max-width: 1200px;
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
       
       .form-container, .filter-container {
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
       
       .filter-row {
           display: flex;
           gap: 15px;
           align-items: end;
           margin-bottom: 10px;
       }
       
       .filter-item {
           flex: 1;
       }
       
       .form-label {
           display: block;
           font-weight: bold;
           margin-bottom: 3px;
           color: #333333;
           font-size: 11px;
       }
       
       .form-input, .form-select {
           width: 100%;
           border: 1px solid #CCCCCC;
           padding: 4px;
           font-family: Tahoma, Arial, sans-serif;
           font-size: 11px;
           box-sizing: border-box;
       }
       
       .filter-btn {
           background: linear-gradient(to bottom, #4A90E2, #2171B5);
           border: 1px solid #1A5490;
           color: white;
           padding: 6px 12px;
           font-size: 11px;
           font-weight: bold;
           cursor: pointer;
           height: 26px;
       }
       
       .filter-btn:hover {
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
       
       .add-btn {
           background: linear-gradient(to bottom, #4A90E2, #2171B5);
           border: 1px solid #1A5490;
           color: white;
           padding: 6px 12px;
           font-size: 11px;
           font-weight: bold;
           text-decoration: none;
           display: inline-block;
           margin-bottom: 15px;
           float: right;
       }
       
       .add-btn:hover {
           background: linear-gradient(to bottom, #3A80D2, #1A61A5);
           text-decoration: none;
           color: white;
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
       
       .category-badge {
           background: #E8E8E8;
           padding: 2px 6px;
           font-size: 10px;
           border-radius: 3px;
           color: #333333;
       }
       
       .action-link {
           color: #0066CC;
           text-decoration: none;
           font-size: 10px;
           margin-right: 5px;
       }
       
       .action-link:hover {
           text-decoration: underline;
       }
       
       .action-link.delete {
           color: #CC0000;
       }
       
       .action-link.toggle {
           color: #FF6600;
       }
       
       .no-data {
           text-align: center;
           padding: 30px;
           color: #666666;
           font-style: italic;
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
       
       .logout-btn:hover {
           background: linear-gradient(to bottom, #E8E8E8, #D0D0D0);
           text-decoration: none;
           color: #333333;
       }
       
       .image-preview {
           width: 40px;
           height: 30px;
           object-fit: cover;
           border: 1px solid #DDDDDD;
       }
       
       .clearfix::after {
           content: "";
           display: table;
           clear: both;
       }
   </style>
</head>
<body>

<div class="main-container">
   <!-- Header -->
   <div class="header">
       Perfect Duyuru Admin Panel v1.0 - Duyuru Yönetimi
       <a href="logout.php" class="logout-btn">Çıkış Yap</a>
   </div>
   
   <!-- Navigation -->
   <div class="nav-bar">
       <a href="dashboard.php">Dashboard</a>
       <a href="add-announcement.php">Duyuru Ekle</a>
       <a href="manage-announcements.php" class="active">Duyuru Yönetimi</a>
       <a href="../index.php" target="_blank">Siteyi Görüntüle</a>
   </div>
   
   <!-- Content -->
   <div class="content">
       <div class="clearfix">
           <a href="dashboard.php" class="back-btn">« Dashboard'a Dön</a>
           <a href="add-announcement.php" class="add-btn">+ Yeni Duyuru Ekle</a>
       </div>
       
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

       <!-- Filtreleme -->
       <div class="filter-container">
           <div class="box-header">Duyuru Filtreleme</div>
           <div class="box-content">
               <form method="GET">
                   <div class="filter-row">
                       <div class="filter-item">
                           <label class="form-label">Arama</label>
                           <input type="text" name="search" class="form-input" placeholder="Başlık veya içerik ara..." value="<?= htmlspecialchars($search) ?>">
                       </div>
                       <div class="filter-item">
                           <label class="form-label">Kategori</label>
                           <select name="category" class="form-select">
                               <option value="">Tüm Kategoriler</option>
                               <option value="genel" <?= $category == 'genel' ? 'selected' : '' ?>>Genel</option>
                               <option value="önemli" <?= $category == 'önemli' ? 'selected' : '' ?>>Önemli</option>
                               <option value="etkinlik" <?= $category == 'etkinlik' ? 'selected' : '' ?>>Etkinlik</option>
                               <option value="duyuru" <?= $category == 'duyuru' ? 'selected' : '' ?>>Duyuru</option>
                               <option value="haber" <?= $category == 'haber' ? 'selected' : '' ?>>Haber</option>
                           </select>
                       </div>
                       <div class="filter-item">
                           <label class="form-label">Durum</label>
                           <select name="status" class="form-select">
                               <option value="">Tüm Durumlar</option>
                               <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Aktif</option>
                               <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Pasif</option>
                           </select>
                       </div>
                       <div class="filter-item" style="flex: 0 0 100px;">
                           <button type="submit" class="filter-btn">Filtrele</button>
                       </div>
                   </div>
               </form>
           </div>
       </div>

       <!-- Duyurular Tablosu -->
       <div class="form-container">
           <div class="box-header">Duyuru Listesi (<?= count($announcements) ?> adet)</div>
           <div class="box-content">
               <?php if (empty($announcements)): ?>
                   <div class="no-data">
                       Hiç duyuru bulunamadı. <a href="add-announcement.php">Yeni duyuru eklemek için tıklayın.</a>
                   </div>
               <?php else: ?>
                   <table>
                       <tr>
                           <th width="40">ID</th>
                           <th>Başlık</th>
                           <th width="80">Kategori</th>
                           <th width="60">Öncelik</th>
                           <th width="60">Durum</th>
                           <th width="50">Resim</th>
                           <th width="120">Tarih</th>
                           <th width="120">İşlemler</th>
                       </tr>
                       <?php foreach($announcements as $announcement): ?>
                       <tr>
                           <td>#<?= $announcement['id'] ?></td>
                           <td>
                               <strong><?= htmlspecialchars(substr($announcement['title'], 0, 60)) ?><?= strlen($announcement['title']) > 60 ? '...' : '' ?></strong>
                               <?php if($announcement['content']): ?>
                                   <br><small style="color: #666666;"><?= htmlspecialchars(substr($announcement['content'], 0, 80)) ?><?= strlen($announcement['content']) > 80 ? '...' : '' ?></small>
                               <?php endif; ?>
                           </td>
                           <td>
                               <span class="category-badge"><?= strtoupper($announcement['category']) ?></span>
                           </td>
                           <td class="priority-<?= $announcement['priority'] ?>">
                               <?php
                               $priority_text = $announcement['priority'] == 'high' ? 'YÜKSEK' : 
                                              ($announcement['priority'] == 'medium' ? 'ORTA' : 'DÜŞÜK');
                               echo $priority_text;
                               ?>
                           </td>
                           <td class="status-<?= $announcement['status'] ?>">
                               <?= strtoupper($announcement['status']) ?>
                           </td>
                           <td>
                               <?php if($announcement['image']): ?>
                                   <img src="../assets/uploads/<?= $announcement['image'] ?>" alt="Resim" class="image-preview">
                               <?php else: ?>
                                   <span style="color: #CCCCCC; font-size: 10px;">YOK</span>
                               <?php endif; ?>
                           </td>
                           <td>
                               <?= date('d.m.Y', strtotime($announcement['created_at'])) ?><br>
                               <small style="color: #666666;"><?= date('H:i', strtotime($announcement['created_at'])) ?></small>
                           </td>
                           <td>
                               <a href="edit-announcement.php?id=<?= $announcement['id'] ?>" class="action-link" title="Düzenle">Düzenle</a>
                               <a href="?toggle_status=<?= $announcement['id'] ?>" class="action-link toggle" title="Durum Değiştir" onclick="return confirm('Durumu değiştirmek istediğinizden emin misiniz?')">Durum</a>
                               <a href="?delete=<?= $announcement['id'] ?>" class="action-link delete" title="Sil" onclick="return confirm('Bu duyuruyu silmek istediğinizden emin misiniz?')">Sil</a>
                           </td>
                       </tr>
                       <?php endforeach; ?>
                   </table>
               <?php endif; ?>
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