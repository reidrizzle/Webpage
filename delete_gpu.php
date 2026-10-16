<?php
/**
 * Delete GPU Hardware Endpoint
 * Secure PDO Prepared Statement Deletion
 */
require_once __DIR__ . '/config/db.php';

// Only Admin can delete GPU
requireAdmin();

$pdo = getDBConnection();
$gpuId = (int)($_GET['id'] ?? 0);
$confirm = isset($_GET['confirm']) && $_GET['confirm'] == '1';

if ($gpuId && $confirm && $pdo) {
    try {
        $stmt = $pdo->prepare("DELETE FROM gpus WHERE id = :id");
        $stmt->execute([':id' => $gpuId]);

        header("Location: manage.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        die("เกิดข้อผิดพลาดในการลบข้อมูล: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: manage.php");
    exit;
}
