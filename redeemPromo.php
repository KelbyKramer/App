<?php
include("src/functions.php");
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
      echo "Unable to redeem.  You don't actually have this promo legitimately purchased.";
    }
    else{
      echo "Promo has been successfully redeemed.  You will be redirected shortly";
      deleteQuery("userpromos", "User_ID=".$id." AND Promo_ID=".$promo_id);
    }
  }
  else{
    echo "There was an error";
  }
}
 ?>

<html>

<form method='post' action=''>
  <div>NOTE: Clicking the button will redeem the promo.  Make sure an employee is present.</div>
  <button type="submit" id="submit" onclick='redirect()'>Redeem this Promo</button>
</form>

</html>

<script>
function redirect(){
 var delay = 3000; // time in milliseconds

 // Display message
 //document.getElementById("message").innerHTML = "Please wait, you are redirecting to the new page.";

 setTimeout(function(){
    window.location.href = 'https://www.tutorialspoint.com/javascript/';
 }, 5000);

}

</script>
