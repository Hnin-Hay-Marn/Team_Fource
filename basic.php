<?php
// ── Sample expense data (replace with DB queries later) ──────────────────────
$categories = [
    ['id'=>'food',        'icon'=>'🍔', 'label'=>'Food',        'budget'=>117],
    ['id'=>'car',         'icon'=>'🚗', 'label'=>'Car',         'budget'=>70],
    ['id'=>'electricity', 'icon'=>'💡', 'label'=>'Electricity', 'budget'=>120],
    ['id'=>'gas',         'icon'=>'🔥', 'label'=>'Gas',         'budget'=>80],
    ['id'=>'rent',        'icon'=>'🏠', 'label'=>'Rent',        'budget'=>900],
    ['id'=>'internet',    'icon'=>'🌐', 'label'=>'Internet',    'budget'=>60],
    ['id'=>'phone',       'icon'=>'📱', 'label'=>'Phone',       'budget'=>50],
    ['id'=>'water',       'icon'=>'💧', 'label'=>'Water',       'budget'=>40],
    ['id'=>'insurance',   'icon'=>'🛡️', 'label'=>'Insurance',  'budget'=>150],
    ['id'=>'shopping',    'icon'=>'🛍️', 'label'=>'Shopping',   'budget'=>89],
    ['id'=>'health',      'icon'=>'🏥', 'label'=>'Health',      'budget'=>90],
];
 
$expenses = [
    ['id'=>1, 'name'=>'June rent',        'category'=>'rent',     'date'=>'2026-06-01', 'amount'=>900],
    ['id'=>2, 'name'=>'Weekly groceries', 'category'=>'food',     'date'=>'2026-05-28', 'amount'=>85],
    ['id'=>3, 'name'=>'Gas fill-up',      'category'=>'car',      'date'=>'2026-05-27', 'amount'=>55],
    ['id'=>4, 'name'=>'Takeout dinner',   'category'=>'food',     'date'=>'2026-05-25', 'amount'=>32],
    ['id'=>5, 'name'=>'Car wash',         'category'=>'car',      'date'=>'2026-05-20', 'amount'=>15],
    ['id'=>6, 'name'=>'New shoes',        'category'=>'shopping', 'date'=>'2026-05-15', 'amount'=>89],
    ['id'=>7, 'name'=>'Dentist visit',    'category'=>'health',   'date'=>'2026-05-10', 'amount'=>90],
];
 
// ── Handle add/edit/delete (POST) ─────────────────────────────────────────────
$action  = $_POST['action']  ?? '';
$message = '';
 
if ($action === 'add') {
    // TODO: INSERT INTO expenses ...
    $message = '✅ Expense added!';
} elseif ($action === 'edit') {
    // TODO: UPDATE expenses SET ...
    $message = '✅ Expense updated!';
} elseif ($action === 'delete') {
    // TODO: DELETE FROM expenses WHERE id = ...
    $message = '🗑️ Expense deleted.';
}
 
// ── Helpers ───────────────────────────────────────────────────────────────────
$totalBasic = array_sum(array_column($categories, 'budget'));
 
