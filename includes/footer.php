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

<!-- Global Quick View Modal -->
<div id="quickViewModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-md p-4 transition-all duration-300">
  <div class="relative w-full max-w-2xl glass-panel-elevated rounded-2xl overflow-hidden border border-cyan-500/30 shadow-2xl shadow-cyan-500/20">
    <button onclick="closeQuickView()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-slate-900/80 border border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
      <i class="fa-solid fa-xmark text-lg"></i>
    </button>
    <div class="grid grid-cols-1 md:grid-cols-2">
      <div class="bg-slate-950 p-6 flex items-center justify-center border-b md:border-b-0 md:border-r border-slate-800">
        <img id="qvImg" src="" alt="GPU Preview" class="max-h-56 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.8)]">
      </div>
      <div class="p-6 flex flex-col justify-between">
        <div>
          <span id="qvBrand" class="text-xs uppercase font-scifi font-bold text-cyan-400 tracking-wider"></span>
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

        <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
          <div>
            <span class="text-[10px] text-slate-400 uppercase font-mono block">ราคาจำหน่าย</span>
            <span id="qvPrice" class="text-xl font-black font-scifi text-cyan-400"></span>
          </div>
          <div class="text-right">
            <span class="text-[10px] text-slate-400 uppercase font-mono block">สถานะคลัง</span>
            <span id="qvStock" class="text-xs font-semibold text-emerald-400"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Core Script -->
<script src="assets/js/main.js"></script>

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
      cyberAlert('สมัครสมาชิกสำเร็จ!', 'บัญชีของคุณถูกสร้างเรียบร้อย สามารถเข้าสู่ระบบได้ทันที', 'success');
    } else if (msg === 'login_required') {
      cyberAlert('กรุณาเข้าสู่ระบบ', 'คุณจำเป็นต้องเข้าสู่ระบบก่อนเข้าใช้งานฟังก์ชันนี้', 'warning');
    }
  });
</script>
<?php endif; ?>

</body>
</html>
