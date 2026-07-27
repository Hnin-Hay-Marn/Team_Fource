<?php
session_start();
require_once 'db.php';
$pdo = getDb();
ensureUsersTableExists($pdo);

$error = '';
$email = '';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        $sql = 'SELECT user_id, name, email, password_hash FROM users WHERE email = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        }

        $error = 'メールアドレスまたはパスワードが正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Log In – Money Tracker</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/login.css">
</head>
 
<body class="login-body">
 
<div class="login-wrapper">
 
  <div class="login-brand">
    <img src="img/logo.png" alt="Money Tracker" class="logo-img">
    <span>Money Tracker</span>
  </div>
 
  <div class="login-card">
    <h2>Welcome back</h2>
    <p class="login-subtitle">Log in to keep tracking your savings.</p>
 
    <?php if ($error): ?>
      <div class="login-banner-error show">
        ⚠️ <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    <?php endif; ?>
 
    <form id="loginForm" method="post" action="login.php" novalidate>
 
      <div class="login-form-row" id="emailRow">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="username" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
        <span class="field-error" id="emailError">Enter a valid email address.</span>
      </div>
 
      <div class="login-form-row" id="passwordRow">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password">
        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">👁️</button>
        <span class="field-error" id="passwordError">Password must be at least 6 characters.</span>
      </div>
 
      <div class="login-options">
        <label class="remember-me">
          <input type="checkbox" id="rememberMe">
          Remember me
        </label>
        <a href="#" class="forgot-link" id="forgotLink">Forgot password?</a>
      </div>
 
      <button type="submit" class="btn-login" id="loginBtn">Log In</button>
    </form>
 
    <div class="login-divider">or</div>
 
    <p class="login-footer">
      New to Money Tracker? <a href="signup.php">Create an account</a>
    </p>
  </div>
 
</div>
 
<div id="toast">✅ Logged in!</div>
 
</body>
</html>
 