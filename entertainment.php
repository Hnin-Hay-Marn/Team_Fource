<?php
require_once 'db.php';
$pdo = getDb();

// Entertainment Budget
$stmt = $pdo->prepare("
    SELECT amount
    FROM budgets
    WHERE category='entertainment'
");
$stmt->execute();

$entertainmentBudget = $stmt->fetchColumn();

// ── Handle add/edit/delete (POST) ─────────────────────────────────────────────
$action = $_POST['action'] ?? '';

if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    ($_POST['action'] ?? '') == 'save_budget'
) {

    $budget = (int)$_POST['budget'];

    $stmt = $pdo->prepare("
        UPDATE budgets
        SET amount=?
        WHERE category='entertainment'
    ");

    $stmt->execute([$budget]);

    header("Location: entertainment.php");
    exit;
}

if ($action === 'add') {
    $stmt = $pdo->prepare("INSERT INTO expenses (name, category, date, amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['category'],
        $_POST['date'],
        $_POST['amount'],
    ]);
    header('Location: entertainment.php?message=' . urlencode('🎉 Added!'));
    exit;
} elseif ($action === 'edit') {
    $stmt = $pdo->prepare("UPDATE expenses SET name = ?, category = ?, date = ?, amount = ? WHERE id = ?");
    $stmt->execute([
        $_POST['name'],
        $_POST['category'],
        $_POST['date'],
        $_POST['amount'],
        $_POST['id'],
    ]);
    header('Location: entertainment.php?message=' . urlencode('✏️ Updated!'));
    exit;
} elseif ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    header('Location: entertainment.php?message=' . urlencode('🗑️ Deleted!'));
    exit;
}

$message = $_GET['message'] ?? '';

// ── Entertainment categories ───────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM categories WHERE category_type = 'entertainment' ORDER BY label");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $pdo->prepare(
    "SELECT * FROM expenses
     WHERE category IN (
         SELECT id FROM categories WHERE category_type = 'entertainment'
     )
     ORDER BY date DESC"
);
$stmt->execute();
$expenses = $stmt->fetchAll();

// Category totals
$categoryTotals = [];

