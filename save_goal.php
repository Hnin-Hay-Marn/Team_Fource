<?php
require_once "db.php";

$pdo = getDb();

$slot = isset($_POST["slot"]) ? (int)$_POST["slot"] : 0;
$name = trim($_POST["name"] ?? "");
$target = (int)($_POST["target"] ?? 0);
$image = $_POST["image"] ?? "";

if ($slot <= 0) {
    exit("Invalid slot");
}

if ($name === "") {
    exit("Goal name is required");
}

if ($target <= 0) {
    exit("Target amount must be greater than 0");
}

$stmt = $pdo->prepare("
UPDATE goals
SET
    name = ?,
    target_amount = ?,
    image = ?
WHERE slot_no = ?
");

$stmt->execute([
    $name,
    $target,
    $image,
    $slot
]);

echo "success";