<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monthly Income | Money Saver App</title>

<!-- Link CSS -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
<div class="layout">
  <aside class="sidebar">
    <h2><span class="logo-icon">💸</span> FOURCE</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analytics</a>
    <a href="achievements.php">Achievements</a>
  </aside>
<div class="main">

  <div class="header">
    <div>
      <h2>Monthly Income</h2>
      <p class="subtitle">Record your income and see the last 12 months in a chart.</p>
    </div>
    <div class="top-actions">
      <button onclick="goPage('dashboard.php')">← Back to Dashboard</button>
    </div>
  </div>

  <div class="card monthly-income-panel">
    <div class="panel-left">
      <p class="panel-label">Monthly Income</p>
      <h1 id="currentIncome" class="hero-income">¥0</h1>
      <p class="panel-note">Enter your latest monthly income and track the last 12 months of progress in the chart below.</p>
    </div>
    <div class="panel-right">
      <div class="income-input-card">
        <h3>Update income</h3>
        <p class="subtitle">Enter your salary or earnings for this month.</p>
        <div class="income-input-row">
          <label for="incomeValue">Amount</label>
          <input id="incomeValue" type="text" placeholder="200000" />
        </div>
        <button id="saveIncome" class="primary-button">Save income</button>
      </div>
    </div>
  </div>

  <div class="card chart-card">
    <div class="chart-header">
      <div>
        <h3>Income Trend</h3>
        <p class="subtitle">Last 12 months</p>
      </div>
      <span id="chartSubtitle">Updated instantly</span>
    </div>
    <canvas id="incomeChart" width="700" height="320"></canvas>
  </div>

</div>
</div>

<script>
function goPage(page){
  window.location.href = page;
}

function formatYen(value){
  return '¥' + Number(value).toLocaleString();
}

function getHistory(){
  var raw = localStorage.getItem('monthlyIncomeHistory');
  if(!raw) return null;
  try { return JSON.parse(raw); } catch(e){ return null; }
}

function saveHistory(history){
  localStorage.setItem('monthlyIncomeHistory', JSON.stringify(history));
}

function createDefaultHistory(currentIncome){
  var months = [];
  var now = new Date();
  for(var i = 11; i >= 0; i--){
    var date = new Date(now.getFullYear(), now.getMonth() - i, 1);
    var key = date.getFullYear() + '-' + (date.getMonth() + 1);
    months.push({
      key: key,
      month: date.toLocaleString('default', { month: 'short' }),
      year: date.getFullYear(),
      income: i === 0 ? Number(currentIncome) : Number(currentIncome) || 0
    });
  }
  return months;
}

function ensureCurrentMonth(history, currentIncome){
  var now = new Date();
  var monthKey = now.getFullYear() + '-' + (now.getMonth() + 1);
  var existing = history.find(function(item){ return item.key === monthKey; });
  if(existing){
    existing.income = Number(currentIncome);
  } else {
    history.push({
      key: monthKey,
      month: now.toLocaleString('default', { month: 'short' }),
      year: now.getFullYear(),
      income: Number(currentIncome)
    });
  }
  history = history.slice(-12);
  return history;
}

function renderChart(history){
  var canvas = document.getElementById('incomeChart');
  if(!canvas) return;
  var ctx = canvas.getContext('2d');
  var width = canvas.width;
  var height = canvas.height;
  var padding = 50;
  var barWidth = (width - padding * 2) / history.length * 0.6;
  var maxIncome = Math.max.apply(null, history.map(function(item){ return item.income; }).concat(100));
  ctx.clearRect(0,0,width,height);

  // background grid
  ctx.strokeStyle = '#e5e7eb';
  ctx.lineWidth = 1;
  for(var row=0; row<=4; row++){
    var y = padding + row * ((height - padding * 2) / 4);
    ctx.beginPath();
    ctx.moveTo(padding, y);
    ctx.lineTo(width - padding, y);
    ctx.stroke();
  }

  // axes
  ctx.strokeStyle = '#94a3b8';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(padding, padding);
  ctx.lineTo(padding, height - padding);
  ctx.lineTo(width - padding, height - padding);
  ctx.stroke();

  // y labels
  ctx.fillStyle = '#475569';
  ctx.font = '13px Segoe UI';
  ctx.textAlign = 'right';
  for(var row=0; row<=4; row++){
    var y = padding + row * ((height - padding * 2) / 4);
    var value = Math.round(maxIncome - (maxIncome / 4) * row);
    ctx.fillText(formatYen(value), padding - 10, y + 4);
  }

  // bars
  history.forEach(function(item, index){
    var x = padding + (index + 0.5) * ((width - padding * 2) / history.length) - barWidth / 2;
    var barHeight = (item.income / maxIncome) * (height - padding * 2);
    var y = height - padding - barHeight;
    ctx.fillStyle = '#22c55e';
    ctx.fillRect(x, y, barWidth, barHeight);
    ctx.fillStyle = '#0f172a';
    ctx.textAlign = 'center';
    ctx.fillText(item.month, x + barWidth / 2, height - padding + 20);
    ctx.fillText(formatYen(item.income), x + barWidth / 2, y - 10);
  });
}

function updateCurrentDisplay(value){
  document.getElementById('currentIncome').textContent = formatYen(value);
}

function normalizeValue(value){
  return value.replace(/[^0-9]/g, '');
}

function saveIncomeValue(rawValue){
  var digits = normalizeValue(rawValue);
  if(digits === '') return null;
  localStorage.setItem('monthlyIncome', digits);
  return digits;
}

function getMonthlyIncome(){
  return localStorage.getItem('monthlyIncome') || '200000';
}

function buildHistory(currentIncome){
  var history = getHistory();
  if(!history || !Array.isArray(history)){
    history = createDefaultHistory(currentIncome);
    saveHistory(history);
  }
  history = ensureCurrentMonth(history, currentIncome);
  saveHistory(history);
  return history;
}

window.addEventListener('DOMContentLoaded', function(){
  var incomeValue = getMonthlyIncome();
  updateCurrentDisplay(incomeValue);
  document.getElementById('incomeValue').value = incomeValue;

  var history = buildHistory(incomeValue);
  renderChart(history);

  document.getElementById('saveIncome').addEventListener('click', function(){
    var raw = document.getElementById('incomeValue').value;
    var saved = saveIncomeValue(raw);
    if(!saved){
      alert('Please enter a valid number.');
      return;
    }
    updateCurrentDisplay(saved);
    var updatedHistory = buildHistory(saved);
    renderChart(updatedHistory);
    alert('Monthly income updated.');
  });
});
</script>
</body>
</html>
