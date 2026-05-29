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
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">貯金目標</a>
    <a href="analytics.php">分析</a>
    <a href="achievements.php">達成バッジ</a>
  </aside>
<div class="main">

  <!-- Header -->
  <div class="header">
    <h2>Hello, Ruby 👋</h2>

    <div class="top-actions">
      <button onclick="goPage('profile.php')">👤 Profile</button>
    </div>
  </div>

  <!-- Top Cards -->
  <div class="top">
    <div class="card">
      <p>Monthly Income</p>
      <div id="monthly-income" class="income" title="Click to edit">¥200,000</div>
    </div>

    <div class="card">
      <p>Savings</p>
      <div class="income">¥50,000</div>
    </div>

    <div class="card">
      <p>Daily Streak</p>
      <div class="streak">12 Days 🔥</div>
    </div>
  </div>

  <!-- Budget Overview -->
  <div class="card budget">
    <h3>Budget Overview</h3>

    <div class="budget-card" onclick="goPage('basic.php')">
      <div class="budget-top">
        <span>🏠 Basic Cost</span>
        <span>¥60,000 / ¥100,000</span>
      </div>
      <div class="bar"><div class="fill green"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('fun.php')">
      <div class="budget-top">
        <span>🎮 Entertainment</span>
        <span>¥20,000 / ¥50,000</span>
      </div>
      <div class="bar"><div class="fill pink"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('emergency.php')">
      <div class="budget-top">
        <span>⚠️ Emergency</span>
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

function like(){
  alert("❤️ You liked your savings progress!");
}
// Monthly income: load/save and allow inline editing (stored in localStorage)
document.addEventListener('DOMContentLoaded', function(){
  var el = document.getElementById('monthly-income');
  if(!el) return;

  function formatYen(n){
    return '¥' + Number(n).toLocaleString();
  }

  // Load saved value
  var saved = localStorage.getItem('monthlyIncome');
  if(saved && saved.match(/^\d+$/)){
    el.textContent = formatYen(saved);
  }

  // Click to edit
  el.style.cursor = 'pointer';
  el.addEventListener('click', function(){
    var current = localStorage.getItem('monthlyIncome') || el.textContent.replace(/[^0-9]/g,'');
    var input = prompt('Enter monthly income (numbers only):', current);
    if(input === null) return; // cancelled
    var digits = input.replace(/[^0-9]/g,'');
    if(digits === ''){ alert('Please enter a valid number'); return; }
    localStorage.setItem('monthlyIncome', digits);
    el.textContent = formatYen(digits);
  });
});
</script>
</div>
</body>
</html>