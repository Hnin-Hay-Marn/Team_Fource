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
    <a href="goals.php">貯金目標</a>
    <a href="analytics.php">分析</a>
    <a href="achievements.php">達成バッジ</a>
  </aside>

  <main class="main">
    <h1>年間達成ボード</h1>
    <p class="subtitle">毎月の貯金結果をバッジで記録します。</p>

    <div class="legend">
      <span>🥇 目標超え</span>
      <span>🥈 目標達成</span>
      <span>❌ 未達成</span>
      <span>🔥 挑戦中</span>
      <span>🔒 これから</span>
    </div>

    <div class="achievement-grid">

      <div class="month-card gold">
        <div class="month">January</div>
        <div class="icon">🥇</div>
        <p>目標超え</p>
      </div>

      <div class="month-card silver">
        <div class="month">February</div>
        <div class="icon">🥈</div>
        <p>目標達成</p>
      </div>

      <div class="month-card failed">
        <div class="month">March</div>
        <div class="icon">❌</div>
        <p>未達成</p>
      </div>

      <div class="month-card gold">
        <div class="month">April</div>
        <div class="icon">🥇</div>
        <p>目標超え</p>
      </div>

      <div class="month-card current">
        <div class="month">May</div>
        <div class="icon">🔥</div>
        <p>挑戦中</p>
      </div>

      <div class="month-card locked">
        <div class="month">June</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">July</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">August</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">September</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">October</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">November</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

      <div class="month-card locked">
        <div class="month">December</div>
        <div class="icon">🔒</div>
        <p>これから</p>
      </div>

    </div>
  </main>
</div>

</body>
</html>