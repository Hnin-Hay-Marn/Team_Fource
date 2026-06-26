<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monthly Achievements</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/achievements.css">
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
    <h1>Yearly Achievement Board</h1>
    <?php include 'includes/topbar.php'; ?>
    <p class="subtitle">The current month is updated automatically from your dashboard savings.</p>

    <div class="legend">
      <span>🥇 Goal Exceeded</span>
      <span>🥈 Goal Achieved</span>
      <span>❌ Goal Not Achieved</span>
      <span>🔒 To Be Unlocked</span>
    </div>

    <div class="achievement-grid" id="achievementGrid"></div>
  </main>
</div>

<script>
const monthNames = [
  "January", "February", "March", "April",
  "May", "June", "July", "August",
  "September", "October", "November", "December"
];

const today = new Date();
const currentMonth = today.getMonth();
const currentYear = today.getFullYear();

const savedAmount = Number(localStorage.getItem("dashboardSavedAmount")) || 0;
const monthlyGoal = Number(localStorage.getItem("monthlySavingGoal")) || 0;

function getBadge(saved, goal) {
  if (saved > goal) {
    return {
      icon: "🥇",
      text: "Goal Exceeded",
      className: "month-card gold"
    };
  }

  if (saved === goal && goal > 0) {
    return {
      icon: "🥈",
      text: "Goal Achieved",
      className: "month-card silver"
    };
  }

  return {
    icon: "❌",
    text: "Goal Not Achieved",
    className: "month-card failed"
  };
}

function saveCurrentMonthOnly() {
  const badge = getBadge(savedAmount, monthlyGoal);

  const key = `achievement_${currentYear}_${currentMonth}`;

  const data = {
    month: currentMonth,
    year: currentYear,
    saved: savedAmount,
    goal: monthlyGoal,
    icon: badge.icon,
    text: badge.text,
    className: badge.className,
    date: today.toLocaleDateString(),
    time: today.toLocaleTimeString()
  };

  localStorage.setItem(key, JSON.stringify(data));
}

function renderAchievementBoard() {
  const grid = document.getElementById("achievementGrid");
  grid.innerHTML = "";

  for (let i = 0; i < 12; i++) {
    const key = `achievement_${currentYear}_${i}`;
    const data = JSON.parse(localStorage.getItem(key));

    if (data) {
      grid.innerHTML += `
        <div class="${data.className}">
          <div class="month">${monthNames[i]}</div>
          <div class="icon">${data.icon}</div>
          <p>${data.text}</p>
          <small>Saved: ¥${Number(data.saved).toLocaleString()}</small>
          <small>Goal: ¥${Number(data.goal).toLocaleString()}</small>
          <small>${data.date} ${data.time}</small>
        </div>
      `;
    } else {
      grid.innerHTML += `
        <div class="month-card locked">
          <div class="month">${monthNames[i]}</div>
          <div class="icon">🔒</div>
          <p>To Be Unlocked</p>
        </div>
      `;
    }
  }
}

saveCurrentMonthOnly();
renderAchievementBoard();
</script>

</body>
</html>