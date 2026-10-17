<?php
/**
 * Advanced Search & Filter Page
 * Multi-criteria hardware search (Keyword, Brand, Chipset, VRAM, Sorting)
 */
$pageTitle = "ค้นหาและกรองสเปคการ์ดจอ (Search)";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();

// Fetch brands and chipsets for dropdown options
$brandsList = [];
$chipsetsList = [];
if ($pdo) {
    try {
        $brandsList = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();
        $chipsetsList = $pdo->query("SELECT DISTINCT manufacturer FROM chipsets ORDER BY manufacturer ASC")->fetchAll();
    } catch (PDOException $e) {
        // Handled silently
    }
}

// Read filter parameters from GET
$q = trim($_GET['q'] ?? '');
$filterBrand = trim($_GET['brand'] ?? '');
$filterChipset = trim($_GET['chipset'] ?? '');
$filterVram = trim($_GET['vram'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');

$gpus = [];
$totalResults = 0;

if ($pdo) {
    try {
        $sql = "SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.badge_color 
                FROM gpus g
                JOIN brands b ON g.brand_id = b.id
                JOIN chipsets c ON g.chipset_id = c.id
                WHERE 1=1";
        
        $params = [];

        // 1. Keyword search (Model name, Chipset Model, Series)
        if (!empty($q)) {
            $sql .= " AND (g.model_name LIKE :q OR g.chipset_model LIKE :q OR g.series LIKE :q)";
            $params[':q'] = "%{$q}%";
        }

        // 2. Brand Filter
        if (!empty($filterBrand)) {
            $sql .= " AND g.brand_id = :brand_id";
            $params[':brand_id'] = $filterBrand;
        }

        // 3. Chipset Manufacturer Filter
        if (!empty($filterChipset)) {
            $sql .= " AND c.manufacturer = :manufacturer";
            $params[':manufacturer'] = $filterChipset;
        }

        // 4. VRAM Filter (GB)
        if (!empty($filterVram)) {
            $sql .= " AND g.vram_gb = :vram";
            $params[':vram'] = $filterVram;
        }

        // 5. Sorting
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY g.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY g.price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY g.model_name ASC";
                break;
            case 'stock_desc':
                $sql .= " ORDER BY g.stock DESC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY g.id DESC";
                break;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $gpus = $stmt->fetchAll();
        $totalResults = count($gpus);

    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb & Header -->
  <div class="mb-8">
    <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-2">
      <a href="index.php" class="hover:underline">HOME</a>
      <span>/</span>
      <span>SEARCH & FILTER</span>
    </div>
    <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
      ADVANCED GPU FILTER ENGINE
    </h1>
    <p class="text-xs text-slate-400 mt-1">
      ค้นหาการ์ดจอตามสเปค ค่ายชิปเซ็ต ขนาด VRAM แบรนด์ผู้ผลิต และช่วงราคา
    </p>
  </div>

  <!-- Filter Card Controls -->
  <div class="glass-panel-elevated rounded-2xl p-6 mb-10 border border-slate-700/80">
    <form method="GET" action="search.php" class="space-y-4">
      
      <!-- Top Search Input -->
      <div class="relative">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-cyan-400 text-base">
          <i class="fa-solid fa-magnifying-glass"></i>
        </span>
        <input type="text" name="q" value="<?= e($q) ?>" 
               placeholder="ค้นหาชื่อรุ่น เช่น RTX 4090, RX 7900 XTX, Arc A770, ROG Strix..." 
               class="w-full pl-12 pr-4 py-3.5 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
      </div>

      <!-- Dropdown Selectors Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Filter Chipset Manufacturer -->
        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1.5">
            ค่ายชิปเซ็ต (Chipset)
          </label>
          <select name="chipset" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทั้งหมด (All Chipsets)</option>
            <option value="NVIDIA" <?= $filterChipset === 'NVIDIA' ? 'selected' : '' ?>>NVIDIA GeForce</option>
            <option value="AMD" <?= $filterChipset === 'AMD' ? 'selected' : '' ?>>AMD Radeon</option>
            <option value="Intel" <?= $filterChipset === 'Intel' ? 'selected' : '' ?>>Intel Arc</option>
          </select>
        </div>

        <!-- Filter Brand -->
        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1.5">
            แบรนด์ผู้ผลิต (AIB Brand)
          </label>
          <select name="brand" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทั้งหมด (All Brands)</option>
            <?php foreach ($brandsList as $b): ?>
              <option value="<?= $b['id'] ?>" <?= $filterBrand == $b['id'] ? 'selected' : '' ?>>
                <?= e($b['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Filter VRAM Capacity -->
        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1.5">
            ขนาดแรมการ์ดจอ (VRAM)
          </label>
          <select name="vram" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="">ทุกขนาด VRAM</option>
            <option value="8" <?= $filterVram == '8' ? 'selected' : '' ?>>8 GB (Esports & 1080p)</option>
            <option value="12" <?= $filterVram == '12' ? 'selected' : '' ?>>12 GB (1440p High)</option>
            <option value="16" <?= $filterVram == '16' ? 'selected' : '' ?>>16 GB (2K/4K Futureproof)</option>
            <option value="24" <?= $filterVram == '24' ? 'selected' : '' ?>>24 GB (Ultra 4K & Generative AI)</option>
          </select>
        </div>

        <!-- Sort by -->
        <div>
          <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1.5">
            การเรียงลำดับ (Sort by)
          </label>
          <select name="sort" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>รายการล่าสุด (Newest)</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>ราคา: ต่ำไปสูง (Low to High)</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>ราคา: สูงไปต่ำ (High to Low)</option>
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>ชื่อรุ่น (A-Z)</option>
            <option value="stock_desc" <?= $sort === 'stock_desc' ? 'selected' : '' ?>>จำนวนสต็อกคงเหลือมากที่สุด</option>
          </select>
        </div>

      </div>

      <!-- Action Buttons -->
      <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
        <div class="text-xs text-slate-400 font-mono">
          <i class="fa-solid fa-bolt text-cyan-400 mr-1"></i>
          พบผลลัพธ์ทั้งหมด <strong class="text-cyan-300 font-scifi font-bold text-sm"><?= $totalResults ?></strong> รายการ
        </div>
        <div class="flex items-center gap-2">
          <a href="search.php" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-mono transition-colors">
            <i class="fa-solid fa-rotate-left mr-1"></i> ล้างค่าตัวกรอง
          </a>
          <button type="submit" class="btn-cyber-primary px-6 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
            <i class="fa-solid fa-filter"></i> นำไปใช้ (Apply Filter)
          </button>
        </div>
      </div>

    </form>
  </div>

  <!-- Search Results Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if (!empty($gpus)): ?>
      <?php foreach ($gpus as $gpu): 
        $brandClass = 'brand-' . strtolower($gpu['manufacturer']);
        $badgeClass = 'badge-' . strtolower($gpu['manufacturer']);
      ?>
      <div class="glass-panel gpu-card <?= $brandClass ?> rounded-2xl flex flex-col justify-between">
        
        <!-- Image Container -->
        <div class="relative">
          <div class="gpu-img-container rounded-t-2xl">
            <img src="<?= e($gpu['image_url']) ?>" alt="<?= e($gpu['model_name']) ?>" onerror="handleImgError(this)" loading="lazy">
          </div>

          <!-- Chipset Badge -->
          <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-scifi font-bold badge-chipset-overlay" style="color: <?= e($gpu['badge_color']) ?>; border: 1px solid <?= e($gpu['badge_color']) ?>60;">
            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background-color: <?= e($gpu['badge_color']) ?>;"></span>
            <?= e($gpu['manufacturer']) ?>
          </div>

          <!-- Warranty Badge -->
          <div class="absolute top-3 right-3 flex items-center gap-1.5">
            <div class="badge-warranty px-2 py-0.5 rounded-md text-[10px] font-scifi font-bold flex items-center gap-1">
              <i class="fa-solid fa-shield-halved text-amber-400"></i> ประกัน 3 ปี
            </div>
          </div>
        </div>

        <!-- Details -->
        <div class="p-5 flex-1 flex flex-col justify-between">
          <div>
            <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1 truncate h-5 flex items-center" title="<?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>">
              <?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>
            </div>
            <h3 class="font-bold text-white text-sm line-clamp-2 h-10 leading-snug hover:text-cyan-400 transition-colors" title="<?= e($gpu['model_name']) ?>">
              <?= e($gpu['model_name']) ?>
            </h3>

            <!-- Specs Grid -->
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
                <span class="truncate"><?= e($gpu['tdp_watts']) ?>W</span>
              </div>
            </div>
          </div>

          <!-- Stock & Price (Pinned via mt-auto) -->
          <div class="pt-3 border-t border-slate-800/80 mt-auto">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs text-slate-400">คงเหลือในสต็อก:</span>
              <span class="text-xs font-mono font-bold <?= $gpu['stock'] > 5 ? 'text-emerald-400' : ($gpu['stock'] > 0 ? 'text-amber-400' : 'text-rose-400') ?>">
                <?= $gpu['stock'] > 0 ? $gpu['stock'] . ' ชิ้น' : 'สินค้าหมด' ?>
              </span>
            </div>

            <div class="flex items-center justify-between gap-2 mt-1">
              <div class="min-w-0">
                <span class="font-scifi font-black text-base sm:text-lg text-rose-400 truncate block">
                  <?= formatPrice($gpu['price']) ?>
                </span>
              </div>
              
              <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="openQuickViewById(<?= (int)$gpu['id'] ?>)" 
                        class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs transition-colors shrink-0" title="ดูสเปคด่วน">
                  <i class="fa-solid fa-eye"></i>
                </button>

                <?php if (isAdmin()): ?>
                  <!-- Admin Edit Button (No buying for Admin) -->
                  <a href="edit_gpu.php?id=<?= $gpu['id'] ?>" 
                     class="px-3 py-2 rounded-xl text-xs font-scifi font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 transition-all flex items-center gap-1.5 whitespace-nowrap shrink-0" title="แก้ไขข้อมูลการ์ดจอนี้">
                    <i class="fa-solid fa-pen-to-square text-xs"></i> <span>แก้ไข</span>
                  </a>
                <?php else: ?>
                  <!-- Member Buy Button (Clean Horizontal Pill like bottom-right card) -->
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
        <h3 class="font-scifi text-lg text-slate-300">ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา</h3>
        <p class="text-xs text-slate-500 mt-1">ลองเปลี่ยนคำค้นหา หรือปรับลดตัวกรองลงเพื่อดูรายการอื่นๆ</p>
        <a href="search.php" class="inline-block mt-4 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-mono">
          <i class="fa-solid fa-rotate-left mr-1"></i> ล้างการค้นหาทั้งหมด
        </a>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
  window.gpuCatalog = window.gpuCatalog || {};
  (<?= json_encode($gpus, JSON_UNESCAPED_UNICODE) ?>).forEach(function(g) { window.gpuCatalog[g.id] = g; });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
