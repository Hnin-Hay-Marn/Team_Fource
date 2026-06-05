<?php 


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Money Saver App</title>

<!-- Link CSS -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<div class="layout">
  <aside class="sidebar">
    <h2><span class="logo-icon">💸</span> FOURCE</h2>
    <a id="navDashboard" href="dashboard.php">Dashboard</a>
    <a id="navGoals" href="goals.php">Savings Goals</a>
    <a id="navAnalytics" href="analytics.php">Analytics</a>
    <a id="navAchievements" href="achievements.php">Achievements</a>
  </aside>
<div class="main">

  <!-- Header -->
  <div class="header">
    <h2 id="greeting">Hello, Ruby 👋</h2>

    <div class="top-actions">
      <div id="google_translate_element" class="google-translate"></div>
      <button id="profileBtn" onclick="goPage('profile.php')">👤 Profile</button>
    </div>
  </div>

  <!-- Top Cards -->
  <div class="top">
    <div class="card" onclick="goPage('monthly_income.php')" style="cursor:pointer;">
      <p id="incomeCardTitle">Monthly Income</p>
      <div id="monthly-income" class="income" title="Click to open monthly income page">¥200,000</div>
    </div>

    <div class="card">
      <p id="savingsCardTitle">Savings</p>
      <div class="income">¥50,000</div>
    </div>

    <div class="card">
      <p id="streakCardTitle">Daily Streak</p>
      <div class="streak">12 Days 🔥</div>
    </div>
  </div>

  <!-- Budget Overview -->
  <div class="card budget">
    <h3 id="budgetHeading">Budget Overview</h3>

    <div class="budget-card" onclick="goPage('basic.php')">
      <div class="budget-top">
        <span id="basicCostLabel">🏠 Basic Cost</span>
        <span>¥60,000 / ¥100,000</span>
      </div>
      <div class="bar"><div class="fill green"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('fun.php')">
      <div class="budget-top">
        <span id="entertainmentLabel">🎮 Entertainment</span>
        <span>¥20,000 / ¥50,000</span>
      </div>
      <div class="bar"><div class="fill pink"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('emergency.php')">
      <div class="budget-top">
        <span id="emergencyLabel">⚠️ Emergency</span>
        <span>¥15,000 / ¥50,000</span>
      </div>
      <div class="bar"><div class="fill red"></div></div>
    </div>

  </div>

</div>

<script>
function goPage(page){
  window.location.href = page;
}

function formatYen(n){
  return '¥' + Number(n).toLocaleString();
}

function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'en,ja,my,ko,zh-CN,th,vi,id,ne',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}

document.addEventListener('DOMContentLoaded', function(){
  var el = document.getElementById('monthly-income');
  if(!el) return;

  var saved = localStorage.getItem('monthlyIncome');
  if(saved && saved.match(/^\d+$/)){
    el.textContent = formatYen(saved);
  }

  el.style.cursor = 'pointer';
  el.addEventListener('click', function(){
    goPage('monthly_income.php');
  });
});
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</div>
</body>
</html>