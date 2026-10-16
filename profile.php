<?php
/**
 * User Profile & Wallet Management Page
 * Edit nickname, name, avatar picture (file upload & preset), and view purchase history
 */
$pageTitle = "โปรไฟล์ & กระเป๋าเงิน (User Profile & Wallet)";
require_once __DIR__ . '/config/db.php';

requireLogin();

$pdo = getDBConnection();
$userId = (int)$_SESSION['user_id'];
$success = null;
$error = null;

// Fetch latest user data
$user = null;
if ($pdo) {
    try {
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmtUser->execute([':id' => $userId]);
        $user = $stmtUser->fetch();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// Handle Profile Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $avatar_url = trim($_POST['avatar_url'] ?? ($user['avatar_url'] ?? ''));
    $reset_wallet = isset($_POST['reset_wallet']);

    // Check if an image file was uploaded
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar_file'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $fileInfo = pathinfo($file['name']);
        $fileExt = strtolower($fileInfo['extension'] ?? '');

        if (!in_array($fileExt, $allowedExts)) {
            $error = "ไฟล์รูปภาพต้องเป็นนามสกุล JPG, JPEG, PNG, WEBP หรือ GIF เท่านั้น";
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            $error = "ขนาดไฟล์รูปภาพต้องไม่เกิน 5 MB";
        } else {
            $uploadDir = __DIR__ . '/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'avatar_' . $userId . '_' . time() . '_' . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $fileExt;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $avatar_url = 'uploads/avatars/' . $newFileName;
            } else {
                $error = "ไม่สามารถบันทึกไฟล์รูปลงระบบได้ กรุณาตรวจสอบสิทธิ์การเขียนโฟลเดอร์";
            }
        }
    }

    if (!$error) {
        if (empty($full_name)) {
            $error = "กรุณาระบุชื่อ - นามสกุล หรือชื่อเล่น";
        } elseif ($pdo) {
            try {
                if ($reset_wallet) {
                    // Reset wallet balance back to 9,999,999 THB
                    $stmt = $pdo->prepare("UPDATE users SET full_name = :fn, avatar_url = :av, wallet_balance = 9999999.00 WHERE id = :id");
                    $stmt->execute([':fn' => $full_name, ':av' => $avatar_url, ':id' => $userId]);
                    $_SESSION['wallet_balance'] = 9999999.00;
                    $success = "บันทึกโปรไฟล์ เปลี่ยนรูปภาพ และเติมเงินกระเป๋าจำลองเป็น ฿9,999,999 เรียบร้อยแล้ว!";
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = :fn, avatar_url = :av WHERE id = :id");
                    $stmt->execute([':fn' => $full_name, ':av' => $avatar_url, ':id' => $userId]);
                    $success = "อัปเดตข้อมูลโปรไฟล์และเปลี่ยนรูปภาพเรียบร้อยแล้ว!";
                }

                $_SESSION['full_name'] = $full_name;
                $_SESSION['avatar_url'] = $avatar_url;

                // Re-fetch user data
                $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
                $stmtUser->execute([':id' => $userId]);
                $user = $stmtUser->fetch();

            } catch (PDOException $e) {
                $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            }
        }
    }
}

