<?php
/**
 * Promotions & Discounts Page (โปรโมชั่น)
 * Flash Sale Deals, Discount Coupon Codes, and Special GPU Bundles
 */
$pageTitle = "โปรโมชั่นพิเศษ (Cyber Promotions)";
require_once __DIR__ . '/config/db.php';

$pdo = getDBConnection();
$promoGpus = [];

if ($pdo) {
    try {
        // Fetch featured or flagship GPUs for promotions
        $stmt = $pdo->prepare("SELECT g.*, b.name AS brand_name, c.manufacturer, c.badge_color 
                               FROM gpus g
                               JOIN brands b ON g.brand_id = b.id
                               JOIN chipsets c ON g.chipset_id = c.id
                               ORDER BY g.is_featured DESC, g.price DESC
                               LIMIT 6");
        $stmt->execute();
        $promoGpus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // silent
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb -->
  <div class="mb-8">
    <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
      <a href="index.php" class="hover:underline">HOME</a>
      <span>/</span>
      <span class="text-slate-400">PROMOTIONS & DEALS</span>
    </div>
    <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
      CYBER PROMOTIONS & FLASH DEALS
    </h1>
    <p class="text-xs sm:text-sm text-slate-400 mt-1">
      ดีลการ์ดจอราคาพิเศษ โค้ดส่วนลดวอลเล็ตจำลอง และสิทธิพิเศษสำหรับสมาชิกระบบ CyberGPU
    </p>
  </div>

  <!-- Big Hero Banner -->
  <div class="glass-panel-elevated p-8 sm:p-10 rounded-3xl border border-rose-500/40 bg-gradient-to-r from-rose-950/40 via-slate-950 to-indigo-950/50 relative overflow-hidden mb-12 shadow-2xl">
    <div class="absolute -right-12 -top-12 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="relative z-10 max-w-2xl">
      <span class="px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 inline-flex items-center gap-1.5 mb-3">
        <i class="fa-solid fa-fire text-rose-400 animate-bounce"></i> MID-SEASON FLASH SALE 2026
      </span>
      <h2 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
        อัปเกรดขุมพลังกราฟิก <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 via-amber-300 to-cyan-400">รับส่วนลดสูงสุด ฿2,000</span>
      </h2>
      <p class="text-xs sm:text-sm text-slate-300 font-thai mt-3 leading-relaxed">
        ใช้โค้ดส่วนลดโปรโมชั่นในหน้าตะกร้าสินค้าเพื่อรับส่วนลดทันที สมาชิกใหม่รับวงเงินจำลองเต็มจำนวน ฿9,999,999 ทดสอบการสั่งซื้อได้ไม่อั้น
      </p>
    </div>
  </div>

  <!-- Coupon Codes Section -->
  <div class="mb-12">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-scifi font-bold text-lg text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-ticket text-cyan-400"></i> โค้ดส่วนลดที่ใช้งานได้ตอนนี้ (Active Coupons)
      </h3>
      <span class="text-xs font-mono text-slate-400">คลิกที่โค้ดเพื่อคัดลอก</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      
      <!-- Coupon 1 -->
      <div class="glass-panel-elevated p-6 rounded-2xl border border-cyan-500/40 bg-gradient-to-br from-slate-900 via-slate-950 to-cyan-950/20 relative group hover:border-cyan-400 transition-all">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-mono font-bold text-cyan-400 uppercase tracking-wider">ALL GPUS</span>
          <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400">ใช้ได้ทุกรุ่น</span>
        </div>
        <div class="text-2xl font-black font-scifi text-white mb-1">
          ลดทันที ฿2,000
        </div>
        <p class="text-xs text-slate-400 mb-4">สำหรับคำสั่งซื้อการ์ดจอทุกรุ่นในระบบ</p>
        
        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
          <span class="font-mono font-black text-cyan-300 tracking-widest text-sm">CYBER2026</span>
          <button onclick="copyCoupon('CYBER2026')" class="btn-cyber-primary px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
            <i class="fa-regular fa-copy"></i> คัดลอก
          </button>
        </div>
      </div>

      <!-- Coupon 2 -->
      <div class="glass-panel-elevated p-6 rounded-2xl border border-rose-500/40 bg-gradient-to-br from-slate-900 via-slate-950 to-rose-950/20 relative group hover:border-rose-400 transition-all">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-mono font-bold text-rose-400 uppercase tracking-wider">RADEON SQUAD</span>
          <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-rose-500/20 text-rose-300">AMD RDNA 3</span>
        </div>
        <div class="text-2xl font-black font-scifi text-white mb-1">
          ลดทันที ฿1,500
        </div>
        <p class="text-xs text-slate-400 mb-4">ส่วนลดพิเศษสำหรับการ์ดจอค่ายแดง AMD</p>
        
        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
          <span class="font-mono font-black text-rose-300 tracking-widest text-sm">RADEONVIBE</span>
          <button onclick="copyCoupon('RADEONVIBE')" class="btn-cyber-primary px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
            <i class="fa-regular fa-copy"></i> คัดลอก
          </button>
        </div>
      </div>

      <!-- Coupon 3 -->
      <div class="glass-panel-elevated p-6 rounded-2xl border border-amber-500/40 bg-gradient-to-br from-slate-900 via-slate-950 to-amber-950/20 relative group hover:border-amber-400 transition-all">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-mono font-bold text-amber-400 uppercase tracking-wider">VIP PILOT</span>
          <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-500/20 text-amber-300">สิทธิพิเศษ</span>
        </div>
        <div class="text-2xl font-black font-scifi text-white mb-1">
          ฟรีค่าจัดส่งด่วน
        </div>
        <p class="text-xs text-slate-400 mb-4">บริการส่งด่วนพร้อมประกันภัยสินค้า 100%</p>
        
        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-950 border border-slate-800">
          <span class="font-mono font-black text-amber-300 tracking-widest text-sm">FREESHIP</span>
          <button onclick="copyCoupon('FREESHIP')" class="btn-cyber-primary px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
            <i class="fa-regular fa-copy"></i> คัดลอก
          </button>
        </div>
      </div>

    </div>
  </div>

  <!-- Flash Sale GPUs -->
  <div class="mb-12">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-scifi font-bold text-lg text-white uppercase tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-bolt text-amber-400"></i> การ์ดจอแนะนำราคาโปรโมชั่น (Promotion Highlight)
      </h3>
      <a href="products.php" class="text-xs text-cyan-400 hover:underline">ดูสินค้าทั้งหมด ➔</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($promoGpus as $gpu): ?>
        <div class="glass-panel-elevated p-5 rounded-2xl border border-slate-800 hover:border-cyan-500/40 transition-all flex flex-col justify-between">
          <div>
            <div class="relative gpu-img-container rounded-xl mb-4">
              <img src="<?= e($gpu['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="w-full h-full object-contain">
              <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[9px] font-scifi font-bold" style="background: <?= e($gpu['badge_color']) ?>20; color: <?= e($gpu['badge_color']) ?>; border: 1px solid <?= e($gpu['badge_color']) ?>40;">
                <?= e($gpu['manufacturer']) ?>
              </span>
              <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-rose-500/30 text-rose-300 border border-rose-500/40">
                PROMO DEAL
              </span>
            </div>

            <div class="text-[11px] font-mono text-slate-400 uppercase truncate h-5">
              <?= e($gpu['brand_name']) ?>
            </div>
            <h4 class="font-bold text-white text-sm line-clamp-2 h-10 leading-snug">
              <?= e($gpu['model_name']) ?>
            </h4>

            <div class="flex items-center justify-between my-3 pt-2 border-t border-slate-800/80">
              <div>
                <span class="text-[10px] text-slate-500 font-mono line-through block"><?= formatPrice($gpu['price'] + 3000) ?></span>
                <span class="font-scifi font-black text-lg text-emerald-400"><?= formatPrice($gpu['price']) ?></span>
              </div>
              <span class="text-[10px] font-mono text-emerald-400 font-bold bg-emerald-950/60 px-2 py-1 rounded">
                ประหยัด ฿3,000
              </span>
            </div>
          </div>

          <div class="pt-3 border-t border-slate-800 flex items-center gap-2 mt-auto">
            <button onclick="addToCartAjax(<?= $gpu['id'] ?>)" 
                    class="flex-1 px-3 py-2 rounded-xl text-xs font-scifi font-bold bg-slate-900 hover:bg-slate-800 border border-slate-700 text-slate-200 hover:text-white transition-colors flex items-center justify-center gap-1.5">
              <i class="fa-solid fa-cart-plus text-cyan-400"></i> ใส่ตะกร้า
            </button>
            <?php if (!isAdmin()): ?>
              <button onclick="confirmBuy(<?= $gpu['id'] ?>, '<?= addslashes(e($gpu['model_name'])) ?>', <?= $gpu['price'] ?>)" 
                      class="flex-1 btn-cyber-primary px-3 py-2 rounded-xl text-xs font-scifi font-bold flex items-center justify-center gap-1.5 whitespace-nowrap shadow-md">
                <i class="fa-solid fa-bolt"></i> ซื้อทันที
              </button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<script>
function copyCoupon(code) {
  navigator.clipboard.writeText(code).then(() => {
    Swal.fire({
      icon: 'success',
      title: 'คัดลอกโค้ดสำเร็จ!',
      text: 'นำโค้ด ' + code + ' ไปวางในหน้าตะกร้าสินค้าเพื่อรับส่วนลดได้ทันที',
      background: '#0f172a',
      color: '#f8fafc',
      confirmButtonColor: '#00F0FF',
      timer: 2000
    });
  });
}

function addToCartAjax(id) {
  fetch('add_to_cart.php?ajax=1&gpu_id=' + id)
    .then(r => r.json())
    .then(data => {
      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'เพิ่มลงตะกร้าแล้ว',
          text: data.message,
          background: '#0f172a',
          color: '#f8fafc',
          showCancelButton: true,
          confirmButtonText: 'ไปที่ตะกร้าสินค้า',
          cancelButtonText: 'เลือกสินค้าต่อ',
          confirmButtonColor: '#00F0FF'
        }).then(result => {
          if (result.isConfirmed) {
            window.location.href = 'cart.php';
          }
        });
        // Update nav badge if present
        const badge = document.getElementById('navCartCount');
        if (badge) badge.textContent = data.cart_count;
      } else {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message, background: '#0f172a', color: '#f8fafc' });
      }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
