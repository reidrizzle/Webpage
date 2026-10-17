<?php
/**
 * Global Footer Component
 * Cyber-Tech Aesthetic Footer & Modal Containers
 */
?>
<footer class="mt-auto border-t border-slate-800/80 bg-slate-950/90 backdrop-blur-md pt-12 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
      
      <!-- Brand & Mission -->
      <div class="space-y-4 md:col-span-1">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-cyan-400 to-emerald-400 flex items-center justify-center text-slate-950">
            <i class="fa-solid fa-bolt-lightning text-sm"></i>
          </div>
          <span class="font-scifi font-bold text-lg text-white tracking-wider">CYBER<span class="text-cyan-400">GPU</span></span>
        </div>
        <p class="text-xs text-slate-400 leading-relaxed">
          แพลตฟอร์มคลังสินค้าและจัดจำหน่ายการ์ดจอเกมมิ่งระดับ Extreme Architecture รองรับ Ray Tracing, DLSS 3.5, FSR 3 และ AI Computing ครบวงจร
        </p>
        <div class="flex items-center gap-3 text-slate-400">
          <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-cyan-400 hover:border-cyan-500/40 transition-colors">
            <i class="fa-brands fa-github text-sm"></i>
          </a>
          <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-cyan-400 hover:border-cyan-500/40 transition-colors">
            <i class="fa-brands fa-discord text-sm"></i>
          </a>
          <a href="#" class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-cyan-400 hover:border-cyan-500/40 transition-colors">
            <i class="fa-brands fa-facebook text-sm"></i>
          </a>
        </div>
      </div>

      <!-- Quick Nav Links -->
      <div>
        <h4 class="font-scifi text-xs uppercase tracking-widest text-cyan-400 font-bold mb-4">ระบบนำทาง</h4>
        <ul class="space-y-2 text-xs text-slate-400 font-medium">
          <li><a href="index.php" class="hover:text-cyan-300 transition-colors">หน้าร้านหลัก (Catalog)</a></li>
          <li><a href="search.php" class="hover:text-cyan-300 transition-colors">ค้นหา & กรองสเปคการ์ดจอ</a></li>
          <li><a href="manage.php" class="hover:text-cyan-300 transition-colors">ระบบจัดการคลังสต็อกสินค้า</a></li>
          <li><a href="add_gpu.php" class="hover:text-cyan-300 transition-colors">เพิ่มข้อมูลการ์ดจอรุ่นใหม่</a></li>
          <li><a href="reports.php" class="hover:text-cyan-300 transition-colors">หน้ารายงานสรุปยอดและกราฟ</a></li>
        </ul>
      </div>

      <!-- Hardware Chipset Brands -->
      <div>
        <h4 class="font-scifi text-xs uppercase tracking-widest text-cyan-400 font-bold mb-4">ค่ายชิปเซ็ตประมวลผล</h4>
        <div class="space-y-2.5 text-xs text-slate-400">
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#00FF66] shadow-[0_0_8px_#00FF66]"></span>
            <span class="font-scifi font-bold text-slate-300">NVIDIA GEFORCE</span>
            <span class="text-[10px] text-slate-500 font-mono">Ada Lovelace</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#FF0055] shadow-[0_0_8px_#FF0055]"></span>
            <span class="font-scifi font-bold text-slate-300">AMD RADEON</span>
            <span class="text-[10px] text-slate-500 font-mono">RDNA 3 Chiplet</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#00F0FF] shadow-[0_0_8px_#00F0FF]"></span>
            <span class="font-scifi font-bold text-slate-300">INTEL ARC</span>
            <span class="text-[10px] text-slate-500 font-mono">Alchemist & Battlemage</span>
          </div>
        </div>
      </div>

      <!-- Server & Tech Stack Info -->
      <div>
        <h4 class="font-scifi text-xs uppercase tracking-widest text-cyan-400 font-bold mb-4">ข้อมูลระบบสถาปัตยกรรม</h4>
        <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-3 space-y-2 text-[11px] font-mono">
          <div class="flex justify-between items-center text-slate-400">
            <span>DATABASE:</span>
            <span class="text-emerald-400 font-bold flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              MySQL 8.0 (PDO)
            </span>
          </div>
          <div class="flex justify-between items-center text-slate-400">
            <span>ENCRYPTION:</span>
            <span class="text-cyan-400">BCRYPT 10 rounds</span>
          </div>
          <div class="flex justify-between items-center text-slate-400">
            <span>CHARSET:</span>
            <span class="text-slate-300">utf8mb4_unicode_ci</span>
          </div>
          <div class="flex justify-between items-center text-slate-400">
            <span>INTERFACE:</span>
            <span class="text-purple-400">Glassmorphism UI</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Bottom Copyright -->
    <div class="pt-6 border-t border-slate-800/60 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
      <p>© <?= date('Y') ?> CYBER-GPU STORE. All Rights Reserved. Refactored Full-Stack Architecture.</p>
      <div class="flex items-center gap-4 font-mono text-[11px]">
        <span class="text-slate-400">SECURE PDO PREPARED STATEMENTS</span>
        <span>•</span>
        <span class="text-slate-400">ZERO SQL INJECTION</span>
      </div>
    </div>
  </div>
