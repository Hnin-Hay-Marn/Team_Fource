<?php
session_start();
require_once 'db.php';

$error = '';
$email = '';
$firstName = '';
$lastName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getDb();
    ensureUsersTableExists($pdo);

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $agreeTerms = isset($_POST['agreeTerms']);

    if ($firstName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!$agreeTerms) {
        $error = 'You must agree to the Terms & Privacy Policy.';
    } else {
        $name = trim($firstName . ' ' . $lastName);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)';

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $passwordHash, PDO::PARAM_STR);
            $stmt->execute();
            $pdo->commit();

            header('Location: login.php');
            exit();
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if ($e->errorInfo[1] === 1062) {
                $error = 'This email is already registered.';
            } else {
                $error = 'Unable to create account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Account – Money Tracker</title>
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
    <h2>Create your account</h2>
    <p class="login-subtitle">Start tracking your savings in minutes.</p>
 
    <?php if ($error): ?>
      <div class="login-banner-error show">
        ⚠️ <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    <?php endif; ?>
 
    <form id="signupForm" method="post" action="signup.php" novalidate>
 
      <div class="login-form-row-2col">
        <div class="login-form-row" id="firstNameRow">
          <label for="firstName">First Name</label>
          <input type="text" id="firstName" name="firstName" placeholder="First name" autocomplete="given-name" value="<?php echo htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>">
          <span class="field-error" id="firstNameError">Enter your first name.</span>
        </div>
        <div class="login-form-row" id="lastNameRow">
          <label for="lastName">Last Name</label>
          <input type="text" id="lastName" name="lastName" placeholder="Last name" autocomplete="family-name" value="<?php echo htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
 
      <div class="login-form-row" id="emailRow">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
        <span class="field-error" id="emailError">Enter a valid email address.</span>
      </div>
 
      <div class="login-form-row" id="passwordRow">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" autocomplete="new-password">
        <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">👁️</button>
        <span class="field-error" id="passwordError">Password must be at least 6 characters.</span>
      </div>
 
      <div class="login-form-row" id="confirmPasswordRow">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" autocomplete="new-password">
        <span class="field-error" id="confirmPasswordError">Passwords don't match.</span>
      </div>
 
      <div class="login-options" style="justify-content: flex-start;">
        <label class="remember-me">
          <input type="checkbox" id="agreeTerms" name="agreeTerms">
          I agree to the Terms & Privacy Policy
        </label>
      </div>
      <span class="field-error" id="termsError" style="display:block; margin: -14px 0 18px;">Please accept the terms to continue.</span>
 
      <button type="submit" class="btn-login" id="signupBtn">Create Account</button>
    </form>
 
    <div class="login-divider">or</div>
 
    <p class="login-footer">
      Already have an account? <a href="login.php">Log in</a>
    </p>
  </div>
 
</div>
 
<div id="toast">✅ Account created!</div>
 
</body>
</html>
 