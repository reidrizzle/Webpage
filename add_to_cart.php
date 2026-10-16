<?php
/**
 * Add To Cart Controller
 * Supports both AJAX requests and standard form submissions
 */
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json; charset=utf-8');

$gpuId = (int)($_POST['gpu_id'] ?? $_GET['gpu_id'] ?? 0);
$qty = max(1, (int)($_POST['quantity'] ?? $_GET['quantity'] ?? 1));
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['ajax']) || isset($_POST['ajax']);

if (!$gpuId) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบรหัสสินค้า']);
        exit;
    }
    header("Location: index.php");
    exit;
}

$pdo = getDBConnection();
if ($pdo) {
    // Check GPU existence and stock
    $stmt = $pdo->prepare("SELECT id, model_name, stock, price FROM gpus WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $gpuId]);
    $gpu = $stmt->fetch();

    if (!$gpu) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลการ์ดจอ']);
            exit;
        }
        header("Location: index.php?msg=not_found");
        exit;
    }

    if ($gpu['stock'] <= 0) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'สินค้าหมด ไม่สามารถเพิ่มลงตะกร้าได้']);
            exit;
        }
        header("Location: index.php?msg=out_of_stock");
        exit;
    }

    // Check existing in cart
    $currentInCart = $_SESSION['cart'][$gpuId] ?? 0;
    if ($currentInCart + $qty > $gpu['stock']) {
        if ($isAjax) {
            echo json_encode([
                'status' => 'error', 
                'message' => "สต็อกคงเหลือ {$gpu['stock']} ชิ้น ในตะกร้ามีอยู่แล้ว {$currentInCart} ชิ้น"
            ]);
            exit;
        }
        header("Location: cart.php?msg=max_stock_reached");
        exit;
    }

    // Add to cart
    addToCart($gpuId, $qty);
    $newCount = getCartCount();

    if ($isAjax) {
        echo json_encode([
            'status' => 'success',
            'message' => "เพิ่ม {$gpu['model_name']} ลงในตะกร้าเรียบร้อยแล้ว!",
            'cart_count' => $newCount
        ]);
        exit;
    }

    header("Location: cart.php?msg=added&item=" . urlencode($gpu['model_name']));
    exit;
}

if ($isAjax) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้']);
    exit;
}
header("Location: index.php");
exit;
