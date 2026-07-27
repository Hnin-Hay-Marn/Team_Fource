<?php
require_once "db.php";

$pdo = getDb();

$slot = isset($_POST["slot"])
    ? (int)$_POST["slot"]
    : 0;

if ($slot <= 0) {
    exit("Invalid slot");
}

$stmt = $pdo->prepare("
UPDATE goals
SET
    name = NULL,
    target_amount = NULL,
    image = NULL
WHERE slot_no = ?
");

$stmt->execute([$slot]);

echo "success";