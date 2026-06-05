<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>貯金目標</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/goals.css">
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
    
    <h1>Savings Goals</h1>
    <?php include 'includes/topbar.php'; ?>
    <div class="goals-grid" id="goalsGrid"></div>
  </main>
</div>

<div id="goalModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeGoalModal()">&times;</span>

    <h2>Add New Goal</h2>

    <label>Goal Name</label>
    <input id="goalName" type="text" placeholder="e.g., Travel Expenses">

    <label>Goal Image</label>
    <input id="goalImage" type="file" accept="image/*">

    <label>Goal Amount</label>
    <input id="goalTarget" type="number" placeholder="e.g., 100000">

    <button type="button" onclick="saveGoal()">Save Goal</button>
  </div>
</div>

<script>
let selectedIndex = null;

let goals = JSON.parse(localStorage.getItem("goals")) || [
  null,
  null,
  null
];

function getSavedAmount() {
  return Number(localStorage.getItem("dashboardSavedAmount")) || 0;
}

function renderGoals() {
  const grid = document.getElementById("goalsGrid");
  grid.innerHTML = "";

  goals.forEach((goal, index) => {
    if (goal === null) {
      grid.innerHTML += `
        <div class="empty-goal" onclick="openGoalModal(${index})">
          <div class="plus">+</div>
          <h3>Goal Slot ${index + 1}</h3>
          <p>Add New Goal</p>
        </div>
      `;
    } else {
      const saved = getSavedAmount();
      const percent = Math.min((saved / goal.target) * 100, 100);

      grid.innerHTML += `
        <div class="goal-card">
          ${goal.image ? `<img src="${goal.image}" alt="goal image">` : ""}
          <h2>${goal.name}</h2>
          <p class="amount">¥${saved.toLocaleString()} / ¥${Number(goal.target).toLocaleString()}</p>

          <div class="progress">
            <div class="progress-bar" style="width:${percent}%;"></div>
          </div>

          <p class="percent">${Math.round(percent)}% Achieved</p>
          <button class="edit-btn" onclick="openGoalModal(${index})">Edit Goal</button>
        </div>
      `;
    }
  });
}

function openGoalModal(index) {
  selectedIndex = index;

  if (goals[index] !== null) {
    document.getElementById("goalName").value = goals[index].name;
    document.getElementById("goalTarget").value = goals[index].target;
  } else {
    document.getElementById("goalName").value = "";
    document.getElementById("goalTarget").value = "";
    document.getElementById("goalImage").value = "";
  }

  document.getElementById("goalModal").style.display = "flex";
}

function closeGoalModal() {
  document.getElementById("goalModal").style.display = "none";
}

function saveGoal() {
  const name = document.getElementById("goalName").value;
  const target = document.getElementById("goalTarget").value;
  const imageInput = document.getElementById("goalImage");

  if (name === "" || target === "") {
    alert("Please enter a goal name and target amount");
    return;
  }

  if (imageInput.files.length > 0) {
    const reader = new FileReader();

    reader.onload = function(e) {
      goals[selectedIndex] = {
        name: name,
        target: target,
        image: e.target.result
      };

      localStorage.setItem("goals", JSON.stringify(goals));
      closeGoalModal();
      renderGoals();
    };

    reader.readAsDataURL(imageInput.files[0]);
  } else {
    goals[selectedIndex] = {
      name: name,
      target: target,
      image: goals[selectedIndex]?.image || ""
    };

    localStorage.setItem("goals", JSON.stringify(goals));
    closeGoalModal();
    renderGoals();
  }
}

window.onclick = function(event) {
  const modal = document.getElementById("goalModal");

  if (event.target === modal) {
    closeGoalModal();
  }
}

renderGoals();
</script>

</body>
</html>