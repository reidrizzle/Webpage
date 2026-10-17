<?php
/**
 * All Products Catalog (สินค้าทั้งหมด)
 * Displays all available GPUs with advanced filters & sorting
 */
$pageTitle = "สินค้าการ์ดจอทั้งหมด (All Products)";
require_once __DIR__ . '/config/db.php';

$pdo = getDBConnection();

// Filters & Query Parameters
$q = trim($_GET['q'] ?? '');
$filterChipset = trim($_GET['chipset'] ?? '');
$filterBrand = (int)($_GET['brand'] ?? 0);
$filterVram = (int)($_GET['vram'] ?? 0);
$sort = trim($_GET['sort'] ?? 'price_desc');

$where = ["1=1"];
$params = [];

if (!empty($q)) {
    $where[] = "(g.model_name LIKE :q OR b.name LIKE :q OR c.series_name LIKE :q)";
    $params[':q'] = "%$q%";
}

if (!empty($filterChipset) && in_array($filterChipset, ['NVIDIA', 'AMD', 'Intel'])) {
    $where[] = "c.manufacturer = :chipset";
    $params[':chipset'] = $filterChipset;
}

if ($filterBrand > 0) {
    $where[] = "g.brand_id = :brand";
    $params[':brand'] = $filterBrand;
}

if ($filterVram > 0) {
    $where[] = "g.vram_gb = :vram";
    $params[':vram'] = $filterVram;
}

// Sorting logic
$orderBy = "g.is_featured DESC, g.price DESC";
switch ($sort) {
    case 'price_asc':
        $orderBy = "g.price ASC";
        break;
    case 'price_desc':
        $orderBy = "g.price DESC";
        break;
    case 'vram_desc':
        $orderBy = "g.vram_gb DESC, g.price DESC";
        break;
    case 'name_asc':
        $orderBy = "g.model_name ASC";
        break;
    case 'newest':
        $orderBy = "g.id DESC";
        break;
}

$gpus = [];
$brandsList = [];

