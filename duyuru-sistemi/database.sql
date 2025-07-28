-- Adminler tablosu
CREATE TABLE admins (
   id INT AUTO_INCREMENT PRIMARY KEY,
   username VARCHAR(50) UNIQUE,
   password VARCHAR(255),
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Duyurular tablosu
CREATE TABLE announcements (
   id INT AUTO_INCREMENT PRIMARY KEY,
   title VARCHAR(255),
   content TEXT,
   category VARCHAR(50),
   priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
   image VARCHAR(255),
   status ENUM('active', 'inactive') DEFAULT 'active',
   start_date DATETIME,
   end_date DATETIME,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Varsayılan admin kullanıcısı ekleme
-- Kullanıcı: admin, Şifre: 123456
INSERT INTO admins (username, password, created_at) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- Örnek duyurular (opsiyonel)
INSERT INTO announcements (title, content, category, priority, status, created_at) VALUES
('Hoşgeldiniz!', 'Perfect Duyuru Sistemi başarıyla kurulmuştur. İlk duyurunuza hoşgeldiniz!', 'genel', 'medium', 'active', NOW()),
('Sistem Test Duyurusu', 'Bu bir test duyurusudur. Admin panelinden yeni duyurular ekleyebilir, mevcut duyuruları düzenleyebilirsiniz.', 'önemli', 'high', 'active', NOW()),
('Örnek Etkinlik', 'Bu bir örnek etkinlik duyurusudur. Tarih, yer ve detayları buraya yazabilirsiniz.', 'etkinlik', 'low', 'active', NOW());
