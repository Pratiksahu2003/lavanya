-- ============================================================
-- NOVAHOMZ — Complete MySQL Database Schema
-- Version: 1.0 | Engine: InnoDB | Charset: utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS `novahomz` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `novahomz`;

-- ============================================================
-- TABLE: admins
-- ============================================================
CREATE TABLE `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin: admin@novahomz.com / Admin@123
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Nova Admin', 'admin@novahomz.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- ============================================================
-- TABLE: settings
-- ============================================================
CREATE TABLE `settings` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_val` TEXT DEFAULT NULL,
  `label`       VARCHAR(200) DEFAULT NULL,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`setting_key`, `setting_val`, `label`) VALUES
('site_name',        'NOVAHOMZ',                              'Site Name'),
('site_tagline',     'Premium Commercial Furniture',          'Site Tagline'),
('phone1',           '+91 8796591267',                       'Phone 1'),
('phone2',           '+91 74999 49187',                       'Phone 2'),
('phone3',           '+91 93541 09802',                       'Phone 3'),
('email',            'info@lavanyaacreation.in',                     'Email'),
('whatsapp',         '91 8796591267',                          'WhatsApp Number (with country code)'),
('address',          'Rz-D1/325A, Mahavir Enclave, New Delhi – 110045', 'Address'),
('instagram',        '#',                                     'Instagram URL'),
('facebook',         '#',                                     'Facebook URL'),
('youtube',          '#',                                     'YouTube URL'),
('meta_title',       'NOVAHOMZ — Premium Commercial Furniture & Workspace Solutions | New Delhi', 'Default Meta Title'),
('meta_desc',        'NOVAHOMZ delivers premium furniture solutions for offices, hotels, schools, hospitals, cafes & homes. 700+ products. Free delivery & installation across India.', 'Default Meta Description'),
('announcement_bar', 'Free Delivery & Professional Installation Across India', 'Announcement Bar Text'),
('business_hours',   'Mon–Sat, 10am–7pm',                    'Business Hours');

-- ============================================================
-- TABLE: industries
-- ============================================================
CREATE TABLE `industries` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `icon`       VARCHAR(10) NOT NULL DEFAULT '🏢',
  `description`TEXT DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `industries` (`name`, `icon`, `description`, `sort_order`) VALUES
('Offices & Startups',    '🏢', 'Ergonomic workstations, executive cabins & reception desks', 1),
('Hotels & Hospitality',  '🏨', 'Room sets, lobby & banquet furniture at scale',             2),
('Cafes & Restaurants',   '☕', 'Bar stools, café tables & indoor-outdoor seating',           3),
('Hospitals & Clinics',   '🏥', 'Patient benches, waiting chairs & ward essentials',         4),
('Schools & Institutes',  '🏫', 'Classroom desks, lab furniture & library sets',             5),
('Homes & Residences',    '🏠', 'Living, bedroom & dining furniture for modern homes',       6);

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE `categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `icon`        VARCHAR(50) DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `show_in_nav` TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`  VARCHAR(255) DEFAULT NULL,
  `meta_desc`   TEXT DEFAULT NULL,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `sort_order`, `show_in_nav`) VALUES
('Living',      'living',      'Sofas, recliners, TV units and living room furniture',       'bi-house-heart', 1, 1),
('Dining',      'dining',      'Dining tables, chairs, bar stools and crockery units',      'bi-egg-fried',   2, 1),
('Bedroom',     'bedroom',     'Beds, wardrobes, dressing tables and bedroom essentials',   'bi-moon-stars',  3, 1),
('Office',      'office',      'Workstations, executive chairs, meeting tables & storage',  'bi-briefcase',   4, 1),
('Commercial',  'commercial',  'Bulk solutions for hotels, hospitals, cafes & schools',     'bi-building',    5, 1),
('Decor',       'decor',       'Home decor, accessories and accent pieces',                 'bi-stars',       6, 1);

