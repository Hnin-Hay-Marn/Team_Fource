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
    <div class="goals-header">
      <div>
        <h1>Savings Goals</h1>
        <p class="subtitle">Create up to 3 goals and complete them using your available savings.</p>
      </div>
 
      <div>
        <?php include 'includes/topbar.php'; ?>
      </div>
    </div>
 
    <div class="goals-grid" id="goalsGrid"></div>
 
    <div id="goalCollection" class="goal-collection"></div>
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
 
function yen(n) {
  return "¥" + Number(n).toLocaleString();
}
 
function getDashboardMoney() {
  let amountLeft = Number(localStorage.getItem("dashboardAmountLeft")) || 0;
  let monthSaving = Number(localStorage.getItem("dashboardSavedAmount")) || 0;
  let totalSavings = Number(localStorage.getItem("totalSavings")) || 0;
 
  return {
    amountLeft,
    monthSaving,
    totalSavings
  };
}
 
function saveDashboardMoney(amountLeft, monthSaving, totalSavings) {
  amountLeft = Math.max(0, Number(amountLeft) || 0);
  monthSaving = Math.max(0, Number(monthSaving) || 0);
  totalSavings = Math.max(0, Number(totalSavings) || 0);
 
  localStorage.setItem("dashboardAmountLeft", amountLeft);
  localStorage.setItem("dashboardSavedAmount", monthSaving);
  localStorage.setItem("totalSavings", totalSavings);
}
 
function renderGoals() {
  const grid = document.getElementById("goalsGrid");
  const money = getDashboardMoney();
 
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
      const target = Number(goal.target) || 0;
      const percent = target > 0
        ? Math.min((money.totalSavings / target) * 100, 100)
        : 0;
 
      grid.innerHTML += `
        <div class="goal-card">
          <button class="delete-goal-btn" onclick="deleteGoal(${index})">✕</button>
 
          ${goal.image ? `<img src="${goal.image}" alt="goal image">` : ""}
 
          <h2>${goal.name}</h2>
 
          <p class="amount">
            ${yen(money.totalSavings)} / ${yen(target)}
          </p>
 
          <div class="progress">
            <div class="progress-bar" style="width:${percent}%;"></div>
          </div>
 
          <p class="percent">${Math.round(percent)}% Achieved</p>
 
          <div class="goal-money-note">
            <small>Left to Spend: ${yen(money.amountLeft)}</small>
            <small>This Month Savings: ${yen(money.monthSaving)}</small>
          </div>
 
          <button class="edit-btn" onclick="openGoalModal(${index})">
            Edit Goal
          </button>
 
          ${percent >= 100 ? `
            <button class="use_btn" onclick="completeGoal(${index})">
              Complete Goal
            </button>
          ` : ""}
        </div>
      `;
    }
  });
}
 
function getGoalCollection() {
  return JSON.parse(localStorage.getItem("goalCollection")) || [];
}
 
function renderCollection() {
  const collection = getGoalCollection();
  const container = document.getElementById("goalCollection");
 
  if (collection.length === 0) {
    container.innerHTML = `
      <div class="collection-empty">
        <p>No completed goals yet.</p>
      </div>
    `;
    return;
  }
 
  container.innerHTML = `
    <h2>Completed Goals</h2>
 
    <div class="collection-grid">
      ${collection.map((item, index) => `
        <div class="collection-card">
          ${item.image ? `<img src="${item.image}" alt="completed goal">` : ""}
 
          <h3>${item.name}</h3>
          <p>Amount: ${yen(item.target)}</p>
          <p>Completed: ${item.date}</p>
 
          <button class="delete-btn" onclick="deleteCollectionGoal(${index})">
            Delete
          </button>
        </div>
      `).join("")}
    </div>
  `;
}
 
