-- ====================================================================
-- CYBER-TECH GPU STORE: DATABASE DEFINITION & MOCK DATA (MySQL)
-- Charset: utf8mb4_unicode_ci
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `gpu_store`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gpu_store`;

-- Disable foreign key checks while setting up schema
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------
-- 1. Table: users
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'member') NOT NULL DEFAULT 'member',
  `avatar_url` VARCHAR(255) DEFAULT 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. Table: brands (ผู้ผลิตการ์ด / AIB Partners)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(60) NOT NULL UNIQUE,
  `country` VARCHAR(60) NOT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `logo_url` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. Table: chipsets (ค่ายชิปเซ็ตประมวลผลหลัก)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `chipsets`;
CREATE TABLE `chipsets` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `manufacturer` ENUM('NVIDIA', 'AMD', 'Intel') NOT NULL,
  `series_name` VARCHAR(60) NOT NULL,
  `architecture` VARCHAR(60) NOT NULL,
  `badge_color` VARCHAR(20) NOT NULL DEFAULT '#00FF66',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. Table: gpus (รายการการ์ดจอ)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `gpus`;
CREATE TABLE `gpus` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand_id` INT UNSIGNED NOT NULL,
  `chipset_id` INT UNSIGNED NOT NULL,
  `model_name` VARCHAR(180) NOT NULL,
  `chipset_model` VARCHAR(80) NOT NULL,
  `series` VARCHAR(60) NOT NULL,
  `vram_gb` INT NOT NULL,
  `memory_type` VARCHAR(30) NOT NULL DEFAULT 'GDDR6X',
  `bus_width` INT NOT NULL,
  `core_clock` INT NOT NULL COMMENT 'MHz',
  `boost_clock` INT NOT NULL COMMENT 'MHz',
  `tdp_watts` INT NOT NULL COMMENT 'Watts',
  `price` DECIMAL(10,2) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `image_url` TEXT NOT NULL,
  `description` TEXT,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('available', 'low_stock', 'out_of_stock') NOT NULL DEFAULT 'available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_gpus_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_gpus_chipset` FOREIGN KEY (`chipset_id`) REFERENCES `chipsets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. Table: orders (ประวัติการสั่งซื้อสำหรับรายงาน)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NOT NULL,
  `gpu_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('completed', 'processing', 'cancelled') NOT NULL DEFAULT 'completed',
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_orders_gpu` FOREIGN KEY (`gpu_id`) REFERENCES `gpus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- MOCK DATA INSERTION
-- ====================================================================

-- 1. Users (Passwords hashed with BCRYPT: 'admin123' and 'user123')
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `avatar_url`) VALUES
(1, 'admin', 'admin@cybergpu.io', '$2y$10$w4r2vN322J5E22Jz9F4cIu33C5uI2aWl47r8Lg8K4oYj4cO9C.oia', 'Neo Cyber Admin', 'admin', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80'),
(2, 'gamer_pro', 'gamer@cybergpu.io', '$2y$10$Ew.7ZzF4U4L94P6H6X.zTuU1gHn0YlP8A7YnN3p8O9t9X5y7B7Q.O', 'Aiden Walker', 'member', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=300&q=80');

-- 2. Brands
INSERT INTO `brands` (`id`, `name`, `country`, `website`, `logo_url`) VALUES
(1, 'ASUS ROG', 'Taiwan', 'https://rog.asus.com', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=150&q=80'),
(2, 'MSI Gaming', 'Taiwan', 'https://msi.com', 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=150&q=80'),
(3, 'GIGABYTE AORUS', 'Taiwan', 'https://gigabyte.com', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=150&q=80'),
(4, 'ZOTAC Gaming', 'Hong Kong', 'https://zotac.com', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=150&q=80'),
(5, 'Sapphire Tech', 'Hong Kong', 'https://sapphiretech.com', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=150&q=80'),
(6, 'PowerColor', 'Taiwan', 'https://powercolor.com', 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=150&q=80'),
(7, 'Intel Arc Direct', 'USA', 'https://intel.com/arc', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=150&q=80');

-- 3. Chipsets
INSERT INTO `chipsets` (`id`, `manufacturer`, `series_name`, `architecture`, `badge_color`) VALUES
(1, 'NVIDIA', 'GeForce RTX 40 Series', 'Ada Lovelace', '#00FF66'),
(2, 'AMD', 'Radeon RX 7000 Series', 'RDNA 3 Chiplet', '#FF0055'),
(3, 'Intel', 'Arc Alchemist & Battlemage', 'Xe-HPG & Xe2', '#00F0FF');

-- 4. GPUs (12 models across NVIDIA, AMD, Intel with varying VRAM & Stock levels)
INSERT INTO `gpus` (`id`, `brand_id`, `chipset_id`, `model_name`, `chipset_model`, `series`, `vram_gb`, `memory_type`, `bus_width`, `core_clock`, `boost_clock`, `tdp_watts`, `price`, `stock`, `image_url`, `description`, `is_featured`, `status`) VALUES
(1, 1, 1, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB', 'GeForce RTX 4090', 'RTX 40 Series', 24, 'GDDR6X', 384, 2235, 2640, 450, 78900.00, 4, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80', 'The undisputed king of 4K ultra gaming and deep learning acceleration. Features 24GB GDDR6X, Axial-tech fans, and patented vapor chamber.', 1, 'available'),
(2, 2, 1, 'MSI GeForce RTX 4080 SUPER 16GB Gaming X Trio', 'GeForce RTX 4080 SUPER', 'RTX 40 Series', 16, 'GDDR6X', 256, 2295, 2610, 320, 42900.00, 8, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80', 'Incredible 4K gaming powerhouse with TRI FROZR 3 thermal design, TORX Fan 5.0, and stylish mystic light RGB synchronization.', 1, 'available'),
(3, 3, 1, 'GIGABYTE GeForce RTX 4070 SUPER WINDFORCE OC 12GB', 'GeForce RTX 4070 SUPER', 'RTX 40 Series', 12, 'GDDR6X', 192, 1980, 2505, 220, 24500.00, 15, 'https://images.unsplash.com/photo-1555680202-c86f0e12f086?auto=format&fit=crop&w=800&q=80', 'Sweet spot for 1440p high-refresh ray tracing gaming. Equipped with 3x alternate spinning fans and pure copper composite heat pipes.', 1, 'available'),
(4, 4, 1, 'ZOTAC Gaming GeForce RTX 4060 Ti 8GB Twin Edge', 'GeForce RTX 4060 Ti', 'RTX 40 Series', 8, 'GDDR6', 128, 2310, 2535, 160, 14900.00, 2, 'https://images.unsplash.com/photo-1587202372574-6330efacff35?auto=format&fit=crop&w=800&q=80', 'Compact dual-slot design perfect for SFF small form factor builds without compromising DLSS 3 frame generation performance.', 0, 'low_stock'),
(5, 5, 2, 'Sapphire NITRO+ AMD Radeon RX 7900 XTX Vapor-X 24GB', 'Radeon RX 7900 XTX', 'RX 7000 Series', 24, 'GDDR6', 384, 2510, 2680, 420, 41500.00, 5, 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=800&q=80', 'Uncapped raw rasterization beast with 24GB VRAM and innovative Vapor-X cooling chamber for maximum FPS in native 4K titles.', 1, 'available'),
(6, 6, 2, 'PowerColor Hellhound AMD Radeon RX 7800 XT 16GB', 'Radeon RX 7800 XT', 'RX 7000 Series', 16, 'GDDR6', 256, 2124, 2430, 263, 20900.00, 12, 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=800&q=80', 'High-end 1440p gaming fidelity with 16GB VRAM, dual BIOS mode (OC / Silent), and LED switch amethyst purple lighting.', 1, 'available'),
(7, 3, 2, 'GIGABYTE Radeon RX 7700 XT GAMING OC 12GB', 'Radeon RX 7700 XT', 'RX 7000 Series', 12, 'GDDR6', 192, 2171, 2599, 245, 16900.00, 3, 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=800&q=80', 'Outstanding price-to-performance for esports and immersive AAA gaming. Features WINDFORCE cooling system and screen cooling.', 0, 'low_stock'),
(8, 7, 3, 'Intel Arc A770 Limited Edition 16GB GDDR6', 'Arc A770', 'Arc Alchemist', 16, 'GDDR6', 256, 2100, 2400, 225, 12900.00, 6, 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80', 'Intel flagship graphics card equipped with full XeSS AI upscaling, hardware AV1 dual-encoders, and generous 16GB memory buffer.', 1, 'available'),
(9, 7, 3, 'Sparkle Intel Arc A750 Titan OC 8GB GDDR6', 'Arc A750', 'Arc Alchemist', 8, 'GDDR6', 256, 2050, 2200, 225, 8900.00, 1, 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80', 'Best budget 1080p card with modern DirectX 12 Ultimate hardware architecture and full ray tracing unit support.', 0, 'low_stock'),
(10, 1, 1, 'ASUS TUF Gaming GeForce RTX 4070 Ti SUPER 16GB', 'GeForce RTX 4070 Ti SUPER', 'RTX 40 Series', 16, 'GDDR6X', 256, 2340, 2670, 285, 33900.00, 7, 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80', 'Military-grade capacitors and heavy metal exoskeleton design for endurance gaming under demanding 2K/4K conditions.', 0, 'available'),
(11, 7, 3, 'Intel Arc B580 Battlemage Limited 12GB GDDR6', 'Arc B580', 'Arc Battlemage', 12, 'GDDR6', 192, 2670, 2850, 190, 10900.00, 10, 'https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80', 'Next-generation Battlemage Xe2 core architecture offering outstanding performance-per-watt and upgraded XMX AI engines.', 1, 'available'),
(12, 2, 2, 'MSI Radeon RX 7600 XT GAMING X 16GB', 'Radeon RX 7600 XT', 'RX 7000 Series', 16, 'GDDR6', 128, 2470, 2755, 190, 13500.00, 0, 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80', 'Massive 16GB memory buffer for heavy textures and future-proof 1080p/1440p content creation pipelines.', 0, 'out_of_stock');

-- 5. Mock Orders for Reporting & Analytics
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `gpu_id`, `quantity`, `unit_price`, `total_price`, `order_date`, `status`) VALUES
(1, 'ORD-2026-001', 2, 1, 1, 78900.00, 78900.00, '2026-10-01 14:20:00', 'completed'),
(2, 'ORD-2026-002', 2, 3, 2, 24500.00, 49000.00, '2026-10-03 10:15:30', 'completed'),
(3, 'ORD-2026-003', 2, 5, 1, 41500.00, 41500.00, '2026-10-05 18:45:10', 'completed'),
(4, 'ORD-2026-004', 2, 8, 2, 12900.00, 25800.00, '2026-10-08 11:30:00', 'completed'),
(5, 'ORD-2026-005', 2, 2, 1, 42900.00, 42900.00, '2026-10-12 16:05:40', 'completed'),
(6, 'ORD-2026-006', 2, 11, 3, 10900.00, 32700.00, '2026-10-15 09:22:15', 'completed');
