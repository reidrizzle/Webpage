<?php
/**
 * GPU Comparison Tool (เปรียบเทียบการ์ดจอ)
 * Side-by-side specs matrix & performance breakdown
 */
$pageTitle = "เปรียบเทียบการ์ดจอ (GPU Comparison)";
require_once __DIR__ . '/config/db.php';

$pdo = getDBConnection();
$allGpus = [];

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.architecture, c.badge_color 
                             FROM gpus g
                             JOIN brands b ON g.brand_id = b.id
                             JOIN chipsets c ON g.chipset_id = c.id
                             ORDER BY g.price DESC");
        $allGpus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // silent
    }
}

// Select default GPUs
$gpu1Id = (int)($_GET['gpu1'] ?? ($allGpus[0]['id'] ?? 0));
$gpu2Id = (int)($_GET['gpu2'] ?? ($allGpus[1]['id'] ?? 0));

$gpu1 = null;
$gpu2 = null;

foreach ($allGpus as $g) {
    if ($g['id'] == $gpu1Id) $gpu1 = $g;
    if ($g['id'] == $gpu2Id) $gpu2 = $g;
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
      <span class="text-slate-400">GPU COMPARISON</span>
    </div>
    <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
      เปรียบเทียบการ์ดจอ (HEAD-TO-HEAD MATRIX)
    </h1>
    <p class="text-xs sm:text-sm text-slate-400 mt-1">
      เลือกการ์ดจอ 2 รุ่นเพื่อเปรียบเทียบสเปค พลังประมวลผล สถาปัตยกรรม และความคุ้มค่าแบบตัวต่อตัว
    </p>
  </div>

  <!-- Selectors Form -->
  <form method="GET" action="compare.php" class="glass-panel-elevated p-6 rounded-3xl border border-slate-700/80 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
      
      <!-- GPU 1 Dropdown -->
      <div class="md:col-span-5">
        <label class="block text-xs font-mono text-cyan-400 uppercase tracking-wider mb-2 font-bold">
          <i class="fa-solid fa-microchip mr-1"></i> เลือกการ์ดจอ รุ่นที่ 1:
        </label>
        <select name="gpu1" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-medium focus:outline-none focus:border-cyan-400">
          <?php foreach ($allGpus as $g): ?>
            <option value="<?= $g['id'] ?>" <?= $g['id'] == $gpu1Id ? 'selected' : '' ?>>
              [<?= $g['manufacturer'] ?>] <?= e($g['model_name']) ?> - <?= formatPrice($g['price']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- VS Badge -->
      <div class="md:col-span-2 text-center flex justify-center py-2 md:py-0">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-500 to-rose-500 p-0.5 shadow-lg">
          <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center font-scifi font-black text-white text-sm">
            VS
          </div>
        </div>
      </div>

      <!-- GPU 2 Dropdown -->
      <div class="md:col-span-5">
        <label class="block text-xs font-mono text-rose-400 uppercase tracking-wider mb-2 font-bold">
          <i class="fa-solid fa-microchip mr-1"></i> เลือกการ์ดจอ รุ่นที่ 2:
        </label>
        <select name="gpu2" onchange="this.form.submit()" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-medium focus:outline-none focus:border-rose-400">
          <?php foreach ($allGpus as $g): ?>
            <option value="<?= $g['id'] ?>" <?= $g['id'] == $gpu2Id ? 'selected' : '' ?>>
              [<?= $g['manufacturer'] ?>] <?= e($g['model_name']) ?> - <?= formatPrice($g['price']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>
  </form>

  <?php if ($gpu1 && $gpu2): ?>
    <!-- Comparison Table / Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
      
      <!-- GPU 1 Card Header -->
      <div class="glass-panel-elevated p-6 rounded-3xl border border-cyan-500/40 relative">
        <div class="w-32 h-32 mx-auto mb-4 bg-slate-950 rounded-2xl p-2 border border-slate-800 flex items-center justify-center">
          <img src="<?= e($gpu1['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
        </div>
        <div class="text-center">
          <span class="text-xs font-mono uppercase px-2 py-0.5 rounded font-bold" style="background: <?= e($gpu1['badge_color']) ?>20; color: <?= e($gpu1['badge_color']) ?>;">
            <?= e($gpu1['manufacturer']) ?> • <?= e($gpu1['brand_name']) ?>
          </span>
          <h2 class="text-xl font-bold font-scifi text-white mt-1"><?= e($gpu1['model_name']) ?></h2>
          <div class="text-2xl font-black font-scifi text-cyan-400 mt-2"><?= formatPrice($gpu1['price']) ?></div>
          
          <div class="mt-4 flex items-center justify-center gap-2">
            <button onclick="addToCartAjax(<?= $gpu1['id'] ?>)" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
              <i class="fa-solid fa-cart-plus"></i> ใส่ตะกร้า
            </button>
          </div>
        </div>
      </div>

      <!-- GPU 2 Card Header -->
      <div class="glass-panel-elevated p-6 rounded-3xl border border-rose-500/40 relative">
        <div class="w-32 h-32 mx-auto mb-4 bg-slate-950 rounded-2xl p-2 border border-slate-800 flex items-center justify-center">
          <img src="<?= e($gpu2['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
        </div>
        <div class="text-center">
          <span class="text-xs font-mono uppercase px-2 py-0.5 rounded font-bold" style="background: <?= e($gpu2['badge_color']) ?>20; color: <?= e($gpu2['badge_color']) ?>;">
            <?= e($gpu2['manufacturer']) ?> • <?= e($gpu2['brand_name']) ?>
          </span>
          <h2 class="text-xl font-bold font-scifi text-white mt-1"><?= e($gpu2['model_name']) ?></h2>
          <div class="text-2xl font-black font-scifi text-rose-400 mt-2"><?= formatPrice($gpu2['price']) ?></div>
          
          <div class="mt-4 flex items-center justify-center gap-2">
            <button onclick="addToCartAjax(<?= $gpu2['id'] ?>)" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-1.5">
              <i class="fa-solid fa-cart-plus"></i> ใส่ตะกร้า
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Specs Comparison Matrix Table -->
    <div class="glass-panel-elevated rounded-3xl border border-slate-700/80 overflow-hidden">
      <div class="px-6 py-4 bg-slate-900/80 border-b border-slate-800">
        <h3 class="font-scifi font-bold text-sm text-white uppercase tracking-wider">
          <i class="fa-solid fa-table-columns text-cyan-400 mr-2"></i> ตารางเปรียบเทียบสเปคเชิงลึก (Specification Matrix)
        </h3>
      </div>

      <div class="divide-y divide-slate-800/80 text-xs font-mono">
        
        <!-- Architecture -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">สถาปัตยกรรม (Architecture)</div>
          <div class="col-span-4 text-cyan-300 font-bold"><?= e($gpu1['architecture'] ?? '-') ?></div>
          <div class="col-span-4 text-rose-300 font-bold"><?= e($gpu2['architecture'] ?? '-') ?></div>
        </div>

        <!-- VRAM Size -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">ขนาด VRAM</div>
          <div class="col-span-4 <?= $gpu1['vram_gb'] >= $gpu2['vram_gb'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu1['vram_gb']) ?> GB <?= $gpu1['vram_gb'] > $gpu2['vram_gb'] ? '👑' : '' ?>
          </div>
          <div class="col-span-4 <?= $gpu2['vram_gb'] >= $gpu1['vram_gb'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu2['vram_gb']) ?> GB <?= $gpu2['vram_gb'] > $gpu1['vram_gb'] ? '👑' : '' ?>
          </div>
        </div>

        <!-- Memory Type -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">ชนิดหน่วยความจำ (Memory Type)</div>
          <div class="col-span-4 text-white"><?= e($gpu1['memory_type']) ?></div>
          <div class="col-span-4 text-white"><?= e($gpu2['memory_type']) ?></div>
        </div>

        <!-- Bus Width -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">บัสเมมโมรี่ (Bus Width)</div>
          <div class="col-span-4 <?= $gpu1['bus_width'] >= $gpu2['bus_width'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu1['bus_width']) ?>-bit <?= $gpu1['bus_width'] > $gpu2['bus_width'] ? '👑' : '' ?>
          </div>
          <div class="col-span-4 <?= $gpu2['bus_width'] >= $gpu1['bus_width'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu2['bus_width']) ?>-bit <?= $gpu2['bus_width'] > $gpu1['bus_width'] ? '👑' : '' ?>
          </div>
        </div>

        <!-- Boost Clock -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">ความเร็ว Boost Clock</div>
          <div class="col-span-4 <?= $gpu1['boost_clock'] >= $gpu2['boost_clock'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu1['boost_clock']) ?> MHz <?= $gpu1['boost_clock'] > $gpu2['boost_clock'] ? '👑' : '' ?>
          </div>
          <div class="col-span-4 <?= $gpu2['boost_clock'] >= $gpu1['boost_clock'] ? 'text-emerald-400 font-bold' : 'text-slate-300' ?>">
            <?= e($gpu2['boost_clock']) ?> MHz <?= $gpu2['boost_clock'] > $gpu1['boost_clock'] ? '👑' : '' ?>
          </div>
        </div>

        <!-- TDP Watts -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">การใช้พลังงาน (TDP)</div>
          <div class="col-span-4 text-amber-400 font-bold"><?= e($gpu1['tdp_watts']) ?> Watts</div>
          <div class="col-span-4 text-amber-400 font-bold"><?= e($gpu2['tdp_watts']) ?> Watts</div>
        </div>

        <!-- Stock -->
        <div class="grid grid-cols-12 p-4 hover:bg-slate-900/40 transition-colors">
          <div class="col-span-4 text-slate-400 font-bold">สต็อกสินค้าคงเหลือ</div>
          <div class="col-span-4 text-white"><?= $gpu1['stock'] ?> ชิ้น</div>
          <div class="col-span-4 text-white"><?= $gpu2['stock'] ?> ชิ้น</div>
        </div>

      </div>
    </div>
  <?php endif; ?>

</div>

<script>
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
          cancelButtonText: 'เปรียบเทียบต่อ',
          confirmButtonColor: '#00F0FF'
        }).then(result => {
          if (result.isConfirmed) {
            window.location.href = 'cart.php';
          }
        });
        const badge = document.getElementById('navCartCount');
        if (badge) badge.textContent = data.cart_count;
      }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
