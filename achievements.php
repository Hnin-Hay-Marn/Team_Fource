<?php



?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Monthly Achievements</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/achievements.css">
</head>
<body>

<div class="layout">
  <aside class="sidebar">
    <h2><span class="logo-icon">💸</span> FOURCE</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analysis</a>
    <a href="achievements.php">Achievements</a>
  </aside>

  <main class="main">
    
    <h1>Yearly Achievement Board</h1>
    <?php include 'includes/topbar.php'; ?>
    <p class="subtitle">Record your monthly savings results with badges.</p>

    <div class="legend">
      <span>🥇 Goal Exceeded</span>
      <span>🥈 Goal Achieved</span>
      <span>❌ Goal Not Achieved</span>
      <span>🔥 In Progress</span>
      <span>🔒 To Be Unlocked</span>
    </div>

    <div class="achievement-grid">

      <div class="month-card gold">
        <div class="month">January</div>
        <div class="icon">🥇</div>
        <p>Goal Exceeded</p>
      </div>

      <div class="month-card silver">
        <div class="month">February</div>
        <div class="icon">🥈</div>
        <p>Goal Achieved</p>
      </div>

      <div class="month-card failed">
        <div class="month">March</div>
        <div class="icon">❌</div>
        <p>Goal Not Achieved</p>
      </div>

      <div class="month-card gold">
        <div class="month">April</div>
        <div class="icon">🥇</div>
        <p>Goal Exceeded</p>
      </div>

      <div class="month-card current">
        <div class="month">May</div>
        <div class="icon">🔥</div>
        <p>In Progress</p>
      </div>

      <div class="month-card locked">
        <div class="month">June</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">July</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">August</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">September</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">October</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">November</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

      <div class="month-card locked">
        <div class="month">December</div>
        <div class="icon">🔒</div>
        <p>To Be Unlocked</p>
      </div>

    </div>
  </main>
</div>

</body>
</html>