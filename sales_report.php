<?php
/**
 * Sales Analytics & Best Selling GPUs Report Page
 * Comprehensive executive sales dashboard:
 * - Gross revenue, total units sold, completed orders, average order value (AOV)
 * - Top-Selling GPUs Leaderboard (อันดับการ์ดจอขายดีที่สุด) highlighting #1 Best Seller
 * - Visual Chart.js analytics: Units sold per GPU model & Sales share by chipset
 * - Revenue breakdown by Brand (AIB Partners)
 * - Complete order history transaction log
 * - Print-ready executive report view
 */
$pageTitle = "รายงานยอดขายและการ์ดจอขายดีที่สุด (Sales & Best Sellers Report)";
require_once __DIR__ . '/config/db.php';
requireAdmin();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();

$kpi = [
    'total_orders'     => 0,
    'total_units_sold' => 0,
    'total_revenue'    => 0,
    'avg_order_value'  => 0
];

$topSellingGpus = [];
$chipsetSales   = [];
$brandSales     = [];
$recentOrders   = [];
$bestSeller     = null;

// Chart datasets
$gpuChartLabels = [];
$gpuChartUnits  = [];
$gpuChartRevenue = [];

$chipsetChartLabels = [];
$chipsetChartUnits  = [];
$chipsetChartColors = [];

