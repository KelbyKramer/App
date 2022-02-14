<?php
include("functions.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($_POST['func'] == "verifyDateAndLoc"){
    //echo "Success";
    //echo $_POST['lat'];
    //echo $_POST['long'];
    date_default_timezone_set('America/Chicago'); // CDT

    $current_date = date('Y/m/d');
    $current_time = date('H:i:s');
    $error = False;
    //backlog check
    session_start();
    $where = array("User_ID" => $_SESSION['id'], "event_id" => $_POST['eventID']);
    $result = query("eventbacklog", "*", $where);
    //var_dump($result);

    if ($result->num_rows > 0) {
      $error = True;
    }
    //time check
    $sql = "SELECT events.*, locations.latLong FROM events INNER JOIN locations ON events.Loc_ID=locations.Loc_ID WHERE events.Event_ID=".$_POST['eventID'];
    $conn = dbConnect();
    $result = mysqli_query($conn, $sql);
    //$arr = array("event_ID" => $_POST['eventID']);
    //$result = query("events", "*", $arr);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        //var_dump($row);
        $start = $row['start'];
        $finish = $row['finish'];
        $date = $row['date'];
        $reward = $row['tokens'];
        $coords = $row['latLong'];
      }
    }
    //TODO: Refactor this into a function

    $event_coords = explode(",", $coords);
    $topLeftLat = floatval($event_coords[0]);
    $topLeftLong = floatval($event_coords[1]);
    $botRightLat = floatval($event_coords[2]);
    $botRightLong = floatval($event_coords[3]);
    $user_coordinates = explode(" ", $_POST['coordinates']);
    $user_latitude = floatval($user_coordinates[0]);
    $user_longitude = floatval($user_coordinates[1]);
    //echo $coords;
    echo " user lat ".$user_latitude;
    echo " user long ".$user_longitude;

    echo " TL Lat ".$topLeftLat;
    echo " TL Long ".$topLeftLong;
    echo " BR Lat ".$botRightLat;
    echo " BR Long ".$botRightLong;

    //Do compare right here
    $Loc_error = False;
    if($topLeftLat < $user_latitude){
      $Loc_error = True;
    }

    if($botRightLat > $user_latitude){
      $Loc_error = True;
    }

    if($topLeftLong > $user_longitude){
      $Loc_error = True;
    }

    if($botRightLong < $user_longitude){
      $Loc_error = True;
    }

    if($Loc_error == True){
      echo "Not in location area";
    }
    else{
      echo "In location area";
    }
  /*
    if ($date != $current_date){
      $error = True;
    }
    //before event
    if($start > $current_time){
      $error = True;
    }

    //after event
    if ($finish < $current_time){
      $error = True;
    }*/
    /*
    echo $current_date;
    echo $current_time;
    echo $start;
    echo $finish;
    echo $error;
    */
    if ($error || $Loc_error){
      echo "There was an error";
      //TODO: return custom warnings based on error
    }
    else{
      //not an error, do token redemption
      $tokens = $_SESSION['tokens'];
      $newTokens = $tokens + $reward;
      $newTotalTokens = 0;
      $conn = dbConnect();
      $sql = "UPDATE users SET current_tokens=".$newTokens." WHERE User_ID=".$_SESSION['id'];
      echo $sql;
      $result = mysqli_query($conn, $sql);
      $_SESSION['tokens'] = $newTokens;

      //$sql = "INSERT INTO eventbacklog ('User_ID', 'event_ID') VALUES ("., 1)";

      $user_id = $_SESSION['id'];
      $event_id = $_POST['eventID'];

      //$con = new mysqli($user, $pass, $db);
      $stmt = $conn->prepare("INSERT INTO eventbacklog (User_ID, event_ID) VALUES (?, ?)");
      $stmt->bind_param("ss", $user_id , $event_id);
      $stmt->execute();
      //token redemption and updating db

      //backlog append
      echo "Great success!";
    }





  }
  else if ($_POST['func'] == "loadHome"){
    //Events information
    $conn = dbConnect();
    $sql = "SELECT events.Event_ID, events.type, events.tokens, events.start, events.finish, events.date, locations.name, locations.address, locations.latLong FROM events INNER JOIN locations ON events.Loc_ID=locations.Loc_ID";
    $result = $conn->query($sql);

    $arr = generateEventArray($result);
    $html =  generateHTML($arr);
    //events html
    echo $html;
    //Promos information
    $sql2 = "SELECT promos.promo_ID, promos.cost, promos.Title, promolinks.redeem_code, promolinks.business_name FROM promos INNER JOIN promolinks ON promos.business_id=promolinks.business_id";
    $result2 = $conn->query($sql2);

    $arr2 = generateEventArray($result2);
    $html2 = generatePromoHTML($arr2);
    echo $html2;

    //User promos information
    $sql3 = "";
    //Leaderboard information

  }
  else if($_POST['func'] == "purchasePromo"){
    $promo_id = $_POST['promo_id'];
    session_start();
    $id = $_SESSION['id'];
    $tokens = $_SESSION['tokens'];

    $conn = dbConnect();
    $sql = "SELECT cost FROM Promos WHERE Promo_ID=".$promo_id;
    $sanitized_sql = mysqli_real_escape_string($conn, $sql);
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $cost = $row['cost'];
      }
    }
    else{
      echo "<center>This Promo does not exist</center>";
    }

    $sql = "SELECT * FROM userpromos WHERE User_ID=".$id." AND Promo_ID=".$promo_id;
    $sanitized_sql = mysqli_real_escape_string($conn, $sql);
    $result = mysqli_query($conn, $sql);
    $error = False;
    if ($result->num_rows > 0) {
      $html = "You already have this promo, use it first before you purchase it again!";
      $error = True;
    }

    if($tokens < $cost){
      $html = "You don't have enough tokens to purchase this!";
      $error = True;
    }

    if($error == False){
      //purchase the promo
      $_SESSION['tokens'] -= $cost;
      $sql = "INSERT INTO userpromos (User_ID, Promo_ID) VALUES ($id, $promo_id)";
      $sanitized_sql = mysqli_real_escape_string($conn, $sql);
      $result = mysqli_query($conn, $sql);
      $sql = "UPDATE users SET current_tokens=".$_SESSION['tokens']." WHERE User_ID=".$id;
      $sanitized_sql = mysqli_real_escape_string($conn, $sql);
      $result = mysqli_query($conn, $sql);
      $html = "Promo has been purchased!";

    }
    echo $html;
  }
  else if($_POST['func'] == "displayPurchasePromos"){
    $conn = dbConnect();
    $sql2 = "SELECT promos.promo_ID, promos.cost, promos.Title, promolinks.redeem_code, promolinks.business_name FROM promos INNER JOIN promolinks ON promos.business_id=promolinks.business_id";
    $result2 = $conn->query($sql2);

    $arr2 = generateEventArray($result2);
    $html2 = generatePromoHTML($arr2);
    echo $html2;
  }
  else if($_POST['func'] == "displayEvents"){

    $conn = dbConnect();
    $sql = "SELECT events.Event_ID, events.type, events.tokens, events.start, events.finish, events.date, locations.name, locations.address, locations.latLong FROM events INNER JOIN locations ON events.Loc_ID=locations.Loc_ID";
    $result = $conn->query($sql);

    $arr = generateEventArray($result);
    $html =  generateHTML($arr);
    echo $html;
  }
  else if($_POST['func'] == "displayMyPromos"){

    $conn = dbConnect();
    session_start();
    $where = array("User_ID" => $_SESSION['id']);
    $result2 = query("userpromos", "Promo_ID", $where);
    //$sql2 = "SELECT Promo_ID FROM userpromos WHERE User_ID=".$_SESSION['id'];
    $ret = array();
    //$result2 = $conn->query($sql2);
    //var_dump($result2);
    if ($result2->num_rows > 0) {
      while($row = $result2->fetch_assoc()) {
        array_push($ret, $row['Promo_ID']);
      }
    }

    $sql = "SELECT promos.Promo_ID, promos.Title, promos.business_id, promolinks.redeem_code, promolinks.business_name FROM promos INNER JOIN promolinks ON promos.business_id=promolinks.business_id WHERE";
    $final = queryUserPromos($sql, $ret);

    $ret = array();
    if ($final) {
      while($row = $final->fetch_assoc()) {
        //var_dump($row);
        array_push($ret, $row);
      }
      $html3= generateRedeemPromoHTML($ret);
      echo $html3;
    }
    else{
      echo "You haven't purchased any promos";
    }
  }
  else if($_POST['func'] == "redeemPromo"){
    $code = $_POST['redeem_code'];
    $id = $_POST['promo_id'];

    session_start();
    //Check if user has the promo in userpromos purchased based on promo ID
    $result = query("userpromos", "*", array("User_ID" => $_SESSION['id'], "Promo_ID" => $id));
    //var_dump($result);

    if($result->num_rows == 0){
      //error, user doesn't have promo
      echo "You don't actually have this promo legitimately purchased";
    }
    else{
      //have the promo
      $sql = "SELECT promolinks.redeem_code FROM promos INNER JOIN promolinks ON promos.business_id = promolinks.business_id WHERE promos.promo_ID=".$id;

      $conn = dbConnect();
      $result = mysqli_query($conn, $sql);
      //$arr = array("event_ID" => $_POST['eventID']);
      //$result = query("events", "*", $arr);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          //var_dump($row);
          $dbCode = $row['redeem_code'];
        }
      }

      //echo $dbCode;


      if($dbCode != $code){
        //error, code was entered wrong
        echo "wrong code was entered";
      }
      else{
        echo "Success";

        $sql = "DELETE FROM userpromos WHERE User_ID=".$_SESSION['id']." AND Promo_ID=".$id;
        $result = mysqli_query($conn, $sql);
      }
    }

    //After, delete promo from userpromos

    //return confirmation message

  }
}
else{
  header("Location: ../restrict.php");
}


 ?>
