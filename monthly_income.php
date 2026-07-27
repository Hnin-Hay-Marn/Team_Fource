<?php 
session_start();
require_once "db.php";

$pdo = getDb();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $amount = (int)$_POST['income'];
    $month = date('Y-m');

    $stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM incomes
    WHERE month=?
    ");

    $stmt->execute([$month]);

    if ($stmt->fetchColumn()) {

        $stmt = $pdo->prepare("
        UPDATE incomes
        SET amount=?
        WHERE month=?
        ");

        $stmt->execute([$amount,$month]);

    } else {

        $stmt = $pdo->prepare("
        INSERT INTO incomes(amount,month)
        VALUES(?,?)
        ");

        $stmt->execute([$amount,$month]);
    }

    header("Location: monthly_income.php");
    exit;
}


$currentMonth = date('Y-m');

// Get current month's income
$stmt = $pdo->prepare("
    SELECT amount
    FROM incomes
    WHERE month = ?
    LIMIT 1
");

$stmt->execute([$currentMonth]);

$currentIncome = $stmt->fetchColumn();

if ($currentIncome === false) {
    $currentIncome = 0;
}
//chart data for last 12 months
$stmt = $pdo->query("
SELECT month, amount
FROM incomes
ORDER BY month DESC
LIMIT 12
");

$chartData = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Monthly Income | Money Saver App</title>

<!-- Link CSS -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/monthly_income.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/sidebar.php'; ?>

<div class="main container-fluid mt-4">



  <div class="header">
    <div>
      <h2 id="pageHeading">Monthly Income</h2>
      <p id="pageSubtitle" class="subtitle">Record your income and see the last 12 months in a chart.</p>
    </div>
  </div>

  <div class="card monthly-income-panel">
    <div class="panel-left">
      <p id="panelLabel" class="panel-label">Monthly Income</p>
      <h1 class="hero-income"> ¥<?= number_format($currentIncome) ?></h1>
      <p id="panelNote" class="panel-note">Enter your latest monthly income and track the last 12 months of progress in the chart below.</p>
    </div>
    <div class="panel-right">
      <div class="income-input-card">
        <h3 id="updateHeading">Update income</h3>
        <p id="updateSubtitle" class="subtitle">Enter your salary or earnings for this month.</p>
        <div class="income-input-row">
          <label id="amountLabel" for="incomeValue">Amount</label>
            
      </div>
        <form method="post">

<input
    type="number"
    name="income"
    value="<?= htmlspecialchars($currentIncome) ?>"
>

<button class="primary-button">
    Save Income
</button>

</form>
      </div>
    </div>
  </div>

  <div class="card chart-card">
    <div class="chart-header">
      <div>
        <h3 id="chartHeading">Income Trend</h3>
        <p id="chartSubtitleText" class="subtitle">Last 12 months</p>
      </div>
      <span id="chartSubtitle">Updated instantly</span>
    </div>
    <canvas id="incomeChart"></canvas>  </div>

</div>
</div>

<!-- <script>
function goPage(page){
  window.location.href = page;
}

function formatYen(value){
  return '¥' + Number(value).toLocaleString();
}

function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'en',
    includedLanguages: 'en,ja,my,ko,zh-CN,th,vi,id,ne',
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
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
  //var padding = 50;
  var paddingLeft = 90;
  var paddingBottom = 50;
  var paddingTop = 40;
  var paddingRight = 30;
  var barWidth = (width - paddingLeft - paddingRight) / history.length * 0.6;
  var maxIncome = Math.max.apply(null, history.map(function(item){ return item.income; }).concat(100));
  ctx.clearRect(0,0,width,height);

  // background grid
  ctx.strokeStyle = '#e5e7eb';
  ctx.lineWidth = 1;
  for(var row=0; row<=4; row++){
    var y = paddingTop + row * ((height - paddingTop - paddingBottom) / 4);
    ctx.beginPath();
    ctx.moveTo(paddingLeft, y);
    ctx.lineTo(width - paddingRight, y);
    ctx.stroke();
  }

  // axes
  ctx.strokeStyle = '#94a3b8';
  ctx.lineWidth = 2;
  ctx.beginPath();
  ctx.moveTo(paddingLeft, paddingTop);
  ctx.lineTo(paddingLeft, height - paddingBottom);
  ctx.lineTo(width - paddingRight, height - paddingBottom);
  ctx.stroke();

  // y labels
  ctx.fillStyle = '#475569';
  ctx.font = '13px Segoe UI';
  ctx.textAlign = 'right';
  for(var row=0; row<=4; row++){
    var y = paddingTop + row * ((height - paddingTop - paddingBottom) / 4);
    var value = Math.round(maxIncome - (maxIncome / 4) * row);
    ctx.fillText(formatYen(value), paddingLeft   - 10, y + 4);
  }

  // bars
  history.forEach(function(item, index){
    var x = paddingLeft + (index + 0.5) * ((width - paddingLeft - paddingRight) / history.length) - barWidth / 2;
    var barHeight = (item.income / maxIncome) * (height - paddingTop - paddingBottom);
    var y = height - paddingBottom   - barHeight;
    ctx.fillStyle = '#22c55e';
    ctx.fillRect(x, y, barWidth, barHeight);
    ctx.fillStyle = '#0f172a';
    ctx.textAlign = 'center';
    ctx.fillText(item.month, x + barWidth / 2, height - paddingBottom    + 20);
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
</script> -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const incomeHistory = <?= json_encode($chartData) ?>;

const labels = incomeHistory.map(item => {
    const d = new Date(item.month + '-01');
    return d.toLocaleString('en-US', { month: 'short' });
});

const amounts = incomeHistory.map(item => Number(item.amount));

const ctx = document.getElementById('incomeChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            data: amounts,
            backgroundColor: '#22c55e',
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.8,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