if ($pdo) {
    try {
        // 1. KPI Overview Summary
        $stmtKpi = $pdo->query("SELECT 
                                    COUNT(id) AS total_orders,
                                    COALESCE(SUM(quantity), 0) AS total_units_sold,
                                    COALESCE(SUM(total_price), 0) AS total_revenue,
                                    COALESCE(AVG(total_price), 0) AS avg_order_value
                                FROM orders 
                                WHERE status = 'completed'");
        $kpiRow = $stmtKpi->fetch();
        if ($kpiRow) {
            $kpi = $kpiRow;
        }

        // 2. Best-Selling GPUs Ranking (การ์ดจอตัวไหนขายดีสุด)
        $sqlTopGpu = "SELECT 
                          g.id,
                          g.model_name,
                          g.chipset_model,
                          g.series,
                          g.vram_gb,
                          g.memory_type,
                          g.price,
                          g.stock,
                          g.image_url,
                          g.status,
                          b.name AS brand_name,
                          c.manufacturer,
                          c.series_name,
                          c.badge_color,
                          COALESCE(SUM(o.quantity), 0) AS total_sold_qty,
                          COALESCE(SUM(o.total_price), 0) AS total_revenue,
                          COUNT(o.id) AS order_count
                      FROM gpus g
                      JOIN brands b ON g.brand_id = b.id
                      JOIN chipsets c ON g.chipset_id = c.id
                      LEFT JOIN orders o ON g.id = o.gpu_id AND o.status = 'completed'
                      GROUP BY g.id, g.model_name, g.chipset_model, g.series, g.vram_gb, g.memory_type, g.price, g.stock, g.image_url, g.status, b.name, c.manufacturer, c.series_name, c.badge_color
                      ORDER BY total_sold_qty DESC, total_revenue DESC";
        $topSellingGpus = $pdo->query($sqlTopGpu)->fetchAll();

        // Identify Top #1 Best Seller
        foreach ($topSellingGpus as $gpuItem) {
            if ($gpuItem['total_sold_qty'] > 0) {
                $bestSeller = $gpuItem;
                break;
            }
        }
        if (!$bestSeller && !empty($topSellingGpus)) {
            $bestSeller = $topSellingGpus[0];
        }

        // Prepare Top 8 GPUs for Bar Chart
        $chartCount = 0;
        foreach ($topSellingGpus as $gRow) {
            if ($chartCount >= 8) break;
            $gpuChartLabels[] = mb_substr($gRow['model_name'], 0, 22) . '...';
            $gpuChartUnits[]   = (int)$gRow['total_sold_qty'];
            $gpuChartRevenue[] = (float)$gRow['total_revenue'];
            $chartCount++;
        }

        // 3. Sales By Chipset Manufacturer (NVIDIA vs AMD vs Intel)
        $sqlChipsetSales = "SELECT 
                                c.manufacturer,
                                c.badge_color,
                                COALESCE(SUM(o.quantity), 0) AS units_sold,
                                COALESCE(SUM(o.total_price), 0) AS total_revenue
                            FROM chipsets c
                            JOIN gpus g ON c.id = g.chipset_id
                            LEFT JOIN orders o ON g.id = o.gpu_id AND o.status = 'completed'
                            GROUP BY c.manufacturer, c.badge_color
                            ORDER BY units_sold DESC, total_revenue DESC";
        $chipsetSales = $pdo->query($sqlChipsetSales)->fetchAll();

        foreach ($chipsetSales as $cRow) {
            $chipsetChartLabels[] = $cRow['manufacturer'] . ' (' . number_format($cRow['units_sold']) . ' ชิ้น)';
            $chipsetChartUnits[]  = (int)$cRow['units_sold'];
            $chipsetChartColors[] = $cRow['badge_color'] ?: '#00F0FF';
        }

        // 4. Sales By Brand
        $sqlBrandSales = "SELECT 
                              b.name AS brand_name,
                              b.country,
                              COALESCE(SUM(o.quantity), 0) AS units_sold,
                              COALESCE(SUM(o.total_price), 0) AS total_revenue
                          FROM brands b
                          JOIN gpus g ON b.id = g.brand_id
                          LEFT JOIN orders o ON g.id = o.gpu_id AND o.status = 'completed'
                          GROUP BY b.id, b.name, b.country
                          ORDER BY units_sold DESC, total_revenue DESC";
        $brandSales = $pdo->query($sqlBrandSales)->fetchAll();

        // 5. Recent Completed Orders Feed
        $sqlRecent = "SELECT 
                          o.*, 
                          u.full_name, 
                          u.username,
                          g.model_name, 
                          g.image_url, 
                          b.name AS brand_name,
                          c.manufacturer
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      JOIN gpus g ON o.gpu_id = g.id
                      JOIN brands b ON g.brand_id = b.id
                      JOIN chipsets c ON g.chipset_id = c.id
                      ORDER BY o.order_date DESC
                      LIMIT 25";
        $recentOrders = $pdo->query($sqlRecent)->fetchAll();

    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Printable Document Header (Visible only in Print mode) -->
  <div class="hidden print:block text-black mb-8 border-b-2 border-black pb-4">
    <div class="flex justify-between items-start">
      <div>
        <h1 class="text-2xl font-black uppercase tracking-wider">CYBER-GPU STORE - SALES & BEST SELLERS REPORT</h1>
        <p class="text-xs text-gray-600">รายงานวิเคราะห์สรุปยอดจำหน่ายสินค้าและสถิติการ์ดจอขายดีที่สุด</p>
      </div>
      <div class="text-right text-xs">
        <p>วันที่พิมพ์รายงาน: <strong><?= date('d/m/Y H:i') ?> (เวลาประเทศไทย)</strong></p>
        <p>ผู้จัดทำรายงาน: <strong><?= e($_SESSION['full_name'] ?? 'Neo Cyber Admin') ?> (@<?= e($_SESSION['username'] ?? 'admin') ?>)</strong></p>
      </div>
    </div>
  </div>

  <!-- Breadcrumbs & Header Controls -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 print:hidden">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <a href="manage.php" class="hover:underline text-slate-400">ADMIN CONTROL</a>
        <span>/</span>
        <span class="text-emerald-400 font-bold">SALES ANALYTICS</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide flex items-center gap-3">
        SALES & BEST SELLERS REPORT
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        รายงานวิเคราะห์ยอดขายรวม สถิติจำนวนชิ้น และจัดอันดับการ์ดจอขายดีที่สุดในระบบ
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="reports.php" class="px-4 py-2.5 rounded-xl bg-purple-950/60 border border-purple-500/40 text-purple-300 hover:text-white hover:bg-purple-900/60 transition-colors text-xs font-scifi font-bold flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-chart-pie"></i> รายงานสรุปคลังสินค้า
      </a>

      <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 hover:border-cyan-400 text-slate-200 hover:text-white transition-all text-xs font-scifi font-bold flex items-center gap-2 shadow-md cursor-pointer">
        <i class="fa-solid fa-print text-cyan-400"></i> พิมพ์รายงานยอดขาย
      </button>

      <a href="manage.php" class="btn-cyber-primary px-4 py-2.5 rounded-xl text-xs font-bold tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-boxes-stacked"></i> จัดการคลังการ์ดจอ
      </a>
    </div>
  </div>

  <!-- Dual Report Mode Tabs (รายงานยอดขาย vs รายงานสรุปคลัง) -->
  <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 mb-8 max-w-xl shadow-xl print:hidden">
    <a href="sales_report.php" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-scifi font-bold text-center bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm flex items-center justify-center gap-2">
      <i class="fa-solid fa-chart-line text-emerald-400"></i>
      <span>รายงานยอดขาย & สินค้าขายดี</span>
    </a>
    <a href="reports.php" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-scifi font-medium text-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all flex items-center justify-center gap-2">
      <i class="fa-solid fa-chart-pie"></i>
      <span>รายงานสรุปคลัง & มูลค่าสินค้า</span>
    </a>
  </div>

  <!-- 1. Executive Metric Summary Cards (4 Cards) -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    
    <!-- Total Revenue -->
    <div class="glass-panel-elevated p-5 sm:p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-emerald-500/50 transition-all">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">ยอดขายรวมทั้งหมด</span>
        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
          <i class="fa-solid fa-coins text-sm"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">
        <?= formatPrice($kpi['total_revenue']) ?>
      </div>
      <div class="text-[10px] font-mono text-slate-500 mt-2 flex items-center gap-1">
        <span class="text-emerald-400 font-bold">● ชำระเงินสำเร็จ 100%</span>
      </div>
    </div>

    <!-- Units Sold -->
    <div class="glass-panel-elevated p-5 sm:p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-cyan-500/50 transition-all">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">จำนวนที่ขายได้</span>
        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400">
          <i class="fa-solid fa-boxes-packing text-sm"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-white">
        <?= number_format($kpi['total_units_sold']) ?> <span class="text-sm font-normal text-slate-400">ชิ้น</span>
      </div>
      <div class="text-[10px] font-mono text-slate-500 mt-2">
        รวมยอดสั่งซื้อจากการ์ดจอทุกรุ่น
      </div>
    </div>

    <!-- Total Completed Orders -->
    <div class="glass-panel-elevated p-5 sm:p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-purple-500/50 transition-all">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">จำนวนคำสั่งซื้อ</span>
        <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
          <i class="fa-solid fa-receipt text-sm"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-purple-400">
        <?= number_format($kpi['total_orders']) ?> <span class="text-sm font-normal text-slate-400">ออเดอร์</span>
      </div>
      <div class="text-[10px] font-mono text-slate-500 mt-2">
        สถานะสำเร็จทั้งหมด (Completed)
      </div>
    </div>

    <!-- Average Order Value (AOV) -->
    <div class="glass-panel-elevated p-5 sm:p-6 rounded-2xl border border-slate-800 relative overflow-hidden group hover:border-amber-500/50 transition-all">
      <div class="flex items-center justify-between mb-2">
        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">ยอดสั่งซื้อเฉลี่ย (AOV)</span>
        <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400">
          <i class="fa-solid fa-scale-balanced text-sm"></i>
        </div>
      </div>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-amber-300">
        <?= formatPrice($kpi['avg_order_value']) ?>
      </div>
      <div class="text-[10px] font-mono text-slate-500 mt-2">
        เฉลี่ยต่อ 1 ใบเสร็จคำสั่งซื้อ
      </div>
    </div>

  </div>

  <!-- 2. TOP #1 BEST SELLER HERO SHOWCASE (การ์ดจอที่ขายดีที่สุด) -->
  <?php if ($bestSeller && $bestSeller['total_sold_qty'] > 0): ?>
    <?php 
      $bestShare = $kpi['total_units_sold'] > 0 ? ($bestSeller['total_sold_qty'] / $kpi['total_units_sold']) * 100 : 0;
    ?>
    <div class="glass-panel-elevated p-6 sm:p-8 rounded-3xl border-2 border-amber-500/50 bg-gradient-to-r from-amber-950/40 via-slate-950 to-slate-900/90 relative overflow-hidden mb-10 shadow-2xl shadow-amber-950/20">
      <div class="absolute -right-16 -top-16 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
        
        <!-- Left Showcase: Badge & GPU Image -->
        <div class="flex flex-col sm:flex-row items-center gap-6 text-center sm:text-left w-full lg:w-auto">
          <div class="relative shrink-0 group">
            <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-3xl ring-4 ring-amber-500/60 p-3 bg-slate-950 shadow-2xl shadow-amber-500/20 flex items-center justify-center relative overflow-hidden">
              <img src="<?= e($bestSeller['image_url']) ?>" alt="Best Seller GPU" onerror="handleImgError(this)" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
              
              <!-- Crown / Trophy Floating Badge -->
              <div class="absolute top-2 left-2 px-2.5 py-1 rounded-xl bg-amber-500 text-slate-950 text-[10px] font-scifi font-black flex items-center gap-1 shadow-lg">
                <i class="fa-solid fa-trophy"></i> NO.1 CHAMPION
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
              <span class="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                🏆 สินค้าขายดีอันดับ 1
              </span>
              <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase" style="background-color: <?= e($bestSeller['badge_color']) ?>20; color: <?= e($bestSeller['badge_color']) ?>; border: 1px solid <?= e($bestSeller['badge_color']) ?>40;">
                <?= e($bestSeller['manufacturer']) ?>
              </span>
              <span class="text-xs font-mono text-slate-400">แบรนด์: <?= e($bestSeller['brand_name']) ?></span>
            </div>

            <h2 class="text-xl sm:text-2xl font-black font-scifi text-white tracking-wide">
              <?= e($bestSeller['model_name']) ?>
            </h2>

            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-xs font-mono text-slate-300 pt-1">
              <span><i class="fa-solid fa-microchip text-cyan-400 mr-1"></i> <?= e($bestSeller['chipset_model']) ?></span>
              <span><i class="fa-solid fa-memory text-purple-400 mr-1"></i> <?= $bestSeller['vram_gb'] ?>GB <?= e($bestSeller['memory_type']) ?></span>
              <span><i class="fa-solid fa-tag text-emerald-400 mr-1"></i> ราคา <?= formatPrice($bestSeller['price']) ?></span>
            </div>

            <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-2">
              <span class="px-2.5 py-1 rounded-lg text-xs font-mono <?= $bestSeller['stock'] > 0 ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-500/30' : 'bg-rose-950/60 text-rose-400 border border-rose-500/30' ?>">
                <i class="fa-solid fa-boxes-stacked mr-1"></i> คงเหลือในคลัง: <strong class="text-white"><?= $bestSeller['stock'] ?></strong> ชิ้น
              </span>
              <a href="edit_gpu.php?id=<?= $bestSeller['id'] ?>" class="text-xs font-mono text-cyan-400 hover:underline inline-flex items-center gap-1">
                <i class="fa-solid fa-pen-to-square"></i> แก้ไขข้อมูล / เติมของ ➔
              </a>
            </div>
          </div>
        </div>

        <!-- Right Showcase: Highlight Numbers -->
        <div class="flex sm:flex-row lg:flex-col gap-4 w-full lg:w-64 shrink-0">
          
          <div class="flex-1 p-4 rounded-2xl bg-slate-900/90 border border-amber-500/30 text-center">
            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider block">ยอดขายสะสม (Units Sold)</span>
            <span class="text-3xl sm:text-4xl font-black font-scifi text-amber-300 block my-1">
              <?= number_format($bestSeller['total_sold_qty']) ?> <span class="text-sm font-normal text-slate-400">ชิ้น</span>
            </span>
            <span class="text-[10px] font-mono text-emerald-400">
              ครองสัดส่วน <?= number_format($bestShare, 1) ?>% ของยอดขายทั้งหมด
            </span>
          </div>

          <div class="flex-1 p-4 rounded-2xl bg-slate-900/90 border border-emerald-500/30 text-center">
            <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider block">รายรับรวม (Total Gross Sales)</span>
            <span class="text-xl sm:text-2xl font-black font-scifi text-emerald-400 block my-1">
              <?= formatPrice($bestSeller['total_revenue']) ?>
            </span>
            <span class="text-[10px] font-mono text-slate-400">
              จาก <?= number_format($bestSeller['order_count']) ?> คำสั่งซื้อ
            </span>
          </div>

        </div>

      </div>
    </div>
  <?php endif; ?>

  <!-- 3. Visual Charts Grid (Chart.js) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10 print:hidden">
    
    <!-- Left: Best Selling GPUs Bar Chart (7 cols) -->
    <div class="lg:col-span-7 glass-panel-elevated p-6 rounded-3xl border border-slate-800">
      <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
        <div>
          <h3 class="font-scifi font-bold text-base text-cyan-400 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-chart-column"></i> อันดับยอดขายการ์ดจอ (Units Sold per GPU)
          </h3>
          <p class="text-[11px] font-mono text-slate-400 mt-0.5">เปรียบเทียบจำนวนชิ้นที่ขายได้ของการ์ดจอแต่ละรุ่น</p>
        </div>
        <span class="text-xs font-mono px-2 py-0.5 rounded bg-cyan-950 border border-cyan-500/40 text-cyan-400 font-bold">
          TOP MODELS
        </span>
      </div>

      <div class="relative h-72">
        <canvas id="gpuSalesChart"></canvas>
      </div>
    </div>

    <!-- Right: Sales Share by Chipset Doughnut Chart (5 cols) -->
    <div class="lg:col-span-5 glass-panel-elevated p-6 rounded-3xl border border-slate-800">
      <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
        <div>
          <h3 class="font-scifi font-bold text-base text-emerald-400 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-pie-chart"></i> สัดส่วนยอดขายตามค่ายชิปเซ็ต
          </h3>
          <p class="text-[11px] font-mono text-slate-400 mt-0.5">CHIPSET MARKET SHARE (NVIDIA / AMD / Intel)</p>
        </div>
      </div>

      <div class="relative h-72 flex items-center justify-center">
        <canvas id="chipsetSalesChart"></canvas>
      </div>
    </div>

  </div>

  <!-- 4. Top-Selling GPUs Full Leaderboard Table (การ์ดจอตัวไหนขายดีสุด) -->
  <div class="glass-panel-elevated p-6 sm:p-7 rounded-3xl border border-slate-800 mb-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-3 border-b border-slate-800">
      <div>
        <h3 class="font-scifi font-bold text-lg text-white uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-trophy text-amber-400"></i> ตารางอันดับการ์ดจอขายดีที่สุด (GPU Sales Ranking)
        </h3>
        <p class="text-xs text-slate-400 mt-1">
          เรียงลำดับตามจำนวนชิ้นที่จำหน่ายได้ (Units Sold) และรายรับรวม (Total Revenue)
        </p>
      </div>
      <span class="text-xs font-mono px-3 py-1 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-semibold self-start sm:self-auto">
        ทั้งหมด <?= count($topSellingGpus) ?> รุ่น
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="text-slate-400 uppercase font-mono text-[11px] border-b border-slate-800 bg-slate-900/50">
          <tr>
            <th class="py-3 px-3 text-center">อันดับ</th>
            <th class="py-3 px-3">รุ่นการ์ดจอ (GPU Model)</th>
            <th class="py-3 px-3">ค่าย / แบรนด์</th>
            <th class="py-3 px-3 text-right">ราคาต่อชิ้น</th>
            <th class="py-3 px-3 text-center">ขายได้ (ชิ้น)</th>
            <th class="py-3 px-3">ส่วนแบ่งยอดขาย</th>
            <th class="py-3 px-3 text-right">รายรับรวม (฿)</th>
            <th class="py-3 px-3 text-center">สต็อกปัจจุบัน</th>
            <th class="py-3 px-3 text-center print:hidden">การจัดการ</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80 font-mono">
          <?php if (!empty($topSellingGpus)): ?>
            <?php 
              $rank = 1;
              $totalUnits = max(1, (int)$kpi['total_units_sold']);
              foreach ($topSellingGpus as $gpu): 
                $soldQty = (int)$gpu['total_sold_qty'];
                $pct = ($soldQty / $totalUnits) * 100;
                
                // Rank styling
                $rankBadge = "<span class='px-2 py-1 rounded-lg bg-slate-800 text-slate-400 font-bold'>#{$rank}</span>";
                if ($rank === 1 && $soldQty > 0) {
                    $rankBadge = "<span class='px-2.5 py-1 rounded-xl bg-amber-500/20 border border-amber-500/50 text-amber-300 font-bold flex items-center justify-center gap-1 shadow-sm'><i class='fa-solid fa-trophy text-amber-400'></i> 1</span>";
                } elseif ($rank === 2 && $soldQty > 0) {
                    $rankBadge = "<span class='px-2.5 py-1 rounded-xl bg-slate-400/20 border border-slate-400/50 text-slate-200 font-bold flex items-center justify-center gap-1'><i class='fa-solid fa-medal text-slate-300'></i> 2</span>";
                } elseif ($rank === 3 && $soldQty > 0) {
                    $rankBadge = "<span class='px-2.5 py-1 rounded-xl bg-amber-700/20 border border-amber-700/50 text-amber-500 font-bold flex items-center justify-center gap-1'><i class='fa-solid fa-award text-amber-600'></i> 3</span>";
                }
            ?>
              <tr class="hover:bg-slate-900/70 transition-colors <?= $rank === 1 && $soldQty > 0 ? 'bg-amber-950/10' : '' ?>">
                <!-- Rank -->
                <td class="py-3.5 px-3 text-center">
                  <?= $rankBadge ?>
                </td>

                <!-- Model Info -->
                <td class="py-3.5 px-3">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-slate-950 p-1 border border-slate-800 shrink-0 flex items-center justify-center">
                      <img src="<?= e($gpu['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-white text-xs truncate max-w-[240px] sm:max-w-xs" title="<?= e($gpu['model_name']) ?>">
                        <?= e($gpu['model_name']) ?>
                      </div>
                      <span class="text-[10px] text-slate-500">
                        <?= $gpu['vram_gb'] ?>GB <?= e($gpu['memory_type']) ?>
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Manufacturer & Brand -->
                <td class="py-3.5 px-3">
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-200"><?= e($gpu['brand_name']) ?></span>
                    <span class="text-[10px]" style="color: <?= e($gpu['badge_color']) ?>;">
                      <?= e($gpu['manufacturer']) ?>
                    </span>
                  </div>
                </td>

                <!-- Unit Price -->
                <td class="py-3.5 px-3 text-right font-semibold text-slate-300">
                  <?= formatPrice($gpu['price']) ?>
                </td>

                <!-- Sold Units -->
                <td class="py-3.5 px-3 text-center">
                  <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?= $soldQty > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-800 text-slate-500' ?>">
                    <?= number_format($soldQty) ?> ชิ้น
                  </span>
                </td>

                <!-- Market Share Bar -->
                <td class="py-3.5 px-3 min-w-[140px]">
                  <div class="space-y-1">
                    <div class="flex justify-between text-[10px] text-slate-400">
                      <span><?= number_format($pct, 1) ?>%</span>
                      <span><?= $gpu['order_count'] ?> ออเดอร์</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                      <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-cyan-400 transition-all duration-500" style="width: <?= min(100, max(0, $pct)) ?>%;"></div>
                    </div>
                  </div>
                </td>

                <!-- Total Revenue -->
                <td class="py-3.5 px-3 text-right font-scifi font-bold text-emerald-400 text-sm">
                  <?= formatPrice($gpu['total_revenue']) ?>
                </td>

                <!-- Current Stock -->
                <td class="py-3.5 px-3 text-center">
                  <?php if ($gpu['stock'] <= 0): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-rose-500/20 text-rose-400 border border-rose-500/30">หมดคลัง (0)</span>
                  <?php elseif ($gpu['stock'] <= 3): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-400 border border-amber-500/30 font-bold">เหลือ <?= $gpu['stock'] ?> ชิ้น</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/10 text-emerald-400"><?= $gpu['stock'] ?> ชิ้น</span>
                  <?php endif; ?>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-3 text-center print:hidden">
                  <a href="edit_gpu.php?id=<?= $gpu['id'] ?>" class="p-1.5 rounded-lg text-cyan-400 hover:text-white hover:bg-cyan-950/60 transition-colors inline-block" title="แก้ไข / ปรับปรุงสต็อก">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                </td>
              </tr>
            <?php 
                $rank++;
              endforeach; 
            ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center py-8 text-slate-500">
                ยังไม่มีข้อมูลยอดขายในระบบ
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 5. Sales by Brand Breakdown (AIB Partners) -->
  <div class="glass-panel-elevated p-6 sm:p-7 rounded-3xl border border-slate-800 mb-10">
    <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
      <div>
        <h3 class="font-scifi font-bold text-base text-purple-400 uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-building"></i> ยอดขายแบ่งตามแบรนด์ผู้ผลิต (Sales by Brand)
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">สรุปจำนวนชิ้นและมูลค่ารายรับรวมแยกตามยี่ห้อ (ASUS, MSI, GIGABYTE, ZOTAC, Sapphire ฯลฯ)</p>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($brandSales as $bRow): ?>
        <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 hover:border-purple-500/40 transition-colors">
          <div class="flex items-center justify-between mb-2">
            <span class="font-bold text-white text-sm"><?= e($bRow['brand_name']) ?></span>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-500/30">
              <?= e($bRow['country']) ?>
            </span>
          </div>

          <div class="flex items-center justify-between text-xs font-mono mt-3 pt-2 border-t border-slate-800/80">
            <span class="text-slate-400">จำนวนที่ขาย:</span>
            <span class="font-bold text-white"><?= number_format($bRow['units_sold']) ?> ชิ้น</span>
          </div>

          <div class="flex items-center justify-between text-xs font-mono mt-1">
            <span class="text-slate-400">รายรับรวม:</span>
            <span class="font-scifi font-bold text-emerald-400"><?= formatPrice($bRow['total_revenue']) ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- 6. Recent Sales Orders Log Feed (ตารางบันทึกรายการคำสั่งซื้อ) -->
  <div class="glass-panel-elevated p-6 sm:p-7 rounded-3xl border border-slate-800">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-slate-800">
      <div>
        <h3 class="font-scifi font-bold text-base text-cyan-400 uppercase tracking-wider flex items-center gap-2">
          <i class="fa-solid fa-clock-rotate-left"></i> ประวัติรายการสั่งซื้อล่าสุด (Recent Sales Orders)
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">บันทึกรายการสั่งซื้อสำเร็จทั้งหมดในระบบ พร้อมเวลาประเทศไทย (UTC+7)</p>
      </div>
      <span class="text-xs font-mono px-3 py-1 rounded-xl bg-slate-900 text-slate-300 border border-slate-800">
        แสดง <?= count($recentOrders) ?> รายการล่าสุด
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="text-slate-400 uppercase font-mono text-[11px] border-b border-slate-800 bg-slate-900/50">
          <tr>
            <th class="py-3 px-3">รหัสคำสั่งซื้อ</th>
            <th class="py-3 px-3">วัน-เวลาที่สั่งซื้อ</th>
            <th class="py-3 px-3">ผู้สั่งซื้อ (Customer)</th>
            <th class="py-3 px-3">รายการการ์ดจอ</th>
            <th class="py-3 px-3 text-center">จำนวน</th>
            <th class="py-3 px-3 text-right">ยอดชำระสุทธิ</th>
            <th class="py-3 px-3 text-center">สถานะ</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/80 font-mono">
          <?php if (!empty($recentOrders)): ?>
            <?php foreach ($recentOrders as $order): ?>
              <tr class="hover:bg-slate-900/60 transition-colors">
                <!-- Order Code -->
                <td class="py-3 px-3">
                  <span class="px-2 py-0.5 rounded bg-cyan-950 border border-cyan-500/30 text-cyan-400 font-bold text-[10px]">
                    <?= e($order['order_code']) ?>
                  </span>
                </td>

                <!-- Date & Time (Bangkok) -->
                <td class="py-3 px-3 text-slate-400 text-[11px]">
                  <?= date('d/m/Y H:i:s', strtotime($order['order_date'])) ?>
                </td>

                <!-- Customer -->
                <td class="py-3 px-3">
                  <div class="flex items-center gap-1.5">
                    <span class="font-bold text-white text-xs"><?= e($order['full_name']) ?></span>
                    <span class="text-[10px] text-slate-500">(@<?= e($order['username']) ?>)</span>
                  </div>
                </td>

                <!-- GPU Item -->
                <td class="py-3 px-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-950 p-1 border border-slate-800 shrink-0 flex items-center justify-center">
                      <img src="<?= e($order['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
                    </div>
                    <span class="text-white text-xs font-semibold truncate max-w-xs" title="<?= e($order['model_name']) ?>">
                      <?= e($order['model_name']) ?>
                    </span>
                  </div>
                </td>

                <!-- Quantity -->
                <td class="py-3 px-3 text-center font-bold text-white">
                  <?= $order['quantity'] ?>
                </td>

                <!-- Total Price -->
                <td class="py-3 px-3 text-right font-scifi font-bold text-emerald-400">
                  <?= formatPrice($order['total_price']) ?>
                </td>

                <!-- Status -->
                <td class="py-3 px-3 text-center">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center justify-center gap-1 w-max mx-auto">
                    <i class="fa-solid fa-circle-check text-[8px]"></i> สำเร็จ
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-8 text-slate-500">
                ยังไม่มีรายการสั่งซื้อ
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Chart.js CDN for Interactive Visualizations -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. GPU Model Sales Bar Chart
  const ctxGpu = document.getElementById('gpuSalesChart');
  if (ctxGpu) {
    new Chart(ctxGpu, {
      type: 'bar',
      data: {
        labels: <?= json_encode($gpuChartLabels) ?>,
        datasets: [{
          label: 'จำนวนที่ขายได้ (ชิ้น)',
          data: <?= json_encode($gpuChartUnits) ?>,
          backgroundColor: 'rgba(0, 240, 255, 0.65)',
          borderColor: '#00F0FF',
          borderWidth: 1.5,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(6, 9, 17, 0.95)',
            titleColor: '#00F0FF',
            borderColor: '#334155',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return ' ขายได้: ' + context.parsed.y + ' ชิ้น';
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#94a3b8',
              stepSize: 1
            },
            grid: {
              color: 'rgba(51, 65, 85, 0.4)'
            }
          },
          x: {
            ticks: {
              color: '#94a3b8',
              font: {
                size: 10
              }
            },
            grid: {
              display: false
            }
          }
        }
      }
    });
  }

  // 2. Chipset Share Doughnut Chart
  const ctxChipset = document.getElementById('chipsetSalesChart');
  if (ctxChipset) {
    new Chart(ctxChipset, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($chipsetChartLabels) ?>,
        datasets: [{
          data: <?= json_encode($chipsetChartUnits) ?>,
          backgroundColor: <?= json_encode($chipsetChartColors) ?>,
          borderColor: '#060911',
          borderWidth: 3,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#e2e8f0',
              font: {
                family: 'monospace',
                size: 11
              },
              padding: 14
            }
          },
          tooltip: {
            backgroundColor: 'rgba(6, 9, 17, 0.95)',
            borderColor: '#334155',
            borderWidth: 1,
            callbacks: {
              label: function(context) {
                return ' ' + context.label;
              }
            }
          }
        },
        cutout: '65%'
      }
    });
  }

});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
