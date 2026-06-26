<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Analysis</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/analytics.css">
</head>

<body>
<div class="layout">
  <aside class="sidebar">
    <h2><img src="img/logo.png" alt="Money Tracker" class="logo-img"> Money Tracker</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analysis</a>
    <a href="achievements.php">Achievements</a>
  </aside>

  <main class="main">

    <div class="analytics-header">
      <div>
        <h1>Analytics</h1>
        <p class="subtitle">Track your spending, savings, and habits clearly.</p>
      </div>

      <div>
        <?php include 'includes/topbar.php'; ?>
      </div>
    </div>

    <div class="amount-cards">
      <div class="amount-card income-card">
        <p>Income</p>
        <h2 id="incomeValue">¥0</h2>
      </div>

      <div class="amount-card spend-card">
        <p>Spent</p>
        <h2 id="spendingValue">¥0</h2>
      </div>

      <div class="amount-card saving-card">
        <p>Saved</p>
        <h2 id="savingValue">¥0</h2>
      </div>
    </div>

    <div class="analytics-grid">

      <section class="chart-card">
        <h2>Spending Breakdown</h2>

        <div class="donut-layout">
          <div class="donut-chart" id="donutChart">
            <div class="donut-center">
              <strong id="donutTotal">¥0</strong>
              <span>Total Spent</span>
            </div>
          </div>

          <div class="donut-info">
            <div>
              <span><i class="dot basic-dot"></i> Basic Cost</span>
              <strong id="basicInfo">¥0</strong>
            </div>

            <div>
              <span><i class="dot fun-dot"></i> Entertainment</span>
              <strong id="funInfo">¥0</strong>
            </div>

            <div>
              <span><i class="dot emergency-dot"></i> Emergency</span>
              <strong id="emergencyInfo">¥0</strong>
            </div>
          </div>
        </div>
      </section>

      <section class="chart-card">
        <h2>Budget Overview</h2>

        <div class="rings">
          <div class="ring-box">
            <div id="basicRing" class="ring">
              <span id="basicRate">0%</span>
            </div>
            <p>🏠 Basic</p>
            <small id="basicAmount">¥0 / ¥100,000</small>
          </div>

          <div class="ring-box">
            <div id="funRing" class="ring">
              <span id="funRate">0%</span>
            </div>
            <p>🎮 Fun</p>
            <small id="funAmount">¥0 / ¥50,000</small>
          </div>

          <div class="ring-box">
            <div id="emergencyRing" class="ring">
              <span id="emergencyRate">0%</span>
            </div>
            <p>⚠️ Emergency</p>
            <small id="emergencyAmount">¥0 / ¥50,000</small>
          </div>
        </div>
      </section>

    </div>

    <section class="chart-card full-card">
      <div class="chart-title-row">
        <h2>Monthly Spending Trend</h2>
        <button type="button" onclick="saveCurrentMonthHistory()">Save This Month</button>
      </div>

      <div class="line-chart">
        <svg id="spendingSvg" viewBox="0 0 700 280"></svg>
      </div>
    </section>

    <section class="chart-card full-card">
      <h2>Monthly Savings Trend</h2>

      <div class="line-chart">
        <svg id="savingSvg" viewBox="0 0 700 280"></svg>
      </div>
    </section>

    <h2 class="section-title">Insights & Recommendations</h2>

    <div class="insight-cards">
      <div class="insight-card pink-card">
        <div class="insight-icon">📈</div>
        <p>Biggest Expense</p>
        <h3 id="biggestExpense">Checking...</h3>
        <strong id="biggestAmount">¥0</strong>
        <span>Try to reduce this category.</span>
      </div>

      <div class="insight-card purple-card">
        <div class="insight-icon">🎮</div>
        <p>Budget Alert</p>
        <h3 id="budgetAlert">Checking...</h3>
        <span id="budgetAlertText">Your budget status will appear here.</span>
      </div>

      <div class="insight-card green-card">
        <div class="insight-icon">💡</div>
        <p>Recommendation</p>
        <span id="recommendationText">Loading advice...</span>
      </div>
    </div>

    <section class="success-card">
      <div class="success-icon">🎯</div>
      <p>
        <strong>You’re on the right track!</strong><br>
        Keep tracking your spending and savings to reach your goals.
      </p>
      <button onclick="location.href='goals.php'">View Goals →</button>
    </section>

  </main>
