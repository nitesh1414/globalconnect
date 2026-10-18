-- =======================================================
-- Global Connect CMS - Database Schema & Initial Data
-- Core PHP & MySQL Multi-Page CMS
-- =======================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- -------------------------------------------------------
-- Table: users
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `full_name` VARCHAR(100) DEFAULT 'Administrator',
  `role` VARCHAR(20) DEFAULT 'admin',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin User: admin / admin123
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`) VALUES
(1, 'admin', '$2y$10$o8kU13i95O3Qyv2kHZZQ9u3wS2vXN7t2b6pE0gL1n9f7j3c4d5e6a', 'admin@globalconnectsg.com', 'Admin User', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- -------------------------------------------------------
-- Table: categories
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT NULL,
  `menu_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `menu_order`) VALUES
(1, 'General', 'general', 'Core company pages', 1),
(2, 'Trading', 'trading', 'Commodities, Minerals, Agro & Energy Trading', 2),
(3, 'Finance', 'finance', 'Trade Finance, Banking Support & Advisory Solutions', 3),
(4, 'Consulting', 'consulting', 'Corporate Financial & Advisory Consulting', 4)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- -------------------------------------------------------
-- Table: pages
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `subtitle` VARCHAR(255) DEFAULT '',
  `banner_title` VARCHAR(255) DEFAULT '',
  `banner_subtitle` VARCHAR(255) DEFAULT '',
  `featured_image` VARCHAR(255) DEFAULT '',
  `summary` TEXT NULL,
  `content` LONGTEXT NULL,
  `meta_title` VARCHAR(255) DEFAULT '',
  `meta_description` TEXT NULL,
  `meta_keywords` VARCHAR(255) DEFAULT '',
  `status` ENUM('published', 'draft') DEFAULT 'published',
  `menu_order` INT DEFAULT 0,
  `is_system` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`category_id`),
  INDEX (`slug`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table: hero_slides (Hero Section Banners & Sliders)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NULL,
  `subtitle` TEXT NULL,
  `badge_text` VARCHAR(100) NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `button_text` VARCHAR(100) NULL,
  `button_link` VARCHAR(255) NULL,
  `secondary_button_text` VARCHAR(100) NULL,
  `secondary_button_link` VARCHAR(255) NULL,
  `show_text_overlay` TINYINT(1) DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Hero Slides
INSERT INTO `hero_slides` (`id`, `title`, `subtitle`, `badge_text`, `image_path`, `button_text`, `button_link`, `secondary_button_text`, `secondary_button_link`, `show_text_overlay`, `status`, `sort_order`) VALUES
(1, 'Global Connect Group of Companies', 'Singapore • India • Indonesia • Hongkong | Sourcing, Supply Chain & Financial Structuring', 'Singapore • India • Global Network', 'assets/images/global-connect-banner-1.png', 'Explore Trading', 'trading.php', 'Trade Finance', 'finance.php', 0, 'active', 1),
(2, 'Your Strategic Partner in Sourcing & Supply Chain', 'We are a global business enterprise headquartered in Singapore and India. At Global Connect, we believe in building meaningful partnerships that drive success across international borders.', 'Singapore • India • Global Network', 'assets/images/banner-hero-1.png', 'Explore Trading', 'trading.php', 'Trade Finance', 'finance.php', 1, 'active', 2)
ON DUPLICATE KEY UPDATE `image_path`=`image_path`;

-- -------------------------------------------------------
-- Table: settings
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'Global Connect'),
('site_tagline', 'We are a global business enterprise headquartered in Singapore And India'),
('company_name', 'Global Connect'),
('header_phone_sg', '(65) 89451100'),
('header_phone_roam', '(91) 99100 02299'),
('fax_number', '(65) 98260001 / (91) 99100 02299'),
('contact_email_primary', 'admin@globalconnectsg.com'),
('contact_email_secondary', 'shammy@globalconnectsg.com'),
('contact_email_tertiary', 'tsnshammy@gmail.com'),
('address_sg', '101 KITCHENER ROAD #03-43 JALAN BESAR PLAZA SINGAPORE (208511)'),
('address_india', 'NO. 211 RAJNIGANDHA BLOCK, GARDEN APARTMENT, 21 VITTHAL MALLYA ROAD (opp. UB CITY) BANGLORE 560001 INDIA'),
('address_branches', 'New Delhi, Mumbai, Hyderabad and Chennai (By Appointment ONLY – IN HYATT GROUP OF HOTELS)'),
('address_indonesia', 'HOTEL GRAND HYATT PLAZA, JL. M.H. THAMRIN KAV. 28-30, GONDANGDIA, MENTENG, JAKARTA, 10350, INDONESIA'),
('address_hongkong', 'HOTEL HYATT REGENCY 18 HANOI ROAD TSIM SHA TSU KOWLOON HONGKONG'),
('stat_partners', '3,600+'),
('stat_countries', '19+'),
('stat_banks', '19+'),
('logo_url', '/assets/images/logo-design-gcv.png'),
('banner_url', '/assets/images/global-connect-banner-1.png'),
('footer_about', 'At Global Connect, we believe in building meaningful partnerships that drive success across borders. With our expertise and network, we connect businesses to new markets and lasting opportunities.')
ON DUPLICATE KEY UPDATE `setting_value`=`setting_value`;

-- -------------------------------------------------------
-- Table: contacts (Inquiries)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(50) DEFAULT '',
  `subject` VARCHAR(200) DEFAULT '',
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `ip_address` VARCHAR(50) DEFAULT '',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------
-- Table: media
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `filepath` VARCHAR(255) NOT NULL,
  `filetype` VARCHAR(100) DEFAULT '',
  `filesize` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
