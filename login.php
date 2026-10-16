<?php
/**
 * User Login Page
 * Cyber-Tech Authentication & Session Initialization
 */
$pageTitle = "เข้าสู่ระบบ (Sign In)";
require_once __DIR__ . '/config/db.php';

$error = null;
$pdo = getDBConnection();

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน";
    } elseif (!$pdo) {
        $error = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาตรวจสอบการตั้งค่า MySQL";
    } else {
        try {
            // Find user by username or email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :ident OR email = :ident LIMIT 1");
            $stmt->execute([':ident' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Successful authentication
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['avatar_url'] = $user['avatar_url'];

                header("Location: index.php?msg=login_success");
                exit;
            } else {
                $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง";
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดของระบบ: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="flex-1 flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative">
  <!-- Ambient Neon Backdrop -->
  <div class="absolute w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl -top-10 -left-10 pointer-events-none"></div>
  <div class="absolute w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -bottom-10 -right-10 pointer-events-none"></div>

  <div class="w-full max-w-md glass-panel-elevated p-8 rounded-3xl border border-cyan-500/20 shadow-2xl relative z-10">
    
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center mx-auto mb-4 text-cyan-400">
        <i class="fa-solid fa-shield-halved text-2xl"></i>
      </div>
      <h2 class="text-2xl font-black font-scifi text-white tracking-wide">
        CYBER AUTHENTICATION
      </h2>
      <p class="text-xs text-slate-400 mt-1">
        เข้าสู่ระบบศูนย์ควบคุมฮาร์ดแวร์ GPU Store
      </p>
    </div>

    <!-- Error Alert -->
    <?php if ($error): ?>
      <div class="mb-6 p-3.5 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-circle-exclamation text-base text-rose-400"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="login.php" class="space-y-5" onsubmit="return validateLoginForm(event)">
      <div>
        <label for="username" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
          ชื่อผู้ใช้ หรือ อีเมล (Username / Email)
        </label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
            <i class="fa-solid fa-user-astronaut"></i>
          </span>
          <input type="text" id="username" name="username" required
                 value="<?= e($_POST['username'] ?? '') ?>"
                 placeholder="admin หรือ username ของคุณ"
                 class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors">
        </div>
      </div>

      <div>
        <div class="flex justify-between items-center mb-1.5">
          <label for="password" class="block text-xs font-mono text-slate-300 uppercase tracking-wider">
            รหัสผ่าน (Password)
          </label>
        </div>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
            <i class="fa-solid fa-lock"></i>
          </span>
          <input type="password" id="password" name="password" required
                 placeholder="••••••••"
                 class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors">
        </div>
      </div>

      <button type="submit" class="w-full btn-cyber-primary py-3 rounded-xl text-sm font-bold tracking-wider flex items-center justify-center gap-2">
        <i class="fa-solid fa-arrow-right-to-bracket"></i> ยืนยันการเข้าสู่ระบบ
      </button>
    </form>

    <!-- Quick Demo Accounts for Project Review -->
    <div class="mt-8 pt-6 border-t border-slate-800 text-center">
      <p class="text-[11px] text-slate-400 uppercase font-mono mb-2.5">
        <i class="fa-solid fa-key text-cyan-400 mr-1"></i> บัญชีทดสอบสำหรับอาจารย์ / กรรมการ
      </p>
      <div class="grid grid-cols-2 gap-2 text-xs">
        <button type="button" onclick="fillDemo('admin', 'admin123')" 
                class="py-2 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-700 text-cyan-400 font-mono transition-colors text-left">
          <span class="font-bold block text-[11px] text-white">ผู้ดูแล (Admin)</span>
          <span class="text-[10px] text-slate-400">admin / admin123</span>
        </button>
        <button type="button" onclick="fillDemo('gamer_pro', 'user123')" 
                class="py-2 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 border border-slate-700 text-purple-400 font-mono transition-colors text-left">
          <span class="font-bold block text-[11px] text-white">สมาชิก (Member)</span>
          <span class="text-[10px] text-slate-400">gamer_pro / user123</span>
        </button>
      </div>

      <p class="text-xs text-slate-400 mt-6">
        ยังไม่มีบัญชีใช้งาน? 
        <a href="register.php" class="text-cyan-400 hover:underline font-semibold ml-1">สมัครสมาชิกใหม่</a>
      </p>
    </div>

  </div>
</div>

<script>
function fillDemo(user, pass) {
  document.getElementById('username').value = user;
  document.getElementById('password').value = pass;
}

function validateLoginForm(e) {
  const user = document.getElementById('username').value.trim();
  const pass = document.getElementById('password').value;
  if (!user || !pass) {
    cyberAlert('ข้อมูลไม่ครบถ้วน', 'กรุณากรอกทั้งชื่อผู้ใช้และรหัสผ่าน', 'warning');
    e.preventDefault();
    return false;
  }
  return true;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
