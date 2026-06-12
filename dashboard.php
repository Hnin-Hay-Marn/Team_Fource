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
      <div id="streak">0 Days 🔥</div>
    </div>
  </div>

  <!-- Budget Overview -->
  <div class="card budget">
    <h3 id="budgetHeading">Budget Overview</h3>

    <div class="budget-card" onclick="goPage('basic.php')">
      <div class="budget-top">
        <span id="basicCostLabel">🏠 Basic Cost</span>
        <span id="basicCostAmount">¥0 / ¥100,000</span>
      </div>
      <div class="bar"><div class="fill green"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('entertainment.php')">
      <div class="budget-top">
        <span id="entertainmentLabel">🎮 Entertainment</span>
        <span id="entertainmentAmount">¥0 / ¥50,000</span>
      </div>
      <div class="bar"><div class="fill pink"></div></div>
    </div>

    <div class="budget-card" onclick="goPage('emergency.php')">
      <div class="budget-top">
        <span id="emergencyLabel">⚠️ Emergency</span>
        <span id="emergencyAmount">¥0 / ¥50,000</span>
      </div>
      <div class="bar"><div class="fill red"></div></div>
    </div>

  </div>

</div>

<script>
  //daily streak start
  let streak = 0;
let lastVisit = localStorage.getItem("lastVisit");

function updateStreak() {
    const today = new Date().toDateString();

    if (!lastVisit) {
        streak = 1;
    } else {
        const lastDate = new Date(lastVisit);
        const currentDate = new Date(today);

        const diffDays = Math.floor(
            (currentDate - lastDate) / (1000 * 60 * 60 * 24)
        );

        if (diffDays === 1) {
            streak = Number(localStorage.getItem("streak")) + 1;
        } else if (diffDays === 0) {
            streak = Number(localStorage.getItem("streak"));
        } else {
            streak = 1;
        }
    }

    localStorage.setItem("streak", streak);
    localStorage.setItem("lastVisit", today);

    document.getElementById("streak").textContent =
        `🔥 ${streak} days`;
}

updateStreak();




  //daily streak end
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

// Update basic cost budget display
function updateBasicCostBudget() {
  var budgetAmount = localStorage.getItem('monthlyBudget') ;
  var basicCostSpan = document.getElementById('basicCostAmount');
  if(basicCostSpan) {
    basicCostSpan.textContent = '¥' + Number(localStorage.getItem('dashboardBasicCost') || 0).toLocaleString() + ' / ¥' + Number(budgetAmount).toLocaleString();
  }
}

// Update all budget displays
function updateBudgetDisplays() {
  // Entertainment
  var entertainmentBudget = localStorage.getItem('entertainmentBudget') || '50000';
  var entertainmentSpan = document.getElementById('entertainmentAmount');
  if(entertainmentSpan) {
    entertainmentSpan.textContent = '¥' + Number(localStorage.getItem('dashboardEntertainmentCost') || 0).toLocaleString() + ' / ¥' + Number(entertainmentBudget).toLocaleString();
  }
  
  // Emergency
  var emergencyBudget = localStorage.getItem('emergencyBudget') || '50000';
  var emergencySpan = document.getElementById('emergencyAmount');
  if(emergencySpan) {
    emergencySpan.textContent = '¥' + Number(localStorage.getItem('dashboardEmergencyCost') || 0).toLocaleString() + ' / ¥' + Number(emergencyBudget).toLocaleString();
  }
  
  // Update basic too
  updateBasicCostBudget();
}

document.addEventListener('DOMContentLoaded', function(){
  // Update budgets on page load
  updateBudgetDisplays();

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

// Listen for storage changes from other tabs/windows
window.addEventListener('storage', function(e) {
  if(e.key === 'monthlyBudget' || e.key === 'entertainmentBudget' || e.key === 'emergencyBudget' || e.key === 'dashboardEntertainmentCost' || e.key === 'dashboardEmergencyCost' || e.key === 'dashboardBasicCost') {
    updateBudgetDisplays();
  }
});
localStorage.setItem("dashboardSavedAmount", 50000);
localStorage.setItem("monthlySavingGoal", 50000);
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</div>
</body>
</html>