</div>

<script>
function yen(n) {
  return "¥" + Number(n).toLocaleString();
}

function yenShort(n) {
  return "¥" + Math.round(Number(n) / 1000) + "k";
}

function pct(part, total) {
  if (total <= 0) return 0;
  return Math.round((part / total) * 100);
}

function getDashboardData() {
  const income = Number(localStorage.getItem("monthlyIncome")) || 200000;

  const basic = Number(localStorage.getItem("dashboardBasicCost")) || 0;
  const fun = Number(localStorage.getItem("dashboardEntertainmentCost")) || 0;
  const emergency = Number(localStorage.getItem("dashboardEmergencyCost")) || 0;

  const spending =
    Number(localStorage.getItem("dashboardTotalSpending")) ||
    basic + fun + emergency;

  const saving = Number(localStorage.getItem("dashboardSavedAmount")) || 0;
  const left = Number(localStorage.getItem("dashboardAmountLeft")) || 0;

  const basicBudget = Number(localStorage.getItem("monthlyBudget")) || 100000;
  const funBudget = Number(localStorage.getItem("entertainmentBudget")) || 50000;
  const emergencyBudget = Number(localStorage.getItem("emergencyBudget")) || 50000;

  return {
    income,
    basic,
    fun,
    emergency,
    spending,
    saving,
    left,
    basicBudget,
    funBudget,
    emergencyBudget
  };
}

function defaultHistory() {
  return [
    { month: "Jan", spending: 0, saving: 0 },
    { month: "Feb", spending: 0, saving: 0 },
    { month: "Mar", spending: 0, saving: 0 },
    { month: "Apr", spending: 0, saving: 0 },
    { month: "May", spending: 0, saving: 0 }
  ];
}

function getHistory() {
  let history = JSON.parse(localStorage.getItem("monthlyHistory")) || defaultHistory();

  const d = getDashboardData();
  const currentMonth = new Date().toLocaleString("en-US", { month: "short" });

  const exists = history.find(item => item.month === currentMonth);

  if (!exists) {
    history.push({
      month: currentMonth,
      spending: d.spending,
      saving: d.saving
    });
  } else {
    exists.spending = d.spending;
    exists.saving = d.saving;
  }

  localStorage.setItem("monthlyHistory", JSON.stringify(history));

  return history;
}

function saveCurrentMonthHistory() {
  const d = getDashboardData();
  const currentMonth = new Date().toLocaleString("en-US", { month: "short" });

  let history = JSON.parse(localStorage.getItem("monthlyHistory")) || [];

  const existing = history.find(item => item.month === currentMonth);

  if (existing) {
    existing.spending = d.spending;
    existing.saving = d.saving;
  } else {
    history.push({
      month: currentMonth,
      spending: d.spending,
      saving: d.saving
    });
  }

  localStorage.setItem("monthlyHistory", JSON.stringify(history));
  drawLineCharts(history);
  alert("This month’s analytics data has been saved!");
}

function setRing(ringId, textId, rate, color) {
  const safeRate = Math.min(rate, 100);
  const ring = document.getElementById(ringId);

  document.getElementById(textId).textContent = rate + "%";

  ring.style.background =
    `conic-gradient(${color} 0% ${safeRate}%, #e8edf3 ${safeRate}% 100%)`;
}

function updateTopCards(d) {
  document.getElementById("incomeValue").textContent = yen(d.income);
  document.getElementById("spendingValue").textContent = yen(d.spending);
  document.getElementById("savingValue").textContent = yen(d.saving);
}

function updateDonut(d) {
  document.getElementById("donutTotal").textContent = yen(d.spending);
  document.getElementById("basicInfo").textContent = yen(d.basic);
  document.getElementById("funInfo").textContent = yen(d.fun);
  document.getElementById("emergencyInfo").textContent = yen(d.emergency);

  if (d.spending <= 0) {
    document.getElementById("donutChart").style.background =
      "conic-gradient(#e8edf3 0deg 360deg)";
    return;
  }

  const basicPercent = pct(d.basic, d.spending);
  const funPercent = pct(d.fun, d.spending);

  const basicDeg = basicPercent * 3.6;
  const funDeg = funPercent * 3.6;

  document.getElementById("donutChart").style.background =
    `conic-gradient(
      #43b883 0deg ${basicDeg}deg,
      #ff4aa2 ${basicDeg}deg ${basicDeg + funDeg}deg,
      #ff3b30 ${basicDeg + funDeg}deg 360deg
    )`;
}

