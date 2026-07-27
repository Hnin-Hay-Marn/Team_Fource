<?php
require_once 'db.php';

 
$pdo = getDb();
 
$stmt = $pdo->query("SELECT * FROM goals ORDER BY slot_no");
$goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
$stmt = $pdo->prepare("SELECT value FROM settings WHERE name='total_savings'");
$stmt->execute();
$totalSavings = $stmt->fetchColumn() ?: 0;
 
$stmt = $pdo->query("
SELECT *
FROM goal_collection
ORDER BY completed_at DESC
");
 
$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>貯金目標</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/goals.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
 
<body>
  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/sidebar.php'; ?>

<div class="main container-fluid mt-4">
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
let goals = <?= json_encode($goals) ?>;
 
let collections = <?= json_encode($collections) ?>;
 
let selectedIndex = null;
 
function yen(n) {
  return "¥" + Number(n).toLocaleString();
}
 
function getDashboardMoney(){

    return {
        totalSavings: <?= (int)$totalSavings ?>
    };

}
 

 
function renderGoals() {
  const grid = document.getElementById("goalsGrid");
  const money = getDashboardMoney();
 
  grid.innerHTML = "";
 
  goals.forEach((goal, index) => {
    if (goal.name === null) {
      grid.innerHTML += `
        <div class="empty-goal" onclick="openGoalModal(${index})">
          <div class="plus">+</div>
          <h3>Goal Slot ${index + 1}</h3>
          <p>Add New Goal</p>
        </div>
      `;
    } else {
      const target = Number(goal.target_amount) || 0;
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
            <small>Total Savings : ${yen(money.totalSavings)}</small>
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
 
function renderCollection() {
 
    const container =
        document.getElementById("goalCollection");
    if(collections.length === 0){
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
        ${collections.map(item => `
            <div class="collection-card">
                ${item.image
                    ? `<img src="${item.image}">`
                    : ""}
                <h3>${item.name}</h3>
                <p>Amount : ${yen(item.target_amount)}</p>
                <p>
                    Completed :
                    ${item.completed_at}
                </p>
                <button
                    class="delete-btn"
                    onclick="deleteCollectionGoal(${item.id})">
                    Delete
                </button>
            </div>
        `).join("")}
        </div>
    `;
}
 
 
function openGoalModal(index) {
  selectedIndex = index;
 
  if (goals[index].name !== null) {

    document.getElementById("goalName").value =
        goals[index].name;

    document.getElementById("goalTarget").value =
        goals[index].target_amount;

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
 
function saveGoal(){
 
    const name =
        document.getElementById("goalName").value.trim();
 
    const target =
        document.getElementById("goalTarget").value;
       
    const imageInput =
        document.getElementById("goalImage");
    if(name=="" || target<=0){
        alert("Please enter a valid goal.");
        return;
    }
    function upload(image){
        fetch("save_goal.php",{
            method:"POST",
            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body:
                "slot="+goals[selectedIndex].slot_no+
                "&name="+encodeURIComponent(name)+
                "&target="+target+
                "&image="+encodeURIComponent(image)
        })
        .then(res=>res.text())
        .then(data => {

    console.log("Response =", data);

    if(data.trim() === "success"){
        alert("Goal saved!");
        location.reload();
    }else{
        alert("Response: " + data);
    }
})
.catch(err => {
    console.error(err);
    alert("Save failed.");
});
    }
    if(imageInput.files.length>0){
        const reader=new FileReader();
        reader.onload=function(e){
            upload(e.target.result);
        };
        reader.readAsDataURL(imageInput.files[0]);
    }else{
        upload(goals[selectedIndex].image || "");
    }
}
 
function saveGoalToDB(name,target,image){
    fetch("save_goal.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:
            "slot="+goals[selectedIndex].slot_no+
            "&name="+encodeURIComponent(name)+
            "&target="+target+
            "&image="+encodeURIComponent(image)
    })
    .then(res=>res.text())
    .then(data=>{
        if(data=="success"){
            location.reload();
        }else{
            alert(data);
        }
    });
}
 
function completeGoal(index){
 
    if(!confirm("Complete this goal?")){
        return;
    }
    fetch("complete_goal.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"slot="+goals[index].slot_no
    })
 
    .then(res => res.text())
.then(data => {
    console.log("Response =", data);

    if (data.trim() === "success") {
        alert("Goal completed!");
        location.reload();
    } else {
        alert("Response: " + data);
    }
});
}
 
 
function deleteGoal(index){
    if(!confirm("Delete this goal?")){
        return;
    }
 
    fetch("delete_goal.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"slot="+goals[index].slot_no
    })
 
    .then(res => res.text())
    .then(data => {

        console.log("Response =", data);

        if(data.trim() === "success"){
            alert("Goal deleted!");
            location.reload();
        }else{
            alert("Response: " + data);
        }
    })

    .catch(err => {
        console.error(err);
        alert("Delete failed.");
    });
}
 
function deleteCollectionGoal(id){
    if(!confirm("Delete this completed goal?")){
        return;
    }
    fetch("delete_collection.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:"id="+id
    })
    .then(res=>res.text())
    .then(data => {

    console.log("Response =", data);

    if(data.trim() === "success"){
        alert("Deleted!");
        location.reload();
    }else{
        alert("Response: " + data);
    }
})
.catch(err => {
    console.error(err);
    alert("Delete failed.");
});
}
 
renderGoals();
renderCollection();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
 
</body>
</html>