if ($pdo) {
    try {
        $brandsList = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();

        $sql = "SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.architecture, c.badge_color,
                       c.series_name AS chipset_model
                FROM gpus g
                JOIN brands b ON g.brand_id = b.id
                JOIN chipsets c ON g.chipset_id = c.id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY $orderBy";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $gpus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span class="text-slate-400">ALL PRODUCTS</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
        สินค้าการ์ดจอทั้งหมด (ALL GPUS)
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        รวมการ์ดจอระดับไฮเอนด์ NVIDIA GeForce, AMD Radeon, Intel Arc สต็อกแท้ 100%
      </p>
    </div>

    <div>
      <a href="compare.php" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-900 border border-slate-700 text-slate-300 hover:text-white hover:border-cyan-400 transition-colors inline-flex items-center gap-2">
        <i class="fa-solid fa-code-compare text-cyan-400"></i> เปรียบเทียบสเปคการ์ดจอ
      </a>
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="glass-panel-elevated p-6 rounded-3xl border border-slate-700/80 mb-8">
    <form method="GET" action="products.php" class="space-y-4">
      
      <!-- Search Input -->
      <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-cyan-400 text-base">
          <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="text" name="q" value="<?= e($q) ?>" 
               placeholder="ค้นหาชื่อรุ่น เช่น RTX 4090, RX 7900 XTX, Arc A770, ROG Strix..." 
               class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400">
      </div>

      <!-- Filters Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">ค่ายชิปเซ็ต</label>
          <select name="chipset" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทั้งหมด (All Chipsets)</option>
            <option value="NVIDIA" <?= $filterChipset === 'NVIDIA' ? 'selected' : '' ?>>NVIDIA GeForce</option>
            <option value="AMD" <?= $filterChipset === 'AMD' ? 'selected' : '' ?>>AMD Radeon</option>
            <option value="Intel" <?= $filterChipset === 'Intel' ? 'selected' : '' ?>>Intel Arc</option>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">แบรนด์ผู้ผลิต</label>
          <select name="brand" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทุกแบรนด์ (All Brands)</option>
            <?php foreach ($brandsList as $b): ?>
              <option value="<?= $b['id'] ?>" <?= $filterBrand == $b['id'] ? 'selected' : '' ?>>
                <?= e($b['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">ขนาดแรม VRAM</label>
          <select name="vram" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทุกขนาด VRAM</option>
            <option value="4" <?= $filterVram == 4 ? 'selected' : '' ?>>4 GB</option>
            <option value="6" <?= $filterVram == 6 ? 'selected' : '' ?>>6 GB</option>
            <option value="8" <?= $filterVram == 8 ? 'selected' : '' ?>>8 GB</option>
            <option value="12" <?= $filterVram == 12 ? 'selected' : '' ?>>12 GB</option>
            <option value="16" <?= $filterVram == 16 ? 'selected' : '' ?>>16 GB</option>
            <option value="24" <?= $filterVram == 24 ? 'selected' : '' ?>>24 GB</option>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">การเรียงลำดับ</label>
          <select name="sort" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>ราคา: สูงไปต่ำ</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>ราคา: ต่ำไปสูง</option>
            <option value="vram_desc" <?= $sort === 'vram_desc' ? 'selected' : '' ?>>ขนาด VRAM: มากไปน้อย</option>
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>ชื่อรุ่น: A - Z</option>
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>สินค้าใหม่ล่าสุด</option>
          </select>
        </div>
      </div>

    </form>
  </div>

  <!-- Products Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if (!empty($gpus)): ?>
      <?php foreach ($gpus as $gpu): 
        $brandClass = 'brand-' . strtolower($gpu['manufacturer']);
      ?>
        <div class="glass-panel gpu-card <?= $brandClass ?> rounded-2xl flex flex-col justify-between">
          
          <!-- Image -->
          <div class="relative">
            <div class="gpu-img-container rounded-t-2xl">
              <img src="<?= e($gpu['image_url']) ?>" alt="<?= e($gpu['model_name']) ?>" onerror="handleImgError(this)" loading="lazy">
            </div>
            
            <!-- Chipset Badge -->
            <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-scifi font-bold badge-chipset-overlay" style="color: <?= e($gpu['badge_color']) ?>; border: 1px solid <?= e($gpu['badge_color']) ?>60;">
              <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: <?= e($gpu['badge_color']) ?>;"></span>
              <?= e($gpu['manufacturer']) ?>
            </div>

            <!-- Warranty & Top Pick Badges -->
            <div class="absolute top-3 right-3 flex items-center gap-1.5">
              <div class="badge-warranty px-2 py-0.5 rounded-md text-[10px] font-scifi font-bold flex items-center gap-1">
                <i class="fa-solid fa-shield-halved text-amber-400"></i> ประกัน 3 ปี
              </div>
              <?php if (!empty($gpu['is_featured'])): ?>
                <div class="px-2 py-0.5 rounded-md text-[10px] font-scifi font-bold bg-cyan-950/90 text-cyan-300 border border-cyan-500/50 shadow-sm hidden sm:flex items-center gap-1">
                  <i class="fa-solid fa-fire text-cyan-400"></i> TOP
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Body -->
          <div class="p-5 flex-1 flex flex-col justify-between">
            <div>
              <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1 truncate h-5 flex items-center" title="<?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>">
                <?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>
              </div>
              <h3 class="font-bold text-white text-sm line-clamp-2 h-10 leading-snug hover:text-cyan-400 transition-colors" title="<?= e($gpu['model_name']) ?>">
                <?= e($gpu['model_name']) ?>
              </h3>

              <!-- Specs -->
              <div class="grid grid-cols-2 gap-1.5 my-3">
                <div class="spec-chip h-7 flex items-center gap-1.5">
                  <i class="fa-solid fa-memory text-cyan-400 text-[10px]"></i>
                  <span class="truncate"><?= e($gpu['vram_gb']) ?>GB <?= e($gpu['memory_type']) ?></span>
                </div>
                <div class="spec-chip h-7 flex items-center gap-1.5">
                  <i class="fa-solid fa-arrows-split-up-and-left text-indigo-400 text-[10px]"></i>
                  <span class="truncate"><?= e($gpu['bus_width']) ?>-bit</span>
                </div>
                <div class="spec-chip h-7 flex items-center gap-1.5">
                  <i class="fa-solid fa-gauge-high text-emerald-400 text-[10px]"></i>
                  <span class="truncate"><?= e($gpu['boost_clock']) ?> MHz</span>
                </div>
                <div class="spec-chip h-7 flex items-center gap-1.5">
                  <i class="fa-solid fa-bolt text-amber-400 text-[10px]"></i>
                  <span class="truncate"><?= e($gpu['tdp_watts']) ?>W TDP</span>
                </div>
              </div>
            </div>

            <!-- Footer (Pinned with mt-auto) -->
            <div class="pt-3 border-t border-slate-800/80 mt-auto">
              <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-slate-400">สถานะสต็อก:</span>
                <?php if ($gpu['stock'] > 5): ?>
                  <span class="text-[11px] font-mono font-semibold text-emerald-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> พร้อมส่ง (<?= $gpu['stock'] ?> ชิ้น)
                  </span>
                <?php elseif ($gpu['stock'] > 0): ?>
                  <span class="text-[11px] font-mono font-semibold text-amber-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span> ใกล้หมด (เหลือ <?= $gpu['stock'] ?>)
                  </span>
                <?php else: ?>
                  <span class="text-[11px] font-mono font-semibold text-rose-400 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> สินค้าหมด
                  </span>
                <?php endif; ?>
              </div>

              <div class="flex items-center justify-between gap-2 mt-1">
                <div class="min-w-0">
                  <span class="text-[10px] text-slate-500 uppercase font-mono block leading-tight">ราคาพิเศษ</span>
                  <span class="font-scifi font-black text-base sm:text-lg text-rose-400 truncate block">
                    <?= formatPrice($gpu['price']) ?>
                  </span>
                </div>
                
                <div class="flex items-center gap-1.5 shrink-0">
                  <button onclick='addToCartAjax(<?= $gpu['id'] ?>)' 
                          class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs transition-colors shrink-0" title="เพิ่มลงตะกร้า">
                    <i class="fa-solid fa-cart-plus text-cyan-400"></i>
                  </button>

                  <?php if (isAdmin()): ?>
                    <a href="edit_gpu.php?id=<?= $gpu['id'] ?>" 
                       class="px-3 py-2 rounded-xl text-xs font-scifi font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 transition-all flex items-center gap-1.5 whitespace-nowrap shrink-0">
                      <i class="fa-solid fa-pen-to-square text-xs"></i> <span>แก้ไข</span>
                    </a>
                  <?php else: ?>
                    <?php if ($gpu['stock'] > 0): ?>
                      <button onclick="confirmBuy(<?= $gpu['id'] ?>, '<?= addslashes(e($gpu['model_name'])) ?>', <?= $gpu['price'] ?>)" 
                              class="btn-cyber-primary px-3 py-2 rounded-xl text-xs font-scifi font-bold flex items-center gap-1.5 whitespace-nowrap shrink-0 shadow-md shadow-cyan-500/20">
                        <i class="fa-solid fa-cart-shopping text-xs"></i> <span>สั่งซื้อ</span>
                      </button>
                    <?php else: ?>
                      <button disabled class="px-2.5 py-2 rounded-xl bg-slate-800 text-slate-500 text-xs font-mono cursor-not-allowed whitespace-nowrap shrink-0">
                        สินค้าหมด
                      </button>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>

          </div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-full text-center py-16 glass-panel rounded-2xl">
        <i class="fa-solid fa-magnifying-glass text-4xl text-slate-600 mb-4 block"></i>
        <h3 class="font-scifi text-lg text-slate-300">ไม่พบการ์ดจอที่ตรงกับเงื่อนไข</h3>
        <p class="text-xs text-slate-500 mt-1">ลองเปลี่ยนคำค้นหา หรือรีเซ็ตตัวกรอง</p>
        <a href="products.php" class="inline-block mt-4 btn-cyber-primary px-5 py-2 rounded-xl text-xs font-bold">
          ล้างตัวกรองทั้งหมด
        </a>
      </div>
    <?php endif; ?>
  </div>

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
          cancelButtonText: 'เลือกสินค้าต่อ',
          confirmButtonColor: '#00F0FF'
        }).then(result => {
          if (result.isConfirmed) {
            window.location.href = 'cart.php';
          }
        });
        const badge = document.getElementById('navCartCount');
        if (badge) badge.textContent = data.cart_count;
      } else {
        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message, background: '#0f172a', color: '#f8fafc' });
      }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
