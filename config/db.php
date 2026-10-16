<?php
/**
 * Database Configuration & Connection (PDO)
 * Cyber-Tech GPU Store
 */

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
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
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
