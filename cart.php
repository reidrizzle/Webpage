<?php
/**
 * Shopping Cart View & Checkout Page (ตะกร้าสินค้า)
 * Cyber-Tech GPU Store
 */
$pageTitle = "ตะกร้าสินค้า (Shopping Cart)";
require_once __DIR__ . '/config/db.php';

$pdo = getDBConnection();
$successMsg = null;
$errorMsg = null;

// Initialize cart session
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. Handle Cart Actions (update qty, remove item, clear cart)
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $gpuId = (int)($_POST['gpu_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 1);
    if ($gpuId && $qty > 0) {
        // Verify stock limit
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT stock FROM gpus WHERE id = :id");
            $stmt->execute([':id' => $gpuId]);
            $stock = (int)$stmt->fetchColumn();
            $_SESSION['cart'][$gpuId] = min($qty, $stock);
        } else {
            $_SESSION['cart'][$gpuId] = $qty;
        }
    } elseif ($gpuId && $qty <= 0) {
        unset($_SESSION['cart'][$gpuId]);
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'remove') {
    $gpuId = (int)($_GET['id'] ?? 0);
    if ($gpuId && isset($_SESSION['cart'][$gpuId])) {
        unset($_SESSION['cart'][$gpuId]);
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
    unset($_SESSION['applied_coupon']);
    header("Location: cart.php");
    exit;
}

// 2. Handle Coupon Code
if (isset($_POST['apply_coupon'])) {
    $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
    if ($code === 'CYBER2026') {
        $_SESSION['applied_coupon'] = ['code' => 'CYBER2026', 'discount' => 2000, 'name' => 'Cyber Neon Launch (-฿2,000)'];
        $successMsg = "ใช้งานคูปอง CYBER2026 ลดทันที ฿2,000 เรียบร้อยแล้ว!";
    } elseif ($code === 'RADEONVIBE') {
        $_SESSION['applied_coupon'] = ['code' => 'RADEONVIBE', 'discount' => 1500, 'name' => 'Radeon Cyber Squad (-฿1,500)'];
        $successMsg = "ใช้งานคูปอง RADEONVIBE ลดทันที ฿1,500 เรียบร้อยแล้ว!";
    } else {
        $errorMsg = "ไม่พบโค้ดส่วนลดนี้ หรือหมดอายุแล้ว";
    }
}

if ($action === 'remove_coupon') {
    unset($_SESSION['applied_coupon']);
    header("Location: cart.php");
    exit;
}

// 3. Handle Full Cart Checkout
if (isset($_POST['checkout'])) {
    requireLogin();
    $userId = (int)$_SESSION['user_id'];

    if (isAdmin()) {
        $errorMsg = "บัญชีผู้ดูแลระบบ (Admin) ไม่สามารถสั่งซื้อสินค้าได้";
    } elseif (empty($_SESSION['cart'])) {
        $errorMsg = "ตะกร้าสินค้าว่างเปล่า กรุณาเลือกสินค้าก่อนทำการสั่งซื้อ";
    } elseif ($pdo) {
        try {
            // Fetch items from cart
            $cartIds = array_keys($_SESSION['cart']);
            $inClause = implode(',', array_fill(0, count($cartIds), '?'));
            $stmt = $pdo->prepare("SELECT * FROM gpus WHERE id IN ($inClause)");
            $stmt->execute($cartIds);
            $cartGpus = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate Subtotal & verify stock
            $subtotal = 0;
            $itemsToBuy = [];
            foreach ($cartGpus as $gpu) {
                $qty = (int)$_SESSION['cart'][$gpu['id']];
                if ($gpu['stock'] < $qty) {
                    throw new Exception("สินค้า {$gpu['model_name']} สต็อกไม่เพียงพอ (เหลือ {$gpu['stock']} ชิ้น)");
                }
                $subtotal += ($gpu['price'] * $qty);
                $itemsToBuy[] = ['gpu' => $gpu, 'qty' => $qty, 'price' => $gpu['price']];
            }

            // Apply coupon discount
            $discount = $_SESSION['applied_coupon']['discount'] ?? 0;
            $grandTotal = max(0, $subtotal - $discount);

            // Check wallet balance
            $wallet = getUserWallet($userId);
            if ($wallet < $grandTotal) {
                throw new Exception("ยอดเงินในกระเป๋าจำลองไม่เพียงพอ (คุณมี " . formatPrice($wallet) . " ต้องการ " . formatPrice($grandTotal) . ")");
            }

            // Start Transaction
            $pdo->beginTransaction();

            // 1. Deduct wallet
            $stmtWallet = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - :total WHERE id = :uid");
            $stmtWallet->execute([':total' => $grandTotal, ':uid' => $userId]);

            // 2. Insert Orders and update stock
            $nowBangkok = date('Y-m-d H:i:s');
            foreach ($itemsToBuy as $item) {
                $gpu = $item['gpu'];
                $qty = $item['qty'];
                $price = $item['price'];
                $totalItemPrice = $price * $qty;
                $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

                // Deduct stock
                $stmtStock = $pdo->prepare("UPDATE gpus SET 
                                            stock = stock - :qty,
                                            status = CASE 
                                                        WHEN stock - :qty <= 0 THEN 'out_of_stock'
                                                        WHEN stock - :qty <= 3 THEN 'low_stock'
                                                        ELSE 'available'
                                                     END
                                            WHERE id = :id");
                $stmtStock->execute([':qty' => $qty, ':id' => $gpu['id']]);

                // Create Order Record
                $stmtOrder = $pdo->prepare("INSERT INTO orders (order_code, user_id, gpu_id, quantity, unit_price, total_price, status, order_date) 
                                           VALUES (:code, :uid, :gid, :qty, :uprice, :tprice, 'completed', :odate)");
                $stmtOrder->execute([
                    ':code'   => $orderCode,
                    ':uid'    => $userId,
                    ':gid'    => $gpu['id'],
                    ':qty'    => $qty,
                    ':uprice' => $price,
                    ':tprice' => $totalItemPrice,
                    ':odate'  => $nowBangkok
                ]);
            }

            $pdo->commit();

            // Update session wallet
            $_SESSION['wallet_balance'] = $wallet - $grandTotal;

            // Clear Cart
            $_SESSION['cart'] = [];
            unset($_SESSION['applied_coupon']);

            header("Location: orders.php?msg=checkout_success");
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errorMsg = $e->getMessage();
        }
    }
}

// 4. Fetch Cart Products Details
$cartProducts = [];
$subtotal = 0;
if (!empty($_SESSION['cart']) && $pdo) {
    try {
        $cartIds = array_keys($_SESSION['cart']);
        $inClause = implode(',', array_fill(0, count($cartIds), '?'));
        $stmt = $pdo->prepare("SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.badge_color 
                               FROM gpus g
                               JOIN brands b ON g.brand_id = b.id
                               JOIN chipsets c ON g.chipset_id = c.id
                               WHERE g.id IN ($inClause)");
        $stmt->execute($cartIds);
        $cartProducts = $stmt->fetchAll();

        foreach ($cartProducts as $p) {
            $qty = $_SESSION['cart'][$p['id']] ?? 1;
            $subtotal += ($p['price'] * $qty);
        }
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}

$discount = $_SESSION['applied_coupon']['discount'] ?? 0;
$grandTotal = max(0, $subtotal - $discount);
$userWallet = isLoggedIn() ? getUserWallet($_SESSION['user_id']) : 9999999.00;

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb -->
  <div class="flex items-center justify-between mb-8">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span class="text-slate-400">SHOPPING CART</span>
      </div>
      <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
        ตะกร้าสินค้า (SHOPPING CART)
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        ตรวจสอบรายการการ์ดจอที่เลือก ปรับจำนวนสินค้า และชำระเงินด้วยวอลเล็ตจำลอง
      </p>
    </div>

    <div>
      <a href="products.php" class="px-3.5 py-2 rounded-xl text-xs font-scifi font-bold bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-cyan-400 transition-colors flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-left"></i> เลือกซื้อสินค้าต่อ
      </a>
    </div>
  </div>

  <?php if ($successMsg): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-sm flex items-center gap-3">
      <i class="fa-solid fa-circle-check text-xl text-emerald-400 shrink-0"></i>
      <span><?= e($successMsg) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($errorMsg): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-sm flex items-center gap-3">
      <i class="fa-solid fa-triangle-exclamation text-xl text-rose-400 shrink-0"></i>
      <span><?= e($errorMsg) ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($cartProducts)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- Cart Items List (8 cols) -->
      <div class="lg:col-span-8 space-y-4">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 text-xs font-mono text-slate-400">
          <span>รายการสินค้าในตะกร้า (<?= count($cartProducts) ?> ชนิด, <?= getCartCount() ?> ชิ้น)</span>
          <a href="cart.php?action=clear" onclick="return confirm('ต้องการล้างตะกร้าสินค้าทั้งหมดหรือไม่?')" class="text-rose-400 hover:underline">
            <i class="fa-solid fa-trash-can mr-1"></i> ล้างตะกร้าทั้งหมด
          </a>
        </div>

        <?php foreach ($cartProducts as $gpu): 
          $qty = (int)($_SESSION['cart'][$gpu['id']] ?? 1);
          $itemTotal = $gpu['price'] * $qty;
        ?>
          <div class="glass-panel-elevated p-4 sm:p-5 rounded-2xl border border-slate-800 hover:border-slate-700 transition-all flex flex-col sm:flex-row items-center gap-4">
            
            <!-- Thumbnail -->
            <div class="w-20 h-20 rounded-xl bg-slate-950 p-2 border border-slate-800 shrink-0 flex items-center justify-center">
              <img src="<?= e($gpu['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
            </div>

            <!-- Details -->
            <div class="flex-1 min-w-0 text-center sm:text-left">
              <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 mb-1">
                <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded font-bold" style="background: <?= e($gpu['badge_color']) ?>20; color: <?= e($gpu['badge_color']) ?>; border: 1px solid <?= e($gpu['badge_color']) ?>40;">
                  <?= e($gpu['manufacturer']) ?>
                </span>
                <span class="text-[10px] font-mono text-slate-400">
                  <?= e($gpu['brand_name']) ?> • <?= e($gpu['vram_gb']) ?>GB <?= e($gpu['memory_type']) ?>
                </span>
              </div>
              <h3 class="font-bold text-white text-sm truncate" title="<?= e($gpu['model_name']) ?>">
                <?= e($gpu['model_name']) ?>
              </h3>
              <div class="text-xs font-mono text-slate-400 mt-0.5">
                ราคาต่อชิ้น: <span class="text-cyan-300 font-bold"><?= formatPrice($gpu['price']) ?></span>
              </div>
            </div>

            <!-- Quantity Controls -->
            <form method="POST" action="cart.php?action=update" class="flex items-center border border-slate-700 bg-slate-950 rounded-xl overflow-hidden shrink-0">
              <input type="hidden" name="gpu_id" value="<?= $gpu['id'] ?>">
              <button type="submit" name="quantity" value="<?= max(0, $qty - 1) ?>" 
                      class="px-3 py-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-minus text-xs"></i>
              </button>
              <span class="px-3 py-1.5 font-scifi font-bold text-white text-xs w-10 text-center">
                <?= $qty ?>
              </span>
              <button type="submit" name="quantity" value="<?= min($gpu['stock'], $qty + 1) ?>" 
                      class="px-3 py-1.5 text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                      <?= $qty >= $gpu['stock'] ? 'disabled title="สต็อกไม่เพียงพอ"' : '' ?>>
                <i class="fa-solid fa-plus text-xs"></i>
              </button>
            </form>

            <!-- Item Total Price & Delete Button -->
            <div class="text-center sm:text-right shrink-0 min-w-[120px]">
              <div class="font-scifi font-bold text-emerald-400 text-sm sm:text-base">
                <?= formatPrice($itemTotal) ?>
              </div>
              <a href="cart.php?action=remove&id=<?= $gpu['id'] ?>" 
                 onclick="return confirm('ลบสินค้านี้ออกจากตะกร้า?')"
                 class="text-xs text-rose-400 hover:text-rose-300 transition-colors mt-1 inline-block">
                <i class="fa-solid fa-trash-can text-xs mr-1"></i> ลบ
              </a>
            </div>

          </div>
        <?php endforeach; ?>

      </div>

      <!-- Checkout Summary Box (4 cols) -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="glass-panel-elevated p-6 rounded-3xl border border-slate-700/80 shadow-2xl">
          <h3 class="font-scifi font-bold text-base text-white uppercase tracking-wider mb-4 pb-3 border-b border-slate-800">
            สรุปคำสั่งซื้อ (Order Summary)
          </h3>

          <!-- Coupon Input Form -->
          <form method="POST" action="cart.php" class="mb-5">
            <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-2">
              <i class="fa-solid fa-ticket text-cyan-400 mr-1"></i> โค้ดส่วนลดโปรโมชั่น (Coupon)
            </label>
            <div class="flex gap-2">
              <input type="text" name="coupon_code" placeholder="เช่น CYBER2026" 
                     value="<?= e($_SESSION['applied_coupon']['code'] ?? '') ?>"
                     class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono uppercase focus:outline-none focus:border-cyan-400">
              <button type="submit" name="apply_coupon" class="px-4 py-2.5 rounded-xl text-xs font-bold btn-cyber-primary">
                ใช้โค้ด
              </button>
            </div>
            <div class="text-[10px] text-slate-500 font-mono mt-1">
              โค้ดแนะนำ: <span class="text-cyan-400 font-bold cursor-pointer" onclick="document.querySelector('[name=coupon_code]').value='CYBER2026'">CYBER2026</span> (-฿2,000)
            </div>
          </form>

          <!-- Pricing Calculations -->
          <div class="space-y-3 pt-2 border-t border-slate-800/80 text-xs font-mono">
            <div class="flex items-center justify-between text-slate-400">
              <span>ราคารวมสินค้า (Subtotal):</span>
              <span class="text-white font-bold"><?= formatPrice($subtotal) ?></span>
            </div>

            <?php if ($discount > 0): ?>
              <div class="flex items-center justify-between text-emerald-400">
                <span>ส่วนลด (<?= e($_SESSION['applied_coupon']['code']) ?>):</span>
                <span>-<?= formatPrice($discount) ?> <a href="cart.php?action=remove_coupon" class="text-rose-400 text-[10px] ml-1">[ยกเลิก]</a></span>
              </div>
            <?php endif; ?>

            <div class="flex items-center justify-between text-slate-400">
              <span>ค่าจัดส่งด่วน (Express Dispatch):</span>
              <span class="text-emerald-400 font-bold">ฟรี (FREE)</span>
            </div>

            <div class="pt-3 border-t border-slate-700/80 flex items-center justify-between">
              <span class="text-sm font-scifi font-bold text-white uppercase">ยอดชำระสุทธิ:</span>
              <span class="text-2xl font-black font-scifi text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400">
                <?= formatPrice($grandTotal) ?>
              </span>
            </div>
          </div>

          <!-- Simulated Wallet Status -->
          <div class="mt-5 p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 text-xs font-mono">
            <div class="flex items-center justify-between text-slate-400 mb-1">
              <span>วอลเล็ตจำลองของคุณ:</span>
              <span class="text-amber-400 font-bold"><?= formatPrice($userWallet) ?></span>
            </div>
            <?php if (isLoggedIn()): ?>
              <?php if ($userWallet >= $grandTotal): ?>
                <div class="text-[11px] text-emerald-400 flex items-center gap-1 mt-1">
                  <i class="fa-solid fa-circle-check"></i> ยอดเงินเพียงพอสำหรับการสั่งซื้อ
                </div>
              <?php else: ?>
                <div class="text-[11px] text-rose-400 flex items-center gap-1 mt-1">
                  <i class="fa-solid fa-triangle-exclamation"></i> ยอดเงินไม่พอ (<a href="profile.php" class="underline">ไปเติมเงิน ฿9.9M</a>)
                </div>
              <?php endif; ?>
            <?php else: ?>
              <div class="text-[11px] text-cyan-400 mt-1">
                กรุณาเข้าสู่ระบบก่อนชำระเงิน (รับฟรี ฿9,999,999)
              </div>
            <?php endif; ?>
          </div>

          <!-- Checkout Button -->
          <form method="POST" action="cart.php" class="mt-5">
            <?php if (isLoggedIn()): ?>
              <?php if (!isAdmin()): ?>
                <button type="submit" name="checkout" 
                        onclick="return confirm('ยืนยันชำระเงินจำลอง <?= formatPrice($grandTotal) ?> เพื่อสั่งซื้อสินค้าในตะกร้า?')"
                        class="w-full btn-cyber-primary py-3.5 rounded-xl text-sm font-scifi font-bold flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/20">
                  <i class="fa-solid fa-credit-card"></i> ชำระเงินด้วยวอลเล็ตจำลอง
                </button>
              <?php else: ?>
                <button disabled class="w-full py-3.5 rounded-xl bg-slate-800 text-slate-500 text-xs font-mono cursor-not-allowed">
                  แอดมินไม่สามารถสั่งซื้อสินค้าได้
                </button>
              <?php endif; ?>
            <?php else: ?>
              <a href="login.php" class="w-full btn-cyber-primary py-3.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 text-center block">
                <i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบเพื่อชำระเงิน
              </a>
            <?php endif; ?>
          </form>

        </div>

      </div>

    </div>

  <?php else: ?>
    <!-- Empty Cart State -->
    <div class="glass-panel-elevated py-16 px-4 rounded-3xl text-center max-w-2xl mx-auto border border-slate-800">
      <div class="w-20 h-20 mx-auto rounded-3xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-600 mb-4 shadow-xl">
        <i class="fa-solid fa-cart-shopping text-3xl"></i>
      </div>
      <h3 class="text-xl font-bold font-scifi text-white mb-1">
        ตะกร้าสินค้าของคุณยังว่างเปล่า
      </h3>
      <p class="text-xs text-slate-400 font-mono mb-6 max-w-md mx-auto">
        คุณยังไม่ได้เลือกการ์ดจอลงในตะกร้า เริ่มค้นหาขุมพลังประมวลผลกราฟิกรุ่นท็อปได้เลยตอนนี้
      </p>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="products.php" class="btn-cyber-primary px-5 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2">
          <i class="fa-solid fa-layer-group"></i> ดูสินค้าการ์ดจอทั้งหมด ➔
        </a>
        <a href="promotions.php" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-slate-900 border border-slate-700 text-slate-300 hover:text-white inline-flex items-center gap-2">
          <i class="fa-solid fa-tags text-rose-400"></i> ดูโปรโมชั่นล่าสุด
        </a>
      </div>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
