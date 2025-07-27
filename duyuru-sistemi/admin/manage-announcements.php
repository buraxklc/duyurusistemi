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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyuru Yönetimi - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-bullhorn"></i> Duyuru Sistemi
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Hoşgeldin, <?= $_SESSION['admin_username'] ?></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Çıkış
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add-announcement.php">
                                <i class="fas fa-plus"></i> Duyuru Ekle
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage-announcements.php">
                                <i class="fas fa-list"></i> Duyuruları Yönet
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-list"></i> Duyuru Yönetimi</h1>
                    <a href="add-announcement.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Duyuru
                    </a>
                </div>

                <?php if(!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <i class="fas fa-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <!-- Filtreleme -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Başlık veya içerik ara..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">Tüm Kategoriler</option>
                                    <option value="genel" <?= $category == 'genel' ? 'selected' : '' ?>>Genel</option>
                                    <option value="önemli" <?= $category == 'önemli' ? 'selected' : '' ?>>Önemli</option>
                                    <option value="etkinlik" <?= $category == 'etkinlik' ? 'selected' : '' ?>>Etkinlik</option>
                                    <option value="duyuru" <?= $category == 'duyuru' ? 'selected' : '' ?>>Duyuru</option>
                                    <option value="haber" <?= $category == 'haber' ? 'selected' : '' ?>>Haber</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filtrele
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Duyurular Tablosu -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Başlık</th>
                                        <th>Kategori</th>
                                        <th>Öncelik</th>
                                        <th>Durum</th>
                                        <th>Resim</th>
                                        <th>Tarih</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($announcements)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Hiç duyuru bulunamadı.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($announcements as $announcement): ?>
                                        <tr>
                                            <td><?= $announcement['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars(substr($announcement['title'], 0, 50)) ?><?= strlen($announcement['title']) > 50 ? '...' : '' ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= $announcement['category'] ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $priority_class = $announcement['priority'] == 'high' ? 'danger' : 
                                                                ($announcement['priority'] == 'medium' ? 'warning' : 'success');
                                                $priority_text = $announcement['priority'] == 'high' ? 'Yüksek' : 
                                                               ($announcement['priority'] == 'medium' ? 'Orta' : 'Düşük');
                                                ?>
                                                <span class="badge bg-<?= $priority_class ?>"><?= $priority_text ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $announcement['status'] == 'active' ? 'success' : 'secondary' ?>">
                                                    <?= $announcement['status'] == 'active' ? 'Aktif' : 'Pasif' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if($announcement['image']): ?>
                                                    <img src="../assets/uploads/<?= $announcement['image'] ?>" alt="Resim" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                                                <?php else: ?>
                                                    <i class="fas fa-image text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d.m.Y H:i', strtotime($announcement['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit-announcement.php?id=<?= $announcement['id'] ?>" class="btn btn-outline-primary" title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?toggle_status=<?= $announcement['id'] ?>" class="btn btn-outline-warning" title="Durum Değiştir" onclick="return confirm('Durumu değiştirmek istediğinizden emin misiniz?')">
                                                        <i class="fas fa-toggle-on"></i>
                                                    </a>
                                                    <a href="?delete=<?= $announcement['id'] ?>" class="btn btn-outline-danger" title="Sil" onclick="return confirm('Bu duyuruyu silmek istediğinizden emin misiniz?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>