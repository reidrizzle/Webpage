<?php
/**
 * Main Home Page (Catalog & Hero Showcase)
 * Cyber-Tech & Futuristic GPU Store
 */
$pageTitle = "หน้าแรก - คลังการ์ดจอเกมมิ่งระดับไฮเอนด์";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();
$gpus = [];
$dbError = null;

// Filter by Manufacturer if clicked in quick chips
$selectedManufacturer = isset($_GET['brand']) ? trim($_GET['brand']) : 'ALL';

if ($pdo) {
    try {
        $sql = "SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.badge_color 
                FROM gpus g
                JOIN brands b ON g.brand_id = b.id
                JOIN chipsets c ON g.chipset_id = c.id";
        
        if ($selectedManufacturer !== 'ALL') {
            $sql .= " WHERE c.manufacturer = :manufacturer";
            $sql .= " ORDER BY g.is_featured DESC, g.price DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':manufacturer' => $selectedManufacturer]);
        } else {
            $sql .= " ORDER BY g.is_featured DESC, g.price DESC";
            $stmt = $pdo->query($sql);
        }
        $gpus = $stmt->fetchAll();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
} else {
    $dbError = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณานำเข้าไฟล์ database/database.sql สู่ MySQL ของคุณ";
}
?>

<!-- Futuristic Hero Banner -->
<section class="relative overflow-hidden py-20 lg:py-28 border-b border-slate-800">
  <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(0,240,255,0.15),rgba(255,255,255,0))]"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      
      <!-- Left Column: Copy & CTAs -->
      <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-950/60 border border-cyan-500/40 text-cyan-300 text-xs font-scifi">
          <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
          NEXT-GEN ARCHITECTURE ARCHIVE 2026
        </div>
        
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black font-scifi tracking-tight text-white leading-tight">
          EXTREME <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-emerald-400 to-indigo-400">GPU COMPUTE</span> & GAMING PERFORMANCE
        </h1>
        
        <p class="text-slate-400 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto lg:mx-0">
          ยกระดับขุมพลังกราฟิกและงานคำนวณปัญญาประดิษฐ์ (AI Workstation) รวบรวมการ์ดจอระดับแฟล็กชิปจากสถาปัตยกรรม <strong class="text-emerald-400">Ada Lovelace</strong>, <strong class="text-rose-400">RDNA 3</strong> และ <strong class="text-cyan-400">Battlemage</strong> พร้อมระบบบริหารจัดการสต็อกที่แม่นยำ
        </p>

        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
          <a href="#catalog" class="btn-cyber-primary px-7 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fa-solid fa-gamepad"></i> ดูสินค้าการ์ดจอทั้งหมด
          </a>
          <a href="search.php" class="btn-cyber-outline px-6 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fa-solid fa-filter"></i> ตัวกรองสเปคขั้นสูง
          </a>
          <a href="reports.php" class="px-5 py-3 rounded-xl bg-purple-950/50 border border-purple-500/40 text-purple-300 hover:text-white hover:bg-purple-900/60 transition-colors text-sm font-scifi flex items-center gap-2">
            <i class="fa-solid fa-chart-line"></i> สรุปรายงานคลัง
          </a>
        </div>

        <!-- Metric Counters -->
        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800/80">
          <div class="glass-panel p-3 rounded-xl text-center">
            <div class="font-scifi text-2xl font-black text-cyan-400"><?= count($gpus) > 0 ? count($gpus) : '12+' ?></div>
            <div class="text-[11px] text-slate-400 uppercase font-mono">Models Catalog</div>
          </div>
          <div class="glass-panel p-3 rounded-xl text-center">
            <div class="font-scifi text-2xl font-black text-emerald-400">350+</div>
            <div class="text-[11px] text-slate-400 uppercase font-mono">TFLOPS Max AI</div>
          </div>
          <div class="glass-panel p-3 rounded-xl text-center">
            <div class="font-scifi text-2xl font-black text-purple-400">100%</div>
            <div class="text-[11px] text-slate-400 uppercase font-mono">Original Warranted</div>
          </div>
        </div>
      </div>

      <!-- Right Column: Hero Futuristic GPU Visual -->
      <div class="lg:col-span-5 relative flex justify-center">
        <div class="relative w-full max-w-md aspect-square rounded-3xl glass-panel-elevated p-6 flex flex-col justify-between border-2 border-cyan-500/20 group hover:border-cyan-500/50 transition-all duration-500">
          <div class="flex justify-between items-center">
            <span class="px-3 py-1 rounded-full text-[10px] font-scifi font-bold bg-cyan-500/10 border border-cyan-500/40 text-cyan-400">
              FEATURED FLAGSHIP
            </span>
            <span class="text-xs font-mono text-slate-400">TITAN CLASS</span>
          </div>

          <div class="relative py-4 flex items-center justify-center">
            <div class="absolute inset-0 bg-cyan-500/10 rounded-full blur-2xl"></div>
            <img src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=700&q=80" 
                 alt="GeForce RTX 4090" 
                 class="relative z-10 max-h-56 object-contain drop-shadow-[0_20px_35px_rgba(0,240,255,0.25)] group-hover:scale-105 group-hover:-rotate-1 transition-transform duration-500">
          </div>

          <div class="bg-slate-950/80 rounded-xl p-3 border border-slate-800">
            <div class="flex justify-between items-center">
              <div>
                <h4 class="font-scifi font-bold text-sm text-white">ASUS ROG RTX 4090 OC</h4>
                <p class="text-[11px] text-slate-400 font-mono">24GB GDDR6X • 384-bit • Ada Lovelace</p>
              </div>
              <div class="text-right">
                <span class="font-scifi font-black text-emerald-400 text-lg">฿78,900</span>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Database Setup Notice if connection issue -->
