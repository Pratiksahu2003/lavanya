<?php
/**
 * LAVANYAA CREATION — Admin Login
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../functions/settings.php';
require_once __DIR__ . '/../functions/uploads.php';

if (isAdminLoggedIn()) { header('Location: ' . BASE_URL . '/admin/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!$email || !$password) {
        $error = 'Please enter both email and password.';
    } elseif (!adminLogin($email, $password)) {
        $error = 'Invalid credentials. Please try again.';
        sleep(1);
    } else {
        header('Location: ' . BASE_URL . '/admin/dashboard.php'); exit;
    }
}
$brandLogo = getSettingImage('company_logo', '/assets/images/lc-logo.png');
$favicon   = getSettingImage('favicon', '/assets/images/lc-logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login — Lavanyaa Creation CMS</title>
  <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .lc-login-left {
      background: #161616;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 60px 48px;
      position: relative;
      overflow: hidden;
    }
    .lc-login-left::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at 50% 50%, rgba(199,168,109,.12) 0%, transparent 65%);
    }
    .lc-login-left img { width: 200px; position: relative; z-index: 1; }
    .lc-login-left h1 {
      font-size: 1.5rem;
      font-weight: 300;
      color: #fff;
      text-align: center;
      margin: 24px 0 8px;
      letter-spacing: .04em;
      position: relative;
      z-index: 1;
    }
    .lc-login-left p {
      font-size: .72rem;
      letter-spacing: .22em;
      text-transform: uppercase;
      color: #C7A86D;
      text-align: center;
      position: relative;
      z-index: 1;
    }
    .lc-login-quote {
      position: absolute;
      bottom: 40px;
      font-family: 'Inter', sans-serif;
      font-size: .78rem;
      color: rgba(255,255,255,.3);
      text-align: center;
      font-style: italic;
      max-width: 280px;
      z-index: 1;
    }
    .lc-login-right {
      background: #F4F5F7;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px;
    }
    .lc-login-box {
      width: 100%;
      max-width: 400px;
    }
    .lc-login-box h2 {
      font-size: 1.5rem;
      font-weight: 800;
      color: #101828;
      margin: 0 0 6px;
      letter-spacing: -.02em;
    }
    .lc-login-box .subtitle {
      font-size: .84rem;
      color: #667085;
      margin-bottom: 36px;
    }
    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block;
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #344054;
      margin-bottom: 7px;
    }
    .form-control {
      display: block;
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #D0D5DD;
      border-radius: 8px;
      font-size: .9rem;
      font-family: inherit;
      color: #101828;
      background: #fff;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus { border-color: #6E4B3A; box-shadow: 0 0 0 3px rgba(110,75,58,.1); }
    .pw-wrap { position: relative; }
    .pw-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #667085; padding: 4px; font-size: 1rem; }
    .btn-login {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      background: #6E4B3A;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 13px;
      font-size: .9rem;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      letter-spacing: .02em;
      transition: background .2s;
      margin-top: 8px;
    }
    .btn-login:hover { background: #4E3428; }
    .alert-error {
      background: rgba(217,45,32,.06);
      color: #B42318;
      border: 1px solid rgba(217,45,32,.2);
      border-radius: 8px;
      padding: 12px 16px;
      font-size: .84rem;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .back-link {
      display: block;
      text-align: center;
      margin-top: 24px;
      font-size: .8rem;
      color: #667085;
    }
    .back-link a { color: #6E4B3A; font-weight: 600; }
    @media (max-width: 768px) {
      body { grid-template-columns: 1fr; }
      .lc-login-left { display: none; }
      .lc-login-right { padding: 32px 20px; }
    }
  </style>
</head>
<body>
  <div class="lc-login-left">
    <img src="<?php echo htmlspecialchars($brandLogo); ?>" alt="Lavanyaa Creation">
    <h1>Lavanyaa Creation</h1>
    <p>Premium Furniture Services</p>
    <div class="lc-login-quote">"Great spaces deserve exceptional furniture."</div>
  </div>

  <div class="lc-login-right">
    <div class="lc-login-box">
      <h2>Welcome Back</h2>
      <p class="subtitle">Sign in to the Lavanyaa Creation admin panel.</p>

      <?php if ($error): ?>
      <div class="alert-error"><i class="bi bi-exclamation-circle-fill"></i><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" class="form-control"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                 placeholder="admin@example.com" required autofocus>
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="pw-wrap">
            <input type="password" name="password" id="pw-field" class="form-control" placeholder="••••••••" required>
            <button type="button" class="pw-toggle" onclick="var f=document.getElementById('pw-field');f.type=f.type==='password'?'text':'password';this.innerHTML=f.type==='password'?'<i class=\'bi bi-eye\'></i>':'<i class=\'bi bi-eye-slash\'></i>';">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right"></i> Sign In</button>
      </form>

      <div class="back-link"><a href="<?php echo BASE_URL; ?>/index.php">← Back to Website</a></div>
    </div>
  </div>
</body>
</html>
