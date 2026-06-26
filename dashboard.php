<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Money Saver App</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<div class="layout">

  <aside class="sidebar">
    <h2><img src="img/logo.png" alt="Money Tracker" class="logo-img"> Money Tracker</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analytics</a>
    <a href="achievements.php">Achievements</a>
  </aside>

  <div class="main">

    <div class="header">
      <div class="greeting-container">
        <h2 id="greeting">Hello, Ruby 👋</h2>
        <div class="header-streak">🔥 <span id="headerStreak">0</span></div>
      </div>

      <div class="top-actions">
        <div id="google_translate_element" class="google-translate"></div>
        <button onclick="goPage('profile.php')">👤 Profile</button>
      </div>
    </div>

    <div class="summary-grid">

      <div class="summary-card" onclick="goPage('monthly_income.php')" style="cursor:pointer;">
        <div class="icon-box green-bg">💵</div>
        <div class="info">
          <p>Monthly Income</p>
          <h2 id="monthly-income" class="income">¥200,000</h2>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box red-bg">📉</div>
        <div class="info">
          <p>Total Spending</p>
          <h2 id="total-spending" class="total_spending">¥0</h2>
          <div class="small-bar"><div id="spendingPercent"></div></div>
          <span id="spendingRate">0% of income</span>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box yellow-bg">🐷</div>
        <div class="info">
          <p>This Month Savings</p>
          <input type="number" id="monthly-savings-input" placeholder="Enter amount">
          <div class="small-bar"><div id="savingPercent"></div></div>
          <span id="savingRate">0% of income</span>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box blue-bg">💳</div>
        <div class="info">
          <p>Amount Left</p>
          <h2 id="amount-left" class="amount_left">¥0</h2>
          <div class="small-bar"><div id="leftPercent"></div></div>
          <span id="leftRate">0% of income</span>
        </div>
      </div>

    </div>

    <div class="focus-card" id="focus-card">

    <div class="focus-left">
        <div class="focus-icon">⚠️</div>

        <div class="focus-content">
            <h3>Spending Focus</h3>

            <p id="focusText">
                Checking your spending...
            </p>
        </div>
    </div>

    <button class="analysis-btn"
            onclick="goPage('analytics.php')">
        View Analysis →
    </button>

</div>


    <!-- Basic -->
    <div class="budget-item" onclick="goPage('basic.php')">

        <div class="budget-header">
            <span>🏠 Basic Cost</span>
            <span id="basicCostAmount">¥0 / ¥100,000</span>
        </div>

        <div class="bar">
            <div id="basicFill" class="green-fill"></div>
        </div>

        <div id="basicStatus" class="budget-status">
            On Track
        </div>

    </div>


    <!-- Entertainment -->
    <div class="budget-item" onclick="goPage('entertainment.php')">

        <div class="budget-header">
            <span>🎮 Entertainment</span>
            <span id="entertainmentAmount">¥0 / ¥50,000</span>
        </div>

        <div class="bar">
            <div id="entertainmentFill" class="pink-fill"></div>
        </div>

        <div id="entertainmentStatus" class="budget-status">
            On Track
        </div>

    </div>


    <!-- Emergency -->
    <div class="budget-item" onclick="goPage('emergency.php')">

        <div class="budget-header">
            <span>⚠️ Emergency</span>
            <span id="emergencyAmount">¥0 / ¥50,000</span>
        </div>

        <div class="bar">
            <div id="emergencyFill" class="red-fill"></div>
        </div>

        <div id="emergencyStatus" class="budget-status">
            On Track
        </div>

    </div>

</div>

  

  </div>
</div>

<script>
function goPage(page){
  window.location.href = page;
}

function formatYen(n){
  return "¥" + Number(n).toLocaleString();
}

function percent(part, total){
  if (total <= 0) return 0;
  return Math.round((part / total) * 100);
}

function updateGreeting(){
  const firstName = localStorage.getItem("profileFirstName") || "Ruby";
  const lastName = localStorage.getItem("profileLastName") || "";

  document.getElementById("greeting").textContent =
    `Hello, ${firstName} ${lastName} 👋`;
}

function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: "en",
    includedLanguages: "en,ja,my,ko,zh-CN,th,vi,id,ne",
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, "google_translate_element");
}

function updateStreak() {
  let streak = Number(localStorage.getItem("streak")) || 0;
  let lastVisit = localStorage.getItem("lastVisit");
  const today = new Date().toDateString();

  if (!lastVisit) {
    streak = 1;
  } else {
    const diffDays = Math.floor(
      (new Date(today) - new Date(lastVisit)) / (1000 * 60 * 60 * 24)
    );

    if (diffDays === 1) streak += 1;
    else if (diffDays > 1) streak = 1;
  }

  localStorage.setItem("streak", streak);
  localStorage.setItem("lastVisit", today);
  document.getElementById("headerStreak").textContent = streak;
}

function updateStatus(id, rate) {
  const el = document.getElementById(id);

  if (rate < 70) {
    el.textContent = "On Track";
    el.className = "budget-status status-good";
  } else if (rate <= 100) {
    el.textContent = "Watch";
    el.className = "budget-status status-watch";
  } else {
    el.textContent = "Over Budget";
    el.className = "budget-status status-danger";
  }
}

