<?php
/**
 * User Registration Page
 * Secure password hashing with BCRYPT & JS Client Validation
 */
$pageTitle = "สมัครสมาชิกใหม่ (Register)";
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
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (empty($username) || empty($full_name) || empty($email) || empty($password)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วนทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } elseif (strlen($password) < 6) {
        $error = "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร";
    } elseif ($password !== $confirm_password) {
        $error = "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
    } elseif (!$pdo) {
        $error = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาลองใหม่อีกครั้ง";
    } else {
        try {
            // Check if username or email is already taken
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
            $stmt->execute([':u' => $username, ':e' => $email]);
            if ($stmt->fetch()) {
                $error = "ชื่อผู้ใช้ (Username) หรืออีเมลนี้มีอยู่ในระบบแล้ว กรุณาใช้ข้อมูลอื่น";
            } else {
                // Hash password securely with BCRYPT
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                
                $insertStmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password, role, wallet_balance) VALUES (:u, :fn, :e, :p, 'member', 9999999.00)");
                $insertStmt->execute([
                    ':u' => $username,
                    ':fn' => $full_name,
                    ':e' => $email,
                    ':p' => $hashedPassword
                ]);

                header("Location: login.php?msg=registered");
                exit;
            }
        } catch (PDOException $e) {
            $error = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="flex-1 flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 relative">
  <!-- Neon Ambient Backdrops -->
  <div class="absolute w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl -top-10 -right-10 pointer-events-none"></div>
  <div class="absolute w-96 h-96 bg-purple-500/10 rounded-full blur-3xl -bottom-10 -left-10 pointer-events-none"></div>

  <div class="w-full max-w-lg glass-panel-elevated p-8 rounded-3xl border border-cyan-500/20 shadow-2xl relative z-10">
    
    <div class="text-center mb-8">
      <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500/20 to-purple-500/20 border border-cyan-500/40 flex items-center justify-center mx-auto mb-4 text-cyan-400">
        <i class="fa-solid fa-user-plus text-2xl"></i>
      </div>
      <h2 class="text-2xl font-black font-scifi text-white tracking-wide">
        CREATE NEW ACCOUNT
      </h2>
      <p class="text-xs text-slate-400 mt-1">
        เข้าร่วมคอมมูนิตี้ฮาร์ดแวร์เกมมิ่งและบริหารจัดการการ์ดจอ
      </p>
    </div>

    <!-- Error Alert -->
    <?php if ($error): ?>
      <div class="mb-6 p-3.5 rounded-xl bg-rose-950/60 border border-rose-500/50 text-rose-300 text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-circle-exclamation text-base text-rose-400"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form id="registerForm" method="POST" action="register.php" class="space-y-4" onsubmit="return validateRegisterForm(event)">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="username" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            ชื่อผู้ใช้ (Username) *
          </label>
          <input type="text" id="username" name="username" required
                 value="<?= e($_POST['username'] ?? '') ?>"
                 placeholder="e.g. cyber_master"
                 class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 transition-colors">
        </div>

        <div>
          <label for="full_name" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            ชื่อ - นามสกุล (Full Name) *
          </label>
          <input type="text" id="full_name" name="full_name" required
                 value="<?= e($_POST['full_name'] ?? '') ?>"
                 placeholder="e.g. สมชาย สายเกม"
                 class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 transition-colors">
        </div>
      </div>

      <div>
        <label for="email" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
          อีเมล (Email Address) *
        </label>
        <input type="email" id="email" name="email" required
               value="<?= e($_POST['email'] ?? '') ?>"
               placeholder="name@example.com"
               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 transition-colors">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="password" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            รหัสผ่าน (Password) *
          </label>
          <input type="password" id="password" name="password" required
                 placeholder="อย่างน้อย 6 ตัวอักษร"
                 class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 transition-colors">
        </div>

        <div>
          <label for="confirm_password" class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
            ยืนยันรหัสผ่าน *
          </label>
          <input type="password" id="confirm_password" name="confirm_password" required
                 placeholder="พิมพ์รหัสผ่านอีกครั้ง"
                 class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-cyan-400 transition-colors">
        </div>
      </div>

      <div class="pt-2">
        <button type="submit" class="w-full btn-cyber-primary py-3 rounded-xl text-sm font-bold tracking-wider flex items-center justify-center gap-2">
          <i class="fa-solid fa-check"></i> ยืนยันการสมัครสมาชิก
        </button>
      </div>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-800 text-center">
      <p class="text-xs text-slate-400">
        มีบัญชีใช้งานอยู่แล้ว? 
        <a href="login.php" class="text-cyan-400 hover:underline font-semibold ml-1">เข้าสู่ระบบที่นี่</a>
      </p>
    </div>

  </div>
</div>

<script>
function validateRegisterForm(e) {
  const username = document.getElementById('username').value.trim();
  const fullName = document.getElementById('full_name').value.trim();
  const email = document.getElementById('email').value.trim();
  const pass = document.getElementById('password').value;
  const confirmPass = document.getElementById('confirm_password').value;

  if (!username || !fullName || !email || !pass || !confirmPass) {
    cyberAlert('กรอกข้อมูลไม่ครบ', 'กรุณากรอกข้อมูลในทุกช่องที่จำเป็น (*)', 'warning');
    e.preventDefault();
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    cyberAlert('อีเมลไม่ถูกต้อง', 'กรุณาระบุรูปแบบอีเมลที่ถูกต้อง', 'warning');
    e.preventDefault();
    return false;
  }

  if (pass.length < 6) {
    cyberAlert('รหัสผ่านสั้นเกินไป', 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร', 'warning');
    e.preventDefault();
    return false;
  }

  if (pass !== confirmPass) {
    cyberAlert('รหัสผ่านไม่ตรงกัน', 'รหัสผ่านและช่องยืนยันรหัสผ่านไม่ตรงกัน', 'warning');
    e.preventDefault();
    return false;
  }

  return true;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
