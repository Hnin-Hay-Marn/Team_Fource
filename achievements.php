<?php
require_once "db.php";

$pdo = getDb();

$stmt = $pdo->query("
SELECT
    month,
    saving_amount,
    achievement
FROM savings
ORDER BY month
");

$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Monthly Achievements</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/achievements.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<!-- bootstrap--------nav bar---- -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top px-3">

    <!-- Menu -->
    <button class="btn btn-success"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu">
        ☰
    </button>

    <!-- Logo -->
    <a class="navbar-brand ms-3 fw-bold" href="#">
        <img src="img/logo.png" width="35" class="me-2">
        Money Tracker
    </a>

    <!-- Right Side -->
    <div class="ms-auto d-flex align-items-center gap-3">

        <div id="google_translate_element"></div>

        <button class="btn btn-outline-success"
                onclick="goPage('profile.php')">
            👤 Profile
        </button>

    </div>

</nav>
<!-- bootstrap--------nav bar end---- -->

<div class="layout">
  <!-- bootstrap--------sidebar---- -->
  <div class="offcanvas offcanvas-start"
     tabindex="-1"
     id="sidebarMenu">

  <div class="offcanvas-header">

    <h5 class="offcanvas-title">
      <img src="img/logo.png" alt="Money Tracker" class="logo-img"> Money Tracker
    </h5>

    <button type="button"
            class="btn-close"
            data-bs-dismiss="offcanvas">
    </button>

  </div>
  <!-- topbar end -->

  <div class="offcanvas-body">

      <a href="dashboard.php" class="btn btn-outline-success w-100 mb-3">
          🏠 Dashboard
      </a>

      <a href="goals.php" class="btn btn-outline-success w-100 mb-3">
          🎯 Savings Goals
      </a>

      <a href="analytics.php" class="btn btn-outline-success w-100 mb-3">
          📊 Analytics
      </a>

      <a href="achievements.php" class="btn btn-outline-success w-100">
          🏆 Achievements
      </a>

  </div>

</div>
<!-- bootstrap--------sidebar end---- -->

<div class="main container-fluid mt-4">
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
  </div>

<script>
const achievements = <?= json_encode($achievements) ?>;
</script>

<script>
const monthNames = [
  "January", "February", "March", "April",
  "May", "June", "July", "August",
  "September", "October", "November", "December"
];

const today = new Date();
const currentMonth = today.getMonth();
const currentYear = today.getFullYear();

// const savedAmount = Number(localStorage.getItem("dashboardSavedAmount")) || 0;
// const monthlyGoal = Number(localStorage.getItem("monthlySavingGoal")) || 0;

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

// function saveCurrentMonthOnly() {
//   const badge = getBadge(savedAmount, monthlyGoal);

//   const key = `achievement_${currentYear}_${currentMonth}`;

//   const data = {
//     month: currentMonth,
//     year: currentYear,
//     saved: savedAmount,
//     goal: monthlyGoal,
//     icon: badge.icon,
//     text: badge.text,
//     className: badge.className,
//     date: today.toLocaleDateString(),
//     time: today.toLocaleTimeString()
//   };

//   localStorage.setItem(key, JSON.stringify(data));
// }

function renderAchievementBoard() {

    const grid = document.getElementById("achievementGrid");
    grid.innerHTML = "";

    for (let i = 0; i < 12; i++) {

        const row = achievements.find(a => {
            const m = Number(a.month.split("-")[1]) - 1;
            return m === i;
        });

        if (row) {

            let icon = "";
            let text = "";
            let className = "";

            if (row.achievement == "gold") {
                icon = "🥇";
                text = "Goal Exceeded";
                className = "month-card gold";

            } else if (row.achievement == "silver") {
                icon = "🥈";
                text = "Goal Achieved";
                className = "month-card silver";

            } else {
                icon = "❌";
                text = "Goal Not Achieved";
                className = "month-card failed";
            }

            grid.innerHTML += `
                <div class="${className}">
                    <div class="month">${monthNames[i]}</div>

                    <div class="icon">${icon}</div>

                    <p>${text}</p>

                    <small>Saved: ¥${Number(row.saving_amount).toLocaleString()}</small>

                    
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


renderAchievementBoard();
</script>

<!-- bootstrap-------- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>