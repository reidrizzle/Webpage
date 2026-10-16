<?php
/**
 * Database Configuration & Connection (PDO)
 * Cyber-Tech GPU Store
 */

// Set Default Timezone to Thailand (Asia/Bangkok, UTC+7)
date_default_timezone_set('Asia/Bangkok');

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection Parameters
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'gpu_store');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

// App Info
if (!defined('SITE_NAME')) define('SITE_NAME', 'CYBER-GPU STORE');
if (!defined('SITE_TAGLINE')) define('SITE_TAGLINE', 'Next-Gen Gaming & AI Compute Hardware');
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '฿');

/**
 * Get PDO Database Connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . ", time_zone = '+07:00'"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $pdo->exec("SET time_zone = '+07:00'");
        } catch (PDOException $e) {
            // In development / demo, we capture error cleanly
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }

    return $pdo;
}

/**
 * Format currency with Thai Baht symbol
 */
function formatPrice($price) {
    return '฿' . number_format($price, 2);
}

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if logged in user is admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login helper
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?msg=login_required");
        exit;
    }
}

/**
 * Require Admin role helper (Block regular members)
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?msg=admin_only");
        exit;
    }
}

/**
 * Get current user wallet balance from database
 */
function getUserWallet($userId) {
    $pdo = getDBConnection();
    if (!$pdo || !$userId) return 9999999.00;
    try {
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (float)$val : 9999999.00;
    } catch (PDOException $e) {
        return 9999999.00;
    }
}

/**
 * Get total quantity of items in shopping cart
 */
function getCartCount() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return 0;
    }
    return (int)array_sum($_SESSION['cart']);
}

/**
 * Add an item to cart helper
 */
function addToCart($gpuId, $qty = 1) {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $gpuId = (int)$gpuId;
    $qty = max(1, (int)$qty);
    if (isset($_SESSION['cart'][$gpuId])) {
        $_SESSION['cart'][$gpuId] += $qty;
    } else {
        $_SESSION['cart'][$gpuId] = $qty;
    }
}

/**
 * Ensure inventory_logs table exists and seed realistic logs if empty
 */
