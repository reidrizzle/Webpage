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
        // Fetch model info before delete to record in logs
        $stmtInfo = $pdo->prepare("SELECT model_name, stock FROM gpus WHERE id = :id LIMIT 1");
        $stmtInfo->execute([':id' => $gpuId]);
        $gpuInfo = $stmtInfo->fetch();

        if ($gpuInfo) {
            logInventoryAction('delete', $gpuId, $gpuInfo['model_name'], $gpuInfo['stock'], 0, -$gpuInfo['stock'], "ลบข้อมูลการ์ดจอออกจากระบบ (สต็อกเดิม {$gpuInfo['stock']} ชิ้น)");
        }

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
