<?php
/**
 * Instant GPU Buy Controller
 * Deducts user simulated wallet (9,999,999 THB), updates stock, and records order
 */
require_once __DIR__ . '/config/db.php';

requireLogin();

$pdo = getDBConnection();
$gpuId = (int)($_POST['gpu_id'] ?? $_GET['gpu_id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if (!$gpuId || !$pdo) {
    header("Location: index.php");
    exit;
}

try {
    // 1. Fetch GPU and lock/check stock
    $stmtGpu = $pdo->prepare("SELECT * FROM gpus WHERE id = :id LIMIT 1");
    $stmtGpu->execute([':id' => $gpuId]);
    $gpu = $stmtGpu->fetch();

    if (!$gpu) {
        header("Location: index.php?msg=gpu_not_found");
        exit;
    }

    if ($gpu['stock'] <= 0) {
        header("Location: index.php?msg=out_of_stock");
        exit;
    }

    $price = (float)$gpu['price'];

    // 2. Fetch current wallet balance
    $currentWallet = getUserWallet($userId);

    if ($currentWallet < $price) {
        header("Location: index.php?msg=insufficient_funds");
        exit;
    }

    // 3. Start Database Transaction
    $pdo->beginTransaction();

    // 3.1 Deduct wallet balance
    $stmtWallet = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - :price WHERE id = :id");
    $stmtWallet->execute([':price' => $price, ':id' => $userId]);

    // 3.2 Deduct GPU stock & update status
    $stmtStock = $pdo->prepare("UPDATE gpus SET 
                                stock = stock - 1,
                                status = CASE 
                                            WHEN stock - 1 <= 0 THEN 'out_of_stock'
                                            WHEN stock - 1 <= 3 THEN 'low_stock'
                                            ELSE 'available'
                                         END
                                WHERE id = :id");
    $stmtStock->execute([':id' => $gpuId]);

    // 3.3 Create Order Record
    $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    $stmtOrder = $pdo->prepare("INSERT INTO orders (order_code, user_id, gpu_id, quantity, unit_price, total_price, status) 
                               VALUES (:code, :uid, :gid, 1, :uprice, :tprice, 'completed')");
    $stmtOrder->execute([
        ':code'   => $orderCode,
        ':uid'    => $userId,
        ':gid'    => $gpuId,
        ':uprice' => $price,
        ':tprice' => $price
    ]);

    $pdo->commit();

    // Update Session
    $_SESSION['wallet_balance'] = $currentWallet - $price;

    $itemName = urlencode($gpu['model_name']);
    header("Location: index.php?msg=buy_success&item={$itemName}&order={$orderCode}&cost={$price}");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: index.php?msg=buy_error&err=" . urlencode($e->getMessage()));
    exit;
}
