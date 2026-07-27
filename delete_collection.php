<?php
require_once "db.php";

$pdo = getDb();

$id = isset($_POST["id"])
    ? (int)$_POST["id"]
    : 0;

if ($id <= 0) {
    exit("Invalid ID");
}

$stmt = $pdo->prepare("
DELETE FROM goal_collection
WHERE id = ?
");

$stmt->execute([$id]);

echo "success";
?>