function getCategoryIcon(array $categories, string $id): string {
    foreach ($categories as $c) {
        if ($c['id'] === $id) return $c['icon'];
    }
    return '💰';
}
function getCategoryLabel(array $categories, string $id): string {
    foreach ($categories as $c) {
        if ($c['id'] === $id) return $c['label'];
    }
    return ucfirst($id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Basic Costs – FOURCE</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/basic.css">
</head>
<body>
 
<!-- ── Layout ──────────────────────────────────────────────────────────────── -->
<div class="layout">
 
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2><span class="logo-icon">💸</span> FOURCE</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="goals.php">Savings Goals</a>
    <a href="analytics.php">Analysis</a>
    <a href="achievements.php">Achievements</a>
  </aside>
 
  <!-- Main content -->
  <div class="main">
    
 
    <?php if ($message): ?>
      <div class="toast"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
 
    <!-- ── Page header ──────────────────────────────────────────────────── -->
    <div class="page-header">
      <div>
        <h1>🏠 Basic Costs</h1>
        <p class="subtitle">Your essential monthly expenses</p>
      </div>
      <div><?php include 'includes/topbar.php'; ?></div>
      <button class="btn-add" onclick="openModal()">＋ Add Expense</button>
    </div>
 
    <!-- ── Category grid ────────────────────────────────────────────────── -->
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <div class="cat-card" onclick="filterByCategory('<?= $cat['id'] ?>')">
          <span class="cat-icon"><?= $cat['icon'] ?></span>
          <span class="cat-label"><?= htmlspecialchars($cat['label']) ?></span>
          <span class="cat-amount">$<?= number_format($cat['budget']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
 
    <!-- ── Total bar ─────────────────────────────────────────────────────── -->
    <div class="total-bar">
      <span class="total-label">Total Basic Costs</span>
      <span class="total-amount">$<?= number_format($totalBasic) ?></span>
    </div>
 
    <!-- ── Expense list ──────────────────────────────────────────────────── -->
    <div class="section-title">
      <h3 id="list-heading">All Expenses</h3>
      <button class="btn-clear" onclick="clearFilter()" id="clear-btn" style="display:none">✕ Show All</button>
    </div>
 
    <div class="expense-list" id="expense-list">
      <?php foreach ($expenses as $exp): ?>
        <div class="expense-item" data-category="<?= $exp['category'] ?>">
          <div class="exp-icon-wrap">
            <?= getCategoryIcon($categories, $exp['category']) ?>
          </div>
          <div class="exp-info">
            <strong><?= htmlspecialchars($exp['name']) ?></strong>
            <span class="exp-meta">
              <?= getCategoryLabel($categories, $exp['category']) ?>
              &bull; <?= htmlspecialchars($exp['date']) ?>
            </span>
          </div>
          <span class="exp-amount">$<?= number_format($exp['amount']) ?></span>
          <div class="exp-actions">
            <button class="btn-icon" title="Edit"
              onclick="openEditModal(<?= $exp['id'] ?>, '<?= addslashes($exp['name']) ?>', '<?= $exp['category'] ?>', '<?= $exp['date'] ?>', <?= $exp['amount'] ?>)">✏️</button>
            <button class="btn-icon btn-del" title="Delete"
              onclick="confirmDelete(<?= $exp['id'] ?>)">🗑️</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
 
  </div><!-- /.main -->
</div><!-- /.layout -->
 
 
<!-- ══════════════════════════════════════════════════════════════════════════
     ADD / EDIT MODAL
══════════════════════════════════════════════════════════════════════════ -->
<div id="modal-overlay" class="modal-overlay">
  <div class="modal" id="modal-box" onclick="event.stopPropagation()">
    <h2 id="modal-title">Add Expense</h2>
 
    <form method="POST" action="basic.php">
      <input type="hidden" name="action" id="form-action" value="add">
      <input type="hidden" name="id"     id="form-id"     value="">
 
      <label>Name
        <input type="text" name="name" id="form-name" placeholder="e.g. June rent" required>
      </label>
 
      <label>Category
        <select name="category" id="form-category" required>
          <option value="">— select —</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= $cat['icon'] ?> <?= htmlspecialchars($cat['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
 
      <label>Date
        <input type="date" name="date" id="form-date"
               value="<?= date('Y-m-d') ?>" required>
      </label>
 
      <label>Amount ($)
        <input type="number" name="amount" id="form-amount"
               placeholder="0" min="0" step="0.01" required>
      </label>
 
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
        <button type="submit" class="btn-save">Save</button>
      </div>
    </form>
  </div>
</div>
 
 
<!-- ══════════════════════════════════════════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════════════════════════════════════════ -->
<div id="del-overlay" class="modal-overlay">
  <div class="modal modal-sm" onclick="event.stopPropagation()">
    <h2>Delete Expense?</h2>
    <p>This action cannot be undone.</p>
    <form method="POST" action="basic.php">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id"     id="del-id" value="">
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeDelModal()">Cancel</button>
        <button type="submit" class="btn-del-confirm">Delete</button>
      </div>
    </form>
  </div>
</div>
 
 
<!-- ══════════════════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════════════════ -->
<script>
// ── Grab overlays ─────────────────────────────────────────────────────────────
var overlay    = document.getElementById('modal-overlay');
var delOverlay = document.getElementById('del-overlay');
 
// Close add/edit modal when clicking the dark backdrop
overlay.addEventListener('click', function(e) {
  if (e.target === overlay) closeAddModal();
});
 
// Close delete modal when clicking the dark backdrop
delOverlay.addEventListener('click', function(e) {
  if (e.target === delOverlay) closeDelModal();
});
 
// ── Add modal ─────────────────────────────────────────────────────────────────
function openModal() {
  document.getElementById('modal-title').textContent    = 'Add Expense';
  document.getElementById('form-action').value          = 'add';
  document.getElementById('form-id').value              = '';
  document.getElementById('form-name').value            = '';
  document.getElementById('form-category').value        = '';
  document.getElementById('form-date').value            = new Date().toISOString().slice(0, 10);
  document.getElementById('form-amount').value          = '';
  overlay.style.display = 'flex';
}
 
function closeAddModal() {
  overlay.style.display = 'none';
}
 
// ── Edit modal ────────────────────────────────────────────────────────────────
function openEditModal(id, name, category, date, amount) {
  document.getElementById('modal-title').textContent    = 'Edit Expense';
  document.getElementById('form-action').value          = 'edit';
  document.getElementById('form-id').value              = id;
  document.getElementById('form-name').value            = name;
  document.getElementById('form-category').value        = category;
  document.getElementById('form-date').value            = date;
  document.getElementById('form-amount').value          = amount;
  overlay.style.display = 'flex';
}
 
// ── Delete modal ──────────────────────────────────────────────────────────────
function confirmDelete(id) {
  document.getElementById('del-id').value = id;
  delOverlay.style.display = 'flex';
}
 
function closeDelModal() {
  delOverlay.style.display = 'none';
}
 
// ── Category filter ───────────────────────────────────────────────────────────
function filterByCategory(cat) {
  const items   = document.querySelectorAll('.expense-item');
  const heading = document.getElementById('list-heading');
  const clearBtn = document.getElementById('clear-btn');
 
  items.forEach(item => {
    item.style.display = (item.dataset.category === cat) ? 'flex' : 'none';
  });
 
  // Capitalise first letter for heading
  heading.textContent = cat.charAt(0).toUpperCase() + cat.slice(1) + ' Expenses';
  clearBtn.style.display = 'inline-flex';
 
  // Highlight active card
  document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
  document.querySelectorAll('.cat-card').forEach(c => {
    if (c.querySelector('.cat-label').textContent.toLowerCase() === cat.toLowerCase()) {
      c.classList.add('active');
    }
  });
}
 
function clearFilter() {
  document.querySelectorAll('.expense-item').forEach(item => item.style.display = 'flex');
  document.getElementById('list-heading').textContent = 'All Expenses';
  document.getElementById('clear-btn').style.display = 'none';
  document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
}
 
// ── Auto-hide toast ───────────────────────────────────────────────────────────
const toast = document.querySelector('.toast');
if (toast) setTimeout(() => toast.style.opacity = '0', 3000);
</script>
 
</body>
</html>
 