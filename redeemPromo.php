<?php
include("src/functions.php");
include("header.php");

session_start();
$id = $_GET['id'];
if($_SESSION['id'] != $id){
  header("location: restrict.php");
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $id = $_GET['id'];
  $promo_id = $_GET['promo_id'];
  $error = False;
  if(is_int($id) && is_int($promo_id)){
    echo "The user id and promo ids must be integers";
    $error = True;
  }

  if($_SESSION['id'] != $id){
    echo "Invalid user ID";
    $error = True;
  }

  if($error == False){
    $result = query("userpromos", "*", array("User_ID" => $id, "Promo_ID" => $promo_id));

    if($result->num_rows == 0){
      echo "<div>Unable to redeem.  You don't actually have this promo legitimately purchased.</div>";
}
    else{
      echo "Promo has been successfully redeemed.";
      deleteQuery("userpromos", "User_ID=".$id." AND Promo_ID=".$promo_id);
    }
  }
  else{
    echo "There was an error";
  }
  echo "<button style='height: 100px; width:200px; background-color: #2a2a72; background-image: linear-gradient(315deg, #2a2a72 0%, #009ffd 74%);'><a style='text-decoration:none; color:black;' href='dashboard.php?id=".$_SESSION['id']."'>Click here to go back to your dashboard</a></button>";
}
 ?>

<html>
<form method='post' action='' id='form'>
  <img src='src/FreeBreakfast.png'></img>
  <div id='div' style='text-align: center;'>NOTE: Clicking the button will redeem the promo.  Make sure an employee is present.</div>
  <button id='button' style='position: absolute; height: 100px; width:200px; left: 20%;background-color: #2a2a72; background-image: linear-gradient(315deg, #2a2a72 0%, #009ffd 74%);' type="submit" id="submit" onclick='redirect()'>Redeem this Promo</button>
</form>
</html>

<script>
  function redirect(){
    var el = document.getElementById('form');
    //var el2 = document.getElementById('button');

    el.style.display = 'none';
    //el2.style.visibility = 'hidden';
  }

</script>
