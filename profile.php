<?php 
require_once 'db.php';

$pdo = getDb();

$stmt = $pdo->prepare("
SELECT value
FROM settings
WHERE name='total_savings'
");

$stmt->execute();

$totalSavings = (int)$stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile – FOURCE</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet" href="css/profile.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
 
<body>
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/sidebar.php'; ?>

<div class="main container-fluid mt-4">

<div class="header">
<div class="top-actions">
<div id="google_translate_element" class="google-translate"></div>
<!-- <button onclick="goPage('dashboard.php')">🏠 Dashboard</button> -->
</div>
</div>
 
    <div class="profile-wrapper">
 
      <!-- ── Centered Avatar Banner ── -->
<div class="avatar-banner">
 
        <!-- Avatar with upload -->
<div class="avatar-glow-ring" onclick="triggerAvatarUpload()" title="Change photo">
<div class="avatar-circle">
<span id="avatarEmoji">👤</span>
<div class="avatar-edit-hint">CHANGE</div>
</div>
</div>
<input type="file" id="avatarInput" accept="image/*" onchange="handleAvatarChange(event)">
 
        <!-- Name + streak -->
<div class="banner-name-row">
<span id="bannerName">Ruby</span>
<span class="streak-badge">🔥 <span id="bannerStreak">0</span></span>
</div>
 
        <!-- Total savings -->
<div class="banner-savings">
    <span class="savings-label">💰 Total Savings</span>
    <span class="savings-amount" id="bannerSavings">
        ¥<?= number_format($totalSavings) ?>
    </span>
</div>



    <div class="mini-stat">
        📅 <span id="bannerMember">Member since June 2025</span>
    </div>
</div>
 
        <!-- Member since -->
<div class="banner-meta" id="bannerMember">Member since June 2025</div>
</div>
 
      <!-- ── Personal Information Card ── -->
<div class="section-card">
<h3>✏️ Personal Information</h3>
 
        <div class="form-row-2col">
<div class="form-row">
<label>First Name</label>
<input type="text" id="firstName" placeholder="First name">
</div>
<div class="form-row">
<label>Last Name</label>
<input type="text" id="lastName" placeholder="Last name">
</div>
</div>
 
        <div class="form-row">
<label>Email</label>
<input type="email" id="emailField" placeholder="you@example.com">
</div>
 
        <div class="form-row-2col">
<div class="form-row">
<label>Currency</label>
<select id="currencySelect">
<option value="JPY">¥ Japanese Yen (JPY)</option>
<option value="USD">$ US Dollar (USD)</option>
<option value="EUR">€ Euro (EUR)</option>
<option value="GBP">£ British Pound (GBP)</option>
<option value="THB">฿ Thai Baht (THB)</option>
<option value="KRW">₩ Korean Won (KRW)</option>
<option value="MYR">RM Malaysian Ringgit (MYR)</option>
<option value="NPR">Rs Nepalese Rupee (NPR)</option>
</select>
</div>
<div class="form-row">
<label>Language</label>
<select id="languageSelect">
<option value="en">English</option>
<option value="ja">日本語</option>
<option value="my">မြန်မာဘာသာ</option>
<option value="ko">한국어</option>
<option value="zh">中文</option>
<option value="th">ภาษาไทย</option>
<option value="vi">Tiếng Việt</option>
<option value="id">Bahasa Indonesia</option>
<option value="ne">नेपाली</option>
</select>
</div>
</div>
 
        <button class="btn-save" onclick="saveProfile()">Save Changes</button>
        <button class="btn-save" onclick="window.location.href='logout.php'">Logout</button>
</div>
 
    </div><!-- /profile-wrapper -->
</div><!-- /main -->
</div><!-- /layout -->
 
<div id="toast">✅ Saved!</div>
 
<script>

function goPage(p) { window.location.href = p; }
 
function loadProfile() {

  const fn = localStorage.getItem('profileFirstName') || 'Ruby';
  const ln = localStorage.getItem('profileLastName') || '';
  const em = localStorage.getItem('profileEmail') || 'ruby@example.com';

  document.getElementById('firstName').value = fn;
  document.getElementById('lastName').value = ln;
  document.getElementById('emailField').value = em;

  document.getElementById('bannerName').textContent =
      (fn + ' ' + ln).trim() || 'Ruby';

  document.getElementById('currencySelect').value =
      localStorage.getItem('profileCurrency') || 'JPY';

  document.getElementById('languageSelect').value =
      localStorage.getItem('profileLanguage') || 'en';

  // Streak
  const streak = localStorage.getItem('streak') || '0';
  document.getElementById('bannerStreak').textContent = streak;

  // Avatar
  const saved = localStorage.getItem('profileAvatar');
  if (saved) {
      applyAvatar(saved);
  }

  // Member since
  let since = localStorage.getItem('memberSince');

  if (!since) {
      since = new Date().toLocaleDateString('en-US', {
          month: 'long',
          year: 'numeric'
      });
      localStorage.setItem('memberSince', since);
  }

  document.getElementById('bannerMember').textContent =
      'Member since ' + since;
}
 
function applyAvatar(src) {

  const circle = document.querySelector('.avatar-circle');

  document.getElementById('avatarEmoji').style.display = 'none';

  let img = circle.querySelector('img');

  if (!img) {

    img = document.createElement('img');

    img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;position:absolute;top:0;left:0;';

    circle.insertBefore(img, circle.firstChild);

  }

  img.src = src;

}
 
function triggerAvatarUpload() {

  document.getElementById('avatarInput').click();

}

function handleAvatarChange(e) {

  const file = e.target.files[0];

  if (!file) return;

  const reader = new FileReader();

  reader.onload = function (ev) {

    localStorage.setItem('profileAvatar', ev.target.result);

    applyAvatar(ev.target.result);

    showToast('📸 Photo updated!');

  };

  reader.readAsDataURL(file);

}
 
function saveProfile() {

  const fn   = document.getElementById('firstName').value.trim();

  const ln   = document.getElementById('lastName').value.trim();

  const em   = document.getElementById('emailField').value.trim();

  const cur  = document.getElementById('currencySelect').value;

  const lang = document.getElementById('languageSelect').value;
 
  localStorage.setItem('profileFirstName', fn);

  localStorage.setItem('profileLastName',  ln);

  localStorage.setItem('profileEmail',     em);

  localStorage.setItem('profileCurrency',  cur);

  localStorage.setItem('profileLanguage',  lang);
 
  document.getElementById('bannerName').textContent = (fn + ' ' + ln).trim() || 'User';

  showToast('✅ Profile saved!');

}
 
function showToast(msg) {

  const t = document.getElementById('toast');

  t.textContent = msg;

  t.classList.add('show');

  setTimeout(() => t.classList.remove('show'), 2400);

}
 
function googleTranslateElementInit() {

  new google.translate.TranslateElement({

    pageLanguage: 'en',

    includedLanguages: 'en,ja,my,ko,zh-CN,th,vi,id,ne',

    layout: google.translate.TranslateElement.InlineLayout.SIMPLE

  }, 'google_translate_element');

}
 
document.addEventListener('DOMContentLoaded', loadProfile);
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 