<?php
/**
 * Executive Analytics & Inventory Reports
 * Report 1: Valuation & Volume Grouped by Chipset & Brand (GROUP BY, COUNT, SUM, AVG) + Chart.js
 * Report 2: Low Stock Alert & Highest Value Flagships + Print-Ready View (window.print())
 */
$pageTitle = "หน้ารายงานสรุปคลังสินค้าและสถิติ (Executive Reports)";
require_once __DIR__ . '/config/db.php';
requireAdmin();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();

// Initialize data containers
$chipsetReport = [];
$brandReport = [];
$lowStockReport = [];
$topValueReport = [];

$chartLabels = [];
$chartValues = [];
$chartColors = [];

$overallTotals = [
    'models' => 0,
    'units' => 0,
    'value' => 0,
    'avg_price' => 0
];

if ($pdo) {
    try {
        // ====================================================================
        // REPORT 1: Group By Chipset Manufacturer (COUNT, SUM, AVG)
        // ====================================================================
        $sqlChipset = "SELECT 
                          c.manufacturer,
                          c.series_name,
                          c.badge_color,
                          COUNT(g.id) AS total_models,
                          COALESCE(SUM(g.stock), 0) AS total_units,
                          COALESCE(SUM(g.stock * g.price), 0) AS total_value,
                          COALESCE(AVG(g.price), 0) AS avg_price
                       FROM chipsets c
                       LEFT JOIN gpus g ON c.id = g.chipset_id
                       GROUP BY c.id, c.manufacturer, c.series_name, c.badge_color
                       ORDER BY total_value DESC";
        $chipsetReport = $pdo->query($sqlChipset)->fetchAll();

        // Prepare data for Chart.js
        foreach ($chipsetReport as $row) {
            $chartLabels[] = $row['manufacturer'] . ' (' . $row['series_name'] . ')';
            $chartValues[] = (float)$row['total_value'];
            $chartColors[] = $row['badge_color'] ?: '#00FF66';
            
            $overallTotals['models'] += $row['total_models'];
            $overallTotals['units'] += $row['total_units'];
            $overallTotals['value'] += $row['total_value'];
        }
        if ($overallTotals['models'] > 0) {
            $overallTotals['avg_price'] = $overallTotals['value'] / max(1, $overallTotals['units']);
        }

        // REPORT 1 (B): Group By Brand (AIB Partners)
        $sqlBrand = "SELECT 
                        b.name AS brand_name,
                        b.country,
                        COUNT(g.id) AS total_models,
                        COALESCE(SUM(g.stock), 0) AS total_units,
                        COALESCE(SUM(g.stock * g.price), 0) AS total_value,
                        COALESCE(AVG(g.price), 0) AS avg_price
                     FROM brands b
                     LEFT JOIN gpus g ON b.id = g.brand_id
                     GROUP BY b.id, b.name, b.country
                     HAVING total_models > 0
                     ORDER BY total_value DESC";
        $brandReport = $pdo->query($sqlBrand)->fetchAll();

        // ====================================================================
        // REPORT 2: Low Stock Risk Alert (stock <= 3)
        // ====================================================================
        $sqlLowStock = "SELECT 
                            g.*, 
                            b.name AS brand_name, 
                            c.manufacturer, 
                            c.badge_color
                        FROM gpus g
                        JOIN brands b ON g.brand_id = b.id
                        JOIN chipsets c ON g.chipset_id = c.id
                        WHERE g.stock <= 3
                        ORDER BY g.stock ASC, g.price DESC";
        $lowStockReport = $pdo->query($sqlLowStock)->fetchAll();

        // REPORT 2 (B): Top 5 Highest Value Flagship GPUs
        $sqlTopValue = "SELECT 
                            g.*, 
                            b.name AS brand_name, 
                            c.manufacturer,
                            (g.stock * g.price) AS total_item_worth
                        FROM gpus g
                        JOIN brands b ON g.brand_id = b.id
                        JOIN chipsets c ON g.chipset_id = c.id
                        ORDER BY g.price DESC
                        LIMIT 5";
        $topValueReport = $pdo->query($sqlTopValue)->fetchAll();

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
        <h1 class="text-2xl font-bold font-serif uppercase tracking-wider">รายงานสถานะคลังสินค้าและการเงินฮาร์ดแวร์ (Official Report)</h1>
        <p class="text-sm">CYBER-GPU STORE - Next-Gen Gaming & AI Compute Hardware</p>
      </div>
      <div class="text-right text-xs">
        <p><strong>วันที่ออกรายงาน:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>ผู้จัดทำ:</strong> Neo Cyber Admin (System Generated)</p>
      </div>
    </div>
  </div>

  <!-- Screen Header Title & Print Action -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 no-print">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span>EXECUTIVE ANALYTICS</span>
      </div>
      <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
        HARDWARE INVENTORY INTELLIGENCE
      </h1>
      <p class="text-xs text-slate-400 mt-1">
        รายงานวิเคราะห์เชิงปริมาณ สรุปมูลค่าคลังสินค้า (Aggregation Queries) และตรวจสอบสต็อกวิกฤต
      </p>
    </div>

    <!-- Quick Buttons -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="sales_report.php" class="px-4 py-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 hover:text-white hover:bg-emerald-900/60 transition-colors text-xs font-scifi font-bold flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-chart-line"></i> รายงานยอดขาย & สินค้าขายดี
      </a>

      <button onclick="window.print()" class="btn-cyber-primary px-5 py-2.5 rounded-xl text-xs font-bold tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-print text-sm"></i> พิมพ์รายงาน (Print / PDF)
      </button>
    </div>
  </div>

  <!-- Dual Report Mode Tabs (รายงานยอดขาย vs รายงานสรุปคลัง) -->
  <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 mb-8 max-w-xl shadow-xl print:hidden">
    <a href="sales_report.php" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-scifi font-medium text-center text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all flex items-center justify-center gap-2">
      <i class="fa-solid fa-chart-line text-emerald-400"></i>
      <span>รายงานยอดขาย & สินค้าขายดี</span>
    </a>
    <a href="reports.php" class="flex-1 py-2.5 px-4 rounded-xl text-xs font-scifi font-bold text-center bg-gradient-to-r from-cyan-500/20 to-purple-500/20 text-cyan-300 border border-cyan-500/40 shadow-sm flex items-center justify-center gap-2">
      <i class="fa-solid fa-chart-pie text-cyan-400"></i>
      <span>รายงานสรุปคลัง & มูลค่าสินค้า</span>
    </a>
  </div>

  <!-- Summary KPI Highlights -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
      <span class="text-[11px] font-mono uppercase text-slate-400 block">จำนวนรุ่นทั้งหมด</span>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-cyan-400 mt-1">
        <?= number_format($overallTotals['models']) ?> <span class="text-xs font-normal text-slate-400">Models</span>
      </div>
    </div>

    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
      <span class="text-[11px] font-mono uppercase text-slate-400 block">จำนวนการ์ดจอในคลัง</span>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-emerald-400 mt-1">
        <?= number_format($overallTotals['units']) ?> <span class="text-xs font-normal text-slate-400">Units</span>
      </div>
    </div>

    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
      <span class="text-[11px] font-mono uppercase text-slate-400 block">มูลค่าคลังสินค้ารวม</span>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-amber-400 mt-1">
        <?= formatPrice($overallTotals['value']) ?>
      </div>
    </div>

    <div class="glass-panel p-5 rounded-2xl border border-slate-800">
      <span class="text-[11px] font-mono uppercase text-slate-400 block">สินค้าเสี่ยงใกล้หมด (Alert)</span>
      <div class="text-2xl sm:text-3xl font-black font-scifi text-rose-400 mt-1">
        <?= count($lowStockReport) ?> <span class="text-xs font-normal text-slate-400">Items</span>
      </div>
    </div>
  </div>

  <!-- ====================================================================
       REPORT 1: INVENTORY VALUATION BY CHIPSET & BRAND + CHART.JS
       ==================================================================== -->
  <div class="mb-14">
    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-800">
      <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 shadow-[0_0_8px_#00F0FF]"></span>
      <h2 class="text-xl font-black font-scifi text-white">
        รายงานที่ 1: สรุปจำนวนและมูลค่าคลังสินค้าแยกตามค่ายชิปเซ็ตและแบรนด์
      </h2>
    </div>

    <!-- Chart + Chipset Table Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
      
      <!-- Chart.js Visualization (5 cols) -->
      <div class="lg:col-span-5 glass-panel-elevated p-6 rounded-2xl border border-slate-800 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-scifi font-bold text-xs uppercase text-cyan-400 tracking-wider">
              <i class="fa-solid fa-chart-pie mr-1.5"></i> สัดส่วนมูลค่าคลังตามค่ายชิปเซ็ต
            </h3>
            <span class="text-[10px] font-mono text-slate-400">Chart.js Engine</span>
          </div>
          <div class="relative h-64 flex items-center justify-center">
            <canvas id="chipsetValueChart"></canvas>
          </div>
        </div>
        <p class="text-[11px] text-slate-400 font-mono text-center mt-4">
          *คำนวณจากสูตร SQL: <code class="text-cyan-300">SUM(stock * price) GROUP BY manufacturer</code>
        </p>
      </div>

      <!-- Chipset Aggregated Table (7 cols) -->
      <div class="lg:col-span-7 glass-panel-elevated rounded-2xl border border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-800 bg-slate-900/60 flex items-center justify-between">
          <span class="text-xs font-scifi font-bold text-slate-300">
            <i class="fa-solid fa-microchip mr-1.5 text-cyan-400"></i> ตารางสรุปแยกตามค่ายชิปเซ็ต (GROUP BY chipsets)
          </span>
          <span class="text-[10px] font-mono text-slate-400">COUNT / SUM / AVG</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-950/80 text-[10px] font-mono uppercase text-slate-400">
                <th class="py-3 px-4">ค่ายชิปเซ็ต</th>
                <th class="py-3 px-3 text-center">รุ่น (COUNT)</th>
                <th class="py-3 px-3 text-center">สต็อก (SUM)</th>
                <th class="py-3 px-3 text-right">ราคาเฉลี่ย (AVG)</th>
                <th class="py-3 px-4 text-right">มูลค่ารวม (SUM)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
              <?php foreach ($chipsetReport as $c): ?>
              <tr class="hover:bg-slate-800/30">
                <td class="py-3 px-4 font-bold text-white flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full" style="background-color: <?= e($c['badge_color']) ?>;"></span>
                  <?= e($c['manufacturer']) ?>
                  <span class="text-[10px] font-normal text-slate-400 font-mono">(<?= e($c['series_name']) ?>)</span>
                </td>
                <td class="py-3 px-3 text-center font-mono text-cyan-400 font-bold"><?= $c['total_models'] ?></td>
                <td class="py-3 px-3 text-center font-mono text-emerald-400 font-bold"><?= $c['total_units'] ?></td>
                <td class="py-3 px-3 text-right font-mono text-slate-300"><?= formatPrice($c['avg_price']) ?></td>
                <td class="py-3 px-4 text-right font-mono font-bold text-amber-400"><?= formatPrice($c['total_value']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr class="border-t-2 border-slate-700 bg-slate-950 font-bold text-xs text-white font-mono">
                <td class="py-3 px-4">รวมทั้งสิ้น (TOTAL)</td>
                <td class="py-3 px-3 text-center text-cyan-400"><?= $overallTotals['models'] ?></td>
                <td class="py-3 px-3 text-center text-emerald-400"><?= $overallTotals['units'] ?></td>
                <td class="py-3 px-3 text-right text-slate-400">-</td>
                <td class="py-3 px-4 text-right text-amber-400"><?= formatPrice($overallTotals['value']) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

    </div>

    <!-- Brand (AIB Partners) Aggregated Table -->
    <div class="glass-panel-elevated rounded-2xl border border-slate-800 overflow-hidden">
      <div class="p-4 border-b border-slate-800 bg-slate-900/60 flex items-center justify-between">
        <span class="text-xs font-scifi font-bold text-slate-300">
          <i class="fa-solid fa-building mr-1.5 text-purple-400"></i> สรุปข้อมูลแยกตามแบรนด์ผู้ผลิตการ์ด (GROUP BY brands)
        </span>
        <span class="text-[10px] font-mono text-slate-400">AIB Performance</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-800 bg-slate-950/80 text-[10px] font-mono uppercase text-slate-400">
              <th class="py-3 px-4">แบรนด์ (Brand)</th>
              <th class="py-3 px-4">ประเทศ</th>
              <th class="py-3 px-4 text-center">จำนวนรุ่น (COUNT)</th>
              <th class="py-3 px-4 text-center">สต็อกในคลัง (SUM)</th>
              <th class="py-3 px-4 text-right">ราคาเฉลี่ยต่อรุ่น (AVG)</th>
              <th class="py-3 px-4 text-right">มูลค่ารวมคลัง (SUM)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            <?php foreach ($brandReport as $b): ?>
            <tr class="hover:bg-slate-800/30">
              <td class="py-3 px-4 font-bold text-white"><?= e($b['brand_name']) ?></td>
              <td class="py-3 px-4 text-slate-400 font-mono text-[11px]"><?= e($b['country']) ?></td>
              <td class="py-3 px-4 text-center font-mono font-bold text-cyan-400"><?= $b['total_models'] ?></td>
              <td class="py-3 px-4 text-center font-mono font-bold text-emerald-400"><?= $b['total_units'] ?></td>
              <td class="py-3 px-4 text-right font-mono text-slate-300"><?= formatPrice($b['avg_price']) ?></td>
              <td class="py-3 px-4 text-right font-mono font-bold text-amber-400"><?= formatPrice($b['total_value']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ====================================================================
       REPORT 2: LOW STOCK ALERTS & TOP HIGHEST VALUATION GPUS
       ==================================================================== -->
  <div class="mb-14">
    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-800">
      <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_8px_#FF0055]"></span>
      <h2 class="text-xl font-black font-scifi text-white">
        รายงานที่ 2: ตรวจสอบการ์ดจอใกล้หมดสต็อก (Low Stock) และการ์ดจอมูลค่าสูงสุด (Top Flagships)
      </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Part A: Low Stock Alert Table (7 cols) -->
      <div class="lg:col-span-7 glass-panel-elevated rounded-2xl border border-rose-500/30 overflow-hidden shadow-xl shadow-rose-950/20">
        <div class="p-4 border-b border-rose-500/30 bg-rose-950/30 flex items-center justify-between">
          <span class="text-xs font-scifi font-bold text-rose-300 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation text-rose-400 animate-pulse"></i>
            รายการสินค้าใกล้หมดสต็อก (Low Stock Warning: สต็อก <= 3)
          </span>
          <span class="text-[10px] font-mono text-rose-300 bg-rose-900/60 px-2 py-0.5 rounded">
            ต้องสั่งซื้อเพิ่มด่วน
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-800 bg-slate-950/80 text-[10px] font-mono uppercase text-slate-400">
                <th class="py-3 px-4">ชื่อรุ่นการ์ดจอ</th>
                <th class="py-3 px-3">ค่าย / แบรนด์</th>
                <th class="py-3 px-3 text-right">ราคา</th>
                <th class="py-3 px-3 text-center">คงเหลือ</th>
                <th class="py-3 px-4 text-center">ระดับความเร่งด่วน</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
              <?php if (!empty($lowStockReport)): ?>
                <?php foreach ($lowStockReport as $item): ?>
                <tr class="hover:bg-slate-800/30">
                  <td class="py-3 px-4 font-bold text-white max-w-xs truncate" title="<?= e($item['model_name']) ?>">
                    <?= e($item['model_name']) ?>
                  </td>
                  <td class="py-3 px-3 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                    <?= e($item['brand_name']) ?>
                  </td>
                  <td class="py-3 px-3 text-right font-mono text-slate-300 whitespace-nowrap">
                    <?= formatPrice($item['price']) ?>
                  </td>
                  <td class="py-3 px-3 text-center font-mono font-bold whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded text-[11px] <?= $item['stock'] == 0 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-amber-500/20 text-amber-400 border border-amber-500/40' ?>">
                      <?= $item['stock'] ?> ชิ้น
                    </span>
                  </td>
                  <td class="py-3 px-4 text-center whitespace-nowrap">
                    <?php if ($item['stock'] == 0): ?>
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-rose-600 text-white font-bold animate-pulse">
                        CRITICAL (หมด)
                      </span>
                    <?php else: ?>
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-600/30 text-amber-300 border border-amber-500/40">
                        RESTOCK SOON
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="py-8 text-center text-slate-400 font-mono">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg mb-1 block"></i>
                    สต็อกสินค้าทุกรายการอยู่ในระดับปลอดภัย
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Part B: Top 5 Highest Valuation GPUs (5 cols) -->
      <div class="lg:col-span-5 glass-panel-elevated rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 bg-slate-900/60 flex items-center justify-between">
          <span class="text-xs font-scifi font-bold text-amber-400 flex items-center gap-2">
            <i class="fa-solid fa-crown text-amber-400"></i>
            อันดับการ์ดจอเรือธงที่มีมูลค่าสูงสุด (Top 5 Flagships)
          </span>
          <span class="text-[10px] font-mono text-slate-400">Ranked by Price</span>
        </div>

        <div class="p-4 space-y-3">
          <?php $rank = 1; foreach ($topValueReport as $flagship): ?>
            <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 font-scifi font-bold text-xs flex items-center justify-center">
                  #<?= $rank++ ?>
                </span>
                <div>
                  <h4 class="font-bold text-white text-xs line-clamp-1" title="<?= e($flagship['model_name']) ?>">
                    <?= e($flagship['model_name']) ?>
                  </h4>
                  <span class="text-[10px] text-slate-400 font-mono">
                    <?= e($flagship['vram_gb']) ?>GB <?= e($flagship['memory_type']) ?> • สต็อก <?= $flagship['stock'] ?> ชิ้น
                  </span>
                </div>
              </div>

              <div class="text-right whitespace-nowrap">
                <div class="font-scifi font-black text-emerald-400 text-xs">
                  <?= formatPrice($flagship['price']) ?>
                </div>
                <div class="text-[9px] text-slate-500 font-mono">
                  มูลค่ารวม <?= formatPrice($flagship['total_item_worth']) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>

    </div>
  </div>

  <!-- Report Sign-off section for print -->
  <div class="hidden print:grid grid-cols-2 gap-12 mt-16 pt-8 border-t border-black text-black text-xs">
    <div>
      <p class="font-bold mb-8">ลงชื่อผู้จัดทำรายงาน (Prepared By):</p>
      <p>____________________________________</p>
      <p class="mt-1">( .............................................................. )</p>
      <p>ตำแหน่ง: เจ้าหน้าที่บริหารคลังสินค้า</p>
    </div>
    <div class="text-right">
      <p class="font-bold mb-8">ลงชื่อผู้อนุมัติ / อาจารย์ผู้ตรวจประเมิน:</p>
      <p>____________________________________</p>
      <p class="mt-1">( .............................................................. )</p>
      <p>วันที่ตรวจประเมิน: ____ / ____ / ________</p>
    </div>
  </div>

</div>

<!-- Chart.js Script Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('chipsetValueChart');
  if (ctx) {
    const labels = <?= json_encode($chartLabels) ?>;
    const dataValues = <?= json_encode($chartValues) ?>;
    const bgColors = <?= json_encode($chartColors) ?>;

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: dataValues,
          backgroundColor: bgColors,
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
              color: '#cbd5e1',
              font: {
                family: 'Prompt',
                size: 11
              },
              padding: 12
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const val = context.raw || 0;
                return ' ' + context.label + ': ฿' + Number(val).toLocaleString();
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
