<?php
/**
 * Navigation Bar Component
 * Perfectly Balanced, Zero-Collision Cyber Navigation Bar
 * - Admin: หน้าแรก, จัดการคลัง, เพิ่มการ์ดจอ, รายงานสรุปคลัง (No wallet, No buy)
 * - User ทั่วไป:
 *    Center: หน้าแรก, สินค้าทั้งหมด, โปรโมชั่น, เปรียบเทียบ, คำสั่งซื้อ
 *    Right:  ตะกร้าสินค้า (Cart Badge), Wallet (฿9.9M), Profile, Logout
 */
$currentPage = basename($_SERVER['PHP_SELF']);
$currentWallet = (isLoggedIn() && !isAdmin()) ? getUserWallet($_SESSION['user_id']) : 0;
$cartCount = getCartCount();
?>
<header class="sticky top-0 z-50 glass-panel border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-xl">
  <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6">
    <div class="flex items-center justify-between h-20 gap-2 md:gap-4">
      
      <!-- 1. Left: Brand Logo (shrink-0) -->
      <div class="flex items-center justify-start shrink-0">
        <a href="index.php" class="flex items-center gap-2.5 group">
          <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-cyan-500 via-indigo-500 to-emerald-400 p-0.5 shadow-lg shadow-cyan-500/20 group-hover:shadow-cyan-500/40 transition-all duration-300 shrink-0">
            <div class="w-full h-full bg-slate-950 rounded-[10px] flex items-center justify-center">
              <i class="fa-solid fa-microchip text-base sm:text-lg text-cyan-400 group-hover:rotate-12 transition-transform duration-300"></i>
            </div>
          </div>
          <div class="flex flex-col">
            <span class="font-scifi text-base sm:text-lg font-black tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-white via-cyan-200 to-cyan-400 leading-tight">
              CYBER<span class="text-cyan-400">GPU</span>
            </span>
            <span class="text-[8px] uppercase tracking-widest text-slate-400 font-scifi -mt-0.5 hidden 2xl:block">
              Compute Hardware
            </span>
          </div>
        </a>
      </div>

      <!-- 2. Center: Compact Cyber Pill Navigation (Fits cleanly, zero collision) -->
      <div class="hidden lg:flex items-center justify-center flex-1 min-w-0 px-1">
        <nav class="bg-slate-900/90 border border-slate-800/90 rounded-2xl p-1 flex items-center gap-0.5 shadow-xl shadow-black/50 backdrop-blur-md">
          
          <?php if (isAdmin()): ?>
            <!-- ================= ADMIN NAVIGATION ================= -->
            <a href="index.php" class="px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-950/80 border border-cyan-500/40 font-scifi' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-house mr-1 text-xs"></i> หน้าแรก
            </a>
            <a href="manage.php" class="px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= in_array($currentPage, ['manage.php', 'edit_gpu.php']) ? 'text-amber-400 bg-amber-950/80 border border-amber-500/40 font-scifi' : 'text-amber-400/90 hover:text-amber-300 hover:bg-amber-950/40' ?>">
              <i class="fa-solid fa-shield-halved mr-1 text-xs"></i> จัดการคลัง
            </a>
            <a href="add_gpu.php" class="px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'add_gpu.php' ? 'text-amber-400 bg-amber-950/80 border border-amber-500/40 font-scifi' : 'text-amber-400/90 hover:text-amber-300 hover:bg-amber-950/40' ?>">
              <i class="fa-solid fa-plus-circle mr-1 text-xs"></i> เพิ่มการ์ดจอ
            </a>
            <a href="reports.php" class="px-3 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'reports.php' ? 'text-cyan-400 bg-cyan-950/80 border border-cyan-500/40 font-scifi' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-chart-pie mr-1 text-xs"></i> รายงานสรุปคลัง
            </a>

          <?php else: ?>
            <!-- ================= USER ทั่วไป NAVIGATION ================= -->
            <!-- 1. หน้าแรก -->
            <a href="index.php" class="px-2.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-950/80 border border-cyan-500/40 font-scifi shadow-[0_0_12px_rgba(0,240,255,0.2)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-house mr-1 text-xs"></i> หน้าแรก
            </a>

            <!-- 2. สินค้าทั้งหมด -->
            <a href="products.php" class="px-2.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= in_array($currentPage, ['products.php', 'search.php']) ? 'text-cyan-400 bg-cyan-950/80 border border-cyan-500/40 font-scifi shadow-[0_0_12px_rgba(0,240,255,0.2)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-layer-group mr-1 text-xs"></i> สินค้าทั้งหมด
            </a>

            <!-- 3. โปรโมชั่น -->
            <a href="promotions.php" class="px-2.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'promotions.php' ? 'text-rose-400 bg-rose-950/80 border border-rose-500/40 font-scifi shadow-[0_0_12px_rgba(255,0,85,0.2)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-tags mr-1 text-xs text-rose-400"></i> โปรโมชั่น
            </a>

            <!-- 4. เปรียบเทียบ -->
            <a href="compare.php" class="px-2.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'compare.php' ? 'text-cyan-400 bg-cyan-950/80 border border-cyan-500/40 font-scifi shadow-[0_0_12px_rgba(0,240,255,0.2)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-code-compare mr-1 text-xs"></i> เปรียบเทียบ
            </a>

            <!-- 5. คำสั่งซื้อ -->
            <a href="orders.php" class="px-2.5 py-1.5 rounded-xl text-xs font-medium whitespace-nowrap transition-all <?= $currentPage === 'orders.php' ? 'text-emerald-400 bg-emerald-950/80 border border-emerald-500/40 font-scifi shadow-[0_0_12px_rgba(16,185,129,0.2)]' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' ?>">
              <i class="fa-solid fa-box-archive mr-1 text-xs text-emerald-400"></i> คำสั่งซื้อ
            </a>
          <?php endif; ?>

        </nav>
      </div>

      <!-- 3. Right: Cart Badge, Wallet & Profile Actions (shrink-0) -->
      <div class="flex items-center justify-end gap-2 shrink-0">
        
        <?php if (!isAdmin()): ?>
          <!-- Shopping Cart Pill (Always clearly visible on Right) -->
          <a href="cart.php" class="relative flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-400 text-slate-300 hover:text-white transition-all group shrink-0 <?= $currentPage === 'cart.php' ? 'border-cyan-400 text-cyan-300 bg-cyan-950/30' : '' ?>" title="ตะกร้าสินค้า">
            <div class="relative">
              <i class="fa-solid fa-cart-shopping text-cyan-400 text-xs sm:text-sm group-hover:scale-110 transition-transform"></i>
              <span id="navCartCount" class="absolute -top-2.5 -right-2.5 min-w-[16px] h-4 px-1 rounded-full text-[9px] font-bold flex items-center justify-center leading-none <?= $cartCount > 0 ? 'bg-cyan-500 text-slate-950 animate-pulse' : 'bg-slate-800 text-slate-400' ?>">
                <?= $cartCount ?>
              </span>
            </div>
            <span class="hidden md:inline text-xs font-medium">ตะกร้า</span>
          </a>
        <?php endif; ?>

        <?php if (isLoggedIn()): ?>
          
          <!-- Wallet Balance Pill (Members only, Admin has NO wallet) -->
          <?php if (!isAdmin()): ?>
            <a href="profile.php" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500/10 to-yellow-500/10 border border-amber-500/30 hover:border-amber-500/60 transition-all group shadow-sm shadow-amber-500/10 shrink-0" title="คลิกเพื่อจัดการโปรไฟล์และกระเป๋าเงิน">
              <div class="w-5 h-5 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform shrink-0">
                <i class="fa-solid fa-wallet text-[11px]"></i>
              </div>
              <div class="flex flex-col text-left">
                <span class="text-[7px] font-mono text-slate-400 uppercase tracking-wider leading-none hidden sm:block">WALLET</span>
                <span class="font-scifi font-bold text-xs text-amber-300 whitespace-nowrap">
                  <?= formatPrice($currentWallet) ?>
                </span>
              </div>
            </a>
          <?php endif; ?>

          <!-- User Profile Pill -->
          <a href="profile.php" class="flex items-center gap-2 p-1 pr-2 sm:pr-2.5 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-colors group shrink-0">
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full ring-2 ring-cyan-500/40 overflow-hidden bg-slate-800 flex items-center justify-center shrink-0">
              <img src="<?= e($_SESSION['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80') ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <div class="hidden xl:flex flex-col text-left">
              <span class="text-xs font-semibold text-slate-200 group-hover:text-cyan-300 transition-colors truncate max-w-[100px]"><?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?></span>
              <span class="text-[7px] font-scifi uppercase px-1 rounded <?= isAdmin() ? 'bg-amber-500/20 text-amber-400' : 'bg-cyan-500/20 text-cyan-400' ?>">
                <?= isAdmin() ? 'ADMIN' : 'MEMBER' ?>
              </span>
            </div>
          </a>

          <!-- Logout Button -->
          <a href="logout.php" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 rounded-xl transition-colors shrink-0" title="ออกจากระบบ">
            <i class="fa-solid fa-right-from-bracket text-sm"></i>
          </a>

        <?php else: ?>
          <div class="flex items-center gap-1.5 shrink-0">
            <a href="login.php" class="px-3 py-1.5 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800/80 transition-colors">
              เข้าสู่ระบบ
            </a>
            <a href="register.php" class="btn-cyber-primary px-3 py-1.5 rounded-xl text-xs font-bold tracking-wider uppercase transition-transform hover:scale-105">
              สมัครสมาชิก
            </a>
          </div>
        <?php endif; ?>

        <!-- Mobile Menu Hamburger Button -->
        <div class="flex lg:hidden items-center ml-0.5">
          <button type="button" onclick="toggleMobileMenu()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none" aria-label="Toggle Menu">
            <i class="fa-solid fa-bars text-xl"></i>
          </button>
        </div>

      </div>

    </div>
  </div>

  <!-- Mobile Drawer Menu -->
  <div id="mobileMenu" class="hidden lg:hidden glass-panel border-b border-slate-800 px-4 pt-2 pb-6 space-y-2">
    <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'index.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
      <i class="fa-solid fa-house mr-2"></i> หน้าแรก
    </a>

    <?php if (isAdmin()): ?>
      <a href="manage.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'manage.php' ? 'text-amber-400 bg-amber-950/60' : 'text-amber-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-shield-halved mr-2"></i> จัดการคลังสินค้า (Admin)
      </a>
      <a href="add_gpu.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'add_gpu.php' ? 'text-amber-400 bg-amber-950/60' : 'text-amber-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-plus-circle mr-2"></i> เพิ่มการ์ดจอ (Admin)
      </a>
      <a href="reports.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'reports.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-chart-pie mr-2"></i> หน้ารายงานสรุปคลัง (Admin)
      </a>
    <?php else: ?>
      <a href="products.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'products.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-layer-group mr-2"></i> สินค้าทั้งหมด
      </a>
      <a href="promotions.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'promotions.php' ? 'text-rose-400 bg-rose-950/60' : 'text-rose-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-tags mr-2 text-rose-400"></i> โปรโมชั่น
      </a>
      <a href="compare.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'compare.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-code-compare mr-2"></i> เปรียบเทียบการ์ดจอ
      </a>
      <a href="orders.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'orders.php' ? 'text-emerald-400 bg-emerald-950/60' : 'text-emerald-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-box-archive mr-2"></i> คำสั่งซื้อของฉัน
      </a>
      <a href="cart.php" class="block px-3 py-2 rounded-md text-base font-medium <?= $currentPage === 'cart.php' ? 'text-cyan-400 bg-cyan-950/60' : 'text-slate-300 hover:bg-slate-800' ?>">
        <i class="fa-solid fa-cart-shopping mr-2"></i> ตะกร้าสินค้า (<?= $cartCount ?>)
      </a>
    <?php endif; ?>
    
    <div class="pt-4 border-t border-slate-800">
      <?php if (isLoggedIn()): ?>
        <div class="px-3 py-2 space-y-2">
          <?php if (!isAdmin()): ?>
            <div class="flex items-center justify-between">
              <span class="text-xs text-slate-400">วงเงินกระเป๋า:</span>
              <span class="font-scifi font-bold text-amber-400 text-sm"><?= formatPrice($currentWallet) ?></span>
            </div>
          <?php else: ?>
            <div class="flex items-center justify-between">
              <span class="text-xs text-slate-400">โหมดการทำงาน:</span>
              <span class="font-scifi font-bold text-amber-400 text-xs">ADMINISTRATOR</span>
            </div>
          <?php endif; ?>
          <div class="flex items-center justify-between pt-1">
            <a href="profile.php" class="text-sm font-semibold text-cyan-400 hover:underline">
              <i class="fa-solid fa-user-gear mr-1"></i> แก้ไขโปรไฟล์ & อัปโหลดรูป
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
