<?php 
session_start();
require_once "db.php";
$pdo = getDb();

$today = date('Y-m-d');

// 前回ログイン日を取得
$stmt = $pdo->prepare("
SELECT value
FROM settings
WHERE name='last_login'
");
$stmt->execute();

$lastLogin = $stmt->fetchColumn();

if ($lastLogin != $today) {

    // streak を +1
    $stmt = $pdo->prepare("
    UPDATE settings
    SET value = value + 1
    WHERE name='streak'
    ");
    $stmt->execute();

    // 今日の日付を保存
    $stmt = $pdo->prepare("
    UPDATE settings
    SET value = ?
    WHERE name='last_login'
    ");
    $stmt->execute([$today]);
}

//monthly income start------
$currentMonth = date('Y-m');

$stmt = $pdo->prepare("
SELECT value
FROM settings
WHERE name='month_closed'
");

$stmt->execute();

$closedMonth = $stmt->fetchColumn();



//========================
// Savingを保存
//========================
if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
    && isset($_POST['target_amount'])
) {

    $targetAmount = (int)$_POST['target_amount'];

    $stmt = $pdo->prepare("
        INSERT INTO savings(month,target_amount)
        VALUES(?,?)
        ON DUPLICATE KEY UPDATE
        target_amount=VALUES(target_amount)
    ");

    $stmt->execute([
        $currentMonth,
        $targetAmount
    ]);
}
//-------------------------
// Monthly Income
//-------------------------

$stmt = $pdo->prepare("
SELECT amount
FROM incomes
WHERE month=?
");

$stmt->execute([$currentMonth]);

$monthlyIncome = $stmt->fetchColumn() ?: 0;

//保存したSavingを読み込み
$stmt = $pdo->prepare("
SELECT target_amount
FROM savings
WHERE month=?
");

$stmt->execute([$currentMonth]);

$targetAmount = $stmt->fetchColumn();

if($targetAmount === false){
    $targetAmount = 0;
}

//Total spending start------
$stmt = $pdo->prepare("
SELECT IFNULL(SUM(amount),0)
FROM expenses
WHERE DATE_FORMAT(date,'%Y-%m')=?
");

$stmt->execute([$currentMonth]);

$totalSpending = $stmt->fetchColumn();

//Budget start------
$stmt = $pdo->query("
SELECT category,amount
FROM budgets
");

$budgets = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $budgets[$row['category']] = $row['amount'];
}

//各カテゴリーの支出
$stmt = $pdo->prepare("
SELECT
c.category_type,
SUM(e.amount) total
FROM expenses e
JOIN categories c
ON e.category=c.id
WHERE DATE_FORMAT(e.date,'%Y-%m')=?
GROUP BY c.category_type
");

$stmt->execute([$currentMonth]);

$basicCost = 0;
$entertainmentCost = 0;
$emergencyCost = 0;

while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

    switch($row['category_type']){

        case 'basic':
            $basicCost=$row['total'];
            break;

        case 'entertainment':
            $entertainmentCost=$row['total'];
            break;

        case 'emergency':
            $emergencyCost=$row['total'];
            break;
    }
}

$totalBudget =
    $budgets['basic']
    + $budgets['entertainment']
    + $budgets['emergency'];

//saving goal start------
$stmt = $pdo->prepare("
SELECT value
FROM settings
WHERE name='saving_goal'
");

$stmt->execute();

$savingGoal = (int)$stmt->fetchColumn();
//amount left start------
//$amountLeft = $monthlyIncome - ($totalSpending + $savingInput);
$actualSaving = $monthlyIncome - $totalSpending;

if($actualSaving < 0){
    $actualSaving = 0;
}

$amountLeft = $monthlyIncome - $totalSpending - $targetAmount;

if($amountLeft < 0){
    $amountLeft = 0;
}


// $actualSaving = $savingInput + $amountLeft;

if ($actualSaving > $targetAmount) {

    $achievement = "gold";

} elseif ($actualSaving == $targetAmount) {

    $achievement = "silver";

} else {

    $achievement = "failed";

}


$savingRate = $monthlyIncome > 0
    ? round($actualSaving / $monthlyIncome * 100)
    : 0;

$leftRate = $monthlyIncome > 0
    ? round($amountLeft / $monthlyIncome * 100)
    : 0;

//daliy streak start------
$stmt=$pdo->prepare("
SELECT value
FROM settings
WHERE name='streak'
");

$stmt->execute();

$streak=$stmt->fetchColumn();

//Greeting start------
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt=$pdo->prepare("
SELECT name
FROM users
WHERE user_id=?
");

$stmt->execute([$userId]);

$userName=$stmt->fetchColumn();

//Progress bar start------
$basicRate = ($budgets['basic'] > 0)
    ? min(100, round($basicCost / $budgets['basic'] * 100))
    : 0;

$entertainmentRate = ($budgets['entertainment'] > 0)
    ? min(100, round($entertainmentCost / $budgets['entertainment'] * 100))
    : 0;

$emergencyRate = ($budgets['emergency'] > 0)
    ? min(100, round($emergencyCost / $budgets['emergency'] * 100))
    : 0;

//Budget Status start------
function budgetStatus($rate)
{
    if ($rate < 70) return "On Track";
    if ($rate <= 100) return "Watch";
    return "Over Budget";
}

//Spending Rate start------
$spendingRate = $monthlyIncome > 0
    ? round($totalSpending / $monthlyIncome * 100)
    : 0;

if (isset($_POST['finish_month'])) {

    $deposit = $actualSaving;

    //analytics_historyへ保存
    $stmt = $pdo->prepare("
INSERT INTO analytics_history
(
    month,
    income,
    spending,
    saving,
    basic,
    entertainment,
    emergency
)
VALUES(?,?,?,?,?,?,?)
");

$stmt->execute([
    $currentMonth,
    $monthlyIncome,
    $totalSpending,
    $actualSaving,
    $basicCost,
    $entertainmentCost,
    $emergencyCost
]);

    // Total Savingsへ追加
    $stmt = $pdo->prepare("
        UPDATE settings
        SET value = value + ?
        WHERE name='total_savings'
    ");
    $stmt->execute([$deposit]);

    // Incomeを0
    $stmt = $pdo->prepare("
        UPDATE incomes
        SET amount = 0
        WHERE month = ?
    ");
    $stmt->execute([$currentMonth]);

    // 今月の支出を削除
    $stmt = $pdo->prepare("
        DELETE FROM expenses
        WHERE DATE_FORMAT(date,'%Y-%m') = ?
    ");
    $stmt->execute([$currentMonth]);

$stmt=$pdo->prepare("
UPDATE savings
SET
saving_amount=?,
achievement=?
WHERE month=?
");

$stmt->execute([
    $actualSaving,
    $achievement,
    $currentMonth
]);

    // Savingを0
    // $stmt = $pdo->prepare("
    //     UPDATE savings
    //     SET saving_amount = 0,
    //         achievement = 'failed'
    //     WHERE month = ?
    // ");
    // $stmt->execute([$currentMonth]);

    // 月締めフラグ
    // $stmt = $pdo->prepare("
    //     UPDATE settings
    //     SET value = ?
    //     WHERE name='month_closed'
    // ");
    // $stmt->execute([$currentMonth]);

    header("Location: dashboard.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Money Saver App</title>

<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/dashboard.css">
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

  <!-- <div class="main"> -->
  <div class="main container-fluid mt-4">

    <div class="header">
      <div class="greeting-container">
        <h2 id="greeting">Hello, <?php echo htmlspecialchars($userName); ?> 👋</h2>
        <div class="header-streak">🔥 <span id="headerStreak"><?php echo htmlspecialchars($streak); ?></span></div>
        <form method="POST">
          <button
    name="finish_month"
    class="btn btn-success"
    <?= $closedMonth == $currentMonth ? 'disabled' : '' ?>>
    Finish Month
</button>
        </form>
      </div>

    
    </div>

    <div class="summary-grid">

      <div class="summary-card" onclick="goPage('monthly_income.php')" style="cursor:pointer;">
        <div class="icon-box green-bg">💵</div>
        <div class="info">
          <p>Monthly Income</p>
          <h2 id="monthly-income" class="income">¥<?php echo number_format($monthlyIncome); ?></h2>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box red-bg">📉</div>
        <div class="info">
          <p>Total Spending</p>
          <h2 id="total-spending" class="total_spending">¥<?php echo number_format($totalSpending); ?></h2>
          <div class="small-bar"><div id="spendingPercent" style="width: <?php echo $spendingRate; ?>%"></div></div>
          <span id="spendingRate"><?php echo $spendingRate; ?>% of income</span>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box yellow-bg">🐷</div>
        <div class="info">
          <p>This Month Savings</p>
          <!-- <input type="number" id="monthly-savings-input" placeholder="Enter amount"> -->
           <form method="post">

              <input
              type="number"
              name="target_amount"
              value="<?= $targetAmount ?>">

              <button class="btn btn-success">
              Save
              </button>

              </form>
          <div class="small-bar"><div id="savingPercent" style="width: <?php echo $savingRate; ?>%"></div></div>
          <span id="savingRate"><?php echo $savingRate; ?>% of income</span>
        </div>
      </div>

      <div class="summary-card">
        <div class="icon-box blue-bg">💳</div>
        <div class="info">
          <p>Amount Left</p>
          <h2 id="amount-left" class="amount_left">¥<?php echo number_format($amountLeft); ?></h2>
          <div class="small-bar"><div id="leftPercent" style="width: <?php echo $leftRate; ?>%"></div></div>
          <span id="leftRate"><?php echo $leftRate; ?>% of income</span>
        </div>
      </div>

    </div>

    <div class="focus-card" id="focus-card">

    <div class="focus-left">
        <div class="focus-icon">⚠️</div>

        <div class="focus-content">
            <h3>Spending Focus</h3>

            <p id="focusText">
                Checking your spending...
            </p>
        </div>
    </div>

    <button class="analysis-btn"
            onclick="goPage('analytics.php')">
        View Analysis →
    </button>

</div>


    <!-- Basic -->
    <div class="budget-item" onclick="goPage('basic.php')">

        <div class="budget-header">
            <span>🏠 Basic Cost</span>
            <span id="basicCostAmount">¥<?php echo number_format($basicCost); ?> / ¥<?php echo number_format($budgets['basic']); ?></span>
        </div>

        <div class="bar">
            <div id="basicFill" class="green-fill" style="width: <?php echo $basicRate; ?>%"></div>
        </div>

        <div id="basicStatus" class="budget-status">
            <?php echo budgetStatus($basicRate); ?>
        </div>

    </div>


    <!-- Entertainment -->
    <div class="budget-item" onclick="goPage('entertainment.php')">

        <div class="budget-header">
            <span>🎮 Entertainment</span>
            <span id="entertainmentAmount">¥<?php echo number_format($entertainmentCost); ?> / ¥<?php echo number_format($budgets['entertainment']); ?></span>
        </div>

        <div class="bar">
            <div id="entertainmentFill" class="pink-fill" style="width: <?php echo $entertainmentRate; ?>%"></div>
        </div>

        <div id="entertainmentStatus" class="budget-status">
            <?php echo budgetStatus($entertainmentRate); ?>
        </div>

    </div>


    <!-- Emergency -->
    <div class="budget-item" onclick="goPage('emergency.php')">

        <div class="budget-header">
            <span>⚠️ Emergency</span>
            <span id="emergencyAmount">¥<?php echo number_format($emergencyCost); ?> / ¥<?php echo number_format($budgets['emergency']); ?></span>
        </div>

        <div class="bar">
            <div id="emergencyFill" class="red-fill" style="width: <?php echo $emergencyRate; ?>%"></div>
        </div>

        <div id="emergencyStatus" class="budget-status">
            <?php echo budgetStatus($emergencyRate); ?>
        </div>

    </div>

</div>

  

  </div>
</div>

<script>
function goPage(page) {
    window.location.href = page;
}

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: "en",
        includedLanguages: "en,ja,my,ko,zh-CN,th,vi,id,ne",
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
    }, "google_translate_element");
}
</script>

<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<!-- bootstrap-------- -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>