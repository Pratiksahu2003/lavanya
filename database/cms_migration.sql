-- ============================================================
-- LAVANYAA CREATION — CMS Migration
-- Safe additions only — no table drops, no column removals
-- ============================================================

-- ── Premium Clients table (new)
CREATE TABLE IF NOT EXISTS `premium_clients` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(200) NOT NULL,
  `logo`   VARCHAR(255) DEFAULT NULL,
  `website`       VARCHAR(255) DEFAULT NULL,
  `description`   TEXT DEFAULT NULL,
  `display_order` INT NOT NULL DEFAULT 0,
  `status`        TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `premium_clients` (`name`,`logo`,`website`,`description`,`display_order`,`status`) VALUES
('The Oberoi',       NULL, NULL, NULL, 1, 1),
('IBIS Hotel',       NULL, NULL, NULL, 2, 1),
('Lemon Tree',       NULL, NULL, NULL, 3, 1),
('Vision Hospitality',NULL, NULL, NULL, 4, 1),
('CPRI',             NULL, NULL, NULL, 5, 1),
('Sun Glassworks Pvt Ltd',          NULL, NULL, NULL, 6, 1),
('Honda India Powder Products Ltd', NULL, NULL, NULL, 7, 1);


ALTER TABLE `products`
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `categories`
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `subcategories`
  ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `inquiries` ADD INDEX IF NOT EXISTS `idx_is_read` (`is_read`);

ALTER TABLE `settings`
  ADD COLUMN IF NOT EXISTS `setting_group` VARCHAR(60) DEFAULT 'general' AFTER `label`;


ALTER TABLE `contact_messages`
  ADD COLUMN IF NOT EXISTS `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `message`;

INSERT IGNORE INTO `settings` (`setting_key`,`setting_val`,`label`) VALUES
('site_name',       'Lavanyaa Creation',                 'Business Name'),
('tagline',         'Premium Furniture Services',         'Tagline'),
('phone1',          '+91 8796591267',                     'Phone 1'),
('address',         'Than Singh Nagar, Anand Parbat, Near Saraswati Memorial Hospital, Central Delhi, Delhi - 110005','Address'),
('business_hours',  'Mon–Sat, 10am–7pm',                 'Business Hours'),
('announcement_bar','Complimentary Delivery & Professional Installation Across India','Announcement Bar'),
('meta_title',      'Lavanyaa Creation — Premium Furniture Services','Meta Title'),
('meta_desc',       'Distinguished name in premium furniture design and bespoke interior solutions since 2019.','Meta Description'),
('footer_copyright','© 2025 Lavanyaa Creation. All Rights Reserved.','Footer Copyright');
