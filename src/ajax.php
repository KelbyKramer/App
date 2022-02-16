<?php
include("functions.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
  if($_POST['func'] == "verifyDateAndLoc"){

    date_default_timezone_set('America/Chicago'); // CDT

    $current_date = date('Y/m/d');
    $current_time = date('H:i:s');
    $error = False;
    //backlog check
    session_start();
    $where = array("User_ID" => $_SESSION['id'], "event_id" => $_POST['eventID']);
    $result = query("eventbacklog", "*", $where);

    if ($result->num_rows > 0) {
      echo "This promo already exists in the backlog";
    }
    else{
      $result = innerJoinQuery("events", "locations", "events.*", "locations.latLong", "events.Loc_ID=locations.Loc_ID", "events.Event_ID=".$_POST['eventID']);

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

      $event_coords = explode(",", $coords);
      $user_coordinates = explode(" ", $_POST['coordinates']);
      $Loc_error = coordinateCheck($event_coords, $user_coordinates);

      if($Loc_error == True){
        echo "Not in location area";
      }
      else{
        echo "In location area";
      }
      $time_Error = False;
      $date_Error = False;
      //$time_Error = timeCheck($start, $finish, $current_time);
      //$date_Error = dateCheck($date, $current_date);

      if ($Loc_error || $time_Error || $date_Error){
        echo "There was an error";
        if($Loc_error){
          echo "You are not in the location radius";
        }

        if($time_Error){
          echo "You are not within the specified time for this event";
        }

        if($date_Error){
          echo "You are not within the specified date for this event";
        }
      }
      else{
        //not an error, do token redemption
        $tokens = $_SESSION['tokens'];
        $newTokens = $tokens + $reward;
        $newTotalTokens = 0;
        $conn = dbConnect();
        $result = updateQuery("users", "current_tokens=".$newTokens, "User_ID=".$_SESSION['id']);

        $_SESSION['tokens'] = $newTokens;

        $user_id = $_SESSION['id'];
        $event_id = $_POST['eventID'];
        $sql = "INSERT INTO eventbacklog (User_ID, event_ID) VALUES (?, ?)";
        insertQuery($sql, $user_id, $event_id);

        echo "Great success!";
      }
    }
    //time check

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
    $result = innerJoinQuery("promos", "promolinks", "promos.promo_ID, promos.cost, promos.Title, promolinks.redeem_code, promolinks.business_name", "", "promos.business_id=promolinks.business_id");
    $arr = generateEventArray($result);
    $html = generatePromoHTML($arr);
    echo $html;
  }
  else if($_POST['func'] == "displayEvents"){

    $conn = dbConnect();
    $result = innerJoinQuery("events", "locations", "events.Event_ID, events.type, events.tokens, events.start, events.finish, events.date, locations.name, locations.address, locations.latLong", "", "events.Loc_ID=locations.Loc_ID");

    $arr = generateEventArray($result);
    $html =  generateHTML($arr);
    echo $html;
  }
  else if($_POST['func'] == "displayMyPromos"){

    $conn = dbConnect();
    session_start();
    $where = array("User_ID" => $_SESSION['id']);
    $result2 = query("userpromos", "Promo_ID", $where);

    $ret = array();

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

    if($result->num_rows == 0){
      echo "You don't actually have this promo legitimately purchased";
    }
    else{
      $result = innerJoinQuery("promos", "promolinks", "promolinks.redeem_code", "", "promos.business_id = promolinks.business_id", "promos.promo_ID=".$id);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $dbCode = $row['redeem_code'];
        }
      }

      if($dbCode != $code){
        //error, code was entered wrong
        echo "wrong code was entered";
      }
      else{
        echo "Success";
        deleteQuery("userpromos", "User_ID=".$_SESSION['id']." AND Promo_ID=".$id);
      }
    }
  }
}
else{
  header("Location: ../restrict.php");
}

 ?>
