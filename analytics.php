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
    <h2>💸 FOURCE</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analysis</a>
    <a href="achievements.php">Achievements</a>
  </aside>

  <main class="main">

  <div class="header">
    <div>
      <h1>Analytics</h1>
      <p id="pageSubtitle" class="subtitle">Record your income and see the last 12 months in a chart.</p>
    </div>
    <div>
      <?php include 'includes/topbar.php'; ?>
    </div>
    
  </div>
    
    
    

    <div class="amount-cards">
      <div class="amount-card income-card">
        <p>Monthly Income</p>
        <h2>¥200,000</h2>
      </div>

      <div class="amount-card spend-card">
        <p>Total Spending</p>
        <h2>¥95,000</h2>
      </div>

      <div class="amount-card saving-card">
        <p>Savings</p>
        <h2>¥50,000</h2>
      </div>
    </div>

    <section class="chart-card">
      <h2>Category-wise Spending</h2>

      <div class="bar-list">
        <div class="bar-row">
          <div class="bar-label">🏠 Basic Cost</div>
          <div class="bar-track">
            <div class="bar-fill basic-fill" style="width:60%;"></div>
          </div>
          <div class="bar-amount">¥60,000</div>
        </div>

        <div class="bar-row">
          <div class="bar-label">🎮 Entertainment</div>
          <div class="bar-track">
            <div class="bar-fill fun-fill" style="width:20%;"></div>
          </div>
          <div class="bar-amount">¥20,000</div>
        </div>

        <div class="bar-row">
          <div class="bar-label">⚠️ Emergency</div>
          <div class="bar-track">
            <div class="bar-fill emergency-fill" style="width:15%;"></div>
          </div>
          <div class="bar-amount">¥15,000</div>
        </div>
      </div>
    </section>

    <section class="chart-card">
      <h2>Monthly Spending Line</h2>

      <div class="line-chart">
        <svg viewBox="0 0 700 280">
          <polyline
            class="line spending-line"
            points="40,210 150,180 260,200 370,140 480,160 590,110 680,130" />

          <circle cx="40" cy="210" r="6"></circle>
          <circle cx="150" cy="180" r="6"></circle>
          <circle cx="260" cy="200" r="6"></circle>
          <circle cx="370" cy="140" r="6"></circle>
          <circle cx="480" cy="160" r="6"></circle>
          <circle cx="590" cy="110" r="6"></circle>
          <circle cx="680" cy="130" r="6"></circle>

          <text x="30" y="245">Jan</text>
          <text x="140" y="245">Feb</text>
          <text x="250" y="245">Mar</text>
          <text x="360" y="245">Apr</text>
          <text x="470" y="245">May</text>
          <text x="580" y="245">Jun</text>
          <text x="670" y="245">Jul</text>
        </svg>
      </div>
    </section>

    <section class="chart-card">
      <h2>Monthly Savings Line</h2>

      <div class="line-chart">
        <svg viewBox="0 0 700 280">
          <polyline
            class="line saving-line"
            points="40,220 150,190 260,170 370,150 480,120 590,90 680,70" />

          <circle cx="40" cy="220" r="6"></circle>
          <circle cx="150" cy="190" r="6"></circle>
          <circle cx="260" cy="170" r="6"></circle>
          <circle cx="370" cy="150" r="6"></circle>
          <circle cx="480" cy="120" r="6"></circle>
          <circle cx="590" cy="90" r="6"></circle>
          <circle cx="680" cy="70" r="6"></circle>

          <text x="30" y="250">Jan</text>
          <text x="140" y="250">Feb</text>
          <text x="250" y="250">Mar</text>
          <text x="360" y="250">Apr</text>
          <text x="470" y="250">May</text>
          <text x="580" y="250">Jun</text>
          <text x="670" y="250">Jul</text>
        </svg>
      </div>
    </section>

  </main>
</div>

</body>
</html>