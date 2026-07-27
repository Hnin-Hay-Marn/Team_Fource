<?php
require_once "db.php";

$pdo = getDb();

$slot = isset($_POST["slot"])
    ? (int)$_POST["slot"]
    : 0;

if($slot<=0){
    exit("Invalid slot");
}

$pdo->beginTransaction();

try{

    // Goal取得
    $stmt=$pdo->prepare("
    SELECT *
    FROM goals
    WHERE slot_no=?
    ");
    $stmt->execute([$slot]);

    $goal=$stmt->fetch(PDO::FETCH_ASSOC);

    if(!$goal){
        throw new Exception("Goal not found");
    }

    // Total Savings取得
    $stmt=$pdo->prepare("
    SELECT value
    FROM settings
    WHERE name='total_savings'
    ");
    $stmt->execute();

    $total=(int)$stmt->fetchColumn();

    if($total<$goal["target_amount"]){
        throw new Exception("Not enough savings");
    }

    // Total Savings更新
    $total-=$goal["target_amount"];

    $stmt=$pdo->prepare("
    UPDATE settings
    SET value=?
    WHERE name='total_savings'
    ");
    $stmt->execute([$total]);

    // Collectionへ保存
    $stmt=$pdo->prepare("
    INSERT INTO goal_collection
    (
        name,
        target_amount,
        image
    )
    VALUES(?,?,?)
    ");

    $stmt->execute([
        $goal["name"],
        $goal["target_amount"],
        $goal["image"]
    ]);

    // Goal削除
    $stmt=$pdo->prepare("
    UPDATE goals
    SET
        name=NULL,
        target_amount=NULL,
        image=NULL
    WHERE slot_no=?
    ");

    $stmt->execute([$slot]);

    $pdo->commit();

    echo "success";

}catch(Exception $e){

    $pdo->rollBack();

    echo $e->getMessage();

}