function openGoalModal(index) {
  selectedIndex = index;
 
  if (goals[index] !== null) {
    document.getElementById("goalName").value = goals[index].name;
    document.getElementById("goalTarget").value = goals[index].target;
  } else {
    document.getElementById("goalName").value = "";
    document.getElementById("goalTarget").value = "";
  }
 
  document.getElementById("goalImage").value = "";
  document.getElementById("goalModal").style.display = "flex";
}
 
function closeGoalModal() {
  document.getElementById("goalModal").style.display = "none";
}
 
function saveGoal() {
  const name = document.getElementById("goalName").value.trim();
  const target = Number(document.getElementById("goalTarget").value);
  const imageInput = document.getElementById("goalImage");
 
  if (name === "" || !target || target <= 0) {
    alert("Please enter a valid goal name and amount.");
    return;
  }
 
  if (imageInput.files.length > 0) {
    const reader = new FileReader();
 
    reader.onload = function(e) {
      goals[selectedIndex] = {
        name,
        target,
        image: e.target.result
      };
 
      localStorage.setItem("goals", JSON.stringify(goals));
      closeGoalModal();
      renderGoals();
    };
 
    reader.readAsDataURL(imageInput.files[0]);
  } else {
    goals[selectedIndex] = {
      name,
      target,
      image: goals[selectedIndex]?.image || ""
    };
 
    localStorage.setItem("goals", JSON.stringify(goals));
    closeGoalModal();
    renderGoals();
  }
}
 
function completeGoal(index) {
  const goal = goals[index];
 
  if (!goal) return;
 
  const target = Number(goal.target) || 0;
 
  let amountLeft = Number(localStorage.getItem("dashboardAmountLeft")) || 0;
  let monthSaving = Number(localStorage.getItem("dashboardSavedAmount")) || 0;
  let totalSavings = Number(localStorage.getItem("totalSavings")) || 0;
 
  if (totalSavings < target) {
    alert("You do not have enough total savings to complete this goal.");
    return;
  }
 
  if (!confirm(`Complete "${goal.name}" and use ${yen(target)}?`)) {
    return;
  }
 
  let remaining = target;
 
  // 1. First deduct from Amount Left To Spend
  if (amountLeft >= remaining) {
    amountLeft -= remaining;
    remaining = 0;
  } else {
    remaining -= amountLeft;
    amountLeft = 0;
  }
 
  // 2. Then deduct from This Month Savings
  if (remaining > 0) {
    if (monthSaving >= remaining) {
      monthSaving -= remaining;
      remaining = 0;
    } else {
      remaining -= monthSaving;
      monthSaving = 0;
    }
  }
 
  // 3. Also deduct from Total Savings shown in Profile
  totalSavings -= target;
 
  if (totalSavings < 0) {
    totalSavings = 0;
  }
 
  saveDashboardMoney(amountLeft, monthSaving, totalSavings);
 
  const collection = getGoalCollection();
 
  collection.push({
    ...goal,
    usedAmount: target,
    date: new Date().toLocaleDateString(),
    time: new Date().toLocaleTimeString()
  });
 
  localStorage.setItem("goalCollection", JSON.stringify(collection));
 
  goals[index] = null;
  localStorage.setItem("goals", JSON.stringify(goals));
 
  renderGoals();
  renderCollection();
 
  alert("🎉 Goal completed! Amount was deducted from Total Savings.");
}
 
function deleteGoal(index) {
  if (!confirm("Delete this goal?")) {
    return;
  }
 
  goals[index] = null;
  localStorage.setItem("goals", JSON.stringify(goals));
  renderGoals();
}
 
function deleteCollectionGoal(index) {
  let collection = getGoalCollection();
 
  if (!confirm("Delete this completed goal?")) {
    return;
  }
 
  collection.splice(index, 1);
  localStorage.setItem("goalCollection", JSON.stringify(collection));
  renderCollection();
}
 
window.onclick = function(event) {
  const modal = document.getElementById("goalModal");
 
  if (event.target === modal) {
    closeGoalModal();
  }
}
 
renderGoals();
renderCollection();
</script>
 
</body>
</html>
 