function ensureInventoryLogsTable($pdo) {
    if (!$pdo) return;
    static $initialized = false;
    if ($initialized) return;

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `inventory_logs` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Check if logs are empty, seed initial data
        $check = $pdo->query("SELECT COUNT(*) FROM inventory_logs")->fetchColumn();
        if ($check == 0) {
            $pdo->exec("INSERT INTO `inventory_logs` 
                (`log_type`, `gpu_id`, `model_name`, `user_id`, `username`, `old_stock`, `new_stock`, `stock_change`, `action_details`, `created_at`) 
                VALUES
                ('restock', 3, 'GIGABYTE GeForce RTX 4070 SUPER WINDFORCE OC 12GB', 1, 'admin', 5, 15, 10, 'เติมสต็อกสินค้าเพิ่ม +10 ชิ้น สำหรับโปรโมชั่นประจำสัปดาห์', '2026-10-16 21:30:00'),
                ('restock', 1, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB', 1, 'admin', 1, 4, 3, 'เติมสต็อกการ์ดจอเรือธง +3 ชิ้น จากผู้นำเข้า ASUS Thailand', '2026-10-15 14:15:00'),
                ('restock', 2, 'MSI GeForce RTX 4080 SUPER 16GB Gaming X Trio', 1, 'admin', 4, 8, 4, 'เติมสต็อกสินค้า +4 ชิ้น ตอบสนองความต้องการประกอบคอม 4K', '2026-10-14 18:00:00'),
                ('restock', 11, 'Intel Arc B580 Battlemage Limited 12GB GDDR6', 1, 'admin', 5, 10, 5, 'เติมสต็อกชิปเซ็ต Battlemage รุ่นใหม่ล่าสุด +5 ชิ้น', '2026-10-13 11:20:00'),
                ('restock', 6, 'PowerColor Hellhound AMD Radeon RX 7800 XT 16GB', 1, 'admin', 6, 12, 6, 'เติมสต็อกล็อตใหม่ +6 ชิ้น สำหรับเกมเมอร์สาย 2K', '2026-10-11 09:45:00'),
                ('add', 11, 'Intel Arc B580 Battlemage Limited 12GB GDDR6', 1, 'admin', 0, 10, 10, 'เพิ่มรายการการ์ดจอใหม่เข้าสู่ระบบ (Intel Battlemage Xe2, ราคา ฿10,900.00)', '2026-10-16 20:00:00'),
                ('edit', 1, 'ASUS ROG Strix GeForce RTX 4090 OC 24GB', 1, 'admin', 4, 4, 0, 'ปรับปรุงข้อมูลราคาจำหน่ายพิเศษและอัปเดตสเปก TDP 450W', '2026-10-15 16:30:00'),
                ('add', 3, 'GIGABYTE GeForce RTX 4070 SUPER WINDFORCE OC 12GB', 1, 'admin', 0, 15, 15, 'เพิ่มรายการการ์ดจอใหม่เข้าสู่ระบบ (NVIDIA Ada Lovelace, ราคา ฿24,500.00)', '2026-10-14 17:10:00'),
                ('edit', 12, 'MSI Radeon RX 7600 XT GAMING X 16GB', 1, 'admin', 0, 0, 0, 'ปรับสถานะเป็นสินค้าหมดชั่วคราว (Out of Stock)', '2026-10-12 10:00:00'),
                ('delete', 99, 'NVIDIA GeForce RTX 3060 Ti Twin 8GB GDDR6', 1, 'admin', 0, 0, 0, 'ลบสินค้าออกจากระบบเนื่องจากตกรุ่นและยกเลิกสายการผลิต', '2026-10-10 08:30:00')
            ");
        }
        $initialized = true;
    } catch (PDOException $e) {
        error_log("Inventory log init error: " . $e->getMessage());
    }
}

/**
 * Log Inventory Action Helper
 */
function logInventoryAction($type, $gpuId, $modelName, $oldStock = null, $newStock = null, $stockChange = null, $details = null) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    ensureInventoryLogsTable($pdo);

    try {
        $userId = $_SESSION['user_id'] ?? 1;
        $username = $_SESSION['username'] ?? 'admin';
        $now = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("INSERT INTO inventory_logs 
            (log_type, gpu_id, model_name, user_id, username, old_stock, new_stock, stock_change, action_details, created_at) 
            VALUES (:type, :gid, :mname, :uid, :uname, :old_s, :new_s, :s_change, :details, :created_at)");
        
        $stmt->execute([
            ':type'       => $type,
            ':gid'        => $gpuId,
            ':mname'      => $modelName,
            ':uid'        => $userId,
            ':uname'      => $username,
            ':old_s'      => $oldStock,
            ':new_s'      => $newStock,
            ':s_change'   => $stockChange,
            ':details'    => $details,
            ':created_at' => $now
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Failed to insert inventory log: " . $e->getMessage());
        return false;
    }
}

/**
 * Fetch Restock Logs
 */
function getRestockLogs($limit = 50) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    ensureInventoryLogsTable($pdo);
    try {
        $stmt = $pdo->prepare("SELECT l.*, g.image_url 
                               FROM inventory_logs l
                               LEFT JOIN gpus g ON l.gpu_id = g.id
                               WHERE l.log_type = 'restock'
                               ORDER BY l.created_at DESC 
                               LIMIT :lim");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Fetch Product Add/Edit/Delete Catalog Logs
 */
function getCatalogLogs($limit = 50) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    ensureInventoryLogsTable($pdo);
    try {
        $stmt = $pdo->prepare("SELECT l.*, g.image_url 
                               FROM inventory_logs l
                               LEFT JOIN gpus g ON l.gpu_id = g.id
                               WHERE l.log_type IN ('add', 'edit', 'delete')
                               ORDER BY l.created_at DESC 
                               LIMIT :lim");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

