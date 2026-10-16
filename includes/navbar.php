<?php
/**
 * Navigation Bar Component
 * Role-Based Access Control (Admin vs Member) & Real-time Wallet Display
 */
$currentPage = basename($_SERVER['PHP_SELF']);
$currentWallet = isLoggedIn() ? getUserWallet($_SESSION['user_id']) : 0;
?>
<header class="sticky top-0 z-50 glass-panel border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-xl">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      
      <!-- Brand Logo -->
      <a href="index.php" class="flex items-center gap-3 group">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-500 via-indigo-500 to-emerald-400 p-0.5 shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/40 transition-all duration-300">
          <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
            <i class="fa-solid fa-microchip text-xl text-cyan-400 group-hover:rotate-12 transition-transform duration-300"></i>
          </div>
        </div>
        <div class="flex flex-col">
          <span class="font-scifi text-xl font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-200 to-cyan-400">
            CYBER<span class="text-cyan-400">GPU</span>
          </span>
          <span class="text-[10px] uppercase tracking-widest text-slate-400 font-scifi -mt-1">
            Compute Hardware
          </span>
        </div>
      </a>

      <!-- Desktop Navigation Menu -->
      <nav class="hidden md:flex items-center gap-1 lg:gap-2">
        <a href="index.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-950/50 border border-cyan-500/30 font-scifi' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
          <i class="fa-solid fa-house mr-1.5 text-xs"></i> หน้าแรก
        </a>
        <a href="search.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all <?= $currentPage === 'search.php' ? 'text-cyan-400 bg-cyan-950/50 border border-cyan-500/30 font-scifi' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
          <i class="fa-solid fa-magnifying-glass mr-1.5 text-xs"></i> ค้นหาการ์ดจอ
        </a>

        <!-- Admin Only Menu Items -->
        <?php if (isAdmin()): ?>
          <a href="manage.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all <?= in_array($currentPage, ['manage.php', 'edit_gpu.php']) ? 'text-cyan-400 bg-cyan-950/50 border border-cyan-500/30 font-scifi' : 'text-amber-400/90 hover:text-amber-300 hover:bg-amber-950/40' ?>">
            <i class="fa-solid fa-shield-halved mr-1.5 text-xs"></i> จัดการคลัง (Admin)
          </a>
          <a href="add_gpu.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all <?= $currentPage === 'add_gpu.php' ? 'text-cyan-400 bg-cyan-950/50 border border-cyan-500/30 font-scifi' : 'text-amber-400/90 hover:text-amber-300 hover:bg-amber-950/40' ?>">
            <i class="fa-solid fa-plus-circle mr-1.5 text-xs"></i> เพิ่มการ์ดจอ
          </a>
        <?php endif; ?>

        <a href="reports.php" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all <?= $currentPage === 'reports.php' ? 'text-cyan-400 bg-cyan-950/50 border border-cyan-500/30 font-scifi' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
          <i class="fa-solid fa-chart-pie mr-1.5 text-xs"></i> หน้ารายงาน
        </a>
      </nav>

      <!-- User Auth & Wallet State -->
      <div class="hidden md:flex items-center gap-3">
        <?php if (isLoggedIn()): ?>
          
          <!-- Simulated Wallet Chip -->
          <a href="profile.php" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/30 hover:border-amber-500/60 transition-all group" title="คลิกเพื่อจัดการโปรไฟล์และกระเป๋าเงิน">
            <i class="fa-solid fa-wallet text-amber-400 text-sm group-hover:scale-110 transition-transform"></i>
            <div class="flex flex-col text-left">
              <span class="text-[9px] font-mono text-slate-400 uppercase tracking-wider">WALLET</span>
              <span class="font-scifi font-bold text-xs text-amber-300 -mt-0.5">
                <?= formatPrice($currentWallet) ?>
              </span>
            </div>
          </a>

          <!-- Profile Badge -->
          <a href="profile.php" class="flex items-center gap-2.5 pl-2 py-1 pr-3 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="w-8 h-8 rounded-full ring-2 ring-cyan-500/40 overflow-hidden bg-slate-800 flex items-center justify-center">
              <img src="<?= e($_SESSION['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80') ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col text-left">
              <span class="text-xs font-semibold text-slate-200"><?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
              <span class="text-[9px] font-scifi uppercase px-1 rounded <?= isAdmin() ? 'bg-amber-500/20 text-amber-400' : 'bg-cyan-500/20 text-cyan-400' ?>">
                <?= isAdmin() ? 'ADMIN' : 'MEMBER' ?>
              </span>
            </div>
          </a>

          <a href="logout.php" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 rounded-lg transition-colors" title="ออกจากระบบ">
            <i class="fa-solid fa-right-from-bracket text-base"></i>
          </a>

        <?php else: ?>
          <div class="flex items-center gap-2">
            <a href="login.php" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors">
              เข้าสู่ระบบ
            </a>
            <a href="register.php" class="btn-cyber-primary px-4 py-2 rounded-lg text-xs font-bold tracking-wider uppercase transition-transform hover:scale-105">
              สมัครสมาชิก (รับ ฿9,999,999)
            </a>
          </div>
        <?php endif; ?>
      </div>

      <!-- Mobile Menu Button -->
      <div class="flex md:hidden items-center">
        <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Toggle Menu">
          <i class="fa-solid fa-bars text-xl"></i>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Drawer Menu -->
  <div id="mobileMenu" class="hidden md:hidden glass-panel border-b border-slate-800 px-4 pt-2 pb-6 space-y-2">
    <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
      <i class="fa-solid fa-house mr-2"></i> หน้าแรก
    </a>
    <a href="search.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'search.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
      <i class="fa-solid fa-magnifying-glass mr-2"></i> ค้นหาการ์ดจอ
    </a>

    <?php if (isAdmin()): ?>
      <a href="manage.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'manage.php' ? 'text-amber-400 bg-amber-950/60' : 'text-amber-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-shield-halved mr-2"></i> จัดการคลัง (Admin)
      </a>
      <a href="add_gpu.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'add_gpu.php' ? 'text-amber-400 bg-amber-950/60' : 'text-amber-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-plus-circle mr-2"></i> เพิ่มการ์ดจอ (Admin)
      </a>
    <?php endif; ?>

    <a href="reports.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'reports.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
      <i class="fa-solid fa-chart-pie mr-2"></i> หน้ารายงานสถิติ
    </a>
    
    <div class="pt-4 border-t border-slate-800">
      <?php if (isLoggedIn()): ?>
        <div class="px-3 py-2 space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-xs text-slate-400">วงเงินกระเป๋า:</span>
            <span class="font-scifi font-bold text-amber-400 text-sm"><?= formatPrice($currentWallet) ?></span>
          </div>
          <div class="flex items-center justify-between">
            <a href="profile.php" class="text-sm font-semibold text-cyan-400 hover:underline">
              <i class="fa-solid fa-user-gear mr-1"></i> แก้ไขโปรไฟล์
            </a>
            <a href="logout.php" class="text-rose-400 hover:underline text-sm font-medium">ออกจากระบบ</a>
          </div>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-2 gap-2 mt-2">
          <a href="login.php" class="text-center py-2 rounded-lg bg-slate-800 text-slate-200 text-sm font-medium">เข้าสู่ระบบ</a>
          <a href="register.php" class="text-center py-2 rounded-lg btn-cyber-primary text-black text-sm font-bold">สมัครสมาชิก</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>