</footer>

<div id="quickViewModal" onclick="if(event.target === this) closeQuickView()" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4 transition-all duration-300">
  <div class="relative w-full max-w-2xl glass-panel-elevated rounded-2xl overflow-hidden border border-cyan-500/30 shadow-2xl shadow-cyan-500/20">
    <button onclick="closeQuickView()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-900/80 border border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
      <i class="fa-solid fa-xmark text-lg"></i>
    </button>
    <div class="grid grid-cols-1 md:grid-cols-2">
      <div class="bg-gradient-to-br from-white to-slate-100 p-6 flex items-center justify-center border-b md:border-b-0 md:border-r border-slate-800 relative">
        <img id="qvImg" src="" alt="GPU Preview" class="max-h-56 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.15)]">
      </div>
      <div class="p-6 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-1">
            <span id="qvBrand" class="text-xs uppercase font-scifi font-bold text-cyan-400 tracking-wider"></span>
            <span id="qvStock" class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-950/80 text-emerald-400 border border-emerald-500/30"></span>
          </div>
          <h3 id="qvModelName" class="text-lg font-bold text-white mt-1 leading-snug"></h3>
          <p id="qvDesc" class="text-xs text-slate-400 mt-2 line-clamp-3 leading-relaxed"></p>
          
          <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-800">
              <span class="text-slate-500 text-[10px] block font-mono">VRAM BUFFER</span>
              <span id="qvVram" class="font-scifi font-bold text-slate-200"></span>
            </div>
            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-800">
              <span class="text-slate-500 text-[10px] block font-mono">CORE/BOOST CLOCK</span>
              <span id="qvClocks" class="font-scifi font-bold text-slate-200"></span>
            </div>
            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-800">
              <span class="text-slate-500 text-[10px] block font-mono">BUS WIDTH</span>
              <span id="qvBus" class="font-scifi font-bold text-slate-200"></span>
            </div>
            <div class="bg-slate-900/80 p-2 rounded-lg border border-slate-800">
              <span class="text-slate-500 text-[10px] block font-mono">POWER TDP</span>
              <span id="qvTdp" class="font-scifi font-bold text-slate-200"></span>
            </div>
          </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between gap-3">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-mono block">ราคาจำหน่าย</span>
            <span id="qvPrice" class="text-xl font-black font-scifi text-rose-400"></span>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" id="qvBuyBtn" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold font-scifi">
              <i class="fa-solid fa-cart-shopping mr-1.5"></i> สั่งซื้อทันที
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Global State for Client Script -->
<script>
  window.isUserLoggedIn = <?= isLoggedIn() ? 'true' : 'false' ?>;
  window.currentUserWallet = <?= isLoggedIn() ? (float)getUserWallet($_SESSION['user_id']) : 0 ?>;
</script>

<!-- Core Script with Cache-Busting -->
<script src="assets/js/main.js?v=<?= time() ?>"></script>

