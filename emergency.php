<?php
$categories = [
    ['id'=>'medical','icon'=>'🏥','label'=>'Medical','budget'=>100],
    ['id'=>'accident','icon'=>'🚑','label'=>'Accident','budget'=>80],
    ['id'=>'repair','icon'=>'🔧','label'=>'Repair','budget'=>60],
    ['id'=>'family','icon'=>'👨‍👩‍👧','label'=>'Family','budget'=>90],
    ['id'=>'emergency','icon'=>'⚠️','label'=>'Other','budget'=>50],
];
 
$expenses = [
    ['id'=>1,'name'=>'Hospital visit','category'=>'medical','date'=>'2026-06-01','amount'=>70],
    ['id'=>2,'name'=>'Bike repair','category'=>'repair','date'=>'2026-05-28','amount'=>30],
    ['id'=>3,'name'=>'Family support','category'=>'family','date'=>'2026-05-20','amount'=>50],
];
 
$totalEmergency = array_sum(array_column($categories,'budget'));
 
function getIcon($cats,$id){
  foreach($cats as $c){ if($c['id']==$id) return $c['icon']; }
  return '⚠️';
}
function getLabel($cats,$id){
  foreach($cats as $c){ if($c['id']==$id) return $c['label']; }
  return ucfirst($id);
}
?>
 
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Emergency</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/emergency.css">
</head>
 
<body>
<div class="layout">
 
<!-- Sidebar -->
<aside class="sidebar">
  <h2>💸 FOURCE</h2>
  <a href="dashboard.php">Dashboard</a>
  <a href="goals.php">Savings Goals</a>
  <a href="analytics.php">Analysis</a>
  <a href="achievements.php">Achievements</a>
</aside>
 
<div class="main">
  
 
<!-- HEADER -->
<div class="page-header">
  <div>
    <h1>⚠️ Emergency</h1>
    <p class="subtitle">Unexpected & urgent spending</p>
    <?php include 'includes/topbar.php'; ?>
  </div>
  
  <div style="display: flex; gap: 10px;">
    <button class="btn-add" onclick="openBudgetModal()">⚙️ Monthly Budget</button>
    <button class="btn-add" onclick="openModal()">＋ Add Expense</button>
  </div>
</div>
 
<!-- CATEGORY GRID -->
<div class="category-grid">
<?php foreach($categories as $c): ?>
  <div class="cat-card">
    <span class="cat-icon"><?= $c['icon'] ?></span>
    <span class="cat-label"><?= $c['label'] ?></span>
    <span class="cat-amount">$<?= $c['budget'] ?></span>
  </div>
<?php endforeach; ?>
</div>
 
<!-- TOTAL -->
<div class="total-bar">
  <span>Total Emergency</span>
  <span>$<?= $totalEmergency ?></span>
</div>
 
<!-- LIST -->
<h3>Recent Emergency</h3>
 
<div class="expense-list">
<?php foreach($expenses as $e): ?>
  <div class="expense-item">
    <div class="exp-icon"><?= getIcon($categories,$e['category']) ?></div>
 
    <div class="exp-info">
      <strong><?= $e['name'] ?></strong>
      <span><?= getLabel($categories,$e['category']) ?> • <?= $e['date'] ?></span>
    </div>
 
    <div class="exp-amount">$<?= $e['amount'] ?></div>
  </div>
<?php endforeach; ?>
</div>
</div>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════
     MONTHLY BUDGET MODAL
══════════════════════════════════════════════════════════════════════════ -->
<div class="modal" id="budget-overlay">
  <div class="modal-box" id="budget-modal" onclick="event.stopPropagation()">
    <h2>Set Monthly Budget</h2>
    <p style="color: #666; margin-bottom: 20px;">Define your total monthly budget for emergency</p>
    
    <label>Monthly Budget (¥)
      <input type="number" id="budget-input" placeholder="50000" min="1" step="1000" required>
    </label>
    
    <div class="modal-actions">
      <button type="button" class="cancel" onclick="closeBudgetModal()">Cancel</button>
      <button type="button" class="save" onclick="saveBudget()">Save Budget</button>
    </div>
  </div>
</div>

<!-- MODAL -->
<div class="modal" id="modal">
  <div class="modal-box">
    <h2>Add Emergency Expense</h2>
 
    <input type="text" placeholder="Name">
    <input type="number" placeholder="Amount">
 
    <button class="save">Save</button>
    <button class="cancel" onclick="closeModal()">Cancel</button>
  </div>
</div>
 
<script>
// ── Budget overlay ───────────────────────────────────────────────────────────
var budgetOverlay = document.getElementById('budget-overlay');

budgetOverlay.addEventListener('click', function(e) {
  if (e.target === budgetOverlay) closeBudgetModal();
});

// ── Budget modal ─────────────────────────────────────────────────────────────
function openBudgetModal() {
  const saved = localStorage.getItem('emergencyBudget');
  document.getElementById('budget-input').value = saved || '50000';
  budgetOverlay.style.display = 'flex';
}

function closeBudgetModal() {
  budgetOverlay.style.display = 'none';
}

function saveBudget() {
  const budget = document.getElementById('budget-input').value;
  if (!budget || budget <= 0) {
    alert('Please enter a valid budget amount');
    return;
  }
  localStorage.setItem('emergencyBudget', budget);
  alert('✅ Budget saved: ¥' + Number(budget).toLocaleString());
  closeBudgetModal();
}

// ── Expense modal ────────────────────────────────────────────────────────────
function openModal(){
  document.getElementById("modal").style.display="flex";
}
function closeModal(){
  document.getElementById("modal").style.display="none";
}

//total emergency budget start
const totalEmergency = <?= $totalEmergency ?>;

localStorage.setItem(
    "dashboardEmergencyCost",
    totalEmergency
);
//total emergency budget end
</script>
 
</body>
</html>
 