<?php if ($dbError): ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
  <div class="bg-amber-950/40 border border-amber-500/50 rounded-xl p-4 flex items-center gap-3 text-amber-200 text-sm">
    <i class="fa-solid fa-triangle-exclamation text-xl text-amber-400"></i>
    <div>
      <strong class="font-scifi">Database Notice:</strong> <?= e($dbError) ?>
      <p class="text-xs text-amber-300/80 mt-1">
        วิธีแก้: นำไฟล์ <code class="bg-slate-900 px-1.5 py-0.5 rounded text-amber-300">database/database.sql</code> ไป Import ใน phpMyAdmin หรือ MySQL Workbench บนฐานข้อมูลชื่อ <code class="bg-slate-900 px-1.5 py-0.5 rounded">gpu_store</code>
      </p>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Catalog Section -->
<section id="catalog" class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Header & Manufacturer Chips -->
  <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
    <div>
      <div class="flex items-center gap-2 text-cyan-400 text-xs font-scifi uppercase tracking-wider">
        <i class="fa-solid fa-layer-group"></i> คลังการ์ดจอทั้งหมด (GPU Catalog)
      </div>
      <h2 class="text-2xl sm:text-3xl font-black font-scifi text-white mt-1">
        เลือกซื้อขุมพลังประมวลผลกราฟิก
      </h2>
    </div>

    <!-- Chips Filter by Manufacturer -->
    <div class="flex flex-wrap items-center gap-2">
      <a href="index.php?brand=ALL#catalog" 
         class="px-4 py-2 rounded-xl text-xs font-scifi font-bold transition-all <?= $selectedManufacturer === 'ALL' ? 'bg-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/30' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:border-slate-700' ?>">
        ทั้งหมด (ALL)
      </a>
      <a href="index.php?brand=NVIDIA#catalog" 
         class="px-4 py-2 rounded-xl text-xs font-scifi font-bold flex items-center gap-2 transition-all <?= $selectedManufacturer === 'NVIDIA' ? 'bg-[#00FF66] text-black shadow-lg shadow-[#00FF66]/30' : 'bg-slate-900 border border-slate-800 text-emerald-400 hover:border-emerald-500/50' ?>">
        <span class="w-2 h-2 rounded-full bg-[#00FF66]"></span> NVIDIA GEFORCE
      </a>
      <a href="index.php?brand=AMD#catalog" 
         class="px-4 py-2 rounded-xl text-xs font-scifi font-bold flex items-center gap-2 transition-all <?= $selectedManufacturer === 'AMD' ? 'bg-[#FF0055] text-white shadow-lg shadow-[#FF0055]/30' : 'bg-slate-900 border border-slate-800 text-rose-400 hover:border-rose-500/50' ?>">
        <span class="w-2 h-2 rounded-full bg-[#FF0055]"></span> AMD RADEON
      </a>
      <a href="index.php?brand=Intel#catalog" 
         class="px-4 py-2 rounded-xl text-xs font-scifi font-bold flex items-center gap-2 transition-all <?= $selectedManufacturer === 'Intel' ? 'bg-[#00F0FF] text-black shadow-lg shadow-[#00F0FF]/30' : 'bg-slate-900 border border-slate-800 text-cyan-400 hover:border-cyan-500/50' ?>">
        <span class="w-2 h-2 rounded-full bg-[#00F0FF]"></span> INTEL ARC
      </a>
    </div>
  </div>

  <!-- GPU Grid Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if (!empty($gpus)): ?>
      <?php foreach ($gpus as $gpu): 
        $brandClass = 'brand-' . strtolower($gpu['manufacturer']);
        $badgeClass = 'badge-' . strtolower($gpu['manufacturer']);
      ?>
      <div class="glass-panel gpu-card <?= $brandClass ?> rounded-2xl flex flex-col justify-between">
        
        <!-- Top Image Container with Manufacturer Badge -->
        <div class="relative">
          <div class="gpu-img-container rounded-t-2xl">
            <img src="<?= e($gpu['image_url']) ?>" alt="<?= e($gpu['model_name']) ?>" onerror="handleImgError(this)" loading="lazy">
          </div>

          <!-- Chipset Badge -->
          <div class="absolute top-3 left-3 flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-scifi font-bold <?= $badgeClass ?>">
            <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?= e($gpu['badge_color']) ?>;"></span>
            <?= e($gpu['manufacturer']) ?>
          </div>

          <!-- Featured Star -->
          <?php if (!empty($gpu['is_featured'])): ?>
            <div class="absolute top-3 right-3 px-2 py-0.5 rounded text-[10px] font-scifi font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
              <i class="fa-solid fa-star mr-1"></i> TOP PICK
            </div>
          <?php endif; ?>
        </div>

        <!-- Body Content -->
        <div class="p-5 flex-1 flex flex-col justify-between">
          <div>
            <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1 truncate h-5 flex items-center" title="<?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>">
              <?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>
            </div>
            <h3 class="font-bold text-white text-sm line-clamp-2 h-10 leading-snug hover:text-cyan-400 transition-colors" title="<?= e($gpu['model_name']) ?>">
              <?= e($gpu['model_name']) ?>
            </h3>

            <!-- Quick Specs Chips Grid -->
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

          <!-- Stock & Price Footer (Pinned to bottom via mt-auto) -->
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
                <span class="font-scifi font-black text-base sm:text-lg text-transparent bg-clip-text bg-gradient-to-r from-white to-cyan-300 truncate block">
                  <?= formatPrice($gpu['price']) ?>
                </span>
              </div>
              
              <div class="flex items-center gap-1.5 shrink-0">
                <button onclick='openQuickView(<?= json_encode($gpu, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
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
        <i class="fa-solid fa-database text-4xl text-slate-600 mb-4 block"></i>
        <h3 class="font-scifi text-lg text-slate-300">ยังไม่พบข้อมูลการ์ดจอ</h3>
        <p class="text-xs text-slate-500 mt-1">กรุณานำเข้าข้อมูลจำลองจาก database/database.sql หรือกดเพิ่มข้อมูลการ์ดจอใหม่</p>
        <a href="add_gpu.php" class="inline-block mt-4 btn-cyber-primary px-5 py-2.5 rounded-xl text-xs font-bold">
          <i class="fa-solid fa-plus-circle mr-1"></i> เพิ่มการ์ดจอตัวแรก
        </a>
      </div>
    <?php endif; ?>
  </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