<!-- Immediate Inline Quick View Fallback (guarantees execution even if cached) -->
<script>
(function() {
  window.gpuCatalog = window.gpuCatalog || {};
  
  if (typeof window.openQuickView !== 'function') {
    window.openQuickView = function(input) {
      var gpuData = null;
      if (input && (input instanceof HTMLElement || input.nodeType === 1)) {
        var raw = input.getAttribute('data-gpu');
        if (raw) {
          try { gpuData = JSON.parse(raw); } catch (e) {}
        }
        if (!gpuData) {
          var elId = input.getAttribute('data-gpu-id') || input.getAttribute('data-id');
          if (elId && window.gpuCatalog && window.gpuCatalog[elId]) gpuData = window.gpuCatalog[elId];
        }
      } else if (typeof input === 'number' || typeof input === 'string') {
        if (window.gpuCatalog && window.gpuCatalog[input]) {
          gpuData = window.gpuCatalog[input];
        } else {
          var el = document.querySelector('[data-gpu-id="' + input + '"]');
          if (el && el.getAttribute('data-gpu')) {
            try { gpuData = JSON.parse(el.getAttribute('data-gpu')); } catch (e) {}
          }
        }
      } else if (input && typeof input === 'object') {
        gpuData = input;
      }

      if (!gpuData) return;
      var modal = document.getElementById('quickViewModal');
      if (!modal) return;

      var setT = function(id, text) {
        var el = document.getElementById(id);
        if (el) el.innerText = (text !== undefined && text !== null) ? text : '';
      };

      setT('qvModelName', gpuData.model_name || '');
      setT('qvBrand', (gpuData.brand_name || '') + (gpuData.chipset_model ? ' • ' + gpuData.chipset_model : ''));
      setT('qvPrice', '฿' + Number(gpuData.price || 0).toLocaleString());
      setT('qvStock', (Number(gpuData.stock) > 0 ? 'คงเหลือ ' + gpuData.stock + ' ชิ้น' : 'สินค้าหมด'));
      setT('qvVram', (gpuData.vram_gb ? gpuData.vram_gb + ' GB ' : '') + (gpuData.memory_type || ''));
      setT('qvClocks', (gpuData.core_clock || '-') + ' / ' + (gpuData.boost_clock || '-') + ' MHz');
      setT('qvBus', (gpuData.bus_width || '-') + '-bit');
      setT('qvTdp', (gpuData.tdp_watts || '-') + 'W');
      setT('qvDesc', gpuData.description || 'ไม่มีรายละเอียดเพิ่มเติม');

      var qvImg = document.getElementById('qvImg');
      if (qvImg) {
        qvImg.src = gpuData.image_url || 'assets/images/gpus/asus_rog_rtx_4090.jpg';
        qvImg.onerror = function() {
          this.src = 'https://ihcupload-bkk.s3.ap-southeast-7.amazonaws.com/img/product/products103815_800.jpg';
        };
      }

      var qvBuyBtn = document.getElementById('qvBuyBtn');
      if (qvBuyBtn) {
        if (Number(gpuData.stock) > 0) {
          qvBuyBtn.disabled = false;
          qvBuyBtn.className = "btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold font-scifi";
          qvBuyBtn.innerHTML = '<i class="fa-solid fa-cart-shopping mr-1.5"></i> สั่งซื้อทันที';
          qvBuyBtn.onclick = function() {
            closeQuickView();
            if (typeof confirmBuy === 'function') confirmBuy(gpuData.id, gpuData.model_name, gpuData.price);
          };
        } else {
          qvBuyBtn.disabled = true;
          qvBuyBtn.className = "px-4 py-2 rounded-xl text-xs font-bold font-scifi bg-slate-800 text-slate-500 cursor-not-allowed";
          qvBuyBtn.innerHTML = 'สินค้าหมด';
          qvBuyBtn.onclick = null;
        }
      }

      modal.classList.remove('hidden');
      modal.classList.add('flex');
      modal.style.display = 'flex';
    };
  }

  if (typeof window.openQuickViewById !== 'function') {
    window.openQuickViewById = function(id) {
      window.openQuickView(id);
    };
  }

  if (typeof window.closeQuickView !== 'function') {
    window.closeQuickView = function() {
      var modal = document.getElementById('quickViewModal');
      if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.style.display = 'none';
      }
    };
  }
})();
</script>