function updateBudgetRings(d) {
  const basicRate = pct(d.basic, d.basicBudget);
  const funRate = pct(d.fun, d.funBudget);
  const emergencyRate = pct(d.emergency, d.emergencyBudget);

  setRing("basicRing", "basicRate", basicRate, "#43b883");
  setRing("funRing", "funRate", funRate, "#ff4aa2");
  setRing("emergencyRing", "emergencyRate", emergencyRate, "#ff3b30");

  document.getElementById("basicAmount").textContent =
    `${yen(d.basic)} / ${yen(d.basicBudget)}`;

  document.getElementById("funAmount").textContent =
    `${yen(d.fun)} / ${yen(d.funBudget)}`;

  document.getElementById("emergencyAmount").textContent =
    `${yen(d.emergency)} / ${yen(d.emergencyBudget)}`;
}

function updateInsights(d) {
  const categories = [
    { name: "Basic Cost", value: d.basic, rate: pct(d.basic, d.basicBudget) },
    { name: "Entertainment", value: d.fun, rate: pct(d.fun, d.funBudget) },
    { name: "Emergency", value: d.emergency, rate: pct(d.emergency, d.emergencyBudget) }
  ];

  const biggest = categories.slice().sort((a,b) => b.value - a.value)[0];
  const overBudget = categories.find(cat => cat.rate > 100);

  document.getElementById("biggestExpense").textContent = biggest.name;
  document.getElementById("biggestAmount").textContent = yen(biggest.value);

  if (overBudget) {
    document.getElementById("budgetAlert").textContent =
      `${overBudget.name} Over Budget`;

    document.getElementById("budgetAlertText").textContent =
      `${overBudget.name} is at ${overBudget.rate}% of its budget.`;
  } else {
    document.getElementById("budgetAlert").textContent = "Budget Looks Good";
    document.getElementById("budgetAlertText").textContent =
      "Your spending is under control.";
  }

  document.getElementById("recommendationText").textContent =
    `Try reducing ${biggest.name} by ¥10,000 to save more easily.`;
}

function drawLineChart(svgId, history, key, color) {
  const svg = document.getElementById(svgId);
  svg.innerHTML = "";

  const width = 700;
  const height = 280;
  const leftPad = 45;
  const rightPad = 30;
  const topPad = 35;
  const bottomPad = 55;

  const maxValue = Math.max(...history.map(item => Number(item[key]) || 0), 10000);

  const stepX =
    history.length > 1
      ? (width - leftPad - rightPad) / (history.length - 1)
      : 0;

  const points = history.map((item, index) => {
    const x = leftPad + index * stepX;
    const value = Number(item[key]) || 0;
    const y =
      height - bottomPad -
      (value / maxValue) * (height - topPad - bottomPad);

    return { x, y, value, month: item.month };
  });

  const polyline = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
  polyline.setAttribute("class", "line");
  polyline.setAttribute("stroke", color);
  polyline.setAttribute("points", points.map(p => `${p.x},${p.y}`).join(" "));
  svg.appendChild(polyline);

  points.forEach(p => {
    const circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
    circle.setAttribute("cx", p.x);
    circle.setAttribute("cy", p.y);
    circle.setAttribute("r", "6");
    circle.setAttribute("stroke", color);
    svg.appendChild(circle);

    const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
    label.setAttribute("x", p.x - 20);
    label.setAttribute("y", p.y - 16);
    label.textContent = yenShort(p.value);
    svg.appendChild(label);

    const month = document.createElementNS("http://www.w3.org/2000/svg", "text");
    month.setAttribute("x", p.x - 15);
    month.setAttribute("y", height - 20);
    month.textContent = p.month;
    svg.appendChild(month);
  });
}

function drawLineCharts(history) {
  drawLineChart("spendingSvg", history, "spending", "#16a34a");
  drawLineChart("savingSvg", history, "saving", "#f6b500");
}

function updateAnalytics() {
  const d = getDashboardData();

  updateTopCards(d);
  updateDonut(d);
  updateBudgetRings(d);
  updateInsights(d);

  const history = getHistory();
  drawLineCharts(history);
}

document.addEventListener("DOMContentLoaded", updateAnalytics);
window.addEventListener("storage", updateAnalytics);
</script>

</body>
</html>