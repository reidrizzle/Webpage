<?php
/**
 * User Profile & Wallet Management Page
 * Edit nickname, name, avatar picture, and view purchase history
 */
$pageTitle = "โปรไฟล์ & กระเป๋าเงิน (User Profile & Wallet)";
require_once __DIR__ . '/config/db.php';

requireLogin();

$pdo = getDBConnection();
$userId = (int)$_SESSION['user_id'];
$success = null;
$error = null;

// Handle Profile Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $avatar_url = trim($_POST['avatar_url'] ?? '');
    $reset_wallet = isset($_POST['reset_wallet']);

    if (empty($full_name)) {
        $error = "กรุณาระบุชื่อ - นามสกุล หรือชื่อเล่น";
    } elseif ($pdo) {
        try {
            if ($reset_wallet) {
                // Reset wallet balance back to 9,999,999 THB
                $stmt = $pdo->prepare("UPDATE users SET full_name = :fn, avatar_url = :av, wallet_balance = 9999999.00 WHERE id = :id");
                $stmt->execute([':fn' => $full_name, ':av' => $avatar_url, ':id' => $userId]);
                $_SESSION['wallet_balance'] = 9999999.00;
                $success = "บันทึกโปรไฟล์และเติมเงินกระเป๋าจำลองเป็น ฿9,999,999 เรียบร้อยแล้ว!";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name = :fn, avatar_url = :av WHERE id = :id");
                $stmt->execute([':fn' => $full_name, ':av' => $avatar_url, ':id' => $userId]);
                $success = "อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว!";
            }

            $_SESSION['full_name'] = $full_name;
            $_SESSION['avatar_url'] = $avatar_url;

        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    }
}

