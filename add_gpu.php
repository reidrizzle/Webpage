<?php
/**
 * Add New GPU Hardware Page
 * Real-time Image Preview & PDO Prepared Statement Insertion
 */
$pageTitle = "เพิ่มข้อมูลการ์ดจอใหม่ (Add New GPU)";
require_once __DIR__ . '/config/db.php';

// Check if user is logged in (optional check, or open for demo testing with notice)
$pdo = getDBConnection();
$error = null;

// Fetch brands and chipsets for dropdown selection
$brands = [];
$chipsets = [];
if ($pdo) {
    try {
        $brands = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();
        $chipsets = $pdo->query("SELECT id, manufacturer, series_name FROM chipsets ORDER BY manufacturer ASC")->fetchAll();
    } catch (PDOException $e) {
        $error = "เกิดข้อผิดพลาดในการโหลดข้อมูลตัวเลือก: " . $e->getMessage();
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model_name = trim($_POST['model_name'] ?? '');
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $chipset_id = (int)($_POST['chipset_id'] ?? 0);
    $chipset_model = trim($_POST['chipset_model'] ?? '');
    $series = trim($_POST['series'] ?? '');
    $vram_gb = (int)($_POST['vram_gb'] ?? 0);
    $memory_type = trim($_POST['memory_type'] ?? 'GDDR6X');
    $bus_width = (int)($_POST['bus_width'] ?? 0);
    $core_clock = (int)($_POST['core_clock'] ?? 0);
    $boost_clock = (int)($_POST['boost_clock'] ?? 0);
    $tdp_watts = (int)($_POST['tdp_watts'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $image_url = trim($_POST['image_url'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Determine status automatically if not selected
    $status = 'available';
    if ($stock <= 0) {
        $status = 'out_of_stock';
    } elseif ($stock <= 3) {
        $status = 'low_stock';
    }

    // Validation
    if (empty($model_name) || empty($brand_id) || empty($chipset_id) || $price <= 0) {
        $error = "กรุณากรอกข้อมูลสำคัญให้ครบถ้วน (ชื่อรุ่น, แบรนด์, ชิปเซ็ต, และราคา)";
    } elseif (!$pdo) {
        $error = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
    } else {
        try {
            $sql = "INSERT INTO gpus (
                        brand_id, chipset_id, model_name, chipset_model, series, 
                        vram_gb, memory_type, bus_width, core_clock, boost_clock, 
                        tdp_watts, price, stock, image_url, description, is_featured, status
                    ) VALUES (
                        :brand_id, :chipset_id, :model_name, :chipset_model, :series, 
                        :vram_gb, :memory_type, :bus_width, :core_clock, :boost_clock, 
                        :tdp_watts, :price, :stock, :image_url, :description, :is_featured, :status
                    )";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':brand_id'      => $brand_id,
                ':chipset_id'    => $chipset_id,
                ':model_name'    => $model_name,
                ':chipset_model' => $chipset_model,
                ':series'        => $series,
                ':vram_gb'       => $vram_gb,
                ':memory_type'   => $memory_type,
                ':bus_width'     => $bus_width,
                ':core_clock'    => $core_clock,
                ':boost_clock'   => $boost_clock,
                ':tdp_watts'     => $tdp_watts,
                ':price'         => $price,
                ':stock'         => $stock,
                ':image_url'     => $image_url ?: 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80',
                ':description'   => $description,
                ':is_featured'   => $is_featured,
                ':status'        => $status
            ]);

            header("Location: manage.php?msg=added");
            exit;
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb -->
  <div class="mb-6">
    <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-2">
      <a href="index.php" class="hover:underline">HOME</a>
      <span>/</span>
      <a href="manage.php" class="hover:underline">MANAGE</a>
      <span>/</span>
      <span>ADD NEW GPU</span>
    </div>
    <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
      CREATE NEW GPU ENTRY
    </h1>
    <p class="text-xs text-slate-400 mt-1">
      บันทึกข้อมูลการ์ดจอใหม่เข้าสู่ระบบคลัง พร้อมระบบแสดงตัวอย่างภาพแบบ Real-Time
    </p>
  </div>

  <?php if ($error): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3">
      <i class="fa-solid fa-triangle-exclamation text-lg text-rose-400"></i>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <form method="POST" action="add_gpu.php" class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <!-- Left Column: Form Inputs (8 cols) -->
      <div class="lg:col-span-8 glass-panel-elevated p-6 sm:p-8 rounded-2xl border border-slate-700/80 space-y-5">
        
        <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider pb-2 border-b border-slate-800">
          <i class="fa-solid fa-microchip mr-2"></i> ข้อมูลพื้นฐานของการ์ดจอ
        </h3>

        <!-- Model Name -->
        <div>
          <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            ชื่อรุ่นเต็ม (Full Model Name) *
          </label>
          <input type="text" name="model_name" required
                 placeholder="e.g. ASUS ROG Strix GeForce RTX 4090 OC 24GB"
                 class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Brand -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              แบรนด์ผู้ผลิต (AIB Brand) *
            </label>
            <select name="brand_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
              <option value="">-- เลือกแบรนด์ผู้ผลิต --</option>
              <?php foreach ($brands as $b): ?>
                <option value="<?= $b['id'] ?>"><?= e($b['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Chipset -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ค่ายชิปเซ็ตประมวลผล (Chipset) *
            </label>
            <select name="chipset_id" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
              <option value="">-- เลือกค่ายชิปเซ็ต --</option>
              <?php foreach ($chipsets as $c): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['manufacturer']) ?> - <?= e($c['series_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Chipset Model -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ชื่อชิปประมวลผล (Chipset Model) *
            </label>
            <input type="text" name="chipset_model" required
                   placeholder="e.g. GeForce RTX 4090 / RX 7900 XTX"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-cyan-400">
          </div>

          <!-- Series -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ซีรีส์สถาปัตยกรรม (Series)
            </label>
            <input type="text" name="series"
                   placeholder="e.g. RTX 40 Series / RX 7000 Series"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-cyan-400">
          </div>
        </div>

        <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider pt-4 pb-2 border-b border-slate-800">
          <i class="fa-solid fa-sliders mr-2"></i> สเปคฮาร์ดแวร์และประสิทธิภาพ
        </h3>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <!-- VRAM -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              VRAM (GB) *
            </label>
            <input type="number" name="vram_gb" required min="1" max="128" value="16"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
          </div>

          <!-- Memory Type -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ชนิดแรม (Type) *
            </label>
            <select name="memory_type" class="w-full px-3 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
              <option value="GDDR6X">GDDR6X</option>
              <option value="GDDR6">GDDR6</option>
              <option value="GDDR7">GDDR7</option>
              <option value="HBM2e">HBM2e</option>
            </select>
          </div>

          <!-- Bus Width -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              บัส (Bit) *
            </label>
            <input type="number" name="bus_width" required min="32" max="1024" value="256"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
          </div>

          <!-- TDP -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              พลังงาน (Watts)
            </label>
            <input type="number" name="tdp_watts" min="30" max="1000" value="285"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Core Clock -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ความเร็ว Base Clock (MHz)
            </label>
            <input type="number" name="core_clock" min="500" max="5000" value="2200"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
          </div>

          <!-- Boost Clock -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ความเร็ว Boost Clock (MHz)
            </label>
            <input type="number" name="boost_clock" min="500" max="5000" value="2600"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400 font-mono">
          </div>
        </div>

        <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider pt-4 pb-2 border-b border-slate-800">
          <i class="fa-solid fa-tag mr-2"></i> ราคาและจำนวนคลังสินค้า
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- Price -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ราคาจำหน่าย (บาท) *
            </label>
            <input type="number" name="price" required step="0.01" min="100" value="29900.00"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-emerald-400 text-sm font-bold focus:outline-none focus:border-cyan-400 font-mono">
          </div>

          <!-- Stock -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              จำนวนสินค้าในสต็อก (Units) *
            </label>
            <input type="number" name="stock" required min="0" value="5"
                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 font-mono">
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            รายละเอียดจุดเด่นสินค้า (Description)
          </label>
          <textarea name="description" rows="3" placeholder="ระบุเทคโนโลยีระบบระบายความร้อน ฟังก์ชันเร่งความเร็ว และจุดเด่น..."
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-cyan-400"></textarea>
        </div>

        <!-- Featured Checkbox -->
        <div class="flex items-center gap-2 pt-2">
          <input type="checkbox" id="is_featured" name="is_featured" value="1"
                 class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-cyan-500 focus:ring-cyan-400">
          <label for="is_featured" class="text-xs text-slate-300 select-none">
            ตั้งเป็นสินค้าแนะนำบนหน้าแรก (Featured Flagship)
          </label>
        </div>

      </div>

      <!-- Right Column: Real-time Image Preview & Presets (4 cols) -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="glass-panel-elevated p-6 rounded-2xl border border-slate-700/80">
          <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider mb-3">
            <i class="fa-solid fa-image mr-1.5"></i> Real-time Image Preview
          </h3>
          
          <!-- Image Box Preview -->
          <div class="gpu-img-container rounded-xl border border-slate-700/80 p-4 mb-4 bg-slate-950/90 h-64">
            <img id="imagePreview" 
                 src="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80" 
                 alt="Preview" 
                 class="max-h-full max-w-full object-contain transition-all">
          </div>

          <!-- Image URL Input -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              URL รูปภาพการ์ดจอ *
            </label>
            <input type="url" id="image_url" name="image_url" required
                   value="https://images.unsplash.com/photo-1587202372775-e229f172b9d7?auto=format&fit=crop&w=800&q=80"
                   placeholder="https://..."
                   class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
          </div>

          <!-- Sample Quick Preset Buttons -->
          <div class="mt-4 pt-4 border-t border-slate-800">
            <span class="text-[10px] uppercase font-mono text-slate-500 block mb-2">หรือเลือกรูปตัวอย่างด่วน:</span>
            <div class="grid grid-cols-3 gap-1.5">
              <button type="button" onclick="setSampleImage('https://images.unsplash.com/photo-1591488320449-011701bb6704?auto=format&fit=crop&w=800&q=80')" 
                      class="px-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-emerald-400 hover:bg-slate-800 font-scifi">
                NVIDIA 40-Series
              </button>
              <button type="button" onclick="setSampleImage('https://images.unsplash.com/photo-1526738549149-8e07eca6c147?auto=format&fit=crop&w=800&q=80')" 
                      class="px-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-rose-400 hover:bg-slate-800 font-scifi">
                AMD Radeon
              </button>
              <button type="button" onclick="setSampleImage('https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80')" 
                      class="px-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[10px] text-cyan-400 hover:bg-slate-800 font-scifi">
                Intel Arc
              </button>
            </div>
          </div>
        </div>

        <!-- Submit Controls Card -->
        <div class="glass-panel-elevated p-6 rounded-2xl border border-slate-700/80 space-y-3">
          <button type="submit" class="w-full btn-cyber-primary py-3.5 rounded-xl text-sm font-bold tracking-wider flex items-center justify-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลการ์ดจอ
          </button>
          
          <a href="manage.php" class="w-full block text-center py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-white text-xs font-mono transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> ยกเลิกและกลับสู่หน้ารายการ
          </a>
        </div>

      </div>

    </div>
  </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  initImagePreview('image_url', 'imagePreview');
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