// Fetch Purchase History
$orders = [];
if ($pdo) {
    try {
        $stmtOrders = $pdo->prepare("SELECT o.*, g.model_name, g.image_url, c.manufacturer 
                                    FROM orders o
                                    JOIN gpus g ON o.gpu_id = g.id
                                    JOIN chipsets c ON g.chipset_id = c.id
                                    WHERE o.user_id = :uid
                                    ORDER BY o.order_date DESC");
        $stmtOrders->execute([':uid' => $userId]);
        $orders = $stmtOrders->fetchAll();
    } catch (PDOException $e) {
        // silent or log
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb & Page Title -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
      <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
        <a href="index.php" class="hover:underline">HOME</a>
        <span>/</span>
        <span class="text-slate-400">PILOT PROFILE & ASSETS</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-black font-scifi text-white tracking-wide">
        PILOT PROFILE & ASSETS
      </h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">
        ศูนย์จัดการข้อมูลบัญชีผู้ใช้ อัปโหลดรูปโปรไฟล์ ตั้งชื่อเล่น และตรวจสอบประวัติการซื้อการ์ดจอ
      </p>
    </div>

    <div>
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-scifi border <?= isAdmin() ? 'border-amber-500/40 bg-amber-950/40 text-amber-300' : 'border-cyan-500/40 bg-cyan-950/40 text-cyan-300' ?>">
        <i class="fa-solid <?= isAdmin() ? 'fa-shield-halved' : 'fa-user-astronaut' ?>"></i>
        <?= isAdmin() ? 'SYSTEM ADMINISTRATOR' : 'VERIFIED PILOT MEMBER' ?>
      </span>
    </div>
  </div>

  <?php if ($success): ?>
    <div class="mb-6 p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-sm flex items-center gap-3 shadow-lg shadow-emerald-950/30">
      <i class="fa-solid fa-circle-check text-xl text-emerald-400 shrink-0"></i>
      <span><?= e($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-sm flex items-center gap-3 shadow-lg shadow-rose-950/30">
      <i class="fa-solid fa-triangle-exclamation text-xl text-rose-400 shrink-0"></i>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <!-- 1. TOP HERO DASHBOARD CARD (Full-Width, Centered & Perfectly Balanced) -->
  <div class="glass-panel-elevated p-6 sm:p-8 rounded-3xl border border-slate-800 bg-gradient-to-r from-slate-900/90 via-slate-950 to-slate-900/90 relative overflow-hidden mb-8 shadow-2xl shadow-black/60">
    <div class="absolute -left-12 -top-12 w-56 h-56 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -right-12 -bottom-12 w-56 h-56 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
      
      <!-- Left Hero: Avatar, Pilot Identity, Tag -->
      <div class="lg:col-span-7 flex flex-col sm:flex-row items-center sm:items-center gap-5 text-center sm:text-left">
        <div class="relative group shrink-0">
          <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl ring-4 ring-cyan-500/40 overflow-hidden bg-slate-950 shadow-xl shadow-cyan-500/20 flex items-center justify-center">
            <img id="topHeroAvatar" src="<?= e($user['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80') ?>" 
                 alt="Avatar" onerror="handleImgError(this)" class="w-full h-full object-cover">
          </div>
          <!-- Quick Camera Trigger Button -->
          <button type="button" onclick="document.getElementById('avatarFileInput').click()" 
                  class="absolute -bottom-2 -right-2 w-9 h-9 rounded-xl bg-cyan-500 text-slate-950 flex items-center justify-center shadow-lg hover:scale-110 hover:bg-cyan-400 transition-all cursor-pointer"
                  title="คลิกเพื่อเลือกไฟล์รูปภาพใหม่">
            <i class="fa-solid fa-camera text-sm"></i>
          </button>
        </div>

        <div class="space-y-1.5 min-w-0">
          <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
            <h2 class="text-2xl sm:text-3xl font-black font-scifi text-white tracking-wide truncate">
              <?= e($user['full_name'] ?: $user['username']) ?>
            </h2>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-cyan-950 border border-cyan-500/40 text-cyan-400 font-bold uppercase tracking-wider">
              @<?= e($user['username']) ?>
            </span>
          </div>
          
          <p class="text-xs sm:text-sm text-slate-400 font-mono">
            <i class="fa-regular fa-envelope mr-1 text-slate-500"></i> <?= e($user['email']) ?>
          </p>

          <div class="pt-2 flex flex-wrap items-center justify-center sm:justify-start gap-3">
            <span class="text-slate-400 font-mono text-xs bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-800">
              <i class="fa-solid fa-shield text-cyan-400 mr-1"></i> ระดับ: <span class="text-white font-bold"><?= isAdmin() ? 'Administrator' : 'General Member' ?></span>
            </span>
            <span class="text-slate-400 font-mono text-xs bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-800">
              <i class="fa-solid fa-bag-shopping text-purple-400 mr-1"></i> ประวัติสั่งซื้อ: <span class="text-white font-bold"><?= count($orders) ?></span> ครั้ง
            </span>
          </div>
        </div>
      </div>

      <!-- Right Hero: Wallet Balance Card (Members only) or Admin Privileges Hub -->
      <div class="lg:col-span-5 flex flex-col justify-center">
        <?php if (!isAdmin()): ?>
          <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-900/90 to-purple-950/40 border border-amber-500/30 relative shadow-inner">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-mono uppercase text-amber-400 font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-wallet text-amber-400"></i> SIMULATED WALLET BALANCE
              </span>
              <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-semibold">
                ● พร้อมสั่งซื้อ
              </span>
            </div>
            
            <div class="text-3xl sm:text-4xl font-black font-scifi text-transparent bg-clip-text bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-400 my-1">
              <?= formatPrice($user['wallet_balance'] ?? 9999999.00) ?>
            </div>
            
            <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono mt-2 pt-2 border-t border-slate-800/80">
              <span>วงเงินจำลองสำหรับทดสอบสั่งซื้อ</span>
              <span class="text-amber-400/90 font-semibold">ไม่จำกัดยอด</span>
            </div>
          </div>
        <?php else: ?>
          <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-900/90 via-slate-950 to-amber-950/30 border border-amber-500/30 relative shadow-inner">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-mono uppercase text-amber-400 font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-amber-400"></i> ADMINISTRATOR PRIVILEGES
              </span>
              <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold">
                ● FULL ACCESS
              </span>
            </div>
            
            <div class="text-2xl sm:text-3xl font-black font-scifi text-amber-300 my-1">
              INVENTORY MASTER
            </div>
            
            <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono mt-2 pt-2 border-t border-slate-800/80">
              <span>สิทธิ์เพิ่ม แก้ไข และจัดการคลังสินค้า</span>
              <a href="manage.php" class="text-cyan-400 hover:underline font-semibold">จัดการคลังสินค้า ➔</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- 2. TWO-COLUMN BALANCED CONTENT (Profile Settings vs Order History) -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Column: Edit Form & Photo Upload (7 cols) -->
    <div class="lg:col-span-7 space-y-6">
      
      <div class="glass-panel-elevated p-6 sm:p-7 rounded-3xl border border-slate-700/80">
        <h3 class="font-scifi font-bold text-base text-cyan-400 uppercase tracking-wider mb-5 pb-3 border-b border-slate-800 flex items-center justify-between">
          <span class="flex items-center gap-2">
            <i class="fa-solid fa-user-pen"></i> แก้ไขข้อมูลและเปลี่ยนรูปโปรไฟล์
          </span>
          <span class="text-[10px] font-mono text-slate-400">PERSONAL SETTINGS</span>
        </h3>

        <form method="POST" action="profile.php" enctype="multipart/form-data" class="space-y-6">
          
          <!-- Image Upload Drag & Drop Area -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-2">
              <i class="fa-solid fa-image text-cyan-400 mr-1"></i> อัปโหลดรูปภาพโปรไฟล์ (Upload Avatar Image)
            </label>

            <!-- Hidden File Input -->
            <input type="file" id="avatarFileInput" name="avatar_file" 
                   accept="image/png, image/jpeg, image/jpg, image/webp, image/gif" 
                   class="hidden" onchange="previewSelectedFile(event)">

            <div id="dropzone" 
                 onclick="document.getElementById('avatarFileInput').click()"
                 class="relative border-2 border-dashed border-slate-700 hover:border-cyan-400 rounded-2xl p-5 text-center transition-all bg-slate-900/60 hover:bg-cyan-950/20 cursor-pointer group">
              
              <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                
                <!-- Circular Image Preview -->
                <div class="relative w-20 h-20 rounded-2xl ring-2 ring-cyan-500/40 overflow-hidden bg-slate-950 shrink-0 shadow-lg shadow-black/50">
                  <img id="formAvatarPreview" src="<?= e($user['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80') ?>" 
                       alt="Avatar Preview" onerror="handleImgError(this)" class="w-full h-full object-cover">
                  <div class="absolute inset-0 bg-slate-950/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fa-solid fa-cloud-arrow-up text-cyan-400 text-xl"></i>
                  </div>
                </div>

                <!-- Guidance & Action Button -->
                <div class="text-center sm:text-left flex-1 min-w-0">
                  <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1.5">
                    <span class="btn-cyber-primary px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-1.5 shadow-md">
                      <i class="fa-solid fa-folder-open"></i> เลือกไฟล์จากเครื่อง
                    </span>
                    <span class="text-xs text-slate-400 hidden sm:inline">หรือลากรูปภาพมาวางตรงนี้</span>
                  </div>
                  <p id="fileNameDisplay" class="text-xs font-mono text-cyan-400 font-semibold truncate">
                    ยังไม่ได้เลือกไฟล์ใหม่ (ใช้รูปปัจจุบัน)
                  </p>
                  <p class="text-[11px] text-slate-500 font-mono mt-1">
                    รองรับไฟล์ JPG, PNG, WEBP, GIF (ขนาดสูงสุด 5MB)
                  </p>
                </div>

              </div>
            </div>
          </div>

          <!-- Or Enter Avatar URL Accordion / Alternative -->
          <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-3">
            <div class="flex items-center justify-between">
              <label for="avatar_url" class="text-xs font-mono text-slate-400 uppercase tracking-wider block">
                หรือใส่ลิงก์รูปภาพ (Direct Image URL)
              </label>
              <span class="text-[10px] font-mono text-slate-500">ทางเลือกเสริม</span>
            </div>
            <input type="text" id="avatar_url" name="avatar_url"
                   value="<?= e($user['avatar_url'] ?? '') ?>"
                   placeholder="https://example.com/my-photo.jpg"
                   class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:border-cyan-400">
            
            <!-- Quick Preset Buttons -->
            <div>
              <span class="text-[10px] font-mono text-slate-500 uppercase block mb-1.5">หรือเลือกรูปโปรไฟล์ Sci-Fi อัตโนมัติ:</span>
              <div class="grid grid-cols-4 gap-2">
                <button type="button" onclick="setAvatarPreset('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80')" class="py-1.5 px-2 rounded-lg bg-slate-900 hover:bg-cyan-950/60 border border-slate-800 hover:border-cyan-500/50 text-[10px] text-cyan-400 transition-colors">Cyber Neo</button>
                <button type="button" onclick="setAvatarPreset('https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80')" class="py-1.5 px-2 rounded-lg bg-slate-900 hover:bg-emerald-950/60 border border-slate-800 hover:border-emerald-500/50 text-[10px] text-emerald-400 transition-colors">Gamer</button>
                <button type="button" onclick="setAvatarPreset('https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80')" class="py-1.5 px-2 rounded-lg bg-slate-900 hover:bg-purple-950/60 border border-slate-800 hover:border-purple-500/50 text-[10px] text-purple-400 transition-colors">Pro Pilot</button>
                <button type="button" onclick="setAvatarPreset('https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80')" class="py-1.5 px-2 rounded-lg bg-slate-900 hover:bg-rose-950/60 border border-slate-800 hover:border-rose-500/50 text-[10px] text-rose-400 transition-colors">Cyberpunk</button>
              </div>
            </div>
          </div>

          <!-- Nickname / Full Name -->
          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-2">
              ชื่อเล่น หรือ ชื่อ-นามสกุล (Nickname / Full Name) <span class="text-rose-400">*</span>
            </label>
            <input type="text" name="full_name" required
                   value="<?= e($user['full_name'] ?? '') ?>"
                   placeholder="เช่น Aiden Walker หรือ นายนภา เกมเมอร์"
                   class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400">
          </div>

          <!-- Account Identity Details (Read-Only) -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-1.5">
                ชื่อในระบบ (Username)
              </label>
              <div class="px-3.5 py-2.5 rounded-xl bg-slate-900/40 border border-slate-800 text-slate-400 text-xs font-mono flex items-center justify-between">
                <span><?= e($user['username'] ?? '') ?></span>
                <i class="fa-solid fa-lock text-[10px] text-slate-600"></i>
              </div>
            </div>

            <div>
              <label class="block text-xs font-mono text-slate-400 uppercase tracking-wider mb-1.5">
                อีเมลบัญชี (Email)
              </label>
              <div class="px-3.5 py-2.5 rounded-xl bg-slate-900/40 border border-slate-800 text-slate-400 text-xs font-mono flex items-center justify-between">
                <span class="truncate"><?= e($user['email'] ?? '') ?></span>
                <i class="fa-solid fa-lock text-[10px] text-slate-600"></i>
              </div>
            </div>
          </div>

          <!-- Reset Wallet Toggle (Only for Shoppers / Members) -->
          <?php if (!isAdmin()): ?>
            <div class="p-4 rounded-2xl bg-purple-950/20 border border-purple-500/30 flex items-center justify-between">
              <div class="pr-3">
                <span class="text-xs font-bold text-purple-300 block flex items-center gap-1.5">
                  <i class="fa-solid fa-arrows-rotate text-purple-400"></i> เติมเงินวอลเล็ตจำลองให้เต็ม ฿9,999,999.00
                </span>
                <span class="text-[10px] text-slate-400 font-mono">
                  เปิดสวิตช์นี้เพื่อรีเซ็ตยอดเงินในกระเป๋ากลับเป็น ฿9,999,999 สำหรับทดสอบซื้อสินค้า
                </span>
              </div>
              <label class="relative inline-flex items-center cursor-pointer shrink-0">
                <input type="checkbox" name="reset_wallet" value="1" class="sr-only peer">
                <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
              </label>
            </div>
          <?php endif; ?>

          <!-- Submit Button -->
          <button type="submit" class="w-full btn-cyber-primary py-3.5 rounded-xl text-sm font-bold tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-cyan-500/20">
            <i class="fa-solid fa-floppy-disk text-base"></i> บันทึกข้อมูลและเปลี่ยนรูปโปรไฟล์
          </button>
        </form>
      </div>

    </div>

    <!-- Right Column: Order History (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
      <div class="glass-panel-elevated p-6 sm:p-7 rounded-3xl border border-slate-700/80">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-800">
          <h3 class="font-scifi font-bold text-base text-cyan-400 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-receipt"></i> ประวัติการสั่งซื้อการ์ดจอ
          </h3>
          <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-semibold">
            <?= count($orders) ?> รายการ
          </span>
        </div>

        <div class="space-y-3.5 max-h-[580px] overflow-y-auto pr-1">
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $ord): ?>
              <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-cyan-500/40 transition-colors flex items-center gap-3.5">
                <div class="w-14 h-14 rounded-xl bg-slate-950 p-1.5 border border-slate-800 shrink-0 flex items-center justify-center">
                  <img src="<?= e($ord['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-mono uppercase px-1.5 py-0.5 rounded bg-cyan-950 border border-cyan-500/30 text-cyan-400 font-bold">
                      <?= e($ord['order_code']) ?>
                    </span>
                    <span class="text-[9px] font-mono text-slate-500">
                      <?= date('d/m/Y H:i', strtotime($ord['order_date'])) ?>
                    </span>
                  </div>
                  <h4 class="font-bold text-white text-xs truncate mt-1" title="<?= e($ord['model_name']) ?>">
                    <?= e($ord['model_name']) ?>
                  </h4>
                  <div class="flex items-center justify-between mt-1">
                    <span class="font-scifi font-bold text-emerald-400 text-xs">
                      <?= formatPrice($ord['total_price']) ?>
                    </span>
                    <span class="text-[9px] font-mono text-emerald-400 font-semibold uppercase flex items-center gap-1">
                      <i class="fa-solid fa-circle-check text-[8px]"></i> สำเร็จ
                    </span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <?php if (isAdmin()): ?>
              <div class="text-center py-12 text-slate-500 font-mono text-xs">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-900 flex items-center justify-center mb-3 text-amber-400 border border-amber-500/30">
                  <i class="fa-solid fa-shield-halved text-2xl"></i>
                </div>
                <p class="text-slate-300 font-semibold mb-1">โหมดผู้ดูแลระบบ (Admin Mode)</p>
                <p class="text-slate-500 text-[11px] mb-4">บัญชีนี้มีสิทธิ์เพิ่มและแก้ไขข้อมูลสินค้าในระบบ (ไม่ต้องสั่งซื้อสินค้า)</p>
                <a href="manage.php" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2">
                  <i class="fa-solid fa-boxes-stacked"></i> จัดการคลังการ์ดจอ ➔
                </a>
              </div>
            <?php else: ?>
              <div class="text-center py-12 text-slate-500 font-mono text-xs">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-900 flex items-center justify-center mb-3 text-slate-600 border border-slate-800">
                  <i class="fa-solid fa-cart-shopping text-2xl"></i>
                </div>
                <p class="text-slate-400 font-semibold mb-1">ยังไม่มีประวัติการซื้อการ์ดจอ</p>
                <p class="text-slate-500 text-[11px] mb-4">คุณมีวงเงิน ฿9,999,999 พร้อมสั่งซื้อได้ทันที</p>
                <a href="index.php" class="btn-cyber-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2">
                  <i class="fa-solid fa-bag-shopping"></i> ไปเลือกซื้อการ์ดจอรุ่นแรกเลย ➔
                </a>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>

</div>

<script>
// Live Preview of Uploaded Image File
function previewSelectedFile(event) {
  const fileInput = event.target;
  const file = fileInput.files && fileInput.files[0];
  
  if (file) {
    // Check file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
      Swal.fire({
        icon: 'error',
        title: 'ไฟล์ใหญ่เกินไป',
        text: 'กรุณาเลือกไฟล์รูปภาพขนาดไม่เกิน 5 MB',
        background: '#0f172a',
        color: '#f8fafc',
        confirmButtonColor: '#00F0FF'
      });
      fileInput.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
      const resultSrc = e.target.result;
      
      // Update form avatar preview
      const formPreview = document.getElementById('formAvatarPreview');
      if (formPreview) formPreview.src = resultSrc;

      // Update top hero avatar
      const heroAvatar = document.getElementById('topHeroAvatar');
      if (heroAvatar) heroAvatar.src = resultSrc;
    };
    reader.readAsDataURL(file);

    // Update File Name Display
    const nameDisplay = document.getElementById('fileNameDisplay');
    if (nameDisplay) {
      nameDisplay.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
      nameDisplay.classList.add('text-emerald-400');
    }
  }
}

// Preset Avatars Selector
function setAvatarPreset(url) {
  const urlInput = document.getElementById('avatar_url');
  const formPreview = document.getElementById('formAvatarPreview');
  const heroAvatar = document.getElementById('topHeroAvatar');
  const fileInput = document.getElementById('avatarFileInput');
  const nameDisplay = document.getElementById('fileNameDisplay');

  if (urlInput) urlInput.value = url;
  if (formPreview) formPreview.src = url;
  if (heroAvatar) heroAvatar.src = url;
  if (fileInput) fileInput.value = ''; // Clear file input if preset chosen
  if (nameDisplay) {
    nameDisplay.textContent = 'เลือกจากรูปตัวอย่าง Sci-Fi สำเร็จรูป';
    nameDisplay.classList.remove('text-emerald-400');
  }
}

// Drag & Drop Handling on Dropzone
document.addEventListener('DOMContentLoaded', function() {
  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('avatarFileInput');
  const urlInput = document.getElementById('avatar_url');
  const formPreview = document.getElementById('formAvatarPreview');
  const heroAvatar = document.getElementById('topHeroAvatar');

  if (dropzone && fileInput) {
    ['dragenter', 'dragover'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('border-cyan-400', 'bg-cyan-950/40');
      });
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropzone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('border-cyan-400', 'bg-cyan-950/40');
      });
    });

    dropzone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      if (files && files.length > 0) {
        fileInput.files = files;
        previewSelectedFile({ target: fileInput });
      }
    });
  }

  // Sync typed URL with previews
  if (urlInput) {
    urlInput.addEventListener('input', function() {
      const val = urlInput.value.trim();
      if (val) {
        if (formPreview) formPreview.src = val;
        if (heroAvatar) heroAvatar.src = val;
      }
    });
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
