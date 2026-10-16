<?php
/**
 * Inventory Management & Administration Table
 * Full CRUD control center with SweetAlert2 Delete Confirmations
 */
require_once __DIR__ . '/config/db.php';

// Only Admin can access inventory management
requireAdmin();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();
$gpus = [];
$stats = [
    'total_models' => 0,
    'total_stock'  => 0,
    'total_value'  => 0,
    'low_stock'    => 0
];

if ($pdo) {
    try {
        // Fetch stats
        $statRow = $pdo->query("SELECT 
                                    COUNT(*) as total_models, 
                                    COALESCE(SUM(stock), 0) as total_stock, 
                                    COALESCE(SUM(stock * price), 0) as total_value,
                                    COALESCE(SUM(CASE WHEN stock <= 3 THEN 1 ELSE 0 END), 0) as low_stock
                                FROM gpus")->fetch();
        if ($statRow) {
            $stats = $statRow;
        }

        // Fetch list of GPUs with Brand and Chipset info
        $sql = "SELECT g.*, b.name AS brand_name, c.manufacturer, c.series_name, c.badge_color 
                FROM gpus g
                JOIN brands b ON g.brand_id = b.id
                JOIN chipsets c ON g.chipset_id = c.id
                ORDER BY g.id DESC";
        $gpus = $pdo->query($sql)->fetchAll();
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
}
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Header Title & Action Buttons -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span>INVENTORY CONTROLLER</span>
      </div>
      <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
        STOCK & HARDWARE MANAGEMENT
      </h1>
      <p class="text-xs text-slate-400 mt-1">
        ตารางศูนย์ควบคุมการ์ดจอ เพิ่ม แก้ไข ปรับปรุงข้อมูล และระบบลบข้อมูลผ่าน SweetAlert2
      </p>
    </div>

    <div class="flex items-center gap-3">
      <a href="reports.php" class="px-4 py-2.5 rounded-xl bg-purple-950/60 border border-purple-500/40 text-purple-300 hover:text-white hover:bg-purple-900/60 transition-colors text-xs font-scifi flex items-center gap-1.5">
        <i class="fa-solid fa-chart-pie"></i> รายงานสถิติ
      </a>
      <a href="add_gpu.php" class="btn-cyber-primary px-5 py-2.5 rounded-xl text-xs font-bold tracking-wider flex items-center gap-2">
        <i class="fa-solid fa-plus-circle"></i> เพิ่มการ์ดจอใหม่
      </a>
    </div>
  </div>

  <!-- Metric Overview Cards -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="glass-panel p-4 rounded-xl border border-slate-800">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-mono text-slate-400 uppercase">รุ่นการ์ดจอในระบบ</span>
        <i class="fa-solid fa-microchip text-cyan-400 text-sm"></i>
      </div>
      <div class="text-2xl font-black font-scifi text-white mt-2">
        <?= number_format($stats['total_models']) ?> <span class="text-xs font-normal text-slate-400">รุ่น</span>
      </div>
    </div>

    <div class="glass-panel p-4 rounded-xl border border-slate-800">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-mono text-slate-400 uppercase">จำนวนของคงคลัง</span>
        <i class="fa-solid fa-boxes-stacked text-emerald-400 text-sm"></i>
      </div>
      <div class="text-2xl font-black font-scifi text-emerald-400 mt-2">
        <?= number_format($stats['total_stock']) ?> <span class="text-xs font-normal text-slate-400">ชิ้น</span>
      </div>
    </div>

    <div class="glass-panel p-4 rounded-xl border border-slate-800">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-mono text-slate-400 uppercase">มูลค่ารวมคลังสินค้า</span>
        <i class="fa-solid fa-coins text-amber-400 text-sm"></i>
      </div>
      <div class="text-2xl font-black font-scifi text-amber-400 mt-2">
        <?= formatPrice($stats['total_value']) ?>
      </div>
    </div>

    <div class="glass-panel p-4 rounded-xl border border-slate-800">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-mono text-slate-400 uppercase">สินค้าสต็อกต่ำ/หมด</span>
        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-sm"></i>
      </div>
      <div class="text-2xl font-black font-scifi text-rose-400 mt-2">
        <?= number_format($stats['low_stock']) ?> <span class="text-xs font-normal text-slate-400">รายการ</span>
      </div>
    </div>
  </div>

  <!-- Management Data Table -->
  <div class="glass-panel-elevated rounded-2xl border border-slate-800 overflow-hidden shadow-2xl">
    <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="text-xs font-scifi text-slate-300">
        <i class="fa-solid fa-table-list mr-1.5 text-cyan-400"></i> รายการสินค้าทั้งหมดในระบบ (<?= count($gpus) ?> รายการ)
      </div>
      <div class="relative w-full sm:w-64">
        <input type="text" id="tableFilter" onkeyup="filterTableRows()" 
               placeholder="พิมพ์เพื่อค้นหาในตาราง..." 
               class="w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-cyan-400">
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse" id="gpuDataTable">
        <thead>
          <tr class="border-b border-slate-800 bg-slate-950/90 text-[11px] font-mono uppercase text-slate-400 tracking-wider">
            <th class="py-3.5 px-4">ภาพ / ค่าย</th>
            <th class="py-3.5 px-4">ชื่อรุ่นและแบรนด์</th>
            <th class="py-3.5 px-4">สเปคหลัก (VRAM/Bus)</th>
            <th class="py-3.5 px-4 text-right">ราคาต่อหน่วย</th>
            <th class="py-3.5 px-4 text-center">จำนวนสต็อก</th>
            <th class="py-3.5 px-4 text-center">จัดการ (Actions)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60 text-xs">
          <?php if (!empty($gpus)): ?>
            <?php foreach ($gpus as $gpu): 
              $badgeClass = 'badge-' . strtolower($gpu['manufacturer']);
            ?>
            <tr class="hover:bg-slate-800/40 transition-colors">
              
              <!-- Thumbnail & Chipset -->
              <td class="py-3 px-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center p-1 overflow-hidden">
                    <img src="<?= e($gpu['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
                  </div>
                  <div>
                    <span class="inline-block px-2 py-0.5 rounded text-[9px] font-scifi font-bold <?= $badgeClass ?>">
                      <?= e($gpu['manufacturer']) ?>
                    </span>
                    <span class="block text-[10px] text-slate-500 font-mono mt-0.5">ID: #<?= $gpu['id'] ?></span>
                  </div>
                </div>
              </td>

              <!-- Model Name & Brand -->
              <td class="py-3 px-4 max-w-xs">
                <div class="font-bold text-white hover:text-cyan-400 transition-colors">
                  <?= e($gpu['model_name']) ?>
                </div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                  <?= e($gpu['brand_name']) ?> • <?= e($gpu['chipset_model']) ?>
                </div>
              </td>

              <!-- Specs -->
              <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px]">
                <div class="text-cyan-300 font-semibold"><?= e($gpu['vram_gb']) ?>GB <?= e($gpu['memory_type']) ?></div>
                <div class="text-slate-400 text-[10px]"><?= e($gpu['bus_width']) ?>-bit • <?= e($gpu['tdp_watts']) ?>W</div>
              </td>

              <!-- Price -->
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <span class="font-scifi font-bold text-emerald-400 text-sm">
                  <?= formatPrice($gpu['price']) ?>
                </span>
              </td>

              <!-- Stock Status -->
              <td class="py-3 px-4 text-center whitespace-nowrap">
                <?php if ($gpu['stock'] > 5): ?>
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-950/60 text-emerald-400 border border-emerald-500/40">
                    <?= $gpu['stock'] ?> ชิ้น (ปกติ)
                  </span>
                <?php elseif ($gpu['stock'] > 0): ?>
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-amber-950/60 text-amber-400 border border-amber-500/40">
                    <?= $gpu['stock'] ?> ชิ้น (ใกล้หมด)
                  </span>
                <?php else: ?>
                  <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-rose-950/60 text-rose-400 border border-rose-500/40">
                    0 ชิ้น (หมด)
                  </span>
                <?php endif; ?>
              </td>

              <!-- Actions: Edit & Delete -->
              <td class="py-3 px-4 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Quick View -->
                  <button type="button" onclick='openQuickView(<?= json_encode($gpu, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                          class="p-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 hover:text-cyan-400 transition-colors" title="ดูตัวอย่าง">
                    <i class="fa-solid fa-eye text-xs"></i>
                  </button>

                  <!-- Edit -->
                  <a href="edit_gpu.php?id=<?= $gpu['id'] ?>" 
                     class="p-2 rounded-lg bg-cyan-950/60 hover:bg-cyan-900 border border-cyan-500/40 text-cyan-300 transition-colors" title="แก้ไขข้อมูล">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                  </a>

                  <!-- Delete with SweetAlert2 -->
                  <button type="button" onclick="confirmDelete(<?= $gpu['id'] ?>, '<?= addslashes(e($gpu['model_name'])) ?>')" 
                          class="p-2 rounded-lg bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-400 transition-colors" title="ลบข้อมูล">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </button>
                </div>
              </td>

            </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-12 text-slate-500 font-mono">
                ไม่พบข้อมูลการ์ดจอในตารางคลังสินค้า
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
function filterTableRows() {
  const query = document.getElementById('tableFilter').value.toLowerCase();
  const rows = document.querySelectorAll('#gpuDataTable tbody tr');
  
  rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    row.style.display = text.includes(query) ? '' : 'none';
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