-- ============================================================
-- TABLE: subcategories
-- ============================================================
CREATE TABLE `subcategories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT UNSIGNED NOT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_cat` (`category_id`),
  CONSTRAINT `fk_subcat_cat` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `subcategories` (`category_id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Sofa',             'sofa',             1),
(1, 'Luxury Sofa Set',  'luxury-sofa',      2),
(1, 'Modern TV Unit',   'tv-unit',          3),
(1, 'Designer Recliner','recliner',         4),
(1, 'Lounge Chair',     'lounge-chair',     5),
(1, 'Centre Table',     'center-table',     6),
(2, 'Dining Table',     'dining-table',     1),
(2, 'Dining Chair',     'dining-chair',     2),
(2, 'Coffee Table',     'coffee-table',     3),
(2, 'Bar Stool',        'bar-stool',        4),
(2, 'Crockery Unit',    'crockery-unit',    5),
(3, 'King Size Bed',    'bed',              1),
(3, 'Queen Size Bed',   'queen-bed',        2),
(3, 'Wardrobe',         'wardrobe',         3),
(3, 'Side Table',       'side-table',       4),
(3, 'Dressing Table',   'dressing-table',   5),
(4, 'Executive Chair',  'executive-chair',  1),
(4, 'Workstation',      'workstation',      2),
(4, 'Meeting Table',    'meeting-table',    3),
(4, 'Reception Desk',   'reception-desk',   4),
(4, 'Storage Cabinet',  'storage-cabinet',  5),
(5, 'Hotels & Hospitality','hotel',         1),
(5, 'Offices & Startups',  'office-bulk',   2),
(5, 'Cafes & Restaurants', 'cafe',          3),
(5, 'Hospitals & Clinics', 'hospital',      4),
(5, 'Schools & Institutes','school',        5);

-- ============================================================
-- TABLE: collections
-- ============================================================
CREATE TABLE `collections` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `slug`        VARCHAR(120) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `collections` (`name`, `slug`, `description`, `sort_order`) VALUES
('Modern Luxe',      'modern-luxe',      'Contemporary luxury for modern spaces', 1),
('Classic Heritage', 'classic-heritage', 'Timeless elegance with traditional craft', 2),
('Office Pro',       'office-pro',       'Professional workspace solutions',       3),
('Hospitality Plus', 'hospitality-plus', 'Commercial-grade hospitality furniture', 4);

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE `products` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_code`   VARCHAR(50) NOT NULL,
  `name`           VARCHAR(200) NOT NULL,
  `slug`           VARCHAR(220) NOT NULL UNIQUE,
  `category_id`    INT UNSIGNED DEFAULT NULL,
  `subcategory_id` INT UNSIGNED DEFAULT NULL,
  `collection_id`  INT UNSIGNED DEFAULT NULL,
  `description`    TEXT DEFAULT NULL,
  `short_desc`     VARCHAR(500) DEFAULT NULL,
  `material`       VARCHAR(200) DEFAULT NULL,
  `dimensions`     VARCHAR(200) DEFAULT NULL,
  `weight`         VARCHAR(100) DEFAULT NULL,
  `colors`         VARCHAR(200) DEFAULT NULL,
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `is_new`         TINYINT(1) NOT NULL DEFAULT 0,
  `is_bestseller`  TINYINT(1) NOT NULL DEFAULT 0,
  `status`         ENUM('active','inactive','out_of_stock') NOT NULL DEFAULT 'active',
  `sort_order`     INT NOT NULL DEFAULT 0,
  `views`          INT UNSIGNED NOT NULL DEFAULT 0,
  `meta_title`     VARCHAR(255) DEFAULT NULL,
  `meta_desc`      TEXT DEFAULT NULL,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_code_scope` (`product_code`, `category_id`, `subcategory_id`),
  UNIQUE INDEX `idx_slug` (`slug`),
  INDEX `idx_cat` (`category_id`),
  INDEX `idx_subcat` (`subcategory_id`),
  INDEX `idx_featured` (`is_featured`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_prod_cat`    FOREIGN KEY (`category_id`)    REFERENCES `categories`(`id`)    ON DELETE SET NULL,
  CONSTRAINT `fk_prod_subcat` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_prod_col`    FOREIGN KEY (`collection_id`)  REFERENCES `collections`(`id`)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample products
INSERT INTO `products` (`product_code`,`name`,`slug`,`category_id`,`subcategory_id`,`description`,`short_desc`,`material`,`is_featured`,`is_new`,`is_bestseller`,`status`) VALUES
('SF001','Luxury Sofa Set','luxury-sofa-set',1,2,'Premium quality Luxury Sofa Set designed for modern homes with comfort and elegance. Upholstered in high-grade fabric with solid wood frame.','Premium fabric sofa set for modern living','High-grade fabric, Solid hardwood',1,1,1,'active'),
('SF002','3-Seater Comfort Sofa','3-seater-comfort-sofa',1,1,'Contemporary sofa featuring clean lines and premium materials. Perfect for any modern living space.','Contemporary sofa for modern living','Premium velvet, Solid wood legs',1,0,1,'active'),
('BD001','King Size Bed Frame','king-size-bed-frame',3,12,'Luxurious king-size bed with upholstered headboard and solid wood base. Brings elegance to any master bedroom.','King size bed with upholstered headboard','Engineered wood, Premium fabric',1,1,0,'active'),
('OF001','Executive Office Chair','executive-office-chair',4,17,'Ergonomic executive chair with lumbar support, adjustable height and premium PU leather upholstery.','Ergonomic executive chair with lumbar support','PU leather, Chrome base',1,0,1,'active'),
('DT001','6-Seater Dining Table','6-seater-dining-table',2,7,'Solid wood dining table with 6 matching chairs. Perfect for family gatherings and entertaining.','Solid wood dining table with chairs','Sheesham wood, Lacquer finish',1,1,0,'active'),
('WS001','Premium Workstation','premium-workstation',4,18,'Modern office workstation designed for productivity. Cable management and modular design.','Modern modular office workstation','Steel frame, MDF top',0,1,0,'active');

-- ============================================================
-- TABLE: product_images
-- ============================================================
CREATE TABLE `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(255) NOT NULL,
  `alt_text`   VARCHAR(200) DEFAULT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_product` (`product_id`),
  CONSTRAINT `fk_pimg_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `product_images` (`product_id`,`image`,`alt_text`,`is_primary`,`sort_order`) VALUES
(1,'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&q=80','Luxury Sofa Set - Front View',1,1),
(1,'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80','Luxury Sofa Set - Side View',0,2),
(2,'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80','3-Seater Comfort Sofa',1,1),
(3,'https://images.unsplash.com/photo-1505693314120-0d443867891c?w=800&q=80','King Size Bed Frame',1,1),
(4,'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&q=80','Executive Office Chair',1,1),
(5,'https://images.unsplash.com/photo-1617806118233-18e1de247200?w=800&q=80','6-Seater Dining Table',1,1),
(6,'https://images.unsplash.com/photo-1506439773649-6e0eb8cfb237?w=800&q=80','Premium Workstation',1,1);

-- ============================================================
-- TABLE: product_features
-- ============================================================
CREATE TABLE `product_features` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `feature`    VARCHAR(300) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_product` (`product_id`),
  CONSTRAINT `fk_pfeat_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `product_features` (`product_id`,`feature`,`sort_order`) VALUES
(1,'Premium fabric upholstery',1),(1,'Solid hardwood frame',2),(1,'High-density foam cushions',3),(1,'Available in custom colors',4),
(2,'3-seater configuration',1),(2,'Velvet fabric cover',2),(2,'Tapered solid wood legs',3),
(3,'Upholstered headboard',1),(3,'Storage under-bed option',2),(3,'Solid wood slats',3),
(4,'Lumbar support system',1),(4,'Height adjustable',2),(4,'360° swivel',3),(4,'Premium PU leather',4),
(5,'Seats 6 comfortably',1),(5,'Solid sheesham wood',2),(5,'Easy assembly',3),
(6,'Modular design',1),(6,'Cable management tray',2),(6,'Adjustable storage',3);

-- ============================================================
-- TABLE: homepage_banners
-- ============================================================
CREATE TABLE `homepage_banners` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(200) NOT NULL,
  `subtitle`    VARCHAR(300) DEFAULT NULL,
  `label`       VARCHAR(100) DEFAULT NULL,
  `image`       VARCHAR(255) DEFAULT NULL,
  `btn1_text`   VARCHAR(100) DEFAULT NULL,
  `btn1_url`    VARCHAR(255) DEFAULT NULL,
  `btn2_text`   VARCHAR(100) DEFAULT NULL,
  `btn2_url`    VARCHAR(255) DEFAULT NULL,
  `sort_order`  INT NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `homepage_banners` (`title`,`subtitle`,`label`,`image`,`btn1_text`,`btn1_url`,`btn2_text`,`btn2_url`,`sort_order`,`is_active`) VALUES
('Premium Spaces Begin Here','End-to-end furniture solutions for homes, offices, hotels, schools & hospitality spaces — handcrafted with precision and delivered with care.','Commercial & Residential Furniture','https://images.unsplash.com/photo-1497366216548-37526070297c?w=900&q=85','Explore Collections','category.php?cat=all','Get Free Quote','contact.php',1,1);

-- ============================================================
-- TABLE: inquiries
-- ============================================================
CREATE TABLE `inquiries` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`         ENUM('product','bulk','whatsapp','general') NOT NULL DEFAULT 'general',
  `product_id`   INT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(200) DEFAULT NULL,
  `product_code` VARCHAR(50) DEFAULT NULL,
  `name`         VARCHAR(150) NOT NULL,
  `email`        VARCHAR(150) DEFAULT NULL,
  `phone`        VARCHAR(20) NOT NULL,
  `message`      TEXT DEFAULT NULL,
  `source`       VARCHAR(100) DEFAULT NULL,
  `is_read`      TINYINT(1) NOT NULL DEFAULT 0,
  `is_replied`   TINYINT(1) NOT NULL DEFAULT 0,
  `admin_notes`  TEXT DEFAULT NULL,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_type`   (`type`),
  INDEX `idx_read`   (`is_read`),
  INDEX `idx_product`(`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: contact_messages
-- ============================================================
CREATE TABLE `contact_messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `phone`      VARCHAR(20) DEFAULT NULL,
  `subject`    VARCHAR(250) DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `is_replied` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: cart_items
-- ============================================================
CREATE TABLE `cart_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id`   VARCHAR(128) NOT NULL,
  `product_id`   INT UNSIGNED NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `product_code` VARCHAR(50) NOT NULL,
  `image`        VARCHAR(255) DEFAULT NULL,
  `quantity`     INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_session` (`session_id`),
  INDEX `idx_product` (`product_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: reviews
-- ============================================================
CREATE TABLE `reviews` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(150) DEFAULT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `review`     TEXT NOT NULL,
  `image`      VARCHAR(255) DEFAULT NULL,
  `status`     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_product` (`product_id`),
  INDEX `idx_status`  (`status`),
  CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLE: testimonials
-- ============================================================
CREATE TABLE `testimonials` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `company`    VARCHAR(200) DEFAULT NULL,
  `location`   VARCHAR(100) DEFAULT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `review`     TEXT NOT NULL,
  `image`      VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimonials` (`name`,`company`,`location`,`rating`,`review`,`sort_order`,`is_active`) VALUES
('Rajesh Sharma',  'TechCorp India',      'Gurgaon',      5, 'NOVAHOMZ transformed our office space completely. The workstations are sturdy, elegant and our team loves them. Highly recommend for bulk office orders.', 1, 1),
('Priya Mehta',    'Hotel Grand Palace',  'New Delhi',     5, 'We furnished 40 rooms and the lobby with NOVAHOMZ. The quality is outstanding and delivery was on time. Our guests frequently compliment the interiors.', 2, 1),
('Amit Kapoor',    'Home Owner',          'Noida',         5, 'Ordered a complete bedroom set for our new flat. The quality exceeded our expectations and installation was professional and quick.', 3, 1),
('Sunita Agarwal', 'Cafe Bloom',          'Faridabad',     5, 'Our cafe looks stunning with NOVAHOMZ furniture. The pieces are durable and the aesthetic is exactly what we wanted. Great value for money.', 4, 1);

SET foreign_key_checks = 1;