function updateBudgetDisplays() {
  const basicBudget = Number(localStorage.getItem("monthlyBudget")) || 100000;
  const entertainmentBudget = Number(localStorage.getItem("entertainmentBudget")) || 50000;
  const emergencyBudget = Number(localStorage.getItem("emergencyBudget")) || 50000;

  const basicCost = Number(localStorage.getItem("dashboardBasicCost")) || 0;
  const entertainmentCost = Number(localStorage.getItem("dashboardEntertainmentCost")) || 0;
  const emergencyCost = Number(localStorage.getItem("dashboardEmergencyCost")) || 0;

  const basicRate = percent(basicCost, basicBudget);
  const entertainmentRate = percent(entertainmentCost, entertainmentBudget);
  const emergencyRate = percent(emergencyCost, emergencyBudget);

  document.getElementById("basicCostAmount").textContent =
    `${formatYen(basicCost)} / ${formatYen(basicBudget)}`;

  document.getElementById("entertainmentAmount").textContent =
    `${formatYen(entertainmentCost)} / ${formatYen(entertainmentBudget)}`;

  document.getElementById("emergencyAmount").textContent =
    `${formatYen(emergencyCost)} / ${formatYen(emergencyBudget)}`;

  document.getElementById("basicFill").style.width = Math.min(basicRate, 100) + "%";
  document.getElementById("entertainmentFill").style.width = Math.min(entertainmentRate, 100) + "%";
  document.getElementById("emergencyFill").style.width = Math.min(emergencyRate, 100) + "%";

  updateStatus("basicStatus", basicRate);
  updateStatus("entertainmentStatus", entertainmentRate);
  updateStatus("emergencyStatus", emergencyRate);

  updateFocusCard();
}

function updateMoneySummary() {
  const income = Number(localStorage.getItem("monthlyIncome")) || 200000;

  const basicCost = Number(localStorage.getItem("dashboardBasicCost")) || 0;
  const entertainmentCost = Number(localStorage.getItem("dashboardEntertainmentCost")) || 0;
  const emergencyCost = Number(localStorage.getItem("dashboardEmergencyCost")) || 0;

  const totalSpending = basicCost + entertainmentCost + emergencyCost;
  let monthlySavings = Number(localStorage.getItem("dashboardSavedAmount")) || 0;

  let amountLeft = income - totalSpending - monthlySavings;

  if (amountLeft < 0) {
    monthlySavings += amountLeft;
    if (monthlySavings < 0) monthlySavings = 0;
    amountLeft = 0;
  }

  const totalSavings = monthlySavings + amountLeft;

  const spendingRate = percent(totalSpending, income);
  const savingRate = percent(monthlySavings, income);
  const leftRate = percent(amountLeft, income);

  document.getElementById("monthly-income").textContent = formatYen(income);
  document.getElementById("total-spending").textContent = formatYen(totalSpending);
  document.getElementById("amount-left").textContent = formatYen(amountLeft);
  document.getElementById("monthly-savings-input").value = monthlySavings;

  document.getElementById("spendingPercent").style.width = Math.min(spendingRate, 100) + "%";
  document.getElementById("savingPercent").style.width = Math.min(savingRate, 100) + "%";
  document.getElementById("leftPercent").style.width = Math.min(leftRate, 100) + "%";

  document.getElementById("spendingRate").textContent = `${spendingRate}% of income`;
  document.getElementById("savingRate").textContent = `${savingRate}% of income`;
  document.getElementById("leftRate").textContent = `${leftRate}% of income`;

  localStorage.setItem("dashboardTotalSpending", totalSpending);
  localStorage.setItem("dashboardAmountLeft", amountLeft);
  localStorage.setItem("dashboardSavedAmount", monthlySavings);
  localStorage.setItem("totalSavings", totalSavings);
}

function updateFocusCard() {
  const categories = [
    {
      name: "Basic Cost",
      cost: Number(localStorage.getItem("dashboardBasicCost")) || 0,
      budget: Number(localStorage.getItem("monthlyBudget")) || 100000
    },
    {
      name: "Entertainment",
      cost: Number(localStorage.getItem("dashboardEntertainmentCost")) || 0,
      budget: Number(localStorage.getItem("entertainmentBudget")) || 50000
    },
    {
      name: "Emergency",
      cost: Number(localStorage.getItem("dashboardEmergencyCost")) || 0,
      budget: Number(localStorage.getItem("emergencyBudget")) || 50000
    }
  ];

  let worst = categories[0];

  categories.forEach(cat => {
    if (percent(cat.cost, cat.budget) > percent(worst.cost, worst.budget)) {
      worst = cat;
    }
  });

  const over = worst.cost - worst.budget;
  const focusText = document.getElementById("focusText");
  const focusCard = document.getElementById("focus-card");

  if (over > 0) {
    focusCard.className = "focus-card danger-focus";
    focusText.textContent = `${worst.name} is over budget by ${formatYen(over)}.`;
  } else {
    focusCard.className = "focus-card good-focus";
    focusText.textContent = `You're on track. Keep your spending under control.`;
  }
}

if (!localStorage.getItem("monthlySavingGoal")) {
  localStorage.setItem("monthlySavingGoal", 50000);
}

document.addEventListener("DOMContentLoaded", function(){
  updateGreeting();
  updateStreak();
  updateBudgetDisplays();
  updateMoneySummary();

  const savingsInput = document.getElementById("monthly-savings-input");

  savingsInput.addEventListener("input", function(){
    localStorage.setItem("dashboardSavedAmount", this.value);
    updateMoneySummary();
  });
});

window.addEventListener("storage", function(e) {
  updateGreeting();
  updateBudgetDisplays();
  updateMoneySummary();
});
</script>

<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

</body>
</html>