// Fetch latest user data & order history
$user = null;
$orders = [];
if ($pdo) {
    try {
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmtUser->execute([':id' => $userId]);
        $user = $stmtUser->fetch();

        // Fetch Purchase History
        $stmtOrders = $pdo->prepare("SELECT o.*, g.model_name, g.image_url, c.manufacturer 
                                    FROM orders o
                                    JOIN gpus g ON o.gpu_id = g.id
                                    JOIN chipsets c ON g.chipset_id = c.id
                                    WHERE o.user_id = :uid
                                    ORDER BY o.order_date DESC");
        $stmtOrders->execute([':uid' => $userId]);
        $orders = $stmtOrders->fetchAll();

    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="py-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
  
  <!-- Breadcrumb -->
  <div class="mb-6">
    <div class="flex items-center gap-2 text-xs font-scifi text-cyan-400 mb-1">
      <a href="index.php" class="hover:underline">HOME</a>
      <span>/</span>
      <span>USER PROFILE & WALLET</span>
    </div>
    <h1 class="text-3xl font-black font-scifi text-white tracking-wide">
      PILOT PROFILE & ASSETS
    </h1>
    <p class="text-xs text-slate-400 mt-1">
      จัดการข้อมูลบัญชีผู้ใช้ ตั้งชื่อเล่น ปรับแต่งรูปโปรไฟล์ และตรวจสอบประวัติการซื้อการ์ดจอ
    </p>
  </div>

  <?php if ($success): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-500/50 text-emerald-300 text-xs flex items-center gap-3">
      <i class="fa-solid fa-circle-check text-lg text-emerald-400"></i>
      <span><?= e($success) ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-3">
      <i class="fa-solid fa-triangle-exclamation text-lg text-rose-400"></i>
      <span><?= e($error) ?></span>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Left Column: Profile Card & Edit Form (7 cols) -->
    <div class="lg:col-span-7 space-y-6">
      
      <!-- Wallet Banner Card -->
      <div class="glass-panel-elevated p-6 rounded-2xl border border-cyan-500/30 bg-gradient-to-br from-slate-900/90 via-slate-950 to-indigo-950/40 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between">
          <div>
            <span class="text-[11px] font-mono uppercase text-cyan-400 font-bold block">
              <i class="fa-solid fa-wallet mr-1"></i> SIMULATED WALLET BALANCE
            </span>
            <div class="text-3xl sm:text-4xl font-black font-scifi text-white mt-1">
              <?= formatPrice($user['wallet_balance'] ?? 9999999.00) ?>
            </div>
            <span class="text-[10px] text-slate-400 font-mono mt-1 block">
              วงเงินจำลองสำหรับการทดสอบสั่งซื้อการ์ดจอในระบบ
            </span>
          </div>
          <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
            <i class="fa-solid fa-coins text-2xl"></i>
          </div>
        </div>
      </div>

      <!-- Edit Profile Form -->
      <div class="glass-panel-elevated p-6 rounded-2xl border border-slate-700/80">
        <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider mb-4 pb-2 border-b border-slate-800">
          <i class="fa-solid fa-user-pen mr-2"></i> ข้อมูลส่วนตัวและรูปโปรไฟล์
        </h3>

        <form method="POST" action="profile.php" class="space-y-4">
          
          <!-- Avatar Preview -->
          <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-900/70 border border-slate-800">
            <div class="w-16 h-16 rounded-full ring-2 ring-cyan-500/50 overflow-hidden bg-slate-800 flex items-center justify-center shrink-0">
              <img id="avatarPreview" src="<?= e($user['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80') ?>" 
                   alt="Avatar" onerror="handleImgError(this)" class="w-full h-full object-cover">
            </div>
            <div class="flex-1">
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1">
                URL รูปโปรไฟล์ (Avatar URL)
              </label>
              <input type="url" id="avatar_url" name="avatar_url" required
                     value="<?= e($user['avatar_url'] ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80') ?>"
                     class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-cyan-400">
            </div>
          </div>

          <!-- Quick Avatar Presets -->
          <div>
            <span class="text-[10px] font-mono text-slate-500 uppercase block mb-1.5">หรือเลือกรูปโปรไฟล์ตัวอย่าง:</span>
            <div class="grid grid-cols-4 gap-2">
              <button type="button" onclick="setAvatar('https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80')" class="p-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] text-cyan-400">Cyber Neo</button>
              <button type="button" onclick="setAvatar('https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=200&q=80')" class="p-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] text-emerald-400">Gamer</button>
              <button type="button" onclick="setAvatar('https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=200&q=80')" class="p-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] text-purple-400">Pro Pilot</button>
              <button type="button" onclick="setAvatar('https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=80')" class="p-1 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-800 text-[10px] text-rose-400">Cyberpunk</button>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
                ชื่อในระบบ (Username)
              </label>
              <input type="text" disabled value="<?= e($user['username'] ?? '') ?>"
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/50 border border-slate-800 text-slate-400 text-xs font-mono cursor-not-allowed">
            </div>

            <div>
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
                อีเมล (Email)
              </label>
              <input type="email" disabled value="<?= e($user['email'] ?? '') ?>"
                     class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/50 border border-slate-800 text-slate-400 text-xs font-mono cursor-not-allowed">
            </div>
          </div>

          <div>
            <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
              ชื่อเล่น หรือ ชื่อ-นามสกุล (Nickname / Full Name) *
            </label>
            <input type="text" name="full_name" required
                   value="<?= e($user['full_name'] ?? '') ?>"
                   placeholder="e.g. เรย์ สายซิ่ง หรือ สมชาย เกมเมอร์"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-400">
          </div>

          <!-- Reset Wallet Option -->
          <div class="p-3.5 rounded-xl bg-purple-950/30 border border-purple-500/30 flex items-center justify-between">
            <div>
              <span class="text-xs font-bold text-purple-300 block">เติมเงินวอลเล็ตจำลองให้เต็ม ฿9,999,999</span>
              <span class="text-[10px] text-slate-400 font-mono">ใช้สำหรับทดสอบซื้อการ์ดจอใหม่ได้ไม่อั้น</span>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" name="reset_wallet" value="1" class="sr-only peer">
              <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-600"></div>
            </label>
          </div>

          <button type="submit" class="w-full btn-cyber-primary py-3 rounded-xl text-sm font-bold tracking-wider flex items-center justify-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลโปรไฟล์
          </button>
        </form>
      </div>

    </div>

    <!-- Right Column: Order History (5 cols) -->
    <div class="lg:col-span-5 space-y-6">
      <div class="glass-panel-elevated p-6 rounded-2xl border border-slate-700/80">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-800">
          <h3 class="font-scifi font-bold text-sm text-cyan-400 uppercase tracking-wider flex items-center gap-2">
            <i class="fa-solid fa-receipt text-cyan-400"></i> ประวัติการสั่งซื้อการ์ดจอ
          </h3>
          <span class="text-[10px] font-mono text-slate-400"><?= count($orders) ?> รายการ</span>
        </div>

        <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $ord): ?>
              <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg bg-slate-950 p-1 border border-slate-800 shrink-0 flex items-center justify-center">
                  <img src="<?= e($ord['image_url']) ?>" alt="GPU" onerror="handleImgError(this)" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 min-w-0">
                  <span class="text-[9px] font-mono uppercase px-1.5 py-0.2 rounded bg-cyan-950 border border-cyan-500/30 text-cyan-400">
                    <?= e($ord['order_code']) ?>
                  </span>
                  <h4 class="font-bold text-white text-xs truncate mt-0.5" title="<?= e($ord['model_name']) ?>">
                    <?= e($ord['model_name']) ?>
                  </h4>
                  <span class="text-[10px] text-slate-400 font-mono block">
                    <?= date('d/m/Y H:i', strtotime($ord['order_date'])) ?>
                  </span>
                </div>
                <div class="text-right shrink-0">
                  <span class="font-scifi font-bold text-emerald-400 text-xs block">
                    <?= formatPrice($ord['total_price']) ?>
                  </span>
                  <span class="text-[9px] font-mono text-emerald-400 font-semibold uppercase">
                    <i class="fa-solid fa-circle-check text-[8px] mr-0.5"></i> สำเร็จ
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center py-10 text-slate-500 font-mono text-xs">
              <i class="fa-solid fa-cart-shopping text-2xl text-slate-600 mb-2 block"></i>
              ยังไม่มีประวัติการซื้อการ์ดจอ<br>
              <a href="index.php" class="text-cyan-400 hover:underline mt-2 inline-block">ไปเลือกซื้อการ์ดจอรุ่นแรกเลย ➔</a>
            </div>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>

</div>

<script>
function setAvatar(url) {
  const input = document.getElementById('avatar_url');
  const preview = document.getElementById('avatarPreview');
  if (input && preview) {
    input.value = url;
    preview.src = url;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('avatar_url');
  const preview = document.getElementById('avatarPreview');
  if (input && preview) {
    input.addEventListener('input', () => {
      preview.src = input.value.trim();
    });
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
