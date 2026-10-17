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
  `wallet_balance` DECIMAL(12,2) NOT NULL DEFAULT 9999999.00,
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

-- --------------------------------------------------------------------
-- 6. Table: inventory_logs (ประวัติการเติมสต็อกและบันทึกการเพิ่ม/ลบสินค้า)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS `inventory_logs`;
CREATE TABLE `inventory_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `log_type` ENUM('restock', 'add', 'edit', 'delete') NOT NULL,
  `gpu_id` INT UNSIGNED DEFAULT NULL,
  `model_name` VARCHAR(180) NOT NULL,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `username` VARCHAR(50) DEFAULT 'admin',
  `old_stock` INT DEFAULT NULL,
  `new_stock` INT DEFAULT NULL,
  `stock_change` INT DEFAULT NULL,
  `action_details` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_log_type` (`log_type`),
  INDEX `idx_created_at` (`created_at`)
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
(7, 'Intel Arc Direct', 'USA', 'https://intel.com/arc', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=150&q=80'),
(8, 'PALIT', 'Taiwan', 'https://palit.com', 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=150&q=80'),
(9, 'COLORFUL', 'China', 'https://en.colorful.cn', 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=150&q=80'),
(10, 'INNO3D', 'Hong Kong', 'https://inno3d.com', 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=150&q=80'),
(11, 'GALAX', 'Hong Kong', 'https://galax.com', 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=150&q=80'),
(12, 'ASRock', 'Taiwan', 'https://asrock.com', 'https://images.unsplash.com/photo-1563089145-599997674d42?auto=format&fit=crop&w=150&q=80'),
(13, 'XFX Gaming', 'USA', 'https://xfxforce.com', 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=150&q=80');

-- 3. Chipsets
INSERT INTO `chipsets` (`id`, `manufacturer`, `series_name`, `architecture`, `badge_color`) VALUES
(1, 'NVIDIA', 'GeForce RTX 40 & 50 Series', 'Ada Lovelace & Blackwell', '#00FF66'),
(2, 'AMD', 'Radeon RX 6000 & 9000 Series', 'RDNA 2 / 3 / 4', '#FF0055'),
(3, 'Intel', 'Arc Alchemist & Battlemage', 'Xe-HPG & Xe2', '#00F0FF'),
(4, 'NVIDIA', 'GeForce RTX 30 Series', 'Ampere Architecture', '#00FF66');


-- 4. GPUs (36 curated models with authentic Box+Card packshots)
INSERT INTO `gpus` (`id`, `brand_id`, `chipset_id`, `model_name`, `chipset_model`, `series`, `vram_gb`, `memory_type`, `bus_width`, `core_clock`, `boost_clock`, `tdp_watts`, `price`, `stock`, `image_url`, `description`, `is_featured`, `status`) VALUES
(1, 6, 2, 'VGA(การ์ดจอ) POWERCOLOR FIGHTER RADEON RX 6500 XT - 4GB GDDR6 V3 (AXRX 6500XT 4GBD6-DHV3) (3Y)', 'Radeon RX 6500 XT', 'RX 6000 Series', 4, 'GDDR6', 64, 2610, 2825, 107, 5490.00, 15, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products103815_800.jpg', 'การ์ดจอรุ่นประหยัดคุ้มค่าสำหรับการเล่นเกม 1080p มอบประสิทธิภาพกราฟิก RDNA 2 พร้อมพัดลมคู่ระบายความร้อนเงียบสนิท ประกันศูนย์ไทย 3 ปีเต็ม', 1, 'available'),
(2, 8, 4, 'VGA(การ์ดจอ) PALIT GEFORCE RTX 3050 STORM X 6GB GDDR6 (NE63050018JE-1072F) (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1470, 70, 6990.00, 18, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products85859_800.jpg', 'การ์ดจอขนาดกะทัดรัดแบบ Single Fan ไม่ต้องต่อไฟเพิ่ม ประหยัดพลังงานขั้นสุด รองรับ Ray Tracing และ NVIDIA DLSS เหมาะสำหรับเคส ITX และอัปเกรดคอมเดิม', 0, 'available'),
(3, 4, 4, 'VGA(การ์ดจอ) ZOTAC GAMING GEFORCE RTX 3050 TWIN EDGE OC - 6GB GDDR6 (ZT-A30510H-10L) (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1492, 70, 7490.00, 12, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products106193_800.jpg', 'ดีไซน์พัดลมคู่ IceStorm 2.0 ระบายความร้อนดีเยี่ยม ปรับจูนคล็อกโอเวอร์คล็อกมาจากโรงงานเพื่อเฟรมเรตที่ลื่นไหล ประกันศูนย์ไทย 3 ปี', 0, 'available'),
(4, 9, 4, 'VGA(การ์ดจอ) COLORFUL GEFORCE RTX 3050 6G V4-V - 6GB GDDR6 (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1470, 70, 7490.00, 10, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products125094_800.jpg', 'COLORFUL ซีรีส์ V4-V ดีไซน์โมเดิร์นสีดำด้าน พัดลมคู่เสียงเงียบ เสถียรภาพสูง ประหยัดไฟ ไม่ต้องต่อไฟเพิ่ม รองรับจอความละเอียดสูง', 0, 'available'),
(5, 10, 4, 'VGA(การ์ดจอ) INNO3D GEFORCE RTX 3050 TWIN X2 V2 - 6GB GDDR6 (N30502-06D6-1880VA60) (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1470, 70, 7490.00, 8, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products125878_800.jpg', 'INNO3D Twin X2 V2 ครีบระบายความร้อนอลูมิเนียมหนาพิเศษ พัดลม Scythe blade กระจายลมเต็มกำลัง ทำงานเงียบกริบในทุกการเล่นเกม', 0, 'available'),
(6, 2, 4, 'VGA(การ์ดจอ) MSI GEFORCE RTX 3050 VENTUS 2X E 6G OC - 6GB GDDR6 (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1492, 70, 7990.00, 14, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products77585_800.jpg', 'MSI VENTUS 2X พร้อมเทคโนโลยี TORX Fan 3.0 สร้างแรงดันลมเพิ่มขึ้น 50% แบ็คเพลตเสริมความแข็งแกร่ง รองรับ MSI Center ปรับแต่งง่ายดาย', 1, 'available'),
(7, 3, 4, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 3050 WINDFORCE OC V2 6G - 6GB GDDR6 (GV-N3050WF2OCV2-6GD) (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1477, 70, 7990.00, 16, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products77610_800.jpg', 'ระบบระบายความร้อน WINDFORCE 2X พัดลมหมุนสลับทิศทาง Alternate Spinning ป้องกันลมหมุนวน เสียงเงียบ ความร้อนต่ำ', 0, 'available'),
(8, 11, 4, 'VGA(การ์ดจอ) GALAX GEFORCE RTX 3050 EX - 6GB GDDR6 (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1485, 70, 7990.00, 7, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products84014_800.jpg', 'GALAX EX ซีรีส์ยอดนิยม รองรับ 1-Click OC ผ่านแอป Xtreme Tuner พร้อมไฟ LED เพิ่มความสวยงามให้กับเคสคอมของคุณ', 0, 'available'),
(9, 1, 4, 'VGA(การ์ดจอ) ASUS DUAL GEFORCE RTX 3050 O6G - 6GB GDDR6 (DUAL-RTX3050-O6G) (3Y)', 'GeForce RTX 3050', 'RTX 30 Series', 6, 'GDDR6', 96, 1042, 1537, 70, 8490.00, 11, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products55523_800.jpg', 'ASUS Dual พัดลม Axial-tech พร้อมใบพัดขนาดใหญ่และวงแหวนกั้นลม เพิ่มแรงดันลมลงบนฮีตซิงก์ เทคโนโลยี 0dB พัดลมหยุดหมุนเมื่ออุณหภูมิต่ำ', 1, 'available'),
(10, 2, 4, 'VGA(การ์ดจอ) MSI GEFORCE RTX 3050 VENTUS 2X XS 8G OC - 8GB GDDR6', 'GeForce RTX 3050 8GB', 'RTX 30 Series', 8, 'GDDR6', 128, 1552, 1807, 115, 9990.00, 6, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products180545_800.jpg', 'รุ่นอัปเกรด VRAM เต็มพิกัด 8GB GDDR6 บนบัส 128-bit รองรับเกมกินแรมและการสตรีมเกมระดับ Full HD ลื่นไหลไม่มีสะดุด', 0, 'available'),
(11, 6, 2, 'VGA(การ์ดจอ) POWERCOLOR REAPER RADEON RX 9050 - 8GB GDDR6 (RX9050 8G-A) (3Y)', 'Radeon RX 9050', 'RX 9000 Series', 8, 'GDDR6', 128, 2200, 2600, 130, 10990.00, 9, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products185004_800.jpg', 'สถาปัตยกรรมชิปเซ็ตรุ่นใหม่จาก AMD Radeon ให้พลังคำนวณกราฟิกระดับสูง รองรับ FSR 3.1 Frame Generation ภาพสวยคมชัด', 0, 'available'),
(12, 12, 2, 'VGA(การ์ดจอ) ASROCK CHALLENGER RADEON RX 9050 - 8GB GDDR6 (RX9050 CL 8G) (3Y)', 'Radeon RX 9050', 'RX 9000 Series', 8, 'GDDR6', 128, 2250, 2620, 130, 10990.00, 5, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products187242_800.jpg', 'ASRock Challenger ดีไซน์สองพัดลมระบายความร้อน Striped Axial Fan พัดลมตัดการทำงานเมื่อไม่โหลด พร้อมแผ่นแบ็คเพลตโลหะ', 0, 'available'),
(13, 1, 1, 'VGA(การ์ดจอ) ASUS DUAL GEFORCE RTX 5050 OC EDITION - 8GB GDDR6 (DUAL-RTX5050-O8G) (3Y)', 'GeForce RTX 5050', 'RTX 50 Series', 8, 'GDDR6', 128, 2310, 2610, 130, 15490.00, 12, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products122930_800.jpg', 'ก้าวสู่ยุคใหม่ของ RTX 50 ซีรีส์ ประสิทธิภาพ AI Tensor Core รุ่นใหม่ล่าสุด รองรับ DLSS 4 และ Ray Tracing เต็มรูปแบบ', 1, 'available'),
(14, 2, 1, 'VGA(การ์ดจอ) MSI GEFORCE RTX 5050 VENTUS 2X OC 8G - 8GB GDDR6 (3Y)', 'GeForce RTX 5050', 'RTX 50 Series', 8, 'GDDR6', 128, 2310, 2600, 130, 15490.00, 8, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products124836_800.jpg', 'MSI Ventus ซีรีส์ยอดฮิต เสถียร เย็น ทนทาน ออกแบบมาเพื่อเกมเมอร์ที่ต้องการความคุ้มค่าและเฟรมเรตสูงในงบประมาณที่เข้าถึงได้', 0, 'available'),
(15, 3, 2, 'VGA(การ์ดจอ) GIGABYTE RADEON RX 9060 XT GAMING OC - 8GB GDDR6 (GV-R9060XTGAMING OC-8GD) (3Y)', 'Radeon RX 9060 XT', 'RX 9000 Series', 8, 'GDDR6', 128, 2420, 2700, 175, 15990.00, 10, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products107992_800.jpg', 'GIGABYTE GAMING OC พัดลม 3 ตัว Triple Fan ระบายความร้อนทรงพลัง ไฟ RGB Fusion พร้อมสลับ Dual BIOS (OC / Silent)', 0, 'available'),
(16, 1, 2, 'VGA(การ์ดจอ) ASUS PRIME RADEON RX 9060 XT O8G - 8GB GDDR6 (PRIME-RX9060XT-O8G) (3Y)', 'Radeon RX 9060 XT', 'RX 9000 Series', 8, 'GDDR6', 128, 2420, 2715, 175, 15990.00, 6, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products108089_800.jpg', 'ASUS PRIME ซีรีส์ใหม่ที่ผสมผสานความทนทานระดับองค์กร เข้ากับสมรรถนะการเล่นเกม 1440p ระดับพรีเมียม', 0, 'available'),
(17, 3, 1, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 5060 WINDFORCE OC 8G - 8GB GDDR7 (GV-N5060WF2OC-8GD) (3Y)', 'GeForce RTX 5060', 'RTX 50 Series', 8, 'GDDR7', 128, 2350, 2610, 145, 17490.00, 14, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products104086_800.jpg', 'หน่วยความจำ Next-Gen GDDR7 แบนด์วิดท์มหาศาล ระบบระบายความร้อน WINDFORCE ปรับแต่งความเร็ว Boost Clock สปีดแรงขึ้นทันตา', 1, 'available'),
(18, 10, 1, 'VGA(การ์ดจอ) INNO3D GEFORCE RTX 5060 TWIN X2 - 8GB GDDR7 (N50602-08D7-195071N) (3Y)', 'GeForce RTX 5060', 'RTX 50 Series', 8, 'GDDR7', 128, 2350, 2595, 145, 17490.00, 11, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products104890_800.jpg', 'การ์ดจอมาตรฐาน GDDR7 คุ้มราคาสูงสุด ดีไซน์สองพัดลมขนาดใหญ่พร้อมฮีตไปป์ทองแดงแท้ สัมผัสตรงกับหน้าชิป GPU', 0, 'available'),
(19, 8, 1, 'VGA(การ์ดจอ) PALIT GEFORCE RTX 5060 INFINITY 2 OC - 8GB GDDR7 (NE75060V19P1-GB2063L) (3Y)', 'GeForce RTX 5060', 'RTX 50 Series', 8, 'GDDR7', 128, 2350, 2625, 145, 17490.00, 9, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products142973_800.jpg', 'Palit Infinity 2 OC ซีรีส์ใหม่ ไฟสะท้อน Infinity Mirror เสริมความโดดเด่นในเคสเกมมิ่งของคุณ', 0, 'available'),
(20, 1, 1, 'VGA(การ์ดจอ) ASUS DUAL GEFORCE RTX 5060 OC EDITION - 8GB GDDR7 (DUAL-RTX5060-O8G) (3Y)', 'GeForce RTX 5060', 'RTX 50 Series', 8, 'GDDR7', 128, 2350, 2640, 145, 18490.00, 13, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products103845_800.jpg', 'ASUS Dual GDDR7 ให้เฟรมเรต 1080p Ultra และ 1440p ที่เสถียรที่สุด ใช้วัสดุเกรดอุตสาหกรรม Auto-Extreme แม่นยำทุกรอยบัดกรี', 1, 'available'),
(21, 9, 1, 'VGA(การ์ดจอ) COLORFUL IGAME GEFORCE RTX 5060 ULTRA OC - 8GB GDDR7 (3Y)', 'GeForce RTX 5060', 'RTX 50 Series', 8, 'GDDR7', 128, 2350, 2650, 150, 18490.00, 8, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products180752_800.jpg', 'iGame Ultra ซีรีส์ลายกราฟฟิตี้โฮโลแกรมสุดล้ำ ปุ่ม One-Key Overclocking ด้านหลัง เพิ่มพลังความเร็วในคลิกเดียว', 0, 'available'),
(22, 1, 2, 'VGA(การ์ดจอ) ASUS DUAL RADEON RX 9060 XT - 16GB GDDR6 (DUAL-RX9060XT-16G) (3Y)', 'Radeon RX 9060 XT 16GB', 'RX 9000 Series', 16, 'GDDR6', 128, 2420, 2720, 190, 19990.00, 7, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products189701_800.jpg', 'หน่วยความจำ VRAM จุใจ 16GB ตอบโจทย์การเล่นเกม AAA พื้นผิว Texture คมชัดสูงระดับ 2K/4K พร้อมระบบพัดลม Dual Axial-tech', 1, 'available'),
(23, 1, 1, 'VGA(การ์ดจอ) ASUS DUAL GEFORCE RTX 5060 TI OC EDITION - 8GB GDDR7 (DUAL-RTX5060TI-O8G) (3Y)', 'GeForce RTX 5060 Ti', 'RTX 50 Series', 8, 'GDDR7', 128, 2400, 2685, 180, 20490.00, 10, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products131064_800.jpg', 'ประสิทธิภาพก้าวกระโดดด้วยชิป RTX 5060 Ti ผสานแรมความเร็วสูง GDDR7 ขับเคลื่อนเกมเฟรมเรตสูงระดับ 144Hz สบายๆ', 0, 'available'),
(24, 13, 2, 'VGA(การ์ดจอ) XFX SWIFT RADEON RX 9070 GRE TRIPLE - 12GB GDDR6 (RX-97GRE29B7) (3Y)', 'Radeon RX 9070 GRE', 'RX 9000 Series', 12, 'GDDR6', 192, 2150, 2550, 230, 23990.00, 4, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products180875_800.jpg', 'XFX Swift 3 พัดลม สวยงามดุดัน เมมโมรี่ 12GB บนบัส 192-bit ระบายความร้อนเงียบสนิท เสริมแผ่นแบ็คเพลตโลหะ ประกัน 3 ปีเต็ม', 1, 'available'),
(25, 3, 1, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 4070 SUPER WINDFORCE OC 12GB (3Y)', 'GeForce RTX 4070 SUPER', 'RTX 40 Series', 12, 'GDDR6X', 192, 1980, 2505, 220, 24500.00, 15, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products97816_800.jpg', 'การ์ดจอขวัญใจมหาชนระดับ 1440p Ultra Ray Tracing พัดลม 3 ตัว WINDFORCE คอปเปอร์เพลททองแดงแท้สัมผัสตรงชิป ประกัน 3 ปีเต็ม', 1, 'available'),
(26, 1, 1, 'VGA(การ์ดจอ) ASUS DUAL GEFORCE RTX 5070 - 12GB GDDR7 OC EDITION (DUAL-RTX5070-O12G) (3Y)', 'GeForce RTX 5070', 'RTX 50 Series', 12, 'GDDR7', 192, 2450, 2715, 250, 32490.00, 8, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products135288_800.jpg', 'พลังประมวลผลรุ่นเรือธงระดับ 2K/4K พร้อมหน่วยความจำ GDDR7 ยกระดับความเร็วในการประมวลผล AI และเกม Ray Tracing ระดับภาพยนตร์', 1, 'available'),
(27, 3, 1, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 5070 GAMING OC 12G - 12GB GDDR7 (GV-N5070GAMING OC-12GD) (3Y)', 'GeForce RTX 5070', 'RTX 50 Series', 12, 'GDDR7', 192, 2450, 2730, 250, 32990.00, 10, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products97670_800.jpg', 'พัดลม 3 ตัว Tri-Fan ใบพัดหมุนย้อนทิศทาง พร้อมช่องระบายความร้อน Screen Cooling ด้านหลังเคส เพื่อการไหลเวียนอากาศสมบูรณ์แบบ', 0, 'available'),
(28, 6, 2, 'VGA(การ์ดจอ) POWERCOLOR REAPER (FIGHTER) RADEON RX 9070 XT - 16GB GDDR6 (RX9070XT 16G-A) (3Y)', 'Radeon RX 9070 XT', 'RX 9000 Series', 16, 'GDDR6', 256, 2300, 2700, 280, 33990.00, 5, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products151859_800.jpg', 'ตัวท็อปสายแดง แรมขนาดใหญ่ 16GB บนบัสกว้าง 256-bit พร้อมลุยเกม 4K ทุกเกม ปรับสุด Ultra ทุกค่าตั้ง', 1, 'available'),
(29, 1, 1, 'VGA(การ์ดจอ) ASUS ROG STRIX GEFORCE RTX 5070 GAMING OC - 12GB GDDR7 (ROG-STRIX-RTX5070-O12G-GAMING) (3Y)', 'GeForce RTX 5070', 'RTX 50 Series', 12, 'GDDR7', 192, 2480, 2760, 260, 39990.00, 4, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products100169_800.jpg', 'ที่สุดแห่งซีรีส์ ROG Strix โครงสร้างโลหะ Die-cast แข็งแกร่ง ฮีตซิงก์ขนาดใหญ่ 3.15 Slot พร้อมไฟ Aura Sync RGB อลังการ', 1, 'available'),
(30, 3, 1, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 5070 TI GAMING OC 16G - 16GB GDDR7 (GV-N507TGAMING OC-16GD) (3Y)', 'GeForce RTX 5070 Ti', 'RTX 50 Series', 16, 'GDDR7', 256, 2450, 2720, 300, 47990.00, 6, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products97753_800.jpg', 'การ์ดจอประสิทธิภาพระดับสูง VRAM 16GB GDDR7 บัส 256-bit รับมือการเรนเดอร์ 3D งานตัดต่อวิดีโอ 8K และเกมระดับท็อป', 1, 'available'),
(31, 8, 1, 'VGA(การ์ดจอ) PALIT GEFORCE RTX 5080 GAMING PRO - 16GB GDDR7 (3Y)', 'GeForce RTX 5080', 'RTX 50 Series', 16, 'GDDR7', 256, 2400, 2750, 360, 57990.00, 3, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products78052_800.jpg', 'พลังขับเคลื่อนระดับ Heavyweight GamingPro ซีรีส์ พัดลม Gale Hunter ใบพัดพิเศษ นำพาความร้อนออกจากชิปเซ็ตอย่างรวดเร็ว', 1, 'available'),
(32, 3, 1, 'VGA(การ์ดจอ) GIGABYTE AORUS GEFORCE RTX 5080 MASTER - 16GB GDDR7 (3Y)', 'GeForce RTX 5080', 'RTX 50 Series', 16, 'GDDR7', 256, 2450, 2780, 380, 63990.00, 2, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products102688_800.jpg', 'AORUS Master ระดับตำนาน หน้าจอ LCD Edge View แสดงอุณหภูมิและภาพ GIF แบบเรียลไทม์ พร้อมไฟ RGB Halo สามวงพัดลม', 1, 'available'),
(33, 1, 1, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB GDDR6X', 'GeForce RTX 4090', 'RTX 40 Series', 24, 'GDDR6X', 384, 2235, 2640, 450, 78900.00, 3, 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products78126_800.jpg', 'ราชันย์แห่งการ์ดจอระดับโลก พลัง 24GB GDDR6X และชิป AD102 เต็มสูบ พร้อมกล่อง Retail Box และการ์ดจอระดับพรีเมียม Vapor Chamber สิทธิบัตรเฉพาะ ASUS สำหรับผู้ที่ต้องการความเป็นที่สุด', 1, 'available'),
(34, 7, 3, 'VGA(การ์ดจอ) SPARKLE INTEL ARC A750 TITAN OC - 8GB GDDR6 (3Y)', 'Arc A750', 'Arc Alchemist', 8, 'GDDR6', 256, 2050, 2200, 225, 8900.00, 6, 'assets/images/gpus/sparkle_arc_a750.jpg', 'การ์ดจอ 3 พัดลม TORN Cooling พร้อมกล่องแพ็คเกจระดับสตูดิโอ รองรับ DirectX 12 Ultimate พร้อมฮาร์ดแวร์ Ray Tracing และ AV1 Dual Encoders', 0, 'available'),
(35, 7, 3, 'VGA(การ์ดจอ) INTEL ARC B580 BATTLEMAGE LIMITED - 12GB GDDR6 (3Y)', 'Arc B580', 'Arc Battlemage', 12, 'GDDR6', 192, 2670, 2850, 190, 10900.00, 10, 'assets/images/gpus/intel_arc_b580.jpg', 'สถาปัตยกรรม Battlemage Xe2 รุ่นล่าสุด พร้อมกล่องและตัวการ์ดจอสตูดิโอแพ็คช็อต ประสิทธิภาพต่อวัตต์ดีเยี่ยม เอนจิน XMX AI อัปเกรดเพื่อการอัปสเกล XeSS 2', 1, 'available'),
(36, 7, 3, 'VGA(การ์ดจอ) INTEL ARC A770 LIMITED EDITION - 16GB GDDR6 (3Y)', 'Arc A770', 'Arc Alchemist', 16, 'GDDR6', 256, 2100, 2400, 225, 12900.00, 8, 'assets/images/gpus/intel_arc_a770.jpg', 'เรือธงค่ายฟ้า มาพร้อม VRAM 16GB GDDR6 และกล่อง Retail Box Limited Edition ดีไซน์สตูดิโอระดับพรีเมียมพร้อมไฟวงแหวน RGB', 1, 'available');

-- 5. Mock Orders for Reporting & Analytics
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `gpu_id`, `quantity`, `unit_price`, `total_price`, `order_date`, `status`) VALUES
(1, 'ORD-2026-001', 2, 25, 1, 24500.00, 24500.00, '2026-10-01 14:20:00', 'completed'),
(2, 'ORD-2026-002', 2, 6, 2, 7990.00, 15980.00, '2026-10-03 10:15:30', 'completed'),
(3, 'ORD-2026-003', 2, 1, 1, 5490.00, 5490.00, '2026-10-05 18:45:10', 'completed'),
(4, 'ORD-2026-004', 2, 28, 2, 33990.00, 67980.00, '2026-10-08 11:30:00', 'completed'),
(5, 'ORD-2026-005', 2, 2, 1, 6990.00, 6990.00, '2026-10-12 16:05:40', 'completed'),
(6, 'ORD-2026-006', 2, 35, 3, 10900.00, 32700.00, '2026-10-15 09:22:15', 'completed');

-- 6. Mock Inventory Logs
INSERT INTO `inventory_logs` (`id`, `log_type`, `gpu_id`, `model_name`, `user_id`, `username`, `old_stock`, `new_stock`, `stock_change`, `action_details`, `created_at`) VALUES
(1, 'restock', 25, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 4070 SUPER WINDFORCE OC 12GB', 1, 'admin', 5, 15, 10, 'เติมสต็อกสินค้าเพิ่ม +10 ชิ้น สำหรับโปรโมชั่นประจำสัปดาห์', '2026-10-16 21:30:00'),
(2, 'restock', 33, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB GDDR6X', 1, 'admin', 1, 3, 2, 'เติมสต็อกการ์ดจอเรือธง +2 ชิ้น จากผู้นำเข้า ASUS Thailand', '2026-10-15 14:15:00'),
(3, 'restock', 6, 'VGA(การ์ดจอ) MSI GEFORCE RTX 3050 VENTUS 2X E 6G OC - 6GB GDDR6', 1, 'admin', 4, 14, 10, 'เติมสต็อกสินค้า +10 ชิ้น รุ่นคุ้มค่าขายดี', '2026-10-14 18:00:00'),
(4, 'restock', 35, 'VGA(การ์ดจอ) INTEL ARC B580 BATTLEMAGE LIMITED - 12GB GDDR6', 1, 'admin', 5, 10, 5, 'เติมสต็อกชิปเซ็ต Battlemage รุ่นใหม่ล่าสุด +5 ชิ้น', '2026-10-13 11:20:00'),
(5, 'restock', 1, 'VGA(การ์ดจอ) POWERCOLOR FIGHTER RADEON RX 6500 XT - 4GB GDDR6 V3', 1, 'admin', 5, 15, 10, 'เติมสต็อกล็อตใหม่ +10 ชิ้น สำหรับเกมเมอร์สาย 1080p', '2026-10-11 09:45:00'),
(6, 'add', 35, 'VGA(การ์ดจอ) INTEL ARC B580 BATTLEMAGE LIMITED - 12GB GDDR6', 1, 'admin', 0, 10, 10, 'เพิ่มรายการการ์ดจอใหม่เข้าสู่ระบบ (Intel Battlemage Xe2, ราคา ฿10,900.00)', '2026-10-16 20:00:00'),
(7, 'edit', 33, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB GDDR6X', 1, 'admin', 3, 3, 0, 'ปรับปรุงข้อมูลราคาจำหน่ายพิเศษและอัปเดตสเปก TDP 450W', '2026-10-15 16:30:00'),
(8, 'add', 25, 'VGA(การ์ดจอ) GIGABYTE GEFORCE RTX 4070 SUPER WINDFORCE OC 12GB', 1, 'admin', 0, 15, 15, 'เพิ่มรายการการ์ดจอใหม่เข้าสู่ระบบ (NVIDIA Ada Lovelace, ราคา ฿24,500.00)', '2026-10-14 17:10:00'),
(9, 'edit', 28, 'VGA(การ์ดจอ) POWERCOLOR REAPER (FIGHTER) RADEON RX 9070 XT - 16GB GDDR6', 1, 'admin', 5, 5, 0, 'ปรับราคาโปรโมชั่นพิเศษ', '2026-10-12 10:00:00'),
(10, 'delete', 99, 'NVIDIA GeForce RTX 3060 Ti Twin 8GB GDDR6', 1, 'admin', 0, 0, 0, 'ลบสินค้าออกจากระบบเนื่องจากตกรุ่นและยกเลิกสายการผลิต', '2026-10-10 08:30:00');
