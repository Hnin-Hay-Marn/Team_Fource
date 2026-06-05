<?php
// ── Entertainment categories ───────────────────────────────────────────────
$categories = [
    ['id'=>'movies',    'icon'=>'🎬', 'label'=>'Movies',    'budget'=>120],
    ['id'=>'games',     'icon'=>'🎮', 'label'=>'Games',     'budget'=>200],
    ['id'=>'music',     'icon'=>'🎵', 'label'=>'Music',     'budget'=>80],
    ['id'=>'travel',    'icon'=>'✈️', 'label'=>'Travel',    'budget'=>300],
    ['id'=>'dating',    'icon'=>'💖', 'label'=>'Dating',    'budget'=>150],
    ['id'=>'sports',    'icon'=>'⚽', 'label'=>'Sports',    'budget'=>90],
];
 
// ── Sample expenses ────────────────────────────────────────────────────────
$expenses = [
    ['id'=>1, 'name'=>'Cinema ticket',  'category'=>'movies', 'date'=>'2026-06-01', 'amount'=>15],
    ['id'=>2, 'name'=>'Steam game',     'category'=>'games',  'date'=>'2026-05-29', 'amount'=>60],
    ['id'=>3, 'name'=>'Spotify',        'category'=>'music',  'date'=>'2026-05-28', 'amount'=>10],
    ['id'=>4, 'name'=>'Dinner date',    'category'=>'dating', 'date'=>'2026-05-25', 'amount'=>45],
];
 
// ── Actions ────────────────────────────────────────────────────────────────
$action  = $_POST['action'] ?? '';
$message = '';
 
if ($action === 'add') {
    $message = '🎉 Added!';
} elseif ($action === 'edit') {
    $message = '✏️ Updated!';
} elseif ($action === 'delete') {
    $message = '🗑️ Deleted!';
}
 
// ── Helpers ───────────────────────────────────────────────────────────────
$total = array_sum(array_column($categories, 'budget'));
 
function getIcon($categories, $id) {
    foreach ($categories as $c) if ($c['id'] === $id) return $c['icon'];
    return '🎯';
}
function getLabel($categories, $id) {
    foreach ($categories as $c) if ($c['id'] === $id) return $c['label'];
    return ucfirst($id);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Entertainment – FOURCE</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/entertainment.css"> <!-- reuse same CSS -->
</head>
 
<body>
 
<div class="layout">
 
<!-- Sidebar -->
<aside class="sidebar">
    <h2><span class="logo-icon">💸</span> FOURCE</h2>
    <a href="dashboard.php" class="active">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analysis</a>
    <a href="achievements.php">Achievements</a>
  </aside>
 
<div class="main">
  
 
<?php if ($message): ?>
  <div class="toast"><?= $message ?></div>
<?php endif; ?>
 
<!-- Header -->
<div class="page-header">
  <div>
    <h1>🎮 Entertainment</h1>
    <p class="subtitle">Fun & lifestyle spending</p>
    <?php include 'includes/topbar.php'; ?>
  </div>
  <button class="btn-add">＋ Add Expense</button>
</div>
 
<!-- Category Grid -->
<div class="category-grid">
<?php foreach ($categories as $cat): ?>
  <div class="cat-card">
    <span class="cat-icon"><?= $cat['icon'] ?></span>
    <span class="cat-label"><?= $cat['label'] ?></span>
    <span class="cat-amount">$<?= $cat['budget'] ?></span>
  </div>
<?php endforeach; ?>
</div>
 
<!-- Total -->
<div class="total-bar">
  <span class="total-label">Total Entertainment</span>
  <span class="total-amount">$<?= $total ?></span>
</div>
 
<!-- Expenses -->
<h3>Recent Fun Spending</h3>
 
<div class="expense-list">
<?php foreach ($expenses as $exp): ?>
  <div class="expense-item">
    <div class="exp-icon-wrap">
      <?= getIcon($categories, $exp['category']) ?>
    </div>
 
    <div class="exp-info">
      <strong><?= $exp['name'] ?></strong>
      <span class="exp-meta">
        <?= getLabel($categories, $exp['category']) ?> • <?= $exp['date'] ?>
      </span>
    </div>
 
    <span class="exp-amount">$<?= $exp['amount'] ?></span>
  </div>
<?php endforeach; ?>
</div>
 
</div>
</div>
 
</body>
</html>
 