<!-- SweetAlert2 URL Message Listener -->
<?php if (isset($_GET['msg'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const msg = '<?= e($_GET['msg']) ?>';
    if (msg === 'added') {
      cyberAlert('เพิ่มข้อมูลสำเร็จ!', 'เพิ่มรายการการ์ดจอใหม่เข้าสู่ฐานข้อมูลเรียบร้อยแล้ว', 'success');
    } else if (msg === 'updated') {
      cyberAlert('อัปเดตข้อมูลสำเร็จ!', 'บันทึกการแก้ไขข้อมูลการ์ดจอเรียบร้อยแล้ว', 'success');
    } else if (msg === 'deleted') {
      cyberAlert('ลบข้อมูลสำเร็จ!', 'นำรายการการ์ดจอออกจากฐานข้อมูลเรียบร้อยแล้ว', 'success');
    } else if (msg === 'login_success') {
      cyberAlert('ยินดีต้อนรับ!', 'เข้าสู่ระบบสำเร็จ พร้อมใช้งานแล้ว', 'success');
    } else if (msg === 'registered') {
      cyberAlert('สมัครสมาชิกสำเร็จ!', 'บัญชีของคุณถูกสร้างเรียบร้อย ได้รับวงเงินจำลอง ฿9,999,999 สำหรับทดสอบช้อปปิ้งทันที', 'success');
    } else if (msg === 'login_required') {
      cyberAlert('กรุณาเข้าสู่ระบบ', 'คุณจำเป็นต้องเข้าสู่ระบบก่อนดำเนินการ', 'warning');
    } else if (msg === 'admin_only') {
      cyberAlert('ปฏิเสธการเข้าถึง!', 'เฉพาะผู้ดูแลระบบ (Admin) เท่านั้นที่สามารถเพิ่ม แก้ไข หรือลบการ์ดจอได้ สิทธิ์ Member ของคุณสามารถเลือกซื้อสินค้าได้ตามปกติ', 'error');
    } else if (msg === 'buy_success') {
      const item = '<?= e($_GET['item'] ?? 'การ์ดจอ') ?>';
      const order = '<?= e($_GET['order'] ?? '') ?>';
      const cost = '<?= e($_GET['cost'] ?? '') ?>';
      Swal.fire({
        title: 'สั่งซื้อสำเร็จเรียบร้อย!',
        html: `
          <div class="text-left text-xs p-3.5 rounded-xl bg-slate-900 border border-emerald-500/40 font-mono space-y-1.5 mt-2">
            <p class="text-slate-300"><strong>สินค้า:</strong> <span class="text-white">${item}</span></p>
            <p class="text-slate-300"><strong>รหัสออเดอร์:</strong> <span class="text-cyan-400 font-bold">${order}</span></p>
            <p class="text-slate-300"><strong>หักเงินจากวอลเล็ต:</strong> <span class="text-emerald-400 font-bold">฿${Number(cost).toLocaleString()}</span></p>
            <p class="text-slate-400 text-[10px] pt-1 border-t border-slate-800">ระบบได้ตัดสต็อกและบันทึกลงประวัติการซื้อของคุณแล้ว</p>
          </div>
        `,
        icon: 'success',
        customClass: {
          popup: 'cyber-swal',
          title: 'cyber-swal-title',
          confirmButton: 'btn-cyber-primary px-6 py-2 rounded-lg text-black font-scifi'
        },
        buttonsStyling: false,
        background: '#0B1120',
        confirmButtonText: 'เยี่ยมเลย (OK)'
      });
    } else if (msg === 'out_of_stock') {
      cyberAlert('สินค้าหมดสต็อก!', 'ขออภัย การ์ดจอรุ่นนี้ถูกสั่งซื้อหมดคลังแล้ว', 'error');
    } else if (msg === 'insufficient_funds') {
      cyberAlert('ยอดเงินไม่พอ!', 'วงเงินในกระเป๋าของคุณไม่เพียงพอ สามารถเติมเงินได้ที่หน้าโปรไฟล์', 'error');
    }
  });
</script>
<?php endif; ?>

</body>
</html>