$stmt = $pdo->query("
    SELECT category, SUM(amount) AS total
    FROM expenses
    GROUP BY category
");

foreach ($stmt->fetchAll() as $row) {
    $categoryTotals[$row['category']] = $row['total'];
}

// ── Helpers ───────────────────────────────────────────────────────────────
// Total entertainment budget start------------------------
//$total = array_sum(array_column($categories, 'budget'));
$stmt = $pdo->query("
    SELECT COALESCE(SUM(amount),0)
    FROM expenses
    WHERE category IN (
        SELECT id
        FROM categories
        WHERE category_type = 'entertainment'
    )
");

$total = $stmt->fetchColumn();
//total entertainment budget end------------------------

function getCategoryIcon(array $categories, string $id): string {
    foreach ($categories as $c) {
        if ($c['id'] === $id) return $c['icon'];
    }
    return '🎯';
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
<title>Entertainment – FOURCE</title>
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/entertainment.css"> <!-- reuse same CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
 
<body>
<?php include 'includes/navbar.php'; ?>
  <?php include 'includes/sidebar.php'; ?>

<div class="main container-fluid mt-4">
  
 
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
  <div style="display: flex; gap: 10px;">
    <button class="btn-add" onclick="openBudgetModal()">⚙️ Monthly Budget</button>
    <button class="btn-add" onclick="openModal()">＋ Add Expense</button>
  </div>
</div>
 
<!-- Category Grid -->
<div class="category-grid">
<?php foreach ($categories as $cat): ?>
  <div class="cat-card" onclick="filterByCategory('<?= $cat['id'] ?>')">
    <span class="cat-icon"><?= $cat['icon'] ?></span>

    <span class="cat-label">
      <?= htmlspecialchars($cat['label']) ?>
    </span>

    <span class="cat-amount">
      ¥<?= number_format($categoryTotals[$cat['id']] ?? 0) ?>
    </span>
  </div>
<?php endforeach; ?>
</div>
 
<!-- Total -->
<div class="total-bar">
  <span class="total-label">Total Entertainment Costs</span>
  <span class="total-amount">
    ¥<?= number_format($total) ?>
</span>
</div>
 
<!-- ── Expense list ──────────────────────────────────────────────────── -->
    <div class="section-title">
      <h3 id="list-heading">Recent Fun Spending</h3>
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
          <span class="exp-amount">¥<?= number_format($exp['amount']) ?></span>
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
  <div class="custom-modal" id="modal-box" onclick="event.stopPropagation()">
    <h2 id="modal-title">Add Expense</h2>
 
    <form method="POST" action="entertainment.php">
      <input type="hidden" name="action" id="form-action" value="add">
      <input type="hidden" name="id"     id="form-id"     value="">
 
      <label>Name
        <input type="text" name="name" id="form-name" placeholder="e.g. Cinema ticket" required>
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
 
      <label>Amount (¥)
        <input type="number" name="amount" id="form-amount"
               placeholder="0" min="0" step="1" required>
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
  <div class="custom-modal modal-sm" onclick="event.stopPropagation()">
    <h2>Delete Expense?</h2>
    <p>This action cannot be undone.</p>
    <form method="POST" action="entertainment.php">
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
     MONTHLY BUDGET MODAL
══════════════════════════════════════════════════════════════════════════ -->
<div id="budget-overlay" class="modal-overlay">
  <div class="custom-modal" id="budget-modal" onclick="event.stopPropagation()">
    <h2>Set Monthly Budget</h2>
    <p style="color: #666; margin-bottom: 20px;">Define your total monthly budget for entertainment</p>
    
    <form method="post">

    <input
        type="hidden"
        name="action"
        value="save_budget">

    <label>
    Monthly Budget (¥)

    <input
        type="number"
        name="budget"
        id="budget-input"
        value="<?= $entertainmentBudget ?>">

    </label>

    <div class="modal-actions">

    <button
    type="button"
    class="btn-cancel"
    onclick="closeBudgetModal()">
    Cancel
    </button>

    <button
    type="submit"
    class="btn-save">
    Save
    </button>

    </div>

</form>
    
  
  </div>
</div>



<script>
// ── Budget overlay ───────────────────────────────────────────────────────────
var budgetOverlay = document.getElementById('budget-overlay');
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

budgetOverlay.addEventListener('click', function(e) {
  if (e.target === budgetOverlay) closeBudgetModal();
});

// ── Budget modal ─────────────────────────────────────────────────────────────
function openBudgetModal() {
  // const saved = localStorage.getItem('entertainmentBudget');
  // document.getElementById('budget-input').value = saved || '50000';
  budgetOverlay.style.display = 'flex';
}

function closeBudgetModal() {
  budgetOverlay.style.display = 'none';
}

// function saveBudget() {
//   const budget = document.getElementById('budget-input').value;
//   if (!budget || budget <= 0) {
//     alert('Please enter a valid budget amount');
//     return;
//   }
//   localStorage.setItem('entertainmentBudget', budget);
//   alert('✅ Budget saved: ¥' + Number(budget).toLocaleString());
//   closeBudgetModal();
// }

function openModal() {
  document.getElementById('modal-title').textContent = 'Add Expense';
  document.getElementById('form-action').value       = 'add';
  document.getElementById('form-id').value           = '';
  document.getElementById('form-name').value         = '';
  document.getElementById('form-category').value     = '';
  document.getElementById('form-date').value         = new Date().toISOString().slice(0, 10);
  document.getElementById('form-amount').value       = '';
  overlay.style.display = 'flex';
}

function closeAddModal() {
  overlay.style.display = 'none';
}
 
// ── Edit modal ────────────────────────────────────────────────────────────────
function openEditModal(id, name, category, date, amount) {
  document.getElementById('modal-title').textContent = 'Edit Expense';
  document.getElementById('form-action').value       = 'edit';
  document.getElementById('form-id').value           = id;
  document.getElementById('form-name').value         = name;
  document.getElementById('form-category').value     = category;
  document.getElementById('form-date').value         = date;
  document.getElementById('form-amount').value       = amount;
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
  const items    = document.querySelectorAll('.expense-item');
  const heading  = document.getElementById('list-heading');
  const clearBtn = document.getElementById('clear-btn');
 
  items.forEach(item => {
    item.style.display = (item.dataset.category === cat) ? 'flex' : 'none';
  });
 
  heading.textContent = cat.charAt(0).toUpperCase() + cat.slice(1) + ' Expenses';
  clearBtn.style.display = 'inline-flex';
 
  document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
  document.querySelectorAll('.cat-card').forEach(c => {
    if (c.querySelector('.cat-label').textContent.toLowerCase() === cat.toLowerCase()) {
      c.classList.add('active');
    }
  });
}
 
function clearFilter() {
  document.querySelectorAll('.expense-item').forEach(item => item.style.display = 'flex');
  document.getElementById('list-heading').textContent = 'Recent Fun Spending';
  document.getElementById('clear-btn').style.display = 'none';
  document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
}

// ── Auto-hide toast ───────────────────────────────────────────────────────────
const toast = document.querySelector('.toast');
if (toast) setTimeout(() => toast.style.opacity = '0', 3000);

// Total entertainment spending sync
var entertainmentCost = <?= array_sum(array_column($expenses, 'amount')) ?>;
localStorage.setItem('dashboardEntertainmentCost', entertainmentCost);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>