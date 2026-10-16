<?php
/**
 * My Orders Page (คำสั่งซื้อของฉัน)
 * Displays user's full GPU purchase history, order codes, receipt modals, and timestamps
 */
$pageTitle = "คำสั่งซื้อของฉัน (My Orders)";
require_once __DIR__ . '/config/db.php';

requireLogin();

$pdo = getDBConnection();
$userId = (int)$_SESSION['user_id'];
$orders = [];
$totalSpent = 0;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT o.*, g.model_name, g.image_url, g.vram_gb, g.memory_type, 
                                      c.manufacturer, c.badge_color, b.name AS brand_name 
                               FROM orders o
                               JOIN gpus g ON o.gpu_id = g.id
                               JOIN chipsets c ON g.chipset_id = c.id
                               JOIN brands b ON g.brand_id = b.id
                               WHERE o.user_id = :uid
                               ORDER BY o.order_date DESC");
        $stmt->execute([':uid' => $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $ord) {
            $totalSpent += (float)$ord['total_price'];
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

$userWallet = getUserWallet($userId);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb & Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span class="text-slate-400">MY ORDERS</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
        คำสั่งซื้อของฉัน (MY ORDERS)
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        ตรวจสอบประวัติการซื้อการ์ดจอ รหัสใบเสร็จดิจิทัล และสถานะการจัดส่ง
      </p>
    </div>

    <div>
      <a href="products.php" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2">
        <i class="fa-solid fa-cart-shopping"></i> สั่งซื้อการ์ดจอเพิ่ม
      </a>
    </div>
  </div>

  <!-- Summary Stats Bar -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="glass-panel-elevated p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-mono text-slate-400 uppercase block">คำสั่งซื้อทั้งหมด</span>
        <span class="text-2xl font-black font-scifi text-white"><?= count($orders) ?> <span class="text-xs text-slate-500 font-normal">รายการ</span></span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
        <i class="fa-solid fa-receipt text-lg"></i>
      </div>
    </div>

    <div class="glass-panel-elevated p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-mono text-slate-400 uppercase block">ยอดชำระจำลองสะสม</span>
        <span class="text-2xl font-black font-scifi text-emerald-400"><?= formatPrice($totalSpent) ?></span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
        <i class="fa-solid fa-coins text-lg"></i>
      </div>
    </div>

    <div class="glass-panel-elevated p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
      <div>
        <span class="text-[11px] font-mono text-slate-400 uppercase block">วอลเล็ตจำลองคงเหลือ</span>
        <span class="text-2xl font-black font-scifi text-amber-300"><?= formatPrice($userWallet) ?></span>
      </div>
      <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
        <i class="fa-solid fa-wallet text-lg"></i>
      </div>
    </div>
  </div>

  <!-- Orders List -->
  <?php if (!empty($orders)): ?>
    <div class="space-y-4">
      <?php foreach ($orders as $ord): ?>
        <div class="glass-panel-elevated p-5 sm:p-6 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
          
          <!-- Left: Thumbnail & Details -->
          <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-20 h-20 rounded-xl bg-slate-950 p-2 border border-slate-800 shrink-0 flex items-center justify-center">
              <img src="<?= e($ord['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
            </div>

            <div class="space-y-1 min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-cyan-950 border border-cyan-500/40 text-cyan-400">
                  <?= e($ord['order_code']) ?>
                </span>
                <span class="text-xs font-mono text-slate-400">
                  <i class="fa-regular fa-clock mr-1 text-slate-500"></i> <?= date('d/m/Y H:i', strtotime($ord['order_date'])) ?> น.
                </span>
                <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded font-bold" style="background: <?= e($ord['badge_color']) ?>20; color: <?= e($ord['badge_color']) ?>;">
                  <?= e($ord['manufacturer']) ?>
                </span>
              </div>

              <h3 class="font-bold text-white text-base truncate" title="<?= e($ord['model_name']) ?>">
                <?= e($ord['model_name']) ?>
              </h3>

              <div class="text-xs font-mono text-slate-400">
                แบรนด์: <span class="text-slate-300"><?= e($ord['brand_name']) ?></span> • 
                จำนวน: <span class="text-white font-bold"><?= $ord['quantity'] ?></span> ชิ้น • 
                ราคาต่อชิ้น: <span class="text-cyan-300"><?= formatPrice($ord['unit_price']) ?></span>
              </div>
            </div>
          </div>

          <!-- Right: Price, Status & Actions -->
          <div class="flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto pt-3 md:pt-0 border-t md:border-t-0 border-slate-800 shrink-0 gap-3">
            <div class="text-left md:text-right">
              <span class="text-[10px] font-mono text-slate-400 uppercase block">ยอดชำระสำเร็จ</span>
              <span class="text-xl sm:text-2xl font-black font-scifi text-emerald-400">
                <?= formatPrice($ord['total_price']) ?>
              </span>
            </div>

            <div class="flex items-center gap-2">
              <span class="text-xs font-mono text-emerald-400 bg-emerald-950/60 border border-emerald-500/40 px-2.5 py-1 rounded-lg flex items-center gap-1.5 font-bold">
                <i class="fa-solid fa-circle-check text-[10px]"></i> ชำระสำเร็จ
              </span>

              <button onclick='viewReceipt(<?= json_encode($ord, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                      class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-mono transition-colors">
                <i class="fa-solid fa-file-invoice mr-1"></i> ใบเสร็จ
              </button>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <!-- Empty Orders State -->
    <div class="glass-panel-elevated py-16 px-4 rounded-3xl text-center max-w-2xl mx-auto border border-slate-800">
      <div class="w-20 h-20 mx-auto rounded-3xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-600 mb-4 shadow-xl">
        <i class="fa-solid fa-receipt text-3xl"></i>
      </div>
      <h3 class="text-xl font-bold font-scifi text-white mb-1">
        ยังไม่มีประวัติการสั่งซื้อการ์ดจอ
      </h3>
      <p class="text-xs text-slate-400 font-mono mb-6 max-w-md mx-auto">
        คุณมีวอลเล็ตจำลอง ฿9,999,999 พร้อมให้คุณเลือกซื้อการ์ดจอเกมมิ่งรุ่นแฟล็กชิปได้ทันที
      </p>
      <a href="products.php" class="btn-cyber-primary px-5 py-2.5 rounded-xl text-xs font-bold inline-flex items-center gap-2">
        <i class="fa-solid fa-layer-group"></i> ไปเลือกซื้อการ์ดจอเลย ➔
      </a>
    </div>
  <?php endif; ?>

</div>

<!-- Receipt Modal Script -->
<script>
function viewReceipt(ord) {
  Swal.fire({
    title: '<span style="font-family: Orbitron; color: #00F0FF;">DIGITAL RECEIPT</span>',
    html: `
      <div style="text-align: left; font-family: Prompt, sans-serif; font-size: 13px; color: #cbd5e1; line-height: 1.8;">
        <div style="border-bottom: 1px dashed #334155; padding-bottom: 8px; margin-bottom: 10px;">
          <div><strong>รหัสคำสั่งซื้อ:</strong> <span style="color: #00F0FF; font-family: monospace;">${ord.order_code}</span></div>
          <div><strong>วัน-เวลาสั่งซื้อ:</strong> ${ord.order_date} น.</div>
          <div><strong>สถานะ:</strong> <span style="color: #10b981; font-weight: bold;">ชำระเงินสำเร็จ (Paid)</span></div>
        </div>
        <div style="margin-bottom: 10px;">
          <div style="font-weight: bold; color: #fff;">${ord.model_name}</div>
          <div>จำนวน: ${ord.quantity} ชิ้น | ราคาต่อชิ้น: ฿${Number(ord.unit_price).toLocaleString()}</div>
        </div>
        <div style="border-top: 1px dashed #334155; padding-top: 8px; font-size: 16px; font-weight: bold; color: #10b981; text-align: right;">
          ยอดรวมทั้งหมด: ฿${Number(ord.total_price).toLocaleString()}
        </div>
      </div>
    `,
    background: '#0f172a',
    color: '#f8fafc',
    confirmButtonText: 'ปิดหน้าต่าง',
    confirmButtonColor: '#00F0FF'
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
